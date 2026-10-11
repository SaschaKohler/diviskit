import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import {
  ARTIFACTS,
  ENV_FILE,
  INSTALL_SKILLS_SRC,
  PLUGIN_SRC,
  REPO_ROOT,
  SITE_DIR,
  SKILLS_SRC,
  ZIP_PATH,
  pluginConstant,
  type E2EState,
} from './env';
import { ddev, ddevDescribe, ensureAppPassword, wpEval } from './helpers/ddev';

function fail(msg: string): never {
  throw new Error(`\n[e2e global-setup] ${msg}\n`);
}

/**
 * Prepares everything specs need against the live DDEV site:
 *   1. ddev binary + site reachable
 *   2. fresh release zip built from plugins/diviskit-agent (release.sh layout)
 *   3. throwaway application password "diviskit-e2e" for REST/MCP auth
 */
export default async function globalSetup() {
  fs.mkdirSync(ARTIFACTS, { recursive: true });

  // ── 1. site ──────────────────────────────────────────────────────
  if (!fs.existsSync(path.join(SITE_DIR, '.ddev'))) {
    fail(`DDEV site not found at ${SITE_DIR}. Set DK_E2E_SITE_DIR.`);
  }
  const desc = ddevDescribe(SITE_DIR);
  const wpUrl =
    process.env.DK_E2E_WP_URL?.replace(/\/$/, '') ??
    (desc?.primary_url as string | undefined) ??
    'https://diviskit-test.ddev.site';
  if (!desc) {
    fail(
      `Could not reach DDEV site at ${SITE_DIR} (ddev describe failed — ` +
        `project stopped?). Run: cd ${SITE_DIR} && ddev start`,
    );
  }
  if (desc.status !== 'running' && desc.status !== 'ok') {
    fail(`DDEV project "${desc.name ?? SITE_DIR}" is not running (${desc.status}). ddev start it first.`);
  }

  // wp-cli sanity — catches broken WP before the suite burns time.
  const coreVersion = ddev(['wp', 'core', 'version'], SITE_DIR, true).trim();
  if (!/^\d+\.\d+/.test(coreVersion)) fail(`wp core version failed on ${SITE_DIR}: ${coreVersion}`);

  // ── 2. zip (same excludes as bin/release.sh step 4) ─────────────
  if (fs.existsSync(ZIP_PATH)) fs.rmSync(ZIP_PATH);
  execFileSync(
    'zip',
    [
      '-qr',
      ZIP_PATH,
      'diviskit-agent',
      '-x',
      '*.DS_Store',
      'diviskit-agent/vendor/*',
      'diviskit-agent/tests/*',
      'diviskit-agent/composer.json',
      'diviskit-agent/composer.lock',
      'diviskit-agent/phpunit.xml*',
      'diviskit-agent/.gitignore',
      'diviskit-agent/.phpunit*',
    ],
    { cwd: path.join(REPO_ROOT, 'plugins'), stdio: 'inherit' },
  );
  for (const required of [
    path.join(ZIP_PATH),
    INSTALL_SKILLS_SRC,
    path.join(PLUGIN_SRC, 'diviskit-agent.php'),
  ]) {
    if (!fs.existsSync(required)) fail(`expected artifact missing: ${required}`);
  }

  // ── 3. admin user + app password ─────────────────────────────────
  const wpUser =
    process.env.DK_E2E_WP_USER ??
    wpEval(
      SITE_DIR,
      '$a=get_users(["role"=>"administrator","number"=>1,"fields"=>["ID","user_login"]]); echo $a[0]->user_login ?? "";',
    ).trim();
  if (!wpUser) fail('no administrator found on the site');

  // Drop stale e2e passwords, then mint a fresh one.
  const wpAppPassword = ensureAppPassword(SITE_DIR, wpUser, 'diviskit-e2e');
  if (!wpAppPassword) fail('application-password create returned empty');

  const repoSkills = fs
    .readdirSync(SKILLS_SRC, { withFileTypes: true })
    .filter((d) => d.isDirectory() && fs.existsSync(path.join(SKILLS_SRC, d.name, 'SKILL.md')))
    .map((d) => d.name)
    .sort();

  const state: E2EState = {
    siteDir: SITE_DIR,
    wpUrl,
    wpUser,
    wpAppPassword,
    zipPath: ZIP_PATH,
    pluginVersion: pluginConstant('VERSION'),
    minServerVersion: pluginConstant('MIN_SERVER_VERSION'),
    repoSkills,
  };
  fs.writeFileSync(ENV_FILE, JSON.stringify(state, null, 2));
  console.log(
    `[e2e global-setup] site=${wpUrl} user=${wpUser} plugin=${state.pluginVersion} skills=[${repoSkills}] zip=${ZIP_PATH}`,
  );
}
