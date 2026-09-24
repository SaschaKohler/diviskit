<?php
/**
 * Plugin Name: Diviskit Agent
 * Description: REST API bridge for Diviskit — connects AI coding agents to your Divi 5 site for page building and design management. Forked from the GPL-licensed DiviOps Agent; serves the REST contract on the canonical diviskit/v1 namespace.
 * Version: 1.7.1
 * Author: Diviskit
 * Text Domain: diviskit-agent
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://diviskit.com/item/diviskit-agent/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Trait includes (see #220 split) ─────────────────────────────
// Loaded before the class declaration so trait names resolve when
// the class declares `use ...;`. Each trait file has its own
// ABSPATH guard, so direct loading is rejected.
require_once __DIR__ . '/includes/trait-canvas.php';
require_once __DIR__ . '/includes/trait-authoring-shape.php';
require_once __DIR__ . '/includes/trait-compatibility.php';
require_once __DIR__ . '/includes/trait-core.php';
require_once __DIR__ . '/includes/trait-global-color.php';
require_once __DIR__ . '/includes/trait-global-font.php';
require_once __DIR__ . '/includes/trait-library.php';
require_once __DIR__ . '/includes/trait-meta.php';
require_once __DIR__ . '/includes/trait-module-schema.php';
require_once __DIR__ . '/includes/trait-menu.php';
require_once __DIR__ . '/includes/trait-page.php';
require_once __DIR__ . '/includes/trait-preset.php';
require_once __DIR__ . '/includes/trait-render.php';
require_once __DIR__ . '/includes/trait-rollback.php';
require_once __DIR__ . '/includes/trait-scf.php';
require_once __DIR__ . '/includes/trait-seo.php';
require_once __DIR__ . '/includes/trait-skills.php';
require_once __DIR__ . '/includes/trait-theme-builder.php';
require_once __DIR__ . '/includes/trait-validate.php';
require_once __DIR__ . '/includes/trait-variable.php';


class Diviskit_Agent {

	// ── Trait composition (see #220 split) ──────────────────────
	// Each trait contributes a slice of the REST surface. The traits
	// are required in the file-scope bootstrap below; methods on each
	// trait are mixed into this class.
	use Diviskit_Agent_Canvas;
	use Diviskit_Agent_AuthoringShape;
	use Diviskit_Agent_Compatibility;
	use Diviskit_Agent_Core;
	use Diviskit_Agent_GlobalColor;
	use Diviskit_Agent_GlobalFont;
	use Diviskit_Agent_Library;
	use Diviskit_Agent_Meta;
	use Diviskit_Agent_Menu;
	use Diviskit_Agent_ModuleSchema;
	use Diviskit_Agent_Page;
	use Diviskit_Agent_Preset;
	use Diviskit_Agent_Render;
	use Diviskit_Agent_Rollback;
	use Diviskit_Agent_SCF;
	use Diviskit_Agent_SEO;
	use Diviskit_Agent_Skills;
	use Diviskit_Agent_ThemeBuilder;
	use Diviskit_Agent_Validate;
	use Diviskit_Agent_Variable;

	/**
	 * Plugin version — surfaced in /handshake for self-diagnosis only;
	 * server no longer gates on it (capability map is the gate).
	 */
	const VERSION = '1.7.1';

	/**
	 * Minimum MCP server version this plugin is compatible with.
	 */
	const MIN_SERVER_VERSION = '1.1.0';

	/**
	 * Per-tool capability map emitted by /handshake.
	 *
	 * Each key is a post-rename MCP tool name slug (without the
	 * `diviskit_` prefix) or a precise additive behavior capability for
	 * a backwards-compatible route extension. The server's `requireCapability(<key>)`
	 * gate at every plugin-touching tool entry compares against this
	 * list. Tools the server adds in newer releases that aren't yet
	 * in this list will fail fast on older plugins with an "upgrade
	 * the diviskit-agent plugin" hint, while every other tool keeps
	 * working — no global version floor.
	 *
	 * Server-local tools (wp-cli wrappers, in-memory templates,
	 * meta_ping/meta_info) don't appear here; the server skips the
	 * capability check for them.
	 *
	 * Maintenance: any new route added below must add its capability
	 * key here in the same PR.
	 */
	const CAPABILITIES = [
		// canvas
		'canvas_create', 'canvas_delete', 'canvas_duplicate', 'canvas_get', 'canvas_list', 'canvas_orphan_audit', 'canvas_update',
		// global colors / fonts
		'global_color_audit_storage', 'global_color_create', 'global_color_delete', 'global_color_list', 'global_color_update',
		'global_font_audit_storage', 'global_font_create', 'global_font_delete', 'global_font_list', 'global_font_update',
		// library
		'library_get', 'library_list', 'library_save',
		// meta
		'meta_find_icon', 'meta_flush_cache',
		// menu
		'menu_create', 'menu_get', 'menu_item_add_custom', 'menu_item_add_page', 'menu_list', 'menu_location_assign',
		// module
		'module_clone', 'module_get', 'module_lock', 'module_move', 'module_unlock', 'module_update',
		'module_clone_backup', 'module_lock_backup', 'module_move_backup', 'module_unlock_backup', 'module_update_backup',
		// page
		'page_create', 'page_get', 'page_get_layout', 'page_list',
		'page_trash', 'page_update_content', 'page_update_content_backup', 'page_update_content_expected_checksum', 'page_update_meta', 'page_update_status',
		// preset
		'preset_audit', 'preset_audit_storage', 'preset_cleanup', 'preset_create', 'preset_delete', 'preset_inspect', 'preset_registry_doctor',
		'preset_reassign', 'preset_scan_orphans', 'preset_set_default', 'preset_update',
		// render
		'render_preview',
		// rollback snapshots
		'rollback_snapshot_delete', 'rollback_snapshot_get', 'rollback_snapshot_list', 'rollback_snapshot_restore',
		// existing SCF text-value authoring
		'scf_text_value_update',
		// semantic SEO metadata
		'seo_provider_list', 'seo_metadata_get', 'seo_metadata_update',
		// schema
		'schema_get_module', 'schema_get_module_dump_all', 'schema_get_settings', 'schema_list_modules',
		// section
		'section_append', 'section_append_backup', 'section_get', 'section_remove', 'section_remove_backup', 'section_replace', 'section_replace_backup',
		// skills
		'skill_get', 'skill_list',
		// theme builder
		'tb_layout_block_insert', 'tb_layout_block_insert_backup', 'tb_layout_get', 'tb_layout_update', 'tb_layout_update_backup', 'tb_template_create', 'tb_template_create_body', 'tb_template_list',
		'tb_template_trash',
		// validate
		'validate_blocks',
		// page_id overload on validate_blocks + render_preview (#700) —
		// single bundle key; both tools accept exactly-one of {content, page_id}.
		'validate_render_by_page_id',
		// variable
		'variable_create', 'variable_create_fluid_system', 'variable_delete',
		'variable_list', 'variable_scan_orphans', 'variable_used_on_page',
		// Sub-feature: structured `gradient` input on variable_create serializes
		// the canonical $variable(gradient) token (#921). Gated separately so a
		// new server fails loud against a plugin too old to serialize it.
		'variable_create_gradient',
		// Storage-path contract (#719). Single contract-level key advertises
		// implementation of the full read-probe + write-canonical + audit-
		// aggregates contract across preset / global_color / global_font
		// surfaces. Per-surface keys (preset_storage_multipath_v1,
		// global_color_storage_multipath_v1, global_font_storage_multipath_v1)
		// also emitted so consumers can detect partial implementations on
		// future plugins that ship the contract per-surface.
		'storage_multipath_probe_v1',
		'preset_storage_multipath_v1',
		'global_color_storage_multipath_v1',
		'global_font_storage_multipath_v1',
	];

	/**
	 * REST namespace. `diviskit/v1` is the canonical and only namespace —
	 * the legacy diviops/v1 compat alias was removed in 1.7.0.
	 */
	const REST_NAMESPACE = 'diviskit/v1';
	const REASSIGN_MAX_PAGES  = 1000;
	const VARIABLES_SCAN_MAX_POSTS = 2000;

	/**
	 * Post types that can contain Divi block markup — scanned for
	 * preset / variable references. Kept in one place so the ref-scanner
	 * and the variable_delete SQL fast-path stay in lockstep.
	 *
	 * Excludes:
	 * - et_theme_builder / et_template — these are template ASSIGNMENT records
	 *   (which layout runs where, conditions, duplication metadata), not the
	 *   block markup itself. Verified empty post_content on every record.
	 * - wp_block / wp_template / wp_template_part — Gutenberg reusable blocks
	 *   and FSE templates, not in use on Divi-rendered pages.
	 */
	const SCANNABLE_POST_TYPES = [
		'page',
		'post',
		'et_header_layout',
		'et_body_layout',
		'et_footer_layout',
		'et_pb_layout',
		'et_pb_canvas',
	];

	/** Block comment tag constants for section parsing. */
	const SECTION_OPEN  = '<!-- wp:divi/section';
	const SECTION_CLOSE = '<!-- /wp:divi/section -->';
	const BLOCK_PREFIX  = '<!-- wp:divi/';

	/**
	 * Default rate limits (requests per minute).
	 */
	const RATE_LIMIT_READ  = 120;
	const RATE_LIMIT_WRITE = 30;

	/** PHP 7.4-compatible constants consumed by the compatibility trait. */
	private const DIVI_POST_FILTER_PRICE_ROUTE = '/divi/v1/loop/product-price-range';
	private const DIVI_POST_FILTER_PRICE_NONCE_ROUTE = '/loop/product-price-range';
	private const DIVI_REST_NAMESPACE = 'divi/v1';
	private const DIVI_POST_FILTER_PRICE_METHOD = 'GET';
	private const DIVI_POST_FILTER_PRICE_CONTROLLER = 'ET\Builder\Packages\ModuleLibrary\PostFilterItem\PostFilterProductPriceRangeController';
	private const DIVI_USER_ROLE_CLASS = 'ET\Builder\Framework\UserRole\UserRole';

	/** PHP 7.4-compatible limits consumed by the authoring-shape trait. */
	private const AUTHORING_SHAPE_LIMITS = [
		'input_bytes' => 1048576, 'blocks' => 4096, 'depth' => 64, 'fields' => 8192,
		'string_bytes' => 1048576,
	];

	public static function init() {
		add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
		add_filter( 'rest_endpoints', [ __CLASS__, 'repair_divi_post_filter_price_permission' ] );
		add_filter( 'rest_pre_dispatch', [ __CLASS__, 'check_rate_limit' ], 10, 3 );
		add_filter( 'rest_post_dispatch', [ __CLASS__, 'wrap_rest_framework_validation_errors' ], 10, 3 );
		add_action( 'admin_menu', [ __CLASS__, 'register_admin_page' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_styles' ] );
		add_action( 'admin_init', [ __CLASS__, 'maybe_write_agents_md' ] );
	}

	/**
	 * Whether a REST route path belongs to this agent.
	 *
	 * @param string $route Route path as returned by WP_REST_Request::get_route().
	 */
	private static function is_agent_route( $route ) {
		$ns = '/' . self::REST_NAMESPACE;
		return $route === $ns || strpos( $route, $ns . '/' ) === 0;
	}

	/**
	 * Register a route on diviskit/v1.
	 * Drop-in wrapper around register_rest_route() — same signature minus
	 * the namespace argument.
	 */
	private static function register_route( $route, $args ) {
		register_rest_route( self::REST_NAMESPACE, $route, $args );
	}

	/**
	 * Plugin activation — drop the Diviskit section into the project
	 * AGENTS.md so an AI editor opened on this directory can self-serve
	 * the MCP connection setup.
	 */
	public static function activate() {
		if ( self::write_project_agents_md() ) {
			update_option( 'diviskit_agents_md_version', self::VERSION, false );
		}
	}

	/**
	 * Regenerate the AGENTS.md Diviskit section when the plugin version on
	 * disk differs from the version that last wrote the file — covers
	 * updates and installs where the activation hook never ran.
	 */
	public static function maybe_write_agents_md() {
		if ( get_option( 'diviskit_agents_md_version' ) === self::VERSION ) {
			return;
		}
		if ( self::write_project_agents_md() ) {
			update_option( 'diviskit_agents_md_version', self::VERSION, false );
		}
	}

	/**
	 * Write or update the marked Diviskit section inside ABSPATH/AGENTS.md.
	 * An existing file is preserved: a marked block is replaced in place,
	 * otherwise the section is appended.
	 */
	private static function write_project_agents_md() {
		$begin   = '<!-- BEGIN DIVISKIT-AGENT -->';
		$end     = '<!-- END DIVISKIT-AGENT -->';
		$section = $begin . "\n" . self::agents_md_section() . $end . "\n";
		$path    = ABSPATH . 'AGENTS.md';

		if ( file_exists( $path ) ) {
			$existing = file_get_contents( $path );
			if ( false === $existing ) {
				return false;
			}
			$start = strpos( $existing, $begin );
			$stop  = strpos( $existing, $end );
			if ( false !== $start && false !== $stop && $stop > $start ) {
				$content = substr( $existing, 0, $start ) . rtrim( $section ) . substr( $existing, $stop + strlen( $end ) );
			} else {
				$content = rtrim( $existing ) . "\n\n" . $section;
			}
		} else {
			$content = "# AGENTS.md\n\n" . $section;
		}

		return false !== file_put_contents( $path, $content );
	}

	/**
	 * AGENTS.md section — site-specific REST base, MCP client config and
	 * the verification procedure an AI agent follows for the connection
	 * setup. Wrapped in BEGIN/END markers by write_project_agents_md().
	 */
	private static function agents_md_section() {
		$rest_base  = rest_url( self::REST_NAMESPACE );
		$config     = wp_json_encode( self::mcp_client_config(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		$min_server = self::MIN_SERVER_VERSION;
		$prompt     = self::mcp_setup_prompt();

		ob_start();
?>
## Diviskit MCP

This site runs the **Diviskit Agent** WordPress plugin — a REST bridge that
lets AI coding agents manage Divi 5 content via the `@diviskit/mcp-server`
MCP server.

- REST base: `<?php echo esc_url( $rest_base ); ?>` — namespace `diviskit/v1`.
- Handshake: `POST <?php echo esc_url( $rest_base ); ?>/handshake` with body
  `{"mcp_server_version":"<server version>"}` and Application-Password basic
  auth → `{ "compatible": true, "capabilities": { ... } }`.
- Envelope: every endpoint returns `{ "ok": true, "data": ... }` or
  `{ "ok": false, "error": { "code", "message", "hint" } }`.

### MCP client config

Create `.devin/mcp_config.local.json` (Devin) or your client's MCP config
equivalent, replacing `<application-password>` with a real WordPress
Application Password:

```json
<?php echo $config; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON config template written to a project file, not HTML output. ?>
```

Gitignore the file — it contains credentials. Create the password under
Users → Profile → Application Passwords, or via WP-CLI:
`wp user application-password create <user> diviskit-mcp --porcelain`
(DDEV: `ddev wp user application-password create admin diviskit-mcp --porcelain`).

### Verify

```bash
curl -s -X POST "<?php echo esc_url( $rest_base ); ?>/handshake" \
  -u "<user>:<app-password>" \
  -H "Content-Type: application/json" \
  -d '{"mcp_server_version":"<?php echo esc_attr( $min_server ); ?>"}'
```

Expect `"compatible": true` and a `capabilities` map. Then restart the AI
client and smoke-test: `tools/list` exposes `diviskit_*` tools and a
read-only call like `diviskit_page_list` returns `{ "ok": true, ... }`.

### Ready-to-paste setup prompt

```
<?php echo $prompt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Plain-text prompt written to a project file, not HTML output. ?>
```

<?php
		return (string) ob_get_clean();
	}

	/**
	 * MCP client config template for this site — shared by the admin
	 * dashboard snippet and the generated AGENTS.md.
	 * NODE_TLS_REJECT_UNAUTHORIZED is only emitted for local/self-signed
	 * dev hosts — never for production.
	 */
	private static function mcp_client_config() {
		$wp_url   = get_site_url();
		$wp_host  = (string) wp_parse_url( $wp_url, PHP_URL_HOST );
		$is_local = (bool) preg_match( '/(^localhost$|\.ddev\.site$|\.local$|\.test$|^127\.0\.0\.1$)/', $wp_host );
		$env      = [
			'WP_URL'          => $wp_url,
			'WP_USER'         => wp_get_current_user()->user_login ? wp_get_current_user()->user_login : 'admin',
			'WP_APP_PASSWORD' => '<application-password>',
		];
		if ( $is_local ) {
			$env['NODE_TLS_REJECT_UNAUTHORIZED'] = '0';
		}
		return [
			'mcpServers' => [
				'diviskit-mcp' => [
					'command' => 'npx',
					'args'    => [ '-y', '--package', '@diviskit/mcp-server', 'diviskit-mcp' ],
					'env'     => $env,
				],
			],
		];
	}

	/**
	 * Paste-ready setup prompt — shown on the admin dashboard and embedded
	 * in the generated AGENTS.md. The receiving agent performs the full
	 * MCP setup: app password, client config, handshake check, smoke test.
	 */
	private static function mcp_setup_prompt() {
		$rest_base = rest_url( self::REST_NAMESPACE );

		ob_start();
?>
Set up the Diviskit MCP server for this WordPress project so that diviskit_* MCP tools become available.

The Diviskit Agent plugin has written an AGENTS.md into the project root — read its "Diviskit MCP" section first. It contains the REST base (<?php echo esc_url( $rest_base ); ?>), the ready-made MCP client config and the verification commands. Use diviskit/v1 only.

Steps:
1. Create a WordPress Application Password named "diviskit-mcp". Prefer WP-CLI: `wp user application-password create <user> diviskit-mcp --porcelain` (DDEV: `ddev wp user application-password create admin diviskit-mcp --porcelain`). If there is no CLI access, ask me to create one under Users → Profile → Application Passwords and paste it.
2. Write the MCP config from AGENTS.md to `.devin/mcp_config.local.json`, replacing `<application-password>`, and add the file to `.gitignore`.
3. Verify the handshake with the curl command from AGENTS.md — expect `"compatible": true` and a `capabilities` map.
4. Smoke-test the server over stdio with the env vars from the config: send `initialize`, then `tools/list` (expect diviskit_* tools), then one read-only `tools/call` such as `diviskit_page_list` (expect `{"ok":true,...}`).
5. Tell me to restart the AI client or reload the MCP session, then confirm the diviskit_* tools registered.
<?php
		return (string) ob_get_clean();
	}

	/**
	 * Wrap WordPress REST schema-validation errors in the Diviskit envelope.
	 *
	 * Route arg validation runs before endpoint callbacks, so typed args and
	 * enums can otherwise leak raw WP REST errors on `/diviskit/v1/*` routes.
	 * Keep route schemas intact and normalize only framework validation errors.
	 *
	 * @param WP_REST_Response $response REST response after dispatch.
	 * @param WP_REST_Server   $server   REST server instance.
	 * @param WP_REST_Request  $request  Current request.
	 * @return WP_REST_Response
	 */
	public static function wrap_rest_framework_validation_errors( $response, $server, $request ) {
		if ( ! is_object( $request ) || ! method_exists( $request, 'get_route' ) ) {
			return $response;
		}

		$route = (string) $request->get_route();
		if ( ! self::is_agent_route( $route ) ) {
			return $response;
		}

		if ( ! is_object( $response ) || ! method_exists( $response, 'get_data' ) ) {
			return $response;
		}

		$body = $response->get_data();
		if ( ! is_array( $body ) || ! isset( $body['code'] ) || ! isset( $body['message'] ) ) {
			return $response;
		}

		$wp_error_code = (string) $body['code'];
		if ( ! in_array( $wp_error_code, [ 'rest_invalid_param', 'rest_missing_callback_param' ], true ) ) {
			return $response;
		}

		$wp_error_data = isset( $body['data'] ) && is_array( $body['data'] ) ? $body['data'] : [];
		$http_status   = isset( $wp_error_data['status'] ) ? (int) $wp_error_data['status'] : 400;
		$error_data    = [
			'wp_error_code' => $wp_error_code,
		];

		foreach ( $wp_error_data as $key => $value ) {
			if ( in_array( $key, [ 'status', 'hint' ], true ) ) {
				continue;
			}
			$error_data[ $key ] = $value;
		}

		return self::envelope_error(
			'invalid_input',
			(string) $body['message'],
			'Fix the request parameters named in error.data, then retry.',
			$http_status,
			$error_data
		);
	}

	/**
	 * Rate limit check via rest_pre_dispatch filter.
	 *
	 * Uses WordPress transients for per-user request counting.
	 * Only applies to diviskit/v1 endpoints.
	 *
	 * Configurable via:
	 *   - DIVISKIT_RATE_LIMIT_READ  constant or env var (default: 120/min)
	 *   - DIVISKIT_RATE_LIMIT_WRITE constant or env var (default: 30/min)
	 *   - DIVISKIT_RATE_LIMIT_DISABLED constant or env var (disables entirely)
	 *   - 'diviskit_rate_limits' filter (receives ['read' => int, 'write' => int])
	 *
	 * @param mixed            $result  Response to replace the requested one.
	 * @param WP_REST_Server   $server  Server instance.
	 * @param WP_REST_Request  $request Current request.
	 * @return mixed|WP_Error
	 */
	public static function check_rate_limit( $result, $server, $request ) {
		// Only apply to the canonical namespace.
		if ( ! self::is_agent_route( $request->get_route() ) ) {
			return $result;
		}

		// Allow disabling via bootstrap-resolved constant.
		if ( DIVISKIT_RATE_LIMIT_DISABLED ) {
			return $result;
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return $result; // Unauthenticated — permission callbacks will reject.
		}

		// Determine if this is a write operation.
		$method   = $request->get_method();
		$is_write = in_array( $method, [ 'POST', 'PUT', 'PATCH', 'DELETE' ], true );

		// Bootstrap-resolved constants are the single source of truth.
		$read_limit  = (int) DIVISKIT_RATE_LIMIT_READ;
		$write_limit = (int) DIVISKIT_RATE_LIMIT_WRITE;

		$limits = apply_filters( 'diviskit_rate_limits', [
			'read'  => $read_limit,
			'write' => $write_limit,
		] );
		if ( ! is_array( $limits ) || ! isset( $limits['read'], $limits['write'] ) ) {
			$limits = [ 'read' => $read_limit, 'write' => $write_limit ];
		}

		$limit         = $is_write ? (int) $limits['write'] : (int) $limits['read'];
		$bucket        = $is_write ? 'write' : 'read';
		$transient_key = "diviskit_rl_{$bucket}_{$user_id}";
		$now           = time();

		$data = get_transient( $transient_key );
		if ( false === $data || ! is_array( $data ) || ! isset( $data['count'], $data['window_start'] ) ) {
			// First request or corrupted transient — start new window.
			set_transient( $transient_key, [ 'count' => 1, 'window_start' => $now ], 60 );
			return $result;
		}

		// Reset window if 60s have elapsed.
		$elapsed = $now - (int) $data['window_start'];
		if ( $elapsed >= 60 ) {
			set_transient( $transient_key, [ 'count' => 1, 'window_start' => $now ], 60 );
			return $result;
		}

		$data['count']++;
		$remaining_ttl = max( 1, 60 - $elapsed );

		if ( $data['count'] > $limit ) {
			$retry_after = $remaining_ttl;

			$response = new WP_REST_Response( [
				'code'    => 'diviskit_rate_limit_exceeded',
				'message' => sprintf(
					'Rate limit exceeded: %d %s requests/minute. Retry after %d seconds.',
					$limit,
					$bucket,
					$retry_after
				),
				'data'    => [ 'status' => 429 ],
			], 429 );
			$response->header( 'Retry-After', $retry_after );
			$response->header( 'X-RateLimit-Limit', $limit );
			$response->header( 'X-RateLimit-Remaining', 0 );
			$response->header( 'X-RateLimit-Reset', (int) $data['window_start'] + 60 );

			return $response;
		}

		set_transient( $transient_key, $data, $remaining_ttl );

		return $result;
	}

	/**
	 * Permission tiers (all require Application Password auth):
	 *
	 * check_read_permission   — edit_posts      — read pages, modules, settings, icons, preset reads
	 * check_write_permission  — edit_pages      — page creation and content modification
	 * check_admin_permission  — manage_options  — theme options, preset cleanup/update/delete, library save
	 */
	public static function check_read_permission() {
		return current_user_can( 'edit_posts' );
	}

	public static function check_write_permission() {
		return current_user_can( 'edit_pages' );
	}

	/**
	 * Statuses intentionally supported by Diviskit page create/status routes.
	 *
	 * WordPress core can register additional workflow statuses, but accepting
	 * every non-internal status here would bypass the status-specific contract
	 * enforced by WP_REST_Posts_Controller::handle_status_param().
	 *
	 * @return string[]
	 */
	private static function supported_page_statuses(): array {
		return [ 'draft', 'pending', 'publish', 'future', 'private' ];
	}

	private static function page_status_requires_publish_capability( string $status ): bool {
		return in_array( $status, [ 'publish', 'future', 'private' ], true );
	}

	/**
	 * Convert a route-style capability error into the canonical Diviskit refusal.
	 *
	 * @param WP_Error $error Permission error from a request-aware guard.
	 * @return WP_REST_Response
	 */
	private static function post_type_permission_refusal( $error ) {
		$data = is_array( $error->get_error_data() ) ? $error->get_error_data() : [];
		unset( $data['status'] );
		return self::envelope_error(
			'forbidden',
			(string) $error->get_error_message(),
			'Authenticate as a user with the required content capability, then retry.',
			403,
			empty( $data ) ? null : $data
		);
	}

	/**
	 * Require mapped creation and publishing capabilities for fixed-publish CPT writes.
	 *
	 * @param string[] $post_types Post types the operation will create.
	 * @return true|WP_Error
	 */
	private static function published_post_types_permission_result( array $post_types ) {
		foreach ( array_values( array_unique( $post_types ) ) as $post_type_name ) {
			$post_type = get_post_type_object( $post_type_name );
			if ( ! $post_type || ! isset( $post_type->cap->create_posts, $post_type->cap->publish_posts ) ) {
				return new WP_Error(
					'rest_cannot_create',
					'Sorry, this content type does not expose the capabilities required for creation.',
					[ 'status' => 403, 'post_type' => $post_type_name ]
				);
			}
			foreach ( [ 'create_posts', 'publish_posts' ] as $cap_key ) {
				$capability = (string) $post_type->cap->{$cap_key};
				if ( '' === $capability || ! current_user_can( $capability ) ) {
					return new WP_Error(
						'create_posts' === $cap_key ? 'rest_cannot_create' : 'rest_cannot_publish',
						'Sorry, you are not allowed to create published content of this type.',
						[
							'status'              => 403,
							'post_type'           => $post_type_name,
							'required_capability' => $capability,
						]
					);
				}
			}
		}

		return true;
	}

	private static function fixed_publish_route_permission( string $base_capability, array $post_types ) {
		if ( ! current_user_can( $base_capability ) ) {
			return new WP_Error( 'rest_forbidden', 'Sorry, you are not allowed to perform this operation.', [ 'status' => 403 ] );
		}
		return self::published_post_types_permission_result( $post_types );
	}

	public static function check_canvas_create_permission() {
		return self::fixed_publish_route_permission( 'edit_pages', [ 'et_pb_canvas' ] );
	}

	public static function check_library_save_permission() {
		return self::fixed_publish_route_permission( 'manage_options', [ 'et_pb_layout' ] );
	}

	public static function check_tb_template_create_permission( $request ) {
		$post_types = [ 'et_theme_builder', 'et_template' ];
		$header_content = $request->get_param( 'header_content' );
		$footer_content = $request->get_param( 'footer_content' );
		$body_content   = $request->get_param( 'body_content' );
		if ( is_string( $header_content ) && '' !== $header_content ) {
			$post_types[] = 'et_header_layout';
		}
		if ( is_string( $footer_content ) && '' !== $footer_content ) {
			$post_types[] = 'et_footer_layout';
		}
		if ( is_string( $body_content ) && '' !== $body_content ) {
			$post_types[] = 'et_body_layout';
		}
		return self::fixed_publish_route_permission( 'manage_options', $post_types );
	}

	/**
	 * Resolve the request-aware page-create capability refusal, if any.
	 *
	 * @param WP_REST_Request|ArrayAccess $request REST-like request.
	 * @return true|WP_Error
	 */
	private static function page_create_permission_result( $request ) {
		$status = sanitize_key( (string) ( $request->get_param( 'status' ) ?? 'draft' ) );
		if ( ! in_array( $status, self::supported_page_statuses(), true ) ) {
			return new WP_Error(
				'rest_invalid_param',
				'Status is not supported for Diviskit page creation.',
				[ 'status' => 400 ]
			);
		}

		$post_type = get_post_type_object( 'page' );
		if ( ! $post_type || ! isset( $post_type->cap->create_posts ) ) {
			return new WP_Error(
				'rest_cannot_create',
				'The page post type does not expose the capabilities required for creation.',
				[ 'status' => 403 ]
			);
		}

		if ( ! current_user_can( $post_type->cap->create_posts ) ) {
			return new WP_Error(
				'rest_cannot_create',
				'Sorry, you are not allowed to create pages.',
				[
					'status'              => 403,
					'required_capability' => (string) $post_type->cap->create_posts,
				]
			);
		}

		$publish_capability = isset( $post_type->cap->publish_posts ) ? (string) $post_type->cap->publish_posts : '';
		if (
			self::page_status_requires_publish_capability( $status )
			&& ( '' === $publish_capability || ! current_user_can( $publish_capability ) )
		) {
			return new WP_Error(
				'rest_cannot_publish',
				'Sorry, you are not allowed to publish pages or create private pages.',
				[
					'status'              => 403,
					'required_capability' => $publish_capability,
					'requested_status'    => $status,
				]
			);
		}

		return true;
	}

	/**
	 * Request-aware REST permission callback for POST /page/create.
	 *
	 * @param WP_REST_Request $request Current REST request.
	 * @return true|WP_Error
	 */
	public static function check_page_create_permission( $request ) {
		return self::page_create_permission_result( $request );
	}

	/**
	 * Resolve post/page status-transition authority before a plan or mutation.
	 *
	 * @param WP_REST_Request|ArrayAccess $request REST-like request.
	 * @return true|WP_Error
	 */
	private static function page_update_status_permission_result( $request ) {
		$post_id = absint( $request['id'] ?? 0 );
		$status  = sanitize_key( (string) $request->get_param( 'status' ) );

		if ( ! in_array( $status, self::supported_page_statuses(), true ) ) {
			return new WP_Error(
				'rest_invalid_param',
				'Status is not supported for Diviskit post/page status updates.',
				[ 'status' => 400 ]
			);
		}

		$post = get_post( $post_id );
		if ( ! $post || ! in_array( (string) $post->post_type, [ 'post', 'page' ], true ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				'Status updates require an editable standard WordPress post or page.',
				[ 'status' => 403 ]
			);
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				'Sorry, you are not allowed to edit this post or page.',
				[ 'status' => 403 ]
			);
		}

		$post_type = get_post_type_object( $post->post_type );
		if ( ! $post_type ) {
			return new WP_Error(
				'rest_cannot_edit',
				'The post type does not expose the capability required for status updates.',
				[ 'status' => 403 ]
			);
		}

		$publish_capability = isset( $post_type->cap->publish_posts ) ? (string) $post_type->cap->publish_posts : '';
		if (
			self::page_status_requires_publish_capability( $status )
			&& ( '' === $publish_capability || ! current_user_can( $publish_capability ) )
		) {
			return new WP_Error(
				'rest_cannot_publish',
				'Sorry, you are not allowed to publish or make this post or page private.',
				[
					'status'              => 403,
					'required_capability' => $publish_capability,
					'requested_status'    => $status,
				]
			);
		}

		return true;
	}

	/**
	 * Request-aware REST permission callback for POST /page/update-status/{id}.
	 *
	 * @param WP_REST_Request $request Current REST request.
	 * @return true|WP_Error
	 */
	public static function check_page_update_status_permission( $request ) {
		return self::page_update_status_permission_result( $request );
	}

	public static function check_authenticated_permission() {
		return get_current_user_id() > 0;
	}

	public static function check_admin_permission() {
		return current_user_can( 'manage_options' );
	}

	public static function check_menu_permission() {
		return current_user_can( 'edit_theme_options' );
	}

	public static function register_routes() {

		// ── Handshake (always available, even without Divi) ──────────
		self::register_route( '/handshake', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'handshake' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'mcp_server_version' => [ 'required' => true, 'type' => 'string' ],
			],
		] );

		// ── Skill bundles ───────────────────────────────────────────
		// Canonical authoring knowledge shipped inside the plugin.
		// Registered before the Divi guard so clients can sync skills
		// even while Divi is inactive. Canonical diviskit/v1 only —
		// new routes are not dual-registered on the legacy namespace.
		register_rest_route( self::REST_NAMESPACE, '/skills', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'skills_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		register_rest_route( self::REST_NAMESPACE, '/skills/(?P<name>[a-z0-9-]+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'skills_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		// Divi availability guard — still requires auth to avoid exposing plugin status.
		if ( ! function_exists( 'et_get_option' ) ) {
			self::register_route( '/(?P<path>.*)', [
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => function () {
					return new WP_Error(
						'divi_unavailable',
						'Divi theme is not active. Activate Divi before using the MCP agent.',
						[ 'status' => 503 ]
					);
				},
				'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			] );
			return;
		}

		self::register_route( '/scf/text-value/update', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'scf_text_value_update' ],
			'permission_callback' => [ __CLASS__, 'check_authenticated_permission' ],
			// Validate without coercion in the handler; framework diagnostics must
			// not reflect rejected field values. Omission means preview, never apply.
		] );

		// ── Read Operations ──────────────────────────────────────────

		self::register_route( '/seo/provider/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'seo_provider_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/seo/metadata/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'seo_metadata_get' ],
			// Preserve the handler's canonical target-specific error envelope.
			'permission_callback' => [ __CLASS__, 'check_authenticated_permission' ],
			'args'                => [
				'id'       => [ 'required' => true, 'type' => 'integer', 'minimum' => 1 ],
				'provider' => [
					'required' => false,
					'type'     => 'string',
					'enum'     => [ 'auto', 'tsf' ],
					'default'  => 'auto',
				],
			],
		] );

		self::register_route( '/seo/metadata/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'seo_metadata_update' ],
			// The handler enforces edit_post before any provider payload read.
			'permission_callback' => [ __CLASS__, 'check_authenticated_permission' ],
			'args'                => [
				'id'                => [ 'required' => true, 'type' => 'integer', 'minimum' => 1 ],
				'provider'          => [
					'required' => false,
					'type'     => 'string',
					'enum'     => [ 'auto', 'tsf' ],
					'default'  => 'auto',
				],
				'expected_checksum' => [
					'required' => true,
					'type'     => 'string',
					'pattern'  => '^sha256:[a-f0-9]{64}$',
				],
				'changes'            => [
					'required' => true,
					'type'     => 'array',
					'minItems' => 1,
					'maxItems' => 2,
					'items'    => [
						'type'                 => 'object',
						'required'             => [ 'field', 'action' ],
						'additionalProperties' => false,
						'properties'           => [
							'field'  => [ 'type' => 'string', 'enum' => [ 'seo_title', 'meta_description' ] ],
							'action' => [ 'type' => 'string', 'enum' => [ 'set', 'clear' ] ],
							'value'  => [ 'type' => 'string' ],
						],
					],
				],
				'dry_run'            => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/page/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'page_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/page/get/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'page_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'id' => [
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return is_numeric( $param );
					},
				],
			],
		] );

		self::register_route( '/page/get-layout/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'page_get_layout' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'full' => [
					'default'     => false,
					'type'        => 'boolean',
					'description' => 'Include full block attrs and raw content (default: false for slim targeting-only response)',
				],
			],
		] );

		self::register_route( '/schema/modules', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'schema_list_modules' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/schema/module/dump-all', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'schema_get_module_dump_all' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/schema/module/(?P<name>[a-zA-Z0-9_/-]+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'schema_get_module' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/schema/settings', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'schema_get_settings' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/global-color/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'global_color_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		// Storage-path contract (#719) — admin-only audit aggregator.
		// Surfaces per-entry provenance + warnings across all candidate
		// storage paths for the global_colors surface (D5 nested,
		// hypothetical top-level, and WP-customizer-bound defaults). Like
		// /preset/scan-orphans this is admin-only because the union
		// payload includes synthetic-id metadata derived from the
		// GlobalData class property, which carries inventory-leak
		// implications via Editor read access.
		self::register_route( '/global-color/audit-storage', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'global_color_audit_storage' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
		] );

		self::register_route( '/global-font/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'global_font_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		// Storage-path contract (#719) — admin-only audit aggregator across
		// the gfid-* catalog (et_divi.et_global_data.global_fonts) AND the
		// `et_uploaded_fonts` local-hosted Pattern B surface.
		self::register_route( '/global-font/audit-storage', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'global_font_audit_storage' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
		] );

		// Global fonts CRUD — parallel to /global-color/* but with
		// gfid-* IDs stored under `et_global_data.global_fonts`. Distinct
		// from /variable/* which writes `gvid-*` fonts under
		// `et_global_data.global_variables.fonts` (variable manager surface).
		self::register_route( '/global-font/create', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'global_font_create' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'id'       => [ 'required' => false, 'type' => 'string' ],
				'family'   => [ 'required' => false, 'type' => 'string' ],
				'source'   => [ 'required' => false, 'type' => 'string' ],
				'weights'  => [ 'required' => false, 'type' => 'array' ],
				'subsets'  => [ 'required' => false, 'type' => 'array' ],
				'label'    => [ 'required' => false, 'type' => 'string' ],
				'fallback' => [ 'required' => false, 'type' => 'string' ],
				'status'   => [ 'required' => false, 'type' => 'string' ],
				'dry_run'  => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/global-font/update', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'global_font_update' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'id'       => [ 'required' => true,  'type' => 'string' ],
				'family'   => [ 'required' => false, 'type' => 'string' ],
				'source'   => [ 'required' => false, 'type' => 'string' ],
				'weights'  => [ 'required' => false, 'type' => 'array' ],
				'subsets'  => [ 'required' => false, 'type' => 'array' ],
				'label'    => [ 'required' => false, 'type' => 'string' ],
				'fallback' => [ 'required' => false, 'type' => 'string' ],
				'status'   => [ 'required' => false, 'type' => 'string' ],
				'dry_run'  => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/global-font/delete', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'global_font_delete' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'id'      => [ 'required' => true,  'type' => 'string' ],
				'force'   => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'dry_run' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/global-color/upsert', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'global_color_upsert' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'colors' => [ 'required' => true, 'type' => 'array' ],
				'mode'   => [ 'required' => false, 'type' => 'string', 'default' => 'merge' ],
			],
		] );

		self::register_route( '/global-color/delete', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'global_color_delete' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'gcid'  => [ 'required' => true,  'type' => 'string' ],
				'force' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/theme-options', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'update_theme_options' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'options' => [ 'required' => true, 'type' => 'object' ],
			],
		] );

		self::register_route( '/preset/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'preset_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/preset/audit', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'preset_audit' ],
			// Admin-only: response includes per-preset page_refs (page IDs + titles correlated with preset usage) — inventory-leak risk via Editor read access. Symmetric with /preset/scan-orphans and /variable/scan-orphans (#501).
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
		] );

		// Storage-path contract (#719) — admin-only audit aggregator across
		// all candidate D5 preset paths PLUS the OUT-OF-BAND `_ng` legacy
		// D4 store. Distinct from /preset/audit (which audits preset
		// CONTENT — usage refs, orphans, defaults, etc.); this surface
		// audits preset STORAGE LOCATION with per-entry provenance and
		// `legacy_d4_ng` tagging. Admin-only for symmetry with the existing
		// /preset/audit gate.
		self::register_route( '/preset/audit-storage', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'preset_audit_storage' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
		] );

		self::register_route( '/preset/inspect/(?P<preset_id>[^/]+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'preset_inspect' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'preset_id' => [ 'required' => true, 'type' => 'string' ],
			],
		] );

		self::register_route( '/preset/registry-doctor', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'preset_registry_doctor' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'repair'                 => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'clear_chunk_transients' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'dry_run'                => [ 'required' => false, 'type' => 'boolean', 'default' => true ],
				'limit'                  => [ 'required' => false, 'type' => 'integer', 'default' => 100, 'minimum' => 1, 'maximum' => 500 ],
			],
		] );

		self::register_route( '/rollback-snapshot/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'rollback_snapshot_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'target_kind' => [ 'required' => false, 'type' => 'string' ],
				'target_id'   => [ 'required' => false, 'type' => 'integer' ],
				'status'      => [ 'required' => false, 'type' => 'string' ],
				'limit'       => [ 'required' => false, 'type' => 'integer', 'default' => 20 ],
			],
		] );

		self::register_route( '/rollback-snapshot/get/(?P<snapshot_id>[^/]+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'rollback_snapshot_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'snapshot_id'   => [ 'required' => true, 'type' => 'string' ],
				'include_value' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/rollback-snapshot/delete/(?P<snapshot_id>[^/]+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'rollback_snapshot_delete' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'snapshot_id' => [ 'required' => true, 'type' => 'string' ],
			],
		] );

		self::register_route( '/rollback-snapshot/restore/(?P<snapshot_id>[^/]+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'rollback_snapshot_restore' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'snapshot_id' => [ 'required' => true, 'type' => 'string' ],
				'dry_run'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/preset/cleanup', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'preset_cleanup' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'dry_run' => [ 'type' => 'boolean', 'default' => true ],
				'dedup'   => [ 'type' => 'boolean', 'default' => false ],
				'action'  => [ 'type' => 'string', 'default' => '' ],
				'prefix'  => [ 'type' => 'string', 'default' => '' ],
			],
		] );

		self::register_route( '/preset/update', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'preset_update' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'preset_id' => [ 'required' => true, 'type' => 'string' ],
				'name'      => [ 'required' => false, 'type' => 'string' ],
				'attrs'     => [ 'required' => false, 'type' => 'object' ],
				'priority'  => [ 'required' => false, 'type' => 'integer' ],
			],
		] );

		self::register_route( '/preset/delete', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'preset_delete' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'preset_id' => [ 'required' => true,  'type' => 'string' ],
				'force'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/preset/create', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'preset_create' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'module_name'       => [ 'required' => true,  'type' => 'string' ],
				'name'              => [ 'required' => true,  'type' => 'string' ],
				'attrs'             => [ 'required' => true,  'type' => 'object' ],
				'type'              => [ 'required' => false, 'type' => 'string', 'default' => 'module' ],
				'group_name'        => [ 'required' => false, 'type' => 'string' ],
				'group_id'          => [ 'required' => false, 'type' => 'string' ],
				'primary_attr_name' => [ 'required' => false, 'type' => 'string' ],
				'make_default'      => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'priority'          => [ 'required' => false, 'type' => 'integer' ],
			],
		] );

		self::register_route( '/preset/reassign', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'preset_reassign' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'old_uuid'     => [ 'required' => true,  'type' => 'string' ],
				'new_uuid'     => [ 'required' => true,  'type' => 'string' ],
				'page_ids'     => [
					'required' => false,
					'type'     => 'array',
					'items'    => [ 'type' => 'integer' ],
					'validate_callback' => static function ( $value ) {
						if ( ! is_array( $value ) ) {
							return new WP_Error( 'rest_invalid_param', 'page_ids must be an array of positive integers', [ 'status' => 400 ] );
						}
						foreach ( $value as $v ) {
							if ( ! is_numeric( $v ) || (int) $v <= 0 || (float) $v !== (float) (int) $v ) {
								return new WP_Error( 'rest_invalid_param', 'page_ids must contain only positive integers', [ 'status' => 400 ] );
							}
						}
						return true;
					},
					'sanitize_callback' => static function ( $value ) {
						return array_map( 'absint', (array) $value );
					},
				],
				'mode'         => [ 'required' => false, 'type' => 'string', 'default' => 'dry-run', 'enum' => [ 'dry-run', 'apply' ] ],
				'strip_inline' => [ 'required' => false, 'type' => 'boolean', 'default' => true ],
				'scope'        => [ 'required' => false, 'type' => 'string', 'default' => 'both', 'enum' => [ 'module', 'group', 'both' ] ],
			],
		] );

		self::register_route( '/preset/scan-orphans', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'preset_scan_orphans' ],
			// Admin-only: response includes page IDs + titles correlated to preset refs — inventory-leak risk.
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
		] );

		self::register_route( '/preset/set-default', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'preset_set_default' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				// Two addressing modes:
				//   1. preset_id (existing): set/clear default by walking items[] for that UUID.
				//   2. type+module (bucket-addressed clear): clear an orphan default pointer
				//      when preset_id no longer exists in items[]. Requires unset=true.
				'preset_id' => [ 'required' => false, 'type' => 'string' ],
				'type'      => [ 'required' => false, 'type' => 'string', 'enum' => [ 'module', 'group' ] ],
				'module'    => [ 'required' => false, 'type' => 'string' ],
				'unset'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		// ── Library Operations ───────────────────────────────────────

		self::register_route( '/library/items', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'library_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'layout_type' => [ 'required' => false, 'type' => 'string', 'default' => '' ],
				'scope'       => [ 'required' => false, 'type' => 'string', 'default' => '' ],
				'per_page'    => [ 'required' => false, 'type' => 'integer', 'default' => 50 ],
				'page'        => [ 'required' => false, 'type' => 'integer', 'default' => 1 ],
			],
		] );

		self::register_route( '/library/item/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'library_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/library/save', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'library_save' ],
			'permission_callback' => [ __CLASS__, 'check_library_save_permission' ],
			'args'                => [
				'title'       => [ 'required' => true, 'type' => 'string' ],
				'content'     => [ 'required' => true, 'type' => 'string' ],
				'layout_type' => [ 'required' => false, 'type' => 'string', 'default' => 'section' ],
				'scope'       => [ 'required' => false, 'type' => 'string', 'default' => 'non_global' ],
			],
		] );

		// ── Theme Builder Operations ────────────────────────────────

		self::register_route( '/theme-builder/template/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'tb_template_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'per_page' => [ 'type' => 'integer', 'default' => 50 ],
				'page'     => [ 'type' => 'integer', 'default' => 1 ],
			],
		] );

		self::register_route( '/theme-builder/layout/get/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'tb_layout_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/theme-builder/layout/update/(?P<id>\d+)', [
			'methods'             => 'PUT',
			'callback'            => [ __CLASS__, 'tb_layout_update' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'content' => [ 'required' => true, 'type' => 'string' ],
				'dry_run' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'  => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/theme-builder/layout/block-insert/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'tb_layout_block_insert' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'content'         => [ 'required' => true, 'type' => 'string' ],
				'parent_selector' => [ 'required' => false, 'type' => 'string' ],
				'parent_path'     => [ 'required' => false, 'type' => 'string' ],
				'position'        => [ 'required' => false, 'type' => 'string', 'default' => 'append' ],
				'dry_run'         => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'          => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/theme-builder/template/create', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'tb_template_create' ],
			'permission_callback' => [ __CLASS__, 'check_tb_template_create_permission' ],
			'args'                => [
				'title'          => [ 'required' => true, 'type' => 'string' ],
				'condition'      => [ 'required' => true, 'type' => 'string' ],
				'header_content' => [ 'required' => false, 'type' => 'string', 'default' => '' ],
				'footer_content' => [ 'required' => false, 'type' => 'string', 'default' => '' ],
				'body_content'   => [ 'required' => false, 'type' => 'string', 'default' => '' ],
			],
		] );

		self::register_route( '/theme-builder/template/trash/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'tb_template_trash' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'id'      => [ 'required' => true ],
				'force'   => [
					'required'    => false,
					'type'        => 'boolean',
					'default'     => false,
					'description' => 'When true, permanently delete (wp_delete_post). Default false moves to trash.',
				],
				'dry_run' => [
					'required'    => false,
					'type'        => 'boolean',
					'default'     => false,
					'description' => 'When true, return the change plan without mutating state.',
				],
			],
		] );

		self::register_route( '/meta/find-icon', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'search_icons' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'q'    => [ 'required' => true, 'type' => 'string' ],
				'type' => [ 'required' => false, 'type' => 'string', 'default' => 'all' ],
				'limit' => [ 'required' => false, 'type' => 'integer', 'default' => 10 ],
			],
		] );

		self::register_route( '/menu/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'menu_list' ],
			'permission_callback' => [ __CLASS__, 'check_menu_permission' ],
		] );

		self::register_route( '/menu/get/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'menu_get' ],
			'permission_callback' => [ __CLASS__, 'check_menu_permission' ],
			'args'                => [
				'id' => [ 'required' => true, 'type' => 'integer' ],
			],
		] );

		// ── Write Operations ─────────────────────────────────────────

		self::register_route( '/menu/create', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'menu_create' ],
			'permission_callback' => [ __CLASS__, 'check_menu_permission' ],
			'args'                => [
				'name'    => [ 'required' => true, 'type' => 'string' ],
				'slug'    => [ 'required' => false, 'type' => 'string' ],
				'dry_run' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/menu/item/add-page', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'menu_item_add_page' ],
			'permission_callback' => [ __CLASS__, 'check_menu_permission' ],
			'args'                => [
				'menu_id'        => [ 'required' => true, 'type' => 'integer' ],
				'page_id'        => [ 'required' => true, 'type' => 'integer' ],
				'label'          => [ 'required' => false, 'type' => 'string' ],
				'parent_item_id' => [ 'required' => false, 'type' => 'integer', 'default' => 0 ],
				'dry_run'        => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/menu/item/add-custom', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'menu_item_add_custom' ],
			'permission_callback' => [ __CLASS__, 'check_menu_permission' ],
			'args'                => [
				'menu_id'        => [ 'required' => true, 'type' => 'integer' ],
				'label'          => [ 'required' => true, 'type' => 'string' ],
				'url'            => [ 'required' => true, 'type' => 'string' ],
				'parent_item_id' => [ 'required' => false, 'type' => 'integer', 'default' => 0 ],
				'dry_run'        => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/menu/location/assign', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'menu_location_assign' ],
			'permission_callback' => [ __CLASS__, 'check_menu_permission' ],
			'args'                => [
				'menu_id'  => [ 'required' => true, 'type' => 'integer' ],
				'location' => [ 'required' => true, 'type' => 'string' ],
				'dry_run'  => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/page/update-content/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'page_update_content' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'      => [ 'required' => true ],
				'content' => [
					'required' => true,
					'type'     => 'string',
				],
				'expected_checksum' => [
					'required' => false,
					'type'     => 'string',
					'pattern'  => '^sha256:[a-f0-9]{64}$',
				],
				'dry_run' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'  => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/page/update-meta/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'page_update_meta' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'                => [ 'required' => true ],
				'title'             => [ 'required' => false, 'type' => 'string' ],
				'post_title'        => [ 'required' => false, 'type' => 'string' ],
				'slug'              => [ 'required' => false, 'type' => 'string' ],
				'post_name'         => [ 'required' => false, 'type' => 'string' ],
				'parent'            => [ 'required' => false, 'type' => 'integer' ],
				'post_parent'       => [ 'required' => false, 'type' => 'integer' ],
				'menu_order'        => [ 'required' => false, 'type' => 'integer' ],
				'preserve_old_slug' => [
					'required'    => false,
					'type'        => 'boolean',
					'default'     => true,
					'description' => 'When true, record the previous slug in _wp_old_slug for published posts when slug changes.',
				],
				'dry_run'           => [
					'required'    => false,
					'type'        => 'boolean',
					'default'     => false,
					'description' => 'When true, return the change plan without mutating state.',
				],
			],
		] );

		self::register_route( '/page/set-meta/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'page_set_meta' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'       => [ 'required' => true ],
				'template' => [ 'required' => false, 'type' => 'string' ],
			],
		] );

		self::register_route( '/page/trash/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'page_trash' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'      => [ 'required' => true ],
				'force'   => [
					'required'    => false,
					'type'        => 'boolean',
					'default'     => false,
					'description' => 'When true, permanently delete (wp_delete_post). Default false moves to trash.',
				],
				'dry_run' => [
					'required'    => false,
					'type'        => 'boolean',
					'default'     => false,
					'description' => 'When true, return the change plan without mutating state.',
				],
			],
		] );

		self::register_route( '/page/update-status/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'page_update_status' ],
			'permission_callback' => [ __CLASS__, 'check_page_update_status_permission' ],
			'args'                => [
				'id'       => [ 'required' => true ],
				'status'   => [
					'required'    => true,
					'type'        => 'string',
					'enum'        => [ 'publish', 'draft', 'private', 'pending', 'future' ],
					'description' => 'Target post status.',
				],
				'date_gmt' => [
					'required'    => false,
					'type'        => 'string',
					'description' => 'Required when status="future" (ISO 8601 UTC). Future dates only.',
				],
				'dry_run'  => [
					'required'    => false,
					'type'        => 'boolean',
					'default'     => false,
					'description' => 'When true, return the change plan without mutating state.',
				],
			],
		] );

		self::register_route( '/section/append/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'section_append' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'      => [ 'required' => true ],
				'content' => [
					'required'    => true,
					'type'        => 'string',
					'description' => 'Divi section block markup to append (<!-- wp:divi/section ...-->...<!-- /wp:divi/section -->)',
				],
				'position' => [
					'required' => false,
					'type'     => 'string',
					'default'  => 'end',
					'enum'     => [ 'start', 'end' ],
				],
				'dry_run'  => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'   => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/section/replace/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'section_replace' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [
					'type'        => 'string',
					'description' => 'Admin label of the section to replace',
				],
				'match_text' => [
					'type'        => 'string',
					'description' => 'Text to search for in section content (case-insensitive substring)',
				],
				'occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'description'       => 'Which occurrence to target when multiple sections match (1-based)',
					'sanitize_callback' => 'absint',
				],
				'content'    => [
					'required'    => true,
					'type'        => 'string',
					'description' => 'New section block markup to replace the matched section',
				],
				'dry_run'    => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/section/remove/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'section_remove' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [
					'type'        => 'string',
					'description' => 'Admin label of the section to remove',
				],
				'match_text' => [
					'type'        => 'string',
					'description' => 'Text to search for in section content (case-insensitive substring)',
				],
				'occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'description'       => 'Which occurrence to target when multiple sections match (1-based)',
					'sanitize_callback' => 'absint',
				],
				'dry_run'    => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/section/get/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'section_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [
					'type'        => 'string',
					'description' => 'Admin label of the section to retrieve',
				],
				'match_text' => [
					'type'        => 'string',
					'description' => 'Text to search for in section content (case-insensitive substring)',
				],
				'occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'description'       => 'Which occurrence to target when multiple sections match (1-based)',
					'sanitize_callback' => 'absint',
				],
			],
		] );

		self::register_route( '/module/update/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'module_update' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [
					'required'    => false,
					'type'        => 'string',
					'description' => 'Admin label of the module to update (exact match)',
				],
				'match_text'  => [
					'required'    => false,
					'type'        => 'string',
					'description' => 'Text content to search for in innerContent (case-insensitive substring match, first match wins). Prefer auto_index for generic or repeated text; content-slot mismatches are rejected instead of silently storing never-rendered attrs.',
				],
				'auto_index'  => [
					'required'    => false,
					'type'        => 'string',
					'description' => 'Auto-index target in "type:N" format (e.g. "text:5", "icon:3"). Takes priority over label and match_text.',
				],
				'occurrence'  => [
					'default'           => 1,
					'type'              => 'integer',
					'description'       => 'Which occurrence to target when multiple modules share the same label (1-based). Only used with label targeting.',
					'sanitize_callback' => 'absint',
				],
				'attrs'       => [
					'required'    => true,
					'type'        => 'object',
					'description' => 'Attribute key-value pairs to merge (dot notation)',
				],
				'dry_run'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'      => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/module/get/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'module_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [
					'required'    => false,
					'type'        => 'string',
					'description' => 'Admin label of the module to retrieve (exact match)',
				],
				'match_text' => [
					'required'    => false,
					'type'        => 'string',
					'description' => 'Text content to search for in module attrs/innerContent (case-insensitive substring match, first match wins)',
				],
				'auto_index' => [
					'required'    => false,
					'type'        => 'string',
					'description' => 'Auto-index target in "type:N" format (e.g. "text:5", "icon:3").',
				],
				'occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'description'       => 'Which occurrence to target when multiple modules share the same label (1-based). Only used with label targeting.',
					'sanitize_callback' => 'absint',
				],
				'full'       => [
					'default'           => false,
					'type'              => 'boolean',
					'description'       => 'Include decoded attrs and raw serialized block markup for the matched module only.',
					'sanitize_callback' => 'rest_sanitize_boolean',
				],
			],
		] );

		self::register_route( '/module/move/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'module_move' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id' => [ 'required' => true ],
				'source_label' => [
					'type'        => 'string',
					'description' => 'Admin label of the module to move (exact match)',
				],
				'source_match_text' => [
					'type'        => 'string',
					'description' => 'Text to search for in source module (case-insensitive substring)',
				],
				'source_auto_index' => [
					'type'        => 'string',
					'description' => 'Auto-index of the module to move in "type:N" format (e.g. "text:3")',
				],
				'source_occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'description'       => 'Which occurrence when multiple sources match by label (1-based)',
					'sanitize_callback' => 'absint',
				],
				'target_label' => [
					'type'        => 'string',
					'description' => 'Admin label of the reference module (exact match)',
				],
				'target_match_text' => [
					'type'        => 'string',
					'description' => 'Text to search for in target module (case-insensitive substring)',
				],
				'target_auto_index' => [
					'type'        => 'string',
					'description' => 'Auto-index of the reference module in "type:N" format (e.g. "text:5")',
				],
				'target_occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'description'       => 'Which occurrence when multiple targets match by label (1-based)',
					'sanitize_callback' => 'absint',
				],
				'position' => [
					'required'    => true,
					'type'        => 'string',
					'description' => 'Where to place the source relative to the target: "before" or "after"',
					'enum'        => [ 'before', 'after' ],
				],
				'dry_run'  => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'   => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		// Module state: lock / unlock / clone. Targeting follows the same
		// label/match_text/auto_index pattern as module_update so callers reuse
		// the same mental model.
		self::register_route( '/module/lock/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'module_lock' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [ 'type' => 'string' ],
				'match_text' => [ 'type' => 'string' ],
				'auto_index' => [ 'type' => 'string' ],
				'occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				],
				'dry_run'    => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/module/unlock/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'module_unlock' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [ 'type' => 'string' ],
				'match_text' => [ 'type' => 'string' ],
				'auto_index' => [ 'type' => 'string' ],
				'occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				],
				'dry_run'    => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/module/clone/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'module_clone' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'id'         => [ 'required' => true ],
				'label'      => [ 'type' => 'string' ],
				'match_text' => [ 'type' => 'string' ],
				'auto_index' => [ 'type' => 'string' ],
				'occurrence' => [
					'default'           => 1,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				],
				'position'   => [
					'default'     => 'after',
					'type'        => 'string',
					'enum'        => [ 'before', 'after' ],
					'description' => 'Place the clone "before" or "after" the source module within its parent.',
				],
				'dry_run'    => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'backup'     => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		// /render + /validate/blocks: accept EITHER `content` (inline markup)
		// OR `page_id` (load post_content from DB). Exactly-one contract is
		// enforced in the handler via self::resolve_content_or_page_id(); both
		// args are 'required' => false at the REST layer so the resolver can
		// emit the typed `invalid_input` envelope (which beats a generic
		// rest_missing_callback_param 400 from the REST framework).
		self::register_route( '/render', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'render_block_markup' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'content' => [
					'required'          => false,
					'type'              => 'string',
				],
				'page_id' => [
					'required' => false,
					'type'     => 'integer',
					// No `absint` sanitize: it coerces -1 → 1 (a valid post),
					// masking the negative-input case. We validate >0 in the
					// handler via self::resolve_content_or_page_id() to keep
					// the explicit invalid_input branch reachable.
				],
			],
		] );

		self::register_route( '/validate/blocks', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'validate_blocks' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'content' => [
					'required'          => false,
					'type'              => 'string',
				],
				'page_id' => [
					'required' => false,
					'type'     => 'integer',
					// No `absint` sanitize: it coerces -1 → 1 (a valid post),
					// masking the negative-input case. We validate >0 in the
					// handler via self::resolve_content_or_page_id() to keep
					// the explicit invalid_input branch reachable.
				],
			],
		] );

		self::register_route( '/page/create', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'page_create' ],
			'permission_callback' => [ __CLASS__, 'check_page_create_permission' ],
			'args'                => [
				'title'   => [ 'required' => true, 'type' => 'string' ],
				'content' => [ 'required' => false, 'type' => 'string', 'default' => '' ],
				'status'  => [
					'required' => false,
					'type'     => 'string',
					'default'  => 'draft',
					'enum'     => [ 'draft', 'pending', 'publish', 'future', 'private' ],
				],
				'dry_run' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		// ── Canvas Operations ────────────────────────────────────────

		self::register_route( '/canvas/create', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'canvas_create' ],
			'permission_callback' => [ __CLASS__, 'check_canvas_create_permission' ],
			'args'                => [
				'title'          => [ 'required' => true, 'type' => 'string' ],
				'parent_page_id' => [ 'required' => true, 'type' => 'integer' ],
				'content'        => [ 'required' => false, 'type' => 'string', 'default' => '' ],
				'canvas_id'      => [ 'required' => false, 'type' => 'string' ],
				'append_to_main' => [ 'required' => false, 'type' => 'string' ],
				'z_index'        => [ 'required' => false, 'type' => 'integer' ],
			],
		] );

		self::register_route( '/canvas/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'canvas_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'parent_page_id' => [ 'required' => false, 'type' => 'integer' ],
				'per_page'       => [ 'required' => false, 'type' => 'integer', 'default' => 50 ],
				'page'           => [ 'required' => false, 'type' => 'integer', 'default' => 1 ],
			],
		] );

		self::register_route( '/canvas/orphan-audit', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'canvas_orphan_audit' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'parent_page_id'  => [ 'required' => false, 'type' => 'integer' ],
				'include_global'  => [ 'required' => false, 'type' => 'boolean', 'default' => true ],
				'include_context' => [ 'required' => false, 'type' => 'boolean', 'default' => true ],
				'status'          => [ 'required' => false, 'type' => 'string', 'default' => 'any' ],
				'per_page'        => [ 'required' => false, 'type' => 'integer', 'default' => 100 ],
				'page'            => [ 'required' => false, 'type' => 'integer', 'default' => 1 ],
			],
		] );

		self::register_route( '/canvas/get/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'canvas_get' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
		] );

		self::register_route( '/canvas/update/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'canvas_update' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
			'args'                => [
				'content'        => [ 'required' => false, 'type' => 'string' ],
				'title'          => [ 'required' => false, 'type' => 'string' ],
				'append_to_main' => [ 'required' => false, 'type' => 'string' ],
				'z_index'        => [ 'required' => false, 'type' => 'integer' ],
			],
		] );

		self::register_route( '/canvas/delete/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'canvas_delete' ],
			'permission_callback' => [ __CLASS__, 'check_write_permission' ],
		] );

		self::register_route( '/canvas/duplicate/(?P<id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'canvas_duplicate' ],
			'permission_callback' => [ __CLASS__, 'check_canvas_create_permission' ],
			'args'                => [
				'title'   => [ 'required' => false, 'type' => 'string' ],
				'dry_run' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		// ── Variable Manager CRUD ──────────────────────────────────────
		self::register_route( '/variable/list', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'variable_list' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'type'   => [ 'required' => false, 'type' => 'string' ],
				'prefix' => [ 'required' => false, 'type' => 'string' ],
			],
		] );

		self::register_route( '/variable/create', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'variable_create' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'type'              => [ 'required' => true, 'type' => 'string' ],
				'id'                => [ 'required' => false, 'type' => 'string' ],
				'label'             => [ 'required' => true, 'type' => 'string' ],
				// Not required at the route layer — callback validates that
				// either value OR fluid params (min+max or targets) is present
				// and returns the richer 400 (fluid_value_conflict / etc).
				'value'             => [ 'required' => false, 'type' => 'string' ],
				// Structured gradient settings for type=gradients — the callback
				// serializes the canonical $variable(gradient) token (#921).
				'gradient'          => [ 'required' => false, 'type' => 'object' ],
				'min'               => [ 'required' => false, 'type' => 'string' ],
				'max'               => [ 'required' => false, 'type' => 'string' ],
				'targets'           => [ 'required' => false, 'type' => 'object' ],
				'output_unit'       => [ 'required' => false, 'type' => 'string' ],
				'root_font_size_px' => [ 'required' => false, 'type' => 'number' ],
			],
		] );

		self::register_route( '/variable/delete', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'variable_delete' ],
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'id'    => [ 'required' => true, 'type' => 'string' ],
				'force' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/variable/scan-orphans', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'variable_scan_orphans' ],
			// Admin-only: response correlates variable IDs with page titles — inventory-leak risk (matches /preset/scan-orphans).
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
		] );

		self::register_route( '/variable/create-fluid-system', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'variable_create_fluid_system' ],
			// Admin-only: bulk write to the variable registry.
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'profile'           => [ 'required' => false, 'type' => 'string', 'default' => 'divi-default' ],
				'custom_anchors'    => [ 'required' => false, 'type' => 'object' ],
				'typography'        => [ 'required' => false, 'type' => 'object' ],
				'spacing'           => [ 'required' => false, 'type' => 'object' ],
				'radius'            => [ 'required' => false, 'type' => 'object' ],
				'namespace'         => [ 'required' => false, 'type' => 'string', 'default' => 'oa' ],
				'output_unit'       => [ 'required' => false, 'type' => 'string' ],
				'root_font_size_px' => [ 'required' => false, 'type' => 'number' ],
				'dry_run'           => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'overwrite'         => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );

		self::register_route( '/variable/used-on-page/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'variable_used_on_page' ],
			'permission_callback' => [ __CLASS__, 'check_read_permission' ],
			'args'                => [
				'id' => [ 'required' => true, 'type' => 'integer' ],
			],
		] );

		self::register_route( '/meta/flush-cache', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'flush_static_cache' ],
			// Admin-only: performs filesystem deletes under wp-content/et-cache/.
			'permission_callback' => [ __CLASS__, 'check_admin_permission' ],
			'args'                => [
				'post_id'                => [ 'required' => false, 'type' => 'integer' ],
				'all'                    => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'after'                  => [ 'required' => false, 'type' => 'integer' ],
				'cleanup_dynamic_assets' => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'cleanup_canvas_refs'    => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
				'dry_run'                => [ 'required' => false, 'type' => 'boolean', 'default' => false ],
			],
		] );
	}

	// ── Admin Settings Page ─────────────────────────────────────

	private static $admin_page_hook = '';

	public static function register_admin_page() {
		self::$admin_page_hook = add_menu_page(
			'Diviskit',
			'Diviskit',
			'manage_options',
			'diviskit',
			[ __CLASS__, 'render_admin_page' ],
			self::admin_menu_icon(),
			81
		);
	}

	public static function enqueue_admin_styles( $hook ): void {
		if ( ! self::$admin_page_hook || self::$admin_page_hook !== $hook ) {
			return;
		}
		wp_enqueue_style( 'diviskit-agent-admin', plugins_url( 'assets/admin.css', __FILE__ ), [ 'dashicons' ], self::VERSION );
		wp_enqueue_script( 'diviskit-agent-admin', plugins_url( 'assets/admin.js', __FILE__ ), [], self::VERSION, true );
		wp_localize_script( 'diviskit-agent-admin', 'diviskitAdmin', [ 'root' => rest_url( self::REST_NAMESPACE . '/' ), 'nonce' => wp_create_nonce( 'wp_rest' ) ] );
		if ( current_user_can( 'manage_options' ) && function_exists( 'et_get_option' ) && isset( $_GET['view'] ) && is_string( $_GET['view'] ) && 'design-system' === $_GET['view'] && 'design-system' === sanitize_key( wp_unslash( $_GET['view'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fixed read-only navigation; exact-token guard rejects normalization variants. No state change.
			wp_enqueue_style( 'diviskit-design-system', plugins_url( 'assets/design-system.css', __FILE__ ), [ 'diviskit-agent-admin' ], self::VERSION );
			wp_enqueue_script( 'diviskit-design-system', plugins_url( 'assets/design-system.js', __FILE__ ), [], self::VERSION, true );
			wp_localize_script( 'diviskit-design-system', 'diviskitDesignSystem', [ 'root' => rest_url( self::REST_NAMESPACE . '/' ), 'nonce' => wp_create_nonce( 'wp_rest' ) ] );
		}
	}

	private static function admin_menu_icon(): string {
		$svg_path = plugin_dir_path( __FILE__ ) . 'assets/diviskit-mark.svg';
		if ( ! is_readable( $svg_path ) ) {
			return 'dashicons-rest-api';
		}
		$svg = file_get_contents( $svg_path );
		if ( false === $svg ) {
			return 'dashicons-rest-api';
		}
		$svg = str_replace( 'fill="black"', 'fill="#f0f0f1"', $svg );
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	private static function admin_rollback_snapshot_badge_class( array $snapshot ): string {
		if ( ! empty( $snapshot['cleanup']['deleted_at'] ) ) {
			return 'diviskit-status--neutral';
		}
		if ( ! empty( $snapshot['restore']['restored_at'] ) ) {
			return 'diviskit-status--success';
		}
		if ( ! empty( $snapshot['expired'] ) ) {
			return 'diviskit-status--error';
		}
		if ( ! empty( $snapshot['interrupted'] ) ) {
			return 'diviskit-status--warning';
		}

		$status = sanitize_key( (string) ( $snapshot['status'] ?? '' ) );
		if ( 'write_applied' === $status ) {
			return 'diviskit-status--success';
		}
		if ( 'write_failed_restored' === $status ) {
			return 'diviskit-status--info';
		}
		if ( 'aborted_before_write' === $status ) {
			return 'diviskit-status--neutral';
		}
		return 'diviskit-status--warning';
	}

	private static function admin_rollback_snapshot_badge_label( array $snapshot ): string {
		if ( ! empty( $snapshot['cleanup']['deleted_at'] ) ) {
			return __( 'deleted', 'diviskit-agent' );
		}
		if ( ! empty( $snapshot['restore']['restored_at'] ) ) {
			return __( 'restored', 'diviskit-agent' );
		}
		if ( ! empty( $snapshot['expired'] ) ) {
			return __( 'expired', 'diviskit-agent' );
		}
		if ( ! empty( $snapshot['interrupted'] ) ) {
			return __( 'interrupted', 'diviskit-agent' );
		}
		return str_replace( '_', ' ', sanitize_key( (string) ( $snapshot['status'] ?? 'created' ) ) );
	}

	private static function admin_rollback_snapshot_format_datetime( $value ): string {
		if ( empty( $value ) ) {
			return '—';
		}
		$timestamp = strtotime( (string) $value );
		if ( false === $timestamp ) {
			return '—';
		}
		$date_format = (string) get_option( 'date_format', 'Y-m-d' );
		$time_format = (string) get_option( 'time_format', 'H:i:s' );
		$format      = trim( $date_format . ' ' . $time_format );
		if ( '' === $format ) {
			$format = 'Y-m-d H:i:s';
		}
		return wp_date( $format, $timestamp );
	}

	private static function render_admin_rollback_snapshots_card( array $snapshots ): void {
		?>
		<details class="diviskit-snapshots" aria-labelledby="diviskit-snapshots-title">
			<summary>
				<h2 id="diviskit-snapshots-title"><span><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span><?php esc_html_e( 'Rollback Backups', 'diviskit-agent' ); ?></span>
				<small><?php
					/* translators: %d: number of snapshots visible to the current user. */
					echo esc_html( sprintf( __( '%d visible / latest 8 maximum', 'diviskit-agent' ), count( $snapshots ) ) );
				?></small></h2>
			</summary>
			<p class="diviskit-muted"><?php esc_html_e( 'Temporary rollback snapshots created by backup-enabled Diviskit content writes. These are not full site backups and this dashboard is read-only.', 'diviskit-agent' ); ?></p>
			<?php if ( empty( $snapshots ) ) : ?>
				<p class="diviskit-empty"><?php esc_html_e( 'No rollback snapshots are currently visible for this user.', 'diviskit-agent' ); ?></p>
			<?php else : ?>
				<div class="diviskit-snapshot-columns" aria-hidden="true">
					<span><?php esc_html_e( 'Target / snapshot', 'diviskit-agent' ); ?></span>
					<span><?php esc_html_e( 'Created', 'diviskit-agent' ); ?></span>
					<span><?php esc_html_e( 'Status', 'diviskit-agent' ); ?></span>
				</div>
				<?php foreach ( $snapshots as $snapshot ) : ?>
					<?php
					$operation  = self::rollback_snapshot_as_array( $snapshot['operation'] ?? [] );
					$target     = self::rollback_snapshot_as_array( $snapshot['target'] ?? [] );
					$created_by = self::rollback_snapshot_as_array( $snapshot['created_by'] ?? [] );
					$target_label = sprintf(
						'%s #%d',
						sanitize_key( (string) ( $target['post_type'] ?? $target['kind'] ?? 'post' ) ),
						absint( $target['id'] ?? 0 )
					);
					$operation_label = (string) ( $operation['tool_operation'] ?? $snapshot['tool'] ?? '' );
					$state_bits = [];
					if ( false === ( $target['exists'] ?? true ) ) {
						$state_bits[] = __( 'target missing', 'diviskit-agent' );
					}
					if ( ! empty( $snapshot['restore']['restorable'] ) ) {
						$state_bits[] = __( 'restorable', 'diviskit-agent' );
					}
					if ( ! empty( $snapshot['restore']['restored_at'] ) ) {
						$state_bits[] = __( 'restored', 'diviskit-agent' );
					}
					if ( ! empty( $snapshot['cleanup']['deleted_at'] ) ) {
						$state_bits[] = __( 'deleted', 'diviskit-agent' );
					}
					$metadata = [
						[ __( 'Snapshot ID', 'diviskit-agent' ), (string) ( $snapshot['snapshot_id'] ?? '' ) ],
						[ __( 'Target', 'diviskit-agent' ), $target_label ],
						[ __( 'Operation', 'diviskit-agent' ), $operation_label ?: __( 'Not recorded', 'diviskit-agent' ) ],
						[ __( 'Tool', 'diviskit-agent' ), (string) ( $snapshot['tool'] ?? '' ) ],
						[ __( 'Status', 'diviskit-agent' ), self::admin_rollback_snapshot_badge_label( $snapshot ) . ' (' . (string) ( $snapshot['status'] ?? 'created' ) . ')' ],
						[ __( 'State', 'diviskit-agent' ), empty( $state_bits ) ? __( 'active', 'diviskit-agent' ) : implode( ', ', $state_bits ) ],
						[ __( 'Created', 'diviskit-agent' ), self::admin_rollback_snapshot_format_datetime( $snapshot['created_at'] ?? '' ) ],
						[ __( 'Expires', 'diviskit-agent' ), self::admin_rollback_snapshot_format_datetime( $snapshot['expires_at'] ?? '' ) ],
						[ __( 'Created by', 'diviskit-agent' ), (string) ( $created_by['login'] ?? '' ) . ( ! empty( $created_by['user_id'] ) ? ' / #' . absint( $created_by['user_id'] ) : '' ) ],
						[ __( 'Before checksum', 'diviskit-agent' ), (string) ( $snapshot['before']['checksum'] ?? '' ) ],
						[ __( 'After checksum', 'diviskit-agent' ), (string) ( $snapshot['after']['checksum'] ?? '' ) ],
					];
					?>
					<details class="diviskit-snapshot">
						<summary>
							<span class="diviskit-snapshot-title">
								<span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
								<span><strong><?php echo esc_html( $target_label ); ?></strong><code><?php echo esc_html( (string) ( $snapshot['snapshot_id'] ?? '' ) ); ?></code></span>
							</span>
							<span class="diviskit-snapshot-time"><?php echo esc_html( self::admin_rollback_snapshot_format_datetime( $snapshot['created_at'] ?? '' ) ); ?></span>
							<span class="diviskit-status <?php echo esc_attr( self::admin_rollback_snapshot_badge_class( $snapshot ) ); ?>"><?php echo esc_html( self::admin_rollback_snapshot_badge_label( $snapshot ) ); ?></span>
						</summary>
						<dl class="diviskit-snapshot-meta">
							<?php foreach ( $metadata as [ $label, $value ] ) : ?>
								<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( '' !== $value ? $value : __( 'Not recorded', 'diviskit-agent' ) ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					</details>
				<?php endforeach; ?>
			<?php endif; ?>
		</details>
		<?php
	}
	public static function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this dashboard.', 'diviskit-agent' ) );
		}
		$divi_active   = function_exists( 'et_get_option' );
		$design_system = isset( $_GET['view'] ) && is_string( $_GET['view'] ) && 'design-system' === $_GET['view'] && 'design-system' === sanitize_key( wp_unslash( $_GET['view'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fixed read-only navigation; exact-token guard rejects normalization variants. No state change.
		$divi_version  = $divi_active && defined( 'ET_BUILDER_PRODUCT_VERSION' ) ? ET_BUILDER_PRODUCT_VERSION : null;
		$rest_url      = rest_url( self::REST_NAMESPACE );
		$rate_disabled = (bool) DIVISKIT_RATE_LIMIT_DISABLED;
		$read_limit    = (int) DIVISKIT_RATE_LIMIT_READ;
		$write_limit   = (int) DIVISKIT_RATE_LIMIT_WRITE;

		$limits = apply_filters( 'diviskit_rate_limits', [
			'read'  => $read_limit,
			'write' => $write_limit,
		] );
		if ( is_array( $limits ) && isset( $limits['read'], $limits['write'] ) ) {
			$read_limit  = (int) $limits['read'];
			$write_limit = (int) $limits['write'];
		}

		// Design Library status.
		$ddl_active  = class_exists( 'Diviskit_Design_Library' );
		$ddl_version = $ddl_active && defined( 'Diviskit_Design_Library::VERSION' ) ? Diviskit_Design_Library::VERSION : null;

		// Pro status.
		$pro_active  = class_exists( 'Diviskit_Pro' );
		$pro_version = $pro_active && defined( 'Diviskit_Pro::VERSION' ) ? constant( 'Diviskit_Pro::VERSION' ) : null;
		$pro_url     = add_query_arg( [ 'page' => 'diviskit-pro' ], admin_url( 'admin.php' ) );

		// Handshake extension data (Pro modules, targets) — same filters the
		// REST handshake applies, so the dashboard shows what clients see.
		$hs_extensions = apply_filters( 'diviskit_agent_handshake_extensions', [] );
		$active_modules    = is_array( $hs_extensions ) && isset( $hs_extensions['active_modules'] ) && is_array( $hs_extensions['active_modules'] ) ? $hs_extensions['active_modules'] : [];
		$pro_capabilities  = is_array( $hs_extensions ) && isset( $hs_extensions['capabilities'] ) && is_array( $hs_extensions['capabilities'] ) ? array_keys( $hs_extensions['capabilities'] ) : [];

		// Route count for the canonical namespace from the live route table.
		$route_count = 0;
		foreach ( array_keys( rest_get_server()->get_routes() ) as $route_path ) {
			if ( 0 === strpos( $route_path, '/' . self::REST_NAMESPACE . '/' ) ) {
				$route_count++;
			}
		}

		// Capability groups (prefix before first underscore → count).
		$capability_groups = [];
		foreach ( array_merge( self::CAPABILITIES, $pro_capabilities ) as $cap_key ) {
			$group = strtok( (string) $cap_key, '_' );
			if ( ! isset( $capability_groups[ $group ] ) ) {
				$capability_groups[ $group ] = 0;
			}
			$capability_groups[ $group ]++;
		}
		ksort( $capability_groups );

		// Copy-ready MCP client config for this site — same template the
		// generated AGENTS.md embeds.
		$mcp_config = wp_json_encode( self::mcp_client_config(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		$brand_logo_url   = plugins_url( 'assets/diviskit-wordmark.svg', __FILE__ );
		$snapshot_request = new class() {
			public function get_param( $key ) {
				return 'limit' === $key ? 8 : null;
			}
		};
		$rollback_snapshots = $design_system ? [] : self::rollback_snapshot_filtered_summaries( $snapshot_request );

		?>
		<div class="wrap">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Diviskit Agent', 'diviskit-agent' ); ?></h1>
			<div class="diviskit-admin">
				<header class="diviskit-header">
					<div>
						<div class="diviskit-brand">
							<img src="<?php echo esc_url( $brand_logo_url ); ?>" alt="<?php esc_attr_e( 'Diviskit', 'diviskit-agent' ); ?>" width="166" height="42" />
							<span class="diviskit-edition"><?php echo esc_html( $pro_active ? __( 'Free + Pro', 'diviskit-agent' ) : __( 'Free', 'diviskit-agent' ) ); ?></span>
						</div>
						<p><?php esc_html_e( 'AI agent bridge for Divi 5', 'diviskit-agent' ); ?></p>
					</div>
					<a href="<?php echo esc_url( rest_url( self::REST_NAMESPACE . '/handshake' ) ); ?>" target="_blank" rel="noopener noreferrer" class="button"><span class="dashicons dashicons-rest-api" aria-hidden="true"></span><?php esc_html_e( 'REST endpoint', 'diviskit-agent' ); ?></a>
				</header>
				<nav class="diviskit-nav" aria-label="<?php esc_attr_e( 'Diviskit pages', 'diviskit-agent' ); ?>">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=diviskit' ) ); ?>"<?php if ( ! $design_system ) echo ' aria-current="page"'; ?>><?php esc_html_e( 'Overview', 'diviskit-agent' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=diviskit&view=design-system' ) ); ?>"<?php if ( $design_system ) echo ' aria-current="page"'; ?>><?php esc_html_e( 'Design System', 'diviskit-agent' ); ?></a>
					<?php if ( $pro_active ) : ?>
						<a href="<?php echo esc_url( $pro_url ); ?>"><?php esc_html_e( 'Diviskit Pro', 'diviskit-agent' ); ?></a>
					<?php endif; ?>
				</nav>
				<div class="diviskit-content">
					<?php if ( $design_system ) : ?>
						<?php require __DIR__ . '/includes/admin-design-system.php'; ?>
					<?php else : ?>
					<?php if ( ! $divi_active ) : ?>
						<p class="diviskit-callout"><?php esc_html_e( 'Divi is not active. Activate the Divi theme to use Divi-dependent MCP tools.', 'diviskit-agent' ); ?></p>
					<?php endif; ?>

					<section class="diviskit-card diviskit-card--hero" aria-labelledby="diviskit-connect-title">
						<div class="diviskit-section-heading">
							<h2 id="diviskit-connect-title"><?php esc_html_e( 'Connect your agent', 'diviskit-agent' ); ?></h2>
							<button type="button" id="diviskit-selftest-run" class="button"><span class="dashicons dashicons-heartbeat" aria-hidden="true"></span><?php esc_html_e( 'Run handshake self-test', 'diviskit-agent' ); ?></button>
						</div>
						<p class="diviskit-muted"><?php esc_html_e( 'Point your MCP client at the diviskit/v1 namespace below.', 'diviskit-agent' ); ?></p>
						<p id="diviskit-selftest-result" class="diviskit-selftest" data-running="<?php esc_attr_e( 'Running handshake…', 'diviskit-agent' ); ?>" hidden><?php esc_html_e( 'Self-test has not run yet.', 'diviskit-agent' ); ?></p>
						<div class="diviskit-namespaces">
							<div class="diviskit-namespace">
								<div>
									<code id="diviskit-ns-canonical"><?php echo esc_url( rest_url( self::REST_NAMESPACE ) ); ?></code>
									<span class="diviskit-muted">
										<?php
										/* translators: %d: number of registered REST routes. */
										echo esc_html( sprintf( __( 'canonical · %d routes', 'diviskit-agent' ), $route_count ) );
										?>
									</span>
								</div>
								<button type="button" class="button button-small" data-diviskit-copy="diviskit-ns-canonical" data-copied="<?php esc_attr_e( 'Copied', 'diviskit-agent' ); ?>"><?php esc_html_e( 'Copy URL', 'diviskit-agent' ); ?></button>
							</div>
						</div>
						<details class="diviskit-mcp-config">
							<summary><?php esc_html_e( 'MCP client configuration', 'diviskit-agent' ); ?></summary>
							<p class="diviskit-muted"><?php
								/* translators: %s: file name of the MCP client config. */
								echo esc_html( sprintf( __( 'Paste into your MCP client config (e.g. %s) and replace the placeholder with a WordPress Application Password.', 'diviskit-agent' ), '<code>.devin/mcp_config.local.json</code>' ) );
							?></p>
							<div class="diviskit-config-block">
								<pre id="diviskit-mcp-config"><?php echo esc_html( $mcp_config ); ?></pre>
								<button type="button" class="button button-small" data-diviskit-copy="diviskit-mcp-config" data-copied="<?php esc_attr_e( 'Copied', 'diviskit-agent' ); ?>"><?php esc_html_e( 'Copy config', 'diviskit-agent' ); ?></button>
							</div>
							<p class="diviskit-muted"><a href="<?php echo esc_url( admin_url( 'profile.php#application-passwords-section' ) ); ?>"><?php esc_html_e( 'Create an Application Password on your profile', 'diviskit-agent' ); ?></a></p>
						</details>
						<details class="diviskit-mcp-config">
							<summary><?php esc_html_e( 'AI editor setup prompt', 'diviskit-agent' ); ?></summary>
							<p class="diviskit-muted"><?php esc_html_e( 'Paste this prompt into your AI editor (Devin, Claude Code, Codex, …). The agent reads the AGENTS.md this plugin wrote to your site root, then runs the full MCP setup — application password, client config, handshake check and smoke test.', 'diviskit-agent' ); ?></p>
							<div class="diviskit-config-block">
								<pre id="diviskit-setup-prompt"><?php echo esc_html( self::mcp_setup_prompt() ); ?></pre>
								<button type="button" class="button button-small" data-diviskit-copy="diviskit-setup-prompt" data-copied="<?php esc_attr_e( 'Copied', 'diviskit-agent' ); ?>"><?php esc_html_e( 'Copy prompt', 'diviskit-agent' ); ?></button>
							</div>
							<p class="diviskit-muted"><?php
								/* translators: %s: absolute path of the generated AGENTS.md. */
								echo esc_html( sprintf( __( 'Setup instructions live in %s — regenerated automatically on plugin updates.', 'diviskit-agent' ), '<code>' . ABSPATH . 'AGENTS.md</code>' ) );
							?></p>
						</details>
					</section>

					<div class="diviskit-overview-grid">
						<section class="diviskit-card" aria-labelledby="diviskit-status-title">
							<h2 id="diviskit-status-title"><?php esc_html_e( 'Site status', 'diviskit-agent' ); ?></h2>
							<dl class="diviskit-facts">
								<div><dt><?php esc_html_e( 'Plugin version', 'diviskit-agent' ); ?></dt><dd><?php echo esc_html( self::VERSION ); ?> <span class="diviskit-muted"><?php esc_html_e( 'Free', 'diviskit-agent' ); ?></span></dd></div>
								<div><dt><?php esc_html_e( 'Divi theme', 'diviskit-agent' ); ?></dt><dd><span class="diviskit-status <?php echo $divi_active ? 'diviskit-status--success' : 'diviskit-status--warning'; ?>"><?php echo esc_html( $divi_active ? __( 'Active', 'diviskit-agent' ) : __( 'Not active', 'diviskit-agent' ) ); ?></span> <?php echo esc_html( $divi_version ?: '' ); ?></dd></div>
								<div><dt><?php esc_html_e( 'Rate limiting', 'diviskit-agent' ); ?></dt><dd><span class="diviskit-status <?php echo $rate_disabled ? 'diviskit-status--warning' : 'diviskit-status--success'; ?>"><?php echo esc_html( $rate_disabled ? __( 'Disabled', 'diviskit-agent' ) : __( 'Active', 'diviskit-agent' ) ); ?></span> <?php if ( ! $rate_disabled ) : ?><span class="diviskit-muted"><?php echo esc_html( $read_limit ); ?>/<?php echo esc_html( $write_limit ); ?> <?php esc_html_e( 'per min (read/write)', 'diviskit-agent' ); ?></span><?php endif; ?></dd></div>
								<div><dt><?php esc_html_e( 'WP-CLI', 'diviskit-agent' ); ?></dt><dd><?php echo esc_html( defined( 'DIVISKIT_WP_CLI_PATH' ) || getenv( 'WP_PATH' ) || getenv( 'WP_CLI_CMD' ) ? __( 'configured', 'diviskit-agent' ) : __( 'not configured', 'diviskit-agent' ) ); ?></dd></div>
							</dl>
							<p class="diviskit-muted"><?php
								/* translators: 1: read limit constant, 2: write limit constant, 3: filter name. */
								echo sprintf( esc_html__( 'Rate limits: %1$s / %2$s constants or the %3$s filter.', 'diviskit-agent' ), '<code>DIVISKIT_RATE_LIMIT_READ</code>', '<code>DIVISKIT_RATE_LIMIT_WRITE</code>', '<code>diviskit_rate_limits</code>' );
							?></p>
						</section>
						<section class="diviskit-card" aria-labelledby="diviskit-addons-title">
							<h2 id="diviskit-addons-title"><?php esc_html_e( 'Add-ons & modules', 'diviskit-agent' ); ?></h2>
							<div class="diviskit-addon">
								<div class="diviskit-section-heading"><h3><?php esc_html_e( 'Diviskit Pro', 'diviskit-agent' ); ?></h3><span class="diviskit-status <?php echo $pro_active ? 'diviskit-status--success' : 'diviskit-status--neutral'; ?>"><?php echo esc_html( $pro_active ? __( 'Active', 'diviskit-agent' ) : __( 'Not active', 'diviskit-agent' ) ); ?></span></div>
								<?php if ( $pro_version ) : ?>
									<p><?php /* translators: %s: installed plugin version. */ echo esc_html( sprintf( __( 'Version %s', 'diviskit-agent' ), $pro_version ) ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $active_modules ) ) : ?>
									<ul class="diviskit-module-list">
										<?php foreach ( $active_modules as $module_name => $module_on ) : ?>
											<li><span class="diviskit-status <?php echo $module_on ? 'diviskit-status--success' : 'diviskit-status--neutral'; ?>"><?php echo esc_html( $module_on ? __( 'on', 'diviskit-agent' ) : __( 'off', 'diviskit-agent' ) ); ?></span> <code><?php echo esc_html( $module_name ); ?></code></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<?php if ( $pro_active ) : ?>
									<a href="<?php echo esc_url( $pro_url ); ?>"><?php esc_html_e( 'Manage Diviskit Pro', 'diviskit-agent' ); ?></a>
								<?php endif; ?>
							</div>
							<div class="diviskit-addon">
								<div class="diviskit-section-heading"><h3><?php esc_html_e( 'Design Library', 'diviskit-agent' ); ?></h3><span class="diviskit-status <?php echo $ddl_active ? 'diviskit-status--success' : 'diviskit-status--neutral'; ?>"><?php echo esc_html( $ddl_active ? __( 'Active', 'diviskit-agent' ) : __( 'Not active', 'diviskit-agent' ) ); ?></span></div>
								<?php if ( $ddl_version ) : ?>
									<p><?php /* translators: %s: installed plugin version. */ echo esc_html( sprintf( __( 'Version %s', 'diviskit-agent' ), $ddl_version ) ); ?></p>
								<?php endif; ?>
								<p><?php echo esc_html( $ddl_active ? __( 'CSS animations, glass effects, Three.js WebGL shaders.', 'diviskit-agent' ) : __( 'Optional plugin for CSS entrance animations and Three.js WebGL shader backgrounds.', 'diviskit-agent' ) ); ?></p>
							</div>
						</section>
					</div>

					<section class="diviskit-card" aria-labelledby="diviskit-caps-title">
						<h2 id="diviskit-caps-title"><?php esc_html_e( 'Exposed capabilities', 'diviskit-agent' ); ?></h2>
						<p class="diviskit-muted"><?php
							/* translators: %d: total number of capability keys advertised in the handshake. */
							echo esc_html( sprintf( __( '%d capability keys advertised in the handshake, grouped by surface.', 'diviskit-agent' ), array_sum( $capability_groups ) ) );
						?></p>
						<ul class="diviskit-cap-groups">
							<?php foreach ( $capability_groups as $cap_group => $cap_count ) : ?>
								<li><code><?php echo esc_html( $cap_group ); ?>_*</code><span><?php echo esc_html( $cap_count ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</section>

					<?php self::render_schema_dump_card( $divi_version ); ?>
					<?php self::render_admin_rollback_snapshots_card( $rollback_snapshots ); ?>
					<section class="diviskit-updates" aria-labelledby="diviskit-updates-title">
						<h2 id="diviskit-updates-title"><?php esc_html_e( 'Updates & setup', 'diviskit-agent' ); ?></h2>
						<div>
							<p><?php esc_html_e( 'Diviskit Agent is a GPL fork of DiviOps Agent. Updates ship with the Diviskit plugin bundle — replace the plugin directory to update. Your Application Password and MCP client configuration carry over unchanged.', 'diviskit-agent' ); ?></p>
							<p><?php esc_html_e( 'The MCP server updates separately through npm or npx. Both namespaces serve the identical contract, so migrating a client is a one-line base-URL change.', 'diviskit-agent' ); ?></p>
						</div>
					</section>
					<?php endif; ?>
				</div>
				<footer class="diviskit-footer"><?php esc_html_e( 'Divi is a registered trademark of Elegant Themes, Inc. Diviskit Agent is not affiliated with or endorsed by Elegant Themes.', 'diviskit-agent' ); ?></footer>
			</div>
		</div>
		<?php
	}

	/**
	 * Schema dump card — documents the one-command skill reference refresh
	 * (update_skills.sh self-fetches the dump via the project's MCP config)
	 * and offers the raw /schema/module/dump-all JSON as a manual download.
	 */
	private static function render_schema_dump_card( ?string $divi_version ): void {
		$dump_url    = rest_url( self::REST_NAMESPACE . '/schema/module/dump-all' );
		$rest_nonce  = wp_create_nonce( 'wp_rest' );
		$fingerprint = self::schema_preset_attrs_map_hash();
		?>
		<section class="diviskit-card" aria-labelledby="diviskit-schema-title">
			<h2 id="diviskit-schema-title"><?php esc_html_e( 'Schema dump', 'diviskit-agent' ); ?></h2>
			<p class="diviskit-muted"><?php esc_html_e( 'The full Divi module schema feeds the local diviskit-builder skill references. After a Divi theme update the schema fingerprint changes — re-run the refresh command to regenerate them.', 'diviskit-agent' ); ?></p>
			<dl class="diviskit-facts">
				<div><dt><?php esc_html_e( 'Divi version', 'diviskit-agent' ); ?></dt><dd><code><?php echo esc_html( $divi_version ?? '—' ); ?></code></dd></div>
				<div><dt><?php esc_html_e( 'Schema fingerprint', 'diviskit-agent' ); ?></dt><dd><code><?php echo esc_html( '' !== $fingerprint ? substr( $fingerprint, 0, 12 ) : '—' ); ?></code></dd></div>
				<div><dt><?php esc_html_e( 'REST endpoint', 'diviskit-agent' ); ?></dt><dd><code><?php echo esc_url( $dump_url ); ?></code></dd></div>
			</dl>
			<h3><?php esc_html_e( 'Refresh skill references', 'diviskit-agent' ); ?></h3>
			<p><?php esc_html_e( 'Run this from anywhere inside the DDEV project. The script reads WP_URL and credentials from .devin/mcp_config.local.json, fetches the dump itself, and regenerates the module references:', 'diviskit-agent' ); ?></p>
			<div class="diviskit-config-block">
				<pre id="diviskit-schema-cmd">~/.config/devin/skills/diviskit-builder/references/modules/update_skills.sh</pre>
				<button type="button" class="button button-small" data-diviskit-copy="diviskit-schema-cmd" data-copied="<?php esc_attr_e( 'Copied', 'diviskit-agent' ); ?>"><?php esc_html_e( 'Copy command', 'diviskit-agent' ); ?></button>
			</div>
			<p class="diviskit-muted"><?php esc_html_e( 'Optional: pass the project directory as the first argument, or grab the raw JSON yourself:', 'diviskit-agent' ); ?></p>
			<p>
				<button type="button" class="button" id="diviskit-schema-download">
					<span class="dashicons dashicons-download" aria-hidden="true"></span>
					<?php esc_html_e( 'Download schema dump (JSON)', 'diviskit-agent' ); ?>
				</button>
				<span id="diviskit-schema-status" style="margin-left:8px;"></span>
			</p>
		</section>
		<script>
		(function() {
			var btn = document.getElementById('diviskit-schema-download');
			var status = document.getElementById('diviskit-schema-status');
			if (!btn) return;
			btn.addEventListener('click', function() {
				btn.disabled = true;
				status.textContent = 'Fetching...';
				fetch('<?php echo esc_js( $dump_url ); ?>', {
					headers: { 'X-WP-Nonce': '<?php echo esc_js( $rest_nonce ); ?>' }
				})
				.then(function(r) { return r.json(); })
				.then(function(data) {
					if (!data.ok) throw new Error('Response not ok');
					var blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
					var url = URL.createObjectURL(blob);
					var a = document.createElement('a');
					a.href = url;
					a.download = 'divi-schema-dump-' + (data.data && data.data.divi_version ? data.data.divi_version : 'unknown') + '.json';
					document.body.appendChild(a);
					a.click();
					document.body.removeChild(a);
					URL.revokeObjectURL(url);
					var sv = (data.data && data.data.schema_version) ? String(data.data.schema_version).substring(0,12) : '—';
					var mc = (data.data && data.data.modules) ? Object.keys(data.data.modules).length : 0;
					status.innerHTML = '<span class="diviskit-status diviskit-status--success">Done — schema: <code>' + sv + '</code>, ' + mc + ' modules</span>';
					btn.disabled = false;
				})
				.catch(function(err) {
					status.innerHTML = '<span class="diviskit-status diviskit-status--error">Error: ' + err.message + '</span>';
					btn.disabled = false;
				});
			});
		})();
		</script>
		<?php
	}
}

// Rate-limit constants — resolved once at bootstrap so these are the single
// source of truth at runtime. Placed after the class declaration so the
// class constants can serve as defaults. Precedence: DIVISKIT_* wp-config.php
// constant > DIVISKIT_* env var > class default. Empty / non-numeric env
// values fall through; an explicit numeric "0" is honored so operators can
// fully disable a bucket.
$diviskit_env = [
	'DISABLED' => getenv( 'DIVISKIT_RATE_LIMIT_DISABLED' ),
	'READ'     => getenv( 'DIVISKIT_RATE_LIMIT_READ' ),
	'WRITE'    => getenv( 'DIVISKIT_RATE_LIMIT_WRITE' ),
];
if ( ! defined( 'DIVISKIT_RATE_LIMIT_DISABLED' ) ) {
	$diviskit_disabled = filter_var( $diviskit_env['DISABLED'], FILTER_VALIDATE_BOOLEAN );
	define( 'DIVISKIT_RATE_LIMIT_DISABLED', $diviskit_disabled );
}
foreach ( [ 'READ' => 'RATE_LIMIT_READ', 'WRITE' => 'RATE_LIMIT_WRITE' ] as $diviskit_key => $diviskit_default ) {
	$diviskit_const = 'DIVISKIT_RATE_LIMIT_' . $diviskit_key;
	if ( defined( $diviskit_const ) ) {
		continue;
	}
	if ( is_numeric( $diviskit_env[ $diviskit_key ] ) ) {
		$diviskit_value = (int) $diviskit_env[ $diviskit_key ];
	} else {
		$diviskit_value = constant( 'Diviskit_Agent::' . $diviskit_default );
	}
	define( $diviskit_const, $diviskit_value );
}
unset( $diviskit_env, $diviskit_disabled, $diviskit_value, $diviskit_const, $diviskit_key, $diviskit_default );

register_activation_hook( __FILE__, [ 'Diviskit_Agent', 'activate' ] );

/**
 * Update client — the Agent is a free product, so it registers in
 * 'free' mode: anonymous update checks against the Diviskit store's
 * ?vendokit-license=* API, no license key, no license admin page.
 * Store URL: define DIVISKIT_AGENT_STORE_URL in wp-config.php to
 * override (e.g. the local dev store), or filter
 * diviskit_agent_store_url. 'item' is the vk_product slug on the
 * store — filterable via diviskit_agent_license_item.
 */
if ( file_exists( __DIR__ . '/includes/class-diviskit-license-client.php' ) ) {
	require_once __DIR__ . '/includes/class-diviskit-license-client.php';

	Diviskit_License_Client::register( array(
		'item'         => (string) apply_filters( 'diviskit_agent_license_item', 'diviskit-agent' ),
		'api_url'      => defined( 'DIVISKIT_AGENT_STORE_URL' )
			? DIVISKIT_AGENT_STORE_URL
			: apply_filters( 'diviskit_agent_store_url', 'https://diviskit.com' ),
		'version'      => Diviskit_Agent::VERSION,
		'file'         => __FILE__,
		'slug'         => 'diviskit-agent',
		'plugin_title' => 'Diviskit Agent',
		'purchase_url' => apply_filters( 'diviskit_agent_purchase_url', 'https://diviskit.com/item/diviskit-agent/' ),
		'free'         => true,
	) );
}

Diviskit_Agent::init();
