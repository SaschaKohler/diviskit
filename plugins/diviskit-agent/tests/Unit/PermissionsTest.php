<?php
/**
 * Permission-callback unit tests — the TESTING.md §1 role matrix,
 * exercised at the guard level (no HTTP layer needed).
 */

class PermissionsTest extends DK_TestCase {

	// ------------------------------------------------------------------
	// Role matrix on the coarse guards
	// ------------------------------------------------------------------

	public function role_matrix(): array {
		// role            read   write  admin  menu   auth
		return [
			'administrator' => [ 'administrator', true,  true,  true,  true,  true ],
			'editor'        => [ 'editor',        true,  true,  false, false, true ],
			'author'        => [ 'author',        true,  false, false, false, true ],
			'contributor'   => [ 'contributor',   true,  false, false, false, true ],
			'subscriber'    => [ 'subscriber',    false, false, false, false, true ],
			'anonymous'     => [ 'anonymous',     false, false, false, false, false ],
		];
	}

	/** @dataProvider role_matrix */
	public function test_coarse_guard_matrix( string $role, bool $read, bool $write, bool $admin, bool $menu, bool $auth ) {
		if ( 'anonymous' === $role ) {
			$this->logout();
		} else {
			$this->login( $role );
		}

		$this->assertSame( $read,  Diviskit_Agent::check_read_permission(),  "read|$role" );
		$this->assertSame( $write, Diviskit_Agent::check_write_permission(), "write|$role" );
		$this->assertSame( $admin, Diviskit_Agent::check_admin_permission(), "admin|$role" );
		$this->assertSame( $menu,  Diviskit_Agent::check_menu_permission(),  "menu|$role" );
		$this->assertSame( $auth,  Diviskit_Agent::check_authenticated_permission(), "auth|$role" );
	}

	// ------------------------------------------------------------------
	// POST /page/create — status-aware capability gate
	// ------------------------------------------------------------------

	public function test_page_create_rejects_unknown_status_before_capability_check() {
		$this->login( 'administrator' );
		$err = Diviskit_Agent::check_page_create_permission( $this->req( [ 'status' => 'bogus' ] ) );
		$this->assertForbidden( $err, 'rest_invalid_param' );
		$this->assertSame( 400, $err->get_error_data()['status'] );
	}

	public function test_page_create_editor_may_draft_and_publish() {
		$this->login( 'editor' );
		$this->assertAllowed( Diviskit_Agent::check_page_create_permission( $this->req( [ 'status' => 'draft' ] ) ) );
		$this->assertAllowed( Diviskit_Agent::check_page_create_permission( $this->req( [ 'status' => 'publish' ] ) ) );
	}

	public function test_page_create_author_cannot_create_pages_at_all() {
		$this->login( 'author' );
		// 'page' maps create_posts → edit_pages, which authors lack.
		$err = Diviskit_Agent::check_page_create_permission( $this->req( [ 'status' => 'draft' ] ) );
		$this->assertForbidden( $err, 'rest_cannot_create' );
		$this->assertSame( 'edit_pages', $err->get_error_data()['required_capability'] );
	}

	public function test_page_create_defaults_to_draft_when_status_absent() {
		$this->login( 'editor' );
		$this->assertAllowed( Diviskit_Agent::check_page_create_permission( $this->req() ) );
	}

	// ------------------------------------------------------------------
	// POST /page/update-status — per-object + publish gate
	// ------------------------------------------------------------------

	public function test_update_status_rejects_bogus_status() {
		$this->login( 'administrator' );
		$err = Diviskit_Agent::check_page_update_status_permission(
			$this->req( [ 'status' => 'bogus' ] )
		);
		$this->assertForbidden( $err, 'rest_invalid_param' );
	}

	public function test_update_status_requires_existing_post_or_page() {
		$this->login( 'administrator' );
		$req = $this->req( [ 'status' => 'draft' ] );
		$req->set_param( 'id', 999999 );
		$this->assertForbidden( Diviskit_Agent::check_page_update_status_permission( $req ), 'rest_cannot_edit' );
	}

	public function test_update_status_author_cannot_touch_admin_page() {
		$admin_page = self::factory()->post->create( [
			'post_type'   => 'page',
			'post_author' => self::factory()->user->create( [ 'role' => 'administrator' ] ),
		] );
		$this->login( 'author' );
		$req = $this->req( [ 'status' => 'publish' ] );
		$req->set_param( 'id', $admin_page );
		$this->assertForbidden( Diviskit_Agent::check_page_update_status_permission( $req ), 'rest_cannot_edit' );
	}

	public function test_update_status_author_publishes_own_post() {
		// Authors can publish their own posts (publish_posts on 'post').
		$author    = self::factory()->user->create( [ 'role' => 'author' ] );
		$own_post  = self::factory()->post->create( [ 'post_type' => 'post', 'post_author' => $author ] );
		wp_set_current_user( $author );
		$req = $this->req( [ 'status' => 'publish' ] );
		$req->set_param( 'id', $own_post );
		$this->assertAllowed( Diviskit_Agent::check_page_update_status_permission( $req ) );
	}

	public function test_update_status_contributor_cannot_publish_own_post() {
		$contributor = self::factory()->user->create( [ 'role' => 'contributor' ] );
		// Draft — a published post would already fail edit_post
		// (contributors lack edit_published_posts).
		$own_post    = self::factory()->post->create( [
			'post_type'   => 'post',
			'post_author' => $contributor,
			'post_status' => 'draft',
		] );
		wp_set_current_user( $contributor );
		$req = $this->req( [ 'status' => 'publish' ] );
		$req->set_param( 'id', $own_post );
		$err = Diviskit_Agent::check_page_update_status_permission( $req );
		$this->assertForbidden( $err, 'rest_cannot_publish' );
		$this->assertSame( 'publish_posts', $err->get_error_data()['required_capability'] );
	}

	// ------------------------------------------------------------------
	// Fixed-publish CPT routes — Divi types absent/present
	// ------------------------------------------------------------------

	public function test_canvas_create_without_divi_post_type_refuses() {
		$this->login( 'administrator' );
		$this->assertFalse( (bool) get_post_type_object( 'et_pb_canvas' ) );
		$err = Diviskit_Agent::check_canvas_create_permission();
		$this->assertForbidden( $err, 'rest_cannot_create' );
		$this->assertSame( 'et_pb_canvas', $err->get_error_data()['post_type'] );
	}

	public function test_canvas_create_role_matrix_with_divi_post_type() {
		$this->stub_divi_post_type( 'et_pb_canvas' );

		$this->login( 'editor' );
		$this->assertAllowed( Diviskit_Agent::check_canvas_create_permission() );

		// Author/subscriber fail the base edit_pages gate → rest_forbidden.
		$this->login( 'author' );
		$this->assertForbidden( Diviskit_Agent::check_canvas_create_permission(), 'rest_forbidden' );

		$this->login( 'subscriber' );
		$this->assertForbidden( Diviskit_Agent::check_canvas_create_permission(), 'rest_forbidden' );
	}

	public function test_canvas_create_per_type_caps() {
		// Custom caps split create/publish from the base gate so the
		// post-type-level refusals are reachable.
		register_post_type( 'et_pb_canvas', [
			'public'          => false,
			'capability_type' => [ 'canvas', 'canvases' ],
			'map_meta_cap'    => true,
		] );

		// edit_pages without edit_canvases → rest_cannot_create.
		add_role( 'dk_canvas_create', 'Canvas Create', [
			'read' => true, 'edit_pages' => true,
		] );
		$this->login( 'dk_canvas_create' );
		$err = Diviskit_Agent::check_canvas_create_permission();
		$this->assertForbidden( $err, 'rest_cannot_create' );
		$this->assertSame( 'edit_canvases', $err->get_error_data()['required_capability'] );

		// edit_pages + edit_canvases without publish_canvases → rest_cannot_publish.
		add_role( 'dk_canvas_pub', 'Canvas Publish', [
			'read' => true, 'edit_pages' => true, 'edit_canvases' => true,
		] );
		$this->login( 'dk_canvas_pub' );
		$err = Diviskit_Agent::check_canvas_create_permission();
		$this->assertForbidden( $err, 'rest_cannot_publish' );
		$this->assertSame( 'publish_canvases', $err->get_error_data()['required_capability'] );
	}

	public function test_library_save_requires_manage_options_then_post_type() {
		$this->login( 'editor' );
		$this->assertForbidden( Diviskit_Agent::check_library_save_permission(), 'rest_forbidden' );

		$this->login( 'administrator' );
		$this->assertForbidden( Diviskit_Agent::check_library_save_permission(), 'rest_cannot_create' );

		$this->stub_divi_post_type( 'et_pb_layout' );
		$this->assertAllowed( Diviskit_Agent::check_library_save_permission() );
	}

	public function test_tb_template_create_scans_request_layout_params() {
		$this->login( 'administrator' );
		$this->stub_divi_post_type( 'et_theme_builder' );
		$this->stub_divi_post_type( 'et_template' );

		// header_content pulls et_header_layout into the required set —
		// unregistered here → rest_cannot_create on that type.
		$err = Diviskit_Agent::check_tb_template_create_permission(
			$this->req( [ 'header_content' => '<!-- wp:divi/section /-->' ] )
		);
		$this->assertForbidden( $err, 'rest_cannot_create' );
		$this->assertSame( 'et_header_layout', $err->get_error_data()['post_type'] );

		$this->stub_divi_post_type( 'et_header_layout' );
		$this->assertAllowed( Diviskit_Agent::check_tb_template_create_permission(
			$this->req( [ 'header_content' => '<!-- wp:divi/section /-->' ] )
		) );
	}

	public function test_tb_template_create_editor_refused() {
		$this->login( 'editor' );
		$this->assertForbidden(
			Diviskit_Agent::check_tb_template_create_permission( $this->req() ),
			'rest_forbidden'
		);
	}
}
