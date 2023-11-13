<?php
function activation_package($order_id){
    $user_id    = get_field('customer', $order_id);
    $package_id = get_field('package', $order_id);
    $activate   = get_field('activate', $order_id);
    # Lấy thông tin gói sản phẩm
    if ($package_id && $user_id && !$activate) {
        $current_cards = get_field('total_cards', 'user_' . $user_id);
        $total_card = get_field('total_card', $package_id); # Số lượng thiệp
        $category   = get_field('category', $package_id); # Danh mục thiệp
        $activation_date = new DateTime(); # Ngày kích hoạt là ngày hôm nay.
        $payment_date = $activation_date->format('Ymd');

        # Cập nhật thông tin gói sản phẩm cho user
        update_field('field_63731ae11ebcb', $package_id, 'user_' . $user_id);
        update_field('field_636e14f7cd13f', $total_card + $current_cards, 'user_' . $user_id);
        update_field('field_636e1565cd142', $category, 'user_' . $user_id);
        update_field('field_636e150bcd140', $activation_date->format('Ymd'), 'user_' . $user_id);
        update_field('field_636e1537cd141', $activation_date->modify( "+1 month" )->format('Ymd'), 'user_' . $user_id);

        # Cập nhật lại thông tin đơn hàng
        # Đánh dấu đơn hàng đã được active 
        update_field('field_636df1ce10556', true, $order_id);
        # Lưu lại ngày kích hoạt
        update_field('field_637191630c85e', $payment_date, $order_id);
        return true;
    } else {
        return false;
    }
}

# Tạo UUID V4 cho đơn hàng Momo.
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

add_filter( 'http_request_timeout', function( $timeout ) { return 60; });

# Verify Coupon 
function get_value_after_coupon( $coupon_id, $package_id ){
    $sub_total = get_field('price', $package_id);
    if ($coupon_id) {
        $expired = get_field('expired', $coupon_id);
        $coupon_type = get_field('coupon_type', $coupon_id);
        $coupon_value = get_field('coupon_value', $coupon_id);
        $coupon_quantity = get_field('coupon_quantity', $coupon_id);

        /* validate data */
        $today = new DateTime();
        if (($coupon_quantity > 0) && ($expired >= $today->format('Ymd'))) {
            if ($coupon_type == "Phần trăm") {
                /* Tính số tiền cuối nhận được */
                $final_total = $sub_total * (100 - $coupon_value) / 100;
            } else {
                /* Tính số tiền cuối nhận được */
                $final_total = ($sub_total > $coupon_value)?($sub_total - $coupon_value):"0";
            }
            return $final_total;
        } else return false;
    } else return false;
}

# Get lastest success order by current user ID
function get_lastest_payment($userID){
    /* $args   = array(
        'post_type'     => 'inova_order',
        'posts_per_page' => 1,
        'author'        => $userID,
        'post_status'   => 'publish',
        'orderby' => 'ID',
        'order' => 'DESC',
        'meta_query'    => array(
            array(
                'key'       => 'status',
                'value'     => 'Đã thanh toán',
                'compare'   => '=',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        get_field();
    } */
}