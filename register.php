<?php
/* 
    Template Name: Register
*/
if (is_user_logged_in()) {
    # redirect sang trang chủ
    wp_redirect(get_bloginfo('url'));
    exit;
} else {
    if (isset($_GET['code'])) {
        $authorization_code = $_GET['code'];
        $code_verifier = get_field('zalo_code_verifier', 'option');

        # get access code 
        $access_token = get_access_token($authorization_code, $code_verifier);
        # get user data
        $output = get_zalo_user_data($access_token);

        # Kiểm tra trong hệ thống xem có tài khoản này hay chưa
        $check_user = username_exists($output->id);
        if (!$check_user) {
            $user_exists = false;
            $display_name = $output->name;
            $user_login = $output->id;
            $avatar     = $output->picture->data->url;
        } else {
            $user_exists = true;
        }
    }

    # Nếu có dữ liệu từ $_POST gửi tới thì xử lý
    if (
        isset($_POST['post_nonce_field']) &&
        wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
    ) {
        $display_name = $_POST['display_name'];
        $user_login = $_POST['user_login'];
        $avatar     = $_POST['avatar'];
        $redirect   = $_POST['redirect_to'];
        $user_pass  = incrementalHash(10);
        $user_email = $_POST['user_email'];
        if (!$user_email) $user_email = incrementalHash(8) . '@hra.vn';
        if (is_email($user_email)) {
            if (!email_exists($user_email)) {
                # Nếu email đã tồn tại trong hệ thống thì báo lỗi, nếu không thì tiếp tục
                if ($user_login) {
                    $args = [
                        'user_login'    => $user_login,
                        'user_pass'     => $user_pass,
                        'user_email'    => $user_email,
                        'display_name'  => $display_name,
                        'user_nicename' => $display_name,
                    ];
            
                    $user = wp_insert_user($args);
                    
                    if ($user) {
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
                $notification = "Email đã tồn tại trong hệ thống";
            }
        } else {
            $notification = "Email này không hợp lệ";
        }
    }
    get_header();
?>
<div id="login">

    <div class="large_left" style="
        background-image: url('https://margoandbees.com/thumbs/887/templates/template_7/8/images/products/407/002GNBo/wedding-invitations-gold-rose-gold-silver-glitter-002-gn-z.jpg');
        background-size: cover;
    "></div>
    <div class="small_right mui-panel">
        <img src="<?php echo get_template_directory_uri(); ?>/img/logo_hera.png">
        <?php 
            # Nếu có callback Zalo trả dữ liệu về thì kiểm tra, rồi tạo form lấy email của khách hàng.
            if (isset($_GET['code']) && !$user_exists ) {
                
        ?>
        <form name="registerZalo" action="<?php echo get_permalink(); ?>" method="post" id="loginform">
            <p class="login-username">
                <label for="user_email">Nhập email của bạn</label>
                <input type="text" name="user_email" class="input" placeholder="Không bắt buộc" value="<?php echo $user_email; ?>">
                <input type="hidden" name="user_login" value="<?php echo $user_login; ?>">
                <input type="hidden" name="display_name" value="<?php echo $display_name; ?>">
                <input type="hidden" name="avatar" value="<?php echo $avatar; ?>">
                <?php 
                    if ($notification) {
                        echo "<span>" . $notification . "</span>";
                    }
                ?>
            </p>
            <p class="login-submit">
                <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
                <input type="hidden" name="redirect_to" value="<?php echo get_bloginfo('url'); ?>">
                <input type="submit" name="wp-submit" id="wp-submit" value="Đăng ký">
            </p>
        </form>
        <?php 
                
            } else if( $user_exists ){
                echo   '<div class="register_greeting">
                            <h3>Tài khoản này đã tồn tại.</h3>
                            <p>Hãy quay trở lại và chọn hình thức đăng nhập</p>';
                echo        '<a href="' . get_bloginfo('url') . '/login" class="mui-btn hera-btn">Quay lại trang đăng nhập</a>';
                echo   '</div>';
            } else {
        ?>
        <div class="register_greeting">
            <div class="social_login">
                <h3>Bạn chưa có tài khoản?</h3>
                <p>Xin hãy chọn một số lựa chọn bên dưới.</p>
                <div class="google_btn social_btn">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-login.php?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/gg.svg" alt="" /> <span>Đăng ký bằng Google</span>
                    </a>
                </div>
                <div class="facebook_btn social_btn">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-login.php?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="600" data-popupheight="679">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/fb.png" alt="" /> <span>Đăng ký bằng Facebook</span>
                    </a>
                </div>
                <?php
                    # Zalo login link
                    $code_verify = generate_verify_code();
                    update_field('field_6356c04455afc', $code_verify, 'option');
                    
                    $code_challenge = generate_code_challenge($code_verify);
                    $url = get_permalink();
                    
                    echo '<div class="zalo_btn social_btn">
                            <a href="https://oauth.zaloapp.com/v4/permission?app_id=4424878354763274341&redirect_uri=' . $url . '&code_challenge=' . $code_challenge . '&state=' . $code_verify . '">
                                <img src="' . get_template_directory_uri() . '/img/zl.webp" alt="" /> <span>Đăng ký bằng Zalo</span>
                            </a>
                        </div>';
                ?>
                <div class="email_btn social_btn">
                    <a href="#">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/favicon.png" alt="" /> <span>Đăng ký tài khoản HERA</span>
                    </a>
                </div>
            </div>
            <div class="hera_register">
                <form name="loginform" id="loginform" action="<?php echo get_bloginfo('url'); ?>/wp-login.php" method="post">
                    <p class="login-username">
                        <label for="user_login">Tên đăng nhập</label>
                        <input type="text" name="log" id="user_login" autocomplete="username" class="input" value="" size="20">
                    </p>
                    <p class="login-password">
                        <label for="user_pass">Mật khẩu</label>
                        <input type="password" name="pwd" id="user_pass" autocomplete="current-password" class="input" value="" size="20">
                    </p>
                    <p class="login-password">
                        <label for="user_pass">Nhập lại mật khẩu</label>
                        <input type="password" name="pwd" id="user_pass" autocomplete="current-password" class="input" value="" size="20">
                    </p>
                    <p class="login-submit">
                        <input type="submit" name="wp-submit" id="wp-submit" value="Đăng ký">
                        <input type="hidden" name="redirect_to" value="<?php echo get_bloginfo('url'); ?>">
                    </p>
                </form>
            </div>
        </div>
        <?php 
            }
        ?>
        <div class="signup">
            <p>Bạn đã có tài khoản? <a href="<?php echo get_bloginfo('url'); ?>/login">Đăng nhập</a></p>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        $('.email_btn').click(function (){
            $('.social_login').hide();
            $('.hera_register').show();
        });
    });
</script>
<?php
    get_footer();
}