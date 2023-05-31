<?php
$general_dir = dirname( __FILE__ );
require_once( $general_dir . '/custom_post.php');
require_once( $general_dir . '/custom_field.php');

/* Xử lý hàm liên quan tới thanh toán */
// require_once( $general_dir . '/secret.php');
require_once( $general_dir . '/order.php');

# convert time
function convert_time($time) {
    $time_arr = explode(':', $time);

    if ($time_arr[0] > 12) {
        $time_arr[0] -= 12;
        $symbol = ' PM';
    } else $symbol = ' AM';

    return implode(':', $time_arr) . $symbol;
}