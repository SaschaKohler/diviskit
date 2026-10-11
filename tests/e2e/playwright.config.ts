import { defineConfig } from '@playwright/test';

/**
 * Diviskit Agent e2e suite.
 *
 * Prerequisites: a running DDEV WordPress site (default: sibling
 * directory ../diviskit-test). Override via env:
 *
 *   DK_E2E_SITE_DIR    path to the DDEV site (default ../../../diviskit-test)
 *   DK_E2E_WP_URL      site base URL (default: `ddev describe` primary_url,
 *                      fallback https://diviskit-test.ddev.site)
 *   DK_E2E_WP_USER     admin user for REST/app-password (default: first
 *                      administrator found on the site)
 *   DK_E2E_MCP_COMMAND MCP server command (default: npx)
 *   DK_E2E_MCP_ARGS    MCP server args as JSON array (default:
 *                      ["-y","--package","@diviskit/mcp-server","diviskit-mcp"])
 *                      e.g. ["../../diviskit-server/dist/index.js"] with
 *                      DK_E2E_MCP_COMMAND=node to test the local build.
 *   DK_E2E_ADMIN_USER / DK_E2E_ADMIN_PASS  wp-admin credentials — only
 *                      required for tests/04 (UI upload flow). `npx
 *                      playwright install chromium` is needed for those.
 *   DK_E2E_FRESH=0     skip tests/00 (fresh DDEV instance provisioning)
 *   DK_E2E_DIVI_ZIP / DK_E2E_DIVI_SOURCE   Divi theme source for tests/00
 *                      (default: the Divi dir on the DK_E2E_SITE_DIR site)
 *
 * Single worker: 00 provisions a Docker site, 01 installs/deletes the
 * plugin on the shared site — specs must not interleave.
 */
export default defineConfig({
  testDir: './tests',
  globalSetup: './global-setup.ts',
  timeout: 90_000,
  expect: { timeout: 15_000 },
  workers: 1,
  fullyParallel: false,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    // DDEV uses self-signed certs.
    ignoreHTTPSErrors: true,
  },
});
