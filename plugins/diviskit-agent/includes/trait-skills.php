<?php
/**
 * Trait Diviskit_Agent_Skills
 *
 * Serves the bundled authoring skills (SKILL.md trees) that ship inside
 * the plugin as the canonical source. Add-on plugins (e.g. diviskit-pro)
 * contribute their own skills/ directory via the `diviskit_skills_dirs`
 * filter, so the manifest always matches the installed plugin surface.
 *
 * Skills are client-side artifacts: clients sync them into their own
 * skills directory (see bin/install-skills.sh in the distribution).
 * Serving them over REST keeps the installed copy version-locked to
 * the plugin — no stale suite copies.
 *
 * Part of the diviskit-agent monolith split. Mixed into
 * Diviskit_Agent via `use` in diviskit-agent.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Diviskit_Agent_Skills {

	/**
	 * Bundle directories that contribute skills, keyed by bundle slug.
	 *
	 * @return array<string,string>
	 */
	private static function skills_bundle_dirs(): array {
		$dirs = [
			'diviskit-agent' => dirname( __DIR__ ) . '/skills',
		];

		/**
		 * Filter the skill bundle directories.
		 *
		 * Add-ons register their own skills/ directory here:
		 *   add_filter( 'diviskit_skills_dirs', function( $dirs ) {
		 *       $dirs['my-bundle'] = plugin_dir_path( __FILE__ ) . 'skills';
		 *       return $dirs;
		 *   } );
		 *
		 * @param array<string,string> $dirs bundle slug => absolute dir path
		 */
		$dirs = apply_filters( 'diviskit_skills_dirs', $dirs );

		return is_array( $dirs ) ? $dirs : [];
	}

	/**
	 * Scan all bundle dirs for skills: <bundle_dir>/<name>/SKILL.md.
	 * First bundle wins on a name collision — the agent ships the shared
	 * primer and is registered first.
	 *
	 * @return array<string,array{bundle:string,dir:string}>
	 */
	private static function skills_index(): array {
		$index = [];
		foreach ( self::skills_bundle_dirs() as $bundle => $dir ) {
			if ( ! is_string( $dir ) || ! is_dir( $dir ) ) {
				continue;
			}
			foreach ( glob( trailingslashit( $dir ) . '*/SKILL.md' ) ?: [] as $md ) {
				$name = basename( dirname( $md ) );
				if ( preg_match( '/^[a-z0-9-]+$/', $name ) && ! isset( $index[ $name ] ) ) {
					$index[ $name ] = [ 'bundle' => $bundle, 'dir' => dirname( $md ) ];
				}
			}
		}
		ksort( $index );
		return $index;
	}

	/**
	 * Enumerate the files of one skill dir with size, sha256 and mode.
	 * Paths are relative to the skill dir — never user-supplied, so no
	 * traversal check is needed downstream.
	 *
	 * @return array<int,array{path:string,size:int,sha256:string,mode:int}>
	 */
	private static function skills_files( string $dir ): array {
		$files = [];
		$it    = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS )
		);
		foreach ( $it as $file ) {
			if ( ! $file->isFile() ) {
				continue;
			}
			$abs     = $file->getPathname();
			$files[] = [
				'path'   => ltrim( substr( $abs, strlen( $dir ) ), '/' ),
				'size'   => $file->getSize(),
				'sha256' => hash_file( 'sha256', $abs ),
				'mode'   => fileperms( $abs ) & 0777,
			];
		}
		usort(
			$files,
			function ( $a, $b ) {
				return strcmp( $a['path'], $b['path'] );
			}
		);
		return $files;
	}

	/**
	 * GET /skills — manifest of every bundled skill with per-file
	 * checksums. Clients diff this against their local install.
	 */
	public static function skills_list( $request ) {
		$skills = [];
		foreach ( self::skills_index() as $name => $meta ) {
			$files    = self::skills_files( $meta['dir'] );
			$skills[] = [
				'name'       => $name,
				'bundle'     => $meta['bundle'],
				'file_count' => count( $files ),
				'files'      => $files,
			];
		}

		return self::envelope_success(
			[
				'plugin_version' => self::VERSION,
				'skills'         => $skills,
			]
		);
	}

	/**
	 * GET /skills/<name> — full skill bundle, file contents base64-encoded.
	 */
	public static function skills_get( $request ) {
		$name  = $request['name'];
		$index = self::skills_index();

		if ( ! isset( $index[ $name ] ) ) {
			return self::envelope_error(
				'not_found',
				sprintf( 'No skill named "%s" is bundled on this site.', $name ),
				'List available skills via GET /wp-json/diviskit/v1/skills.',
				404
			);
		}

		$meta  = $index[ $name ];
		$files = [];
		foreach ( self::skills_files( $meta['dir'] ) as $f ) {
			$content = file_get_contents( $meta['dir'] . '/' . $f['path'] );
			if ( false === $content ) {
				continue;
			}
			$files[] = $f + [
				'encoding' => 'base64',
				'content'  => base64_encode( $content ),
			];
		}

		return self::envelope_success(
			[
				'name'    => $name,
				'bundle'  => $meta['bundle'],
				'files'   => $files,
			]
		);
	}
}
