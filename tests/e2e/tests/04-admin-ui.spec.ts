import { test, expect } from '@playwright/test';
import path from 'node:path';
import { loadState } from '../env';
import { wp } from '../helpers/ddev';

/**
 * Optional browser flow: the real user path — wp-admin login →
 * Plugins → Add New → Upload zip → Activate. Requires the wp-admin
 * password (app passwords can't log into wp-login.php):
 *
 *   npx playwright install chromium
 *   DK_E2E_ADMIN_USER=admin DK_E2E_ADMIN_PASS=… npx playwright test tests/04
 */

const ADMIN_USER = process.env.DK_E2E_ADMIN_USER;
const ADMIN_PASS = process.env.DK_E2E_ADMIN_PASS;

test.describe('wp-admin upload install (optional)', () => {
  test.skip(
    !ADMIN_USER || !ADMIN_PASS,
    'set DK_E2E_ADMIN_USER + DK_E2E_ADMIN_PASS to run the wp-admin upload flow',
  );

  const s = loadState();
  const SLUG = 'diviskit-agent';

  test.beforeAll(() => {
    wp(s.siteDir, ['plugin', 'deactivate', SLUG], true);
    wp(s.siteDir, ['plugin', 'delete', SLUG], true);
  });

  test('login → upload zip → activate', async ({ page }) => {
    await page.goto(`${s.wpUrl}/wp-login.php`);
    await page.fill('#user_login', ADMIN_USER!);
    await page.fill('#user_pass', ADMIN_PASS!);
    await page.click('#wp-submit');
    await page.waitForURL(/wp-admin/);

    await page.goto(`${s.wpUrl}/wp-admin/plugin-install.php?tab=upload`);
    await page.setInputFiles('input#pluginzip', s.zipPath);
    await page.click('#install-plugin-submit');

    await expect(page.locator('body')).toContainText(/installed successfully/i, {
      timeout: 60_000,
    });
    await page.getByRole('link', { name: /activate plugin/i }).first().click();
    await expect(page.locator('body')).toContainText(/Plugin activated|active/i);
  });

  test('REST surface live after UI install', async ({ page }) => {
    // Plugin state is what matters — verify via REST handshake.
    const res = await page.request.post(`${s.wpUrl}/wp-json/diviskit/v1/handshake`, {
      data: { mcp_server_version: '1.7.0' },
      headers: {
        Authorization:
          'Basic ' + Buffer.from(`${s.wpUser}:${s.wpAppPassword}`).toString('base64'),
      },
    });
    expect(res.status()).toBe(200);
    expect((await res.json()).compatible).toBe(true);
  });
});
