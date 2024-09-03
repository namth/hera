<?php 
/* 
* Template Name: Retail wedding invitation cards
*/
get_header();
get_template_part('header', 'topbar');

# if not logged in, redirect to login page
if (!is_user_logged_in()) {
    wp_redirect( get_bloginfo("url") . '/dang-nhap');
    exit;
}

# get some infomation of user.
$current_user = wp_get_current_user();
$customer_name = $current_user->display_name;
$customer_email = $current_user->user_email;
$customer_phone = get_field('phone', 'user_' . $current_user->ID);
$customer_address = get_field('address', 'user_' . $current_user->ID);

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
                        'author'        => $current_user->ID,
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

                    $partner = isset($_COOKIE['partner'])?$_COOKIE['partner']:"";
                    # setup hash
                    $hash = inova_encrypt(json_encode(array(
                        'id'            => 0,
                        'final_total'   => 5000,
                        'package_id'    => 0,
                    )), 'e');

                ?>
                </div>
                <div class="">
                    <div class="mui-col-md-3">
                        <h3>Chọn số lượng thiệp bạn cần</h3>
                        <div class="package_box">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/hera/wedding_cards.webp" alt="">
                            <!-- Show a form to input number of invitation cards -->
                            <div class="mui-textfield inova_number_input">
                                <label>Số lượng thiệp</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-number" data-plus="-1">-</button>
                                    </span>
                                    <input type="text" name="invite_cards" id="invite_cards" class="form-control input-number" value="1">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-number" data-plus="1">+</button>
                                    </span>
                                </div>
                            </div>
                            <span class="price has_coupon">
                                
                            </span>
                            <span class="final_price">
                                5.000 ₫
                            </span>
                            <div class="view_coupon">
                                <div class="name">
                                    Mã <span class="value"></span> 
                                    <span class="coupon_name"></span>
                                </div>
                            </div>
                        </div>
                        <div class="coupon">
                            <a href="#" class="coupon_link">Bạn có mã giảm giá?</a>
                            <div class="coupon_form">
                                <input type="text" name="coupon_code">
                                <button class="mui-btn hera-btn">Thêm mã</button>
                            </div>
                            <div class="coupon_notification"></div>
                        </div>
                    </div>
                    <div class="mui-col-md-5">
                        <h3>Nhập thông tin mua hàng</h3>
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
                            <input type="hidden" name="price" value="5000">
                            <input type="hidden" name="number_of_card" value="1">
                            <input type="hidden" name="package" value="0">
                            <button type="submit" class="mui-btn hera-btn fullwidth">Tiếp tục thanh toán</button>
                        </form>
                    </div>
                    <div class="mui-col-md-4">
                        <h3>Hỗ trợ</h3>
                        <p>Bạn có thể tham khảo các gói thiệp với chi phí hợp lý hơn để tiết kiệm ngân sách.</p>
                        <a href="<?php echo get_bloginfo('url') . "/danh-sach-goi-san-pham/"; ?>" class="mui-btn hera-btn">Xem các gói</a>
                        <a href="https://zalo.me/03660.96339" class="mui-btn hera-btn">Zalo hỗ trợ</a>
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