<?php 
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
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