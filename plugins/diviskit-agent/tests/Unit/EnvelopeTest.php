<?php
/**
 * Envelope shape: { ok, data } / { ok:false, error:{ code, message, hint } }.
 * Exercised through the public skills surface — no reflection needed.
 */

class EnvelopeTest extends DK_TestCase {

	public function test_skills_list_success_envelope() {
		$this->login( 'administrator' );
		$res  = Diviskit_Agent::skills_list( $this->req( [], 'GET' ) );
		$this->assertInstanceOf( WP_REST_Response::class, $res );
		$body = $res->get_data();

		$this->assertTrue( $body['ok'] );
		$this->assertSame( Diviskit_Agent::VERSION, $body['data']['plugin_version'] );
		$this->assertIsArray( $body['data']['skills'] );
		$this->assertNotEmpty( $body['data']['skills'] );
		foreach ( $body['data']['skills'] as $skill ) {
			$this->assertMatchesRegularExpression( '/^[a-z0-9-]+$/', $skill['name'] );
			$this->assertSame( $skill['file_count'], count( $skill['files'] ) );
			foreach ( $skill['files'] as $f ) {
				$this->assertMatchesRegularExpression( '/^[0-9a-f]{64}$/', $f['sha256'] );
				$this->assertStringNotContainsString( '..', $f['path'] );
			}
		}
	}

	public function test_skills_get_unknown_name_error_envelope() {
		$this->login( 'administrator' );
		$req = $this->req( [], 'GET' );
		$req->set_param( 'name', 'no-such-skill' );
		$res = Diviskit_Agent::skills_get( $req );

		$this->assertSame( 404, $res->get_status() );
		$body = $res->get_data();
		$this->assertFalse( $body['ok'] );
		$this->assertSame( 'not_found', $body['error']['code'] );
		$this->assertNotEmpty( $body['error']['message'] );
		$this->assertNotEmpty( $body['error']['hint'] );
	}

	public function test_skills_get_bundle_file_contract() {
		$this->login( 'administrator' );
		$req = $this->req( [], 'GET' );
		$req->set_param( 'name', 'diviskit' );
		$res = Diviskit_Agent::skills_get( $req );
		$this->assertTrue( $res->get_data()['ok'] );

		foreach ( $res->get_data()['data']['files'] as $f ) {
			$this->assertSame( 'base64', $f['encoding'] );
			$bytes = base64_decode( $f['content'], true );
			$this->assertNotFalse( $bytes );
			$this->assertSame( $f['sha256'], hash( 'sha256', $bytes ) );
			$this->assertSame( $f['size'], strlen( $bytes ) );
		}
	}
}
