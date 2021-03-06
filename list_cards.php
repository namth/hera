<?php
/* 
    Template Name: List Card from API
*/
get_header();
get_template_part('header', 'topbar');

$current_user_id = get_current_user_id();

if (isset($_POST['search'])) {
    $search = strip_tags($_POST['search']);
} else $search = "";

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-12" id="search_box">
            <div class="back-btn">
                <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-arrow-left"></i> Trang chủ </a>
            </div>
            <h1>Mẫu thiệp cưới cho mọi người</h1>
            <h4>Hàng trăm mẫu thiệp mới nhất được cập nhật tại đây.</h4>
            <form class="mui-form--inline" method="POST">
                <div class="mui-textfield search_bar">
                    <input type="text" name="search" placeholder="Tìm kiếm tất cả mẫu thiệp tại đây" value="<?php echo $search; ?>">
                    <button class=""><i class="fa fa-search"></i></button>
                </div>
            </form>
        </div>
        <div class="mui-col-md-12">
            <div class="mui-panel">
                <div class="heracard_list mui-row">
                    <?php
                    $api = 'https://design.inova.ltd/api-list-cards/';
                    $content_json = file_get_contents($api);

                    $content_array = json_decode($content_json);

                    // print_r($content_array);

                    foreach ($content_array as $card) {

                        if ($card->thumbnail) {
                            $card_thumbnail = $card->thumbnail;
                        } else {
                            $card_thumbnail = get_template_directory_uri() . '/img/no-img.png';
                        }
                    ?>
                    <div class="mui-col-md-3">
                        <div class="heracard">
                            <div class="images" style="<?php 
                                echo 'background: url('. $card_thumbnail .') no-repeat 50% 50%;';
                                echo 'background-size: contain;';
                            ?>">
                                
                            </div>
                            <div class="caption">
                                <div class="user_action">
                                    <a href="#"><i class="fa fa-heart"></i><span>Thích</span></a>
                                    <a href="#"><i class="fa fa-star"></i><span>Thêm vào danh sách yêu thích</span></a>
                                    <a href="#"><i class="fa fa-share-alt"></i><span>Chia sẻ</span></a>
                                </div>
                                <div class="caption_title mui-col-md-12">
                                    <span><?php echo $card->title; ?></span>
                                    <div class="like_share">
                                        <i class="fa fa-heart"> 892</i>
                                        <i class="fa fa-vcard-o"> 12</i>
                                    </div>
                                </div>
                                <a href="#detail_">
                                    <div class="bg-overlay"></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>