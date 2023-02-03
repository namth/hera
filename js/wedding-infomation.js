jQuery(document).ready(function ($) {
    /* Bấm vào mỗi section thì sẽ hiện form tương ứng và ẩn các form khác đi */
    $('.edit_section').click(function(){
        var form = $(this).data('form');
        var parent = $(this).parents().eq(1);
        // Ẩn tất cả các form hiện thời
        $('.has_data').show(200);
        $('.hide_form').hide(200);
        // Hiện tất cả các edit section và has_data lên
        $('.edit_section').show(200);
        // Ẩn div thêm dữ liệu, nếu là nút chỉnh sửa thì ẩn div chứa has_data
        if ($(this).find('.section_name').length > 0) {
            // Nếu ấn vào nút thêm mới
            $(this).hide();
        } else {
            // Nếu ấn vào nút chỉnh sửa
            // Ẩn dữ liệu cũ đi
            $(this).parents().eq(1).find('.group_data .has_data').hide();
        }
        
        // Hiện form chỉnh sửa hoặc thêm mới.
        $(form).show(200);
        return false;
    });

    /* Bấm vào nút close thì sẽ ẩn form và hiện lại các section */
    $('.close_button').click(function(){
        var action_id = $(this).parent().attr('id');
        var group_data = $(this).parents().eq(1);
        var has_data = group_data.find('.has_data');
        $(this).parent().hide(200);
        if (has_data.length > 0) {
            has_data.show(200);
        } else {
            group_data.find('.no_data').show(200);
        }
    });

    /* Khi bấm submit một form thì gọi ajax để xử lý form đó, thêm dữ liệu vào  */
    $('.hide_form button[type="submit"]').click(function () {
        var $data = $(this).parents().eq(1).serialize();
        // console.log($data);
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "addWeddingInfo",
                data: $data,
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                // console.log(resp);
                $('.hide_form').hide();
                $('#add_more_form').prepend('<span class="success_notification">Đã update thành công.</span>');
                location.reload();
                /* setTimeout(function(){
                    if ($('.success_notification').length > 0) {
                        $('.success_notification').remove(200);
                    }
                }, 4000) */
            },
        });
        return false;
    });

    /* Xử lý ajax khi sửa trực tiếp nội dung trên div */
    function edit_wedding_info(span_select){
        var field = span_select.data('field');
        var where = span_select.data('where');
        var content = span_select.text();
        var parent = span_select.parent();
        var heart_img = $('.heart_icon img').attr('src');
        // console.log(content);

        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "updateWeddingInfo",
                field: field,
                content: content,
                where: where,
            },
            beforeSend: function() {
                if (parent.find('i').length != 0) {
                    parent.find('i').hide();
                    parent.prepend('<b class="loading"><img src="' + heart_img + '" alt=""></b>').show();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                // console.log(resp);
                if (parent.find('i').length != 0) {
                    parent.find('.loading').remove();
                    parent.find('i').show();
                }
                span_select.data('update', false);
            },
        });
    }
    $('.diveditable').blur(function(){
        var update = $(this).data('update');
        console.log(update);
        if (update) {
            edit_wedding_info($(this));
        }
        return false;
    });

    $(document.body).on('keypress keyup paste input', '.diveditable', function(e){
        var update = $(this).data('update');
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            e.preventDefault();
            $(this).data('update', true);
            $(this).blur();
            return false;
        }
        if (keyCode === 27) {
            $(this).data('update', false);
            $(this).blur();
            return false;
        } else if (!update) {
            $(this).data('update', true);
        }
    });

    /* Khi click vào sửa ngày tháng thì sẽ hiện form sửa ngày dương lịch, sau đó tự động update vào ngày âm lịch */
    $('.date_data').click(function() {
        $(this).hide();
        $(this).parent().find('.date_input').show();
    });

    /* Xử lý khi click vào nút đóng button close-btn-mini */
    $('.close-btn-mini').click(function() {
        var date_div = $(this).parent().parent();
        date_div.hide();
        date_div.prev().show();
        return false;
    });

    // xử lý ajax khi click vào nút sửa thời gian
    $('.date_input form').submit(function () {
        // lấy dữ liệu từ form và mã hoá thành chuỗi
        var $data = $(this).serialize();
        var $divUpdate = $(this).parents(2);
        var $solarUpdate = $divUpdate.children('.date_data').children('.diveditable');
        var $lunarUpdate = $divUpdate.next().children('.diveditable');
        // console.log($data);
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "weddingDateInput",
                data: $data,
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                var obj = JSON.parse(resp);
                // console.log(obj);
                if (obj['status']) {
                    /* Hiện lại div ngày tháng, ẩn div nhập dữ liệu ngày tháng */
                    $divUpdate.find('.date_input').hide();
                    $divUpdate.find('.date_data').show(200);
                    /* Hiển thị nội dung mới */
                    $solarUpdate.html(obj["solarUpdate"]);
                    $lunarUpdate.html(obj["lunarUpdate"]);
                }
            },
        });
        return false;
    });

    /* Xử lý khi thay đổi input solar date và chuyển vào trường input ẩn */
    $('.date_calculate .solar').change(function(){
        var data_input = $(this).val();

        $(this).parent().find('.lunar').val(data_input);
    });
});