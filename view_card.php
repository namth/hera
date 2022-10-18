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

$cardid = get_field('card_id', $group);
$found_customer = false;

$groom = get_field('groom', 'user_' . $user->ID);
$bride = get_field('bride', 'user_' . $user->ID);

# Đọc category xem thiệp thuộc nhà trai hay nhà gái. 
$category = get_the_category($group);
$category_name = $category[0]->name;

if ($category_name = "Nhà gái") {
    $wedding_adress = get_field('bride_wedding_adress', 'user_' . $user->ID);
    $wedding_time = get_field('bride_wedding_time', 'user_' . $user->ID);
} else {
    $wedding_adress = get_field('groom_wedding_adress', 'user_' . $user->ID);
    $wedding_time = get_field('groom_wedding_time', 'user_' . $user->ID);
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

$data_replace = array(
    '{khach_moi}'   => $guests,
    '{chu_re}'      => $groom,
    '{co_dau}'      => $bride,
    '{ngay_gio_cuoi_hoi}' => $wedding_time,
    '{dia_diem_to_chuc}'  => $wedding_adress,
    '{2}'           => strtolower($xung_ho[0]),
    '{1}'           => strtolower($xung_ho[1]),
    '{function_button}'   => $function_div,
);

if ($cardid) {
    $token = refresh_token();
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $cardid;
    $mycard = inova_api($api_url, $token, 'GET', '');
    ?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <title><?php bloginfo('name'); ?> &raquo; <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
        <meta content="Thiệp cưới online cao cấp HERA" name="description">

        <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.png" type="image/x-icon"/>
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.png" type="image/x-icon"/>
        <?php echo $mycard->css; ?>

        <?php 
            if ($mycard->type != 'HTML') {
        ?>
        <style>
            #function_action a {
                display: inline-block;
                text-decoration: none;
                border-radius: 5px;
                padding: 12px 20px;
                background: darkslategray;
                color: #ffffe9;
                font-family: arial;
                margin: 0px 5px;
            }
            .notification{
                margin: 21px 0;
                padding: 12px;
                font-family: arial;
                font-style: italic;
                border-radius: 9px;
                display: inline-block;
                min-width: 69%;
            }
            .accept{
                background: #acffa0;
                color: #029520;
            }
            .deny{
                background: #ffd5d5;
                color: #950202;
            }
        </style>
        <?php 
            }
            wp_head() 
        ?>
    </head>
        <body>
        <?php
            $html = replace_content($data_replace, $mycard->html);
            print_r($html);

            # data to read js
            echo '<input type="hidden" name="group" value="' . $_group . '">';
            echo '<input type="hidden" name="invitee" value="' . $_invitee . '">';
        ?>
        </body>
        <?php 
            echo $mycard->js;
            wp_footer(); 
        ?>
    </html>
    <?php
} else {
    echo "Lỗi trang 404.";
}
    

