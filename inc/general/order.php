<?php
define("MOMO_PARTNER_CODE", "MOMOV3HW20220505");
define("MOMO_ACCESS_KEY", "QymLYnfiBN9hyoo3");
define("MOMO_SECRET_KEY", "m2qjTh8zoKGfhG3UIM4ccbSHhJog4CVO");
define("MOMO_ENDPOINT", "https://payment.momo.vn/v2/gateway/api/create");

function activation_package($order_id){
    $user_id    = get_field('customer', $order_id);
    $package_id = get_field('package', $order_id);
    $activate   = get_field('activate', $order_id);
    # Lấy thông tin gói sản phẩm
    if ($package_id && $user_id && !$activate) {
        $total_card = get_field('total_card', $package_id); # Số lượng thiệp
        $category   = get_field('category', $package_id); # Danh mục thiệp
        $activation_date = new DateTime(); # Ngày kích hoạt là ngày hôm nay.
        $payment_date = $activation_date->format('Ymd');

        # Cập nhật thông tin gói sản phẩm cho user
        update_field('field_636e14f7cd13f', $total_card, 'user_' . $user_id);
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