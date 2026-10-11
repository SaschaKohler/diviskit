import { execFileSync } from 'node:child_process';

/** Thin wrappers around `ddev` / `ddev wp` against a DDEV site dir. */

export function ddev(args: string[], siteDir: string, allowFail = false): string {
  try {
    return execFileSync('ddev', args, {
      cwd: siteDir,
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'pipe'],
    });
  } catch (e: any) {
    if (allowFail) return (e.stdout ?? '') + (e.stderr ?? '');
    throw new Error(`ddev ${args.join(' ')} failed (cwd=${siteDir}): ${e.stderr ?? e.message}`);
  }
}

/** Like ddev() but reports the exit code instead of throwing. */
export function ddevTry(args: string[], siteDir: string): { code: number; out: string } {
  try {
    return {
      code: 0,
      out: execFileSync('ddev', args, {
        cwd: siteDir,
        encoding: 'utf8',
        stdio: ['ignore', 'pipe', 'pipe'],
      }),
    };
  } catch (e: any) {
    return { code: e.status ?? 1, out: ((e.stdout ?? '') + (e.stderr ?? '')).toString() };
  }
}

/** `ddev wp <args>` — output trimmed. */
export function wp(siteDir: string, args: string[], allowFail = false): string {
  return ddev(['wp', ...args], siteDir, allowFail);
}

/** `ddev wp eval '<php>'` — single-shot PHP inside the site. */
export function wpEval(siteDir: string, php: string, allowFail = false): string {
  return ddev(['wp', 'eval', php], siteDir, allowFail);
}

/** `ddev exec <cmd>` inside the web container docroot. */
export function ddevExec(siteDir: string, cmd: string, allowFail = false): string {
  return ddev(['exec', 'bash', '-c', cmd], siteDir, allowFail);
}

/**
 * (Re)create a named application password for a wp user — stale
 * passwords with the same name are deleted first. Returns the
 * plaintext password.
 */
export function ensureAppPassword(siteDir: string, user: string, name: string): string {
  wpEval(
    siteDir,
    `$u=get_user_by('login','${user}');` +
      `if(!$u){fwrite(STDERR,"no user ${user}");exit(1);}` +
      `foreach (WP_Application_Passwords::get_user_application_passwords($u->ID) as $p)` +
      `{ if ($p['name']==='${name}') WP_Application_Passwords::delete_application_password($u->ID,$p['uuid']); }`,
  );
  return wp(siteDir, ['user', 'application-password', 'create', user, name, '--porcelain']).trim();
}

/** Remove the plugin's AGENTS.md marker block; drops the file when it
 *  held nothing else. Safe to call when AGENTS.md is absent. */
export function stripAgentsMdBlock(siteDir: string) {
  ddevExec(
    siteDir,
    `if [ -f AGENTS.md ]; then ` +
      `sed -e '/<!-- BEGIN DIVISKIT-AGENT -->/,/<!-- END DIVISKIT-AGENT -->/d' AGENTS.md > /tmp/ag.md; ` +
      `if grep -q '[^[:space:]]' /tmp/ag.md; then mv /tmp/ag.md AGENTS.md; else rm -f AGENTS.md /tmp/ag.md; fi; ` +
      `fi`,
  );
}

/**
 * Parsed `ddev describe -j`, or null when the project is down/missing.
 * ddev wraps the payload as {"raw":{...}} and may interleave
 * {"level":"info"} log lines — scan for the raw doc.
 */
export function ddevDescribe(siteDir: string): Record<string, any> | null {
  try {
    const out = execFileSync('ddev', ['describe', '-j'], {
      cwd: siteDir,
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'pipe'],
    });
    for (const line of out.split('\n').reverse()) {
      try {
        const d = JSON.parse(line);
        if (d && typeof d === 'object') return d.raw ?? d;
      } catch {
        /* not a json line */
      }
    }
    return null;
  } catch {
    return null;
  }
}
