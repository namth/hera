<?php
/* 
* Template Name: Affiliate check
*/
// đọc dữ liệu userid
$userid = $_GET['u'];
$number_of_click = get_field('number_of_clicks', 'user_' . $userid);
if (is_numeric($userid)) {
    // tăng số đếm khi click vào link theo userid
    update_field('field_63eb41b276ba7', $number_of_click + 1, 'user_' . $userid);

    // lưu userid vào cookie 1 tháng cho đến khi user thanh toán 
    setcookie('partner', $userid, time() + 2592000, '/');

}
// Chuyển trang sang trang chính 
wp_redirect(get_bloginfo('url'));
exit;