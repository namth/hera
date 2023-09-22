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

        if ($access_token) {
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
    }

    # Xử lý nốt khi đăng ký qua zalo.
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
                        $result = Generate_Featured_Image($avatar, $user);
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
    } else if (
        isset($_POST['register_nonce_field']) &&
        wp_verify_nonce($_POST['register_nonce_field'], 'register_nonce')
    ) {

    }
    get_header();

    # Zalo login link
    $code_verify = generate_verify_code();
    update_field('field_6356c04455afc', $code_verify, 'option');
    
    $code_challenge = generate_code_challenge($code_verify);
    $url = get_permalink();
?>
<div id="login">
    <div class="large_left" style="
        background-image: url('https://source.unsplash.com/1920x1024?hdr,flower,coffee,wedding');
        background-size: cover;
    "></div>
    <div class="small_right mui-panel">
        <img src="<?php echo get_template_directory_uri(); ?>/img/logo_hera.png">
        <?php 
            # Nếu có callback Zalo trả dữ liệu về thì kiểm tra, rồi tạo form lấy email của khách hàng.
            if (isset($_GET['code']) && !$user_exists ) {
                
        ?>
        <form name="registerZalo" action="<?php echo get_permalink(); ?>" method="post" id="loginform">
            <?php 
                if ($notification) {
                    echo "<span>" . $notification . "</span>";
                }
            ?>
            <p class="login-username">
                <label for="user_email">Nhập email của bạn</label>
                <input type="text" name="user_email" class="input" placeholder="Không bắt buộc" value="<?php echo $user_email; ?>">
                <input type="hidden" name="user_login" value="<?php echo $user_login; ?>">
                <input type="hidden" name="display_name" value="<?php echo $display_name; ?>">
                <input type="hidden" name="avatar" value="<?php echo $avatar; ?>">
            </p>
            <p class="login-submit">
                <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
                <input type="hidden" name="redirect_to" value="<?php echo get_bloginfo('url'); ?>">
                <input type="submit" name="wp-submit" id="wp-submit" value="Đăng ký">
            </p>
        </form>
        <?php 
                
            } else if( $user_exists ){
                $url = get_bloginfo('url') . '/zalo-login';
                echo   '<div class="register_greeting">
                            <div class="social_login">
                                <h3>Tài khoản này đã tồn tại.</h3>
                                <p>Hãy quay trở lại và chọn hình thức đăng nhập hoặc <a target="_blank" href="https://id.zalo.me/" class="link">đổi tài khoản zalo khác</a></p>';
                echo            '<a href="' . get_bloginfo('url') . '/login" class="mui-btn hera-btn">Quay lại trang đăng nhập</a>';
                echo            '<div class="zalo_btn social_btn">
                                    <a href="https://oauth.zaloapp.com/v4/permission?app_id=4424878354763274341&redirect_uri=' . $url . '&code_challenge=' . $code_challenge . '&state=' . $code_verify . '">
                                        <img src="' . get_template_directory_uri() . '/img/zl.webp" alt="" /> <span>Đăng nhập bằng tài khoản này</span>
                                    </a>
                                </div>';
                echo   '    </div>
                        </div>';
            } else {
        ?>
        <div class="register_greeting">
            <div class="social_login">
                <h3>Bạn chưa có tài khoản?</h3>
                <p>Xin hãy chọn một số lựa chọn bên dưới.</p>
                <div class="google_btn social_btn">
                    <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/gg.svg" alt="" /> <span>Đăng ký bằng Google</span>
                    </a>
                </div>
                <div class="facebook_btn social_btn">
                    <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="600" data-popupheight="679">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/fb.png" alt="" /> <span>Đăng ký bằng Facebook</span>
                    </a>
                </div>
                <?php
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
                <form name="loginform" id="register_form" action="" method="post">
                    <p class="login-username">
                        <label for="user_login">Tên đăng nhập</label>
                        <input type="text" name="user_login" id="user_login" class="input">
                        <span class="form_check_icon"></span>
                        <span class="user_error_notif error"></span>
                    </p>
                    <p class="login-username">
                        <label for="user_email">Email</label>
                        <input type="text" name="user_email" id="user_email" class="input">
                        <span class="form_check_icon"></span>
                        <span class="email_error_notif error"></span>
                    </p>
                    <p class="login-password">
                        <label for="user_pass">Mật khẩu</label>
                        <input type="password" name="user_pass" class="input">
                        <span class="form_check_icon"></span>
                        <span class="password_error_notif error"></span>
                    </p>
                    <p class="login-password">
                        <span class="imgloading" data-loading="<?php echo get_template_directory_uri() . '/img/loader.gif'; ?>"></span>
                        <label for="user_pass">Nhập lại mật khẩu</label>
                        <input type="password" name="confirm_pass" class="input">
                        <?php wp_nonce_field('register_nonce', 'register_nonce_field'); ?>
                        <input type="hidden" name="redirect_to" value="<?php echo get_bloginfo('url'); ?>">
                        <span class="confirm_password_error_notif error"></span>
                    </p>
                    <p class="login-submit">
                        <button class="mui-btn fullwidth check_data" disabled style="margin-top: 10px;">Tiếp tục</button>
                    </p>
                </form>
                <div class="slidercaptcha card" style="display: none;">
                    <div class="card-header">
                        <span>Xác thực đăng ký</span>
                    </div>
                    <div class="card-body">
                        <div id="captcha"></div>
                    </div>
                </div>
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
<script src="<?php echo get_template_directory_uri() . '/js/slidecaptcha/longbow.slidercaptcha.min.js'?>"></script>
<script>
    jQuery(document).ready(function ($) {
        /* Khi bấm nút đăng ký với tài khoản Hera thì sẽ hiện form đăng ký */
        $('.email_btn').click(function (){
            $('.social_login').hide();
            $('.hera_register').show();
        });

        var check_username  = false;
        var check_email     = false;
        var check_password  = false;
        var checkicon       = '<i class="fa fa-check-circle" aria-hidden="true"></i>';
        var loading         = '<img src="<?php echo get_template_directory_uri() ?>/img/heart-preloader.gif" alt="">';

        /* Dùng axjax kiểm tra user đã sử dụng chưa */
        $('input[name="user_login"]').change(function(){
            var user_login = $(this).val();
            user_login = user_login.replace(/[^a-zA-Z.0-9]+/g, '');
            $(this).val(user_login);

            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "checkUsernameExist",
                    user_login: user_login,
                },
                beforeSend: function() {
                    $('input[name="user_login"]').prop('disabled', true).parent().find('.form_check_icon').html(loading);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    if (!resp) {
                        $('.user_error_notif').html('');
                        $('input[name="user_login"]').prop('disabled', false).parent().find('.form_check_icon').html(checkicon);
                        check_username = true;
                        if(check_password && check_email) {
                            $('#register_form').hide();
                            $('.slidercaptcha').show();
                        }
                    } else {
                        $('.user_error_notif').html(resp);
                        $('input[name="user_login"]').prop('disabled', false).parent().find('.form_check_icon').html('');
                        check_username = false;
                    }
                },
            });
        });

        /* Dùng axjax kiểm tra email đã sử dụng chưa */
        $('input[name="user_email"]').change(function(){
            var user_email = $(this).val();
            user_email = user_email.replace(/[^a-zA-Z0-9_/-@.]/g, '');
            $(this).val(user_email);

            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "checkEmailExist",
                    user_email: user_email,
                },
                beforeSend: function() {
                    $('input[name="user_email"]').prop('disabled', true).parent().find('.form_check_icon').html(loading);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    if (!resp) {
                        $('.email_error_notif').html('');
                        $('input[name="user_email"]').prop('disabled', false).parent().find('.form_check_icon').html(checkicon);
                        check_email = true;
                        if(check_password && check_username) {
                            $('#register_form').hide();
                            $('.slidercaptcha').show();
                        }
                    } else {
                        $('.email_error_notif').html(resp);
                        $('input[name="user_email"]').prop('disabled', false).parent().find('.form_check_icon').html('');
                        check_email = false;
                    }
                },
            });
        });

        /* Kiểm tra mật khẩu confirm có chính xác không */
        /* Nếu confirm_pass có dữ liệu thì mới check */
        $('input[type="password"]').change(function(){
            var user_pass = $('input[name="user_pass"]').val();
            var confirm_pass = $('input[name="confirm_pass"]').val();

            if ((user_pass.length < 8) || (user_pass.length > 20)) {
                $('span.password_error_notif').html('Mật khẩu phải từ 8 đến 20 ký tự');
            } else {
                $('input[name="user_pass"]').parent().find('.form_check_icon').html(checkicon);
                $('span.password_error_notif').html('');
            }

            if (confirm_pass) {
                if (user_pass != confirm_pass) {
                    $('span.confirm_password_error_notif').html('Mật khẩu xác nhận không trùng khớp');
                } else {
                    $('span.confirm_password_error_notif').html('');
                    check_password = true;

                    /* chuyển sang phần nhập captcha và submit */
                    if(check_username && check_email) {
                        $('#register_form').hide();
                        $('.slidercaptcha').show();
                    }
                }                
            } else {
                check_password = false;
            }
        });
        
        /* Hiển thị captcha slider */
        var captcha = sliderCaptcha({
            id: 'captcha',
            onSuccess: function () {  
                var $data = $(".hera_register form").serialize();
                var loading = $('.imgloading').data('loading');
                $.ajax({
                    type: "POST",
                    url: AJAX.ajax_url,
                    data: {
                        action: "registerhera",
                        data: $data,
                    },
                    beforeSend: function() {
                        $('.slidercaptcha').hide(); 
                        $('.hera_register').append('<img src="' + loading + '" style="margin: 0 auto; width: 60px;"/>');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    },
                    success: function (resp) {
                        window.location.href = resp;
                    },
                });
            }
        });
    });
</script>
<?php
    get_footer();
}