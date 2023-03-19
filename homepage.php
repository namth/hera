<?php
/* 
* Template Name: Home
*/

/* count user */
$number_of_user = count_users();

get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
?>
<div id="header_topbar" class="mui-row">
    <div class="mui-col-md-10 mui-col-md-offset-1">
    <?php 
        wp_nav_menu(array(
            'menu'      => 6,
            'container' => '',
        ));
    ?>
    </div>
</div>
<div class="mui-panel mui-row" id="header">
    <div id="center_logo" class="mui-col-md-10 mui-col-md-offset-1">
        <a href="<?php echo get_bloginfo('url'); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo_hera.png" alt="">
        </a>
    </div>
    <div id="header_bottom" class="mui-col-md-10 mui-col-md-offset-1">
        <?php 
            wp_nav_menu(array(
                'menu'      => 6,
            ));
        ?>
    </div>
</div>
<div id="content" class="mui-row">
    <div class="mui-col-md-12">
        <div id="section1" class="mui-col-md-10 mui-col-md-offset-1">
            <div class="mui-col-md-5">
                <div>
                    <h1>Thiệp cưới online thông minh</h1>
                    <h2>Giải pháp mới cho một đám cưới hiện đại.</h2>
                    <p>Chúng tôi còn gọi đó là thiệp cưới thông minh HERA. Bạn có thể lập kế hoạch mời cưới, chia nhóm khách mời, tạo thiệp cưới cho từng khách hàng chỉ bằng vài nút bấm chuột, có khả năng nhận được thông tin phản hồi của khách để có thể điều chỉnh cỗ cưới hợp lý hơn.</p>
                    <a href="<?php echo get_bloginfo('url') . "/login/"; ?>" class="mui-btn hera-btn">Thử ngay</a>
                </div>
            </div>
            <div class="mui-col-md-7">
                <img src="<?php echo get_template_directory_uri() . '/img/wedding_cards.png'; ?>" alt="">
            </div>
        </div>
        <div id="section3" class="mui-col-md-10 mui-col-md-offset-1">
            <div class="center_title">
                <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/Rose-logo.svg" alt="">
                <h2>Lập kế hoạch mời cưới thông minh hơn</h2>
                <p>Với nhiều lợi ích vượt trội hơn hẳn so với cách mời cưới hiện tại</p>
            </div>
            <div class="right_detail">
                <div class="mui-row">
                    <div class="mui-col-md-4">
                        <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/wedding_card.png" alt="">
                    </div>
                    <div class="mui-col-md-4">
                        <div class="iconbox">
                            <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/card.png" alt="">
                            <h3>Cá nhân hoá cho từng khách mời</h3>
                            <p>Mỗi người sẽ có một thiệp riêng, thể hiện sự tôn trọng của bạn với khách mời. Nhiều vị khách khó tính sẽ không thích mình chụp thiệp gửi cho họ.</p>
                        </div>
                    </div>
                    <div class="mui-col-md-4">
                        <div class="iconbox">
                            <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/guest_accept.png" alt="">
                            <h3>Kiểm soát số lượng khách mời</h3>
                            <p>Khách mời có thể bấm vào nút xác nhận tham gia hoặc từ chối ngay trên thiệp, nhờ đó bạn có thể chuẩn bị cỗ mời khách hợp lý hơn</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="section2" class="mui-col-md-12">
            <div class="center_title mt20">
                <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/flower.svg" alt="">
                <h2>Tính năng chỉ đường qua Google Maps</h2>
                <p>Không cần vẽ sơ đồ, thiệp mời có tích hợp Google Maps, giúp khách của bạn tìm đến địa điểm chính xác nhất.</p>
            </div>
            <div class="mui-row">
                <div class="mui-col-md-12">
                    <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/hra_maps.png" alt="">
                </div>
            </div>
        </div>
        <div id="section4" class="mui-col-md-12">
            <div class="center_title mt20">
                <!-- <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/Rose-logo-2.svg" alt=""> -->
                <h2>Những con số đáng tự hào</h2> 
            </div>
            <div class="mui-container">
                <div class="mui-row">
                    <div class="mui-col-md-4">
                        <div class="numberbox">
                            <div class="number">200+</div>
                            <div class="text">Mẫu thiệp cưới</div>
                        </div>
                    </div>
                    <div class="mui-col-md-4">
                        <div class="numberbox">
                            <div class="number"><?php echo $number_of_user['total_users']; ?>+</div>
                            <div class="text">Người sử dụng</div>
                        </div>
                    </div>
                    <div class="mui-col-md-4">
                        <div class="numberbox">
                            <div class="number"><?php echo $number_of_user['avail_roles']['contributor']; ?>+</div>
                            <div class="text">Đối tác</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="section5" class="mui-col-md-12">
            <div class="center_title mt20">
                <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/Rose-logo-2.svg" alt="">
                <h2>Liên hệ</h2> 
            </div>
            <div class="mui-container">
                <div class="mui-row">
                    <div class="mui-col-md-6">

                    </div>
                    <div class="mui-col-md-6">
                        <img class="m_auto" src="<?php echo get_template_directory_uri(); ?>/img/02.png" alt="">
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php
    }
}
get_footer();