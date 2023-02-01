<?php
/* 
*  Template Name: Wedding Infomation
*/ 
get_header();
get_template_part('header', 'topbar');
date_default_timezone_set('Asia/Ho_Chi_Minh');

# get some of user infomation
$current_user_id = get_current_user_id();
$where_update = 'user_' . $current_user_id;

if ( isset($_POST['post_nonce_field']) &&
wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {
    $groom = strip_tags($_POST['groom']);
    $bride = strip_tags($_POST['bride']);
    
    if ($groom) {
        update_field('field_62b13a3d49b34', $groom, $where_update);
    }
    if ($bride) {
        update_field('field_62b13a4949b35', $bride, $where_update);
    }

    wp_redirect(get_permalink());
}

$groom  = get_field('groom', $where_update);
$bride  = get_field('bride', $where_update);
$active_groom   = get_field('active_groom', $where_update);
$active_bride   = get_field('active_bride', $where_update);

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
                <a href="<?php echo get_bloginfo('url'); ?>">Trang chủ</a>
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
                            <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b13a3d49b34">
                                <?php echo $groom; ?>
                            </span>
                        </div>
                        <div class="heart_icon">
                            <img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt="">
                        </div>
                        <div class="mui-textfield bride">
                            <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b13a4949b35">
                                <?php echo $bride; ?>
                            </span>
                        </div>
                    </div>
                    <h3>Thông tin chi tiết</h3>
                    <div id="addition_info" class="mui-row">
                        <div class="mui-col-md-6">
                            <div class="content_info">
                                <?php 
                                    $groom_father           = get_field('groom_father', $where_update);
                                    $groom_mother           = get_field('groom_mother', $where_update);
                                    $groom_wedding_adress   = get_field('groom_wedding_adress', $where_update);
                                    $groom_wedding_time     = get_field('groom_wedding_time', $where_update);
                                    $groom_wedding_moontime = get_field('groom_wedding_moontime', $where_update);
                                    $groom_party_address    = get_field('groom_party_address', $where_update);
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
                                                    echo '<div class="data_item"><i class="fa fa-male"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b128ec93a7f" data-where="' . $where_update . '">' . $groom_father . '</span></div>';
                                                }
                                                if ($groom_mother) {
                                                    echo '<div class="data_item"><i class="fa fa-female"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b129b693a80" data-where="' . $where_update . '">' . $groom_mother . '</span></div>';
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
                                        <span class="edit_section" data-form='#groom_wedding_form'>
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
                                            echo '<div class="data_item"><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b12acd93a81" data-where="' . $where_update . '">' . $groom_wedding_adress . '</span></div>';
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
                                                echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $_groom_wedding_moontime->format('d/m/Y g:i a') . '</span></div>'; 
                                            }
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#groom_wedding_form'>
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
                                                    <input type="text" name="field_62b12acd93a81" value="<?php if ($groom_wedding_adress) echo $groom_wedding_adress; ?>">
                                                    <label for="">Địa điểm</label>
                                                </div>
                                                <div class="mui-textfield date_calculate">
                                                    <input class="solar" type="datetime-local" name="field_62b12b8f93a83" value="<?php if ($_groom_wedding_time) echo $_groom_wedding_time->format('Y-m-d\TH:i:s'); ?>">
                                                    <input class="lunar" type="hidden" name="field_62b135cb93a85" value="<?php if ($_groom_wedding_moontime) echo $_groom_wedding_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                    <label for="">Thời gian (dương lịch)</label>
                                                </div>
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
                                        <span class="edit_section" data-form='#groom_party_form'>
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
                                            echo '<div class="data_item"><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b12b4593a82" data-where="' . $where_update . '">' . $groom_party_address . '</span></div>';
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
                                            echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $_groom_party_moontime->format('d/m/Y g:i a') . '</span></div>';
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#groom_party_form'>
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
                                                    <input type="text" name="field_62b12b4593a82" value="<?php if ($groom_party_address) echo $groom_party_address; ?>">
                                                    <label for="">Địa điểm</label>
                                                </div>
                                                <div class="mui-textfield date_calculate">
                                                    <input class="solar" type="datetime-local" name="field_62b12bb293a84" value="<?php if ($_groom_party_time) echo $_groom_party_time->format('Y-m-d\TH:i:s'); ?>">
                                                    <input class="lunar" type="hidden" name="field_62b13605bfa89" value="<?php if ($_groom_party_moontime) echo $_groom_party_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                    <label for="">Thời gian (dương lịch)</label>
                                                </div>
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
                                    $bride_wedding_adress   = get_field('bride_wedding_adress', $where_update);
                                    $bride_wedding_time     = get_field('bride_wedding_time', $where_update);
                                    $bride_wedding_moontime = get_field('bride_wedding_moontime', $where_update);
                                    $bride_party_address    = get_field('bride_party_address', $where_update);
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
                                                    echo '<div class="data_item"><i class="fa fa-male"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b1363fb0691" data-where="' . $where_update . '">' . $bride_father . '</span></div>';
                                                }
                                                if ($bride_mother) {
                                                    echo '<div class="data_item"><i class="fa fa-female"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b1363fb069e" data-where="' . $where_update . '">' . $bride_mother . '</span></div>';
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
                                        <span class="edit_section" data-form='#bride_wedding_form'>
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
                                            echo '<div class="data_item"><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b1363fb06a6" data-where="' . $where_update . '">' . $bride_wedding_adress . '</span></div>';

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
                                                    echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $_bride_wedding_moontime->format('d/m/Y g:i a') . '</span></div>';
                                                }
                                            }
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#bride_wedding_form'>
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
                                                        <input type="text" name="field_62b1363fb06a6" value="<?php if ($bride_wedding_adress) echo $bride_wedding_adress; ?>">
                                                        <label for="">Địa điểm</label>
                                                    </div>
                                                    <div class="mui-textfield date_calculate">
                                                        <input class="solar" type="datetime-local" name="field_62b1363fb06af" value="<?php if ($_bride_wedding_time) echo $_bride_wedding_time->format('Y-m-d\TH:i:s'); ?>">
                                                        <input class="lunar" type="hidden" name="field_62b1363fb06b7" value="<?php if ($_bride_wedding_moontime) echo $_bride_wedding_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                        <label for="">Thời gian (dương lịch)</label>
                                                    </div>
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
                                        <span class="edit_section" data-form='#bride_party_form'>
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
                                            echo '<div class="data_item"><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true oncut="return false" onpaste="return false" data-field="field_62b1363fb06bf" data-where="' . $where_update . '">' . $bride_party_address . '</span></div>';
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
                                            echo '<div class="lunar_date data_item"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $_bride_party_moontime->format('d/m/Y g:i a') . '</span></div>';
                                            echo '</div>';
                                        } else {
                                        ?>
                                            <div class="no_data edit_section data_item" data-form='#bride_party_form'>
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
                                                    <input type="text" name="field_62b1363fb06bf" value="<?php if ($bride_party_address) echo $bride_party_address; ?>">
                                                    <label for="">Địa điểm</label>
                                                </div>
                                                <div class="mui-textfield date_calculate">
                                                    <input class="solar" type="datetime-local" name="field_62b1363fb06c7" value="<?php if ($_bride_party_time) echo $_bride_party_time->format('Y-m-d\TH:i:s'); ?>">
                                                    <input class="lunar" type="hidden" name="field_62b1363fb06cf" value="<?php if ($_bride_party_moontime) echo $_bride_party_moontime->format('Y-m-d\TH:i:s'); ?>">
                                                    <label for="">Thời gian (dương lịch)</label>
                                                </div>
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
        <div class="mui-col-md-2"></div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/js/wedding-infomation.js"></script>
<?php
get_footer();