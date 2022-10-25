<?php
/* 
* Template name: Zalo login api test
*/
if (isset($_GET['code'])) {
    $authorization_code = $_GET['code'];
    $code_verifier = get_field('zalo_code_verifier', 'option');

    # get access code 
    $data = array(
        'code'          => $authorization_code,
        'app_id'        => '4424878354763274341',
        'grant_type'    => 'authorization_code',
        'code_verifier' => $code_verifier,
    );
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "secret_key: 1qFDWGD94kuPapqjryca",
    ));
    
    curl_setopt($ch, CURLOPT_URL,"https://oauth.zaloapp.com/v4/access_token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
    
    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    $output = json_decode($server_output);

    # update refresh_token to db
    update_field('field_6354e8e2fd49e',$output->refresh_token , 'option');

    $access_token = $output->access_token;

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

    # Kiểm tra trong hệ thống xem có tài khoản này hay chưa
    $userid = username_exists($output->id);
    if ($userid) {
        # Nếu có thì đăng nhập vào tài khoản đó và chuyển về trang chủ
        // dùng wp_signon() để đăng nhập
        $user_obj = get_user_by('ID', $userid);

        wp_set_current_user( $userid, $user_obj->user_login );
        wp_set_auth_cookie( $userid, true, false );
        do_action( 'wp_login', $user_obj->user_login, $userid );
        
        # redirect sang trang chủ
        wp_redirect( get_bloginfo('url') );
        exit;
    } else {
        # Nếu không có thì tạo tài khoản dựa trên ID của tài khoản zalo sau đó đăng nhập và chuyển về trang chủ
        $user_login = $output->id;
        $user_pass  = incrementalHash(10);
        $email      = incrementalHash(8) . '@hra.vn';

        $args = [
            'user_login'    => $output->id,
            'user_pass'     => incrementalHash(10),
            'user_email'    => incrementalHash(8) . '@hra.vn',
            'display_name'  => $output->name,
            'user_nicename' => $output->name,
        ];

        $user = wp_insert_user($args);
        
        if ($user) {
            # Đăng nhập sau khi tạo tài khoản
            wp_set_current_user( $user, $user_login );
            wp_set_auth_cookie( $user, true, false );
            do_action( 'wp_login', $user_login, $user );

            # Set avatar for user
            $result = Generate_Featured_Image($output->picture->data->url, $user);
            print_r($result);
            # Sau đó chuyển về trang chủ
            /* wp_redirect( get_bloginfo('url') );
            exit; */
        }
    }
} else {
    $code_verify = generate_verify_code();
    update_field('field_6356c04455afc', $code_verify, 'option');

    echo "Code verify: " . $code_verify;
    $code_challenge = generate_code_challenge($code_verify);
    
    echo "<br><br>Code Challenge: " . $code_challenge;
    echo "<br><br><a href='https://oauth.zaloapp.com/v4/permission?app_id=4424878354763274341&redirect_uri=" . get_permalink() . "&code_challenge=" . $code_challenge . "&state=" . $code_verify . "'>Login Zalo</a>";
}