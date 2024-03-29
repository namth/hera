function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

jQuery(document).ready(function ($) {

    // $('.checkout_input input[type="number"]').change(function() {
    //     var value = $(this).val();
    //     var price = $(this).data('price');
    //     var formatter = new Intl.NumberFormat('vi', {
    //         style: 'currency',
    //         currency: 'VND',
    //     });
          
    //     $(this).parents(2).children('.total').html(formatter.format(value * price));

    //     /* Tính tổng tiền */
    //     var normal_card = $('input[name="normal_card_qtt"]');
    //     var vip_card = $('input[name="vip_card_qtt"]');
    //     var coupon = $('input[name="coupon_value"]').val();
    //     var coupon_type = $('input[name="coupon_type"]').val();

    //     var sub_total = normal_card.val() * normal_card.data('price') + vip_card.val() * vip_card.data('price');
    //     var final_total;

    //     if (coupon_type == 'percent') {
    //         final_total = sub_total * (100 - coupon) / 100    
    //     } else {
    //         if (coupon >= sub_total) {
    //             final_total = 0;
    //         } else {
    //             final_total = sub_total - coupon;
    //         }
    //     }

    //     $('input[name="sub_total"]').val(sub_total);
    //     $('.sub_total').html(formatter.format(sub_total));
    //     $('.final_total').html(formatter.format(final_total));
        
    //     return false;
    // });

    /* Mở form nhập mã coupon khi click vào link */
    $('.coupon_link').click(function(){
        $('.coupon_form').toggleClass("flexbox");
        return false;
    });

    /* Khi bấm submit một form thì gọi ajax để xử lý form add coupon, thêm dữ liệu vào  */
    $('.coupon_form button').click(function () {
        // alert('alo');
        var data = $('input[name="coupon_code"]').val();
        var package = $('input[name="package"]').val();
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "addCouponCode",
                data: data,
                package: package,
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
                /* gắn thông báo thành công hoặc thất bại */
                firstDiv = '<div id="notification">';
                endDiv = '</div>';
                $('.coupon_notification').append(firstDiv + obj['message'] + endDiv).show(200);
                setTimeout(function(){
                    if ($('#notification').length > 0) {
                    $('#notification').remove();
                    }
                }, 10000);

                /* Nếu thành công thì thay đổi một số tham số */
                if (obj['status']) {
                    /* Ẩn form nhập coupon và xoá format của tổng phụ */
                    $('.coupon_form').toggleClass("flexbox");
                    $('.package_box .price').addClass('has_coupon');
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
                    $('.package_box .final_price').html(formatter.format(obj['final_total'])).show();
                }
            },
        });
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