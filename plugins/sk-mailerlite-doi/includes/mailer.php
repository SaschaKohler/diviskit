<?php
/**
 * SK MailerLite DOI — confirmation mail.
 *
 * Sends a branded, German double-opt-in mail via wp_mail (use an SMTP
 * plugin / mail service for reliable delivery). Table-based layout and
 * inline styles only — email clients don't do modern CSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function skml_confirm_url( $token ) {
    return rest_url( 'skml/v1/confirm?token=' . rawurlencode( $token ) );
}

/**
 * @return bool wp_mail result
 */
function skml_send_confirm_mail( $email, $token ) {
    $o   = skml_options();
    $url = skml_confirm_url( $token );

    $host     = wp_parse_url( home_url(), PHP_URL_HOST );
    $headers  = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo( 'name' ) . ' <noreply@' . $host . '>',
    );

    return wp_mail( $email, $o['mail_subject'], skml_mail_html( $url ), $headers );
}

function skml_mail_html( $confirm_url ) {
    $o       = skml_options();
    $site    = get_bloginfo( 'name' );
    $home    = esc_url( home_url( '/' ) );
    $policy  = esc_url( $o['policy_url'] );
    $imprint = esc_url( $o['imprint_url'] );

    ob_start();
    ?>
<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#f1f1f1;font-family:-apple-system,'Segoe UI',Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f1f1;">
<tr><td align="center" style="padding:40px 16px;">

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background-color:#ffffff;border:1px solid #e2e2e2;">

    <tr><td style="padding:28px 32px 0;">
      <p style="margin:0;font-family:'Courier New',monospace;font-size:11px;letter-spacing:2px;color:#b89e00;text-transform:uppercase;"><?php echo esc_html( $site ); ?></p>
    </td></tr>

    <tr><td style="padding:16px 32px 0;">
      <h1 style="margin:0;font-size:26px;line-height:1.2;color:#17191a;font-weight:700;"><?php echo esc_html( $o['mail_heading'] ); ?></h1>
    </td></tr>

    <tr><td style="padding:16px 32px 0;">
      <p style="margin:0;font-size:15px;line-height:1.65;color:#3a3d3f;"><?php echo esc_html( $o['mail_intro'] ); ?></p>
    </td></tr>

    <tr><td style="padding:28px 32px;">
      <table role="presentation" cellpadding="0" cellspacing="0"><tr>
        <td style="background-color:#ffd400;">
          <a href="<?php echo esc_url( $confirm_url ); ?>"
             style="display:inline-block;padding:14px 28px;font-family:'Courier New',monospace;font-size:13px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#17191a;text-decoration:none;border:1px solid #17191a;"><?php echo esc_html( $o['mail_button'] ); ?></a>
        </td>
      </tr></table>
    </td></tr>

    <tr><td style="padding:0 32px 28px;">
      <p style="margin:0;font-size:12px;line-height:1.6;color:#8a8d8f;word-break:break-all;">
        Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br>
        <a href="<?php echo esc_url( $confirm_url ); ?>" style="color:#8a8d8f;"><?php echo esc_html( $confirm_url ); ?></a>
      </p>
    </td></tr>

  </table>

  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">
    <tr><td style="padding:20px 8px;font-size:11px;line-height:1.6;color:#8a8d8f;text-align:center;">
      <p style="margin:0 0 8px;"><?php echo esc_html( $o['mail_footer'] ); ?></p>
      <p style="margin:0;">
        <a href="<?php echo $home; ?>" style="color:#8a8d8f;"><?php echo esc_html( $site ); ?></a>
        <?php if ( $imprint ) : ?> · <a href="<?php echo $imprint; ?>" style="color:#8a8d8f;">Impressum</a><?php endif; ?>
        <?php if ( $policy ) : ?> · <a href="<?php echo $policy; ?>" style="color:#8a8d8f;">Datenschutz</a><?php endif; ?>
      </p>
    </td></tr>
  </table>

</td></tr>
</table>
</body>
</html>
<?php
    return ob_get_clean();
}
