<?php
/* 
* Source: wedding-infomation.php
* Xử lý khi nhập dữ liệu đám cưới ở trang nhập liệu
*/
// date_default_timezone_set('Asia/Ho_Chi_Minh');

add_action('wp_ajax_addWeddingInfo', 'addWeddingInfo');
add_action('wp_ajax_nopriv_addWeddingInfo', 'addWeddingInfo');
function addWeddingInfo(){
    $current_user_id = get_current_user_id();
    $data = parse_str($_POST['data'], $output);
    if (isset($output['whereupdate'])) {
        $where_update = $output['whereupdate'];
    } else {
        $where_update = 'user_' . $current_user_id;
    }

    array_pop($output); # remove refer_link
    $lunar_field = [
        'field_62b135cb93a85',
        'field_62b13605bfa89',
        'field_62b1363fb06b7',
        'field_62b1363fb06cf',
    ];

    if (isset($output['wedding_field']) && wp_verify_nonce($output['wedding_field'], 'wedding')) {
        array_pop($output); # remove nonce
        foreach ($output as $key => $value) {
            if (in_array($key, $lunar_field)) {
                # tính toán ngày tháng 
                $time = substr($value, 10);

                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $today = new DateTime($value);
                $lunar= ShowLunarDate($today, 'YYYY-mm-dd') . $time;

                update_field($key, $lunar, $where_update);
            } else {
                if (trim($value)) {
                    update_field($key, $value, $where_update);
                }
            }
            echo $key . " " . $value;
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
    if($_POST['content']!=""){
        update_field($_POST['field'], trim($_POST['content']), $_POST['where']);
    }
    echo $_POST['where'];
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
        $data['lunarUpdate'] = formatLunarDate($lunartime, 'Ngày dd tháng mm năm MYMY');
    } else {
        $data['status']      = false;
    }
    echo json_encode($data);
    exit;
}


/* 
* Source: wedding-infomation.php
* Upload wedding photo
*/
add_action('wp_ajax_uploadWeddingPhoto', 'uploadWeddingPhoto');
add_action('wp_ajax_nopriv_uploadWeddingPhoto', 'uploadWeddingPhoto');
function uploadWeddingPhoto() {
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
                    $wedding_photo = get_field('wedding_photo', 'user_' . $userID);
                    wp_delete_attachment($wedding_photo);

                    #update
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    update_field('field_63dbd605caa96', $attach_id, 'user_' . $userID);
                } 
                
                echo 1;
            } else {
                echo false;
            }
            break;
        }
    }
    
    exit;
}