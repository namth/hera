<?php
/* 
* Template Name: View Card Demo
*/
$_group         = get_query_var('group');
$group          = inova_encrypt($_group, 'd');

$cardid         = get_field('card_id', $group);
$html           = get_field('html', $group);
$noi_dung_1     = get_field('content_1', $group)?get_field('content_1', $group):get_field('content_1', 'option');
$noi_dung_2     = get_field('content_2', $group)?get_field('content_2', $group):get_field('content_2', 'option');
$noi_dung_3     = get_field('content_3', $group)?get_field('content_3', $group):get_field('content_3', 'option');
$loi_moi        = get_field('custom_invite', $group)?get_field('custom_invite', $group):get_field('wedding_invitation', 'option');

$groom          = get_field('groom', 'option');
$bride          = get_field('bride', 'option');

$wedding_adress = get_field('wedding_address', 'option');
$wedding_time   = explode(' ',get_field('wedding_time', 'option'));
$xung_ho        = get_field('sample_guest_relationship', 'option');
$vai_ve         = get_field('sample_guest_title', 'option');
$guests         = get_field('sample_guest', 'option');

# function button or show result when customer accept invite or deny invite
$function_div = '<div id="function_action">
        <a href="#" class="accept_invite invitation" data-answer="Y">Đi được nhé</a>
        <a href="#" class="deny_invite invitation" data-answer="N">Bận mất rồi</a>
    </div>';
if ($joined == "Y") {
    $function_div .= '<div class="notification accept">Đã xác nhận tham dự.</div>';
} else if ($joined == "N") {
    $function_div .= '<div class="notification deny">Xác nhận không tham dự được.</div>';
}

# setup wp_head & wp_footer
$wp_head    = echo_to_string('wp_head');

# data to get response from guests
$data_input =  '<input type="hidden" name="group" value="' . $_group . '">
                <input type="hidden" name="invitee" value="' . $_invitee . '">';
$wp_footer  = $data_input . echo_to_string('wp_footer');


$data_replace = array(
    '{noi_dung_1}'  => $noi_dung_1,
    '{noi_dung_2}'  => $noi_dung_2,
    '{noi_dung_3}'  => $noi_dung_3,
    '{loi_moi}'     => wpautop($loi_moi),
    '{khach_moi}'   => $guests,
    '{chu_re}'      => $groom,
    '{co_dau}'      => $bride,
    '{ngay_duong_lich_dam_cuoi}' => $wedding_time[0],
    '{gio_dam_cuoi}' => $wedding_time[1],
    '{dia_diem_dam_cuoi}'       => $wedding_adress,
    '{google_maps_dam_cuoi}'    => $button_maps_dam_cuoi,
    '{ngay_duong_lich_an_co}'   => $wedding_time[0],
    '{gio_an_co}'   => $wedding_time[1],
    '{dia_diem_an_co}'          => $wedding_adress,
    '{google_maps_an_co}'       => $button_maps_an_co,
    '{function_button}'         => $function_div,
    '{wp_header}'   => $wp_head,
    '{wp_footer}'   => $wp_footer,
    '{2}'           => strtolower($vai_ve),
    '{1}'           => strtolower($xung_ho),
);

if ($cardid) {
    
    $html = replace_content($data_replace, $html);
    print_r($html);

} else {
    echo "Lỗi trang 404.";
}
    

