<?php
/* 
* Template Name: Đổi mật khẩu
*/
$current_user = wp_get_current_user();

if (
    isset($_POST['post_nonce_field']) &&
    wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
) {
    $user_oldpass   = strip_tags($_POST['user_oldpass']);
    $confirm_pass   = strip_tags($_POST['confirm_pass']);
    $usr_args       = [
        'ID'    => $current_user->ID
    ];

    $check = wp_authenticate_username_password( NULL, $current_user->user_login, $confirm_pass );
    if ($confirm_pass && $check) {
        $usr_args['user_pass'] = $confirm_pass;
    } 

    $updated = wp_update_user($usr_args);

    # redirect to user page
    wp_redirect(get_author_posts_url($current_user->ID));
    exit;
}

get_header();
get_template_part('header', 'topbar');

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-8 mt20">
            <!-- <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                
            </div> -->
            <div class="mui-panel">
                <div class="back-btn mb20">
                    <a href="javascript:history.back()"><i class="fa fa-arrow-left"></i> Quay lại</a>
                </div>

                <h3 class="title_general">Đổi mật khẩu</h3>
                <p>Mật khẩu phải có tối thiểu 8 ký tự và tối đa 20 ký tự, bao gồm cả chữ hoa, chữ thường và ký tự đặc biệt.</p>
                <div class="mui-row">
                    <div class="mui-col-lg-7 mui-col-md-12">
                        <form class="mui-form" method="POST" name="guest_form" id="hera_form">
                            <div class="mui-textfield">
                                <input class="password" type="password" name="user_oldpass" id="user_oldpass" style="width: 92%;">
                                <label for="user_oldpass">Mật khẩu cũ</label>
                                <span class="form_check_icon"></span>
                                <span class="see_pass"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                <span class="pass_error_notif error"></span>
                            </div>
                            <div class="mui-textfield">
                                <input class="password confirm_pass" type="password" name="user_newpass" id="user_newpass" style="width: 92%;">
                                <label for="user_newpass">Mật khẩu mới</label>
                            </div>
                            <div class="mui-textfield">
                                <input class="password confirm_pass" type="password" name="confirm_pass" id="confirm_pass" style="width: 92%;">
                                <label for="confirm_pass">Nhập lại mật khẩu mới</label>
                                <span class="form_check_icon"></span>
                                <span class="confirm_error_notif error"></span>
                            </div>
                            <?php
                            wp_nonce_field('post_nonce', 'post_nonce_field');
                            ?>
                            <button type="submit" class="mui-btn hera-btn">Cập nhật</button>
                        </form>
                    </div>
                    <div class="mui-col-lg-5 mui-col-md-12">
                    </div>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2">
        </div>
    </div>
</div>
<script>
    /* Check các thông tin bằng ajax */
    jQuery(document).ready(function($) {
        var check_password = true;
        var check_confirm_password = true;
        var error_message;
        var checkicon = '<i class="fa fa-check-circle" aria-hidden="true"></i>';
        var loading = '<img src="<?php echo get_template_directory_uri() ?>/img/heart-preloader.gif" alt="">';

        /* Dùng axjax kiểm tra password đã đúng chưa */
        $('input[name="user_oldpass"]').on('change', function() {
            var user_oldpass = $(this).val();

            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "checkPassword",
                    user_pass: user_oldpass,
                },
                beforeSend: function() {
                    $('input[name="user_oldpass"]').prop('disabled', true).parent().find('.form_check_icon').html(loading);
                    $('button[type="submit"]').prop('disabled', true);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function(resp) {
                    if (!resp) {
                        $('.pass_error_notif').html('');
                        $('input[name="user_oldpass"]').prop('disabled', false).parent().find('.form_check_icon').html(checkicon);
                        check_password = true;
                        if (check_confirm_password) {
                            $('button[type="submit"]').prop('disabled', false);
                        }
                    } else {
                        $('.pass_error_notif').html(resp);
                        $('input[name="user_oldpass"]').prop('disabled', false).parent().find('.form_check_icon').html('');
                        check_password = false;
                    }
                },
            });
        });

        function checkPassword(str) {
            if (str.length < 8) {
                error_message = "Mật khẩu không được ít hơn 8 ký tự";
                return false
            }
            if (str.length > 20) {
                error_message = "Mật khẩu không được quá 20 ký tự";
                return false
            }
            var re = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;
            error_message = re.test(str)?"":"Mật khẩu phải bao gồm cả chữ hoa, chữ thường và ký tự đặc biệt.";
            return re.test(str);
        }

        /* Dùng axjax kiểm tra mật khẩu mới và mật khẩu confirm có khớp nhau không */
        $('input.confirm_pass').on("keyup change", function() {
            var user_newpass = $('input[name="user_newpass"]').val();
            var confirm_pass = $('input[name="confirm_pass"]').val();

            if (user_newpass == confirm_pass) {
                check_confirm_password = true;
                if (checkPassword(user_newpass)) {
                    $('.confirm_error_notif').html('');
                    $(this).parent().find('.form_check_icon').html(checkicon);
                    if (check_password) {
                        $('button[type="submit"]').prop('disabled', false);
                    }
                } else {
                    check_phone = false;
                    $('.confirm_error_notif').html(error_message);
                    $('.confirm_error_notif').parent().find('.form_check_icon').html('');
                    $('button[type="submit"]').prop('disabled', true);
                }
            } else {
                check_phone = false;
                if (confirm_pass.length > 0) {
                    $('.confirm_error_notif').html('Mật khẩu không khớp');
                } else {
                    $('.confirm_error_notif').html('');
                }
                $('.confirm_error_notif').parent().find('.form_check_icon').html('');
                $('button[type="submit"]').prop('disabled', true);
            }
        });

        $('.see_pass').click(function(){
            let type = $('.password').attr('type');

            if(type == 'password'){
                $('.password').prop('type', 'text');
                $('.see_pass').html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
            } else {
                $('.password').prop('type', 'password');
                $('.see_pass').html('<i class="fa fa-eye" aria-hidden="true"></i>');
            }
        });
    });
</script>
<?php
get_footer();
