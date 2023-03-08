<?php
/* 
* Template Name: Home
*/
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
        <div id="section1">
            <div class="left_content">
                <div>
                    <h1>Thiệp cưới online thông minh</h1>
                    <h2>Giải pháp mới cho một đám cưới hiện đại.</h2>
                    <p>Chúng tôi còn gọi đó là thiệp cưới thông minh HERA. Bạn có thể lập kế hoạch mời cưới, chia nhóm khách mời, tạo thiệp cưới cho từng khách hàng chỉ bằng vài nút bấm chuột, có khả năng nhận được thông tin phản hồi của khách để có thể điều chỉnh cỗ cưới hợp lý hơn.</p>
                    <a href="" class="mui-btn hera-btn">Xem chi tiết</a>
                </div>
            </div>
            <div class="right_content">
                <img src="<?php echo get_template_directory_uri() . '/img/wedding_cards.png'; ?>" alt="">
            </div>
        </div>
        <div id="section3" class="mui-col-md-10 mui-col-md-offset-1">
            <div class="center_title">
                <img src="<?php echo get_template_directory_uri(); ?>/img/Rose-logo.svg" alt="">
                <h2>Thay đổi hoàn toàn phương thức mời cưới cũ</h2>
                <p>Loại bỏ những bất cập hiện có từ phương thức mời cưới cũ</p>
            </div>
            <div class="mui-row right_detail">
                <div class="mui-col-md-6">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/02.png" alt="">
                </div>
                <div class="mui-col-md-6">
                    <p class="description">Bạn bận rộn, mời cưới bạn qua điện thoại và chụp ảnh thiệp cứng và gửi qua zalo cho bạn</p>
                    <ul>
                        <li>
                            
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="section2" class="mui-col-md-10 mui-col-md-offset-1">
            <div class="center_title">
                <h2>Giải pháp công nghệ áp dụng vào thiệp cưới thông minh</h2>
                <p>Thay thế phương thức mời cưới cũ, giải quyết hoàn hảo những vấn đề của việc gửi thiệp cứng, 
                    <br>trải nghiệm những lợi ích bất ngờ từ việc ứng dụng công nghệ vào thiệp cưới</p>
            </div>
            <div class="listbox mui-row">
                <div class="iconbox mui-col-md-6 mui-col-lg-3">
                    <div class="icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/flower.svg" alt="">
                    </div>
                    <h3>Tích hợp google maps vào thiệp cưới</h3>
                    <p>Chắc bạn cũng đã từng loay hoay tìm đường đến địa điểm đám cưới? Nếu vị trí ở xa trung tâm thì việc tìm kiếm cũng khó khăn.</p>
                </div>
                <div class="iconbox mui-col-md-6 mui-col-lg-3">
                    <div class="icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/Rose-logo-2.svg" alt="">
                    </div>
                    <h3>Biết được bạn có thể tham gia được hay không</h3>
                    <p>Khi bạn mời khách, bạn không biết được họ có tham dự được hay không, dẫn tới việc khó khăn trong việc chuẩn bị cỗ</p>
                </div>
                <div class="iconbox mui-col-md-6 mui-col-lg-3">
                    <div class="icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/flower-4.svg" alt="">
                    </div>
                    <h3>Sử dụng không giới hạn mẫu thiệp</h3>
                    <p>Bạn phải dùng chung một mẫu thiệp để mời toàn bộ khách mời của bố mẹ bạn và của bạn, thậm chí nhà trai và nhà gái phải thống nhất in chung thiệp</p>
                </div>
                <div class="iconbox mui-col-md-6 mui-col-lg-3">
                    <div class="icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/Rose-logo.svg" alt="">
                    </div>
                    <h3>Khó khăn trong việc gửi tiền mừng</h3>
                    <p>Nếu khách mời không tham dự được, sẽ muốn hỏi bạn tài khoản ngân hàng để mừng. Không gửi thì khách không thể mừng cưới bạn, nhưng nếu gửi thì vừa ngại vừa mất thời gian</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    }
}
get_footer();