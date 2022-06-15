<?php
add_action('init','all_my_hooks');
function all_my_hooks(){
    $dir = dirname( __FILE__ );
    require_once( $dir . '/inc/custom_post.php');
    require_once( $dir . '/inc/custom_field.php');

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
    $token = get_field('token', 'option');
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/card/' . $cardid;
    $mycard = inova_api($api_url, $token, 'GET', '');

    $current_user_id = get_current_user_id();

    ?>
    <div class="mui-row" id="detail_card_popup">
        <div class="mui-col-md-9 card_thumbnail">
            <img src="<?php echo $mycard->thumbnail; ?>" alt="">
        </div>
        <div class="mui-col-md-3" id="detail_data_box">
            <h2><?php echo $mycard->title; ?></h2>
            <div class="mui-divider"></div>

            <div class="button_group">
                <button id="like" class="mui-btn mui-btn--raised"><i class="fa fa-heart-o"></i></button>
                <button class="mui-btn mui-btn--raised"><i class="fa fa-star-o" aria-hidden="true"></i> Lưu</button>
                <button class="mui-btn mui-btn--raised"><i class="fa fa-share-alt" aria-hidden="true"></i> Chia sẻ</button>
            </div>
            <div class="mui-divider"></div>

            <div class="card_content">
                <?php echo $mycard->content; ?>
            </div>
            <button id="select_card" class="mui-btn mui-btn--raised mui-btn--primary fullwidth"><i class="fa fa-map" aria-hidden="true"></i> Thử mẫu thiệp này</button>
            <div class="use_card">
                <form action="" method="post" style="display: none;">
                    <input type="hidden" name="cardid" value="<?php echo $cardid; ?>">
                    <input type="hidden" name="thumbnail" value="<?php echo $mycard->thumbnail; ?>">
                    <?php 
                    $icon = array(
                        'fa-user',
                        'fa-universal-access',
                        'fa-users',
                        'fa-user-o',
                        'fa-user-circle-o',
                        'fa-vcard-o',
                        'fa-odnoklassniki',
                        'fa-male',
                        'fa-female',
                        'fa-child',
                    );
                    $args   = array(
                        'post_type'     => 'thiep_moi',
                        'posts_per_page' => -1,
                        'author'        => $current_user_id,
                    );
                    $args['meta_query'] = array(
                        array(
                            'key'       => 'status',
                            'value'     => 'Running',
                            'compare'   => '=',
                        ),
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            
                            $groupid = get_the_ID();
                            $current_cardid = get_field('card_id');
                            if ($current_cardid == $cardid) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                    ?>
                    <div class="form_element">
                        <input type="checkbox" name="customer_group[]" id="<?php echo "group_" . $groupid; ?>" value="<?php echo $groupid; ?>" <?php echo $checked; ?>>
                        <label for="<?php echo "group_" . $groupid; ?>">
                            <div class="img_icon">
                                <i class="fa <?php echo $icon[array_rand($icon)]; ?>"></i>
                            </div>
                            <div class="customer_name">
                                <?php the_title(); ?>
                            </div>
                        </label>
                    </div>
                    <?php
                        }
                        wp_reset_postdata();
                    }
                    wp_nonce_field('post_nonce', 'post_nonce_field');
                    ?>
                    <div class="mui-row">
                        <button id="close_select_card" class="mui-btn mui-btn--raised"><i class="fa fa-close"></i></button>
                        <input type="submit" value="Chọn thiệp này" class="mui-btn mui-btn--raised mui-btn--primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    exit;
}

/* Cho phép khách chọn thiệp ở trang danh sách thiệp */
add_action('wp_ajax_addCardToCustomerGroup', 'addCardToCustomerGroup');
add_action('wp_ajax_nopriv_addCardToCustomerGroup', 'addCardToCustomerGroup');
function addCardToCustomerGroup(){
    $current_user_id = get_current_user_id();
    $data = parse_str($_POST['data'], $output);

    if (isset($output['post_nonce_field']) &&
        wp_verify_nonce($output['post_nonce_field'], 'post_nonce')) {
        
        # duyệt qua toàn bộ các nhóm, nếu có lựa chọn thì set, nếu không có lựa chọn thì xoá 
        $args   = array(
            'post_type'     => 'thiep_moi',
            'posts_per_page' => -1,
            'author'        => $current_user_id,
        );
        $args['meta_query'] = array(
            array(
                'key'       => 'status',
                'value'     => 'Running',
                'compare'   => '=',
            ),
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $groupid = get_the_ID();
                if (in_array($groupid, $output['customer_group'])) {
                    # nếu nhóm khách mời mà có trong danh sách lựa chọn thì set lại
                    update_field('field_610ead267af54', $output['cardid']); # setup cardid
                    update_field('field_610ead167af53', $output['thumbnail']); # setup thumbnail
                    $updated = true;
                } else {
                    # nếu không thì kiểm tra xem trường cardid nếu có dữ liệu thì xoá đi
                    $current_cardid = get_field('card_id');
                    if ($current_cardid && ($current_cardid == $output['cardid'])) {
                        update_field('field_610ead267af54', ''); # remove cardid
                        update_field('field_610ead167af53', ''); # remove thumbnail
                        $updated = true;
                    }
                }
            } wp_reset_postdata();
        }
    } 

    if ($updated) {
        echo '<div class="success_notification"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Đã chọn mẫu này cho các nhóm trên.</div>';
    } else echo '<div class="error_notification"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Đã xảy ra lỗi gì đó, vui lòng kiểm tra lại hoặc liên hệ với admin.</div>';
    exit;
}

/* Sửa tên của nhóm khách mời */
add_action('wp_ajax_updateCustomerGroup', 'updateCustomerGroup');
add_action('wp_ajax_nopriv_updateCustomerGroup', 'updateCustomerGroup');
function updateCustomerGroup(){
    $newTitle = strip_tags($_POST['content']);
    $guestid = $_POST['guestid'];
    if ( empty ( $newTitle ) ) {
        exit;
    }

    // if $new_title is defined, but it matches the current title, return
    if ( get_the_title($guestid) === $newTitle ) {
        exit;
    }

    $post_update = array(
        'ID'         => $guestid,
        'post_title' => $newTitle
    );

    wp_update_post( $post_update );

    exit;
}


add_action('wp_ajax_listCardFromAPI', 'listCardFromAPI');
add_action('wp_ajax_nopriv_listCardFromAPI', 'listCardFromAPI');
function listCardFromAPI() {
    if (isset($_POST['retoken']) && ($_POST['retoken'] == 1)) {
        $token = refresh_token();
    } else {
        $token = get_field('token', 'option');
    }
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/cards';
    $listcards = inova_api($api_url, $token, 'GET', '');
    // print_r($listcards);

    if ($listcards->code === "rest_forbidden") {
        ?>
        <div class="error_messages">
            <span>Xin lỗi, hệ thống đang không tải được mẫu. Hãy thử lại sau một lát nữa. <?php echo $token; ?></span>
            <button class="mui-btn hera-btn" id='reload_card'>Tải lại</button>
            <img src="<?php echo get_template_directory_uri() . '/img/f_loading.gif'; ?>" alt="" style="display: none;">
        </div>
        <?php
    } else {
        foreach ($listcards as $card) {

            if ($card->thumbnail) {
                $card_thumbnail = $card->thumbnail;
            } else {
                $card_thumbnail = get_template_directory_uri() . '/img/no-img.png';
            }

            $liked = $card->liked?$card->liked:0;
            $used = $card->used?$card->used:0;
        ?>
        <div class="mui-col-md-3">
            <div class="heracard">
                <div class="images" style="<?php 
                    echo 'background: url('. $card_thumbnail .') no-repeat 50% 50%;';
                    echo 'background-size: contain;';
                ?>">
                    
                </div>
                <div class="caption">
                    <div class="user_action">
                        <a href="#"><i class="fa fa-heart-o"></i><span>Thích</span></a>
                        <a href="#"><i class="fa fa-star-o"></i><span>Thêm vào danh sách yêu thích</span></a>
                        <a href="#"><i class="fa fa-share-alt"></i><span>Chia sẻ</span></a>
                    </div>
                    <div class="caption_title mui-col-md-12">
                        <span><?php echo $card->title; ?></span>
                        <!-- <div class="like_share">
                            <i class="fa fa-heart"></i> <?php echo $liked; ?>
                            <i class="fa fa-vcard-o"></i> <?php echo $used; ?>
                        </div> -->
                    </div>
                    <a href="#" class="viewcard" data-cardid="<?php echo $card->ID; ?>">
                        <div class="bg-overlay"></div>
                    </a>
                </div>
            </div>
        </div>
        <?php 
        }
    }
    exit;
}