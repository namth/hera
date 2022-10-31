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
            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "checkUsernameExist",
                    user_login: $(this).val(),
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
                        $('.form_check_icon').html('');
                        $('.user_error_notif').html(resp);
                        $('input[name="user_login"]').prop('disabled', false).parent().find('.form_check_icon').html();
                        check_username = false;
                    }
                },
            });
        });

        /* Dùng axjax kiểm tra email đã sử dụng chưa */
        $('input[name="user_email"]').change(function(){
            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "checkEmailExist",
                    user_email: $(this).val(),
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
                        $('.form_check_icon').html('');
                        $('.email_error_notif').html(resp);
                        $('input[name="user_email"]').prop('disabled', false).parent().find('.form_check_icon').html();
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

            if ((user_pass.length < 8) || (user_pass.length > 15)) {
                $('span.password_error_notif').html('Mật khẩu phải từ 8 đến 15 ký tự');
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
                        console.log(resp);
                    },
                });
            }
        });
    });