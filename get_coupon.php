<?php
/* 
* Template Name: Get Coupon (Partner)
*/
if (isset($_GET['code'])) {
    $code = explode('-', $_GET['code']);
    $coupon_id = $code[0];

    # verify coupon
    $owner_coupon = get_field('partner', $coupon_id);
    $partner_id = $code[1];
    
    if ($partner_id && ($owner_coupon == $partner_id)){
        # set partner id to cookie

        # lấy một số thông tin của coupon
        $coupon_name = get_field('coupon_name', $coupon_id);
        $coupon_type = get_field('coupon_type', $coupon_id);
        $coupon_value = get_field('coupon_value', $coupon_id);
        if ($coupon_id) {
            if ($coupon_type == "Phần trăm") {
                $coupon = number_format($coupon_value) . "%";
            } else {
                $coupon = number_format($coupon_value) . " ₫";
            }
        }
        
        # Lấy thông tin đối tác
        $partner_name = get_field('partner_name', 'user_'. $partner_id);

        # đếm số lượt truy cập coupon
        $number_of_click = get_field('number_of_clicks', 'user_' . $partner_id);
        if (is_numeric($partner_id)) {
            // tăng số đếm khi click vào link theo partner_id
            update_field('field_63eb41b276ba7', $number_of_click + 1, 'user_' . $partner_id);

            // lưu partner_id vào cookie 1 tháng cho đến khi user thanh toán 
            setcookie('partner', $partner_id, time() + 2592000, '/');
        }
        
        get_header();
        get_header('logocenter');
        if (have_posts()) {
            while (have_posts()) {
                the_post();
        
        ?>
        <div class="mui-container-fluid" style="background: white;">
            <div class="mui-row">
                <div class="mui-col-md-2"></div>
                    <div class="mui-col-md-8 mt20">
                        <h2>Voucher <?php echo $partner_name; ?> dành cho bạn</h2>
                        <div class="voucher">
                            <div class="logo_partner">
                                <img src="http://localhost/hera/wp-content/uploads/2023/07/Leewedding.png" alt="">
                            </div>
                            <div class="content_voucher">
                                <h3>Giảm giá <?php echo $coupon; ?> khi sử dụng thiệp cưới online tại HERA</h3>
                                <h4>Sử dụng mã <span><?php echo $coupon_name; ?></span></h4>
                                <a href="<?php echo get_bloginfo('url'); ?>/cac-goi-thiep-moi-hera/" class="action">Dùng ngay</a>
                            </div>
                        </div>
        
                        <div class="guideline_content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        }
        get_footer();
    } else {
        wp_redirect('https://thiepcuoi.hra.vn');
        exit;
    }
}
