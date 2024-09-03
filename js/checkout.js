function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}


jQuery(document).ready(function ($) {
    /* add coupon ajax function */
    function addCouponCode(coupon_code, package, new_cards=0, isMessage=false) {
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "addCouponCode",
                coupon: coupon_code,
                package: package,
                cards: new_cards,
            },
            beforeSend: function () {
                // $('.coupon_form').hide(200);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                var obj = JSON.parse(resp);
                console.log(obj);
                if (isMessage) {
                    /* gắn thông báo thành công hoặc thất bại */
                    firstDiv = '<div id="notification">';
                    endDiv = '</div>';
                    $('.coupon_notification').append(firstDiv + obj['message'] + endDiv).show(200);
                    setTimeout(function(){
                        if ($('#notification').length > 0) {
                        $('#notification').remove();
                        }
                    }, 10000);

                    $('.coupon_form').toggleClass("flexbox");
                    $('.package_box .price').addClass('has_coupon');
                }
                
                /* Nếu thành công thì thay đổi một số tham số */
                if (obj['status']) {
                    /* Ẩn form nhập coupon và xoá format của tổng phụ */
                    /* Hiển thị giá trị coupon */
                    $('.view_coupon .value').html(obj['coupon']);
                    $('.view_coupon .coupon_name').html(obj['coupon_label']);
                    $('.view_coupon').show(200);
                    /* Truyền dữ liệu đã mã hoá vào input để gửi đi */
                    $('input[name="coupon"]').val(obj['hash']);
                    /* Hiện tổng tiền sau giảm giá */
                    var formatter = new Intl.NumberFormat('vi', {
                        style: 'currency',
                        currency: 'VND',
                    });
                    $('.package_box .price').html(formatter.format(obj['sub_total'])).show();
                    $('.package_box .final_price').html(formatter.format(obj['final_total'])).show();
                }
            },
        });
    }

    /* increase or decrease when click button in class .inova_number_input */
    $('.inova_number_input .btn-number').click(function () {
        var plus = $(this).data('plus');
        var cards = $('.inova_number_input #invite_cards').val();
        var price = $('input[name="price"]').val();
        var new_cards = parseInt(cards) + plus;
        var formatter = new Intl.NumberFormat('vi', {
            style: 'currency',
            currency: 'VND',
        });
        var total = new_cards * price;

        if (new_cards > 0) {
            $('.inova_number_input #invite_cards').val(new_cards);
        } else return false;

        /* read coupon code if have, and then add coupon to this */
        var coupon_code = $('input[name="coupon_code"]').val();
        // console.log(coupon_code);
        if (coupon_code != '') {
            addCouponCode(coupon_code, 0, new_cards);
        } else {
            $('.package_box .final_price').html(formatter.format(total));
        }
        return false;
    });

    /* Validate input in the number input '.inova_number_input #invite_cards', only allowing numeric input. */
    $('.inova_number_input #invite_cards').keypress(function (e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    /* Mở form nhập mã coupon khi click vào link */
    $('.coupon_link').click(function(){
        $('.coupon_form').toggleClass("flexbox");
        return false;
    });

    /* Khi bấm submit một form thì gọi ajax để xử lý form add coupon, thêm dữ liệu vào  */
    $('.coupon_form button').click(function () {
        var coupon_code = $('input[name="coupon_code"]').val();
        var package = $('input[name="package"]').val();
        var number_cards = $('.inova_number_input #invite_cards').val();

        addCouponCode(coupon_code, package, number_cards, true);
        return false;
    });

    /* Xử lý khi click vào nút submit */
    $('form#confirm_order').submit(function () {
        // Check validate data
        var customer_name = $("input[name='customer_name']").val();
        var customer_phone = $("input[name='customer_phone']").val();
        var customer_email = $("input[name='customer_email']").val();
        var customer_address = $("input[name='customer_address']").val();
        var notification = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Hãy điền đầy đủ thông tin của bạn.';
        var valid = false;
        
        if (customer_address && customer_email && customer_phone && customer_name) {
            if (isEmail(customer_email)) {
                var data = $(this).serialize();
                $.ajax({
                    type: "POST",
                    url: AJAX.ajax_url,
                    data: {
                        action: "createInvoice",
                        data: data,
                    },
                    beforeSend: function () {
        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    },
                    success: function (resp) {
                        // console.log(resp);
                        // $('#checkout').append(resp);
                        window.location.replace(resp);
                    }
                });
                valid = true;            
            } else {
                notification = "Email của bạn không đúng, hãy kiểm tra lại.";
                valid = false;
            }
        } else {
            valid = false;
        }

        if (!valid) {
            $("#notificate").html(notification).show();
            setTimeout(function(){
                if ($('#notificate').length > 0) {
                    $('#notificate').hide();
                }
            }, 8000);
        }
        return false;
    });
});