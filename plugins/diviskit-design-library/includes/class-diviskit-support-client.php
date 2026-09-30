<?php
/**
 * Diviskit Support Client — bundlable SDK for licensed products.
 *
 * Drop this file into a product plugin (next to the license client,
 * e.g. includes/) and register once:
 *
 *   require_once __DIR__ . '/includes/class-diviskit-support-client.php';
 *   Diviskit_Support_Client::register( [
 *       'item'              => 'sk-consent',                  // vk_product slug
 *       'item_id'           => 0,                             // optional, overrides slug
 *       'api_url'           => 'https://shop.example.com/',   // site running vendokit-support
 *       'version'           => SK_CONSENT_VERSION,            // sent as diagnostic
 *       'slug'              => 'sk-consent',                  // must match the license client slug
 *       'plugin_title'      => 'SK Consent',
 *       'license_state_key' => 'dklc_state_sk-consent',       // default: dklc_state_<slug>
 *       'support_url'       => '',                            // panel URL for redirects/notices
 *   ] );
 *
 * The client reuses the license client's stored state — no separate
 * credential handling: the license_key saved by
 * Diviskit_License_Client in `license_state_key` is the auth token for
 * every request. Licensed products send the key implicitly; free
 * products (no key stored) submit anonymously — the store accepts
 * anonymous tickets only for products flagged "free_download".
 *
 * What the host product gets:
 *   - render_support_panel(): new-ticket form + ticket list + thread
 *     view with replies, embeddable in any admin screen
 *   - automatic diagnostics with every ticket: plugin version, WP
 *     version, PHP version, site URL
 *   - ticket list cache (5 min) with manual refresh
 *
 * Multiple products can each bundle this file: the class is guarded by
 * class_exists, and every register() call creates an isolated instance
 * keyed by the product slug.
 *
 * Server side: vendokit-support ≥ 0.1.0 on the store
 * ( ?vendokit-support=<action> API ).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Diviskit_Support_Client' ) ) :

class Diviskit_Support_Client {

	const SDK_VERSION = '1.0.0';
	const QUERY_VAR   = 'vendokit-support';
	const LIST_TTL    = 300; // ticket list cache, seconds

	/** @var array<string,Diviskit_Support_Client> */
	private static array $instances = [];

	private array $config;

	public static function register( array $config ): self {
		$config = wp_parse_args( $config, [
			'item'              => '',
			'item_id'           => 0,
			'api_url'           => '',
			'version'           => '0.0.0',
			'slug'              => '',
			'plugin_title'      => 'Plugin',
			'license_state_key' => '',
			'query_var'         => self::QUERY_VAR,
			'support_url'       => '',
		] );
		$config['item']              = sanitize_title( (string) $config['item'] );
		$config['item_id']           = absint( $config['item_id'] );
		$config['api_url']           = trailingslashit( esc_url_raw( (string) $config['api_url'] ) );
		$config['slug']              = sanitize_key( (string) $config['slug'] );
		$config['license_state_key'] = $config['license_state_key'] ?: 'dklc_state_' . $config['slug'];

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
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'admin_post_dks_create_' . $this->config['slug'], [ $this, 'handle_create' ] );
		add_action( 'admin_post_dks_reply_' . $this->config['slug'], [ $this, 'handle_reply' ] );
		add_action( 'admin_notices', [ $this, 'notice' ] );
	}

	// ---------------------------------------------------------------
	// State — license key lives in the license client's shared option
	// ---------------------------------------------------------------

	private function license_key(): string {
		$state = get_option( $this->config['license_state_key'], [] );
		return is_array( $state ) ? (string) ( $state['license_key'] ?? '' ) : '';
	}

	private function back_url(): string {
		return $this->config['support_url'] ?: wp_get_referer() ?: admin_url();
	}

	private function back_with( string $msg, int $ticket_id = 0 ): void {
		$args = [ 'dks-msg' => $msg ];
		if ( $ticket_id ) {
			$args['dks-ticket'] = $ticket_id;
		}
		wp_safe_redirect( add_query_arg( $args, remove_query_arg( [ 'dks-msg', 'dks-refresh' ], $this->back_url() ) ) );
		exit;
	}

	// ---------------------------------------------------------------
	// API
	// ---------------------------------------------------------------

	public function api_request( string $action, array $payload = [] ) {
		$body = wp_parse_args( $payload, [
			'item'           => (string) $this->config['item'],
			'item_id'        => $this->config['item_id'],
			'site_url'       => home_url(),
			'plugin_version' => $this->config['version'],
			'wp_version'     => get_bloginfo( 'version' ),
			'php_version'    => PHP_VERSION,
		] );
		$key = $this->license_key();
		if ( '' !== $key && '' === (string) ( $body['license_key'] ?? '' ) ) {
			$body['license_key'] = $key;
		}

		$query_var = sanitize_key( (string) ( $this->config['query_var'] ?? self::QUERY_VAR ) ) ?: self::QUERY_VAR;
		$url       = $this->config['api_url'] . $query_var . '/' . $action;
		$response  = wp_remote_post( $url, [ 'timeout' => 15, 'body' => $body ] );
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'api_unreachable', $response->get_error_message() );
		}
		$code = (int) wp_remote_retrieve_response_code( $response );
		$data = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( 200 !== $code ) {
			$message = is_array( $data ) && ! empty( $data['message'] ) ? (string) $data['message'] : sprintf( 'Support API request failed with HTTP %d.', $code );
			return new WP_Error( 'api_error', $message, [ 'status' => $code ] );
		}
		if ( ! is_array( $data ) || empty( $data['success'] ) ) {
			$message = is_array( $data ) && ! empty( $data['message'] ) ? (string) $data['message'] : 'The support server returned an error.';
			return new WP_Error( is_array( $data ) && ! empty( $data['error_type'] ) ? (string) $data['error_type'] : 'api_error', $message );
		}
		return $data;
	}

	/** Ticket list with a short cache — panel views shouldn't hit the store twice a minute. */
	public function tickets( bool $fresh = false ) {
		$key = 'dks_tickets_' . $this->config['slug'];
		if ( $fresh ) {
			delete_transient( $key );
		}
		$cached = get_transient( $key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
		$data = $this->api_request( 'list_tickets' );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$tickets = (array) ( $data['tickets'] ?? [] );
		set_transient( $key, $tickets, self::LIST_TTL );
		return $tickets;
	}

	public function ticket( int $id ) {
		$data = $this->api_request( 'get_ticket', [ 'ticket' => $id ] );
		return is_wp_error( $data ) ? $data : (array) ( $data['ticket'] ?? [] );
	}

	// ---------------------------------------------------------------
	// Handlers (admin-post, per-slug nonce)
	// ---------------------------------------------------------------

	public function handle_create(): void {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'dks_' . $this->config['slug'], 'dks_nonce' ) ) {
			wp_die( 'Bad nonce.' );
		}
		$subject = sanitize_text_field( (string) ( $_POST['dks_subject'] ?? '' ) );
		$message = trim( (string) ( $_POST['dks_message'] ?? '' ) );
		$email   = sanitize_email( (string) ( $_POST['dks_email'] ?? '' ) );
		if ( '' === $subject || '' === $message ) {
			$this->back_with( 'invalid' );
		}

		$user = wp_get_current_user();
		$data = $this->api_request( 'create_ticket', [
			'subject' => $subject,
			'message' => $message,
			'name'    => $user->display_name,
			'email'   => $email ?: $user->user_email,
		] );
		if ( is_wp_error( $data ) ) {
			$this->back_with( 'error' );
		}
		delete_transient( 'dks_tickets_' . $this->config['slug'] );
		$this->back_with( 'created', (int) ( $data['ticket']['id'] ?? 0 ) );
	}

	public function handle_reply(): void {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'dks_' . $this->config['slug'], 'dks_nonce' ) ) {
			wp_die( 'Bad nonce.' );
		}
		$ticket_id = absint( $_POST['dks_ticket'] ?? 0 );
		$message   = trim( (string) ( $_POST['dks_message'] ?? '' ) );
		if ( ! $ticket_id || '' === $message ) {
			$this->back_with( 'invalid', $ticket_id );
		}
		$data = $this->api_request( 'reply_ticket', [ 'ticket' => $ticket_id, 'message' => $message ] );
		if ( is_wp_error( $data ) ) {
			$this->back_with( 'error', $ticket_id );
		}
		delete_transient( 'dks_tickets_' . $this->config['slug'] );
		$this->back_with( 'replied', $ticket_id );
	}

	public function notice(): void {
		$msg = sanitize_key( (string) ( $_GET['dks-msg'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification -- display only.
		$map = [
			'created' => [ 'success', sprintf( __( 'Ticket für %s erstellt — du erhältst eine Bestätigung per E-Mail.', 'diviskit' ), $this->config['plugin_title'] ) ],
			'replied' => [ 'success', __( 'Antwort gesendet.', 'diviskit' ) ],
			'invalid' => [ 'error', __( 'Bitte Betreff und Nachricht ausfüllen.', 'diviskit' ) ],
			'error'   => [ 'error', __( 'Der Support-Server konnte nicht erreicht werden — bitte später erneut versuchen.', 'diviskit' ) ],
		];
		if ( isset( $map[ $msg ] ) ) {
			printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $map[ $msg ][0] ), esc_html( $map[ $msg ][1] ) );
		}
	}

	// ---------------------------------------------------------------
	// Panel — embed via ->render_support_panel() in a host admin screen
	// ---------------------------------------------------------------

	public function render_support_panel(): void {
		$slug = $this->config['slug'];
		if ( ! empty( $_GET['dks-refresh'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- manual refresh.
			$this->tickets( true );
		}
		$ticket_id = absint( $_GET['dks-ticket'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification -- view only.
		echo '<div class="dks-panel dks-panel-' . esc_attr( $slug ) . '">';
		if ( $ticket_id ) {
			$this->render_thread( $ticket_id );
		} else {
			$this->render_form();
			$this->render_list();
		}
		echo '</div>';
	}

	private function render_form(): void {
		echo '<h3>' . esc_html__( 'Neues Ticket', 'diviskit' ) . '</h3>';
		if ( '' === $this->license_key() ) {
			echo '<p class="description">' . esc_html__( 'Ohne aktive Lizenz nur für Free-Produkte möglich — E-Mail ist dann Pflicht.', 'diviskit' ) . '</p>';
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'dks_' . $this->config['slug'], 'dks_nonce' );
		echo '<input type="hidden" name="action" value="dks_create_' . esc_attr( $this->config['slug'] ) . '">';
		if ( '' === $this->license_key() ) {
			echo '<p><label>' . esc_html__( 'E-Mail', 'diviskit' ) . '<br><input type="email" name="dks_email" class="regular-text" required></label></p>';
		}
		echo '<p><label>' . esc_html__( 'Betreff', 'diviskit' ) . '<br><input type="text" name="dks_subject" class="regular-text" maxlength="200" required></label></p>';
		echo '<p><label>' . esc_html__( 'Nachricht', 'diviskit' ) . '<br><textarea name="dks_message" rows="6" class="large-text" required></textarea></label></p>';
		echo '<p class="description">' . esc_html( sprintf(
			__( 'Automatisch angehängt: %s-Version %s, WordPress %s, PHP %s, Site-URL.', 'diviskit' ),
			$this->config['plugin_title'],
			$this->config['version'],
			get_bloginfo( 'version' ),
			PHP_VERSION
		) ) . '</p>';
		echo '<p><button type="submit" class="button button-primary">' . esc_html__( 'Ticket erstellen', 'diviskit' ) . '</button></p>';
		echo '</form>';
	}

	private function render_list(): void {
		$tickets = $this->tickets();
		echo '<h3>' . esc_html__( 'Deine Tickets', 'diviskit' ) . ' '
			. '<a href="' . esc_url( add_query_arg( 'dks-refresh', 1, $this->back_url() ) ) . '" class="button button-small">' . esc_html__( 'Aktualisieren', 'diviskit' ) . '</a></h3>';
		if ( is_wp_error( $tickets ) ) {
			echo '<p class="description">' . esc_html( $tickets->get_error_message() ) . '</p>';
			return;
		}
		if ( ! $tickets ) {
			echo '<p class="description">' . esc_html__( 'Noch keine Tickets.', 'diviskit' ) . '</p>';
			return;
		}
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Ticket', 'diviskit' ) . '</th><th>' . esc_html__( 'Betreff', 'diviskit' ) . '</th><th>' . esc_html__( 'Status', 'diviskit' ) . '</th><th>' . esc_html__( 'Aktualisiert', 'diviskit' ) . '</th></tr></thead><tbody>';
		foreach ( $tickets as $t ) {
			$url = add_query_arg( 'dks-ticket', (int) $t['id'], remove_query_arg( 'dks-refresh', $this->back_url() ) );
			echo '<tr><td><a href="' . esc_url( $url ) . '">' . esc_html( $t['ticket_number'] ) . '</a></td>'
				. '<td>' . esc_html( $t['subject'] ) . '</td>'
				. '<td>' . esc_html( $t['status'] ) . '</td>'
				. '<td>' . esc_html( $t['updated_at'] ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}

	private function render_thread( int $ticket_id ): void {
		$ticket = $this->ticket( $ticket_id );
		echo '<p><a href="' . esc_url( remove_query_arg( 'dks-ticket', $this->back_url() ) ) . '">← ' . esc_html__( 'Zur Übersicht', 'diviskit' ) . '</a></p>';
		if ( is_wp_error( $ticket ) ) {
			echo '<p class="description">' . esc_html( $ticket->get_error_message() ) . '</p>';
			return;
		}
		echo '<h3>' . esc_html( ( $ticket['ticket_number'] ?? '' ) . ' — ' . ( $ticket['subject'] ?? '' ) ) . ' <code>' . esc_html( $ticket['status'] ?? '' ) . '</code></h3>';
		foreach ( (array) ( $ticket['replies'] ?? [] ) as $reply ) {
			$admin = 'admin' === ( $reply['author_type'] ?? '' );
			echo '<div style="max-width:640px;margin:12px 0;padding:12px 16px;border:1px solid #dcdcde;border-radius:6px;' . ( $admin ? 'background:#f0f6fb;border-color:#c5d9ed;' : '' ) . '">';
			echo '<p style="margin:0 0 6px;color:#646970;font-size:12px">' . esc_html( ( $admin ? 'Support' : ( $reply['author_name'] ?: 'Du' ) ) . ' · ' . ( $reply['created_at'] ?? '' ) ) . '</p>';
			echo wp_kses_post( wpautop( (string) ( $reply['message'] ?? '' ) ) );
			echo '</div>';
		}
		if ( 'closed' === ( $ticket['status'] ?? '' ) ) {
			echo '<p class="description">' . esc_html__( 'Dieses Ticket ist geschlossen.', 'diviskit' ) . '</p>';
			return;
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'dks_' . $this->config['slug'], 'dks_nonce' );
		echo '<input type="hidden" name="action" value="dks_reply_' . esc_attr( $this->config['slug'] ) . '">';
		echo '<input type="hidden" name="dks_ticket" value="' . esc_attr( (string) $ticket_id ) . '">';
		echo '<p><textarea name="dks_message" rows="5" class="large-text" required></textarea></p>';
		echo '<p><button type="submit" class="button button-primary">' . esc_html__( 'Antwort senden', 'diviskit' ) . '</button></p>';
		echo '</form>';
	}
}

endif;
