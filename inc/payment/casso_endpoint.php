<?php 
add_action('rest_api_init', function(){
    # casso endpoint
    register_rest_route('hera/v1/', 'casso_endpoint', array(
        'methods'   => 'POST',
        'callback'  => 'casso_endpoint',
    ));
});

function casso_endpoint(WP_REST_Request $request) {
    $token = $request->get_header('secure-token');
    $json_result = json_decode($request->get_body()); 

    # check token casso
    if( $token != CASSO_TOKEN ) {
        return [
            'error' => 401,
            'status'=> "Thiếu Secure Token hoặc secure token không khớp",
        ];
    }

    # Nếu không có dữ liệu thì báo lỗi
    if (!$json_result || !$request) {
        return [
            'error' => 412,
            'status'=> "Request thiếu body hoặc sai định dạng",
        ];
    }

    # Nếu error lớn hơn 0, có lỗi ở phía Casso
    if ($json_result->error != 0){
        return [
            'error' => 500,
            'status'=> "Có lỗi xảy ra ở phía Casso",
        ];
    }

    # Nhận dạng order id
    $data = $json_result->data[0];
    $description = strtoupper(trim($data->description));

    $search = quick_search_order($description);

    # Nếu tìm nhanh được order thì trả kết quả luôn, nếu không thấy thì tìm nâng cao
    if (!$search) {
        $search = advance_search_order($description);
        $status = "Chưa thanh toán";
    } else {
        $status = get_field('status', $search);
    }

    # Kiểm tra xem order đã được active chưa 
    $active = get_field('activate', $search);

    # Nếu tìm được order ID thì xử lý kích hoạt đơn hàng.
    if (!$active) {
        process_transferbank($search, $data->id, $data->amount);

        /* $output["error"] = 0;
        # check xem id đã được xử lý hay chưa
        $pre_casso_id_array = explode(',', get_field('casso_id', $search));

        if (!$pre_casso_id_array || !in_array($data->id, $pre_casso_id_array)) {
            # kiểm tra xem đủ tiền hay thiếu tiền
            // GIÁ TIỀN TỔNG CỘNG CỦA ĐƠN HÀNG
            $total = get_field('final_total', $search);
            $pre_paid = get_field('paid', $search);
            $ORDER_MONEY = $total - $pre_paid;
    
            // Số tiền chuyển thiếu tối đa mà hệ thống vẫn chấp nhận để xác nhận đã thanh toán
            $ACCEPTABLE_DIFFERENCE = 10000;
    
            # Số tiền khách chuyển
            $paid = $data->amount;
    
            $ACCEPTABLE_DIFFERENCE = abs($ACCEPTABLE_DIFFERENCE);
    
            if ( $paid < $ORDER_MONEY  - $ACCEPTABLE_DIFFERENCE ){
                # chuyển trạng thái đơn hàng sang  Thanh toán thiếu 
                $output["status"] = 'Thanh toán thiếu';
    
            } else if ($paid <= $ORDER_MONEY + $ACCEPTABLE_DIFFERENCE){
                # chuyển trạng thái đơn hàng sang đã thanh toán 
                $output["status"] = 'Đã thanh toán';
            } else {
                # chuyển trạng thái đơn hàng sang Thanh toán dư 
                $output["status"] = 'Thanh toán dư';
            }
            
            # update status
            update_field('field_62eb93b78ca79', $output["status"], $search);
            # update số tiền khách trả.
            update_field('field_636c40bbd3e8c', $paid + $pre_paid, $search);
            # update casso id
            $pre_casso_id_array[] = $data->id;
            $casso_id_array = implode(',', array_filter($pre_casso_id_array));
            update_field('field_636c856e9d08d', $casso_id_array, $search);

            # update payment method
            $method = get_field('payment_method', $search);
            if (!$method) {
                update_field('field_636c85b89d08e', 'Chuyển khoản ngân hàng (Kích hoạt tự động)', $search);
            }

            # Kích hoạt đơn hàng 
            $active_done = activation_package($search);

            if ($active_done) {
                # Đánh dấu đơn hàng đã được active 
                update_field('field_636df1ce10556', true, $data->order);
            }
        } else {
            $output["status"] = "Đơn hàng đã xử lý";
        } */
    } else {
        $output["error"] = 404;
        $output["status"] = "Không phải đơn thanh toán";
    }
    return $output;
}

function process_transferbank ( $orderid, $payment_id, $amount ) {
    if ($orderid) {
        $output["error"] = 0;
        # check xem id đã được xử lý hay chưa
        $pre_casso_id_array = explode(',', get_field('casso_id', $orderid));

        if (!$pre_casso_id_array || !in_array($payment_id, $pre_casso_id_array)) {
            # kiểm tra xem đủ tiền hay thiếu tiền
            // GIÁ TIỀN TỔNG CỘNG CỦA ĐƠN HÀNG
            $total = get_field('final_total', $orderid);
            $pre_paid = get_field('paid', $orderid);
            $ORDER_MONEY = $total - $pre_paid;
    
            // Số tiền chuyển thiếu tối đa mà hệ thống vẫn chấp nhận để xác nhận đã thanh toán
            $ACCEPTABLE_DIFFERENCE = 10000;
    
            # Số tiền khách chuyển
            $paid = $amount;
    
            $ACCEPTABLE_DIFFERENCE = abs($ACCEPTABLE_DIFFERENCE);
    
            if ( $paid < $ORDER_MONEY  - $ACCEPTABLE_DIFFERENCE ){
                # chuyển trạng thái đơn hàng sang  Thanh toán thiếu 
                $output["status"] = 'Thanh toán thiếu';
    
            } else {
                if ($paid <= $ORDER_MONEY + $ACCEPTABLE_DIFFERENCE){
                    # chuyển trạng thái đơn hàng sang đã thanh toán 
                    $output["status"] = 'Đã thanh toán';
                } else {
                    # chuyển trạng thái đơn hàng sang Thanh toán dư 
                    $output["status"] = 'Thanh toán dư';
                }

                # Kích hoạt đơn hàng 
                $active_done = activation_package($orderid);

            }
            
            # update status
            update_field('field_62eb93b78ca79', $output["status"], $orderid);
            # update số tiền khách trả.
            update_field('field_636c40bbd3e8c', $paid + $pre_paid, $orderid);
            # update casso id
            $pre_casso_id_array[] = $payment_id;
            $casso_id_array = implode(',', array_filter($pre_casso_id_array));
            update_field('field_636c856e9d08d', $casso_id_array, $orderid);

            # update payment method
            $method = get_field('payment_method', $orderid);
            if (!$method) {
                update_field('field_636c85b89d08e', 'Chuyển khoản ngân hàng (Kích hoạt tự động)', $orderid);
            }

        } else {
            $output["status"] = "Đơn hàng đã xử lý";
        }
    } else {
        $output["error"] = 404;
        $output["status"] = "Không phải đơn thanh toán";
    }
    return $output;
}

function advance_search_order( $orderid ) {
    # quét tất cả các đơn order chưa thanh toán để tìm kiếm
    $args = array(
        'post_type' => 'inova_order',
        'meta_query' => array(
            'relation'      => 'OR',
            array(
                'key'       => 'status',
                'value'     => 'Chưa thanh toán',
                'compare'   => '=',
            ),
            array(
                'key'       => 'status',
                'value'     => 'Thanh toán thiếu',
                'compare'   => '=',
            ),
        )
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            if (strpos($orderid, get_the_title()) !== false){
                return get_the_ID();
            }
        }
    } else {
        return false;
    }
}

function quick_search_order( $orderid ) {
    # quét tất cả các đơn order chưa thanh toán để tìm kiếm
    $args = array(
        'post_type' => 'inova_order',
        's'         => $orderid,
        'meta_query' => array(
            'relation'      => 'OR',
            array(
                'key'       => 'status',
                'value'     => 'Chưa thanh toán',
                'compare'   => '=',
            ),
            array(
                'key'       => 'status',
                'value'     => 'Thanh toán thiếu',
                'compare'   => '=',
            ),
        )
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            if (get_the_title() == $orderid){
                return get_the_ID();
            }
        }
    } else {
        return false;
    }
}
