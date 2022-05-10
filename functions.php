<?php
// function
register_nav_menus(array('main-menu' => esc_html__('Main Menu', 'inovacards')));
add_theme_support('title-tag');

add_action('wp_enqueue_scripts', 'inovacards_load_scripts');
function inovacards_load_scripts()
{
    /** Call design-cards enqueue */
    
    /* Css */
    wp_enqueue_style('main-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('mui', get_template_directory_uri() . '/css/mui.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');
    wp_enqueue_style('inova', get_template_directory_uri() . '/css/inova.css');
    
    /* Js */
    wp_enqueue_script('jquery');
    wp_enqueue_script('mui', get_template_directory_uri() . '/js/mui.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('inova', get_template_directory_uri() . '/js/inova.js', array('jquery', 'mui'), '1.0', true);
    // wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/dfe5b27416.js', array(), '4.0', true);
    wp_localize_script('inova', 'AJAX', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

/* Redirect after logout */
add_action('wp_logout', 'ps_redirect_after_logout');
function ps_redirect_after_logout()
{
    wp_redirect(get_bloginfo('url'));
    exit();
}

// Add custom Theme Functions here
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'    => 'Theme options', // Title hiển thị khi truy cập vào Options page
        'menu_title'    => 'Tùy biến chung', // Tên menu hiển thị ở khu vực admin
        'menu_slug'     => 'theme-settings', // Url hiển thị trên đường dẫn của options page
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

add_action('wp_ajax_edit_guest', 'edit_guest');
add_action('wp_ajax_nopriv_edit_guest', 'edit_guest');
function edit_guest(){
    $groupid = $_POST['groupid'];
    $guestid = $_POST['guestid'];

    if (have_rows('guest_list', $groupid)) {
        while (have_rows('guest_list', $groupid)) {
            the_row();

            $stt = get_sub_field('stt');
            if ($stt == $guestid) {
                $xung_ho = explode('/', get_sub_field('xung_ho'));
                $data = array(
                    'name'          => get_sub_field('name'),
                    'guest_attach'  => get_sub_field('guest_attach'),
                    'mine'          => $xung_ho[0],
                    'your'          => $xung_ho[1],
                    'phone'         => get_sub_field('phone'),
                );
                break;
            }
        }
    }

    echo json_encode($data);
    exit;
}