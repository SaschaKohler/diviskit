<?php
/**
 * Diviskit Optin — frontend form (shortcode [diviskit_optin_form],
 * legacy alias [skml_doi_form] from sk-mailerlite-doi <=0.3.0).
 *
 * Renders the Vision-styled signup card, submits to
 * POST /wp-json/diviskit-optin/v1/subscribe via fetch — no jQuery.
 * Styling uses the site's Divi global-color CSS vars (var(--gcid-*))
 * with neutral fallbacks so the form also works off-Divi.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function dkopt_form_register_assets() {
    wp_register_style( 'dkopt-form', DIVISKIT_OPTIN_URL . 'assets/form.css', array(), DIVISKIT_OPTIN_VERSION );
    wp_register_script( 'dkopt-form', DIVISKIT_OPTIN_URL . 'assets/form.js', array(), DIVISKIT_OPTIN_VERSION, true );
}
add_action( 'init', 'dkopt_form_register_assets' );

function dkopt_form_shortcode() {
    $o         = dkopt_options();
    $captcha   = dkopt_recaptcha_active();
    $sitekey   = $captcha ? trim( (string) $o['recaptcha_site_key'] ) : '';
    $policy    = trim( (string) $o['policy_url'] );
    $interests = dkopt_interests_active() ? dkopt_interests() : array();

    wp_enqueue_style( 'dkopt-form' );
    wp_enqueue_script( 'dkopt-form' );
    wp_localize_script( 'dkopt-form', 'DKOPT', array(
        'rest'    => esc_url_raw( rest_url( 'diviskit-optin/v1/subscribe' ) ),
        'error'   => 'Etwas ist schiefgelaufen. Bitte erneut versuchen.',
        'captcha' => $captcha,
        'site'    => $sitekey,
    ) );

    if ( $captcha ) {
        // reCAPTCHA v3 — invisible, token per submit via grecaptcha.execute().
        wp_enqueue_script( 'dkopt-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( $sitekey ), array(), null, true );
    }

    ob_start();
    ?>
    <div class="dkopt-form" data-dkopt-form>
      <div class="dkopt-body">
        <?php if ( '' !== $o['form_heading'] ) : ?>
          <h4 class="dkopt-heading"><?php echo esc_html( $o['form_heading'] ); ?></h4>
        <?php endif; ?>
        <?php if ( '' !== $o['form_subline'] ) : ?>
          <p class="dkopt-sub"><?php echo esc_html( $o['form_subline'] ); ?></p>
        <?php endif; ?>

        <form novalidate>
          <input type="email" name="email" required
                 placeholder="<?php echo esc_attr( $o['form_placeholder'] ); ?>"
                 aria-label="<?php echo esc_attr( $o['form_placeholder'] ); ?>"
                 autocomplete="email" class="dkopt-input">

          <?php if ( $interests ) : ?>
            <fieldset class="dkopt-interests">
              <?php if ( '' !== trim( (string) $o['interests_heading'] ) ) : ?>
                <legend class="dkopt-int-legend"><?php echo esc_html( $o['interests_heading'] ); ?></legend>
              <?php endif; ?>
              <?php foreach ( $interests as $slug => $int ) : ?>
                <label class="dkopt-consent dkopt-interest">
                  <input type="checkbox" name="interests[]" value="<?php echo esc_attr( $slug ); ?>">
                  <span class="dkopt-box" aria-hidden="true"></span>
                  <span class="dkopt-consent-text"><?php echo esc_html( $int['label'] ); ?></span>
                </label>
              <?php endforeach; ?>
            </fieldset>
          <?php endif; ?>

          <label class="dkopt-consent">
            <input type="checkbox" name="consent" required>
            <span class="dkopt-box" aria-hidden="true"></span>
            <span class="dkopt-consent-text">
              <?php echo esc_html( $o['consent_text'] ); ?>
              <?php if ( '' !== $policy ) : ?>
                <a href="<?php echo esc_url( $policy ); ?>">Datenschutzerklärung</a>
              <?php endif; ?>
            </span>
          </label>

          <div class="dkopt-hp" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>

          <?php if ( '' !== $sitekey ) : ?>
            <p class="dkopt-captcha-note">Geschützt durch reCAPTCHA —
              <a href="https://policies.google.com/privacy" rel="noopener" target="_blank">Datenschutz</a> /
              <a href="https://policies.google.com/terms" rel="noopener" target="_blank">Nutzungsbedingungen</a>.</p>
          <?php endif; ?>

          <button type="submit" class="dkopt-btn">
            <span class="dkopt-btn-label"><?php echo esc_html( $o['form_button'] ); ?></span>
            <span class="dkopt-spinner" aria-hidden="true"></span>
          </button>

          <p class="dkopt-msg" role="status" aria-live="polite"></p>
        </form>
      </div>

      <div class="dkopt-done" hidden>
        <?php if ( '' !== $o['form_success_heading'] ) : ?>
          <h4 class="dkopt-heading"><?php echo esc_html( $o['form_success_heading'] ); ?></h4>
        <?php endif; ?>
        <p class="dkopt-sub"><?php echo esc_html( $o['form_success'] ); ?></p>
      </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'diviskit_optin_form', 'dkopt_form_shortcode' );
add_shortcode( 'skml_doi_form', 'dkopt_form_shortcode' ); // legacy sk-mailerlite-doi
