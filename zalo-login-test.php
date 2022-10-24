<?php
/* 
* Template name: Zalo login api test
*/
if (isset($_GET['code'])) {
    $authorization_code = $_GET['code'];
    $code_verifier = get_field('code_verifier', 'option');

    echo "<br>Authorization_code: <br><br>";
    echo $authorization_code;
    
    # get access code 
    echo "<br>Get access_code ...<br>";
    $data = array(
        'code'          => $authorization_code,
        'app_id'        => '3812610181368191268',
        'grant_type'    => 'authorization_code',
        'code_verifier' => $code_verifier,
    );
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "secret_key: PsjmsfgO9B1PQKmHlUS7",
    ));
    
    curl_setopt($ch, CURLOPT_URL,"https://oauth.zaloapp.com/v4/oa/access_token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
    
    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    $output = json_decode($server_output);

    # update refresh_token to db
    update_field('field_62419013d4ef2',$output->refresh_token , 'option');

    echo "Access token: " . $output->access_token;   
    /* $access_token = get_access_token();

    print_r($access_token);
    echo "<br>";

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.zalo.me/v2.0/me?fields=id,name,picture',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
        'access_token: ' . $access_token
        ),
    ));

    $server_output = curl_exec($curl);

    curl_close($curl);

    $output = json_decode($server_output);

    print_r($output); */
} else {
    $code_verify = generate_verify_code();
    // update_field('field_62431f72801e6', $code_verifier, 'option');

    echo "Code verify: " . $code_verify;
    $code_challenge = generate_code_challenge($code_verify);
    
    echo "<br><br>Code Challenge: " . $code_challenge;
    echo "<br><br><a target='_blank' href='https://oauth.zaloapp.com/v4/permission?app_id=4424878354763274341&redirect_uri=https://hera.inova.ltd/zalo-api-test/&code_challenge=" . $code_challenge . "&state=" . $code_verify . "'>Get authorization code</a>";
}

function generate_verify_code(){
    $random = openssl_random_pseudo_bytes(32);
    $verifier = base64_encode($random);
    return $verifier;
}

function generate_code_challenge($str) {
    return base64url_encode(pack('H*', hash('sha256', $str)));
}

function base64url_encode($plainText)
{
    $base64 = base64_encode($plainText);
    $base64 = trim($base64, "=");
    $base64url = strtr($base64, '+/', '-_');
    return ($base64url);
}