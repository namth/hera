<?php
/* 
* Template Name: View Card Demo
*/
$_group         = get_query_var('group');
if ($_group) {
    $group          = inova_encrypt($_group, 'd');
    
    $cardid         = get_field('card_id', $group);
    $html           = get_field('html', $group);
    $noi_dung_1     = get_field('content_1', $group)?get_field('content_1', $group):get_field('content_1', 'option');
    $noi_dung_2     = get_field('content_2', $group)?get_field('content_2', $group):get_field('content_2', 'option');
    $noi_dung_3     = get_field('content_3', $group)?get_field('content_3', $group):get_field('content_3', 'option');
    $loi_moi        = get_field('custom_invite', $group)?get_field('custom_invite', $group):get_field('wedding_invitation', 'option');
} else {
    $cardid         = $_GET['cardid'];
    $token = get_field('token', 'option');
    if (!check_token($token)) {
        # Kiểm tra nếu token vẫn hoạt động thì thôi, nếu không thì phải lấy lại token mới.
        $token = refresh_token();
    }
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $cardid . '?status=private';
    $mycard = inova_api($api_url, $token, 'GET', '');

    $html           = $mycard->html;
    $noi_dung_1     = $mycard->content1 ? $mycard->content1 : get_field('content_1', 'option');
    $noi_dung_2     = $mycard->content2 ? $mycard->content2 : get_field('content_2', 'option');
    $noi_dung_3     = $mycard->content3 ? $mycard->content3 : get_field('content_3', 'option');
    $loi_moi        = $mycard->content4 ? $mycard->content4 : get_field('wedding_invitation', 'option');
}

# co dau chu re
$groom          = get_field('groom', 'option');
$bride          = get_field('bride', 'option');
# bo me co dau chu re 
$groom_father   = get_field('groom_father', 'option');
$groom_mother   = get_field('groom_mother', 'option');
$bride_father   = get_field('bride_father', 'option');
$bride_mother   = get_field('bride_mother', 'option');

$wedding_adress = get_field('wedding_address', 'option');
$wedding_time   = explode(' ',get_field('wedding_time', 'option'));
# sun-day wedding time
$time_object        = DateTime::createFromFormat('d/m/Y', $wedding_time[0]);
$wedding_dayname    = DayName($time_object->format('w'));
$wedding_day        = $time_object->format('d');
$wedding_month      = $time_object->format('m');
$wedding_year       = $time_object->format('Y');
# moon-day wedding time
$time_object            = DateTime::createFromFormat('d/m/Y', ShowLunarDate($time_object, 'dd/mm/YYYY'));
$wedding_moon_date      = ShowLunarDate($time_object, 'ngày dd tháng mm năm MYMY');
$wedding_moon_day       = $time_object->format('d');
$wedding_moon_month     = $time_object->format('m');
$wedding_moon_year      = $time_object->format('Y');
$wedding_moonyear_text  = ConvertMoonYear($party_moon_year);

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
    '{noi_dung_1}'                  => $noi_dung_1,
    '{noi_dung_2}'                  => $noi_dung_2,
    '{noi_dung_3}'                  => $noi_dung_3,
    '{loi_moi}'                     => wpautop($loi_moi),
    '{khach_moi}'                   => $guests,
    '{chu_re}'                      => ucwords($groom),
    '{co_dau}'                      => ucwords($bride),
    '{G}'                           => nameLetter($groom),
    '{B}'                           => nameLetter($bride),
    '{ngay_duong_lich_dam_cuoi}'    => $wedding_time[0],
    '{ngay_am_lich_dam_cuoi}'       => $wedding_moon_date,
    '{gio_dam_cuoi}'                => $wedding_time[1],
    '{dia_diem_dam_cuoi}'           => $wedding_adress,
    '{google_maps_dam_cuoi}'        => $button_maps_dam_cuoi,
    '{ngay_duong_lich_an_co}'       => $wedding_time[0],
    '{ngay_am_lich_an_co}'          => $wedding_moon_date,
    '{gio_an_co}'                   => convert_time($wedding_time[1]),
    '{dia_diem_an_co}'              => $wedding_adress,
    '{google_maps_an_co}'           => $button_maps_an_co,
    '{function_button}'             => $function_div,
    '{wp_header}'                   => $wp_head,
    '{wp_footer}'                   => $wp_footer,
    '{2}'                           => strtolower($vai_ve),
    '{1}'                           => strtolower($xung_ho),
    '{ten}'                         => $name,
    '{wedding_dayname}'             => $wedding_dayname,
    '{wedding_day}'                 => $wedding_day,
    '{wedding_month}'               => $wedding_month,
    '{wedding_year}'                => $wedding_year,
    '{wedding_moon_day}'            => $wedding_moon_day,
    '{wedding_moon_month}'          => $wedding_moon_month,
    '{wedding_moon_year}'           => $wedding_moon_year,
    '{wedding_moon_year_text}'      => $wedding_moonyear_text,
    '{party_dayname}'               => $wedding_dayname,
    '{party_day}'                   => $wedding_day,
    '{party_month}'                 => $wedding_month,
    '{party_year}'                  => $wedding_year,
    '{party_moon_day}'              => $wedding_moon_day,
    '{party_moon_month}'            => $wedding_moon_month,
    '{party_moon_year}'             => $wedding_moon_year,
    '{party_moon_year_text}'        => $wedding_moonyear_text,
    '{bo_co_dau}'                   => $bride_father,
    '{me_co_dau}'                   => $bride_mother,
    '{bo_chu_re}'                   => $groom_father,
    '{me_chu_re}'                   => $groom_mother,
);

if ($cardid) {
    
    $html = replace_content($data_replace, $html);
    print_r($html);

} else {
    echo "Lỗi trang 404.";
}
    

