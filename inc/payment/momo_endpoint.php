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
    $inova_orderid = $extraData->inova_orderid;
    $activate = get_field('activate', $inova_orderid);
    update_field('field_6394b76112cf9', $json_result, $inova_orderid);

    $signature = $json_result->signature;
    //before sign HMAC SHA256 signature
    $rawHash =  
        "accessKey=" . MOMO_ACCESS_KEY . 
        "&amount=" . $json_result->amount .
        "&extraData=" . $json_result->extraData .
        "&message=" . $json_result->message .
        "&orderId=" . $json_result->orderId .
        "&orderInfo=" . $json_result->orderInfo .
        "&orderType=" . $json_result->orderType .
        "&partnerCode=" . $json_result->partnerCode .
        "&payType=" . $json_result->payType .
        "&requestId=" . $json_result->requestId .
        "&responseTime=" . $json_result->responseTime .
        "&resultCode=" . $json_result->resultCode .
        "&transId=" . $json_result->transId;

    $check_signature = hash_hmac("sha256", $rawHash, MOMO_SECRET_KEY);

    # Nếu chưa active và xác nhận chữ ký đúng thì cập nhật trạng thái đơn hàng
    if ( !$activate && ($json_result->resultCode == 0) && ($signature == $check_signature)) {
        # chuyển trạng thái đơn hàng
        update_field('field_62eb93b78ca79', "Đã thanh toán", $inova_orderid);
        update_field('field_636c85b89d08e', 'Thanh toán qua ví MOMO', $inova_orderid); #phương thức thanh toán
        update_field('field_636c40bbd3e8c', $json_result->amount, $inova_orderid); # update số tiền khách thanh toán
        # kích hoạt gói
        $active_done = activation_package($inova_orderid);

        if ($active_done) {
            # Đánh dấu đơn hàng đã được active 
            update_field('field_636df1ce10556', true, $inova_orderid);
        }
    }

    return new WP_REST_Response(null, 204);
}

function momo_check_order($data = []) {
    $resultCode     = $data['resultCode'];
    $signature      = $data['signature'];
    $extraData      = json_decode(base64_decode($data['extraData']));
    $inova_orderid = $extraData->inova_orderid;

    //before sign HMAC SHA256 signature
    $rawHash =  
        "accessKey=" . MOMO_ACCESS_KEY . 
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

    $check_signature = hash_hmac("sha256", $rawHash, MOMO_SECRET_KEY);

    # Nếu không có lỗi gì và xác nhận được chữ ký hợp lệ thì kích hoạt gói.
    if ( ($resultCode == 0) && ($signature == $check_signature)) {
        $activate = get_field('activate', $inova_orderid);
        if (!$activate) {
            # chuyển trạng thái đơn hàng
            update_field('field_62eb93b78ca79', "Đã thanh toán", $inova_orderid);
            update_field('field_636c85b89d08e', 'Thanh toán qua ví MOMO', $inova_orderid); #phương thức thanh toán
            update_field('field_636c40bbd3e8c', $data['amount'], $inova_orderid); # update số tiền khách thanh toán
            # kích hoạt gói
            $active_done = activation_package($inova_orderid);
    
            if ($active_done) {
                # Đánh dấu đơn hàng đã được active 
                update_field('field_636df1ce10556', true, $inova_orderid);
            }
        }
        # trả về kết quả kích hoạt thành công
        return true;
    } else {
        return false;
    }
}