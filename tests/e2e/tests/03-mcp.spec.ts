import { test, expect } from '@playwright/test';
import { McpStdioClient } from '../helpers/mcp';
import { authContext, handshake } from '../helpers/rest';

/**
 * diviskit-mcp end-to-end: spawn the real server over stdio (npm package
 * by default, local dist via DK_E2E_MCP_COMMAND/DK_E2E_MCP_ARGS), run
 * the MCP handshake and prove the plugin surface is reachable.
 *
 * Capability contract is one-directional: the PLUGIN advertises a
 * superset (forward-looking keys, sub-behavior flags); each
 * plugin-touching TOOL must find its key in the map — the server's
 * requireCapability() gate fails fast otherwise. Skills have no MCP
 * tools at all (REST-only surface, exercised in tests/02).
 */

// Plugin-touching tool prefixes that must be capability-gated.
const PLUGIN_PREFIX =
  /^(page|canvas|module|section|menu|library|preset|variable|global_(color|font)|rollback|schema|tb_|seo_|scf_text|meta_(find_icon|flush_cache)|validate|render)_/;

// Server-local tools: wp-cli wrappers, in-memory templates, connectivity
// probes — intentionally NOT in the plugin capability map.
const LOCAL_TOOL =
  /^(meta_(ping|info|wp_cli)|template_|migrate_|scf_(status|sync|export|import|field_group)|vk_|agent|target)/;

test.describe('diviskit-mcp server (stdio)', () => {
  test.slow(); // first run may pull the npm package

  let mcp: McpStdioClient;
  let initResult: any;
  let tools: { name: string }[];
  let capabilities: Record<string, true>;

  test.beforeAll(async () => {
    mcp = McpStdioClient.spawnForSite();
    initResult = await mcp.connect();
    tools = await mcp.listTools();
    const ctx = await authContext();
    const res = await handshake(ctx, '1.7.0');
    capabilities = (await res.json()).capabilities;
    await ctx.dispose();
  }, 180_000);

  test.afterAll(() => {
    mcp?.close();
  });

  test('initialize negotiates protocol + serverInfo', () => {
    expect(initResult.protocolVersion).toBeTruthy();
    expect(initResult.serverInfo?.name).toBeTruthy();
    expect(initResult.capabilities?.tools).toBeDefined();
  });

  test('tools/list exposes core diviskit_* tools', () => {
    const names = tools.map((t) => t.name);
    for (const required of [
      'diviskit_page_list',
      'diviskit_page_get',
      'diviskit_page_get_layout',
      'diviskit_page_create',
      'diviskit_canvas_list',
      'diviskit_menu_list',
      'diviskit_variable_list',
      'diviskit_library_list',
      'diviskit_module_get',
      'diviskit_module_update',
      'diviskit_validate_blocks',
      'diviskit_render_preview',
      'diviskit_schema_list_modules',
      'diviskit_tb_template_list',
      'diviskit_meta_ping',
    ]) {
      expect(names, `missing tool ${required}`).toContain(required);
    }
    // Every advertised tool carries the diviskit_ prefix.
    expect(names.every((n) => n.startsWith('diviskit_'))).toBe(true);
  });

  test('capability drift: every plugin-touching tool is capability-gated', () => {
    const ungated: string[] = [];
    for (const { name } of tools) {
      const slug = name.replace(/^diviskit_/, '');
      if (!PLUGIN_PREFIX.test(slug + '_') && !PLUGIN_PREFIX.test(slug)) continue;
      if (LOCAL_TOOL.test(slug)) continue;
      // Sub-behavior tools (x_backup) resolve via their base key.
      const base = slug.replace(/_(backup|expected_checksum)$/, '');
      if (!capabilities[base] && !capabilities[slug]) {
        ungated.push(name);
      }
    }
    expect(ungated, 'plugin-touching tools without a handshake capability').toEqual([]);
  });

  test('read tools return ok envelopes', async () => {
    for (const name of [
      'diviskit_page_list',
      'diviskit_canvas_list',
      'diviskit_menu_list',
      'diviskit_variable_list',
      'diviskit_global_color_list',
      'diviskit_library_list',
      'diviskit_schema_list_modules',
      'diviskit_tb_template_list',
      'diviskit_rollback_snapshot_list',
      'diviskit_meta_ping',
    ]) {
      const { envelope } = await mcp.callTool(name);
      expect(envelope, `${name} returned no parseable envelope`).toBeDefined();
      expect(envelope.ok, `${name} → ${JSON.stringify(envelope).slice(0, 200)}`).toBe(true);
    }
  });

  test('diviskit_page_list returns structured page data', async () => {
    const { envelope } = await mcp.callTool('diviskit_page_list');
    expect(envelope?.ok).toBe(true);
    expect(envelope.data).toBeDefined();
  });
});
