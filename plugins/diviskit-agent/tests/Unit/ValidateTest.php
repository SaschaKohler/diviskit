<?php
/**
 * validate_blocks: Divi 4 `[et_pb_*]` shortcode markup must surface as a
 * dedicated `legacy_shortcode` error — corpus-recall output, not a D5 page.
 * Findings ride in the success branch: `data.errors[]`, never an error
 * envelope (see validate_blocks docblock).
 */

class ValidateTest extends DK_TestCase {

	private function validate( string $content ): array {
		$this->login( 'administrator' );
		$req = $this->req( [ 'content' => $content ], 'POST' );
		$res = Diviskit_Agent::validate_blocks( $req );
		$this->assertInstanceOf( WP_REST_Response::class, $res );
		$body = $res->get_data();
		$this->assertTrue( $body['ok'], 'expected success envelope with findings in data.errors[]' );
		return $body['data'];
	}

	private function error_codes( array $data ): array {
		return array_column( $data['errors'], 'code' );
	}

	public function test_pure_et_pb_markup_flags_legacy_shortcode() {
		$data  = $this->validate(
			'[et_pb_section][et_pb_row][et_pb_column type="4_4"][et_pb_text]Hi[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]'
		);
		$this->assertFalse( $data['valid'] );
		$this->assertContains( 'legacy_shortcode', $this->error_codes( $data ) );
	}

	public function test_et_pb_embedded_inside_divi_block_flags_legacy_shortcode() {
		$data = $this->validate(
			'<!-- divi/section {"builderVersion":"5.0.0"} -->[et_pb_text]x[/et_pb_text]<!-- /divi/section -->'
		);
		$this->assertContains( 'legacy_shortcode', $this->error_codes( $data ) );
	}

	public function test_divi_markup_without_shortcodes_has_no_legacy_flag() {
		$data = $this->validate( '<!-- divi/section {"builderVersion":"5.0.0"} /-->' );
		$this->assertNotContains( 'legacy_shortcode', $this->error_codes( $data ) );
	}
}
