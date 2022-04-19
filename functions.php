<?php
// function
register_nav_menus(array('main-menu' => esc_html__('Main Menu', 'inovacards')));
add_theme_support('title-tag');

add_action('wp_enqueue_scripts', 'inovacards_load_scripts');
function inovacards_load_scripts()
{
    /* Css */
    wp_enqueue_style('main-style', get_template_directory_uri() . '/style.css');

    /* Js */
    wp_enqueue_script('jquery');

    /** Call design-cards enqueue */
    if (is_page('design-cards')) {
        /* CSS */
        wp_enqueue_style( 'toastr', get_template_directory_uri() . '/css/toastr.min.css' );
        wp_enqueue_style( 'grapes', get_template_directory_uri() . '/css/grapes.min.css' );
        wp_enqueue_style( 'grapesjs-preset-webpage', get_template_directory_uri() . '/css/grapesjs-preset-webpage.min.css' );
        wp_enqueue_style( 'grapesjs-plugin-filestack', get_template_directory_uri() . '/css/grapesjs-plugin-filestack.css' );
        wp_enqueue_style( 'demos', get_template_directory_uri() . '/css/demos.css' );
        wp_enqueue_style( 'grapick', 'https://unpkg.com/grapick/dist/grapick.min.css' );
        /* JS */
        wp_enqueue_script( 'toastr', get_template_directory_uri() . '/js/toastr.min.js', array('jquery'), '1.0', true );
        wp_enqueue_script( 'grapes', get_template_directory_uri() . '/js/grapes.min.js', array('jquery'), 'v0.17.19', true );
        wp_enqueue_script( 'grapesjs-preset-webpage', get_template_directory_uri() . '/js/grapesjs-preset-webpage.min.js', array('jquery'), 'v0.1.11', true );
        wp_enqueue_script( 'grapesjs-lory-slider', get_template_directory_uri() . '/js/grapesjs-lory-slider.min.js', array('jquery'), '0.1.5', true );
        wp_enqueue_script( 'grapesjs-custom-code', get_template_directory_uri() . '/js/grapesjs-custom-code.min.js', array('jquery'), '0.1.2', true );
        wp_enqueue_script( 'grapesjs-touch', get_template_directory_uri() . '/js/grapesjs-touch.min.js', array('jquery'), '0.1.1', true );
        wp_enqueue_script( 'grapesjs-parser-postcss', get_template_directory_uri() . '/js/grapesjs-parser-postcss.min.js', array('jquery'), '0.1.1', true );
        wp_enqueue_script( 'grapesjs-tui-image-editor', get_template_directory_uri() . '/js/grapesjs-tui-image-editor.min.js', array('jquery'), '0.1.2', true );
        wp_enqueue_script( 'grapesjs-style-bg', get_template_directory_uri() . '/js/grapesjs-style-bg.min.js', array('jquery'), '1.0.1', true );
        wp_enqueue_script( 'grapesjs-plugin-ckeditor', get_template_directory_uri() . '/js/grapesjs-plugin-ckeditor.min.js', array('jquery'), '1.0', true );
        wp_enqueue_script( 'ckeditor', get_template_directory_uri() . '/js/ckeditor/ckeditor.js', array('jquery'), '1.0.0', true );
        wp_enqueue_script( 'custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '1.0', true );
        wp_localize_script( 'custom', 'AJAX', array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ));
    } else {
        wp_enqueue_style( 'mui', get_template_directory_uri() . '/css/mui.min.css' );
        wp_enqueue_style( 'inova', get_template_directory_uri() . '/css/inova.css' );

        wp_enqueue_script( 'mui', get_template_directory_uri() . '/js/mui.min.js', array('jquery'), '1.0', true );
        wp_enqueue_script( 'inova', get_template_directory_uri() . '/js/inova.js', array('jquery', 'mui'), '1.0', true );
        wp_enqueue_script( 'font-awesome', 'https://kit.fontawesome.com/dfe5b27416.js', array( ), '4.0', true );
    }
}

/* Update plain text */
function update_plain_field($field_key = '', $field_name = '', $plain = '', $postid = '')
{
    global $wpdb;
    $table = $wpdb->prefix . 'postmeta';
    $field = $wpdb->get_results("SELECT * FROM $table WHERE post_id = '$postid' AND meta_key = '$field_name'");
    if ($field) {
        $wpdb->update(
            $table,
            array(
                'meta_value' => serialize($plain)
            ),
            array(
                'post_id' => $postid,
                'meta_key'=> $field_name
            ),
            array('%s')
        );
    } else {
        $wpdb->insert(
            $table,
            array(
                'post_id' => $postid,
                'meta_key' => $field_name,
                'meta_value' => serialize($plain),
            ),
            array(
                '%d',
                '%s',
                '%s',
            )
        );
        $wpdb->insert(
            $table,
            array(
                'post_id' => $postid,
                'meta_key' => '_' . $field_name,
                'meta_value' => serialize($plain)
            ),
            array(
                '%d',
                '%s',
                '%s',
            )
        );

    }
}

/* Redirect after logout */
add_action('wp_logout','ps_redirect_after_logout');
function ps_redirect_after_logout(){
         wp_redirect( get_bloginfo('url') );
         exit();
}