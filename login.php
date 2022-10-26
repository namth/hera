<?php
/* 
    Template Name: Login
*/
if (is_user_logged_in()) {
    // redirect sang trang chủ
    wp_redirect(get_bloginfo('url'));
    exit;
} else {
    
    get_header();

?>
<div id="login">

    <div class="large_left" style="
        background-image: url('https://margoandbees.com/thumbs/887/templates/template_7/8/images/products/407/002GNBo/wedding-invitations-gold-rose-gold-silver-glitter-002-gn-z.jpg');
        background-size: cover;
    "></div>
    <div class="small_right mui-panel">
        <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="" width="80">
        <form name="loginform" id="loginform" action="<?php echo get_bloginfo('url'); ?>/wp-login.php" method="post">
            <p class="login-username">
                <label for="user_login">Tên đăng nhập</label>
                <input type="text" name="log" id="user_login" autocomplete="username" class="input" value="" size="20">
            </p>
            <p class="login-password">
                <label for="user_pass">Mật khẩu</label>
                <input type="password" name="pwd" id="user_pass" autocomplete="current-password" class="input" value="" size="20">
            </p>
            <p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Tự động đăng nhập</label></p><p class="login-submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Đăng nhập">
                <input type="hidden" name="redirect_to" value="<?php echo get_bloginfo('url'); ?>">
            </p>

            <div class="social_login">
                <div class="google_btn social_btn">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-login.php?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/gg.svg" alt="" /> <span>Đăng nhập bằng Google</span>
                    </a>
                </div>
                <div class="facebook_btn social_btn">
                    <a href="<?php echo get_bloginfo('url'); ?>/wp-login.php?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="600" data-popupheight="679">
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
                            <a href="https://oauth.zaloapp.com/v4/permission?app_id=4424878354763274341&redirect_uri=' . $url . '&code_challenge=' . $code_challenge . '&state=' . $code_verify . '">
                                <img src="' . get_template_directory_uri() . '/img/zl.webp" alt="" /> <span>Đăng nhập bằng Zalo</span>
                            </a>
                        </div>';
                ?>
            </div>
        </form>
    </div>
</div>
<?php
    get_footer();
}