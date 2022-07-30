<?php
/* 
*  Template Name: Wedding Infomation
*/ 
get_header();
get_template_part('header', 'topbar');

# get some of user infomation
$current_user_id = get_current_user_id();

if ( isset($_POST['post_nonce_field']) &&
wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {
    $groom = strip_tags($_POST['groom']);
    $bride = strip_tags($_POST['bride']);
    
    if ($groom) {
        update_field('field_62b13a3d49b34', $groom, 'user_' . $current_user_id);
    }
    if ($bride) {
        update_field('field_62b13a4949b35', $bride, 'user_' . $current_user_id);
    }
}

$groom  = get_field('groom', 'user_' . $current_user_id);
$bride  = get_field('bride', 'user_' . $current_user_id);
$active_groom   = get_field('active_groom', 'user_' . $current_user_id);
$active_bride   = get_field('active_bride', 'user_' . $current_user_id);

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2">
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
                            <?php echo $groom; ?>
                        </div>
                        <div class="heart_icon">
                            <img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt="">
                        </div>
                        <div class="mui-textfield bride">
                            <?php echo $bride; ?>
                        </div>
                    </div>
                    <h3>Thông tin chi tiết</h3>
                    <div id="addition_info" class="mui-row">
                        <div class="mui-col-md-6">
                            <div class="content_info">
                                <h4>Nhà trai</h4>
                                <?php 
                                    $groom_father           = get_field('groom_father', 'user_' . $current_user_id);
                                    $groom_mother           = get_field('groom_mother', 'user_' . $current_user_id);
                                    $groom_wedding_adress   = get_field('groom_wedding_adress', 'user_' . $current_user_id);
                                    $groom_wedding_time     = get_field('groom_wedding_time', 'user_' . $current_user_id);
                                    $groom_wedding_moontime = get_field('groom_wedding_moontime', 'user_' . $current_user_id);
                                    $groom_party_address    = get_field('groom_party_address', 'user_' . $current_user_id);
                                    $groom_party_time       = get_field('groom_party_time', 'user_' . $current_user_id);
                                    $groom_party_moontime   = get_field('groom_party_moontime', 'user_' . $current_user_id);

                                    if ($groom_father) {
                                        echo '<div><i class="fa fa-male"></i> <span class="diveditable" contenteditable=true data-field="field_62b128ec93a7f">' . $groom_father . '</span></div>';
                                    }
                                    if ($groom_mother) {
                                        echo '<div><i class="fa fa-female"></i> <span class="diveditable" contenteditable=true data-field="field_62b129b693a80">' . $groom_mother . '</span></div>';
                                    }
                                    if ($groom_wedding_adress) {
                                        echo '<h4>Địa điểm, thời gian tổ chức hôn lễ</h4>';
                                        echo '<div><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true data-field="field_62b12acd93a81">' . $groom_wedding_adress . '</span></div>';
                                        echo '<div class="date_editable">
                                                <div class="date_data">
                                                    <i class="fa fa-calendar"></i> <span class="diveditable">' . $groom_wedding_time . '</span>
                                                </div>
                                                <div class="date_input">
                                                    <form method="post">
                                                        <input name="solartime" type="datetime-local">
                                                        <input type="hidden" name="solartime_field" value="field_62b12b8f93a83">
                                                        <input type="hidden" name="lunartime_field" value="field_62b135cb93a85">
                                                        <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                        <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                    </form>
                                                </div>
                                            </div>';
                                        echo '<div class="lunar_date"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $groom_wedding_moontime . '</span></div>';
                                    }
                                    if ($groom_party_address) {
                                        echo '<h4>Địa điểm, thời gian tổ chức tiệc cưới</h4>';
                                        echo '<div><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true data-field="field_62b12b4593a82">' . $groom_party_address . '</span></div>';
                                        echo '<div class="date_editable">
                                                <div class="date_data">
                                                    <i class="fa fa-calendar"></i> <span class="diveditable">' . $groom_party_time . '</span>
                                                </div>
                                                <div class="date_input">
                                                    <form method="post">
                                                        <input name="solartime" type="datetime-local">
                                                        <input type="hidden" name="solartime_field" value="field_62b12bb293a84">
                                                        <input type="hidden" name="lunartime_field" value="field_62b13605bfa89">
                                                        <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                        <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                    </form>
                                                </div>
                                            </div>';
                                        echo '<div class="lunar_date"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $groom_party_moontime . '</span></div>';
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="mui-col-md-6">
                            <div class="content_info">
                                <h4>Nhà gái</h4>
                                <?php 
                                    $bride_father           = get_field('bride_father', 'user_' . $current_user_id);
                                    $bride_mother           = get_field('bride_mother', 'user_' . $current_user_id);
                                    $bride_wedding_adress   = get_field('bride_wedding_adress', 'user_' . $current_user_id);
                                    $bride_wedding_time     = get_field('bride_wedding_time', 'user_' . $current_user_id);
                                    $bride_wedding_moontime = get_field('bride_wedding_moontime', 'user_' . $current_user_id);
                                    $bride_party_address    = get_field('bride_party_address', 'user_' . $current_user_id);
                                    $bride_party_time       = get_field('bride_party_time', 'user_' . $current_user_id);
                                    $bride_party_moontime   = get_field('bride_party_moontime', 'user_' . $current_user_id);

                                    if ($bride_father) {
                                        echo '<div><i class="fa fa-male"></i> <span class="diveditable" contenteditable=true data-field="field_62b1363fb0691">' . $bride_father . '</span></div>';
                                    }
                                    if ($bride_mother) {
                                        echo '<div><i class="fa fa-female"></i> <span class="diveditable" contenteditable=true data-field="field_62b1363fb069e">' . $bride_mother . '</span></div>';
                                    }
                                    if ($bride_wedding_adress) {
                                        echo '<h4>Địa điểm, thời gian tổ chức lễ vu quy</h4>';
                                        echo '<div><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true data-field="field_62b1363fb06a6">' . $bride_wedding_adress . '</span></div>';
                                        echo '<div class="date_editable">
                                                <div class="date_data">
                                                    <i class="fa fa-calendar"></i> <span class="diveditable">' . $bride_wedding_time . '</span>
                                                </div>
                                                <div class="date_input">
                                                    <form method="post">
                                                        <input name="solartime" type="datetime-local">
                                                        <input type="hidden" name="solartime_field" value="field_62b1363fb06af">
                                                        <input type="hidden" name="lunartime_field" value="field_62b1363fb06b7">
                                                        <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                        <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                    </form>
                                                </div>
                                            </div>';
                                        echo '<div class="lunar_date"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $bride_wedding_moontime . '</span></div>';
                                    }
                                    if ($bride_party_address) {
                                        echo '<h4>Địa điểm, thời gian tổ chức tiệc cưới</h4>';
                                        echo '<div><i class="fa fa-building"></i> <span class="diveditable" contenteditable=true data-field="field_62b1363fb06bf">' . $bride_party_address . '</span></div>';
                                        echo '<div class="date_editable">
                                                <div class="date_data">
                                                    <i class="fa fa-calendar"></i> <span class="diveditable">' . $bride_party_time . '</span>
                                                </div>
                                                <div class="date_input">
                                                    <form method="post">
                                                        <input name="solartime" type="datetime-local">
                                                        <input type="hidden" name="solartime_field" value="field_62b1363fb06c7">
                                                        <input type="hidden" name="lunartime_field" value="field_62b1363fb06cf">
                                                        <button class="mui-btn mui-btn--small hera-btn">Sửa</button>
                                                        <button class="mui-btn mui-btn--small close-btn-mini">X</button>
                                                    </form>
                                                </div>    
                                            </div>';
                                        echo '<div class="lunar_date"><i class="fa fa-calendar-o"></i> (âm lịch)<span class="diveditable">' . $bride_party_moontime . '</span></div>';
                                    }

                                    // $day = '2022-07-23T02:42';
                                    // $today = new DateTime($day);
                                    // $lunar = ShowLunarDate($today, 'YYYY-mm-dd');

                                    // echo $today->format('d/m/Y g:i a');
                                    // echo "<br>";
                                    // print_r($lunar . substr($day, 10));
                                    // update_field('field_62b1363fb06c7', strtotime($day), 'user_' . $current_user_id);
                                    // update_field('field_62b1363fb06cf', strtotime($lunar . substr($day, 10)), 'user_' . $current_user_id);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div id="add_more_form">
                        <h3>Thông tin cần cập nhật</h3>
                        <div id="parents_form" class="hide_form">
                            <form class="mui-form" method="POST">
                                <h4>Họ nhà trai</h4>
                                <div class="mui-textfield">
                                    <input type="text" name="field_62b128ec93a7f" value="<?php echo $groom_father; ?>">
                                    <label for="">Bố chú rể</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="text" name="field_62b129b693a80" value="<?php echo $groom_mother; ?>">
                                    <label for="">Mẹ chú rể</label>
                                </div>
                                <h4>Họ nhà gái</h4>
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
                        <div id="groom_wedding_form" class="hide_form">
                            <form class="mui-form" method="POST">
                                <h4>Địa điểm tổ chức hôn lễ tại nhà trai</h4>
                                <div class="mui-textfield">
                                    <input type="text" name="field_62b12acd93a81">
                                    <label for="">Địa điểm</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b12b8f93a83">
                                    <label for="">Thời gian (dương lịch)</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b135cb93a85">
                                    <label for="">Thời gian (âm lịch)</label>
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
                        <div id="groom_party_form" class="hide_form">
                            <form class="mui-form" method="POST">
                                <h4>Địa điểm dự tiệc của nhà trai</h4>
                                <div class="mui-textfield">
                                    <input type="text" name="field_62b12b4593a82">
                                    <label for="">Địa điểm</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b12bb293a84">
                                    <label for="">Thời gian (dương lịch)</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b13605bfa89">
                                    <label for="">Thời gian (âm lịch)</label>
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
                        <div id="bride_wedding_form" class="hide_form">
                            <form class="mui-form" method="POST">
                                <h4>Địa điểm tổ chức lễ vu quy tại nhà gái</h4>
                                <div class="mui-textfield">
                                    <input type="text" name="field_62b1363fb06a6">
                                    <label for="">Địa điểm</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b1363fb06af">
                                    <label for="">Thời gian (dương lịch)</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b1363fb06b7">
                                    <label for="">Thời gian (âm lịch)</label>
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
                        <div id="bride_party_form" class="hide_form">
                            <form class="mui-form" method="POST">
                                <h4>Địa điểm dự tiệc của nhà gái</h4>
                                <div class="mui-textfield">
                                    <input type="text" name="field_62b1363fb06bf">
                                    <label for="">Địa điểm</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b1363fb06c7">
                                    <label for="">Thời gian (dương lịch)</label>
                                </div>
                                <div class="mui-textfield">
                                    <input type="datetime-local" name="field_62b1363fb06cf">
                                    <label for="">Thời gian (âm lịch)</label>
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
                    <div id="add_section">
                        <a class="section_name" href="#" data-form='#parents_form'>
                            <i class="fa fa-users" aria-hidden="true"></i>
                            <span>Bố mẹ hai bên</span>
                        </a>
                        <a class="section_name" href="#" data-form='#groom_wedding_form'>
                            <i class="fa fa-building" aria-hidden="true"></i>
                            <span>Địa điểm tổ chức hôn lễ tại nhà trai</span>
                        </a>
                        <a class="section_name" href="#" data-form='#bride_wedding_form'>
                            <i class="fa fa-building" aria-hidden="true"></i>
                            <span>Địa điểm tổ chức lễ vu quy tại nhà gái</span>
                        </a>
                        <a class="section_name" href="#" data-form='#groom_party_form'>
                            <i class="fa fa-building" aria-hidden="true"></i>
                            <span>Địa điểm dự tiệc của nhà trai</span>
                        </a>
                        <a class="section_name" href="#" data-form='#bride_party_form'>
                            <i class="fa fa-building" aria-hidden="true"></i>
                            <span>Địa điểm dự tiệc của nhà gái</span>
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="mui-col-md-2"></div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        /* Bấm vào mỗi section thì sẽ hiện form tương ứng và ẩn các form khác đi */
        $('#add_section .section_name').click(function(){
            var form = $(this).data('form');
            $('.hide_form').hide(200);
            $('#add_section .section_name').show(200);
            $(this).hide();
            $(form).show(200);
            return false;
        });
        /* Bấm vào nút close thì sẽ ẩn form và hiện lại các section */
        $('.close_button').click(function(){
            var action_id = $(this).parent().attr('id');
            $(this).parent().hide(200);
            $('#add_section .section_name[data-form="#' + action_id + '"]').show(200);
        });

        /* Khi bấm submit một form thì gọi ajax để xử lý form đó, thêm dữ liệu vào  */
        $('.hide_form button[type="submit"]').click(function () {
            var $data = $(this).parents().eq(1).serialize();
            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "addWeddingInfo",
                    data: $data,
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    console.log(resp);
                    $('.hide_form').hide();
                    $('#add_more_form').prepend('<span class="success_notification">Đã update thành công.</span>');
                    location.reload();
                    setTimeout(function(){
                        if ($('.success_notification').length > 0) {
                            $('.success_notification').remove(200);
                        }
                    }, 4000)
                },
            });
            return false;
        });

        /* Xử lý ajax khi sửa trực tiếp nội dung trên div */
        $('.content_info span').blur(function(){
            var field = $(this).data('field');
            var content = $(this).text();
            var parent = $(this).parent();
            console.log(parent);

            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "updateWeddingInfo",
                    field: field,
                    content: content,
                },
                beforeSend: function() {
                    parent.find('i').hide();
                    parent.prepend('<b class="loading"><img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt=""></b>').show();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    parent.find('.loading').remove();
                    parent.find('i').show();
                },
            });
            return false;
        });

        /* Khi click vào sửa ngày tháng thì sẽ hiện form sửa ngày dương lịch, sau đó tự động update vào ngày âm lịch */
        $('.date_data').click(function() {
            $(this).hide();
            $(this).parent().find('.date_input').show();
        });

        /* Xử lý khi click vào nút đóng button close-btn-mini */
        $('.close-btn-mini').click(function() {
            var date_div = $(this).parent().parent();
            date_div.hide();
            date_div.prev().show();
            return false;
        });

        // xử lý ajax khi click vào nút sửa thời gian
        $('.date_input form').submit(function () {
            // lấy dữ liệu từ form và mã hoá thành chuỗi
            var $data = $(this).serialize();
            var $divUpdate = $(this).parents(2);
            var $solarUpdate = $divUpdate.children('.date_data').children('.diveditable');
            var $lunarUpdate = $divUpdate.next().children('.diveditable');
            // console.log($data);
            $.ajax({
                type: "POST",
                url: AJAX.ajax_url,
                data: {
                    action: "weddingDateInput",
                    data: $data,
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function (resp) {
                    var obj = JSON.parse(resp);
                    console.log(obj);
                    /* Hiện lại div ngày tháng, ẩn div nhập dữ liệu ngày tháng */
                    $divUpdate.find('.date_input').hide();
                    $divUpdate.find('.date_data').show(200);
                    /* Hiển thị nội dung mới */
                    $solarUpdate.html(obj["solarUpdate"]);
                    $lunarUpdate.html(obj["lunarUpdate"]);
                },
            });
            return false;
        });
    });
</script>
<?php
get_footer();