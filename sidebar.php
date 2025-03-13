<div id="hera_sidebar">
    <img src="<?php echo get_template_directory_uri(); ?>/img/top.png" alt="">
    <ul class="main_menu mb20">
        <li><i class="ph ph-house-line"></i> <a href="<?php echo home_url('/main'); ?>">Trang chủ</a></li>
        <li><i class="ph ph-heart"></i> <a href="<?php echo home_url('/wedding-infomation'); ?>">Thông tin đám cưới</a></li>
        <li><i class="ph ph-swatches"></i> <a href="<?php echo home_url('/danh-sach-mau'); ?>">Danh sách mẫu</a></li>
        <li><i class="ph ph-storefront"></i> <a href="<?php echo home_url('/danh-sach-goi-san-pham'); ?>">Mua thiệp</a></li>
    </ul>
    <h5 class="title_menu">Cài đặt</h5>
    <?php 
        $current_user_id = get_current_user_id();
    ?>
    <ul class="main_menu mb20">
        <li><i class="ph ph-newspaper-clipping"></i> <a href="<?php echo home_url('/danh-sach-don-hang'); ?>">Đơn hàng</a></li>
        <li><i class="ph ph-user"></i> <a href="<?php echo get_author_posts_url($current_user_id); ?>">Tài khoản của tôi</a></li>
        <li><i class="ph ph-command"></i> <a href="<?php echo home_url('/huong-dan-su-dung'); ?>">Hướng dẫn sử dụng</a></li>
        <li><i class="ph ph-sign-out"></i> <a href="<?php echo wp_logout_url(home_url()); ?>">Đăng xuất</a></li>
    </ul>
</div>