<?php
/* 
* Template Name: Sửa thông tin cá nhân
*/
$current_user = wp_get_current_user();
// print_r($current_user);
$user_login     = $current_user->user_login;
$display_name   = $current_user->display_name;
$user_email     = $current_user->user_email;

$phone = get_field('phone', 'user_' . $current_user->ID);
$address = get_field('address', 'user_' . $current_user->ID);

if (
    isset($_POST['post_nonce_field']) &&
    wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
) {
    $user_login     = strip_tags($_POST['user_login']);
    $display_name   = strip_tags($_POST['display_name']);
    $user_email     = strip_tags($_POST['user_email']);
    $user_phone     = strip_tags($_POST['user_phone']);
    $user_address   = strip_tags($_POST['user_address']);
    $usr_args       = [
        'ID'    => $current_user->ID
    ];

    if ($user_login != $current_user->user_login) {
        global $wpdb;
        $wpdb->update($wpdb->users, array('user_login' => $user_login), array('ID' => $current_user->ID));
        $usr_args['user_nicename'] = $user_login;
    }

    if ($display_name != $current_user->display_name) {
        $usr_args['display_name'] = $display_name;
    }

    if ($user_email != $current_user->user_email) {
        $usr_args['user_email'] = $user_email;
    }

    // print_r($usr_args);
    // $usr_args = [
    //     'user_email' => $customer_email,
    //     'display_name' => $customer_name,
    // ];
    $updated = wp_update_user($usr_args);
    if ($user_phone != $phone) {
        update_field('field_62ee62714e989', $user_phone, 'user_' . $current_user->ID);
        $phone = $user_phone;
    }
    if ($user_address != $address) {
        update_field('field_62ee62714e963', $user_address, 'user_' . $current_user->ID);
        $address = $user_address;
    }

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
            <div class="mui-panel">
                <div class="back-btn mb20">
                    <a href="javascript:history.back()"><i class="fa fa-arrow-left"></i> Quay lại</a>
                </div>
                <h3 class="title_general">Sửa thông tin tài khoản</h3>
                <div class="mui-row">
                    <div class="mui-col-lg-7 mui-col-md-12">
                        <form class="mui-form" method="POST" name="guest_form" id="hera_form">
                            <div class="mui-textfield">
                                <input type="text" name="user_login" id="user_login" data-userlogin="<?php echo $user_login; ?>" value="<?php echo $user_login; ?>" required>
                                <label for="user_login">Tên đăng nhập <span class='required'>*</span></label>
                                <span class="form_check_icon"></span>
                                <span class="user_error_notif error"></span>
                            </div>
                            <div class="mui-textfield">
                                <input type="text" name="display_name" id="display_name" value="<?php echo $display_name; ?>">
                                <label for="display_name">Họ và tên đầy đủ của bạn</label>
                            </div>
                            <div class="mui-textfield">
                                <input type="text" name="user_email" id="user_email" data-email="<?php echo $user_email; ?>" value="<?php echo $user_email; ?>" required>
                                <label for="user_email">Email của bạn <span class='required'>*</span></label>
                                <span class="form_check_icon"></span>
                                <span class="email_error_notif error"></span>
                            </div>
                            <div class="mui-textfield">
                                <input type="text" name="user_phone" id="user_phone" data-phone="<?php echo $phone; ?>" value="<?php echo $phone; ?>">
                                <label for="user_phone">Số điện thoại của bạn</label>
                                <span class="form_check_icon"></span>
                                <span class="phone_error_notif error"></span>
                            </div>
                            <div class="mui-textfield">
                                <input type="text" name="user_address" id="user_address" value="<?php echo $address; ?>">
                                <label for="user_address">Địa chỉ</label>
                            </div>
                            <?php
                            wp_nonce_field('post_nonce', 'post_nonce_field');
                            ?>
                            <button type="submit" class="mui-btn hera-btn">Cập nhật</button>
                            <!-- <a class="hera-link" href="javascript:history.back()">Quay lại</a> -->

                        </form>
                    </div>
                    <div class="mui-col-lg-5 mui-col-md-12">
                        <?php
                        /* wp_nav_menu(array(
                            'menu'      => "User Function Menu",
                            'container' => '',
                            'menu_class' => 'user_function'
                        )); */
                        ?>
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
        var check_username = true;
        var check_email = true;
        var check_phone = true;
        var checkicon = '<i class="fa fa-check-circle" aria-hidden="true"></i>';
        var loading = '<img src="<?php echo get_template_directory_uri() ?>/img/heart-preloader.gif" alt="">';

        /* Dùng axjax kiểm tra user đã sử dụng chưa */
        $('input[name="user_login"]').change(function() {
            var newusername = $(this).val();
            var oldusername = $(this).data('userlogin')
            newusername = newusername.replace(/[^a-zA-Z.0-9]+/g, '');
            $(this).val(newusername);

            if (newusername != oldusername) {
                $.ajax({
                    type: "POST",
                    url: AJAX.ajax_url,
                    data: {
                        action: "checkUsernameExist",
                        user_login: $(this).val(),
                    },
                    beforeSend: function() {
                        $('input[name="user_login"]').prop('disabled', true).parent().find('.form_check_icon').html(loading);
                        $('button[type="submit"]').prop('disabled', true);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    },
                    success: function(resp) {
                        if (!resp) {
                            $('.user_error_notif').html('');
                            $('input[name="user_login"]').prop('disabled', false).parent().find('.form_check_icon').html(checkicon);
                            check_username = true;
                            if (check_email && check_phone) {
                                $('button[type="submit"]').prop('disabled', false);
                            }
                        } else {
                            $('.user_error_notif').html(resp);
                            $('input[name="user_login"]').prop('disabled', false).parent().find('.form_check_icon').html('');
                            check_username = false;
                        }
                    },
                });
            } else {
                $('.user_error_notif').html('');
                $(this).parent().find('.form_check_icon').html('');
                if (check_email && check_phone) {
                    $('button[type="submit"]').prop('disabled', false);
                }
            }
        });

        /* Dùng axjax kiểm tra email đã sử dụng chưa */
        $('input[name="user_email"]').change(function() {
            var newemail = $(this).val();
            var oldemail = $(this).data('email');
            newemail = newemail.replace(/[^a-zA-Z0-9_/-@.]/g, '');
            $(this).val(newemail);

            if (oldemail != newemail) {
                $.ajax({
                    type: "POST",
                    url: AJAX.ajax_url,
                    data: {
                        action: "checkEmailExist",
                        user_email: $(this).val(),
                    },
                    beforeSend: function() {
                        $('input[name="user_email"]').prop('disabled', true).parent().find('.form_check_icon').html(loading);
                        $('button[type="submit"]').prop('disabled', true);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    },
                    success: function(resp) {
                        if (!resp) {
                            $('.email_error_notif').html('');
                            $('input[name="user_email"]').prop('disabled', false).parent().find('.form_check_icon').html(checkicon);
                            check_email = true;
                            if (check_username && check_phone) {
                                $('button[type="submit"]').prop('disabled', false);
                            }
                        } else {
                            $('.email_error_notif').html(resp);
                            $('input[name="user_email"]').prop('disabled', false).parent().find('.form_check_icon').html('');
                            check_email = false;
                        }
                    },
                });
            } else {
                $('.email_error_notif').html('');
                $(this).parent().find('.form_check_icon').html('');
                if (check_username && check_phone) {
                    $('button[type="submit"]').prop('disabled', false);
                }
            }
        });

        /* Dùng axjax kiểm tra email đã sử dụng chưa */
        $('input[name="user_phone"]').change(function() {
            var user_phone = $(this).val();
            var oldphone = $(this).data('phone');
            newphone = user_phone.replace(/[^0-9]/g, '').substring(0, 10);
            $(this).val(newphone);

            if (oldphone != newphone) {
                if (newphone.length == 10) {
                    check_phone = true;
                    $('.phone_error_notif').html('');
                    $(this).parent().find('.form_check_icon').html(checkicon);
                    if (check_username && check_email) {
                        $('button[type="submit"]').prop('disabled', false);
                    }
                } else {
                    check_phone = false;
                    $('.phone_error_notif').html('Số điện thoại không hợp lệ');
                    $(this).parent().find('.form_check_icon').html('');
                    $('button[type="submit"]').prop('disabled', true);
                }
            } else {
                check_phone = true;
                $('.phone_error_notif').html('');
                $(this).parent().find('.form_check_icon').html('');
                if (check_username && check_email) {
                    $('button[type="submit"]').prop('disabled', false);
                }
            }
        });
    });
</script>
<?php
get_footer();
