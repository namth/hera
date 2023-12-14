<?php
/* 
* Template Name: View Card 
*/
$group = get_query_var('group');
$character = get_query_var('character');
$customer = get_query_var('invitee');
$key_html = $_GET['key'];

echo $key_html;

$user_login = get_post_field('post_author', $group);
$user = get_user_by('ID', $user_login);
$hera_character = get_user_meta($user_login, 'hera_wedding_character', true);
$_group = inova_encrypt($group, 'e');
$package_id = get_field('package_id', 'user_' . $user->ID);
if(!($package_id || is_user_logged_in())){
    wp_redirect(get_permalink(5));
    exit;
}

$cardid         = get_field('card_id', $group);
if (!$key_html) {
    $html           = get_field('html', $group);
} else {
    $token = get_field('token', 'option');
    if (!check_token($token)) {
        # Kiểm tra nếu token vẫn hoạt động thì thôi, nếu không thì phải lấy lại token mới.
        $token = refresh_token();
    }
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $cardid . '/print_card_temp';
    $mycard = inova_api($api_url, $token, 'GET', '');

    $html           = $mycard->html;
}
$noi_dung_1     = get_field('content_1', $group)?get_field('content_1', $group):get_field('content_1', 'option');
$noi_dung_2     = get_field('content_2', $group)?get_field('content_2', $group):get_field('content_2', 'option');
$noi_dung_3     = get_field('content_3', $group)?get_field('content_3', $group):get_field('content_3', 'option');
$loi_moi        = get_field('custom_invite', $group)?get_field('custom_invite', $group):get_field('wedding_invitation', 'option');
$found_customer = false;

# co dau chu re
$groom = get_field('groom', 'user_' . $user->ID);
$bride = get_field('bride', 'user_' . $user->ID);
# anh cuoi
$wedding_photo = get_field('wedding_photo', 'user_' . $user->ID);
# bo me co dau chu re
$groom_father   = get_field('groom_father', 'user_' . $user->ID);
$groom_mother   = get_field('groom_mother', 'user_' . $user->ID);
$bride_father   = get_field('bride_father', 'user_' . $user->ID);
$bride_mother   = get_field('bride_mother', 'user_' . $user->ID);

# Đọc category xem thiệp thuộc nhà trai hay nhà gái. 
$category = get_the_category($group);
$category_name = $category[0]->name;

if ($category_name == "Nhà gái") {
    $wedding_location   = get_field('bride_wedding_location', 'user_' . $user->ID);
    $wedding_address    = get_field('bride_wedding_address', 'user_' . $user->ID);
    $wedding_time       = explode(' ',get_field('bride_wedding_time', 'user_' . $user->ID));
    $wedding_moontime   = explode(' ',get_field('bride_wedding_moontime', 'user_' . $user->ID));
    $party_location     = get_field('bride_party_location', 'user_' . $user->ID);
    $party_address      = get_field('bride_party_address', 'user_' . $user->ID);
    $party_time         = explode(' ',get_field('bride_party_time', 'user_' . $user->ID));
    $party_moontime     = explode(' ',get_field('bride_party_moontime', 'user_' . $user->ID));
    $google_maps_dam_cuoi = get_field('bride_wedding_maps', 'user_' . $user->ID);
    $google_maps_an_co  = get_field('bride_party_maps', 'user_' . $user->ID);
} else {
    $wedding_location   = get_field('groom_wedding_location', 'user_' . $user->ID);
    $wedding_address    = get_field('groom_wedding_address', 'user_' . $user->ID);
    $wedding_time       = explode(' ',get_field('groom_wedding_time', 'user_' . $user->ID));
    $wedding_moontime   = explode(' ',get_field('groom_wedding_moontime', 'user_' . $user->ID));
    $party_location     = get_field('groom_party_location', 'user_' . $user->ID);
    $party_address      = get_field('groom_party_address', 'user_' . $user->ID);
    $party_time         = explode(' ',get_field('groom_party_time', 'user_' . $user->ID));
    $party_moontime     = explode(' ',get_field('groom_party_moontime', 'user_' . $user->ID));
    $google_maps_dam_cuoi = get_field('groom_wedding_maps', 'user_' . $user->ID);
    $google_maps_an_co  = get_field('groom_party_maps', 'user_' . $user->ID);
}

# sun-day wedding time
if ($wedding_time[0]) {
    $time_object        = DateTime::createFromFormat('d/m/Y', $wedding_time[0]);
    $wedding_dayname    = DayName($time_object->format('w'));
    $wedding_day        = $time_object->format('d');
    $wedding_month      = $time_object->format('m');
    $wedding_year       = $time_object->format('Y');
    $wedding_moon_date  = ShowLunarDate($time_object, 'ngày dd tháng mm năm MYMY');
}
# moon-day wedding time
if ($wedding_moontime[0]) {
    $time_object            = DateTime::createFromFormat('d/m/Y', $wedding_moontime[0]);
    $wedding_moon_day       = $time_object->format('d');
    $wedding_moon_month     = $time_object->format('m');
    $wedding_moon_year      = $time_object->format('Y');
    $wedding_moonyear_text  = ConvertMoonYear($wedding_moon_year);
}

# sun-day party time
if ($party_time[0]) {
    $time_object        = DateTime::createFromFormat('d/m/Y', $party_time[0]);
    $party_dayname      = DayName($time_object->format('w'));
    $party_day          = $time_object->format('d');
    $party_month        = $time_object->format('m');
    $party_year         = $time_object->format('Y');
    $party_moon_date    = ShowLunarDate($time_object, 'ngày dd tháng mm năm MYMY');
}
# moon-day party time
if ($party_moontime[0]) {
    $time_object        = DateTime::createFromFormat('d/m/Y', $party_moontime[0]);
    $party_moon_day     = $time_object->format('d');
    $party_moon_month   = $time_object->format('m');
    $party_moon_year    = $time_object->format('Y');
    $party_moonyear_text  = ConvertMoonYear($party_moon_year);
}

if ($google_maps_dam_cuoi) {
    $button_maps_dam_cuoi = '<a href="https://www.google.com/maps/dir/?api=1&destination=' . $google_maps_dam_cuoi . '" class="googlemaps keychainify-checked" target="_blank">Chỉ đường</a>
                            <a href="https://maps.google.com/?q=' . $google_maps_dam_cuoi . '" class="googlemaps" id="googlemaps" target="_blank">Xem vị trí</a>';
}

if ($google_maps_an_co) {
    $button_maps_an_co = '<a href="https://www.google.com/maps/dir/?api=1&destination=' . $google_maps_an_co . '" class="googlemaps keychainify-checked" target="_blank">Chỉ đường</a>
                            <a href="https://maps.google.com/?q=' . $google_maps_an_co . '" class="googlemaps" id="googlemaps" target="_blank">Xem vị trí</a>';
}

# Lấy các thông tin cần thiết để replace vào thiệp
if (have_rows('guest_list', $group)) {
    while (have_rows('guest_list', $group)) {
        the_row();

        $row_index = get_sub_field('id');
        if ($row_index != $customer) {
            continue;
        } else {
            # nếu tìm thấy khách thì lấy thông tin rồi break ra khỏi vòng lặp
            $sent           = get_sub_field('sent');
            $joined         = get_sub_field('joined');
            $name           = get_sub_field('name');
            $guest_attach   = get_sub_field('guest_attach');
            $xung_ho        = explode('/', get_sub_field('xung_ho'));
            $found_customer = true;
            break;
        }
    }
}

if ($guest_attach) {
    $guests = $name . ' và ' . $guest_attach;
} else $guests = $name;

# function button or show result when customer accept invite or deny invite
$function_div = '<div id="function_action">
        <a href="#" class="accept_invite invitation" data-answer="Y">Đi được nhé</a>
        <a href="#" class="deny_invite invitation" data-answer="N">Bận mất rồi</a>
    </div>';
if ($joined =="Y") {
    $function_div .= '<div class="notification accept">Đã xác nhận tham dự.</div>';
} else if ($joined =="N") {
    $function_div .= '<div class="notification deny">Xác nhận không tham dự được.</div>';
}

# setup wp_head & wp_footer
$wp_head    = "<title>Đám cưới " . $groom . " và " . $bride . "</title>";
if ($wedding_photo) {
    $wp_head   .= "<meta property='og:image' content='" .  wp_get_attachment_url($wedding_photo) . "'/>";
}
$wp_head   .= echo_to_string('wp_head');

# data to get response from guests
$data_input =  '<input type="hidden" name="group" value="' . $_group . '">
                <input type="hidden" name="invitee" value="' . $customer . '">';
$wp_footer  = $data_input . echo_to_string('wp_footer');

# dữ liệu tuỳ chỉnh
$data_replace = array(
    '{noi_dung_1}'  => $noi_dung_1,
    '{noi_dung_2}'  => $noi_dung_2,
    '{noi_dung_3}'  => $noi_dung_3,
    '{loi_moi}'     => wpautop($loi_moi),
    '{khach_moi}'   => $guests,
    '{chu_re}'      => ucwords($groom),
    '{co_dau}'      => ucwords($bride),
    '{G}'           => nameLetter($groom),
    '{B}'           => nameLetter($bride),
    '{ngay_duong_lich_dam_cuoi}' => $wedding_time[0],
    '{ngay_am_lich_dam_cuoi}'    => $wedding_moon_date,
    '{gio_dam_cuoi}' => $wedding_time[1],
    '{wedding_location}'        => $wedding_location,
    '{dia_diem_dam_cuoi}'       => $wedding_address,
    '{google_maps_dam_cuoi}'    => $button_maps_dam_cuoi,
    '{ngay_duong_lich_an_co}'   => $party_time[0],
    '{ngay_am_lich_an_co}'      => $party_moon_date,
    '{gio_an_co}'   => convert_time($party_time[1]),
    '{party_location}'          => $party_location,
    '{dia_diem_an_co}'          => $party_address,
    '{google_maps_an_co}'       => $button_maps_an_co,
    '{function_button}'         => $function_div,
    '{wp_header}'   => $wp_head,
    '{wp_footer}'   => $wp_footer,
    '{2}'           => strtolower($xung_ho[0]),
    '{1}'           => strtolower($xung_ho[1]),
    '{ten}'         => $name,
    '{wedding_dayname}'     => $wedding_dayname,
    '{wedding_day}'         => $wedding_day,
    '{wedding_month}'       => $wedding_month,
    '{wedding_year}'        => $wedding_year,
    '{wedding_moon_day}'        => $wedding_moon_day,
    '{wedding_moon_month}'      => $wedding_moon_month,
    '{wedding_moon_year}'       => $wedding_moon_year,
    '{wedding_moon_year_text}'  => $wedding_moonyear_text,
    '{party_dayname}'     => $party_dayname,
    '{party_day}'         => $party_day,
    '{party_month}'       => $party_month,
    '{party_year}'        => $party_year,
    '{party_moon_day}'        => $party_moon_day,
    '{party_moon_month}'      => $party_moon_month,
    '{party_moon_year}'       => $party_moon_year,
    '{party_moon_year_text}'  => $party_moonyear_text,
    '{bo_co_dau}'   => $bride_father,
    '{me_co_dau}'   => $bride_mother,
    '{bo_chu_re}'   => $groom_father,
    '{me_chu_re}'   => $groom_mother,
);
if ($cardid) {
    
    $html = replace_content($data_replace, $html);
    print_r($html);

} else {
    echo "Lỗi trang 404.";
}