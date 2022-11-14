<?php 
/* 
* Hàm này sẽ gọi yêu cầu đồng bộ cho casso ngay lập tức
*/
add_action('wp_ajax_syncCasso', 'syncCasso');
add_action('wp_ajax_nopriv_syncCasso', 'syncCasso');
function syncCasso(){
    define('CASSO_APIKEY', 'AK_CS.ecab342063ca11edb41ba114fdd37350.BOzUgiV389ClrLgbzVyzIA9gB1v0tXV9WkKdtwDPHDTsmRI0Va0iCL6Adc9cUecq2OqBvIIu');
    # Gọi yêu cầu đồng bộ từ casso
    $curl = curl_init();

    # Gọi số tài khoản TP Bank
    $data = array(
        'bank_acc_id' => '14719869999',
    );
    $postdata = json_encode($data);

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://oauth.casso.vn/v2/sync",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postdata,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Apikey " . CASSO_APIKEY,
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    # Gọi thêm số tài khoản của ngân hàng khác
    # ...
    
    exit;
}

add_action('wp_ajax_checkOrder', 'checkOrder');
add_action('wp_ajax_nopriv_checkOrder', 'checkOrder');
function checkOrder(){
    $order_id  = $_POST['order'];
    $activate = get_field('activate', $order_id);

    if ($activate) {
        $message = base64_encode("Thanh toán thành công. Bạn có thể quay lại trang chủ để tiếp tục.");
        $check["done"]  = true;
        $check["url"]   = get_bloginfo("url") . "/thank-you/?m=" . $message;
    } else {
        $check["done"]  = false;
    }
    echo json_encode($check);
    exit;
}