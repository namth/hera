<?php
/* 
* Template name: Zalo login api test
*/
if (isset($_GET['code'])) {
    $authorization_code = $_GET['code'];
    $code_verifier = get_field('zalo_code_verifier', 'option');

    $access_token = get_access_token($authorization_code, $code_verifier);

    # Lấy thông tin user từ access token
    $output = get_zalo_user_data($access_token);

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
            # add checking login number to user account 
            track_user_logins(get_user_by('ID', $user));

            # Đăng nhập sau khi tạo tài khoản
            wp_set_current_user( $user, $user_login );
            wp_set_auth_cookie( $user, true, false );
            do_action( 'wp_login', $user_login, $user );

            # Set avatar for user
            $result = Generate_Featured_Image($output->picture->data->url, $user);
            # Sau đó chuyển về trang chủ
            wp_redirect( get_bloginfo('url') );
            exit;
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