import { request, type APIRequestContext } from '@playwright/test';
import { loadState } from '../env';

/**
 * Playwright request context factories for the Diviskit REST surface.
 * DDEV self-signed cert → ignoreHTTPSErrors everywhere. Pass `creds`
 * to target a site other than the suite default (e.g. the fresh
 * instance in tests/00).
 */

export interface SiteCreds {
  url: string;
  user: string;
  pass: string;
}

export async function anonContext(url?: string): Promise<APIRequestContext> {
  const base = url ?? loadState().wpUrl;
  return request.newContext({ baseURL: base, ignoreHTTPSErrors: true });
}

export async function authContext(creds?: SiteCreds): Promise<APIRequestContext> {
  const s = loadState();
  const c = creds ?? { url: s.wpUrl, user: s.wpUser, pass: s.wpAppPassword };
  const basic = Buffer.from(`${c.user}:${c.pass}`).toString('base64');
  return request.newContext({
    baseURL: c.url,
    ignoreHTTPSErrors: true,
    extraHTTPHeaders: { Authorization: `Basic ${basic}` },
  });
}

/** POST handshake with a given server version (or omit the arg). */
export async function handshake(ctx: APIRequestContext, serverVersion?: string) {
  const data = serverVersion === undefined ? {} : { mcp_server_version: serverVersion };
  return ctx.post('/wp-json/diviskit/v1/handshake', { data });
}
