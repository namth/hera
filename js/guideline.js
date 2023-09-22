jQuery(document).ready(function ($) {

    /* Nếu cookie đang ở trạng thái min thì set class min cho guideline */
    var guide_state_min = getCookie('state_min');
    if (guide_state_min) {
        $(".minimize").hide();
        $(".minimize").parent().addClass("guide_section_minimize");
        $(".maximize").show();
    }

    $("#guide_section img").on("click", function () {
        var width = $(this).width();

        if (width < 80) {
            $(this).parent().find("ul").toggle(200);
        }
    });

    $(".minimize").on("click", function () {
        $(this).hide();
        $(this).parent().addClass("guide_section_minimize");
        $(".maximize").show();

        setCookie('state_min', true, 30);
    });

    $(".maximize").on("click", function () {
        $(this).hide();
        $(this).parent().removeClass("guide_section_minimize");
        $(".minimize").show();
        $(this).parent().find(".playlist").show();

        setCookie('state_min', false, 30);
    });
});
