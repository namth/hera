<?php

add_action('rest_api_init', function (){
    # Hiển thị số lượng coupon còn lại dựa trên id của coupon đó
    register_rest_route('hera/v1', 'getcoupon', array(
        'methods'   => 'POST',
        'callback'  => 'get_coupon',
    ));
});

function get_coupon(){
    $coupon = $_POST['coupon'];
    $quantity = get_field('coupon_quantity', $coupon);
    return $quantity;
}

