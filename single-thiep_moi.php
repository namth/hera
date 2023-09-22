<?php
function show_notification($icon, $notification, $icon2, $link, $button_label)
{
    if ($notification && $button_label) {
        echo '<div class="notification">
                <span><i class="fa ' . $icon . '" aria-hidden="true"></i> ' . $notification . '</span>
                <a class="card_link" href="' . $link . '"><i class="fa ' . $icon2 . '" aria-hidden="true"></i> ' . $button_label . '</a>
            </div>';
    }
}

# Kiểm tra xem user hiện tại có đủ thẩm quyền để xem trang này hay không
$current_userID = get_current_user_id();
$author_id      = get_post_field('post_author');
if (($current_userID != $author_id) && !current_user_can('manage_options')) {
    # Nếu user hiện tại không phải tác giả và cũng không phải là admin thì sẽ chuyển về trang chủ 
    wp_redirect(get_bloginfo('url'));
    exit;
}
$guest_list     = get_field('guest_list');
$category       = get_the_terms(get_the_ID(), 'category');
$category_name  = $category[0]->name;
$used_cards     = get_field('used_cards', 'user_' . $author_id);
$package_id     = get_field('package_id', 'user_' . $author_id);

/* 
Kiểm tra xem có sử dụng package nào không
Nếu không thì sẽ hạn chế các trường "copy link", check đã mời, nút xem thiệp
*/

# Thêm mới và update khách mời
if (
    isset($_POST['post_nonce_field']) &&
    wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
) {
    $guest_name = $_POST['guest_name'];
    $guest_attach = $_POST['guest_attach'];
    $vai_ve = $_POST['vai_ve'];
    $xung_ho = $_POST['xung_ho'];
    $sdt = substr(preg_replace("/[^0-9]/", "", $_POST['sdt']), 0, 10);
    $update = $_POST['update'];

    if (!$update) {

        if (!$vai_ve) {
            $vai_ve = "Bạn";
        }

        if (!$xung_ho) {
            $xung_ho = "Tôi";
        }

        if ($guest_name) {
            $row_update = array(
                'name'          => $guest_name,
                'guest_attach'  => $guest_attach,
                'xung_ho'       => $vai_ve . "/" . $xung_ho,
                'phone'         => $sdt,
                'sent'          => false,
                'joined'        => false,
            );

            add_row('field_61066efde7dbc', $row_update);
            # Cập nhật số lượng thiệp đã sử dụng
            update_field('field_63b853e50f9a8', $used_cards + 1, 'user_' . $author_id);
        }
    } else {
        # if you are in update mode
        $guestid = $_POST["guestid"];

        # check position of guest where want to update
        if ($guest_name) {
            if (have_rows('guest_list') && $guestid) {
                while (have_rows('guest_list')) {
                    the_row();

                    $sent       = get_sub_field('sent');
                    $joined     = get_sub_field('joined');
                    $row        = get_row_index();
                    if ($vai_ve && $xung_ho) {
                        $xungho = $vai_ve . "/" . $xung_ho;
                    } else {
                        $xungho = $xung_ho ? $xung_ho : $vai_ve;
                    }

                    if ($row == $guestid) {
                        $row_update = array(
                            'name'          => $guest_name,
                            'guest_attach'  => $guest_attach,
                            'xung_ho'       => $xungho,
                            'phone'         => validate_phonenumber($sdt),
                            'sent'          => $sent,
                            'joined'        => $joined,
                        );

                        update_row('field_61066efde7dbc', $row, $row_update);
                        break;
                    }
                }
                reset_rows();
            }
        }
    }
}

# xử lý xoá nhóm và chuyển về trang chủ
if (isset($_GET['d']) && ($_GET['d'] != '')) {
    $delete = json_decode(inova_encrypt($_GET['d'], 'd'));

    if (wp_verify_nonce($delete->nonce, 'delete') && ($current_user->ID == $delete->userid)) {
        # cập nhật lại số thiệp đã sử dụng

        # soft delete nhóm bằng cách chuyển nhóm sang trạng thái đã xoá và private.
        update_field('field_62a34ca619e78', 'Deleted', $delete->groupid);
        $update = wp_update_post(array(
            'ID'            => $delete->groupid,
            'post_status'   => 'private',
        ));
        wp_redirect(get_bloginfo('url'));
    }
}

get_header();
get_template_part('header', 'topbar');
if (have_posts()) {
    while (have_posts()) {
        the_post();

        $groupid        = get_the_ID();
        $image          = get_field('thumbnail');
        $status         = get_field('status');
        $card_id        = get_field('card_id');
        $groupid_encode = inova_encrypt(get_the_ID(), 'e');

        if ($image) {
            $card_thumbnail = $image;
        } else {
            $card_thumbnail = get_template_directory_uri() . '/img/no-img.png';
        }

        $data_token = inova_encrypt(json_encode(array(
            'groupid'   => $groupid,
            'userid'    => $author_id,
        )), 'e');

        $link_view_demo = get_bloginfo('url') . '/view-demo/?group=' . $groupid_encode;
        $link_select_card = get_bloginfo('url') . '/danh-sach-mau/?g=' . $data_token;
        $link_upload = get_bloginfo('url') . '/tai-khach-hang-qua-file-excel/?g=' . $data_token;
        $link_edit_content = get_bloginfo('url') . '/edit-content/?g=' . $data_token;

        $guest_data = [
            'total' => 0,
            'sent'  => 0,
            'joined'    => 0,
            'decline'   => 0,
            'notanswer' => 0
        ];
        if (have_rows('guest_list')) {
            while (have_rows('guest_list')) {
                the_row();

                $guest_data['total']++;
                $guest_data['sent'] += get_sub_field('sent') ? 1 : 0;
                $joined = get_sub_field('joined');
                $name = get_sub_field('name');
                $guest_attach = get_sub_field('guest_attach');
                $row_index = get_row_index();

                if ($joined == "Y") {
                    $guest_data['joined']++;
                } else if ($joined == "N") $guest_data['decline']++;
                else $guest_data['notanswer']++;
            }
            reset_rows();
        }
?>
        <div class="mui-container-fluid">
            <div class="mui-row">
                <div class="mui-col-md-2 npl">
                    <?php
                    get_sidebar();
                    ?>
                </div>
                <div class="mui-col-lg-8 mui-col-md-12">
                    <div class="breadcrumb">
                        <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                        <i class="fa fa-chevron-right"></i>
                        <span contenteditable="true" oncut="return false" onpaste="return false" class="title" data-guestid="<?php echo get_the_ID(); ?>"> <?php the_title(); ?></span>
                        <span class="loader"><img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt=""></span>
                    </div>
                    <div class="mui-panel">
                        <h3 class="title_general">Mẫu thiệp</h3>
                        <div class="mui-row mb20">
                            <div class="mui-col-lg-3 mui-col-md-6">
                                <div class="heracard">
                                    <div class="images">
                                        <img src="<?php echo $card_thumbnail; ?>" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="mui-col-lg-4 mui-col-md-3 mui-col-sm-6">
                                <a href="<?php echo $link_select_card; ?>" class="mui-btn hera-btn"><i class="fa fa-pagelines" aria-hidden="true"></i> Chọn thiệp</a>
                                <?php
                                # Nếu chưa chọn thiệp thì không hiện 2 nút dưới
                                if ($card_id) {
                                    # Lấy thông tin đám cưới: 
                                    $groom = get_field('groom', 'user_' . $author_id);
                                    $bride = get_field('bride', 'user_' . $author_id);

                                    if ($category_name == "Nhà gái") {
                                        $party_adress       = get_field('bride_party_address', 'user_' . $author_id);
                                        $party_time         = explode(' ', get_field('bride_party_time', 'user_' . $author_id));
                                    } else {
                                        $party_adress       = get_field('groom_party_address', 'user_' . $author_id);
                                        $party_time         = explode(' ', get_field('groom_party_time', 'user_' . $author_id));
                                    }
                                    # Khi điền đầy đủ thông tin, thì mới hiện nút "xem thiệp" và "sửa nội dung thiệp"
                                    $check_groombride = $groom && $bride;
                                    $check_party = $party_adress && $party_time;
                                    $link_wedding_infomation = get_bloginfo('url') . "/wedding-infomation";
                                    if ($check_groombride && $check_party) {
                                        echo '<a href="' . $link_view_demo . '" target="_blank" class="hera-link"><i class="fa fa-weibo" aria-hidden="true"></i> Xem mẫu</a>';
                                        echo '<a href="' . $link_edit_content . '" class="hera-link"><i class="fa fa-foursquare" aria-hidden="true"></i> Sửa nội dung thiệp</a>';
                                    }
                                }
                                ?>
                            </div>
                            <?php
                            if ($guest_data['total']) {
                            ?>
                                <div class="mui-col-lg-5 mui-col-md-3 mui-col-sm-6 statistic">
                                    <h4>Thống kê</h4>
                                    <p>Tổng số người trong nhóm này: <b><?php echo $guest_data['total']; ?></b></p>
                                    <?php
                                    if ($guest_data['joined']) {
                                    ?>
                                        <span><b><?php echo $guest_data['joined']; ?></b> người tham dự được</span>
                                    <?php
                                    }
                                    if ($guest_data['decline']) {
                                    ?>
                                        <span><b><?php echo $guest_data['decline']; ?></b> người không tham dự được</span>
                                    <?php
                                    }
                                    if ($guest_data['notanswer']) {
                                    ?>
                                        <span><b><?php echo $guest_data['notanswer']; ?></b> người chưa trả lời</span>
                                    <?php
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>
                        </div>

                        <div class="mui-divider"></div>

                        <h3 class="mb10">Danh sách khách</h3>
                        <div class="mui-row">
                            <div class="mui-col-md-12 mb10">
                                <?php
                                if ($card_id) {
                                    if (!$check_groombride) {
                                        $icon = 'fa-exclamation-circle';
                                        $thongbao = 'Bạn chưa nhập thông tin cô dâu chú rể';
                                        $link = $link_wedding_infomation;
                                        $icon2 = 'fa-venus-mars';
                                        $button = 'Nhập tên cô dâu chú rể';
                                    } else if (!$check_party) {
                                        $icon = 'fa-globe';
                                        $thongbao = 'Bạn chưa nhập thời gian và địa điểm tổ chức';
                                        $link = $link_wedding_infomation;
                                        $icon2 = 'fa-exclamation-circle';
                                        $button = 'Nhập thông tin';
                                    } else if (!$package_id && $guest_data['total']) {
                                        $icon = 'fa-eye-slash';
                                        $thongbao = 'Bạn chưa kích hoạt gói thiệp. Khách mời sẽ không thể xem link bạn gửi!';
                                        $link = get_bloginfo('url') . "/danh-sach-goi-san-pham/";
                                        $icon2 = 'fa-gift';
                                        $button = 'Xem gói thiệp';
                                    }
                                    show_notification($icon, $thongbao, $icon2, $link, $button);
                                }
                                ?>
                            </div>
                            <div class="mui-col-md-12 mb10">
                                <input type="hidden" name="groupid" value="<?php echo get_the_ID(); ?>">
                                <button class="mui-btn hera-btn" onclick="activateModal()">
                                    <i class="fa fa-user-plus"></i> Thêm mới
                                </button>
                                <a id="upload_data" href="<?php echo $link_upload; ?>" class="mui-btn"><i class="fa fa-cloud-upload"></i> Upload danh sách</a>
                            </div>
                            <div class="mui-col-md-12">
                                <table class="mui-table" id="list_customer">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Khách mời</th>
                                            <th>Cách xưng hô</th>
                                            <th>Số điện thoại</th>
                                            <?php
                                            # Nếu chưa đăng ký gói thì chưa hiện tính năng chia sẻ
                                            if ($package_id && $card_id) {
                                            ?>
                                                <th>Link thiệp mời</th>
                                                <th>Đã mời</th>
                                                <th>Tham dự</th>
                                            <?php
                                            }
                                            ?>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        if (have_rows('guest_list')) {
                                            while (have_rows('guest_list')) {
                                                the_row();

                                                $sent = get_sub_field('sent');
                                                $joined = get_sub_field('joined');
                                                $name = get_sub_field('name');
                                                $guest_attach = get_sub_field('guest_attach');
                                                $row_index = get_row_index();

                                                # Xoá khách mời
                                                $del_data = inova_encrypt(json_encode(array(
                                                    'userid'    => $author_id,
                                                    'groupid'   => get_the_ID(),
                                                    'row_index' => $row_index,
                                                    'nonce'     => wp_create_nonce('delcustomer_' . $row_index),
                                                )), 'e');

                                                if ($guest_attach) {
                                                    $guests = $name . ' và ' . $guest_attach;
                                                } else $guests = $name;

                                                $viewlink = get_bloginfo('url') . '/myacc/' . $current_user->user_login . '-' . $groupid_encode . '-' . $row_index;
                                        ?>
                                                <tr>
                                                    <td data-label="Số thứ tự" data-encode="<?php echo $data_card_encode; ?>"><?php echo $row_index; ?></td>
                                                    <td data-label="Khách mời"><?php echo $guests; ?></td>
                                                    <td data-label="Cách xưng hô"><?php echo get_sub_field('xung_ho'); ?></td>
                                                    <td data-label="Số điện thoại"><?php echo get_sub_field('phone'); ?></td>
                                                    <?php
                                                    # Nếu chưa đăng ký gói thì chưa hiện tính năng chia sẻ
                                                    if ($package_id && $card_id) {
                                                        echo '<td data-label="Link thiệp mời">';
                                                        echo '    <a href="' . $viewlink . '" class="copy_link">Copy link</a>';
                                                        echo '</td>';
                                                    ?>
                                                        <td data-label="Đã mời"><input type="checkbox" class="sent_friend" <?php if ($sent) {
                                                                                                                                echo "checked";
                                                                                                                            } ?> data-field="field_61066fd1e7dc1" data-index="<?php echo $row_index; ?>">
                                                        </td>
                                                        <td data-label="Tham dự"><?php
                                                                                    if ($joined == "Y") {
                                                                                        echo "Có";
                                                                                    } else if ($joined == "N") echo "Không";
                                                                                    else echo "Chưa trả lời";
                                                                                    ?>
                                                        </td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <td data-label="Thao tác">
                                                        <?php
                                                        # Nếu chưa chọn mẫu thiệp và chưa nhập đủ thông tin thì chưa hiện tính năng chia sẻ
                                                        if ($card_id && $check_party) {
                                                            $_icon = $package_id ? '<i class="fa fa-eye"></i>' : '<i class="fa fa-eye-slash"></i>';
                                                            echo '<a href="' . $viewlink . '" target="_blank">' . $_icon . '</a>';
                                                        }
                                                        ?>

                                                        <a href="#" class="edit_guest" data-guest="<?php echo $row_index; ?>"><i class="fa fa-pencil"></i></a>
                                                        <a href="#" data-del="<?php echo $del_data; ?>" class="del_customer"><i class="fa fa-trash"></i></a>
                                                        <span class="loader"><img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt=""></span>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="new_guest" onclick="activateModal()">
                                    <i class="fa fa-user-plus"></i> Thêm khách mời
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="footer_section mui--text-center">
                        <?php
                        $dnonce = wp_create_nonce('delete');
                        $del_token = inova_encrypt(json_encode(array(
                            'groupid'   => $groupid,
                            'userid'    => $author_id,
                            'nonce'     => $dnonce,
                        )), 'e');
                        ?>
                        <a href="?d=<?php echo $del_token ?>" class="mui--text-danger" onclick="return confirm('Bạn chắc chắn muốn xoá nhóm này?')">Xoá nhóm này?</a>
                    </div>
                </div>
                <div class="mui-col-md-2 left_sidebar">
                    <?php
                    $menu = 'Hướng dẫn trang chi tiết thiệp';
                    $guideline = wp_nav_menu(array(
                        'menu'          => $menu,
                        'container_id'  => 'guide_section',
                        'container_class'   => '',
                        'items_wrap'    => '<a href="#" class="maximize"><i class="fa fa-external-link" aria-hidden="true"></i> Khôi phục</a><img src="' . get_template_directory_uri() . '/img/thaochi.jpg"><ul class="playlist"><h4>Hướng dẫn nhanh</h4>%3$s</ul><a href="#" class="minimize"><i class="fa fa-level-down" aria-hidden="true"></i> Thu nhỏ</a>',
                        'menu_class'    => 'main_menu mb20',
                        'echo' => FALSE,
                        'fallback_cb' => '__return_false'
                    ));

                    if (!empty($guideline)) {
                        echo $guideline;
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="mui-col-md-4  mui-col-sm-12" id="create_card_form">
            <form class="mui-form" method="POST" name="guest_form">
                <legend>Thêm khách mời</legend>
                <div class="mui-textfield">
                    <input type="text" placeholder="VD: Anh Nam" name="guest_name" required>
                    <label for="">Tên khách mời *</label>
                </div>
                <div class="mui-textfield">
                    <input type="text" placeholder="VD: người thương" name="guest_attach">
                    <label for="">Và</label>
                </div>
                <div class="mui-textfield">
                    <input type="text" name="vai_ve" placeholder="Cô, Chú, Bạn, Cậu, Em, Anh ...">
                    <label>Mình gọi họ là gì?</label>
                </div>
                <div class="mui-textfield">
                    <input type="text" name="xung_ho" placeholder="Tôi, Tớ, Mình, Cháu ...">
                    <label>Cách mình xưng hô với họ?</label>
                </div>
                <div class="mui-textfield">
                    <input type="text" name="sdt" placeholder="Số điện thoại">
                    <label>Số điện thoại</label>
                </div>
                <input type="hidden" name="update" value="0">
                <input type="hidden" name="guestid" value="0">
                <?php
                wp_nonce_field('post_nonce', 'post_nonce_field');
                ?>
                <button type="submit" class="mui-btn mui-btn--danger">Cập nhật</button>
            </form>
        </div>
        <script src="<?php echo get_template_directory_uri(); ?>/js/cookie.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/guideline.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/soundmanager2-jsmin.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/soundmanager2-player.js"></script>
        <link href="<?php echo get_template_directory_uri(); ?>/css/soundmanager2-player.css" rel="stylesheet" type="text/css">

        <script src="<?php echo get_template_directory_uri(); ?>/js/single-thiep_moi.js"></script>
        <script>
            /* Prevent resubmit form when page is reloaded */
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
<?php
    }
}
get_footer();
