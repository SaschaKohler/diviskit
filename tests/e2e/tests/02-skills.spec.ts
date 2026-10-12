import { test, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import crypto from 'node:crypto';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { loadState, INSTALL_SKILLS_SRC, SKILLS_SRC } from '../env';
import { anonContext, authContext } from '../helpers/rest';

/**
 * Skills surface e2e: REST manifest + bundles (sha256/base64 integrity,
 * parity with the repo's canonical skills/ tree) and the real
 * install-skills.sh script across every advertised --client target.
 */

interface ManifestFile {
  path: string;
  size: number;
  sha256: string;
  mode: number;
}
interface ManifestSkill {
  name: string;
  bundle: string;
  file_count: number;
  files: ManifestFile[];
}

const sha256 = (b: Buffer) => crypto.createHash('sha256').update(b).digest('hex');

test.describe('skills REST surface', () => {
  const s = loadState();
  let manifest: { plugin_version: string; skills: ManifestSkill[] };

  test.beforeAll(async () => {
    const ctx = await authContext();
    const res = await ctx.get('/wp-json/diviskit/v1/skills');
    expect(res.status()).toBe(200);
    manifest = (await res.json()).data;
    await ctx.dispose();
  });

  test('GET /skills unauthenticated → 401', async () => {
    const ctx = await anonContext();
    expect((await ctx.get('/wp-json/diviskit/v1/skills')).status()).toBe(401);
    await ctx.dispose();
  });

  test('manifest shape: version + every repo skill advertised', () => {
    expect(manifest.plugin_version).toBe(s.pluginVersion);
    // Sites with diviskit-pro serve extra Pro bundles via the
    // diviskit_skills_dirs filter — the free skills must be a subset.
    const names = manifest.skills.map((x) => x.name);
    for (const repoSkill of s.repoSkills) {
      expect(names, `free skill ${repoSkill} missing`).toContain(repoSkill);
    }
    for (const skill of manifest.skills) {
      expect(typeof skill.bundle).toBe('string');
      if (s.repoSkills.includes(skill.name)) {
        expect(skill.bundle).toBe('diviskit-agent');
      }
      expect(skill.file_count).toBe(skill.files.length);
      for (const f of skill.files) {
        expect(f.path).not.toMatch(/^\/|\.\./);
        expect(f.sha256).toMatch(/^[0-9a-f]{64}$/);
        expect(f.size).toBeGreaterThan(0);
        expect(f.mode).toBeGreaterThan(0);
      }
    }
  });

  for (const skillName of loadState().repoSkills) {
    test(`bundle ${skillName}: base64 + sha256 + parity with repo source`, async () => {
      const ctx = await authContext();
      const res = await ctx.get(`/wp-json/diviskit/v1/skills/${skillName}`);
      expect(res.status()).toBe(200);
      const bundle = (await res.json()).data;
      await ctx.dispose();

      expect(bundle.name).toBe(skillName);
      const relPaths = new Set<string>();
      for (const f of bundle.files as (ManifestFile & { content: string; encoding: string })[]) {
        expect(f.encoding).toBe('base64');
        const bytes = Buffer.from(f.content, 'base64');
        expect(sha256(bytes), `${skillName}/${f.path} checksum`).toBe(f.sha256);
        expect(bytes.length, `${skillName}/${f.path} size`).toBe(f.size);
        relPaths.add(f.path);

        // Site serves exactly what the repo ships (deploy freshness).
        const repoFile = path.join(SKILLS_SRC, skillName, f.path);
        expect(fs.existsSync(repoFile), `${repoFile} missing in repo`).toBe(true);
        expect(bytes.equals(fs.readFileSync(repoFile)), `${skillName}/${f.path} content drift`).toBe(true);
      }
      expect(relPaths.has('SKILL.md')).toBe(true);
    });
  }

  test('GET /skills/<unknown> → 404 not_found envelope', async () => {
    const ctx = await authContext();
    const res = await ctx.get('/wp-json/diviskit/v1/skills/no-such-skill');
    expect(res.status()).toBe(404);
    const body = await res.json();
    expect(body.ok).toBe(false);
    expect(body.error.code).toBe('not_found');
    await ctx.dispose();
  });
});

test.describe('install-skills.sh', () => {
  const s = loadState();
  const env = {
    ...process.env,
    WP_URL: s.wpUrl,
    WP_USER: s.wpUser,
    WP_APP_PASSWORD: s.wpAppPassword,
  };

  const run = (args: string[], cwd: string, extraEnv: NodeJS.ProcessEnv = {}) =>
    execFileSync('bash', [INSTALL_SKILLS_SRC, ...args], {
      cwd,
      env: { ...env, ...extraEnv },
      encoding: 'utf8',
    });

  let tmp: string;

  test.beforeEach(() => {
    tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'dk-skills-'));
  });

  test.afterEach(() => {
    fs.rmSync(tmp, { recursive: true, force: true });
  });

  test('generic --target installs every skill with SKILL.md', () => {
    const target = path.join(tmp, 'skills');
    const out = run(['--client', 'generic', '--target', target], tmp);
    expect(out).toContain('plugin_version:');
    for (const name of s.repoSkills) {
      expect(fs.existsSync(path.join(target, name, 'SKILL.md')), name).toBe(true);
    }
  });

  test('installed files are sha256-identical to the REST manifest', async () => {
    const target = path.join(tmp, 'skills');
    run(['--client', 'generic', '--target', target], tmp);

    const ctx = await authContext();
    const manifest = (await (await ctx.get('/wp-json/diviskit/v1/skills')).json()).data;
    await ctx.dispose();

    for (const skill of manifest.skills as ManifestSkill[]) {
      for (const f of skill.files) {
        const local = path.join(target, skill.name, f.path);
        expect(fs.existsSync(local), `${skill.name}/${f.path}`).toBe(true);
        expect(sha256(fs.readFileSync(local)), `${skill.name}/${f.path}`).toBe(f.sha256);
      }
    }
  });

  test('re-run removes files no longer in the bundle (stale cleanup)', () => {
    const target = path.join(tmp, 'skills');
    run(['--client', 'generic', '--target', target], tmp);
    const stale = path.join(target, s.repoSkills[0], 'STALE-OLD-FILE.txt');
    fs.writeFileSync(stale, 'leftover');
    run(['--client', 'generic', '--target', target], tmp);
    expect(fs.existsSync(stale)).toBe(false);
  });

  for (const client of ['devin', 'codex', 'claude', 'opencode', 'dsh'] as const) {
    test(`--client ${client} --scope project → .${client}/skills`, () => {
      const out = run(['--client', client, '--scope', 'project'], tmp);
      expect(out).toContain('installed');
      for (const name of s.repoSkills) {
        expect(fs.existsSync(path.join(tmp, `.${client}`, 'skills', name, 'SKILL.md')), name).toBe(true);
      }
    });
  }

  test('--client cursor → skills tree + generated .mdc rules', () => {
    run(['--client', 'cursor', '--scope', 'project'], tmp);
    for (const name of s.repoSkills) {
      expect(fs.existsSync(path.join(tmp, '.cursor', 'skills', name, 'SKILL.md')), name).toBe(true);
      const mdc = path.join(tmp, '.cursor', 'rules', `${name}.mdc`);
      expect(fs.existsSync(mdc), mdc).toBe(true);
      const content = fs.readFileSync(mdc, 'utf8');
      expect(content).toContain('alwaysApply: false');
      expect(content).toContain('SKILL.md');
    }
  });

  test('credentials resolve from --config mcp_config.local.json (no env)', () => {
    const target = path.join(tmp, 'skills');
    const config = path.join(tmp, 'mcp_config.local.json');
    fs.writeFileSync(
      config,
      JSON.stringify({
        mcpServers: {
          'diviskit-mcp': {
            command: 'npx',
            args: ['-y', '--package', '@diviskit/mcp-server', 'diviskit-mcp'],
            env: { WP_URL: s.wpUrl, WP_USER: s.wpUser, WP_APP_PASSWORD: s.wpAppPassword },
          },
        },
      }),
    );
    const out = run(['--client', 'generic', '--target', target, '--config', config], tmp, {
      WP_URL: '',
      WP_USER: '',
      WP_APP_PASSWORD: '',
    });
    expect(out).toContain('installed');
    expect(fs.existsSync(path.join(target, s.repoSkills[0], 'SKILL.md'))).toBe(true);
  });

  test('credentials resolve from --config cordis.patch.yml (dsh row)', () => {
    const target = path.join(tmp, 'skills');
    const config = path.join(tmp, 'cordis.patch.yml');
    fs.writeFileSync(
      config,
      [
        '- id: mcp-diviskit',
        "  name: '@deepseek-ai/dsh-mcp-client'",
        '  config:',
        '    serverName: diviskit',
        '    transport: stdio',
        '    command: npx',
        "    args: ['-y', '--package', '@diviskit/mcp-server', 'diviskit-mcp']",
        '    env:',
        `      WP_URL: '${s.wpUrl}'`,
        `      WP_USER: '${s.wpUser}'`,
        '      WP_APP_PASSWORD: !!js process.env.TEST_DSH_APP_PASSWORD',
        '',
      ].join('\n'),
    );
    const out = run(['--client', 'generic', '--target', target, '--config', config], tmp, {
      WP_URL: '',
      WP_USER: '',
      WP_APP_PASSWORD: '',
      TEST_DSH_APP_PASSWORD: s.wpAppPassword,
    });
    expect(out).toContain('installed');
    expect(fs.existsSync(path.join(target, s.repoSkills[0], 'SKILL.md'))).toBe(true);
  });

  test('missing client → usage error, no partial install', () => {
    expect(() => run(['--client', 'bogus', '--target', path.join(tmp, 'x')], tmp)).toThrow();
    expect(fs.existsSync(path.join(tmp, 'x', 'SKILL.md'))).toBe(false);
  });
});
