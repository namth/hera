<?php

add_action('init','all_my_hooks');
function all_my_hooks(){
    $dir = dirname( __FILE__ );
    require_once( $dir . '/inc/custom_post.php');
    require_once( $dir . '/inc/custom_field.php');
    
    # function library
    require_once( $dir . '/inc/api_function.php');
    require_once( $dir . '/inc/ajax_function.php');
    require_once( $dir . '/inc/casso_endpoint.php');
    
    # Init SESSION
    if(!session_id()) {
        session_start();
    }

    # Init post thumbnail 
    add_theme_support( 'post-thumbnails' ); 
    add_theme_support( 'html5' ); 
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
        wp_enqueue_style('slidecaptcha', get_template_directory_uri() . '/js/slidecaptcha/slidercaptcha.min.css');
        wp_enqueue_style('inova', get_template_directory_uri() . '/css/inova.css');
        
        /* Js */
        wp_enqueue_script('mui', get_template_directory_uri() . '/js/mui.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('inova', get_template_directory_uri() . '/js/inova.js', array('jquery', 'mui'), '1.0', true);
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

/* 
* File helper.php của momo
*/
function execPostRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    //execute post
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result;
}

/* 
* Generate 6 charactor by time to create order id
*/
function incrementalHash($len = 6){
    $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                 .'0123456789'); // and any other characters
    shuffle($seed);
    $rand = '';
    foreach (array_rand($seed, $len) as $k) $rand .= $seed[$k];
    return $rand;
}

/* 
* Check xem thanh toán nào quá hạn 7 ngày thì chuyển sang trạng thái huỷ thanh toán
*/
function check_payment_status(){
    $status = 'Chưa thanh toán';
    $now = current_time('timestamp', 7);

    /* query tat ca nhung don hang chua thanh toan */
    $args   = array(
        'post_type'     => 'inova_order',
        'posts_per_page' => -1,
        'post_status'   => 'publish',
        'meta_query'    => array(
            array(
                'key'       => 'status',
                'value'     => $status,
                'compare'   => '=',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            /* Kiểm tra ngày hiện tại và ngày tạo đơn có quá 7 ngày không */
            $start_date = strtotime(get_the_date('d-m-Y'));
            $end_date = strtotime("+7 day", $start_date);

            # miss deadline
            if ($now > $end_date) {
                # cập nhật trạng thái huỷ
                update_field('field_62eb93b78ca79', 'Huỷ'); # status
            }
        }
    }
}

# reading excel PHPExcel
function wp_reading_excel($tmp_name)
{
    try {
        $inputFileType = PHPExcel_IOFactory::identify($tmp_name);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($tmp_name);
    } catch (Exception $e) {
        die('Lỗi không thể đọc file "' . pathinfo($tmp_name, PATHINFO_BASENAME) . '": ' . $e->getMessage());
    }

    // Lấy sheet hiện tại
    $sheet = $objPHPExcel->getSheet(0);

    // Lấy tổng số dòng của file
    $highestRow = $sheet->getHighestRow();
    // Lấy tổng số cột của file
    $highestColumn = $sheet->getHighestColumn();

    //  Thực hiện việc lặp qua từng dòng của file, để lấy thông tin
    for ($row = 1; $row <= $highestRow; $row++) {
        // Lấy dữ liệu từng dòng và đưa vào mảng $rowData
        $data = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
        if ($data[0][0]) {
            $rowData[] = $data[0];
        }
    }

    return $rowData;
}

# Search groupID theo tên và userID
function search_group($groupName, $userID) {
    $author_query_id = array(
        'author'    => $userID, 
        'post_type' => 'thiep_moi',
        's'         => $groupName,
    );
    $query = new WP_Query($author_query_id);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            if (get_the_title() == $groupName){
                return get_the_ID();
            }
        }
    } else {
        return false;
    }
}

# Remove admin bar except administration
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

# Zalo API login
function get_access_token($authorization_code, $code_verifier) {
    # get access code 
    $data = array(
        'code'          => $authorization_code,
        'app_id'        => '4424878354763274341',
        'grant_type'    => 'authorization_code',
        'code_verifier' => $code_verifier,
    );
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "secret_key: 1qFDWGD94kuPapqjryca",
    ));
    
    curl_setopt($ch, CURLOPT_URL,"https://oauth.zaloapp.com/v4/access_token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
    
    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    $output = json_decode($server_output);

    if ($output->access_token) {
        return $output->access_token;
    } else {
        return false;
    }
}

# Zalo get infomation from access_code
function get_zalo_user_data( $access_token ){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.zalo.me/v2.0/me?fields=id,name,picture',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
        'access_token: ' . $access_token
        ),
    ));

    $server_output = curl_exec($curl);

    curl_close($curl);

    $output = json_decode($server_output);

    return $output;
}

# Zalo generate verifier code
function generate_verify_code(){
    $random = openssl_random_pseudo_bytes(32);
    $verifier = base64_encode($random);
    return $verifier;
}

# Zalo generate code challenge
function generate_code_challenge($str) {
    return base64url_encode(pack('H*', hash('sha256', $str)));
}

# encode for zalo generate code
function base64url_encode($plainText)
{
    $base64 = base64_encode($plainText);
    $base64 = trim($base64, "=");
    $base64url = strtr($base64, '+/', '-_');
    return ($base64url);
}

# add image from url to set avatar for user
function Generate_Featured_Image( $image_url, $userID  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
    else                                    $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    $res= update_user_meta($userID, 'hrc_user_avatar', $attach_id);

    return $res;
}

# Lưu lại số lần đăng nhập của user
add_action( 'wp_login', 'track_user_logins', 10, 2 );
function track_user_logins( $user_login, $user ){
    if( $login_amount = get_user_meta( $user->id, 'login_amount', true ) ){
        // They've Logged In Before, increment existing total by 1
        update_user_meta( $user->id, 'login_amount', ++$login_amount );
    } else {
        // First Login, set it to 1
        update_user_meta( $user->id, 'login_amount', 1 );
    }
}