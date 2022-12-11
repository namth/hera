<?php 
# Đường link thông báo tức thời IPN dành cho momo khi có 1 giao dịch thành công
add_action('rest_api_init', function(){
    # casso endpoint
    register_rest_route('hera/v1/', 'momo_endpoint', array(
        'methods'   => 'POST',
        'callback'  => 'momo_endpoint',
    ));
});

function momo_endpoint(WP_REST_Request $request) {
    $json_result = json_decode($request->get_body());

    $extraData = json_decode(base64_decode($json_result->extraData));
    update_field('field_6394b76112cf9', $json_result, $extraData->inova_orderid);

    return new WP_REST_Response(null, 204);
}

function momo_check_order($data = []) {
    $config = file_get_contents(get_template_directory_uri() . '/inc/config.json');
    $array = json_decode($config, true);

    $resultCode     = $data['resultCode'];
    $signature      = $data['signature'];
    $extraData      = json_decode(base64_decode($data['extraData']));
    $inova_orderid = $extraData->inova_orderid;

    //before sign HMAC SHA256 signature
    $rawHash =  
        "accessKey=" . $array["accessKey"] . 
        "&amount=" . $data['amount'] . 
        "&extraData=" . $data['extraData'] .
        "&message=" . $data['message'] .
        "&orderId=" . $data['orderId'] .
        "&orderInfo=" . $data['orderInfo'] .
        "&orderType=" . $data['orderType'] .
        "&partnerCode=" . $data['partnerCode'] .
        "&payType=" . $data['payType'] .
        "&requestId=" . $data['requestId'] .
        "&responseTime=" . $data['responseTime'] .
        "&resultCode=" . $data['resultCode'] .
        "&transId=" . $data['transId'];

    $check_signature = hash_hmac("sha256", $rawHash, $array["secretKey"]);

    # Nếu không có lỗi gì và xác nhận được chữ ký hợp lệ thì kích hoạt gói.
    if ( ($resultCode == 0) && ($signature == $check_signature)) {
        # chuyển trạng thái đơn hàng
        update_field('field_62eb93b78ca79', "Đã thanh toán", $inova_orderid);
        update_field('field_636c85b89d08e', 'Thanh toán qua ví MOMO', $inova_orderid); #phương thức thanh toán
        # kích hoạt gói
        $active_done = activation_package($inova_orderid);

        if ($active_done) {
            # Đánh dấu đơn hàng đã được active 
            update_field('field_636df1ce10556', true, $inova_orderid);
        }
        # trả về thông báo
        return true;
    } else {
        return false;
    }
}