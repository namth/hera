<?php
add_action('init','all_my_hooks');
function all_my_hooks(){
    $dir = dirname( __FILE__ );
    # API function library
    require_once( $dir . '/inc/api_function.php');
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
    $groupid    = $_POST['groupid'];
    $guestid    = $_POST['guestid'];
    $data       = array();

    if (have_rows('guest_list', $groupid)) {
        while (have_rows('guest_list', $groupid)) {
            the_row();

            $stt = get_sub_field('stt');
            if ($stt == $guestid) {
                $xung_ho = explode('/', get_sub_field('xung_ho'));
                $data = array(
                    'id'            => $guestid,
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

/*
* Setup view to display detail card when customer click to each card.
*/ 
add_action('wp_ajax_viewDetailCard', 'viewDetailCard');
add_action('wp_ajax_nopriv_viewDetailCard', 'viewDetailCard');
function viewDetailCard(){
    $cardid = $_POST['cardid'];
    $token = get_token();
    $api_url = 'https://design.inova.ltd/wp-json/inova/v1/card/' . $cardid;
    $mycard = inova_api($api_url, $token, 'GET', '');

    ?>
    <div class="mui-row" id="detail_card_popup">
        <div class="mui-col-md-9 card_thumbnail">
            <img src="<?php echo $mycard->thumbnail; ?>" alt="">
        </div>
        <div class="mui-col-md-3" id="detail_data_box">
            <h2><?php echo $mycard->title; ?></h2>
            
            <div class="button_group">
                <button class="mui-btn mui-btn--raised"><i class="fa fa-heart"></i></button>
                <button class="mui-btn mui-btn--raised"><i class="fa fa-star" aria-hidden="true"></i> Lưu</button>
                <button class="mui-btn mui-btn--raised"><i class="fa fa-share-alt" aria-hidden="true"></i> Chia sẻ</button>
            </div>
            <button class="mui-btn mui-btn--raised mui-btn--primary fullwidth"><i class="fa fa-map" aria-hidden="true"></i> Thử mẫu thiệp này</button>
        </div>
    </div>
    <?php
    exit;
}