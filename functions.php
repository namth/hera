<?php
add_filter( 'get_the_archive_title', function ( $title ) {
    if( is_category() ) {
        $title = single_cat_title( '', false );
    }
    return $title;
});

add_action('init','all_my_hooks');
function all_my_hooks(){
    $dir = dirname( __FILE__ );
    require_once( $dir . '/inc/general/function.php');
    
    # function library
    require_once( $dir . '/inc/api_function.php');
    require_once( $dir . '/inc/ajax_function.php');
    require_once( $dir . '/inc/cronjob.php');
    require_once( $dir . '/api.php');
    # Init SESSION
    if(!session_id()) {
        session_start();
    }

    # Init post thumbnail 
    add_theme_support( 'post-thumbnails' ); 
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

# set secure for wp_login
add_action( 'set_auth_cookie', function ( $cookie ) {
    $cookie_name = is_ssl() ? SECURE_AUTH_KEY : AUTH_KEY;
    $_COOKIE[ $cookie_name ] = $cookie;
} );

add_action( 'set_logged_in_cookie', function ( $cookie ) {
    $_COOKIE[ LOGGED_IN_KEY ] = $cookie;
} );

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

    if (is_page(72) || is_page(276)) {
        wp_enqueue_script('mycards', get_template_directory_uri() . '/js/mycards.js', array('jquery'), '1.0', true);
        wp_localize_script('mycards', 'AJAX', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    } else {
        /* Css */
        wp_enqueue_style('mui', get_template_directory_uri() . '/css/mui.min.css');
        wp_enqueue_style('main-style', get_template_directory_uri() . '/style.css');
        wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');
        wp_enqueue_style('slidecaptcha', get_template_directory_uri() . '/js/slidecaptcha/slidercaptcha.min.css');
        wp_enqueue_style('inova', get_template_directory_uri() . '/css/inova.css');
        wp_enqueue_style('frontpage', get_template_directory_uri() . '/css/frontpage/frontpage.css');
        
        /* Js */
        wp_enqueue_script('mui', get_template_directory_uri() . '/js/mui.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('frontpage', get_template_directory_uri() . '/js/frontpage/frontpage.js', array('jquery', 'mui'), '1.0', true);
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
    $parent = acf_add_options_page(array(
        'page_title'    => 'Tùy biến chung', // Title hiển thị khi truy cập vào Options page
        'menu_title'    => 'Tùy biến chung', // Tên menu hiển thị ở khu vực admin
        'menu_slug'     => 'theme-settings', // Url hiển thị trên đường dẫn của options page
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

    // Add sub page.
    $child = acf_add_options_sub_page(array(
        'page_title'    => __('Data mẫu'),
        'menu_title'    => __('Data mẫu'),
        'menu_slug'     => 'data-sample',
        'parent_slug'   => $parent['menu_slug'],
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
    add_rewrite_rule('^thiepcuoi/(.*)/(.*)/(.*)/(.*)?', 'index.php?page_id=72&group=$matches[1]&character=$matches[2]&invitee=$matches[3]&guest=$matches[4]', 'top');
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

# reading excel PHPExcel
function wp_reading_excel($tmp_name)
{
    require_once get_template_directory() . '/vendor/autoload.php';

    try {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($tmp_name);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        
        return $sheetData;
    } catch (Exception $e) {
        // die('Lỗi không thể đọc file "' . pathinfo($tmp_name, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        return false;
    }
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
        'app_id'        => ZALO_APP_ID,
        'grant_type'    => 'authorization_code',
        'code_verifier' => $code_verifier,
    );
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "secret_key: " . ZALO_SECRET_KEY,
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
function track_user_logins( $user ){
    if( $login_amount = get_user_meta( get_current_user_id(), 'login_amount', true ) ){
        // They've Logged In Before, increment existing total by 1
        update_user_meta( get_current_user_id(), 'login_amount', ++$login_amount );
    } else {
        // First Login, set it to 1
        update_user_meta( get_current_user_id(), 'login_amount', 1 );
    }
}

# Kiểm tra và format lại số điện thoại, số nào không hợp lệ thì trả về false, số hợp lệ thì được định dạng lại
function validate_phonenumber($phonenumber){
    # Xoá ký tự thừa không phải là số trong chuỗi.
    $phonenumber = preg_replace("/[^0-9]/", "", $phonenumber);
    # Chỉnh những số có đầu là 84 (nếu có 11 ký tự) về số 0
    if (preg_match('/^[0-9]{11}$/', $phonenumber)) {
        $phonenumber = preg_replace("/^(84)/", "0", trim($phonenumber));
    }

    # Nếu số điện thoại sau khi chỉnh có số 0 (hoặc không có) ở đầu và 9 số đằng sau (không bắt đầu bằng 0) thì trả về số điện thoại đúng
    if (preg_match('/^(0|)[1-9][0-9]{8}$/', $phonenumber)) {
        return str_pad($phonenumber, 10, '0', STR_PAD_LEFT);
    } else {
        return false;
    }
}

# Chuyển đổi dạng echo sang dạng string thay cho việc echo như trước
function echo_to_string( $function )
{
    ob_start();
    call_user_func( $function );
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

# check coupon xem có được sử dụng chưa 
function check_coupon_limit($coupon_id, $user_id){
    $limit = get_field('limit', $coupon_id);

    # nếu limit bằng 0 thì trả về true (mã hợp lệ)
    if (!$limit) {
        return true;
    }
    
    $args   = array(
        'post_type'     => 'inova_order',
        'posts_per_page' => 999,
        'author'        => $user_id,
        'post_status'   => 'publish',
        'orderby' => 'ID',
        'order' => 'DESC',
        'meta_query'    => array(
            array(
                'key'       => 'status',
                'value'     => array('Đã thanh toán', 'Thanh toán dư'),
                'compare'   => 'IN',
            ),
        ),
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $coupon  = get_field('coupon');
            # nếu có mã giảm giá được sử dụng thành công thì trừ giới hạn đi 1
            if ($coupon == $coupon_id) {
                $limit--;
            }

            # nếu limit về 0 thì trả về false 
            if (!$limit) {
                return $limit;
            }
        }
    }

    # nếu check hết mà limit vẫn dương thì trả về limit
    return $limit;
}

function convertToUnaccented($str) {
    $unaccentedStr = "";
    $unicodeChars = array(
        'Á'=>'A', 'À'=>'A', 'Ả'=>'A', 'Ã'=>'A', 'Ạ'=>'A', 'Ă'=>'A', 'Ắ'=>'A', 'Ằ'=>'A', 'Ẳ'=>'A', 'Ẵ'=>'A', 'Ặ'=>'A',
        'Â'=>'A', 'Ấ'=>'A', 'Ầ'=>'A', 'Ẩ'=>'A', 'Ẫ'=>'A', 'Ậ'=>'A', 'Đ'=>'D', 'É'=>'E', 'È'=>'E', 'Ẻ'=>'E', 'Ẽ'=>'E',
        'Ẹ'=>'E', 'Ê'=>'E', 'Ế'=>'E', 'Ề'=>'E', 'Ể'=>'E', 'Ễ'=>'E', 'Ệ'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Ỉ'=>'I', 'Ĩ'=>'I',
        'Ị'=>'I', 'Ó'=>'O', 'Ò'=>'O', 'Ỏ'=>'O', 'Õ'=>'O', 'Ọ'=>'O', 'Ô'=>'O', 'Ố'=>'O', 'Ồ'=>'O', 'Ổ'=>'O', 'Ỗ'=>'O',
        'Ộ'=>'O', 'Ơ'=>'O', 'Ớ'=>'O', 'Ờ'=>'O', 'Ở'=>'O', 'Ỡ'=>'O', 'Ợ'=>'O', 'Ú'=>'U', 'Ù'=>'U', 'Ủ'=>'U', 'Ũ'=>'U',
        'Ụ'=>'U', 'Ư'=>'U', 'Ứ'=>'U', 'Ừ'=>'U', 'Ử'=>'U', 'Ữ'=>'U', 'Ự'=>'U', 'Ý'=>'Y', 'Ỳ'=>'Y', 'Ỷ'=>'Y', 'Ỹ'=>'Y',
        'Ỵ'=>'Y',
        'á'=>'a', 'à'=>'a', 'ả'=>'a', 'ã'=>'a', 'ạ'=>'a', 'ă'=>'a', 'ắ'=>'a', 'ằ'=>'a', 'ẳ'=>'a', 'ẵ'=>'a', 'ặ'=>'a',
        'â'=>'a', 'ấ'=>'a', 'ầ'=>'a', 'ẩ'=>'a', 'ẫ'=>'a', 'ậ'=>'a', 'đ'=>'d', 'é'=>'e', 'è'=>'e', 'ẻ'=>'e', 'ẽ'=>'e',
        'ẹ'=>'e', 'ê'=>'e', 'ế'=>'e', 'ề'=>'e', 'ể'=>'e', 'ễ'=>'e', 'ệ'=>'e', 'í'=>'i', 'ì'=>'i', 'ỉ'=>'i', 'ĩ'=>'i',
        'ị'=>'i', 'ó'=>'o', 'ò'=>'o', 'ỏ'=>'o', 'õ'=>'o', 'ọ'=>'o', 'ô'=>'o', 'ố'=>'o', 'ồ'=>'o', 'ổ'=>'o', 'ỗ'=>'o',
        'ộ'=>'o', 'ơ'=>'o', 'ớ'=>'o', 'ờ'=>'o', 'ở'=>'o', 'ỡ'=>'o', 'ợ'=>'o', 'ú'=>'u', 'ù'=>'u', 'ủ'=>'u', 'ũ'=>'u',
        'ụ'=>'u', 'ư'=>'u', 'ứ'=>'u', 'ừ'=>'u', 'ử'=>'u', 'ữ'=>'u', 'ự'=>'u', 'ý'=>'y', 'ỳ'=>'y', 'ỷ'=>'y', 'ỹ'=>'y',
        'ỵ'=>'y'
    );
    
    for($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
        $char = mb_substr($str, $i, 1, 'UTF-8');
        if(isset($unicodeChars[$char])) {
            $unaccentedStr .= $unicodeChars[$char];
        } else {
            $unaccentedStr .= $char;
        }
    }
    
    return $unaccentedStr;
}

# Lấy chữ cái đầu của tên
function nameLetter($name){
    # Lấy ra từ cuối cùng trong tên
    $pieces = explode(' ', $name);
    $last_word = convertToUnaccented(array_pop($pieces)); # Chuyển về không dấu

    # Lấy ra chữ cái đầu tiên
    $first_character = $last_word[0];
    
    return $first_character;
}

# lưu lại thời điểm login cuối cùng
function user_last_login( $user_login, $user ){
    update_user_meta( $user->ID, '_last_login', time() );
}
add_action( 'wp_login', 'user_last_login', 10, 2 );