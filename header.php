<?php 
header('Access-Control-Allow-Origin: *');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php bloginfo('name'); ?> &raquo; <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
    <meta content="Thiệp cưới online HERA" name="description">

    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.png" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.png" type="image/x-icon"/>

    <?php
    wp_head();
    ?>
</head>

<body>