jQuery(document).ready(function ($) {

    /* Nếu cookie đang ở trạng thái min thì set class min cho guideline */
    var guide_state_min = getCookie('state_min');
    var width = $(window).width();

    /* Nếu đang ở trạng thái thu nhỏ và chiều rộng màn hình lớn hơn 991 thì hiển thị chế độ thu nhỏ */
    if (guide_state_min && (width >= 991)) {
        $(".minimize").hide();
        $(".minimize").parent().addClass("guide_section_minimize");
        $(".maximize").show();
    }

    /* Nếu đang ở chế độ thu nhỏ thì cho phép ẩn hiện menu bằng cách bấm vào ảnh */
    $("#guide_section img").on("click", function () {
        var width = $(this).width();
        if (width < 80) {
            $(this).parent().find("ul").toggle(200);
        }
    });

    /* Xử lý khi click vào link thu nhỏ */
    $(".minimize").on("click", function () {
        $(this).hide();
        $(this).parent().addClass("guide_section_minimize");
        $(".maximize").show();

        setCookie('state_min', true, 30);
    });

    /* Xử lý sự kiện khi click vào link phóng to */
    $(".maximize").on("click", function () {
        $(this).hide();
        $(this).parent().removeClass("guide_section_minimize");
        $(".minimize").show();
        $(this).parent().find(".playlist").show();

        setCookie('state_min', false, 30);
    });
});
