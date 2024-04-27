<?php
/* 
*  Template Name: Wedding Infomation
*/ 
get_header();
get_template_part('header', 'topbar');
// date_default_timezone_set('Asia/Ho_Chi_Minh');

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

$where_update = 'user_' . $current_user_id;

if ( isset($_POST['post_nonce_field']) &&
wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {
    $groom = trim(strip_tags($_POST['groom']));
    $bride = trim(strip_tags($_POST['bride']));
    
    if ($groom && $bride) {
        update_field('field_62b13a3d49b34', $groom, $where_update);
        update_field('field_62b13a4949b35', $bride, $where_update);
    }

    wp_redirect(get_permalink());
}

$groom  = get_field('groom', $where_update);
$bride  = get_field('bride', $where_update);
$active_groom   = get_field('active_groom', $where_update);
$active_bride   = get_field('active_bride', $where_update);
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
                <span class="title"> <?php the_title(); ?></span>
            </div>
            <div class="mui-panel" id="wedding_infomation">
                <h3>Thông tin nhân vật chính</h3>
                <?php 
                if(!$groom && !$bride) {
                    ?>
                    <form class="mui-form" method="POST">
                        <div class="mui-textfield">
                            <input type="text" name="groom">
                            <label for="">Tên chú rể</label>
                        </div>
                        <div class="heart_icon">
                            <img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt="">
                        </div>
                        <div class="mui-textfield">
                            <input type="text" name="bride">
                            <label for="">Tên cô dâu</label>
                        </div>
                        <?php
                        wp_nonce_field('post_nonce', 'post_nonce_field');
                        ?>
                        <div class="mui-textfield">
                            <button type="submit" class="mui-btn mui-btn--danger">Cập nhật</button>
                        </div>
                    </form>
                    <?php
                } else {
                    ?>
                    <div id="main-info">
                        <div class="mui-textfield groom">
                            <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b13a3d49b34" data-where="<?php echo $where_update; ?>">
                                <?php echo $groom; ?>
                            </span>
                        </div>
                        <div class="heart_icon">
                            <img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt="">
                        </div>
                        <div class="mui-textfield bride">
                            <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b13a4949b35" data-where="<?php echo $where_update; ?>">
                                <?php echo $bride; ?>
                            </span>
                        </div>
                    </div>
                    <h3>Ảnh cưới</h3>
                    <p>Lựa chọn một ảnh đẹp nhất của hai bạn để làm hình ảnh đại diện cho đám cưới</p>
                    <div>
                        <div class="wedding_photo">
                        <?php 
                            $wedding_photo = get_field('wedding_photo', $where_update);
                            if ($wedding_photo) {
                                echo "<button class='mui-btn hera-btn uploadbtn'>Đổi ảnh khác</button>";
                                echo '<img src="' . wp_get_attachment_url($wedding_photo) . '" alt="" width="300">';
                            } else {
                                echo "<button class='mui-btn hera-btn uploadbtn'>Tải ảnh lên</button>";
                            }
                        ?>
                        </div>
                        <div class="uploadform" id="uploadform">
                            <span class='close_btn'>x</span>
                            <form class="box" method="post" action="" enctype="multipart/form-data">
                                <div class="box__input">
                                    <input class="box__file" type="file" name="files[]" id="file" data-multiple-caption="{count} files selected" multiple />
                                    <label for="file" style="display: block;">
                                        <figure>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="34" viewBox="0 0 20 17">
                                                <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/>
                                            </svg>
                                        </figure> 
                                        <strong>Tải tệp lên</strong><br>
                                        <span class="box__dragndrop">hoặc kéo ảnh vào đây</span>.
                                    </label>
                                </div>
                                <div class="box__uploading">Uploading…</div>
                                <div class="box__success">Done!</div>
                                <div class="box__error">Error! <span></span></div>
                            </form>
                        </div>
                    </div>
                    <h3>Thông tin chi tiết</h3>
                    <div id="addition_info" class="mui-row">
                        <div class="mui-col-md-6">
                            <div class="content_info">
                                <?php 
                                    $groom_father           = get_field('groom_father', $where_update);
                                    $groom_mother           = get_field('groom_mother', $where_update);
                                    $groom_wedding_location = get_field('groom_wedding_location', $where_update);
                                    $groom_wedding_adress   = get_field('groom_wedding_adress', $where_update);
                                    $groom_wedding_maps     = get_field('groom_wedding_maps', $where_update);
                                    $groom_wedding_time     = get_field('groom_wedding_time', $where_update);
                                    $groom_wedding_moontime = get_field('groom_wedding_moontime', $where_update);
                                    $groom_party_location   = get_field('groom_party_location', $where_update);
                                    $groom_party_address    = get_field('groom_party_address', $where_update);
                                    $groom_party_maps       = get_field('groom_party_maps', $where_update);
                                    $groom_party_time       = get_field('groom_party_time', $where_update);
                                    $groom_party_moontime   = get_field('groom_party_moontime', $where_update);

                                    if ($groom_wedding_time) {
                                        $_groom_wedding_time    = DateTime::createFromFormat('d/m/Y H:i', $groom_wedding_time);
                                    } else {
                                        $_groom_wedding_time    = new DateTime();
                                    }
                                    if($groom_party_time) {
                                        $_groom_party_time  = DateTime::createFromFormat('d/m/Y H:i', $groom_party_time);
                                    } else {
                                        $_groom_party_time  = new DateTime();
                                    }
                                    $_groom_wedding_moontime= DateTime::createFromFormat('d/m/Y H:i', $groom_wedding_moontime);
                                    $_groom_party_moontime  = DateTime::createFromFormat('d/m/Y H:i', $groom_party_moontime);

                                ?>
                                <section class="content_box">
                                    <span class="content_title">
                                        <h4>Nhà trai</h4>
                                        <span class="edit_section" data-form='#groom_parents_form'>
                                        <?php 
                                        if ($groom_father || $groom_mother) {
                                            echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                        }
                                        ?>
                                        </span> 
                                    </span>
                                    <div class="group_data">
                                        <?php
                                            if ($groom_father || $groom_mother) {
                                                echo '<div class="has_data">';

                                                if ($groom_father) {
                                                    echo '<div class="data_item"><i class="fa fa-male"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b128ec93a7f" data-where="' . $where_update . '">' . $groom_father . '</span></div>';
                                                }
                                                if ($groom_mother) {
                                                    echo '<div class="data_item"><i class="fa fa-female"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b129b693a80" data-where="' . $where_update . '">' . $groom_mother . '</span></div>';
                                                }
                                                echo "</div>";
                                            } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#groom_parents_form'>
                                                <a class="section_name" href="#">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    <span>Thêm tên bố mẹ chú rể...</span>
                                                </a>
                                            </div>
                                        <?php
                                            }
                                        ?>
                                        <div id="groom_parents_form" class="hide_form">
                                            <form class="mui-form" method="POST">
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_62b128ec93a7f" value="<?php echo $groom_father; ?>">
                                                    <label for="">Bố chú rể</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_62b129b693a80" value="<?php echo $groom_mother; ?>">
                                                    <label for="">Mẹ chú rể</label>
                                                </div>
                                                <?php
                                                wp_nonce_field('wedding', 'wedding_field');
                                                ?>
                                                <input type="hidden" name="whereupdate" value="<?php echo $where_update; ?>">
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
                                        <h4>Địa điểm, thời gian tổ chức hôn lễ</h4>
                                        <span class="edit_section" data-form='#groom_wedding_form' data-mapid="gw_googlemaps" data-latlng='gw_latlng'>
                                        <?php 
                                            if ($groom_wedding_adress) {
                                                echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                            }
                                        ?>
                                        </span>
                                    </span>
                                    <div class="group_data">
                                        <?php
                                        if ($groom_wedding_adress) {
                                            echo '<div class="has_data">';
                                            echo '<div class="data_item">
                                                    <i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_6568ba0dd5db1" data-where="' . $where_update . '">' . $groom_wedding_location . '</span>
                                                </div>';
                                            echo '<div class="data_item"><i class="fa fa-map-marker"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b12acd93a81" data-where="' . $where_update . '">' . $groom_wedding_adress . '</span></div>';
                                            echo '<div class="date_editable data_item">
                                                    <div class="date_data">
                                                        <i class="fa fa-calendar"></i> <span class="diveditable">' . $_groom_wedding_time->format('d/m/Y g:i a') . '</span>
                                                    </div>
                                                    <div class="date_input">
                                                        <form method="post">
                                                            <input name="solartime" type="datetime-local" value="' . $_groom_wedding_time->format('Y-m-d\TH:i:s') . '">
                                                            <input type="hidden" name="solartime_field" value="field_62b12b8f93a83">
                                                            <input type="hidden" name="lunartime_field" value="field_62b135cb93a85">
                                                            <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                            <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                        </form>
                                                    </div>
                                                </div>';
                                            if ($groom_wedding_moontime) {
                                                echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . formatLunarDate($_groom_wedding_moontime, 'Ngày dd tháng mm năm MYMY') . '</span></div>'; 
                                            }
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#groom_wedding_form' data-mapid="gw_googlemaps" data-latlng='gw_latlng'>
                                                <a class="section_name" href="#">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    <span>Địa điểm tổ chức hôn lễ tại nhà trai</span>
                                                </a>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <div id="groom_wedding_form" class="hide_form">
                                            <form class="mui-form" method="POST">
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_6568ba0dd5db1" value="<?php if ($groom_wedding_location) echo $groom_wedding_location; ?>" placeholder="Tư gia, Trung tâm tiệc cưới, Nhà văn hóa ...">
                                                    <label for="">Nhập nơi tổ chức</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_62b12acd93a81" value="<?php if ($groom_wedding_adress) echo $groom_wedding_adress; ?>">
                                                    <label for="">Nhập địa điểm tổ chức</label>
                                                </div>
                                                <div class="mui-textfield date_calculate">
                                                    <input class="solar" type="datetime-local" name="field_62b12b8f93a83" value="<?php if ($_groom_wedding_time) echo $_groom_wedding_time->format('Y-m-d\TH:i:s'); ?>">
                                                    <input class="lunar" type="hidden" name="field_62b135cb93a85" value="<?php if ($_groom_wedding_moontime) echo $_groom_wedding_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                    <label for="">Thời gian (dương lịch)</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <label for="">Chọn vị trí tổ chức chính xác trên bản đồ</label>
                                                    <!-- <input type="text" id="pac-input" class="controls"> -->
                                                    <div id="gw_googlemaps" class="google_maps"></div>
                                                </div>
                                                <input id="gw_latlng" type="hidden" name="field_63dbd489cc720" value="<?php if ($groom_wedding_maps) echo $groom_wedding_maps; ?>">
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
                                        <span class="edit_section" data-form='#groom_party_form' data-mapid='gp_googlemaps' data-latlng='gp_latlng'>
                                        <?php 
                                        if ($groom_party_address) {
                                            echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                        }
                                        ?>
                                        </span>
                                    </span>
                                    <div class="group_data">
                                        <?php
                                        if ($groom_party_address) {
                                            echo '<div class="has_data">';
                                            echo '<div class="data_item">
                                                    <i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_6568ba25d5db2" data-where="' . $where_update . '">' . $groom_party_location . '</span>
                                                </div>';
                                            echo '<div class="data_item"><i class="fa fa-map-marker"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b12b4593a82" data-where="' . $where_update . '">' . $groom_party_address . '</span></div>';
                                            echo '<div class="date_editable data_item">
                                                    <div class="date_data">
                                                        <i class="fa fa-calendar"></i> <span class="diveditable">' . $_groom_party_time->format('d/m/Y g:i a') . '</span>
                                                    </div>
                                                    <div class="date_input">
                                                        <form method="post">
                                                            <input name="solartime" type="datetime-local" value="' . $_groom_party_time->format('Y-m-d\TH:i:s') . '">
                                                            <input type="hidden" name="solartime_field" value="field_62b12bb293a84">
                                                            <input type="hidden" name="lunartime_field" value="field_62b13605bfa89">
                                                            <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                            <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                        </form>
                                                    </div>
                                                </div>';
                                            echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . formatLunarDate($_groom_party_moontime, 'Ngày dd tháng mm năm MYMY') . '</span></div>';
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#groom_party_form' data-mapid='gp_googlemaps' data-latlng='gp_latlng'>
                                                <a class="section_name" href="#">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    <span>Địa điểm dự tiệc của nhà trai</span>
                                                </a>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <div id="groom_party_form" class="hide_form">
                                            <form class="mui-form" method="POST">
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_6568ba25d5db2" value="<?php if ($groom_party_location) echo $groom_party_location; ?>" placeholder="Tư gia, Trung tâm tiệc cưới, Nhà văn hóa ...">
                                                    <label for="">Nhập nơi tổ chức</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_62b12b4593a82" value="<?php if ($groom_party_address) echo $groom_party_address; ?>">
                                                    <label for="">Nhập địa điểm tổ chức</label>
                                                </div>
                                                <div class="mui-textfield date_calculate">
                                                    <input class="solar" type="datetime-local" name="field_62b12bb293a84" value="<?php if ($_groom_party_time) echo $_groom_party_time->format('Y-m-d\TH:i:s'); ?>">
                                                    <input class="lunar" type="hidden" name="field_62b13605bfa89" value="<?php if ($_groom_party_moontime) echo $_groom_party_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                    <label for="">Thời gian (dương lịch)</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <label for="">Chọn vị trí tổ chức chính xác trên bản đồ</label>
                                                    <!-- <input type="text" id="pac-input" class="controls"> -->
                                                    <div id="gp_googlemaps" class="google_maps"></div>
                                                </div>
                                                <input id="gp_latlng" type="hidden" name="field_63dbd4bfcc721" value="<?php if ($groom_party_maps) echo $groom_party_maps; ?>">
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
                        <div class="mui-col-md-6">
                            <div class="content_info">
                                <?php 
                                    $bride_father           = get_field('bride_father', $where_update);
                                    $bride_mother           = get_field('bride_mother', $where_update);
                                    $bride_wedding_location = get_field('bride_wedding_location', $where_update);
                                    $bride_wedding_adress   = get_field('bride_wedding_adress', $where_update);
                                    $bride_wedding_maps     = get_field('bride_wedding_maps', $where_update);
                                    $bride_wedding_time     = get_field('bride_wedding_time', $where_update);
                                    $bride_wedding_moontime = get_field('bride_wedding_moontime', $where_update);
                                    $bride_party_location   = get_field('bride_party_location', $where_update);
                                    $bride_party_address    = get_field('bride_party_address', $where_update);
                                    $bride_party_maps       = get_field('bride_party_maps', $where_update);
                                    $bride_party_time       = get_field('bride_party_time', $where_update);
                                    $bride_party_moontime   = get_field('bride_party_moontime', $where_update);

                                    if ($bride_wedding_time) {
                                        $_bride_wedding_time    = DateTime::createFromFormat('d/m/Y H:i', $bride_wedding_time);
                                    } else {
                                        $_bride_wedding_time    = new DateTime();
                                    }
                                    if ($bride_party_time) {
                                        $_bride_party_time  = DateTime::createFromFormat('d/m/Y H:i', $bride_party_time);
                                    } else {
                                        $_bride_party_time  = new DateTime();
                                    }
                                    $_bride_wedding_moontime= DateTime::createFromFormat('d/m/Y H:i', $bride_wedding_moontime);
                                    $_bride_party_moontime  = DateTime::createFromFormat('d/m/Y H:i', $bride_party_moontime);
                                ?>
                                <section class="content_box">
                                    <span class="content_title">
                                        <h4>Nhà gái</h4>
                                        <span class="edit_section" data-form='#bride_parents_form'>
                                        <?php 
                                        if ($bride_father || $bride_mother) {
                                            echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                        }
                                        ?>
                                        </span>
                                    </span>
                                    <div class="group_data">
                                        <?php 
                                            if ($bride_father || $bride_mother){
                                                echo '<div class="has_data">';
                                                if ($bride_father) {
                                                    echo '<div class="data_item"><i class="fa fa-male"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b1363fb0691" data-where="' . $where_update . '">' . $bride_father . '</span></div>';
                                                }
                                                if ($bride_mother) {
                                                    echo '<div class="data_item"><i class="fa fa-female"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b1363fb069e" data-where="' . $where_update . '">' . $bride_mother . '</span></div>';
                                                }
                                                echo '</div>';
                                            } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#bride_parents_form'>
                                                <a class="section_name" href="#">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    <span>Thêm tên bố mẹ cô dâu...</span>
                                                </a>
                                            </div>
                                        <?php 
                                            }
                                        ?>
                                        <div id="bride_parents_form" class="hide_form">
                                            <form class="mui-form" method="POST">
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_62b1363fb0691" value="<?php echo $bride_father; ?>">
                                                    <label for="">Bố cô dâu</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_62b1363fb069e" value="<?php echo $bride_mother; ?>">
                                                    <label for="">Mẹ cô dâu</label>
                                                </div>
                                                <?php
                                                wp_nonce_field('wedding', 'wedding_field');
                                                ?>
                                                <input type="hidden" name="whereupdate" value="<?php echo $where_update; ?>">
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
                                        <h4>Địa điểm, thời gian tổ chức lễ vu quy</h4>
                                        <span class="edit_section" data-form='#bride_wedding_form' data-mapid='bw_googlemaps' data-latlng='bw_latlng'>
                                        <?php 
                                        if ($bride_wedding_adress) {
                                            echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                        }
                                        ?>
                                        </span>
                                    </span>
                                    <div class="group_data">
                                        <?php
                                        if ($bride_wedding_adress) {
                                            echo '<div class="has_data">';
                                            echo '<div class="data_item">
                                                    <i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_6568a76696279" data-where="' . $where_update . '">' . $bride_wedding_location . '</span>
                                                </div>';
                                            echo '<div class="data_item"><i class="fa fa-map-marker"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b1363fb06a6" data-where="' . $where_update . '">' . $bride_wedding_adress . '</span></div>';

                                            if ($bride_wedding_time) {
                                                echo '<div class="date_editable data_item">
                                                        <div class="date_data">
                                                            <i class="fa fa-calendar"></i> <span class="diveditable">' . $_bride_wedding_time->format('d/m/Y g:i a') . '</span>
                                                        </div>
                                                        <div class="date_input">
                                                            <form method="post">
                                                                <input name="solartime" type="datetime-local" value="' . $_bride_wedding_time->format('Y-m-d\TH:i:s') . '">
                                                                <input type="hidden" name="solartime_field" value="field_62b1363fb06af">
                                                                <input type="hidden" name="lunartime_field" value="field_62b1363fb06b7">
                                                                <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                                <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                            </form>
                                                        </div>
                                                    </div>';
                                                
                                                if ($bride_wedding_moontime) {
                                                    echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . formatLunarDate($_bride_wedding_moontime, 'Ngày dd tháng mm năm MYMY') . '</span></div>';
                                                }
                                            }
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#bride_wedding_form' data-mapid='bw_googlemaps' data-latlng='bw_latlng'>
                                                <a class="section_name" href="#">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    <span>Thêm địa điểm tổ chức lễ vu quy tại nhà gái...</span>
                                                </a>
                                            </div>
                                        <?php 
                                        }
                                        ?>
                                            <div id="bride_wedding_form" class="hide_form">
                                                <form class="mui-form" method="POST">
                                                    <div class="mui-textfield">
                                                        <input type="text" name="field_6568a76696279" value="<?php if ($bride_wedding_location) echo $bride_wedding_location; ?>" placeholder="Tư gia, Trung tâm tiệc cưới, Nhà văn hóa ...">
                                                        <label for="">Nhập nơi tổ chức</label>
                                                    </div>
                                                    <div class="mui-textfield">
                                                        <input type="text" name="field_62b1363fb06a6" value="<?php if ($bride_wedding_adress) echo $bride_wedding_adress; ?>">
                                                        <label for="">Nhập địa điểm tổ chức</label>
                                                    </div>
                                                    <div class="mui-textfield date_calculate">
                                                        <input class="solar" type="datetime-local" name="field_62b1363fb06af" value="<?php if ($_bride_wedding_time) echo $_bride_wedding_time->format('Y-m-d\TH:i:s'); ?>">
                                                        <input class="lunar" type="hidden" name="field_62b1363fb06b7" value="<?php if ($_bride_wedding_moontime) echo $_bride_wedding_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                        <label for="">Thời gian (dương lịch)</label>
                                                    </div>
                                                    <div class="mui-textfield">
                                                        <label for="">Chọn vị trí tổ chức chính xác trên bản đồ</label>
                                                        <div id="bw_googlemaps" class="google_maps"></div>
                                                    </div>
                                                    <input id="bw_latlng" type="hidden" name="field_63dbd5791d673" value="<?php if ($bride_wedding_maps) echo $bride_wedding_maps; ?>">
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
                                        <span class="edit_section" data-form='#bride_party_form' data-mapid='bp_googlemaps' data-latlng='bp_latlng'>
                                        <?php 
                                        if ($groom_party_address) {
                                            echo '<i class="fa fa-pencil" aria-hidden="true"></i>';
                                        }
                                        ?>
                                        </span>
                                    </span>
                                    <div class="group_data">
                                        <?php
                                        if ($bride_party_address) {
                                            echo '<div class="has_data">';
                                            echo '<div class="data_item">
                                                    <i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_6568a8029627a" data-where="' . $where_update . '">' . $bride_party_location . '</span>
                                                </div>';
                                            echo '<div class="data_item"><i class="fa fa-map-marker"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return true" data-field="field_62b1363fb06bf" data-where="' . $where_update . '">' . $bride_party_address . '</span></div>';
                                            echo '<div class="date_editable data_item">
                                                    <div class="date_data">
                                                        <i class="fa fa-calendar"></i> <span class="diveditable">' . $_bride_party_time->format('d/m/Y g:i a') . '</span>
                                                    </div>
                                                    <div class="date_input">
                                                        <form method="post">
                                                            <input name="solartime" type="datetime-local" value="' . $_bride_party_time->format('Y-m-d\TH:i:s') . '">
                                                            <input type="hidden" name="solartime_field" value="field_62b1363fb06c7">
                                                            <input type="hidden" name="lunartime_field" value="field_62b1363fb06cf">
                                                            <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                            <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                        </form>
                                                    </div>    
                                                </div>';
                                            echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . formatLunarDate($_bride_party_moontime, 'Ngày dd tháng mm năm MYMY') . '</span></div>';
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#bride_party_form' data-mapid='bp_googlemaps' data-latlng='bp_latlng'>
                                                <a class="section_name" href="#">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    <span>Địa điểm dự tiệc của nhà gái</span>
                                                </a>
                                            </div>
                                        <?php 
                                        }
                                        ?>
                                        <div id="bride_party_form" class="hide_form">
                                            <form class="mui-form" method="POST">
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_6568a8029627a" value="<?php if ($bride_party_location) echo $bride_party_location; ?>" placeholder="Tư gia, Trung tâm tiệc cưới, Nhà văn hóa ...">
                                                    <label for="">Nhập nơi tổ chức</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <input type="text" name="field_62b1363fb06bf" value="<?php if ($bride_party_address) echo $bride_party_address; ?>">
                                                    <label for="">Nhập địa điểm tổ chức</label>
                                                </div>
                                                <div class="mui-textfield date_calculate">
                                                    <input class="solar" type="datetime-local" name="field_62b1363fb06c7" value="<?php if ($_bride_party_time) echo $_bride_party_time->format('Y-m-d\TH:i:s'); ?>">
                                                    <input class="lunar" type="hidden" name="field_62b1363fb06cf" value="<?php if ($_bride_party_moontime) echo $_bride_party_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                    <label for="">Thời gian (dương lịch)</label>
                                                </div>
                                                <div class="mui-textfield">
                                                    <label for="">Chọn vị trí tổ chức chính xác trên bản đồ</label>
                                                    <!-- <input type="text" id="pac-input" class="controls"> -->
                                                    <div id="bp_googlemaps" class="google_maps"></div>
                                                </div>
                                                <input id="bp_latlng" type="hidden" name="field_63dbd5ab1d674" value="<?php if ($bride_party_maps) echo $bride_party_maps; ?>">
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
                <?php
                }
                ?>
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