<?php
# get authentication code
# refresh token everyday
function refresh_token()
{
    $api_url  = get_field('api_base_url', 'option') . '/wp-json/inova/v1/gettoken';
    $username = "inovacard";
    $password = "matkhaumoi2@@";

    $user = array(
        'username' => $username,
        'password' => $password,
    );

    // echo "Connect to " . $api_url . " ...<br>";
    // echo "Username: " . $username . "<br>";
    // echo "Password: " . $password . "<br>";

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

# call any api with authentication token
function inova_api($api, $token, $method, $body) {
    $args = array(
        'method'    => $method,
        'timeout'   => '60',
        'headers'   => array(
            'Content-Type'  => 'application/json; charset=utf-8',
            'Authorization' => $token,
        ),
        'body'  => json_encode($body),
    );

    $response = wp_remote_post(
        $api,
        $args
    );

    $response_body = json_decode(wp_remote_retrieve_body($response));

    return $response_body;
}


