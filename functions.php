<?php
add_action('init','all_my_hooks');
function all_my_hooks(){
    $dir = dirname( __FILE__ );
    require_once( $dir . '/inc/custom_post.php');
    require_once( $dir . '/inc/custom_field.php');

    # function library
    require_once( $dir . '/inc/api_function.php');
    require_once( $dir . '/inc/ajax_function.php');

    # Init SESSION
    if(!session_id()) {
        session_start();
    }
}

# mã hoá và giải mã 
function inova_encrypt($stringToHandle = "",$encryptDecrypt = 'e'){
    // Set secret keys
    $secret_key = 'INOVA'; // Change this!
    $secret_iv = 'HERA'; // Change this!
    $key = hash('sha256',$secret_key);
    $iv = substr(hash('sha256',$secret_iv),0,16);
    // Check whether encryption or decryption
    if($encryptDecrypt == 'e'){
       // We are encrypting
       $output = base64_encode(openssl_encrypt($stringToHandle,"AES-256-CBC",$key,0,$iv));
    }else if($encryptDecrypt == 'd'){
       // We are decrypting
       $output = openssl_decrypt(base64_decode($stringToHandle),"AES-256-CBC",$key,0,$iv);
    }

    return $output;
}

# Destroy session 
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');
function myEndSession() {
    session_destroy ();
}

// function
register_nav_menus(array('main-menu' => esc_html__('Main Menu', 'inovacards')));
add_theme_support('title-tag');

/*
* Call design-cards enqueue 
*/
add_action('wp_enqueue_scripts', 'inovacards_load_scripts');
function inovacards_load_scripts()
{
    /* Js */
    wp_enqueue_script('jquery');
    if (is_page(72)) {
        wp_enqueue_script('mycards', get_template_directory_uri() . '/js/mycards.js', array('jquery'), '1.0', true);
        wp_localize_script('mycards', 'AJAX', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    } else {
        /* Css */
        wp_enqueue_style('main-style', get_template_directory_uri() . '/style.css');
        wp_enqueue_style('mui', get_template_directory_uri() . '/css/mui.min.css');
        wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');
        wp_enqueue_style('inova', get_template_directory_uri() . '/css/inova.css');
        
        /* Js */
        wp_enqueue_script('mui', get_template_directory_uri() . '/js/mui.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('inova', get_template_directory_uri() . '/js/inova.js', array('jquery', 'mui'), '1.0', true);
        // wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/dfe5b27416.js', array(), '4.0', true);
        wp_localize_script('inova', 'AJAX', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
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

/* 
* rewrite rules for invited_link of each user
*/
function custom_rewrite_rules() {
	global $wp; 
    $wp->add_query_var('myacc');
    $wp->add_query_var('group');
    $wp->add_query_var('invitee');
    add_rewrite_rule('^myacc/(.*)/(.*)/(.*)?', 'index.php?page_id=72&myacc=$matches[1]&group=$matches[2]&invitee=$matches[3]', 'top');
}
add_action('init', 'custom_rewrite_rules');

add_action('init', 'do_output_buffer');
function do_output_buffer() {
    ob_start();
}

# replace content by any template
function replace_content($arr_replace, $content)
{
    if (is_array($arr_replace)) {
        foreach ($arr_replace as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        return $content;
    }
}

# Tìm kiếm bài post theo từ khoá nằm trong customfield cụ thể
# Tham số truyền vào $key: là tên customfield (dạng slug) không phải dạng field_...
# Giá trị trả về: post_ID hoặc false
function search_customfield($post_type, $search, $key){
    $args   = array(
        'post_type' => $post_type,
        'meta_query' => array(
            array(
                'key'     => $key,
                'value'   => $search,
                'compare' => '='
            ),
        )
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            return get_the_ID();
        } wp_reset_postdata();
    } else return false;
}

