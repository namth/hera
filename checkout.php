<?php 
/* 
* Template Name: Thanh toán
*/
get_header();
get_template_part('header', 'topbar');
$current_user_id = get_current_user_id();

$normal_price = get_field('normal_price','option');
$vip_price = get_field('vip_price','option');
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-10">
            <div class="mui-panel" id="checkout">
                <h3 class="title_general mui--divider-bottom">Mua thiệp cưới</h3>
                <p>Bạn hãy chọn số lượng của từng loại thiệp mua ở box phía bên dưới.</p>
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
                <form action="#" method="POST">
                    <div class="mui-row">
                        <div class="mui-col-md-8">
                            <div class="mui-row">
                                <div class="mui-col-md-4">
                                    <div class="checkout_input green">
                                        <h4>Thiệp thường</h4>
                                        <input class="numberstyle" type="number" name="normal_card_qtt" value="0" min="0" step="10" data-price="5000">
                                        <span><?php echo number_format($normal_price); ?> ₫ / thiệp</span>
                                        <span class="total  mui--divider-top">0 ₫</span>
                                    </div>
                                </div>
                                <div class="mui-col-md-4">
                                    <div class="checkout_input orange">
                                        <h4>Thiệp VIP</h4>
                                        <input class="numberstyle" type="number" name="vip_card_qtt" value="0" min="0" step="10" data-price="10000">
                                        <span><?php echo number_format($vip_price); ?> ₫ / thiệp</span>
                                        <span class="total  mui--divider-top">0 ₫</span>
                                    </div>
                                </div>
                                <div class="mui-col-md-4">
                                    <div class="checkout_input">
                                        <h4 class="sub_title">Tổng tiền <span>(chưa có VAT)</span></h4>
                                        <span class="sub_total no_coupon">0 ₫</span>
                                        <input type="hidden" name="sub_total" value="">
                                        <div class="coupon">
                                            <div class="code">
                                                <span class="title">
                                                    Mã giảm giá
                                                </span>
                                                <span class="coupon_name"></span>
                                            </div>
                                            <div class="value"></div>
                                            <input type="hidden" name="coupon" value="">
                                        </div>
                                        <span class="total final_total mui--divider-top" style="display: none;">0 ₫ </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mui-col-md-4">
                            <h4 class="info_title">Thông tin thanh toán</h4>
                            <div class="checkout_infomation">
                                <h5>Nam Tran</h5>
                                <span>0986896800</span>
                                <span>namth.pass@gmail.com</span>
                                <span>95/81/62 Vũ Xuân Thiều, Sài Đồng, Long Biên, Hà Nội</span>
                                <a href="#" class="link_edit">Chỉnh sửa thông tin</a>
                            </div>
                        </div>
                    </div>
                    <div class="mui-row">
                        <div class="mui-col-md-6">
                            <a href="#" class="coupon_link">Bạn có mã giảm giá?</a>
                            <div class="coupon_form">
                                <input type="text" name="coupon_code">
                                <button class="mui-btn hera-btn">Thêm mã</button>
                            </div>
                            <div class="coupon_notification"></div>
                        </div>
                        <div class="mui-col-md-6 checkout_btn">
                            <?php 
                            wp_nonce_field('post_nonce', 'post_nonce_field');
                            ?>
                            <input type="submit" class="mui-btn hera-btn" value="Tiếp tục thanh toán">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/js/inputnumber.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/checkout.js"></script>
<?php
get_footer();