<?php
$guest_list = get_field('guest_list');
$current_user = wp_get_current_user();
$used_cards = get_field('used_cards', 'user_' . $current_user->ID);
$package_id = get_field('package_id', 'user_' . $current_user->ID);

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

    if (!$update){
    
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
            update_field('field_63b853e50f9a8', $used_cards + 1, 'user_' . $current_user->ID);
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
                    
                    if ($row == $guestid) {
                        $row_update = array(
                            'name'          => $guest_name,
                            'guest_attach'  => $guest_attach,
                            'xung_ho'       => $vai_ve . "/" . $xung_ho,
                            'phone'         => $sdt,
                            'sent'          => $sent,
                            'joined'        => $joined,
                        );
                                                
                        update_row('field_61066efde7dbc', $row, $row_update);
                        break;
                    }
                }
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
        wp_redirect( get_bloginfo('url') );
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
            'userid'    => $current_user->ID,
        )), 'e');

        $link_view_demo = get_bloginfo('url') . '/view-demo/?group=' . $groupid_encode;
        $link_select_card = get_bloginfo('url') . '/danh-sach-mau/?g=' . $data_token;
        $link_upload = get_bloginfo('url') . '/tai-khach-hang-qua-file-excel/?g=' . $data_token;
        $link_edit_content = get_bloginfo('url') . '/edit-content/?g=' . $data_token;
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
                        <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                        <i class="fa fa-chevron-right"></i>
                        <span contenteditable="true" oncut="return false" onpaste="return false" class="title" data-guestid="<?php echo get_the_ID(); ?>"> <?php the_title(); ?></span>
                        <span class="loader"><img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt=""></span>
                    </div>
                    <div class="mui-panel">
                        <h3 class="title_general">Mẫu thiệp</h3>
                        <div class="mui-row mb20">
                            <div class="mui-col-md-3">
                                <div class="heracard">
                                    <div class="images">
                                        <img src="<?php echo $card_thumbnail; ?>" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="mui-col-md-9">
                                <a href="<?php echo $link_select_card; ?>" class="mui-btn hera-btn">Chọn thiệp</a><br>
                                <a href="<?php echo $link_view_demo; ?>" target="_blank" class="mui-btn hera-btn">Xem mẫu</a><br>
                                <a href="<?php echo $link_edit_content; ?>" class="mui-btn hera-btn">Sửa nội dung thiệp</a>
                            </div>
                        </div>

                        <div class="mui-divider"></div>

                        <!-- <h3 class="mb0">Lời mời</h3>
                        <p>Lời mời riêng dành cho nhóm (nếu có). Thêm {1} và {2} vào vị trí bạn muốn thay đổi. <a href="#" class="mui--text-danger">Xem hướng dẫn chi tiết.</a></p>
                        <form id="loi_moi" class="mui-form" method="POST">
                            <div class="mui-textfield">
                                <textarea placeholder="Ghi lời mời tại đây ... " name="loi_moi"></textarea>
                            </div>

                            <button type="submit" class="mui-btn hera-btn">Cập nhật</button>
                        </form>

                        <div class="mui-divider"></div> -->

                        <h3 class="mb10">Danh sách khách</h3>
                        <div class="mui-row">
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
                                                    'userid'    => $current_user->ID,
                                                    'groupid'   => get_the_ID(),
                                                    'row_index' => $row_index,
                                                    'nonce'     => wp_create_nonce('delcustomer_' . $row_index),
                                                )), 'e');

                                                if ($guest_attach) {
                                                    $guests = $name . ' và ' . $guest_attach;
                                                } else $guests = $name;

                                                $viewlink = get_bloginfo('url') . '/myacc/' . $current_user->user_login . '/' . $groupid_encode . '/' . inova_encrypt($row_index, 'e');
                                        ?>
                                                <tr>
                                                    <td><?php echo $row_index; ?></td>
                                                    <td><?php echo $guests; ?></td>
                                                    <td><?php echo get_sub_field('xung_ho'); ?></td>
                                                    <td><?php echo get_sub_field('phone'); ?></td>
                                                    <?php
                                                        # Nếu chưa đăng ký gói thì chưa hiện tính năng chia sẻ
                                                        if ($package_id && $card_id) {
                                                            echo '<td>';
                                                            echo '    <a href="' . $viewlink . '" class="copy_link">Copy link</a>';
                                                            echo '</td>';
                                                    ?>
                                                    <td><input type="checkbox" class="sent_friend"
                                                        <?php if ($sent) {
                                                            echo "checked";
                                                        } ?>
                                                        data-field="field_61066fd1e7dc1" data-index="<?php echo $row_index; ?>">
                                                    </td>
                                                    <td><?php 
                                                        if ($joined=="Y") {
                                                            echo "Có";
                                                        } else if ($joined == "N") echo "Không"; 
                                                        else echo "Chưa trả lời";
                                                        ?>
                                                    </td>
                                                    <?php
                                                        }
                                                    ?>
                                                    <td>
                                                        <?php
                                                            # Nếu chưa đăng ký gói thì chưa hiện tính năng chia sẻ
                                                            if ($package_id && $card_id) {
                                                                echo '<a href="' . $viewlink . '" target="_blank"><i class="fa fa-eye"></i></a>';
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
                            </div>
                        </div>
                    </div>
                    <div class="footer_section mui--text-center">
                        <?php 
                            $dnonce = wp_create_nonce('delete');
                            $del_token = inova_encrypt(json_encode(array(
                                'groupid'   => $groupid,
                                'userid'    => $current_user->ID,
                                'nonce'     => $dnonce,
                            )), 'e');
                        ?>
                        <a href="?d=<?php echo $del_token ?>" class="mui--text-danger" onclick="return confirm('Bạn chắc chắn muốn xoá nhóm này?')">Xoá nhóm này?</a>
                    </div>
                </div>
                <div class="mui-col-md-2"></div>
            </div>
        </div>

        <div class="mui-col-md-4  mui-col-sm-12" id="create_card_form">
            <form class="mui-form" method="POST" name="guest_form">
                <legend>Thêm khách mời</legend>
                <div class="mui-textfield">
                    <input type="text" placeholder="VD: Anh Nam" name="guest_name">
                    <label for="">Tên khách mời</label>
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
        <script src="<?php echo get_template_directory_uri(); ?>/js/single-thiep_moi.js"></script>
<?php
    }
}
get_footer();
