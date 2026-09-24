<?php
/**
 * Diviskit License Client — bundlable SDK for licensed products.
 *
 * Drop this file into a product plugin (e.g. includes/) and register:
 *
 *   require_once __DIR__ . '/includes/class-diviskit-license-client.php';
 *   Diviskit_License_Client::register( [
 *       'item_id'      => 169,                              // vk_product post ID on the store
 *       'api_url'      => 'https://shop.example.com/',      // site running vendokit licensing
 *       'version'      => SK_CONSENT_VERSION,
 *       'file'         => __FILE__-of-main-plugin-file,
 *       'slug'         => 'sk-consent',
 *       'plugin_title' => 'SK Consent',
 *       'purchase_url' => 'https://shop.example.com/item/sk-consent/', // optional
 *   ] );
 *
 * Free products (no license required): pass 'free' => true. The client
 * then skips the license page/handlers entirely and performs anonymous
 * update checks — the store must flag the product "free_download" so
 * get_license_version returns a signed package URL without a key.
 *
 * What the host product gets for free:
 *   - license state in wp_options (per product, namespaced by slug)
 *   - activate / deactivate / refresh against ?vendokit-license=*
 *   - WP update transient + plugins_api integration (signed package URLs)
 *   - a generic license page under Settings → "<Title> License"
 *   - admin notice when a license is saved but no longer valid
 *
 * Multiple products can each bundle this file: the class is guarded by
 * class_exists, and every register() call creates an isolated instance
 * keyed by the product slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Diviskit_License_Client' ) ) :

class Diviskit_License_Client {

	const SDK_VERSION       = '1.2.0';
	const QUERY_VAR         = 'vendokit-license';

	const STATUS_ACTIVE      = 'active';
	const STATUS_INACTIVE    = 'inactive';
	const STATUS_EXPIRED     = 'expired';
	const STATUS_DISABLED    = 'disabled';
	const STATUS_INVALID     = 'invalid';
	const STATUS_UNREACHABLE = 'unreachable';

	/** @var array<string,Diviskit_License_Client> */
	private static array $instances = [];

	/** @var array{item_id:int, api_url:string, version:string, file:string, slug:string, plugin_title:string, purchase_url:string, settings_key:string, basename:string} */
	private array $config;

	public static function register( array $config ): self {
		$config = wp_parse_args( $config, [
			'item_id'      => 0,
			'api_url'      => '',
			'version'      => '0.0.0',
			'file'         => '',
			'slug'         => '',
			'plugin_title' => 'Plugin',
			'purchase_url' => '',
			'settings_key' => '',
			'query_var'    => self::QUERY_VAR,
			'free'         => false,
		] );
		$config['item_id']      = absint( $config['item_id'] );
		$config['api_url']      = trailingslashit( esc_url_raw( $config['api_url'] ) );
		$config['slug']         = sanitize_key( (string) $config['slug'] );
		$config['basename']     = $config['file'] ? plugin_basename( $config['file'] ) : '';
		$config['settings_key'] = $config['settings_key'] ?: 'dklc_state_' . $config['slug'];

		$instance = new self( $config );
		$instance->hooks();
		self::$instances[ $config['slug'] ] = $instance;
		return $instance;
	}

	public static function instance( string $slug ): ?self {
		return self::$instances[ sanitize_key( $slug ) ] ?? null;
	}

	private function __construct( array $config ) {
		$this->config = $config;
	}

	private function hooks(): void {
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_plugin_update' ] );
		add_filter( 'plugins_api', [ $this, 'plugins_api_filter' ], 10, 3 );
		add_filter( 'pre_update_option_' . $this->config['settings_key'], [ $this, 'sanitize_state_for_storage' ], 10, 2 );

		// Free products update anonymously — no license UI or handlers.
		if ( is_admin() && empty( $this->config['free'] ) ) {
			add_action( 'admin_menu', [ $this, 'add_page' ] );
			add_action( 'admin_post_dklc_activate_' . $this->config['slug'], [ $this, 'handle_activate' ] );
			add_action( 'admin_post_dklc_deactivate_' . $this->config['slug'], [ $this, 'handle_deactivate' ] );
			add_action( 'admin_post_dklc_refresh_' . $this->config['slug'], [ $this, 'handle_refresh' ] );
			add_filter( 'plugin_action_links_' . $this->config['basename'], [ $this, 'plugin_action_links' ] );
		}
	}

	// ---------------------------------------------------------------
	// State
	// ---------------------------------------------------------------

	private function state(): array {
		$state = get_option( $this->config['settings_key'], [] );
		return wp_parse_args( is_array( $state ) ? $state : [], [
			'license_key'        => '',
			'redacted_key'       => '',
			'status'             => self::STATUS_INACTIVE,
			'expires'            => '',
			'activation_hash'    => '',
			'matched_item_label' => '',
			'last_checked'       => 0,
			'last_error_code'    => '',
			'last_error_message' => '',
		] );
	}

	private function save_state( array $state ): void {
		update_option( $this->config['settings_key'], $state, false );
	}

	public function sanitize_state_for_storage( $value, $old_value ) {
		if ( ! is_array( $value ) ) {
			return [];
		}
		$value['license_key'] = sanitize_text_field( (string) ( $value['license_key'] ?? '' ) );
		$value['status']      = sanitize_key( (string) ( $value['status'] ?? self::STATUS_INACTIVE ) );
		return $value;
	}

	public function status_payload(): array {
		$state = $this->state();
		$state['update_message'] = $this->is_active()
			? __( 'Updates enabled.', 'diviskit' )
			: __( 'Activate a license to enable updates.', 'diviskit' );
		return $state;
	}

	public function is_active(): bool {
		if ( ! empty( $this->config['free'] ) ) {
			return true;
		}
		$state = $this->state();
		return self::STATUS_ACTIVE === $state['status'] && '' !== $state['license_key'];
	}

	public static function redact_key( string $key ): string {
		$clean = preg_replace( '/\s+/', '', trim( $key ) );
		if ( strlen( $clean ) <= 8 ) {
			return str_repeat( '*', max( 0, strlen( $clean ) - 2 ) ) . substr( $clean, -2 );
		}
		return substr( $clean, 0, 4 ) . str_repeat( '*', 8 ) . substr( $clean, -4 );
	}

	// ---------------------------------------------------------------
	// API
	// ---------------------------------------------------------------

	public function api_request( string $action, array $payload = [] ) {
		$body = wp_parse_args( $payload, [
			'item_id'         => $this->config['item_id'],
			'current_version' => $this->config['version'],
			'site_url'        => home_url(),
		] );

		$query_var = sanitize_key( (string) ( $this->config['query_var'] ?? self::QUERY_VAR ) )
			?: self::QUERY_VAR;

		$url      = add_query_arg( [ $query_var => $action ], $this->config['api_url'] );
		$response = wp_remote_post( $url, [ 'timeout' => 15, 'body' => $body ] );
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'api_unreachable', $response->get_error_message() );
		}
		$code = (int) wp_remote_retrieve_response_code( $response );
		$data = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( 200 !== $code ) {
			$message = is_array( $data ) && ! empty( $data['message'] ) ? (string) $data['message'] : sprintf( 'License API request failed with HTTP %d.', $code );
			return new WP_Error( 'api_error', $message, [ 'status' => $code ] );
		}
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'malformed_response', 'The license server returned a malformed response.' );
		}
		return $data;
	}

	private function state_from_response( array $response, string $license_key, bool $preserve_key = false ): array {
		$status   = isset( $response['status'] ) ? strtolower( sanitize_key( (string) $response['status'] ) ) : '';
		$status   = $this->normalize_status( $status, $response );
		$is_valid = self::STATUS_ACTIVE === $status;
		if ( ! $is_valid && ! $preserve_key ) {
			$license_key = '';
		}
		return [
			'license_key'        => $license_key,
			'redacted_key'       => self::redact_key( $license_key ?: (string) ( $response['license_key'] ?? '' ) ),
			'status'             => $status,
			'expires'            => sanitize_text_field( (string) ( $response['expiration_date'] ?? $response['expires'] ?? '' ) ),
			'activation_hash'    => sanitize_text_field( (string) ( $response['activation_hash'] ?? '' ) ),
			'matched_item_label' => sanitize_text_field( (string) ( $response['variation_title'] ?? '' ) ),
			'last_checked'       => time(),
			'last_error_code'    => isset( $response['error_type'] ) ? sanitize_key( (string) $response['error_type'] ) : '',
			'last_error_message' => isset( $response['message'] ) ? sanitize_text_field( (string) $response['message'] ) : '',
		];
	}

	private function normalize_status( string $status, array $response = [] ): string {
		if ( array_key_exists( 'success', $response ) && ! (bool) $response['success'] && '' === $status ) {
			return self::STATUS_INVALID;
		}
		if ( in_array( $status, [ 'valid', 'active' ], true ) ) {
			return self::STATUS_ACTIVE;
		}
		if ( in_array( $status, [ 'expired', 'expire' ], true ) || ! empty( $response['is_expired'] ) ) {
			return self::STATUS_EXPIRED;
		}
		if ( in_array( $status, [ 'disabled', 'revoked', 'suspended' ], true ) ) {
			return self::STATUS_DISABLED;
		}
		if ( in_array( $status, [ 'unregistered', 'inactive' ], true ) ) {
			return self::STATUS_INACTIVE;
		}
		if ( in_array( $status, [ self::STATUS_EXPIRED, self::STATUS_DISABLED, self::STATUS_INVALID, self::STATUS_UNREACHABLE ], true ) ) {
			return $status;
		}
		return 'error' === $status ? self::STATUS_UNREACHABLE : self::STATUS_INVALID;
	}

	public function activate( string $license_key ) {
		$license_key = sanitize_text_field( trim( $license_key ) );
		if ( '' === $license_key ) {
			return new WP_Error( 'license_key_missing', 'Enter a license key to activate.' );
		}
		$response = $this->api_request( 'activate_license', [ 'license_key' => $license_key ] );
		if ( is_wp_error( $response ) ) {
			$this->save_state( array_merge( $this->state(), [
				'status'             => self::STATUS_UNREACHABLE,
				'last_checked'       => time(),
				'last_error_code'    => $response->get_error_code(),
				'last_error_message' => $response->get_error_message(),
			] ) );
			return $response;
		}
		$state = $this->state_from_response( $response, $license_key );
		$this->save_state( $state );
		if ( self::STATUS_ACTIVE !== $state['status'] ) {
			return new WP_Error(
				$state['last_error_code'] ?: 'activation_failed',
				$state['last_error_message'] ?: 'The license could not be activated.'
			);
		}
		$this->clear_update_cache();
		return true;
	}

	public function deactivate(): void {
		$state = $this->state();
		if ( '' !== $state['license_key'] ) {
			$this->api_request( 'deactivate_license', [ 'license_key' => $state['license_key'] ] );
		}
		$this->save_state( [
			'license_key'        => '',
			'redacted_key'       => $state['redacted_key'],
			'status'             => self::STATUS_INACTIVE,
			'expires'            => '',
			'activation_hash'    => '',
			'last_checked'       => time(),
			'last_error_message' => 'License deactivated on this site.',
		] );
		$this->clear_update_cache();
	}

	public function refresh(): void {
		$state = $this->state();
		if ( '' === $state['license_key'] ) {
			return;
		}
		$response = $this->api_request( 'check_license', [ 'license_key' => $state['license_key'] ] );
		if ( is_wp_error( $response ) ) {
			$state['status']             = self::STATUS_UNREACHABLE;
			$state['last_checked']       = time();
			$state['last_error_code']    = $response->get_error_code();
			$state['last_error_message'] = $response->get_error_message();
			$this->save_state( $state );
			return;
		}
		$this->save_state( $this->state_from_response( $response, $state['license_key'], true ) );
	}

	public function get_version_info() {
		$state = $this->state();
		if ( '' === $state['license_key'] ) {
			// Free products check anonymously; licensed products stay silent.
			return ! empty( $this->config['free'] )
				? $this->api_request( 'get_license_version' )
				: false;
		}
		return $this->api_request( 'get_license_version', [ 'license_key' => $state['license_key'] ] );
	}

	// ---------------------------------------------------------------
	// WP update integration
	// ---------------------------------------------------------------

	private function update_cache_key(): string {
		return 'dklc_update_' . $this->config['slug'];
	}

	public function clear_update_cache(): void {
		delete_transient( $this->update_cache_key() );
	}

	public function check_plugin_update( $transient ) {
		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}
		foreach ( [ 'response', 'no_update', 'checked' ] as $prop ) {
			if ( ! isset( $transient->$prop ) || ! is_array( $transient->$prop ) ) {
				$transient->$prop = [];
			}
		}

		$info = $this->version_info();
		if ( $info && ! empty( $info->new_version ) ) {
			unset( $info->sections );
			if ( version_compare( $this->config['version'], (string) $info->new_version, '<' ) ) {
				$transient->response[ $this->config['basename'] ] = $info;
			} else {
				$transient->no_update[ $this->config['basename'] ] = $info;
			}
		}
		$transient->checked[ $this->config['basename'] ] = $this->config['version'];
		return $transient;
	}

	public function plugins_api_filter( $data, $action = '', $args = null ) {
		if ( 'plugin_information' !== $action || ! $args || empty( $args->slug ) || $this->config['slug'] !== $args->slug ) {
			return $data;
		}
		$info = $this->version_info();
		return $info ?: new WP_Error( 'no_data', 'No update information is available.' );
	}

	private function version_info() {
		if ( ! $this->is_active() ) {
			return false;
		}
		$cache_key = $this->update_cache_key();
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return ! empty( $cached->new_version ) ? $cached : false;
		}

		$response = $this->get_version_info();
		if ( is_wp_error( $response ) || ! is_array( $response ) || empty( $response['new_version'] ) ) {
			set_transient( $cache_key, (object) [ 'new_version' => '' ], HOUR_IN_SECONDS );
			return false;
		}

		$info         = (object) $response;
		$info->plugin = $this->config['basename'];
		$info->slug   = $this->config['slug'];
		if ( ! empty( $info->sections ) ) {
			$info->sections = (array) $info->sections;
		}
		$info->banners = isset( $info->banners ) ? (array) $info->banners : [];
		$info->icons   = isset( $info->icons ) ? (array) $info->icons : [];

		set_transient( $cache_key, $info, 3 * HOUR_IN_SECONDS );
		return $info;
	}

	// ---------------------------------------------------------------
	// Admin
	// ---------------------------------------------------------------

	public function add_page(): void {
		add_options_page(
			sprintf( '%s License', $this->config['plugin_title'] ),
			sprintf( '%s License', $this->config['plugin_title'] ),
			'manage_options',
			'dklc-license-' . $this->config['slug'],
			[ $this, 'render_page' ]
		);
	}

	public function plugin_action_links( array $links ): array {
		$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=dklc-license-' . $this->config['slug'] ) ) . '">License</a>';
		return $links;
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'forbidden' );
		}
		$state  = $this->status_payload();
		$action = 'dklc_' . $this->config['slug'];
		$nonce  = 'dklc_nonce_' . $this->config['slug'];
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $this->config['plugin_title'] ); ?> — License</h1>
			<?php if ( isset( $_GET['dklc_notice'] ) ) : ?>
				<div class="notice notice-<?php echo 'success' === $_GET['dklc_notice'] ? 'success' : 'error'; ?> is-dismissible"><p>
					<?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['dklc_message'] ?? '' ) ) ); ?>
				</p></div>
			<?php endif; ?>
			<table class="widefat striped" style="max-width:720px;margin:16px 0"><tbody>
				<tr><th>Status</th><td><strong><?php echo esc_html( $state['status'] ); ?></strong></td></tr>
				<tr><th>License key</th><td><code><?php echo esc_html( $state['redacted_key'] ?: '—' ); ?></code></td></tr>
				<tr><th>Plan</th><td><?php echo esc_html( $state['matched_item_label'] ?: '—' ); ?></td></tr>
				<tr><th>Expires</th><td><?php echo esc_html( $state['expires'] ?: 'lifetime / —' ); ?></td></tr>
				<tr><th>Last checked</th><td><?php echo esc_html( $state['last_checked'] ? wp_date( 'Y-m-d H:i', (int) $state['last_checked'] ) : 'never' ); ?></td></tr>
				<?php if ( $state['last_error_message'] ) : ?>
				<tr><th>Last message</th><td><?php echo esc_html( $state['last_error_message'] ); ?></td></tr>
				<?php endif; ?>
			</tbody></table>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:720px">
				<?php wp_nonce_field( $nonce ); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>_activate">
				<input type="password" name="license_key" class="regular-text" autocomplete="off" placeholder="XXXX-XXXXX-XXXXX-XXXXX">
				<?php submit_button( 'Activate License', 'primary', 'submit', false ); ?>
			</form>
			<p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:8px">
				<?php wp_nonce_field( $nonce ); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>_refresh">
				<?php submit_button( 'Refresh Status', 'secondary', 'submit', false ); ?>
			</form>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block">
				<?php wp_nonce_field( $nonce ); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>_deactivate">
				<?php submit_button( 'Deactivate License', 'delete', 'submit', false ); ?>
			</form>
			</p>
			<?php if ( $this->config['purchase_url'] ) : ?>
				<p><a href="<?php echo esc_url( $this->config['purchase_url'] ); ?>" target="_blank" rel="noopener">Purchase / manage license</a></p>
			<?php endif; ?>
		</div>
		<?php
	}

	private function check_request(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'forbidden' );
		}
		check_admin_referer( 'dklc_nonce_' . $this->config['slug'] );
	}

	private function redirect( string $type, string $message ): void {
		wp_safe_redirect( add_query_arg(
			[ 'dklc_notice' => $type, 'dklc_message' => $message ],
			admin_url( 'options-general.php?page=dklc-license-' . $this->config['slug'] )
		) );
		exit;
	}

	public function handle_activate(): void {
		$this->check_request();
		$key    = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';
		$result = $this->activate( $key );
		if ( is_wp_error( $result ) ) {
			$this->redirect( 'error', $result->get_error_message() );
		}
		$this->redirect( 'success', 'License activated.' );
	}

	public function handle_deactivate(): void {
		$this->check_request();
		$this->deactivate();
		$this->redirect( 'success', 'License deactivated.' );
	}

	public function handle_refresh(): void {
		$this->check_request();
		$this->refresh();
		$this->redirect( 'success', 'License status refreshed.' );
	}
}

endif;
