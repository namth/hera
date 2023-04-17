<?php
$api_dir = dirname( __FILE__ );
require_once( $api_dir . '/payment/casso_endpoint.php');
require_once( $api_dir . '/payment/momo_endpoint.php');
require_once( $api_dir . '/payment/tpbank_api.php');

# get authentication code
# refresh token everyday
function refresh_token()
{
    $api_url  = get_field('api_base_url', 'option') . '/wp-json/inova/v1/gettoken';

    $user = array(
        'username' => INOVAUSER,
        'password' => INOVAPASS,
    );

    # authenticate to get token
    $jwt = wp_remote_post(
        $api_url,
        array(
            'method'        => 'POST',
            'timeout'       => '60',
            'body'          => $user,
        )
    );

    $token = json_decode(wp_remote_retrieve_body($jwt));
    // print_r($token);
    if (!$token->token) {
        return false;
    } else {
        update_field('field_62a6a717c5658', $token->token, 'option');
        return $token->token;
    }
};

function check_token($token) {
    $api_url  = get_field('api_base_url', 'option') . '/wp-json/inova/v1/checktoken';
    $check = inova_api($api_url, $token, 'POST', '');

    if ($check->code == 'success') {
        return true;
    } else {
        return false;
    }
}

# call any api with authentication token
function inova_api($api, $token, $method, $body) {
    $args = array(
        'method'    => $method,
        'timeout'   => '120',
        'headers'   => array(
            'Content-Type'  => 'application/json; charset=utf-8',
            'Authorization' => $token,
        ),
        'body'      => json_encode($body),
        'sslverify' => true,
    );

    $response = wp_remote_post(
        $api,
        $args
    );

    if (is_wp_error($response)) {
        return $response;
    } else {
        $response_body = json_decode(wp_remote_retrieve_body($response));
        return $response_body;
    }
}

# gọi API để lấy HTML khi đã chọn thiệp 
function getHTML($cardid){
    $token = refresh_token();
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $cardid;
    $mycard = inova_api($api_url, $token, 'GET', '');

    return $mycard->html;
}

# CRONJOB
# tạo hook cronjob để refresh_token hàng ngày.
add_action( 'daily_refresh_token', 'refresh_token' );
# check những đơn hết hạn thanh toán hàng ngày
add_action( 'daily_check_payment_status', 'check_payment_status' ); 

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