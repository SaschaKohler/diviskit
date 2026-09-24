<?php
/** Read-only dashboard shell. Registry reads are deferred to the inspector asset. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section id="diviskit-design-system" class="diviskit-ds" aria-labelledby="diviskit-ds-title">
	<div class="diviskit-intro">
		<h2 id="diviskit-ds-title"><?php esc_html_e( 'Design System', 'diviskit-agent' ); ?></h2>
		<p><?php esc_html_e( 'Saved presets and variables on this site. Read-only; stored definitions are not a computed-style or visual-correctness guarantee.', 'diviskit-agent' ); ?></p>
	</div>
	<?php if ( ! function_exists( 'et_get_option' ) ) : ?>
		<p class="diviskit-callout" role="status"><?php esc_html_e( 'Design System unavailable: Divi is not active. No preset or variable registries have been requested.', 'diviskit-agent' ); ?></p>
	<?php else : ?>
	<div class="diviskit-ds-toolbar">
		<div class="diviskit-ds-views" role="group" aria-label="<?php esc_attr_e( 'Design System view', 'diviskit-agent' ); ?>">
			<button type="button" class="button" data-view="presets" aria-pressed="true"><?php esc_html_e( 'Presets', 'diviskit-agent' ); ?></button>
			<button type="button" class="button" data-view="variables" aria-pressed="false"><?php esc_html_e( 'Variables', 'diviskit-agent' ); ?></button>
		</div>
		<label class="diviskit-ds-search"><span class="screen-reader-text"><?php esc_html_e( 'Search name, ID or type', 'diviskit-agent' ); ?></span><input type="search" data-search placeholder="<?php esc_attr_e( 'Search name, ID or type', 'diviskit-agent' ); ?>" /></label>
		<button type="button" class="button" data-refresh title="<?php esc_attr_e( 'Refresh registry', 'diviskit-agent' ); ?>" aria-label="<?php esc_attr_e( 'Refresh registry', 'diviskit-agent' ); ?>"><span class="dashicons dashicons-update" aria-hidden="true"></span></button>
	</div>
	<p data-status role="status" aria-live="polite"><?php esc_html_e( 'Not loaded.', 'diviskit-agent' ); ?></p>
	<div data-notices></div>
	<div class="diviskit-ds-workspace">
		<section class="diviskit-ds-list" aria-label="<?php esc_attr_e( 'Registry entries', 'diviskit-agent' ); ?>"><ul data-list></ul><button type="button" class="button" data-more hidden><?php esc_html_e( 'Show more', 'diviskit-agent' ); ?></button></section>
		<section class="diviskit-ds-detail" data-detail tabindex="-1" aria-label="<?php esc_attr_e( 'Selected entry', 'diviskit-agent' ); ?>"><p><?php esc_html_e( 'No entry selected.', 'diviskit-agent' ); ?></p></section>
	</div>
	<noscript><p><?php esc_html_e( 'JavaScript is required for on-demand inspection. The same data is available through the Diviskit preset and variable tools.', 'diviskit-agent' ); ?></p></noscript>
	<?php endif; ?>
</section>
