<?php
/* 
    Template Name: Single Inova Card
*/
get_header();
get_header('logocenter');

if (isset($_GET['c']) && ($_GET['c'] != "")) {
    $cardid = inova_encrypt($_GET['c'], 'd');
} else {
    $data = false;
}
$current_user_id = get_current_user_id();

$token = get_field('token', 'option');
# Kiểm tra nếu token vẫn hoạt động thì thôi, nếu không thì phải lấy lại token mới.
if (!check_token($token)) {
    $token = refresh_token();
}
$api_base_url = get_field('api_base_url', 'option');
$api_url = $api_base_url . '/wp-json/inova/v1/card/' . $cardid;
$mycard = inova_api($api_url, $token, 'GET', '');

$liked      = get_user_meta($current_user_id, 'liked')?get_user_meta($current_user_id, 'liked'):array();
$liked_arr = array();
if(!empty($liked)){
    $liked_arr  = explode(',', $liked[0]);
}
$icon       = in_array($cardid, $liked_arr)?"fa-heart":"fa-heart-o";

?>
<div class="mui-container-fluid" style="padding: 0 80px;">
    <div class="mui-row">
        <div class="mui-row" id="detail_card_popup">
            <div class="mui-col-lg-9 mui-col-md-12 card_thumbnail">
                <img src="<?php echo $mycard->thumbnail; ?>" alt="" width=100%>
            </div>
            <div class="mui-col-lg-3 mui-col-md-12" id="detail_data_box">
                <h2><?php echo $mycard->title; ?></h2>
                <div class="mui-divider"></div>
    
                <div class="button_group">
                    <button id="like" class="like mui-btn mui-btn--raised" data-card=<?php echo $cardid; ?>><i class="fa <?php echo $icon; ?>"></i></button>
                    <button class="mui-btn mui-btn--raised"><i class="fa fa-star-o" aria-hidden="true"></i></button>
                    <button class="mui-btn mui-btn--raised"><i class="fa fa-share-alt" aria-hidden="true"></i></button>
                </div>
                <div class="mui-divider"></div>
    
                <div class="card_content">
                    <?php echo $mycard->content; ?>
                </div>
                <div class="login_require">
                    <div class="social_login">
                        <h4>Vui lòng đăng nhập để sử dụng thiệp này.</h4>
                        <h4>Bạn có thể đăng nhập nhanh qua</h4>
                        <div class="google_btn social_btn">
                            <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/gg.svg" alt="" /> <span>Google</span>
                            </a>
                        </div>
                        <div class="facebook_btn social_btn">
                            <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="600" data-popupheight="679">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/fb.png" alt="" /> <span>Facebook</span>
                            </a>
                        </div>
                        <?php
                            # Zalo login link
                            $code_verify = generate_verify_code();
                            update_field('field_6356c04455afc', $code_verify, 'option');
                            
                            $code_challenge = generate_code_challenge($code_verify);
                            $url = get_bloginfo('url') . '/zalo-login';
                            
                            echo '<div class="zalo_btn social_btn">
                                    <a href="https://oauth.zaloapp.com/v4/permission?app_id=' . ZALO_APP_ID . '&redirect_uri=' . $url . '&code_challenge=' . $code_challenge . '&state=' . $code_verify . '">
                                        <img src="' . get_template_directory_uri() . '/img/zl.webp" alt="" /> <span>Zalo</span>
                                    </a>
                                </div>';
                        ?>
                    </div>
                    <div class="signup">
                        <p>Hoặc có thể đăng ký tài khoản tại đây.</p>
                        <a href="<?php echo get_bloginfo('url'); ?>/dang-ky"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt=""> Đăng ký</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        ?>
    </div>
</div>

<?php
get_footer();