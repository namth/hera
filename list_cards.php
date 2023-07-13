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

if (is_user_logged_in()) {
    $back_link = get_bloginfo('url');
    get_header('topbar');
} else {
    // $back_link = 'https://thiepcuoi.hra.vn';
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
        </div>
        <div class="mui-col-md-12">
            <?php 
                if (isset($data->userid) && ($data->userid == $current_user_id)) {
                    echo '<input type="hidden" name="groupid" value="' . $data->groupid  . '">';
                }
            ?>
            <div class="mui-panel">
                <div class="heracard_list mui-row">
                    <span class="loader"><img src="<?php echo get_template_directory_uri() . '/img/flower_loading.gif'; ?>" alt="">Đang tải ...</span>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mui-container-fluid" id="detail_card" style="display: none;">
    <img src="<?php echo get_template_directory_uri() . '/img/flower_puzzles_preloader.gif'; ?>" style="margin: 0 auto;">
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
                $('.heracard_list').html(resp);
            },
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