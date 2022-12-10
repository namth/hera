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
        $check["done"]  = true;
        $check["url"]   = get_bloginfo("url") . "/thank-you/";
    } else {
        $check["done"]  = false;
    }
    echo json_encode($check);
    exit;
}

/* 
* Source: checkout.php | js/checkout.js
* Tạo hoá đơn mới cho khách khi bấm vào nút thanh toán */
add_action('wp_ajax_createInvoice', 'createInvoice');
add_action('wp_ajax_nopriv_createInvoice', 'createInvoice');
function createInvoice() {
    $data = parse_str($_POST['data'], $output);
    $coupon = json_decode(inova_encrypt($output["coupon"], 'd'));
    $customer_name = $output['customer_name'];
    $customer_phone = $output['customer_phone'];
    $customer_email = $output['customer_email'];
    $customer_address = $output['customer_address'];
    $current_user = wp_get_current_user();
    
    # Nếu có dữ liệu gói thì mới tạo hoá đơn
    if ($coupon->package_id) {
        $total = get_field('price', $coupon->package_id);
        # Tạo hoá đơn mới
        $order_code = gen_uuid();
        
        $args = array(
            'post_title'    => $order_code,
            'post_status'   => 'publish',
            'post_type'     => 'inova_order',
        );
        $inserted = wp_insert_post($args);
        
        # Tạo mã đơn hàng mới
        $prefix = (strlen($inserted) < 6)?incrementalHash( 6 - strlen($inserted) ):incrementalHash(1);
        $new_order_id = strtoupper($prefix) . $inserted;
        # Update lại order id mới
        wp_update_post(array(
            'ID' => $inserted,
            'post_title' => $new_order_id, 
        ));
    
        # Update dữ liệu vào hoá đơn mới tạo
        update_field('field_62e6ae7175ee5', $current_user->ID, $inserted); # customer
        update_field('field_62eb93b78ca79', 'Chưa thanh toán', $inserted); # status
        update_field('field_62e6ad5875ee1', $coupon->package_id, $inserted); # package
        update_field('field_62e6ae8f75ee6', $coupon->id, $inserted); # status
        update_field('field_62e6aea375ee7', $total, $inserted); # total
        update_field('field_62eb96e0f9af7', $coupon->final_total, $inserted); # final_total
        update_field('field_6393fd6ddef97', $order_code, $inserted); # uuid
    
        # Update thông tin mua hàng
        $usr_args = [
            'user_email' => $customer_email,
            'display_name' => $customer_name,
        ];
        $updated = wp_update_user($usr_args);
        update_field('field_62ee62714e989', $customer_phone, 'user_' . $current_user->ID);
        update_field('field_62ee62714e963', $customer_address, 'user_' . $current_user->ID);

        # Xử lý coupon trừ bớt số lượng của loại coupon có giới hạn
        # Nếu số lượng là 9999 tức là unlimited, nếu khác 9999 thì -1
        $coupon_quantity = get_field('coupon_quantity', $coupon->id);
        if ($coupon_quantity != 9999 & $coupon_quantity > 0) {
            update_field('field_62ec9d1e450e8', --$coupon_quantity, $coupon->id);
        }
        
        echo get_permalink($inserted);
    }
    exit;
}

