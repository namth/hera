<?php
/* 
* Template name: Zalo login api test
*/
require_once get_template_directory() . '/vendor/autoload.php';

use Zalo\Zalo;

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
        
        print_r($user_obj);
        # redirect sang trang chủ
        // wp_redirect( get_bloginfo('url') );
        // exit;
    } else {
        # Nếu không có thì tạo tài khoản dựa trên ID của tài khoản zalo sau đó đăng nhập và chuyển về trang chủ
        $user_login = $output->id;
        $user_pass  = incrementalHash(10);
        $email      = incrementalHash(8) . '@hera.ai.vn';

        $args = [
            'user_login'    => $user_login,
            'user_pass'     => $user_pass,
            'user_email'    => $email,
            'display_name'  => $output->name,
            'nickname'      => $output->name,
        ];

        $user = wp_insert_user($args);
        
        if ($user) {
            # Đăng nhập sau khi tạo tài khoản
            wp_set_current_user( $user, $user_login );
            wp_set_auth_cookie( $user, true, false );
            do_action( 'wp_login', $user_login, $user );
            
            # add checking login number to user account 
            if( $login_amount = get_user_meta( $user, 'login_amount', true ) ) {
                // They've Logged In Before, increment existing total by 1
                update_user_meta( $user, 'login_amount', ++$login_amount );
            } else {
                // First Login, set it to 1
                update_user_meta( $user, 'login_amount', 1 );
            }

            # Set avatar for user
            $avatar_url = $output->picture->data->url;
            if ($avatar_url) {
                $result = Generate_Featured_Image($avatar_url, $user);
            }

            print_r($output);
            # Sau đó chuyển về trang chủ
            // wp_redirect( get_bloginfo('url') );
            // exit;
        }
    }
} else {
    $code_verify = generate_verify_code();
    update_field('field_6356c04455afc', $code_verify, 'option');

    echo "Code verify: " . $code_verify;
    $code_challenge = generate_code_challenge($code_verify);
    
    echo "<br><br>Code Challenge: " . $code_challenge;
    echo "<br><br><a href='https://oauth.zaloapp.com/v4/permission?app_id=61533937584017085&redirect_uri=" . get_permalink() . "&code_challenge=" . $code_challenge . "&state=" . $code_verify . "'>Login Zalo</a>";
}