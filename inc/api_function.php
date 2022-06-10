<?php
# get authentication code
function get_token()
{
    $api_url  = "https://design.inova.ltd";
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
        $api_url . '/wp-json/api/v1/token',
        array(
            'method'        => 'POST',
            'timeout'       => '45',
            'headers'       => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'          => json_encode($user),
        )
    );

    $token = json_decode(wp_remote_retrieve_body($jwt));
    // print_r($token);
    if (!$token->jwt_token) {
        return false;
    } else {
        return $token->jwt_token;
    }
};

# call any api with authentication token
function inova_api($api, $token, $method, $body) {
    $args = array(
        'method'    => $method,
        'timeout'   => '45',
        'headers'   => array(
            'Content-Type'  => 'application/json; charset=utf-8',
            'Authorization' => 'Bearer ' . $token,
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