<?php
//validation
function NAMESPACE_validate_re_captcha_field( $username, $email, $wpErrors )
{
    $remoteIP = $_SERVER['REMOTE_ADDR'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret'   => 'G-SECRET',
            'response' => $recaptchaResponse,
            'remoteip' => $remoteIP
        ]
    ] );

    $response_code = wp_remote_retrieve_response_code( $response );
    $response_body = wp_remote_retrieve_body( $response );

    if ( $response_code == 200 )
    {
        $result = json_decode( $response_body, true );

        if ( ! $result['success'] )
        {
            $wpErrors->add( 'recaptcha', __( 'Моля, поставете отметка в квадратчето, за да докажете, че не сте робот.', 'woocommerce' ) );
        }
    }
}
add_action( 'woocommerce_register_post', 'NAMESPACE_validate_re_captcha_field', 10, 3 );
?>
<!-- Display the recaptcha -->
<script src='https://www.google.com/recaptcha/api.js?hl=bg'></script>
<form action="">
    <div class="g-recaptcha" data-sitekey="G-KEY"></div>
</form>

