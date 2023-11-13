<?php 
/* 
* Template Name: Xác nhận đơn hàng
*/
if ( isset( $_GET['p'] ) && ($_GET['p'] != "")) {
    $package_id = inova_encrypt($_GET['p'], 'd');
    if (!is_numeric($package_id)) {
        wp_redirect( get_bloginfo("url") );
        exit;
    }
    get_header();
    get_template_part('header', 'topbar');
    $current_user_id = get_current_user_id();
    #Lấy thông tin gói đang có dựa trên hoá đơn thành công cuối cùng
    
    $current_package_id = get_field('package_id', 'user_' . $current_user_id);

    ?>
    <div class="mui-container-fluid">
        <div class="mui-row">
            <div class="mui-col-md-2 npl">
                <?php
                get_sidebar();

                ?>
            </div>
            <div class="mui-col-md-10 mt20">
                <div class="mui-panel" id="confirm_order">
                    <h3 class="title_general mui--divider-bottom">Xác nhận đơn hàng</h3>
                    <div class="notification">
                    <?php 
                        $status = 'Chưa thanh toán';
                        $args   = array(
                            'post_type'     => 'inova_order',
                            'posts_per_page' => 20,
                            'paged'         => $paged,
                            'author'        => $current_user_id,
                            'post_status'   => 'publish',
                            'meta_query'    => array(
                                array(
                                    'key'       => 'status',
                                    'value'     => $status,
                                    'compare'   => '=',
                                ),
                            ),
                        );

                        $query = new WP_Query($args);

                        if ($query->have_posts()) {
                            echo "<span>Bạn đang có " . $query->post_count . " hoá đơn chưa thanh toán</span>";
                            echo "<a class='card_link' href='". get_bloginfo('url') ."/danh-sach-don-hang/'>
                                    <i class='fa fa-cart-plus' aria-hidden='true'></i>
                                    Thanh toán ngay</a>";
                        }

                    ?>
                    </div>
                    <div class="">
                        <div class="mui-col-md-3">
                            <h3>Gói sản phẩm đã chọn</h3>
                            <?php
                                $partner = $_COOKIE['partner'];
                                $price = get_field('price', $package_id);
                                $coupon = get_field('coupon', $package_id);
                                # check coupon xem có được sử dụng chưa, nếu limit thì set coupon về 0
                                if (!check_coupon_limit($coupon, $current_user->ID)) {
                                    $coupon = 0;
                                }
                                if ($coupon) {
                                    $final_price = get_value_after_coupon( $coupon, $package_id);
                                } else {
                                    $final_price = $price;
                                }

                                $thumbnail_url = get_the_post_thumbnail_url($package_id, 'thumbnail');

                                # setup hash
                                $hash = inova_encrypt(json_encode(array(
                                    'id'            => $coupon,
                                    'final_total'   => $final_price,
                                    'package_id'    => $package_id,
                                )), 'e');

                                # get some infomation of user.
                                $current_user = wp_get_current_user();
                                $customer_name = $current_user->display_name;
                                $customer_email = $current_user->user_email;
                                $customer_phone = get_field('phone', 'user_' . $current_user->ID);
                                $customer_address = get_field('address', 'user_' . $current_user->ID);

                                $class_coupon = $coupon?'has_coupon':'';
                            ?>
                            <div class="package_box">
                                <img src="<?php echo $thumbnail_url; ?>" alt="">
                                <h4><?php echo get_the_title($package_id); ?></h4>
                                <span class="price <?php echo $class_coupon; ?>">
                                    <?php echo number_format($price) . " ₫"; ?>
                                </span>
                                <div class="view_coupon">
                                    <div class="name">
                                        Mã <span class="value"></span> 
                                        <span class="coupon_name"></span>
                                    </div>
                                </div>
                                <span class="final_price">
                                    <?php if ($final_price != $price) echo number_format($final_price) . " ₫"; ?>
                                </span>
                            </div>
                            <div class="coupon">
                                <a href="#" class="coupon_link">Bạn có mã giảm giá?</a>
                                <div class="coupon_form">
                                    <input type="text" name="coupon_code">
                                    <input type="hidden" name="package" value="<?php echo $package_id; ?>">
                                    <button class="mui-btn hera-btn">Thêm mã</button>
                                </div>
                                <div class="coupon_notification"></div>
                            </div>
                        </div>
                        <div class="mui-col-md-6">
                            <h3>Nhập thông tin của bạn</h3>
                            <div id="notificate" class="notification"></div>
                            <form class="mui-form" method="POST" enctype="multipart/form-data" id="confirm_order">
                                <div class="hera_input">
                                    <label for="customer_name">Họ và tên</label>
                                    <input type="text" name="customer_name" value="<?php if($customer_name) echo $customer_name; ?>">
                                </div>
                                <div class="hera_input">
                                    <label for="customer_phone">Số điện thoại</label>
                                    <input type="text" name="customer_phone" value="<?php if($customer_phone) echo $customer_phone; ?>">
                                </div>
                                <div class="hera_input">
                                    <label for="customer_email">Email</label>
                                    <input type="email" name="customer_email" value="<?php if($customer_email) echo $customer_email; ?>">
                                </div>
                                <div class="hera_input">
                                    <label for="customer_address">Địa chỉ</label>
                                    <input type="text" name="customer_address" value="<?php if($customer_address) echo $customer_address; ?>">
                                </div>
                                <?php 
                                    wp_nonce_field('post_nonce', 'post_nonce_field');
                                ?>
                                <input type="hidden" name="coupon" value="<?php echo $hash; ?>">
                                <input type="hidden" name="partner" value="<?php echo $partner; ?>">
                                <button type="submit" class="mui-btn hera-btn fullwidth">Tiếp tục thanh toán</button>
                            </form>
                        </div>
                        <div class="mui-col-md-3">
                            <h3>Hỗ trợ thanh toán</h3>
                            <p>Hãy điền đầy đủ thông tin để chúng tôi dễ dàng hỗ trợ bạn khi cần thiết.</p>
                            <a href="<?php echo get_bloginfo('url') . "/danh-sach-goi-san-pham/"; ?>" class="mui-btn hera-btn">Đổi gói</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo get_template_directory_uri(); ?>/js/inputnumber.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/checkout.js"></script>
    <?php
    get_footer();
} else {
    wp_redirect( get_bloginfo("url") );
    exit;
}