<?php
/* 
    Template Name: New Login
*/
if (is_user_logged_in()) {
    // redirect sang trang chủ
    wp_redirect(get_bloginfo('url') . '/doi-tac/');
    exit;
} else {
    // check form
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
        isset( $_POST['post_nonce_field'] ) && 
        wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {
        
        if (isset($_POST)) {
            $error = false;
            
            if ( isset($_POST['username']) && ($_POST['username'] != "") ) {
                $username = $_POST['username'];

                if ( isset($_POST['password']) && ($_POST['password'] != "") ) {
                    $password = $_POST['password'];
                } else {
                    $error = true;
                    $error_message = __('Mời bạn nhập mật khẩu.', 'hera');
                }
            } else {
                $error = true;
                $error_message = __('Mời bạn nhập User ID / Email.', 'hera');
            }    

            if ( isset($_POST['remember']) && ($_POST['remember'] == "on") ) {
                $remember = true;
            } else {
                $remember = false;
            }

        } else $error = true;
        
        if (!$error) {
            // dùng wp_signon() để đăng nhập
            $user = wp_signon( array(
                'user_login'    => $_POST['username'],
                'user_password' => $_POST['password'],
                'remember'      => $remember,
            ), false );

            // print_r($user);

            $userID = $user->ID;

            wp_set_current_user( $userID, $username );
            wp_set_auth_cookie( $userID, true, false );
            do_action( 'wp_login', $username, $user );
            
            // redirect sang trang chủ
            wp_redirect( get_bloginfo('url') . '/doi-tac/');
            exit;
        }
    }
    get_header();

?>
<div id="newlogin" style="
        background-image: url('https://source.unsplash.com/1920x1024?hdr,wedding,flower');
        background-size: cover;
">
    <form name="loginform" id="loginform" action="" method="post">
        <img src="<?php echo get_template_directory_uri(); ?>/img/logo_hera.png">
        <p class="error_message"><?php if ($error_message) {
            echo $error_message;
        } ?></p>
        <p class="login-username">
            <label for="user_login">Tên đăng nhập</label>
            <input type="text" name="username" id="user_login" autocomplete="username" class="input" value="" size="20">
        </p>
        <p class="login-password">
            <label for="user_pass">Mật khẩu</label>
            <input type="password" name="password" id="user_pass" autocomplete="current-password" class="input" value="" size="20">
        </p>
        <?php 
            wp_nonce_field( 'post_nonce', 'post_nonce_field' );
        ?>
        <p class="login-remember"><label>
            <input name="remember" type="checkbox" id="rememberme" value="forever"> Tự động đăng nhập</label></p><p class="login-submit">
            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Đăng nhập">
            <input type="hidden" name="redirect_to" value="<?php echo get_bloginfo('url'); ?>">
        </p>

        <!-- <div class="social_login">
            <div class="google_btn social_btn">
                <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/gg.svg" alt="" /> <span>Đăng nhập bằng Google</span>
                </a>
            </div>
            <div class="facebook_btn social_btn">
                <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="600" data-popupheight="679">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/fb.png" alt="" /> <span>Đăng nhập bằng Facebook</span>
                </a>
            </div>
            <?php
                # Zalo login link
                $code_verify = generate_verify_code();
                update_field('field_6356c04455afc', $code_verify, 'option');
                
                $code_challenge = generate_code_challenge($code_verify);
                $url = get_bloginfo('url') . '/zalo-login';
                
                echo '<div class="zalo_btn social_btn">
                        <a href="https://oauth.zaloapp.com/v4/permission?app_id=61533937584017085&redirect_uri=' . $url . '&code_challenge=' . $code_challenge . '&state=' . $code_verify . '">
                            <img src="' . get_template_directory_uri() . '/img/zl.webp" alt="" /> <span>Đăng nhập bằng Zalo</span>
                        </a>
                    </div>';
            ?>
        </div>
        <div class="signup">
            <p>Bạn chưa có tài khoản? <a href="<?php echo get_bloginfo('url'); ?>/dang-ky">Đăng ký</a></p>
        </div> -->
    </form>

</div>



<?php wp_footer(  ); ?>
</body>

</html>
<?php
}