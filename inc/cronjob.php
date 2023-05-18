<?php
# CRONJOB
# tạo hook cronjob để refresh_token hàng ngày.
add_action( 'daily_refresh_token', 'refresh_token' );

/* 
* Check xem thanh toán nào quá hạn 7 ngày thì chuyển sang trạng thái huỷ thanh toán
*/
add_action( 'daily_check_payment_status', 'check_payment_status' ); 
function check_payment_status(){
    $status = 'Chưa thanh toán';
    $now = current_time('timestamp', 7);

    /* query tat ca nhung don hang chua thanh toan */
    $args   = array(
        'post_type'     => 'inova_order',
        'posts_per_page' => -1,
        'post_status'   => 'publish',
        'meta_query'    => array(
            array(
                'key'       => 'status',
                'value'     => $status,
                'compare'   => '=',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            /* Kiểm tra ngày hiện tại và ngày tạo đơn có quá 7 ngày không */
            $start_date = strtotime(get_the_date('d-m-Y'));
            $end_date = strtotime("+7 day", $start_date);

            # miss deadline
            if ($now > $end_date) {
                # cập nhật trạng thái huỷ
                update_field('field_62eb93b78ca79', 'Huỷ'); # status
            }
        }
    }
}

# tạo hook cronjob để check lịch sử thanh toán chuyển khoản tp bank.
add_action( 'check_bank_transaction_history', 'check_bank_transaction_history' );
function check_bank_transaction_history() {
    # lấy ngày hôm nay 
    $now = new DateTime();

    # kiểm tra xem có đơn chờ thanh toán (hoặc thanh toán thiếu) hôm nay không?
    /* query tat ca nhung don hang chua thanh toan hoac thanh toan thieu*/
    $args   = array(
        'post_type'     => 'inova_order',
        'posts_per_page' => -1,
        'post_status'   => 'publish',
        'meta_query'    => array(
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
        ),
        'date_query'    => array(
            array(
                'after' => '-24 hours',
            ),
        ),
    );

    $query = new WP_Query($args);

    # nếu có thì mới check lịch sử giao dịch
    if ($query->post_count) {
        // echo "Check thong tin tai khoan...";
        $tpb = get_tpb_token('00225624', 'Tumotdensau2@@');
        $transactionHistory = get_tpb_history($tpb, '14719869999', $now->format('Ymd'), $now->format('Ymd'));
        $history = json_decode($transactionHistory);
    
        // print_r($history->transactionInfos);
        if ($history->transactionInfos) {
            # kiểm tra từng đơn
            foreach ($history->transactionInfos as $transaction) {
                $search = quick_search_order(strtoupper($transaction->description));
    
                # Nếu tìm nhanh được order thì trả kết quả luôn, nếu không thấy thì tìm nâng cao
                if (!$search) {
                    $search = advance_search_order(strtoupper($transaction->description));
                } else {
                    $status = get_field('status', $search);
                }

                # Kiểm tra xem order đã được active chưa 
                $active = get_field('activate', $search);
    
                if (!$active) {
                    $output = process_transferbank($search, $transaction->id, $transaction->amount);
                } else {
                    $output["error"] = 404;
                    $output["status"] = "Không phải đơn thanh toán";
                }
            }
        }
    }
    return $output;
}

# Cuối mỗi ngày sẽ chạy một hàm cronjob để đếm số đơn hàng mà đối tác đã giới thiệu
add_action( 'daily_update_order', 'daily_update_order' );
function daily_update_order(){
    # mỗi ngày đều chạy hàm này vào lúc 23:50
    /* query tat ca nhung don hang chua thanh toan */
    $args   = array(
        'post_type'     => 'inova_order',
        'posts_per_page' => -1,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => 'activate',
                'value'     => true,
                'compare'   => '=',
            ),
            array(
                'key'       => 'commission_check',
                'value'     => true,
                'compare'   => '!=',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $seller = get_field('seller');
            $final_total = get_field('final_total');
            $paid = get_field('paid');
            $package_id = get_field('package');
            $total_card = get_field('total_card', $package_id);

            # neu co nguoi gioi thieu thi cap nhat thong tin cho user do
            if ($seller) {
                # lấy số lượng gói thiệp đã cung cấp rồi cộng thêm 1
                $number_of_order    = get_field('number_of_order', 'user_' . $seller);
                update_field('field_63eb41cbd2cae', $number_of_order + 1, 'user_' . $seller);

                # update doanh số tháng
                $monthly_sales      = get_field('monthly_sales', 'user_' . $seller);
                update_field('field_63eb42f2d2cb0', $monthly_sales + $final_total, 'user_' . $seller);

                # update số lượng thiệp được sử dụng
                $number_of_card     = get_field('number_of_card', 'user_' . $seller);
                update_field('field_63eb42d4d2caf', $number_of_card + $total_card, 'user_' . $seller);
            }

            # Đánh dấu “trạng thái trả hoa hồng" là TRUE
            update_field('field_63eb542d86f38', true);
        }
    }
}

# Cuối mỗi tháng sẽ chạy một hàm cronjob để chốt lại một số thông tin hoa hồng cho user
add_action( 'monthly_update_commission', 'monthly_update_commission' );
function monthly_update_commission(){
    # mỗi ngày đều chạy hàm này vào lúc 0:30
    # nếu ngày hiện tại bằng với ngày mùng 1 thì sẽ update thông tin hoa hồng cho user.
    $now = new DateTime();

    if($now->format('d') == '1') {
        # query tất cả user có role là cộng tác viên, và có doanh số tháng lớn hơn 0
        $args   = array(
            'number'    => 999,
            'meta_query'=> array(
                'relation' => 'OR',
                array(
                    'key'     => 'monthly_sales',
                    'value'   => 0,
                    'compare' => '>',
                ),
            )
        );
        $users_query = new WP_User_Query($args);
        # duyệt qua từng user: 
        $users = $users_query->get_results();
        if (!empty($users)) {
            foreach ($users as $user) {
                # Lấy doanh số của tháng đã lưu và tính theo chính sách chiết khấu để ra số hoa hồng nhận được trong tháng.
                $monthly_sales  = get_field('monthly_sales', 'user_' . $user->ID);
                # tính toán mức chiết khấu 
                if ($monthly_sales < '3000000') {
                    $discount_amount = '0.15';
                } else if (($monthly_sales >= '3000000') && ($monthly_sales < '6000000')) {
                    $discount_amount = '0.2';
                } else if (($monthly_sales >= '6000000') && ($monthly_sales < '9000000')) {
                    $discount_amount = '0.25';
                } else {
                    $discount_amount = '0.3';
                }

                # Chuyển hoa hồng chờ nhận sang hoa hồng có thể rút
                update_field('field_63eb4315d2cb1', round($monthly_sales * $discount_amount), 'user_' . $user->ID);
                # Reset lại doanh số của tháng về 0
                update_field('field_63eb42f2d2cb0', 0, 'user_' . $user->ID);
            }
        }

    }
}