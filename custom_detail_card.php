<?php
/* 
*  Template Name: Custom Address and Time Cards
*/ 
get_header();
get_template_part('header', 'topbar');

# get some of user infomation
$current_user_id = get_current_user_id();
# nếu là admin thì cho phép đọc biến số $_GET["uid"] để xem thiệp của user khác
if (current_user_can('manage_options') && isset($_GET["uid"]) && ($_GET["uid"] != "")) {
    $current_user_id = $_GET["uid"];

    $current_user = get_user_by("ID", $current_user_id);

    if (!$current_user) {
        # neu khong ton tai user thi ve trang chu
        wp_redirect(get_bloginfo('url'));
        exit;
    }
}

# đọc biến số tùy chỉnh ID thiệp cần custom thời gian và địa điểm
if ( isset($_GET["pid"]) && ($_GET["pid"] != "") ) {
    $card_id = inova_encrypt($_GET["pid"], 'd');
    if ( is_numeric($card_id) && get_post_type($card_id) == "thiep_moi" ) {
        $where_update = $card_id;
        // echo $where_update;
    } else {
        wp_redirect(get_permalink());
        exit;
    }
} else {
    wp_redirect(get_permalink());
    exit;
}

$google_api     = get_field('google_maps_api_key', 'option');
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-8">
            <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url') . "/main/"; ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                <i class="fa fa-chevron-right"></i>
                <a href="<?php echo get_permalink($card_id); ?>"><?php echo get_the_title($card_id); ?></a>
                <i class="fa fa-chevron-right"></i>
                <span class="title"> <?php the_title(); ?></span>
            </div>
            <div class="mui-panel" id="wedding_infomation">                
                <h3><?php the_title(); ?></h3>
                <div id="addition_info" class="mui-row">
                    <div class="mui-col-md-6">
                        <div class="content_info">
                            <?php 
                                $custom_wedding_location = get_field('custom_wedding_location', $where_update);
                                $custom_wedding_address  = get_field('custom_wedding_address', $where_update);
                                $custom_wedding_maps     = get_field('custom_wedding_maps', $where_update);
                                $custom_wedding_time     = get_field('custom_wedding_time', $where_update);
                                // $custom_wedding_moontime = get_field('custom_wedding_moontime', $where_update);
                                $custom_party_location   = get_field('custom_party_location', $where_update);
                                $custom_party_address    = get_field('custom_party_address', $where_update);
                                $custom_party_maps       = get_field('custom_party_maps', $where_update);
                                $custom_party_time       = get_field('custom_party_time', $where_update);
                                // $custom_party_moontime   = get_field('custom_party_moontime', $where_update);

                                if ($custom_wedding_time) {
                                    // print_r($custom_wedding_time);
                                    $_custom_wedding_time    = DateTime::createFromFormat('d/m/Y H:i', $custom_wedding_time);
                                } else {
                                    $_custom_wedding_time    = new DateTime();
                                }
                                if($custom_party_time) {
                                    $_custom_party_time  = DateTime::createFromFormat('d/m/Y H:i', $custom_party_time);
                                } else {
                                    $_custom_party_time  = new DateTime();
                                }
                                $_custom_wedding_moontime= ShowLunarDate($_custom_wedding_time, 'ngày dd tháng mm năm MYMY');
                                $_custom_party_moontime  = ShowLunarDate($_custom_party_time, 'ngày dd tháng mm năm MYMY');

                            ?>
                            <section class="content_box">
                                <span class="content_title">
                                    <h4>Địa điểm, thời gian tổ chức hôn lễ</h4>
                                    <span class="edit_section" data-form='#custom_wedding_form' data-mapid="gw_googlemaps" data-latlng='gw_latlng'>
                                    <?php 
                                        if ($custom_wedding_address) {
                                            echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                        }
                                    ?>
                                    </span>
                                </span>
                                <div class="group_data">
                                    <?php
                                    if ($custom_wedding_address) {
                                        echo '<div class="has_data">';
                                        echo '<div class="data_item">
                                                <i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_661f4e7441f14" data-where="' . $where_update . '">' . $custom_wedding_location . '</span>
                                            </div>';
                                        echo '<div class="data_item">
                                                <i class="fa fa-map-marker"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_661e9426847eb" data-where="' . $where_update . '">' . $custom_wedding_address . '</span>
                                            </div>';
                                        echo '<div class="date_editable data_item">
                                                <div class="date_data">
                                                    <i class="fa fa-calendar"></i> <span class="diveditable">' . $_custom_wedding_time->format('d/m/Y g:i a') . '</span>
                                                </div>
                                                <div class="date_input">
                                                    <form method="post">
                                                        <input name="solartime" type="datetime-local" value="' . $_custom_wedding_time->format('Y-m-d\TH:i:s') . '">
                                                        <input type="hidden" name="solartime_field" value="field_661d635af6a15">
                                                        <input type="hidden" name="whereupdate" value="' . $where_update . '">
                                                        <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                        <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                    </form>
                                                </div>
                                            </div>';
                                        if ($_custom_wedding_moontime) {
                                            echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $_custom_wedding_moontime . '</span></div>'; 
                                        }
                                        echo '</div>';
                                    } else {
                                    ?>
                                        <div class="no_data edit_section data_item" data-form='#custom_wedding_form' data-mapid="gw_googlemaps" data-latlng='gw_latlng'>
                                            <a class="section_name" href="#">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                <span>Chỉnh sửa thời gian địa điểm tổ chức hôn lễ</span>
                                            </a>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div id="custom_wedding_form" class="hide_form">
                                        <form class="mui-form" method="POST">
                                            <div class="mui-textfield">
                                                <input type="text" name="field_661f4e7441f14" value="<?php if ($custom_wedding_location) echo $custom_wedding_location; ?>" placeholder="Tư gia, Trung tâm tiệc cưới, Nhà văn hóa ...">
                                                <label for="">Nhập nơi tổ chức</label>
                                            </div>
                                            <div class="mui-textfield">
                                                <input type="text" name="field_661e9426847eb" value="<?php if ($custom_wedding_address) echo $custom_wedding_address; ?>" placeholder="Số 5, ngõ 165 phố ...">
                                                <label for="">Nhập địa điểm tổ chức</label>
                                            </div>
                                            <div class="mui-textfield date_calculate">
                                                <input class="solar" type="datetime-local" name="field_661d635af6a15" value="<?php if ($_custom_wedding_time) echo $_custom_wedding_time->format('Y-m-d\TH:i:00'); ?>">
                                                <label for="">Thời gian (dương lịch)</label>
                                            </div>
                                            <div class="mui-textfield">
                                                <label for="">Chọn vị trí tổ chức chính xác trên bản đồ</label>
                                                <!-- <input type="text" id="pac-input" class="controls"> -->
                                                <div id="gw_googlemaps" class="google_maps"></div>
                                            </div>
                                            <input id="gw_latlng" type="hidden" name="field_661f4e9d41f15" value="<?php if ($custom_wedding_maps) echo $custom_wedding_maps; ?>">
                                            <input type="hidden" name="whereupdate" value="<?php echo $where_update; ?>">
                                            <?php
                                            wp_nonce_field('wedding', 'wedding_field');
                                            ?>
                                            <div class="submit_div">
                                                <button type="submit" class="mui-btn mui-btn--danger">Cập nhật</button>
                                            </div>
                                        </form>
                                        <span class="close_button">X</span>
                                    </div>
                                </div>
                            </section>
                            <section class="content_box">
                                <span class="content_title">
                                    <h4>Địa điểm, thời gian tổ chức tiệc cưới</h4>
                                    <span class="edit_section" data-form='#custom_party_form' data-mapid='gp_googlemaps' data-latlng='gp_latlng'>
                                    <?php 
                                    if ($custom_party_address) {
                                        echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                    }
                                    ?>
                                    </span>
                                </span>
                                <div class="group_data">
                                    <?php
                                    if ($custom_party_address) {
                                        echo '<div class="has_data">';
                                        echo '<div class="data_item"><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_661f4ee541f16" data-where="' . $where_update . '">' . $custom_party_location . '</span></div>';
                                        echo '<div class="data_item"><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_661e9442847ec" data-where="' . $where_update . '">' . $custom_party_address . '</span></div>';
                                        echo '<div class="date_editable data_item">
                                                <div class="date_data">
                                                    <i class="fa fa-calendar"></i> <span class="diveditable">' . $_custom_party_time->format('d/m/Y g:i a') . '</span>
                                                </div>
                                                <div class="date_input">
                                                    <form method="post">
                                                        <input name="solartime" type="datetime-local" value="' . $_custom_party_time->format('Y-m-d\TH:i:s') . '">
                                                        <input type="hidden" name="solartime_field" value="field_661d631ff6a14">
                                                        <input type="hidden" name="whereupdate" value="' . $where_update . '">
                                                        <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                        <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                    </form>
                                                </div>
                                            </div>';
                                        if($_custom_party_moontime) {
                                            echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $_custom_party_moontime . '</span></div>';
                                        }
                                        echo '</div>';
                                    } else {
                                    ?>
                                        <div class="no_data edit_section data_item" data-form='#custom_party_form' data-mapid='gp_googlemaps' data-latlng='gp_latlng'>
                                            <a class="section_name" href="#">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                <span>Chỉnh sửa thời gian và địa điểm dự tiệc</span>
                                            </a>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div id="custom_party_form" class="hide_form">
                                        <form class="mui-form" method="POST">
                                            <div class="mui-textfield">
                                                <input type="text" name="field_661f4ee541f16" value="<?php if ($custom_party_location) echo $custom_party_location; ?>" placeholder="Tư gia, Trung tâm tiệc cưới, Nhà văn hóa ...">
                                                <label for="">Nhập nơi tổ chức</label>
                                            </div>
                                            <div class="mui-textfield">
                                                <input type="text" name="field_661e9442847ec" value="<?php if ($custom_party_address) echo $custom_party_address; ?>" placeholder="Số 5, ngõ 165 phố ...">
                                                <label for="">Nhập địa điểm tổ chức</label>
                                            </div>
                                            <div class="mui-textfield date_calculate">
                                                <input class="solar" type="datetime-local" name="field_661d631ff6a14" value="<?php if ($_custom_party_time) echo $_custom_party_time->format('Y-m-d\TH:i:00'); ?>">
                                                <label for="">Thời gian (dương lịch)</label>
                                            </div>
                                            <div class="mui-textfield">
                                                <label for="">Chọn vị trí tổ chức chính xác trên bản đồ</label>
                                                <!-- <input type="text" id="pac-input" class="controls"> -->
                                                <div id="gp_googlemaps" class="google_maps"></div>
                                            </div>
                                            <input id="gp_latlng" type="hidden" name="field_661f4f1a41f17" value="<?php if ($custom_party_maps) echo $custom_party_maps; ?>">
                                            <input type="hidden" name="whereupdate" value="<?php echo $where_update; ?>">
                                            <?php
                                            wp_nonce_field('wedding', 'wedding_field');
                                            ?>
                                            <div class="submit_div">
                                                <button type="submit" class="mui-btn mui-btn--danger">Cập nhật</button>
                                            </div>
                                        </form>
                                        <span class="close_button">X</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2 left_sidebar">
            <?php
            $menu = 'Hướng dẫn trang điền thông tin';
            $guideline = wp_nav_menu(array(
                'menu'          => $menu,
                'container_id'  => 'guide_section',
                'container_class'   => '',
                'items_wrap'    => '<a href="#" class="maximize"><i class="fa fa-external-link" aria-hidden="true"></i> Khôi phục</a><img src="' . get_template_directory_uri() . '/img/thaochi.jpg"><ul class="playlist"><h4>Hướng dẫn nhanh</h4>%3$s</ul><a href="#" class="minimize"><i class="fa fa-level-down" aria-hidden="true"></i> Thu nhỏ</a>',
                'menu_class'    => 'main_menu mb20',
                'echo' => FALSE,
                'fallback_cb' => '__return_false'
            ));

            if ( ! empty ( $guideline ) ){
                echo $guideline;
            }
            ?>
        </div>
    </div>
</div>
<div style="display:none;">
    <input type="text" id="pac-input" class="controls" placeholder="Tìm kiếm tọa độ chính xác của đám cưới">
</div>
<script src="<?php echo get_template_directory_uri(); ?>/js/cookie.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/guideline.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/soundmanager2-jsmin.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/soundmanager2-player.js"></script>
<link href="<?php echo get_template_directory_uri(); ?>/css/soundmanager2-player.css" rel="stylesheet" type="text/css">
<script>
    let latlngStr;

    function initGeocode() {
        /* read cookie */
        latlngStr = getCookie('latlngStr');
        /* if not have cookie, then get permission to guest's position */
        if (latlngStr == null) navigator.geolocation.getCurrentPosition(showLocation);
    }

    function showLocation(position){
        latlngStr = [
            position.coords.latitude,
            position.coords.longitude
        ].join(',');

        /* set cookie */
        setCookie('latlngStr', latlngStr, 7);
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api; ?>&callback=initGeocode&libraries=places" type="text/javascript"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/googlemaps.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/wedding-infomation.js"></script>
<?php
get_footer();