<?php
/* 
* Source: wedding-infomation.php
* Xử lý khi nhập dữ liệu đám cưới ở trang nhập liệu
*/
add_action('wp_ajax_addWeddingInfo', 'addWeddingInfo');
add_action('wp_ajax_nopriv_addWeddingInfo', 'addWeddingInfo');
function addWeddingInfo(){
    $current_user_id = get_current_user_id();
    $data = parse_str($_POST['data'], $output);
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
                $today = new DateTime($value);
                $lunar= ShowLunarDate($today, 'YYYY-mm-dd') . $time;

                update_field($key, $lunar, 'user_' . $current_user_id);
            } else {
                if (trim($value)) {
                    update_field($key, $value, 'user_' . $current_user_id);
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
        $data['lunarUpdate'] = $lunartime->format('d/m/Y g:i a');
    } else {
        $data['status']      = false;
    }
    echo json_encode($data);
    exit;
}
