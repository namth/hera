<?php 
/* 
* Hàm này sẽ gọi yêu cầu đồng bộ cho casso ngay lập tức
*/
add_action('wp_ajax_syncCasso', 'syncCasso');
add_action('wp_ajax_nopriv_syncCasso', 'syncCasso');
function syncCasso(){
    # Gọi yêu cầu đồng bộ từ casso
    $curl = curl_init();

    # Gọi số tài khoản TP Bank
    /* $data = array(
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
    $err = curl_error($curl); */
    
    # Gọi thêm số tài khoản của ngân hàng khác
    $data = array(
        'bank_acc_id' => '19038145926015',
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
* add new invoice from $data
* @param array $data
*    'customer_name' => string
*    'customer_phone' => string
*    'customer_email' => string
*    'customer_address' => string
*    'partner' => int (partner id)
*    'buyer' => int (user id who buy this service)
*    'userid' => int (user id who use this service)
*    'package' => int (package id)
*    'product_name' => string (use this if package id is 0, else use package name from package id)
*    'total_card' => int
*    'coupon' => int (coupon id)
*    'total' => int (package id)
*    'final_total' => int (final total)
* @return link to the new invoice
*/
function addNewInvoice($data) {
    $customer_name = $data['customer_name'];
    $customer_phone = $data['customer_phone'];
    $customer_email = $data['customer_email'];
    $customer_address = $data['customer_address'];
    $partner = $data['partner'];
    $buyer = $data['buyer'];
    $userid = $data['userid'];
    $package = $data['package'];
    $product_name = $data['product_name'];
    $total_card = $data['total_card'];
    $coupon = $data['coupon'];
    $total = $data['total'];
    $final_total = $data['final_total'];

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
    if ($partner) {
        update_field('field_63eb529586f37', $partner, $inserted);
    } else {
        # nếu ko có dữ liệu partner từ đối tác thì lấy dữ liệu người giới thiệu từ user 
        $partner = get_field('inviter', 'user_' . $userid);
        if ($partner) {
            update_field('field_63eb529586f37', $partner, $inserted);
        }
    }
    update_field('field_62e6ae7175ee5', $userid, $inserted); # customer
    update_field('field_66dede28df18a', $buyer, $inserted); # buyer
    update_field('field_62eb93b78ca79', 'Chưa thanh toán', $inserted); # status
    update_field('field_62e6ad5875ee1', $package, $inserted); # package
    update_field('field_66ddd71fdf189', $product_name, $inserted); # product_name
    update_field('field_669fdadaabfa2', $total_card, $inserted); # total_card
    update_field('field_62e6ae8f75ee6', $coupon, $inserted); # coupon
    update_field('field_62e6aea375ee7', $total, $inserted); # total
    update_field('field_62eb96e0f9af7', $final_total, $inserted); # final_total
    update_field('field_6393fd6ddef97', $order_code, $inserted); # uuid

    # Update thông tin mua hàng
    $usr_args = [
        'user_email' => $customer_email,
        'display_name' => $customer_name,
    ];
    $updated = wp_update_user($usr_args);
    update_field('field_62ee62714e989', $customer_phone, 'user_' . $buyer);
    update_field('field_62ee62714e963', $customer_address, 'user_' . $buyer);

    # Xử lý coupon trừ bớt số lượng của loại coupon có giới hạn
    # Nếu số lượng là 9999 tức là unlimited, nếu khác 9999 thì -1
    if ($coupon) {
        $coupon_quantity = get_field('coupon_quantity', $coupon);
        if ($coupon_quantity != 9999 & $coupon_quantity > 0) {
            update_field('field_62ec9d1e450e8', --$coupon_quantity, $coupon);
        }
    }

    return get_permalink($inserted);
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
    $partner = $output['partner'];
    $current_user = wp_get_current_user();
    
    # Nếu có dữ liệu gói thì mới tạo hoá đơn
    if ($coupon->package_id) {
        $total      = get_field('price', $coupon->package_id);
        $total_card = get_field('total_card', $coupon->package_id);

        $data = array(
            'customer_name'     => $customer_name,
            'customer_phone'    => $customer_phone,
            'customer_email'    => $customer_email,
            'customer_address'  => $customer_address,
            'partner'           => $partner,
            'buyer'             => $current_user->ID,
            'userid'            => $current_user->ID,
            'package'           => $coupon->package_id,
            'product_name'      => get_the_title($coupon->package_id),
            'total_card'        => $total_card,
            'coupon'            => $coupon->id,
            'total'             => $total,
            'final_total'       => $coupon->final_total,
        );

        # Tạo hoá đơn mới
        $invoice_link = addNewInvoice($data);
        
        echo $invoice_link;
    } else {
        /* 
        * if package id is 0, then create a new invoice from customer who buy cards with flexible number of cards
        */
        $price      = 5000;
        $total_card = $_POST['cards'];
        $total      = $total_card * $price;
        $package_id = 0;
        $product_name = $total_card . " x Thiệp cưới online";
        $coupon_id = $coupon->id? $coupon->id: 0;
        $final_total = $coupon->id? $coupon->final_total: $total;

        $data = array(
            'customer_name'     => $customer_name,
            'customer_phone'    => $customer_phone,
            'customer_email'    => $customer_email,
            'customer_address'  => $customer_address,
            'partner'           => $partner,
            'buyer'             => $current_user->ID,
            'userid'            => $current_user->ID,
            'package'           => $package_id,
            'product_name'      => $product_name,
            'total_card'        => $total_card,
            'coupon'            => $coupon_id,
            'total'             => $total,
            'final_total'       => $final_total,
        );

        # Tạo hoá đơn mới
        $invoice_link = addNewInvoice($data);

        echo $invoice_link;
    }
    exit;
}

add_action('wp_ajax_activeOrder', 'activeOrder');
add_action('wp_ajax_nopriv_activeOrder', 'activeOrder');
function activeOrder(){
    $active_data = json_decode(inova_encrypt($_POST['active_data'], 'd'));
    $inova_orderid = $active_data->order_id;
    # chuyển trạng thái đơn hàng
    update_field('field_62eb93b78ca79', "Đã thanh toán", $inova_orderid);
    update_field('field_636c85b89d08e', 'Kích hoạt ngay', $inova_orderid); #phương thức thanh toán
    update_field('field_636c40bbd3e8c', 0, $inova_orderid); # update số tiền khách thanh toán
    # kích hoạt gói
    $active_done = activation_package($inova_orderid);

    if ($active_done) {
        # Đánh dấu đơn hàng đã được active 
        update_field('field_636df1ce10556', true, $inova_orderid);
    }

    echo get_bloginfo("url") . "/thank-you/";
    exit;
}

/* 
* Source: checkout.php | js/checkout.js
* Cho phép sửa nhanh nội dung trên giao diện hiển thị thông tin đám cưới */
add_action('wp_ajax_addCouponCode', 'addCouponCode');
add_action('wp_ajax_nopriv_addCouponCode', 'addCouponCode');
function addCouponCode() {
    
    /**
     * Process the payment and validate the coupon.
     *
     * @param string $_POST['coupon'] The coupon code.
     * @param string $_POST['package'] The package ID.
     * @param int $_POST['cards'] The number of cards.
     *
     * @return string JSON-encoded data containing the payment status, coupon details, and final total.
     */
    $price = 5000;

    $coupon = $_POST['coupon'];
    $package = $_POST['package'];
    if ($package) {
        $cards = 0;
        $sub_total = get_field('price', $package);
    } else {
        $cards = $_POST['cards'];
        $sub_total = $cards * $price;
    }

    /* Check if the coupon exists */
    $id_coupon = search_customfield('coupon', $coupon, 'coupon_name');

    /* If it exists, validate the coupon for expiration and availability */
    if ($id_coupon) {
        $expired = get_field('expired', $id_coupon);
        $coupon_type = get_field('coupon_type', $id_coupon);
        $coupon_value = get_field('coupon_value', $id_coupon);
        $coupon_quantity = get_field('coupon_quantity', $id_coupon);
        
        /* Validate the coupon data */
        $today = new DateTime();
        if (($coupon_quantity > 0) && ($expired >= $today->format('Ymd'))) {
            $data = array(
                'status' => true,
                'coupon' => $coupon,
                'coupon_value' => $coupon_value,
                'sub_total' => $sub_total,
                'package' => $package,
                'message'=> '<div class="success_notification"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Đã thêm mã coupon thành công.</div>',
            );
            if ($coupon_type == "Phần trăm") {
                /* Calculate the final total after applying the percentage discount */
                $final_total = $sub_total * (100 - $coupon_value) / 100;
                /* Save the data */
                $data['type'] = 'percent';
                $data['coupon_label'] = '- ' . $coupon_value . '%';
                $data['final_total'] = $final_total;
                $data['hash'] = inova_encrypt(json_encode(array(
                    'id'            => $id_coupon,
                    'final_total'   => $final_total,
                    'package_id'    => $package,
                    'number_of_cards' => $cards,
                )), 'e');
            } else {
                /* Calculate the final total after applying the fixed discount */
                $final_total = ($sub_total > $coupon_value)?($sub_total - $coupon_value):"0";
                /* Save the data */
                $data['type'] = 'fix';
                $data['coupon_label'] = '- ' . number_format($coupon_value) . ' ₫';
                $data['final_total'] = $final_total;
                $data['hash'] = inova_encrypt(json_encode(array(
                    'id'            => $id_coupon,
                    'final_total'   => $final_total,
                    'package_id'    => $package,
                    'number_of_cards' => $cards,
                )), 'e');
            }
        } else {
            $check_fail = true;
            $message = "Mã khuyến mại đã hết hạn hoặc hết số lượng.";
        }

    } else {
        $check_fail = true;
        $message = "Không tìm thấy mã khuyến mại.";
    }

    if ($check_fail) {
        $data = array(
            'status' => false,
            'message'=> '<div class="error_notification"><i class="fa fa-time-circle-o" aria-hidden="true"></i> ' . $message . '</div>',
        );
    }

    echo json_encode($data);
    exit;
}
