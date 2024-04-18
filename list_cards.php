<?php
/* 
    Template Name: List Card from API
*/
get_header();
// get_template_part('header', 'top-nologin');

$current_user_id = get_current_user_id();

if (isset($_POST['search'])) {
    $search = strip_tags($_POST['search']);
} else $search = "";

if (isset($_GET['g']) && ($_GET['g'] != "")) {
    $data = json_decode(inova_encrypt($_GET['g'], 'd'));
} else {
    $data = false;
}

# hiển thị header khác nếu chưa đăng đăng nhập
if (is_user_logged_in()) {
    $back_link = get_bloginfo('url') . '/main';
    get_header('topbar');
} else {
    $back_link = '';
    get_header('logocenter');
}
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-12" id="search_box">
            <?php 
                if ($back_link) {
                    echo '<div class="back-btn mt20">
                            <a href="' . $back_link . '"><i class="fa fa-arrow-left"></i> Trang chủ </a>
                        </div>';
                }
            ?>
            <h1>Mẫu thiệp cưới cho mọi người</h1>
            <h4>Hàng trăm mẫu thiệp mới nhất sẽ được cập nhật tại đây.</h4>
            <!-- <form class="mui-form--inline" method="POST">
                <div class="mui-textfield search_bar">
                    <input type="text" name="search" placeholder="Tìm kiếm tất cả mẫu thiệp tại đây" value="<?php echo $search; ?>">
                    <button class=""><i class="fa fa-search"></i></button>
                </div>
            </form> -->
            <div class="hera_tab left">
                <span class="active" data-cat="1">Thiệp mời dự tiệc</span>
                <span data-cat="4">Thiệp mời đầy đủ</span>
            </div>
        </div>
        <div class="mui-col-md-12">
            <?php 
                if (isset($data->userid)) {
                    echo '<input type="hidden" name="groupid" value="' . $data->groupid  . '">';
                }
            ?>
            <div class="mui-panel">
                <div class="heracard_list mui-row">
                    <span class="loader">
                        <dotlottie-player src="<?php echo get_template_directory_uri() . '/img/flowerloading.json'; ?>" 
                                background="transparent" speed="1" 
                                style="width: 300px; height: 300px" direction="1" playMode="bounce" loop autoplay style="margin: 0 auto;">
                        </dotlottie-player>
                        Đang tải ...
                    
                    </span>
                    <div class="cards"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mui-container-fluid" id="detail_card" style="display: none;">
    <dotlottie-player 
        class="loading"
        src="<?php echo get_template_directory_uri() . '/img/flowerloading.json'; ?>" 
        background="transparent" speed="1" 
        style="width: 500px; height: 500px" direction="1" playMode="bounce" loop autoplay style="margin: 0 auto;">
    </dotlottie-player>
</div>
<script>
    jQuery(document).ready(function ($) {
        $.ajax({
            type: "POST",
            url: AJAX.ajax_url,
            data: {
                action: "listCardFromAPI",
            },
            beforeSend: function() {
                $('.heracard_list .loader').css('opacity', 1);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function (resp) {
                $('.heracard_list .loader').hide();
                $('.heracard_list .cards').html(resp);
            },
        });

        $(document.body).on('click', '.hera_tab span', function(event){
            // event.preventDefault();

            var cat = $(this).data('cat');
            $('.hera_tab span').removeClass("active");
            $(this).addClass("active");
            $('.hera_tab').toggleClass("left").toggleClass("right");

            console.log(cat);

            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "listCardFromAPI",
                    cat: cat
                },
                beforeSend: function() {
                    $('.heracard_list .loader').show();
                    $('.heracard_list .cards').html('');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    $('.heracard_list .loader').hide();
                    $('.heracard_list .cards').html(resp);
                },
            });

            return false
        });

        $(document.body).on('click', '.error_messages #reload_card', function(){
            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "listCardFromAPI",
                    retoken: '1',
                },
                beforeSend: function() {
                    $('.error_messages #reload_card').hide();
                    $('.error_messages span').hide();
                    $('.error_messages img').show();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    $('.heracard_list').html(resp);
                },
            });
        });
    });
</script>
<?php
get_footer();
?>