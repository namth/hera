jQuery(document).ready(function ($) {
    // Gọi lệnh đồng bộ tới casso
    $("#check_payment .active_now").click(function(){
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "syncCasso",
            },
            beforeSend: function() {
                $("#fullloading").css('display','flex');
                $("#fullloading .description").html('<span class="blink_me">Đang đồng bộ hoá với ngân hàng ...</span>');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                // var obj = JSON.parse(resp);

            },
        });

        // Cài đặt bộ hẹn giờ kiểm tra order 
        setInterval(checkOrder, 2000);
        return false;
    });

    function checkOrder(){
        var order_id = $('input[name="order_id"]').val();
        // Gọi ajax để kiểm tra hoá đơn cho tới khi được kích hoạt
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "checkOrder",
                order: order_id
            },
            beforeSend: function() {
                $("#fullloading .description").html('<span class="blink_me">Đang kiểm tra giao dịch</span>');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                console.log(resp);
                var obj = JSON.parse(resp);
                if (obj['done'] == true) {
                    clearAllInterval();

                    // redirect to thank you page 
                    window.location.replace(obj['url']);
                }
            },
        });
    }

    // Xử lý khi bấm vào nút close trên màn hình
    $("#fullloading .close_button").click(function(){
        /* Ẩn loading */
        $("#fullloading").css('display','none');
        
        /* Xoá check */
        clearAllInterval();
    });


    /* Close momo popup */
    $(".popup_momo").on('click', function(e){
        if (e.target !== this)
            return;

        $(this).remove();
    });
    $(".close_popup").on('click', function(e){
        $(".popup_momo").remove();
    });

    /* Xử lý khi bấm vào nút kích hoạt ngay */
    $(".active_free").click(function(e){
        e.preventDefault();
        var active_data = $(this).attr('href');
        console.log(active_data);
        // Gọi ajax để kiểm tra hoá đơn cho tới khi được kích hoạt
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "activeOrder",
                active_data: active_data
            },
            beforeSend: function() {
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                window.location.replace(resp);
            },
        });
    });
});