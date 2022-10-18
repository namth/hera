<?php
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
* Source: list_cards.php
* Setup view to display detail card when customer click to each card.
*/ 
add_action('wp_ajax_viewDetailCard', 'viewDetailCard');
add_action('wp_ajax_nopriv_viewDetailCard', 'viewDetailCard');
function viewDetailCard(){
    $cardid = $_POST['cardid'];
    $current_groupid = $_POST['groupid'];
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
            <button id="select_card" class="mui-btn mui-btn--raised mui-btn--primary fullwidth" 
                    data-groupid="<?php echo $current_groupid; ?>"
                    data-loading="<?php echo get_template_directory_uri() . '/img/loader.gif'; ?>"><i class="fa fa-map" aria-hidden="true"></i> Thử mẫu thiệp này</button>
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

/* 
* Source: list_cards.php (trong popup, khi không có target trước)
* Cho phép khách chọn thiệp ở trang danh sách thiệp */
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

/* 
* Source: list_cards.php (trong popup, khi biết trước target)
* Set card vào khách mời */
add_action('wp_ajax_addCardToSelectedGroup', 'addCardToSelectedGroup');
add_action('wp_ajax_nopriv_addCardToSelectedGroup', 'addCardToSelectedGroup');
function addCardToSelectedGroup(){
    $current_user_id = get_current_user_id();
    $cardid = $_POST['cardid'];
    $groupid = $_POST['groupid'];
    $thumbnail = $_POST['thumbnail'];

    $author_id = get_post_field ('post_author', $groupid);
    if ($author_id == $current_user_id) {
        update_field('field_610ead267af54', $cardid, $groupid); # setup cardid
        update_field('field_610ead167af53', $thumbnail, $groupid); # setup thumbnail
        $updated = true;
    }
    echo get_permalink($groupid);
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
        if (!$token) {
            $token = refresh_token();
        }
    }
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/cards';
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
            <span><?php echo $listcards->errors['http_request_failed'][0]; ?></span>
            <button class="mui-btn hera-btn" id='reload_card'>Tải lại</button>
            <img src="<?php echo get_template_directory_uri() . '/img/f_loading.gif'; ?>" alt="" style="display: none;">
        </div>
        <?php
    } else{
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

/* 
* Source: single-thiep_moi.php
* Xử lý ajax khi bấm nút xoá một khách mời trong một group
*/
add_action('wp_ajax_deleteCustomer', 'deleteCustomer');
add_action('wp_ajax_nopriv_deleteCustomer', 'deleteCustomer');
function deleteCustomer() {
    $data = json_decode(inova_encrypt($_POST['content'], 'd'));

    /* Kiểm tra tính xác thực */
    if (wp_verify_nonce($data->nonce, 'delcustomer_' . $data->row_index)) {
        /* Delete customer from group post by id */
        delete_row('field_61066efde7dbc', $data->row_index, $data->groupid);
        echo true;
    } else echo false;
    exit;
}

/* 
* Source: wedding-infomation.php
* Xử lý khi nhập dữ liệu đám cưới ở trang nhập liệu
*/
add_action('wp_ajax_addWeddingInfo', 'addWeddingInfo');
add_action('wp_ajax_nopriv_addWeddingInfo', 'addWeddingInfo');
function addWeddingInfo(){
    $current_user_id = get_current_user_id();
    $data = parse_str($_POST['data'], $output);
    $refer_link = array_pop($output);
    if (isset($output['wedding_field']) && wp_verify_nonce($output['wedding_field'], 'wedding')) {
        $nonce = array_pop($output);
        foreach ($output as $key => $value) {
            if (trim($value)) {
                update_field($key, $value, 'user_' . $current_user_id);
            }
        }
    }
    exit;
}

/* 
* Source: wedding-infomation.php
* Cho phép sửa nhanh nội dung trên giao diện hiển thị thông tin đám cưới */
add_action('wp_ajax_updateWeddingInfo', 'updateWeddingInfo');
add_action('wp_ajax_nopriv_updateWeddingInfo', 'updateWeddingInfo');
function updateWeddingInfo() {
    $current_user_id = get_current_user_id();
    if($_POST['content']!=""){
        update_field($_POST['field'], $_POST['content'], 'user_' . $current_user_id);
    }
    exit;
}

/* 
* Source: wedding-infomation.php
* Cho phép sửa nhanh ngày tháng trên giao diện hiển thị thông tin đám cưới */
add_action('wp_ajax_weddingDateInput', 'weddingDateInput');
add_action('wp_ajax_nopriv_weddingDateInput', 'weddingDateInput');
function weddingDateInput() {
    $current_user_id = get_current_user_id();
    $data = parse_str($_POST['data'], $output);
    $solartime_field = $output['solartime_field'];
    $lunartime_field = $output['lunartime_field'];
    
    if ($output['solartime']) {
        # tính toán ngày tháng 
        $time = substr($output['solartime'], 10);
        $today = new DateTime($output['solartime']);
        $lunar= ShowLunarDate($today, 'YYYY-mm-dd') . $time;
        $lunartime = new DateTime($lunar);
        
        # cập nhật vào cơ sở dữ liệu
        update_field($solartime_field, strtotime($output['solartime']), 'user_' . $current_user_id); // thời gian dương lịch
        update_field($lunartime_field, strtotime($lunar), 'user_' . $current_user_id); // thời gian dương lịch
    
        # output để hiển thị
        $data['status']      = true;
        $data['solarUpdate'] = $today->format('d/m/Y g:i a');
        $data['lunarUpdate'] = $lunartime->format('d/m/Y g:i a');
    } else {
        $data['status']      = false;
    }
    echo json_encode($data);
    exit;
}


/* 
* Source: view_card.php
* Xử lý khi click vào nút đồng ý hoặc từ chối tham dự */
add_action('wp_ajax_acceptInvite', 'acceptInvite');
add_action('wp_ajax_nopriv_acceptInvite', 'acceptInvite');
function acceptInvite() {
    $group = inova_encrypt($_POST['group'], 'd');
    $invitee = inova_encrypt($_POST['invitee'], 'd');
    $answer = $_POST['answer'];

    if (($answer=='Y')||($answer == 'N')) {
        # nếu đồng ý tham gia thì tìm người có mã số là $invitee trong nhóm $group để update 
        if (have_rows('guest_list', $group)) {
            while (have_rows('guest_list', $group)) {
                the_row();
        
                $row_index = get_row_index();
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
        echo '<div class="deny notification">Không thể cập nhật bạn vào dữ liệu thiệp, hãy kiểm tra lại.</div>';
    }
    exit;
}

/* 
* Source: checkout.php | js/checkout.js
* Cho phép sửa nhanh nội dung trên giao diện hiển thị thông tin đám cưới */
add_action('wp_ajax_addCouponCode', 'addCouponCode');
add_action('wp_ajax_nopriv_addCouponCode', 'addCouponCode');
function addCouponCode() {
    $data = $_POST['data'];
    $sub_total = $_POST['sub_total'];

    /* Kiểm tra coupon có tồn tại không */
    $id_coupon = search_customfield('coupon', $data, 'coupon_name');

    /* Nếu có thì validate coupon xem đã hết hạn hoặc hết mã chưa */
    if ($id_coupon) {
        $expired = get_field('expired', $id_coupon);
        $coupon_type = get_field('coupon_type', $id_coupon);
        $coupon_value = get_field('coupon_value', $id_coupon);
        $coupon_quantity = get_field('coupon_quantity', $id_coupon);
        
        /* validate data */
        $today = new DateTime();
        if (($coupon_quantity > 0) && ($expired >= $today->format('Ymd'))) {
            $data = array(
                'status' => true,
                'coupon' => $data,
                'message'=> '<div class="success_notification"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Đã thêm mã coupon thành công.</div>',
            );
            if ($coupon_type == "Phần trăm") {
                $data['type'] = 'percent';
                $data['value'] = $coupon_value;
                $data['coupon_label'] = '- ' . $coupon_value . '%';
                $data['final_total'] = $sub_total * (100 - $coupon_value) / 100;
                $data['hash'] = inova_encrypt(json_encode(array(
                    'id'        => $id_coupon,
                    'type'      => 'percent',
                    'value'     => $coupon_value,
                )), 'e');
            } else {
                $data['type'] = 'fix';
                $data['value'] = $coupon_value;
                $data['coupon_label'] = '- ' . number_format($coupon_value) . ' ₫';
                $data['final_total'] = ($sub_total > $coupon_value)?($sub_total - $coupon_value):"0";
                $data['hash'] = inova_encrypt(json_encode(array(
                    'id'        => $id_coupon,
                    'type'      => 'fix',
                    'value'     => $coupon_value,
                )), 'e');
            }
        } else {
            $check_fail = true;
            $message = "Mã khuyến mại đã hết hạn hoặc hết số lượng.";
        }

    } else {
        $check_fail = true;
        $message = "Không tìm thấy mã khuyến mại.";
    }
    
    if ($check_fail) {
        $data = array(
            'status' => false,
            'message'=> '<div class="error_notification"><i class="fa fa-time-circle-o" aria-hidden="true"></i> ' . $message . '</div>',
        );
    }
    echo json_encode($data);
    exit;
}

/* 
* Source: checkout.php | js/checkout.js
* Tạo hoá đơn mới cho khách khi bấm vào nút thanh toán */
add_action('wp_ajax_createInvoice', 'createInvoice');
add_action('wp_ajax_nopriv_createInvoice', 'createInvoice');
function createInvoice() {
    $data = parse_str($_POST['data'], $output);
    $coupon = json_decode(inova_encrypt($output["coupon"], 'd'));
    $current_user = wp_get_current_user();
    $normal_price = get_field('normal_price','option');
    $vip_price = get_field('vip_price','option');
    $vat = '10';

    # Tính toán tổng tiền và khuyến mại
    $total = $output['normal_card_qtt'] * $normal_price + $output['vip_card_qtt'] * $vip_price;
    if ($coupon->type == 'percent') {
        $sub_total = $total * (100 - $coupon->value) / 100;
    } else if ($coupon->type == 'fix'){
        $sub_total = ($total > $coupon->value)?($total - $coupon->value):0;
    } else {
        $sub_total = $total;
    }
    $vat_total = $sub_total * $vat / 100;
    $final_total = $sub_total + $vat_total;
    $order_code = incrementalHash(8);

    # Tạo hoá đơn mới
    $args = array(
        'post_title'    => $order_code,
        'post_status'   => 'publish',
        'post_type'     => 'inova_order',
    );
    $inserted = wp_insert_post($args);

    # Update dữ liệu vào hoá đơn mới tạo
    update_field('field_62e6ae7175ee5', $current_user->ID, $inserted); # customer
    update_field('field_62eb93b78ca79', 'Chưa thanh toán', $inserted); # status
    update_field('field_62e6ad5875ee1', $output["normal_card_qtt"], $inserted); # status
    update_field('field_62ec9ad08de7a', $output["vip_card_qtt"], $inserted); # status
    update_field('field_62e6ae8f75ee6', $coupon->id, $inserted); # status
    update_field('field_62e6aea375ee7', $total, $inserted); # total
    update_field('field_62e6af7675ee9', $sub_total, $inserted); # sub_total
    update_field('field_62eb96c9f9af6', $vat_total, $inserted); # vat_total
    update_field('field_62eb96e0f9af7', $final_total, $inserted); # final_total

    echo get_permalink($inserted);
    exit;
}


