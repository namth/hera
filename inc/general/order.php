<?php
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