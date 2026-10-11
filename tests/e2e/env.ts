import fs from 'node:fs';
import path from 'node:path';

/** Suite constants — resolved once, shared by setup and specs. */

export const E2E_DIR = __dirname;
export const REPO_ROOT = path.resolve(E2E_DIR, '../..');
export const PLUGIN_SRC = path.join(REPO_ROOT, 'plugins', 'diviskit-agent');
export const PLUGIN_MAIN = path.join(PLUGIN_SRC, 'diviskit-agent.php');
export const INSTALL_SKILLS_SRC = path.join(PLUGIN_SRC, 'bin', 'install-skills.sh');
export const SKILLS_SRC = path.join(PLUGIN_SRC, 'skills');

export const SITE_DIR = path.resolve(
  process.env.DK_E2E_SITE_DIR ?? path.join(E2E_DIR, '..', '..', '..', 'diviskit-test'),
);

export const ARTIFACTS = path.join(E2E_DIR, '.artifacts');
export const ZIP_PATH = path.join(ARTIFACTS, 'diviskit-agent.zip');
export const ENV_FILE = path.join(ARTIFACTS, 'env.json');

/** Written by global-setup, read by every spec via loadState(). */
export interface E2EState {
  siteDir: string;
  wpUrl: string;
  wpUser: string;
  wpAppPassword: string;
  zipPath: string;
  pluginVersion: string;
  minServerVersion: string;
  repoSkills: string[];
}

let cached: E2EState | undefined;

export function loadState(): E2EState {
  if (!cached) {
    if (!fs.existsSync(ENV_FILE)) {
      throw new Error(`${ENV_FILE} missing — global-setup did not run (ddev site reachable?)`);
    }
    cached = JSON.parse(fs.readFileSync(ENV_FILE, 'utf8')) as E2EState;
  }
  return cached;
}

/** Version + constants parsed from the plugin main file (source of truth). */
export function pluginConstant(name: string): string {
  const src = fs.readFileSync(PLUGIN_MAIN, 'utf8');
  const m = src.match(new RegExp(`const ${name} = '([^']+)'`));
  if (!m) throw new Error(`const ${name} not found in ${PLUGIN_MAIN}`);
  return m[1];
}

/** Capability keys advertised by the plugin handshake (const CAPABILITIES). */
export function pluginCapabilities(): string[] {
  const src = fs.readFileSync(PLUGIN_MAIN, 'utf8');
  const m = src.match(/const CAPABILITIES = \[([\s\S]*?)\];/);
  if (!m) throw new Error('const CAPABILITIES not found');
  return [...m[1].matchAll(/'([a-z0-9_]+)'/g)].map((x) => x[1]);
}
