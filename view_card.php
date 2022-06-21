<?php
/* 
* Template Name: View Card 
*/
    $cardid = 21;

    $token = refresh_token();
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $cardid;
    $mycard = inova_api($api_url, $token, 'GET', '');

    $user_login = get_query_var('myacc');
    $group = get_query_var('group');
    $customer = get_query_var('invitee');


    // get_header();
    print_r($mycard->html);

    // get_footer();