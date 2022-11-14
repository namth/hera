<?php 
/* 
* Hàm này sẽ gọi yêu cầu đồng bộ cho casso ngay lập tức
*/
add_action('wp_ajax_activeOrder', 'activeOrder');
add_action('wp_ajax_nopriv_activeOrder', 'activeOrder');
function activeOrder(){
    $data = json_decode(inova_encrypt($_POST['data'], 'd'));

    # Kiểm tra xem đơn hàng đã được thanh toán đủ hay chưa
    $final_total    = get_field('final_total', $data->order);
    $paid           = get_field('paid', $data->order);
    $activate       = get_field('activate', $data->order);

    # Kiểm tra trạng thái kích hoạt chưa
    if(($paid >= $final_total) && !$activate){
        # Nếu đã thanh toán đủ và chưa kích hoạt thì kích hoạt dịch vụ
        $active = activation_package($data->package, $data->user);

        if ($active) {
            # Đánh dấu đơn hàng đã được active 
            update_field('field_636df1ce10556', true, $data->order);
        }
    } else {
        define('CASSO_APIKEY', 'AK_CS.ecab342063ca11edb41ba114fdd37350.BOzUgiV389ClrLgbzVyzIA9gB1v0tXV9WkKdtwDPHDTsmRI0Va0iCL6Adc9cUecq2OqBvIIu');
        # Gọi yêu cầu đồng bộ từ casso
        $curl = curl_init();

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
                "Authorization: " . CASSO_APIKEY,
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
    }
    
    exit;
}