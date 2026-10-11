import { test, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { ARTIFACTS, loadState } from '../env';
import {
  ddevDescribe,
  ddevExec,
  ddevTry,
  ensureAppPassword,
  stripAgentsMdBlock,
  wp,
  wpEval,
} from '../helpers/ddev';
import { anonContext, authContext, handshake, type SiteCreds } from '../helpers/rest';

/**
 * Full clean-room install: provisions a dedicated DDEV project
 * (.artifacts/e2e-site, project name `diviskit-agent-e2e`), downloads
 * WordPress, installs the Divi theme (donor: another site's licensed
 * copy — payware, can't come from wp.org) and installs the repo-built
 * diviskit-agent zip. afterAll removes plugin + theme + AGENTS.md block
 * + app password, so every run starts from a stock site again.
 *
 * Env:
 *   DK_E2E_FRESH=0        skip this spec entirely
 *   DK_E2E_DIVI_ZIP       path to a Divi.zip to install from
 *   DK_E2E_DIVI_SOURCE    path to a Divi theme dir to zip+install
 *                         (default: <DK_E2E_SITE_DIR>/wp-content/themes/Divi)
 */

const FRESH_DIR = path.join(ARTIFACTS, 'e2e-site');
// NOTE: ddev project names are a global registry — this name must not
// collide with an existing (even stale) project.
const FRESH_NAME = process.env.DK_E2E_FRESH_NAME ?? 'diviskit-agent-e2e';
const SLUG = 'diviskit-agent';
const APP_USER = 'admin';
const APP_NAME = 'diviskit-e2e';

const DOCKER_OK = (() => {
  try {
    execFileSync('docker', ['info'], { stdio: 'ignore' });
    return true;
  } catch {
    return false;
  }
})();

function sh(cmd: string, args: string[], cwd: string, timeoutMs = 300_000) {
  execFileSync(cmd, args, { cwd, stdio: 'inherit', timeout: timeoutMs });
}

test.describe.serial('fresh DDEV instance (wp + divi + plugin)', () => {
  test.describe.configure({ timeout: 600_000 });
  test.skip(process.env.DK_E2E_FRESH === '0', 'DK_E2E_FRESH=0 disables the fresh-instance spec');
  test.skip(!DOCKER_OK, 'Docker is not running — cannot provision a DDEV site');

  const s = loadState();
  let creds: SiteCreds;

  test.beforeAll(() => {
    fs.mkdirSync(FRESH_DIR, { recursive: true });

    // ── DDEV project ───────────────────────────────────────────────
    if (!fs.existsSync(path.join(FRESH_DIR, '.ddev', 'config.yaml'))) {
      sh(
        'ddev',
        ['config', '--project-type=wordpress', '--project-name', FRESH_NAME, '--docroot', '.'],
        FRESH_DIR,
      );
      startFresh();
    } else if (ddevDescribe(FRESH_DIR)?.status !== 'running') {
      startFresh();
    }

    function startFresh() {
      try {
        sh('ddev', ['start'], FRESH_DIR, 600_000);
      } catch (e) {
        throw new Error(
          `ddev start failed. If it complained about the hostname not being\n` +
            `resolvable, add the /etc/hosts entry once (needs sudo — run it\n` +
            `yourself in a terminal):\n` +
            `  sudo /opt/homebrew/bin/ddev-hostname ${FRESH_NAME}.ddev.site 127.0.0.1\n` +
            `then re-run the tests.\n\n${e}`,
        );
      }
    }

    // ── WordPress ──────────────────────────────────────────────────
    const installed = ddevTry(['wp', 'core', 'is-installed'], FRESH_DIR).code === 0;
    if (!installed) {
      if (!fs.existsSync(path.join(FRESH_DIR, 'wp-load.php'))) {
        wp(FRESH_DIR, ['core', 'download']);
      }
      wp(FRESH_DIR, ['config', 'create', '--dbname=db', '--dbuser=db', '--dbpass=db', '--dbhost=db', '--force']);
      wp(FRESH_DIR, [
        'core',
        'install',
        `--url=https://${FRESH_NAME}.ddev.site`,
        '--title=Diviskit E2E',
        '--admin_user=admin',
        '--admin_password=admin',
        '--admin_email=admin@example.com',
      ]);
    }

    // Leftovers from an interrupted previous run → clean slate.
    wp(FRESH_DIR, ['plugin', 'deactivate', SLUG], true);
    wp(FRESH_DIR, ['plugin', 'delete', SLUG], true);
    ddevExec(FRESH_DIR, 'rm -rf wp-content/themes/Divi wp-content/plugins/' + SLUG, true);
    stripAgentsMdBlock(FRESH_DIR);

    creds = {
      url: (ddevDescribe(FRESH_DIR)?.primary_url as string) ?? `https://${FRESH_NAME}.ddev.site`,
      user: APP_USER,
      pass: ensureAppPassword(FRESH_DIR, APP_USER, APP_NAME),
    };
  });

  test.afterAll(() => {
    try {
      // Back to stock: no diviskit plugin, no Divi theme, no marker block.
      wp(FRESH_DIR, ['plugin', 'deactivate', SLUG], true);
    wp(FRESH_DIR, ['plugin', 'delete', SLUG], true);

    const themes = wp(FRESH_DIR, ['theme', 'list', '--field=name'], true)
      .split('\n')
      .map((t) => t.trim())
      .filter((t) => t && t !== 'Divi');
    if (themes[0]) wp(FRESH_DIR, ['theme', 'activate', themes[0]], true);
    wp(FRESH_DIR, ['theme', 'delete', 'Divi'], true);
    ddevExec(FRESH_DIR, 'rm -rf wp-content/themes/Divi wp-content/plugins/' + SLUG, true);
    stripAgentsMdBlock(FRESH_DIR);
    wpEval(
      FRESH_DIR,
      `$u=get_user_by('login','${APP_USER}');` +
        `foreach (WP_Application_Passwords::get_user_application_passwords($u->ID) as $p)` +
        `{ if ($p['name']==='${APP_NAME}') WP_Application_Passwords::delete_application_password($u->ID,$p['uuid']); }`,
      true,
    );
    } catch {
      // Provisioning never succeeded — nothing to clean.
    }
  });

  test('stock WordPress is reachable', async () => {
    const ctx = await anonContext(creds.url);
    const res = await ctx.get('/');
    expect(res.status()).toBe(200);
    expect(await res.text()).not.toMatch(/Fatal error|Parse error/);
    await ctx.dispose();
  });

  test('Divi theme installs + activates', () => {
    const staged = path.join(FRESH_DIR, '.devin', 'tmp', 'e2e');
    fs.mkdirSync(staged, { recursive: true });

    const zipFromEnv = process.env.DK_E2E_DIVI_ZIP;
    const srcDir =
      process.env.DK_E2E_DIVI_SOURCE ?? path.join(s.siteDir, 'wp-content', 'themes', 'Divi');
    let zipPath = zipFromEnv;
    if (!zipPath) {
      expect(
        fs.existsSync(srcDir),
        `Divi source missing — set DK_E2E_DIVI_ZIP or DK_E2E_DIVI_SOURCE (${srcDir})`,
      ).toBe(true);
      zipPath = path.join(staged, 'Divi.zip');
      if (fs.existsSync(zipPath)) fs.rmSync(zipPath);
      execFileSync('zip', ['-qr', zipPath, 'Divi', '-x', '*.DS_Store', 'Divi/.git*'], {
        cwd: path.dirname(srcDir),
      });
    } else {
      fs.copyFileSync(zipPath, path.join(staged, 'Divi.zip'));
      zipPath = path.join(staged, 'Divi.zip');
    }

    const out = wp(FRESH_DIR, ['theme', 'install', '/var/www/html/.devin/tmp/e2e/Divi.zip', '--activate']);
    expect(out).toMatch(/Success|activated/i);
    expect(wpEval(FRESH_DIR, 'echo get_stylesheet();').trim()).toBe('Divi');
    expect(wpEval(FRESH_DIR, 'echo function_exists("et_get_option") ? "1" : "0";').trim()).toBe('1');
  });

  test('diviskit-agent zip installs + activates', () => {
    const staged = path.join(FRESH_DIR, '.devin', 'tmp', 'e2e');
    fs.copyFileSync(s.zipPath, path.join(staged, 'diviskit-agent.zip'));
    const out = wp(FRESH_DIR, [
      'plugin',
      'install',
      '/var/www/html/.devin/tmp/e2e/diviskit-agent.zip',
      '--activate',
    ]);
    expect(out).toMatch(/Success|activated/i);
    expect(wpEval(FRESH_DIR, `echo is_plugin_active('${SLUG}/${SLUG}.php') ? "1" : "0";`).trim()).toBe('1');
  });

  test('AGENTS.md marker written on activation', () => {
    const out = ddevExec(FRESH_DIR, 'cat AGENTS.md 2>/dev/null || echo __MISSING__');
    expect(out).not.toContain('__MISSING__');
    expect(out.match(/BEGIN DIVISKIT-AGENT/g) ?? []).toHaveLength(1);
  });

  test('handshake compatible, Divi reported active', async () => {
    const ctx = await authContext(creds);
    const res = await handshake(ctx, '1.7.0');
    expect(res.status()).toBe(200);
    const body = await res.json();
    expect(body.compatible).toBe(true);
    expect(body.plugin_version).toBe(s.pluginVersion);
    expect(body.divi.active).toBe(true);
    expect(body.divi.version).toBeTruthy();
    expect(body.authenticated_user.login).toBe(APP_USER);
    await ctx.dispose();
  });

  test('skills endpoint serves the free bundles', async () => {
    const ctx = await authContext(creds);
    const res = await ctx.get('/wp-json/diviskit/v1/skills');
    expect(res.status()).toBe(200);
    const body = await res.json();
    expect(body.ok).toBe(true);
    const names = body.data.skills.map((x: any) => x.name);
    for (const repoSkill of s.repoSkills) expect(names).toContain(repoSkill);
    await ctx.dispose();
  });

  test('frontend renders with Divi + plugin active', async () => {
    const ctx = await anonContext(creds.url);
    const res = await ctx.get('/');
    expect(res.status()).toBe(200);
    const body = await res.text();
    expect(body).not.toMatch(/Fatal error|Parse error|Uncaught/);
    await ctx.dispose();
  });
});
