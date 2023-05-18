<?php
$api_dir = dirname( __FILE__ );
require_once( $api_dir . '/payment/casso_endpoint.php');
require_once( $api_dir . '/payment/momo_endpoint.php');
require_once( $api_dir . '/payment/tpbank_api.php');

# get authentication code
# refresh token everyday
function refresh_token()
{
    $api_url  = get_field('api_base_url', 'option') . '/wp-json/inova/v1/gettoken';

    $user = array(
        'username' => INOVAUSER,
        'password' => INOVAPASS,
    );

    # authenticate to get token
    $jwt = wp_remote_post(
        $api_url,
        array(
            'method'        => 'POST',
            'timeout'       => '60',
            'body'          => $user,
        )
    );

    $token = json_decode(wp_remote_retrieve_body($jwt));
    // print_r($token);
    if (!$token->token) {
        return false;
    } else {
        update_field('field_62a6a717c5658', $token->token, 'option');
        return $token->token;
    }
};

function check_token($token) {
    $api_url  = get_field('api_base_url', 'option') . '/wp-json/inova/v1/checktoken';
    $check = inova_api($api_url, $token, 'POST', '');

    if ($check->code == 'success') {
        return true;
    } else {
        return false;
    }
}

# call any api with authentication token
function inova_api($api, $token, $method, $body) {
    $args = array(
        'method'    => $method,
        'timeout'   => '120',
        'headers'   => array(
            'Content-Type'  => 'application/json; charset=utf-8',
            'Authorization' => $token,
        ),
        'body'      => json_encode($body),
        'sslverify' => true,
    );

    $response = wp_remote_post(
        $api,
        $args
    );

    if (is_wp_error($response)) {
        return $response;
    } else {
        $response_body = json_decode(wp_remote_retrieve_body($response));
        return $response_body;
    }
}

# gọi API để lấy HTML khi đã chọn thiệp 
function getHTML($cardid){
    $token = refresh_token();
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $cardid;
    $mycard = inova_api($api_url, $token, 'GET', '');

    return $mycard->html;
}
