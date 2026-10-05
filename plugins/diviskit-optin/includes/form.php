<?php
/**
 * SK MailerLite DOI — frontend form (shortcode [skml_doi_form]).
 *
 * Renders the Vision-styled signup card, submits to
 * POST /wp-json/skml/v1/subscribe via fetch — no jQuery.
 * Styling uses the site's Divi global-color CSS vars (var(--gcid-*))
 * with neutral fallbacks so the form also works off-Divi.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function skml_form_register_assets() {
    wp_register_style( 'skml-form', SKML_URL . 'assets/form.css', array(), SKML_VERSION );
    wp_register_script( 'skml-form', SKML_URL . 'assets/form.js', array(), SKML_VERSION, true );
}
add_action( 'init', 'skml_form_register_assets' );

function skml_form_shortcode() {
    $o         = skml_options();
    $captcha   = skml_recaptcha_active();
    $sitekey   = $captcha ? trim( (string) $o['recaptcha_site_key'] ) : '';
    $policy    = trim( (string) $o['policy_url'] );
    $interests = skml_interests_active() ? skml_interests() : array();

    wp_enqueue_style( 'skml-form' );
    wp_enqueue_script( 'skml-form' );
    wp_localize_script( 'skml-form', 'SKML', array(
        'rest'    => esc_url_raw( rest_url( 'skml/v1/subscribe' ) ),
        'error'   => 'Etwas ist schiefgelaufen. Bitte erneut versuchen.',
        'captcha' => $captcha,
        'site'    => $sitekey,
    ) );

    if ( $captcha ) {
        // reCAPTCHA v3 — invisible, token per submit via grecaptcha.execute().
        wp_enqueue_script( 'skml-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( $sitekey ), array(), null, true );
    }

    ob_start();
    ?>
    <div class="skml-form" data-skml-form>
      <div class="skml-body">
        <?php if ( '' !== $o['form_heading'] ) : ?>
          <h4 class="skml-heading"><?php echo esc_html( $o['form_heading'] ); ?></h4>
        <?php endif; ?>
        <?php if ( '' !== $o['form_subline'] ) : ?>
          <p class="skml-sub"><?php echo esc_html( $o['form_subline'] ); ?></p>
        <?php endif; ?>

        <form novalidate>
          <input type="email" name="email" required
                 placeholder="<?php echo esc_attr( $o['form_placeholder'] ); ?>"
                 aria-label="<?php echo esc_attr( $o['form_placeholder'] ); ?>"
                 autocomplete="email" class="skml-input">

          <?php if ( $interests ) : ?>
            <fieldset class="skml-interests">
              <?php if ( '' !== trim( (string) $o['interests_heading'] ) ) : ?>
                <legend class="skml-int-legend"><?php echo esc_html( $o['interests_heading'] ); ?></legend>
              <?php endif; ?>
              <?php foreach ( $interests as $slug => $int ) : ?>
                <label class="skml-consent skml-interest">
                  <input type="checkbox" name="interests[]" value="<?php echo esc_attr( $slug ); ?>">
                  <span class="skml-box" aria-hidden="true"></span>
                  <span class="skml-consent-text"><?php echo esc_html( $int['label'] ); ?></span>
                </label>
              <?php endforeach; ?>
            </fieldset>
          <?php endif; ?>

          <label class="skml-consent">
            <input type="checkbox" name="consent" required>
            <span class="skml-box" aria-hidden="true"></span>
            <span class="skml-consent-text">
              <?php echo esc_html( $o['consent_text'] ); ?>
              <?php if ( '' !== $policy ) : ?>
                <a href="<?php echo esc_url( $policy ); ?>">Datenschutzerklärung</a>
              <?php endif; ?>
            </span>
          </label>

          <div class="skml-hp" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>

          <?php if ( '' !== $sitekey ) : ?>
            <p class="skml-captcha-note">Geschützt durch reCAPTCHA —
              <a href="https://policies.google.com/privacy" rel="noopener" target="_blank">Datenschutz</a> /
              <a href="https://policies.google.com/terms" rel="noopener" target="_blank">Nutzungsbedingungen</a>.</p>
          <?php endif; ?>

          <button type="submit" class="skml-btn">
            <span class="skml-btn-label"><?php echo esc_html( $o['form_button'] ); ?></span>
            <span class="skml-spinner" aria-hidden="true"></span>
          </button>

          <p class="skml-msg" role="status" aria-live="polite"></p>
        </form>
      </div>

      <div class="skml-done" hidden>
        <?php if ( '' !== $o['form_success_heading'] ) : ?>
          <h4 class="skml-heading"><?php echo esc_html( $o['form_success_heading'] ); ?></h4>
        <?php endif; ?>
        <p class="skml-sub"><?php echo esc_html( $o['form_success'] ); ?></p>
      </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'skml_doi_form', 'skml_form_shortcode' );
