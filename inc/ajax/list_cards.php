<?php
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
    $liked      = get_user_meta($current_user_id, 'liked')?get_user_meta($current_user_id, 'liked'):array();
    $liked_arr  = explode(',', $liked[0]);
    $icon       = in_array($cardid, $liked_arr)?"fa-heart":"fa-heart-o";

    ?>
    <div class="mui-row" id="detail_card_popup">
        <div class="mui-col-lg-9 mui-col-md-12 card_thumbnail">
            <img src="<?php echo $mycard->thumbnail; ?>" alt="">
        </div>
        <div class="mui-col-lg-3 mui-col-md-12" id="detail_data_box">
            <h2><?php echo $mycard->title; ?></h2>
            <div class="mui-divider"></div>

            <div class="button_group">
                <button id="like" class="like mui-btn mui-btn--raised" data-card=<?php echo $cardid; ?>><i class="fa <?php echo $icon; ?>"></i></button>
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
                    if ($current_user_id) {
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
                                    <dotlottie-player src="<?php echo get_template_directory_uri() . '/img/heart_card.json'; ?>" 
                                            background="transparent" speed="1" 
                                            style="width: 50px; height: 50px" direction="1" playMode="bounce" loop autoplay style="margin: 0 auto;">
                                    </dotlottie-player>
                                </div>
                                <div class="customer_name">
                                    <?php the_title(); ?>
                                </div>
                            </label>
                        </div>
                        <?php
                            }
                            wp_reset_postdata();

                            echo '<div class="mui-row">
                                    <button id="close_select_card" class="mui-btn mui-btn--raised"><i class="fa fa-close"></i></button>
                                    <input type="submit" value="Chọn thiệp này" class="mui-btn mui-btn--raised mui-btn--primary">
                                </div>';
                        } else {
                            echo '<span><i>Bạn phải tạo nhóm khách mời trước khi chọn thiệp. Hãy quay lại trang chủ để tạo nhóm.</i></span>';
                            echo '<div class="mui-row">
                                    <button id="close_select_card" class="mui-btn mui-btn--raised"><i class="fa fa-close"></i></button>
                                    <a href="' . get_bloginfo('url') . '" class="mui-btn mui-btn--raised mui-btn--primary">Quay lại</a>
                                </div>';
                        }
                        wp_nonce_field('post_nonce', 'post_nonce_field');
                    } else {
                        ?>
                        <div class="login_require">
                            <div class="social_login">
                                <h4>Bạn có thể đăng nhập nhanh qua</h4>
                                <div class="google_btn social_btn">
                                    <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
                                        <img src="<?php echo get_template_directory_uri(); ?>/img/gg.svg" alt="" /> <span>Google</span>
                                    </a>
                                </div>
                                <div class="facebook_btn social_btn">
                                    <a href="<?php echo get_bloginfo('url'); ?>/herasecurelogin?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="600" data-popupheight="679">
                                        <img src="<?php echo get_template_directory_uri(); ?>/img/fb.png" alt="" /> <span>Facebook</span>
                                    </a>
                                </div>
                                <?php
                                    # Zalo login link
                                    $code_verify = generate_verify_code();
                                    update_field('field_6356c04455afc', $code_verify, 'option');
                                    
                                    $code_challenge = generate_code_challenge($code_verify);
                                    $url = get_bloginfo('url') . '/zalo-login';
                                    
                                    echo '<div class="zalo_btn social_btn">
                                            <a href="https://oauth.zaloapp.com/v4/permission?app_id=' . ZALO_APP_ID . '&redirect_uri=' . $url . '&code_challenge=' . $code_challenge . '&state=' . $code_verify . '">
                                                <img src="' . get_template_directory_uri() . '/img/zl.webp" alt="" /> <span>Zalo</span>
                                            </a>
                                        </div>';
                                ?>
                            </div>
                            <div class="signup">
                                <p>Hoặc có thể đăng ký tài khoản tại đây.</p>
                                <a href="<?php echo get_bloginfo('url'); ?>/dang-ky"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt=""> Đăng ký</a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    
                </form>
            </div>
        </div>
    </div>
    <div id="direction">
        <?php 
            $nextlink = $_POST['nextid']?inova_encrypt($_POST['nextid'], 'e'):"";
            $prevlink = $_POST['previd']?inova_encrypt($_POST['previd'], 'e'):"";

            if ($nextlink) echo '<a class="arrow right" href="?c=' . $nextlink . '" data-card=".heracard-' . $_POST['nextid'] . '"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
            if ($prevlink) echo '<a class="arrow left" href="?c=' . $prevlink . '" data-card=".heracard-' . $_POST['previd'] . '"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
        ?>
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

                    # gọi API để lấy html của thiệp để đưa vào lưu tại thiệp
                    $token = get_field('token', 'option');
                    if (!check_token($token)) {
                        # Kiểm tra nếu token vẫn hoạt động thì thôi, nếu không thì phải lấy lại token mới.
                        $token = refresh_token();
                    }
                    $api_base_url = get_field('api_base_url', 'option');
                    $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $output['cardid'];
                    $mycard = inova_api($api_url, $token, 'GET', '');
                    if (!is_wp_error($mycard)) {
                        update_field('field_62a7d154c86cd', $mycard->html);

                        $content_1 = $mycard->content1 ? $mycard->content1 : get_field('content_1', 'option');
                        $content_2 = $mycard->content2 ? $mycard->content2 : get_field('content_2', 'option');
                        $content_3 = $mycard->content3 ? $mycard->content3 : get_field('content_3', 'option');
                        $loi_moi   = $mycard->content4 ? $mycard->content4 : get_field('wedding_invitation', 'option');

                        update_field('field_63ceb66556861', $content_1);
                        update_field('field_63ceb69856862', $content_2);
                        update_field('field_63ceb6e956863', $content_3);
                        update_field('field_63ca81d252862', $loi_moi);
                    }

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
    if (($author_id == $current_user_id) || current_user_can('manage_options')) {
        update_field('field_610ead267af54', $cardid, $groupid); # setup cardid
        update_field('field_610ead167af53', $thumbnail, $groupid); # setup thumbnail
        
        # gọi API để lấy html của thiệp để đưa vào lưu tại thiệp
        $token = get_field('token', 'option');
        if (!check_token($token)) {
            # Kiểm tra nếu token vẫn hoạt động thì thôi, nếu không thì phải lấy lại token mới.
            $token = refresh_token();
        }
        $api_base_url = get_field('api_base_url', 'option');
        $api_url = $api_base_url . '/wp-json/inova/v1/html/' . $cardid;
        $mycard = inova_api($api_url, $token, 'GET', '');
        if (!is_wp_error($mycard)) {
            update_field('field_62a7d154c86cd', $mycard->html, $groupid);

            $content_1 = $mycard->content1 ? $mycard->content1 : get_field('content_1', 'option');
            $content_2 = $mycard->content2 ? $mycard->content2 : get_field('content_2', 'option');
            $content_3 = $mycard->content3 ? $mycard->content3 : get_field('content_3', 'option');
            $loi_moi   = $mycard->content4 ? $mycard->content4 : get_field('wedding_invitation', 'option');

            update_field('field_63ceb66556861', $content_1, $groupid);
            update_field('field_63ceb69856862', $content_2, $groupid);
            update_field('field_63ceb6e956863', $content_3, $groupid);
            update_field('field_63ca81d252862', $loi_moi, $groupid);
        }
    }
    echo get_permalink($groupid);
    exit;
}

/* 
* Source: list_cards.php (trong popup)
* Set like or dislike to card */
add_action('wp_ajax_pressLikeButton', 'pressLikeButton');
function pressLikeButton(){
    $cardid = $_POST['card'];
    $current_user_id = get_current_user_id();

    $liked = get_user_meta($current_user_id, 'liked')?get_user_meta($current_user_id, 'liked'):array();
    $liked_arr = explode(',', $liked[0]);

    if (!empty($liked_arr) && (($key = array_search($cardid, $liked_arr)) !== false)) {
        unset($liked_arr[$key]);
        $type = 'dislike';
    } else {
        $liked_arr[] = $cardid;
        $type = 'like';
    }

    # cap nhat vao du lieu user
    $new_liked = implode(',', $liked_arr);
    update_user_meta($current_user_id, 'liked', $new_liked);

    # goi API de set like 
    $token = get_field('token', 'option');
    $api_base_url = get_field('api_base_url', 'option');
    $api_url = $api_base_url . '/wp-json/inova/v1/update_card/';
    $data = array(
        'postID'    => $cardid,
        'type'      => $type,
    );
    $mycard = inova_api($api_url, $token, 'POST', $data);

    echo implode(',', $liked_arr);
    exit;
}
