<?php
/* 
* Template Name: View Card 
*/
$user_login = get_query_var('myacc');
$user = get_user_by('login', $user_login);
$_group = get_query_var('group');
$_invitee = get_query_var('invitee');
$group = inova_encrypt($_group, 'd');
$customer = inova_encrypt($_invitee, 'd');

$cardid         = get_field('card_id', $group);
$html           = get_field('html', $group);
$noi_dung_1     = get_field('content_1', $group)?get_field('content_1', $group):get_field('content_1', 'option');
$noi_dung_2     = get_field('content_2', $group)?get_field('content_2', $group):get_field('content_2', 'option');
$noi_dung_3     = get_field('content_3', $group)?get_field('content_3', $group):get_field('content_3', 'option');
$loi_moi        = get_field('custom_invite', $group)?get_field('custom_invite', $group):get_field('wedding_invitation', 'option');
$found_customer = false;

$groom = get_field('groom', 'user_' . $user->ID);
$bride = get_field('bride', 'user_' . $user->ID);

# Đọc category xem thiệp thuộc nhà trai hay nhà gái. 
$category = get_the_category($group);
$category_name = $category[0]->name;

if ($category_name == "Nhà gái") {
    $wedding_adress = get_field('bride_wedding_adress', 'user_' . $user->ID);
    $wedding_time = explode(' ',get_field('bride_wedding_time', 'user_' . $user->ID));
    $party_adress = get_field('bride_party_address', 'user_' . $user->ID);
    $party_time = explode(' ',get_field('bride_party_time', 'user_' . $user->ID));
    $google_maps_dam_cuoi = get_field('bride_wedding_maps', 'user_' . $user->ID);
    $google_maps_an_co = get_field('bride_party_maps', 'user_' . $user->ID);
} else {
    $wedding_adress = get_field('groom_wedding_adress', 'user_' . $user->ID);
    $wedding_time = explode(' ',get_field('groom_wedding_time', 'user_' . $user->ID));
    $party_adress = get_field('groom_party_address', 'user_' . $user->ID);
    $party_time = explode(' ',get_field('groom_party_time', 'user_' . $user->ID));
    $google_maps_dam_cuoi = get_field('groom_wedding_maps', 'user_' . $user->ID);
    $google_maps_an_co = get_field('groom_party_maps', 'user_' . $user->ID);
}

if ($google_maps_dam_cuoi) {
    $button_maps_dam_cuoi = '<a href="https://www.google.com/maps/dir/?api=1&destination=' . $google_maps_dam_cuoi . '" class="googlemaps keychainify-checked" target="_blank">Chỉ đường</a>
                            <a href="https://maps.google.com/?q=' . $google_maps_dam_cuoi . '" class="googlemaps" id="googlemaps" target="_blank">Xem đường đi</a>';
}

if ($google_maps_an_co) {
    $button_maps_an_co = '<a href="https://www.google.com/maps/dir/?api=1&destination=' . $google_maps_an_co . '" class="googlemaps keychainify-checked" target="_blank">Chỉ đường</a>
                            <a href="https://maps.google.com/?q=' . $google_maps_an_co . '" class="googlemaps" id="googlemaps" target="_blank">Xem đường đi</a>';
}

# Lấy các thông tin cần thiết để replace vào thiệp
if (have_rows('guest_list', $group)) {
    while (have_rows('guest_list', $group)) {
        the_row();

        $row_index = get_row_index();
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
$wp_head    = echo_to_string('wp_head');

# data to get response from guests
$data_input =  '<input type="hidden" name="group" value="' . $_group . '">
                <input type="hidden" name="invitee" value="' . $_invitee . '">';
$wp_footer  = $data_input . echo_to_string('wp_footer');
/* $loi_moi = replace_content([
    '{2}'           => strtolower($xung_ho[0]),
    '{1}'           => strtolower($xung_ho[1]),
], $loi_moi);

$noi_dung_2 = replace_content([
    '{2}'           => strtolower($xung_ho[0]),
    '{1}'           => strtolower($xung_ho[1]),
], $noi_dung_2);
 */
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
    '{ngay_duong_lich_an_co}'   => $party_time[0],
    '{gio_an_co}'   => $party_time[1],
    '{dia_diem_an_co}'          => $party_adress,
    '{google_maps_an_co}'       => $button_maps_an_co,
    '{function_button}'         => $function_div,
    '{wp_header}'   => $wp_head,
    '{wp_footer}'   => $wp_footer,
    '{2}'           => strtolower($xung_ho[0]),
    '{1}'           => strtolower($xung_ho[1]),
    '{ten}'         => $name,
);
if ($cardid) {
    
    $html = replace_content($data_replace, $html);
    print_r($html);

} else {
    echo "Lỗi trang 404.";
}
    

