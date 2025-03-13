<?php
/* 
* Template Name: Liên kết tài khoản partner
*/
$current_user = wp_get_current_user();
$flag = false;
$notification = '';

# process form
if (isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {
    $username = sanitize_text_field($_POST['username']);
    $user_id = username_exists($username);
    
    if ($user_id) {
        # check if user is current user
        if ($user_id == $current_user->ID) {
            $notification = "Không thể tự liên kết với tài khoản mình!";
        } else {
            # check if user is already linked
            $linked = get_user_meta($user_id, 'partner_id', true);
            if ($linked) {
                $notification = "Tài khoản này đã được liên kết!";
            } else {
                update_user_meta($current_user->ID, 'partner_id', $user_id);
                update_user_meta($user_id, 'partner_id', $current_user->ID);
                $notification = "Liên kết thành công!";
                $flag = true;
            }
        }
    } else {
        $notification = "Không tìm thấy tài khoản!";
    }
}

# remove user link
if (isset($_GET['removeuser'])) {
    $partner_id = sanitize_text_field($_GET['removeuser']);
    delete_user_meta($current_user->ID, 'partner_id', $partner_id);
    delete_user_meta($partner_id, 'partner_id', $current_user->ID);
    $notification = "Gỡ liên kết thành công!";
    $flag = true;

    # redirect to same page
    $url = get_permalink();
    wp_redirect($url);
    exit;
}

get_header();
get_template_part('header', 'topbar');

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-8 mt20">
            <!-- <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                
            </div> -->
            <div class="mui-panel">
                <div class="back-btn mb20">
                    <a href="javascript:history.back()"><i class="fa fa-arrow-left"></i> Quay lại</a>
                </div>
                <h3 class="title_general">Liên kết tài khoản</h3>
                <p>Bạn có thể liên kết với tài khoản của nửa kia của bạn để cùng tạo thiệp online và sử dụng thiệp dễ dàng hơn.</p>
                <?php 
                    if (isset($notification) && $notification) {
                        if ($flag) {
                            $icon = 'fa-check';
                            $class = 'success_notification';
                        } else {
                            $icon = 'fa-times';
                            $class = 'error_notification';
                        }
                        echo '  <div class="' . $class . '" style="margin-bottom: 15px;">
                                    <span><i class="fa ' . $icon . '" aria-hidden="true"></i> ' . $notification . '</span>
                                </div>';
                    }
                ?>
                <div class="mui-row">
                    <div class="mui-col-lg-7 mui-col-md-12">
                        <?php 
                        # check if user is already linked
                        $linked = get_user_meta($current_user->ID, 'partner_id', true);
                        if ($linked) {
                            $partner = get_user_by('ID', $linked);
                            # show partner info with avatar
                            echo '
                                <h4>Thông tin tài khoản đã liên kết</h4>
                                <div class="user_found">
                                    ' . get_avatar($partner->ID, 63) . '
                                    <span>' . $partner->display_name . '</span>
                                </div>
                                <a href="?removeuser=' . $linked . '" class="mui-btn hera-btn">Gỡ liên kết</a>
                                ';
                        } else {
                        ?>
                        <form class="mui-form link_partner_form" method="POST" name="guest_form" id="hera_form">
                            <div class="mui-textfield">
                                <input class="password" type="text" name="username" id="user_oldpass" style="width: 92%;">
                                <label for="user_oldpass">Tên đăng nhập của bạn ấy</label>
                                <span class="form_check_icon"></span>
                                <span class="see_pass"><i class="fa fa-search" aria-hidden="true"></i></span>
                                <span class="pass_error_notif error"></span>
                                <div id="userFinded"></div>
                            </div>
                            <?php
                            wp_nonce_field('post_nonce', 'post_nonce_field');
                            ?>
                            <button type="submit" class="mui-btn hera-btn" disabled>Cập nhật</button>
                        </form>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2">
        </div>
    </div>
</div>
<?php
get_footer();
