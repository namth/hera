<?php

$ajax_dir = dirname( __FILE__ );
require_once( $ajax_dir . '/ajax/wedding-infomation.php');
require_once( $ajax_dir . '/ajax/list_cards.php');
require_once( $ajax_dir . '/ajax/payment.php');
require_once( $ajax_dir . '/ajax/single-thiep_moi.php');

/* 
* Source: single-thiep_moi.php
* Xử lý khi bấm vào nút sửa khách mời và sửa thông tin trên popup 
*/
add_action('wp_ajax_edit_guest', 'edit_guest');
add_action('wp_ajax_nopriv_edit_guest', 'edit_guest');
function edit_guest(){
    $groupid    = $_POST['groupid'];
    $guestid    = $_POST['guestid'];
    $data       = array();

    if (have_rows('guest_list', $groupid)) {
        while (have_rows('guest_list', $groupid)) {
            the_row();

            $row_index = get_row_index();
            if ($row_index == $guestid) {
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
* Source: single-thiep_moi.php
* Sửa tên của nhóm khách mời */
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

/* 
* Source: list_cards.php
* Liệt kê các mẫu thiệp thông qua API */
add_action('wp_ajax_listCardFromAPI', 'listCardFromAPI');
add_action('wp_ajax_nopriv_listCardFromAPI', 'listCardFromAPI');
function listCardFromAPI() {
    if (isset($_POST['retoken']) && ($_POST['retoken'] == 1)) {
        $token = refresh_token();
    } else {
        $token = get_field('token', 'option');
        # Kiểm tra nếu token vẫn hoạt động thì thôi, nếu không thì phải lấy lại token mới.
        if (!check_token($token)) {
            $token = refresh_token();
        }
    }
    $category = '?cat=1';
    if (isset($_POST['cat'])) {
        $category = '?cat=' . $_POST['cat'];
    } 
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/cards/' . $category;
    $listcards = inova_api($api_url, $token, 'GET', '');

    if ($listcards->code === "rest_forbidden") {
        ?>
        <div class="error_messages">
            <span>Xin lỗi, hệ thống đang không tải được mẫu. Hãy thử lại sau một lát nữa.</span>
            <button class="mui-btn hera-btn" id='reload_card'>Tải lại</button>
            <img src="<?php echo get_template_directory_uri() . '/img/f_loading.gif'; ?>" alt="" style="display: none;">
        </div>
        <?php
    } else if(!empty($listcards->errors)) {
        ?>
        <div class="error_messages">
            <span>Xin lỗi, server hệ thống đang phản hồi chậm. Hãy thử lại sau một lát nữa.</span>
            <button class="mui-btn hera-btn" id='reload_card'>Tải lại</button>
            <img src="<?php echo get_template_directory_uri() . '/img/f_loading.gif'; ?>" alt="" style="display: none;">
        </div>
        <?php
    } else{
        $current_user_id = get_current_user_id();
        $liked = get_user_meta($current_user_id, 'liked')?get_user_meta($current_user_id, 'liked'):array();
        $liked_arr = explode(',', $liked[0]);

        $cards = $listcards->cards;
        foreach ($cards as $card) {

            # lay hinh anh thiep
            if ($card->thumbnail) {
                $card_thumbnail = $card->thumbnail;
            } else {
                $card_thumbnail = get_template_directory_uri() . '/img/no-img.png';
            }

            $liked  = $card->liked?$card->liked:0;
            $used   = $card->used?$card->used:0;
            $icon   = in_array($card->ID, $liked_arr)?"fa-heart":"fa-heart-o";
        ?>
        <div class="mui-col-lg-3 mui-col-md-4 mui-col-sm-6">
            <div class="heracard">
                <div class="images" style="<?php 
                    echo 'background: url('. $card_thumbnail .') no-repeat 50% 50%;';
                    echo 'background-size: cover;';
                ?>">
                    
                </div>
                <div class="caption">
                    <div class="user_action">
                        <a href="#" class="like" data-card=<?php echo $card->ID; ?>><i class="fa <?php echo $icon . " heracard-" . $card->ID; ?>"></i><span>Thích</span></a>
                        <a href="#"><i class="fa fa-star-o"></i><span>Thêm vào danh sách yêu thích</span></a>
                        <a href="#"><i class="fa fa-share-alt"></i><span>Chia sẻ</span></a>
                    </div>
                    <div class="caption_title mui-col-md-12">
                        <span><?php echo $card->title; ?></span>
                        <div class="like_share">
                            <i class="fa fa-heart"></i> <?php echo $liked; ?>
                            <!-- <i class="fa fa-vcard-o"></i> <?php echo $used; ?> -->
                        </div>
                    </div>
                    <a href="#" class="viewcard heracard-<?php echo $card->ID; ?>" 
                        data-cardid="<?php echo $card->ID; ?>"
                        data-cardlink="<?php echo get_bloginfo('url') . "/thiep-moi/?c=" . inova_encrypt($card->ID, 'e'); ?>">
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

/* 
* Source: single-thiep_moi.php
* Xử lý ajax khi bấm nút xoá một khách mời trong một group
*/
add_action('wp_ajax_deleteCustomer', 'deleteCustomer');
add_action('wp_ajax_nopriv_deleteCustomer', 'deleteCustomer');
function deleteCustomer() {
    $data = json_decode(inova_encrypt($_POST['content'], 'd'));
    $used_cards = get_field('used_cards', 'user_' . $data->userid);

    /* Kiểm tra tính xác thực */
    if (wp_verify_nonce($data->nonce, 'delcustomer_' . $data->row_index)) {
        /* Delete customer from group post by id */
        delete_row('field_61066efde7dbc', $data->row_index, $data->groupid);
        /* Update used cards */
        update_field('field_63b853e50f9a8', --$used_cards, 'user_' . $data->userid);
        echo true;
    } else echo false;
    exit;
}


/* 
* Source: view_card.php
* Xử lý khi click vào nút đồng ý hoặc từ chối tham dự */
add_action('wp_ajax_acceptInvite', 'acceptInvite');
add_action('wp_ajax_nopriv_acceptInvite', 'acceptInvite');
function acceptInvite() {
    $group = inova_encrypt($_POST['group'], 'd');
    $invitee = $_POST['invitee'];
    $answer = $_POST['answer'];

    if (($answer=='Y')||($answer == 'N')) {
        # nếu đồng ý tham gia thì tìm người có mã số là $invitee trong nhóm $group để update 
        if (have_rows('guest_list', $group)) {
            while (have_rows('guest_list', $group)) {
                the_row();
        
                $row_index = get_sub_field('id');
                if ($row_index != $invitee) {
                    continue;
                } else {
                    # nếu tìm thấy khách thì update thông tin rồi break ra khỏi vòng lặp
                    update_sub_field('joined', $answer);
                    $found_customer = true;
                    break;
                }
                $found_customer= false;
            }
        } else $found_customer= false;
    }
    if ($found_customer) {
        $notification = $answer=='Y'?'<div class="accept notification">Đã xác nhận tham dự.</div>':'<div class="deny notification">Đã xác nhận không tham dự được.</div>';
        echo $notification;
    } else {
        echo '<div class="deny notification">Không thể cập nhật vào dữ liệu thiệp, hãy kiểm tra lại.</div>';
    }
    exit;
}

/* 
* Source: register.php
* Đăng ký tài khoản HERA
*/
add_action('wp_ajax_registerhera', 'registerhera');
add_action('wp_ajax_nopriv_registerhera', 'registerhera');
function registerhera() {
    $data = parse_str($_POST['data'], $output);
    if (isset($output['register_nonce_field']) &&
        wp_verify_nonce($output['register_nonce_field'], 'register_nonce')) {
        
        $user_login = sanitize_user($output["user_login"]);
        $user_email = sanitize_email($output["user_email"]);
        $user_pass = $output["user_pass"];

        if (is_email($user_email) && !username_exists($user_login) && !email_exists($user_email)) {
            $args = [
                'user_login'    => $user_login,
                'user_pass'     => $user_pass,
                'user_email'    => $user_email,
            ];

            $user = wp_insert_user($args);
            
            if ($user) {
                # Đăng nhập sau khi tạo tài khoản
                wp_set_current_user( $user, $user_login );
                wp_set_auth_cookie( $user, true, false );
                do_action( 'wp_login', $user_login, $user );

                # Sau đó chuyển về trang chủ
                print_r( get_bloginfo('url') );
                exit;
            }
        }
    }
}

/* 
* Source: register.php
* Kiểm tra username đã tồn tại trên hệ thống chưa
*/
add_action('wp_ajax_checkUsernameExist', 'checkUsernameExist');
add_action('wp_ajax_nopriv_checkUsernameExist', 'checkUsernameExist');
function checkUsernameExist() {
    $username = sanitize_user($_POST['user_login']);
    $check_user = username_exists($username);
    if (!$check_user) {
        $altered = preg_replace("/[^a-zA-Z.0-9]+/", "", $username);
        # Nếu chưa có tài khoản thì kiểm tra xem có hợp lệ không và thông báo
        if ((strlen($altered) >= 3) && validate_username($altered)) {
            print_r(false);
        } else {
            print_r("Tên đăng nhập phải có ít nhất 3 ký tự");
        }
    } else {
        print_r("Tài khoản này đã tồn tại hoặc tên tài khoản không hợp lệ.");
    }
    exit;
}
/* 
* Source: register.php
* Kiểm tra email đã tồn tại trên hệ thống chưa
*/
add_action('wp_ajax_checkEmailExist', 'checkEmailExist');
add_action('wp_ajax_nopriv_checkEmailExist', 'checkEmailExist');
function checkEmailExist() {
    $email = sanitize_user($_POST['user_email']);
    $check_email = email_exists($email);
    if (!$check_email) {
        # Nếu chưa có tài khoản thì kiểm tra xem có hợp lệ không và thông báo
        if ( is_email($email) ) {
            print_r(false);
        } else {
            print_r("Email không hợp lệ.");
        }
    } else {
        print_r("Email này đã được sử dụng.");
    }
    exit;
}

/* 
* Source: change-author-password.php
* Kiểm tra password có đúng chưa
*/
add_action('wp_ajax_checkPassword', 'checkPassword');
add_action('wp_ajax_nopriv_checkPassword', 'checkPassword');
function checkPassword() {
    $user_pass = sanitize_user($_POST['user_pass']);
    $current_user = wp_get_current_user();
    $check = wp_authenticate_username_password( NULL, $current_user->user_login, $user_pass );

    if($check->ID){
        print_r(false);
    } else {
        print_r("Mật khẩu không đúng.");
    }
    exit;
}

/* 
* Source: author.php
* Upload avatar
*/
add_action('wp_ajax_uploadAvatar', 'uploadAvatar');
add_action('wp_ajax_nopriv_uploadAvatar', 'uploadAvatar');
function uploadAvatar() {
    if( ! isset( $_FILES ) || empty( $_FILES ) || ! isset( $_FILES['files'] ) )
        return;

    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    $upload_overrides = array( 'test_form' => false );

    $files = $_FILES['files'];
    foreach ($files['name'] as $key => $value) {
      if ($files['name'][$key]) {
        $uploadedfile = array(
            'name'     => $files['name'][$key],
            'type'     => $files['type'][$key],
            'tmp_name' => $files['tmp_name'][$key],
            'error'    => $files['error'][$key],
            'size'     => $files['size'][$key]
        );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        if ( $movefile && !isset( $movefile['error'] ) ) {
            $filename = basename($movefile['url']);
            $attachment = array(
                'post_mime_type' => $movefile['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $movefile['file'] );
            
            # delete current avatar and add new avatar
            if ($attach_id) {
                # delete
                $userID = get_current_user_id();
                $link_avatar = get_avatar_url($userID, array('size' => '2000'));
                $current_attachment_id = attachment_url_to_postid($link_avatar);
                wp_delete_attachment($current_attachment_id);

                #update
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
                wp_update_attachment_metadata( $attach_id, $attach_data );
                $res= update_user_meta($userID, 'hrc_user_avatar', $attach_id);
            } 
            
            echo $res;
        } else {
            echo false;
        }
      }
    }
    
    exit;
}

/* 
* Source: single-thiep_moi.php
* Cho phép sửa nhanh nội dung trên giao diện hiển thị thông tin đám cưới */
add_action('wp_ajax_updateSentFriend', 'updateSentFriend');
add_action('wp_ajax_nopriv_updateSentFriend', 'updateSentFriend');
function updateSentFriend() {
    $field      = $_POST['field'];
    $row        = $_POST['row'];
    $ischecked  = $_POST['ischecked'];
    $groupid    = $_POST['groupid'];

    # nếu đồng ý tham gia thì tìm người có mã số là $invitee trong nhóm $group để update 
    if (have_rows('guest_list', $groupid)) {
        while (have_rows('guest_list', $groupid)) {
            the_row();
    
            $row_index = get_row_index();
            if ($row_index != $row) {
                continue;
            } else {
                # nếu tìm thấy khách thì update thông tin rồi break ra khỏi vòng lặp
                update_sub_field($field, $ischecked);
                $found_customer = true;
                break;
            }
            $found_customer= false;
        }
    } else $found_customer= false;

    echo $found_customer;
    exit;
}

/* 
* Source: link-to-partner.php
* Liên kết tài khoản partner
*/
add_action('wp_ajax_findUser', 'findUser');
function findUser() {
    $username = $_POST['username'];
    $user = get_user_by('login', $username);
    if ($user) {
        # get avatar url
        $link_avatar = get_avatar_url($user->ID, array('size' => '100'));
        $result = '<div class="user_found">
                <img src="' . $link_avatar . '" alt="">
                <span>' . $user->display_name . '</span>
            </div>';
        $flag = true;
    } else {
        $result = '<div class="error">Không tìm thấy tài khoản.</div>';
        $flag = false;
    }
    echo json_encode(array('result' => $result, 'flag' => $flag));
    exit;
}