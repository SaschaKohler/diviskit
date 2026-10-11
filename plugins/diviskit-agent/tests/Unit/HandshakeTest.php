<?php
/**
 * Handshake contract: version gate (426) and payload shape.
 */

class HandshakeTest extends DK_TestCase {

	private function handshake_request( ?string $version ): WP_REST_Request {
		$req = new WP_REST_Request( 'POST', '/diviskit/v1/handshake' );
		if ( null !== $version ) {
			$req->set_param( 'mcp_server_version', $version );
		}
		return $req;
	}

	public function test_old_server_version_gets_426() {
		$res = Diviskit_Agent::handshake( $this->handshake_request( '0.9.9' ) );
		$this->assertInstanceOf( WP_Error::class, $res );
		$this->assertSame( 'upgrade_required', $res->get_error_code() );
		$this->assertSame( 426, $res->get_error_data()['status'] );
	}

	public function test_valid_handshake_payload() {
		$this->login( 'administrator' );
		$res  = Diviskit_Agent::handshake( $this->handshake_request( Diviskit_Agent::MIN_SERVER_VERSION ) );
		$this->assertInstanceOf( WP_REST_Response::class, $res );
		$data = $res->get_data();

		$this->assertTrue( $data['compatible'] );
		$this->assertSame( 'diviskit', $data['server'] );
		$this->assertSame( Diviskit_Agent::VERSION, $data['plugin_version'] );
		$this->assertSame( Diviskit_Agent::MIN_SERVER_VERSION, $data['min_server'] );
		$this->assertContains( 'diviskit/v1', $data['namespaces'] );
		$this->assertSame( get_current_user_id(), $data['authenticated_user']['id'] );

		// Capability map: every declared key advertises true.
		$capabilities = (array) $data['capabilities'];
		foreach ( Diviskit_Agent::CAPABILITIES as $key ) {
			$this->assertArrayHasKey( $key, $capabilities, "missing capability $key" );
			$this->assertTrue( $capabilities[ $key ] );
		}
		// No stray keys beyond the declared map (plus pro extensions).
		foreach ( array_keys( $capabilities ) as $key ) {
			if ( ! in_array( $key, Diviskit_Agent::CAPABILITIES, true ) ) {
				$this->markTestIncomplete( "pro/extension capability advertised: $key" );
			}
		}
	}

	public function test_divi_flag_reflects_runtime() {
		$this->login( 'administrator' );
		$res  = Diviskit_Agent::handshake( $this->handshake_request( '9.9.9' ) );
		$data = $res->get_data();
		// Test env has no Divi — flag must be honest, not hardcoded.
		$this->assertFalse( $data['divi']['active'] );
		$this->assertNull( $data['divi']['version'] );
	}
}
