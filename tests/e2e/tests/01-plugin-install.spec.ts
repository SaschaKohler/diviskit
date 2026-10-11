import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import { loadState, INSTALL_SKILLS_SRC } from '../env';
import { wp, wpEval, ddevExec } from '../helpers/ddev';
import { anonContext, authContext, handshake } from '../helpers/rest';

/**
 * Covers TESTING.md §5 "Clean install" end-to-end: the release zip
 * (built in global-setup from plugins/diviskit-agent, same layout as
 * bin/release.sh) is pushed into the site and installed via wp-cli —
 * the same code path `wp plugin install` uses for a wp.org download.
 * Activation must write the AGENTS.md marker block, expose the
 * diviskit/v1 REST surface and stay fatal-free.
 */

const SLUG = 'diviskit-agent';

test.describe.serial('plugin install (clean state → active)', () => {
  const s = loadState();
  const containerZip = '/var/www/html/.devin/tmp/e2e/diviskit-agent.zip';

  test.beforeAll(() => {
    // Ensure a genuinely clean slate: deactivate + delete any existing copy.
    wp(s.siteDir, ['plugin', 'deactivate', SLUG], true);
    wp(s.siteDir, ['plugin', 'delete', SLUG], true);
    // wp plugin delete removes the dir; assert it's really gone.
    const dir = ddevExec(s.siteDir, `test -d wp-content/plugins/${SLUG} && echo PRESENT || echo ABSENT`);
    expect(dir.trim()).toBe('ABSENT');

    // Stage the zip inside the project so the container can read it.
    const staged = path.join(s.siteDir, '.devin', 'tmp', 'e2e');
    fs.mkdirSync(staged, { recursive: true });
    fs.copyFileSync(s.zipPath, path.join(staged, 'diviskit-agent.zip'));
  });

  test('zip installs and activates without fatal', () => {
    const out = wp(s.siteDir, ['plugin', 'install', containerZip, '--activate']);
    expect(out).toMatch(/Success|activated/i);
    expect(wp(s.siteDir, ['plugin', 'is-active', SLUG], true)).not.toMatch(/Error/);
  });

  test('frontend renders — no PHP fatal', async () => {
    const ctx = await anonContext();
    const res = await ctx.get('/');
    expect(res.status()).toBe(200);
    const body = await res.text();
    expect(body).not.toMatch(/Fatal error|Parse error|Uncaught Error/);
    await ctx.dispose();
  });

  test('AGENTS.md written at ABSPATH, marker block exactly once', () => {
    const out = ddevExec(s.siteDir, 'cat AGENTS.md 2>/dev/null || echo __MISSING__');
    expect(out).not.toContain('__MISSING__');
    const markers = out.match(/BEGIN DIVISKIT-AGENT/g) ?? [];
    expect(markers).toHaveLength(1);
    expect(out).toContain('END DIVISKIT-AGENT');
    expect(out).toContain('wp-json/diviskit/v1');
  });

  test('re-activation does not duplicate the AGENTS.md marker', () => {
    wp(s.siteDir, ['plugin', 'deactivate', SLUG]);
    wp(s.siteDir, ['plugin', 'activate', SLUG]);
    const out = ddevExec(s.siteDir, 'cat AGENTS.md');
    expect(out.match(/BEGIN DIVISKIT-AGENT/g) ?? []).toHaveLength(1);
  });

  test('handshake: unauthenticated → 401', async () => {
    const ctx = await anonContext();
    const res = await handshake(ctx, '1.7.0');
    expect(res.status()).toBe(401);
    await ctx.dispose();
  });

  test('handshake: missing mcp_server_version → 400', async () => {
    const ctx = await authContext();
    const res = await handshake(ctx);
    expect(res.status()).toBe(400);
    await ctx.dispose();
  });

  test('handshake: outdated server → 426 upgrade_required', async () => {
    const ctx = await authContext();
    const res = await handshake(ctx, '0.0.1');
    expect(res.status()).toBe(426);
    const body = await res.json();
    expect(body.code).toBe('upgrade_required');
    await ctx.dispose();
  });

  test('handshake: valid → compatible + capability map + version', async () => {
    const ctx = await authContext();
    const res = await handshake(ctx, '1.7.0');
    expect(res.status()).toBe(200);
    const body = await res.json();

    expect(body.compatible).toBe(true);
    expect(body.server).toBe('diviskit');
    expect(body.plugin_version).toBe(s.pluginVersion);
    expect(body.min_server).toBe(s.minServerVersion);
    expect(body.namespaces).toContain('diviskit/v1');
    expect(body.authenticated_user.login).toBe(s.wpUser);
    expect(body.divi.active).toBe(true);

    // Core capability keys a fresh install must advertise.
    for (const key of [
      'page_list',
      'page_get',
      'canvas_list',
      'skill_list',
      'skill_get',
      'menu_list',
      'variable_list',
      'module_update',
      'validate_blocks',
      'render_preview',
    ]) {
      expect(body.capabilities, `missing capability ${key}`).toHaveProperty(key);
    }
    await ctx.dispose();
  });

  test('install-skills.sh downloadable from the deployed plugin, byte-identical', async () => {
    const ctx = await anonContext();
    const res = await ctx.get(`/wp-content/plugins/${SLUG}/bin/install-skills.sh`);
    expect(res.status()).toBe(200);
    const served = await res.body();
    const repo = fs.readFileSync(INSTALL_SKILLS_SRC);
    expect(served.equals(repo)).toBe(true);
    await ctx.dispose();
  });

  test('plugin registered with correct version in WP', () => {
    const version = wpEval(
      s.siteDir,
      `echo get_plugin_data(WP_PLUGIN_DIR.'/${SLUG}/${SLUG}.php')['Version'];`,
    ).trim();
    expect(version).toBe(s.pluginVersion);
  });
});
