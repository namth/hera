<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php bloginfo('name'); ?>" name="description">
    <title><?php bloginfo('name'); ?> &raquo; <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>

    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.png" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.png" type="image/x-icon"/>

    <?php
    wp_head();
    ?>
</head>

<body>

<?php
$userid = $_GET['u'];
$number_of_click = get_field('number_of_clicks', 'user_' . $userid);
if (is_numeric($userid)) {
    // tăng số đếm khi click vào link theo userid
    update_field('field_63eb41b276ba7', $number_of_click + 1, 'user_' . $userid);

    // lưu userid vào cookie 1 tháng cho đến khi user thanh toán 
    setcookie('partner', $userid, time() + 2592000, '/');

}