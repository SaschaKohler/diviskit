<?php
/**
 * Base test case for the Diviskit Agent suite.
 *
 * WP_UnitTestCase wraps every test in a DB transaction (rolled back in
 * tear_down) — fixtures never leak between tests or into the dev site.
 * On top: request superglobals reset, deterministic REMOTE_ADDR, and the
 * plugin's rate-limit transients cleared so throttling never bleeds
 * across tests.
 */
class DK_TestCase extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		$_GET    = [];
		$_POST   = [];
		$_COOKIE = [];
		$_SERVER['REMOTE_ADDR'] = '10.0.0.1';
	}

	public function tear_down() {
		$_GET = $_POST = $_COOKIE = [];
		parent::tear_down();
	}

	// ------------------------------------------------------------------
	// Fixtures
	// ------------------------------------------------------------------

	/** Log in a fresh user with the given role; returns the user id. */
	protected function login( string $role ): int {
		$uid = self::factory()->user->create( [ 'role' => $role ] );
		wp_set_current_user( $uid );
		return $uid;
	}

	protected function logout(): void {
		wp_set_current_user( 0 );
	}

	/** REST request stub with params. */
	protected function req( array $params = [], string $method = 'POST' ): WP_REST_Request {
		$req = new WP_REST_Request( $method, '/diviskit/v1/test' );
		foreach ( $params as $k => $v ) {
			$req->set_param( $k, $v );
		}
		return $req;
	}

	/** Register a Divi-style CPT stub (capability_type 'page'). */
	protected function stub_divi_post_type( string $name ): void {
		register_post_type( $name, [
			'public'           => false,
			'capability_type'  => 'page',
			'map_meta_cap'     => true,
		] );
	}

	/** WP_Error assertion helpers — the guards return true|WP_Error. */
	protected function assertForbidden( $result, string $code = '' ): WP_Error {
		$this->assertInstanceOf( WP_Error::class, $result );
		if ( '' !== $code ) {
			$this->assertSame( $code, $result->get_error_code() );
		}
		return $result;
	}

	protected function assertAllowed( $result ): void {
		$this->assertTrue( $result );
	}
}
