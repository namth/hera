<?php
$guest_list = get_field('guest_list');
$guest_id = get_field('guest_id');

# Thêm mới khách mời
if (
    isset($_POST['post_nonce_field']) &&
    wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
) {
    $guest_name = $_POST['guest_name'];
    $guest_attach = $_POST['guest_attach'];
    $vai_ve = $_POST['vai_ve'];
    $xung_ho = $_POST['xung_ho'];
    $sdt = $_POST['sdt'];

    # nếu guest_id chưa có thì set = 1
    if (!$guest_id) {
        $guest_id = 1;
    } else $guest_id++;

    if (!$vai_ve) {
        $vai_ve = "Bạn";
    }

    if (!$xung_ho) {
        $xung_ho = "Tôi";
    }

    if ($guest_name) {
        $row_update = array(
            'stt'           => $guest_id,
            'name'          => $guest_name,
            'guest_attach'  => $guest_attach,
            'xung_ho'       => $vai_ve . "/" . $xung_ho,
            'phone'         => $sdt,
            'sent'          => false,
            'joined'        => false,
            'thanh_toan'    => false,
        );

        add_row('field_61066efde7dbc', $row_update);
        update_field('field_610ffbbe6d701', $guest_id);
    } else {
    }
}
get_header();
get_template_part('header', 'topbar');
if (have_posts()) {
    while (have_posts()) {
        the_post();
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
                        <span> <?php the_title(); ?></span>
                    </div>
                    <div class="mui-panel">
                        <h3 class="title_general">Mẫu thiệp</h3>
                        <div class="mui-row mb20">
                            <div class="mui-col-md-3">
                                <div class="heracard">
                                    <div class="images">
                                        <img src="<?php echo get_template_directory_uri(); ?>/img/no-img.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="mui-col-md-9">
                                <a href="#" class="mui-btn hera-btn">Chọn thiệp</a><br>
                                <a href="#" class="mui-btn hera-btn">Xem mẫu</a>
                            </div>
                        </div>

                        <div class="mui-divider"></div>

                        <h3 class="mb0">Lời mời</h3>
                        <p>Lời mời riêng dành cho nhóm (nếu có). Thêm {vai_ve} và {xung_ho} vào vị trí bạn muốn thay đổi.</p>
                        <form id="loi_moi" class="mui-form" method="POST">
                            <div class="mui-textfield">
                                <textarea placeholder="Ghi lời mời tại đây ... " name="loi_moi"></textarea>
                            </div>

                            <button type="submit" class="mui-btn hera-btn">Cập nhật</button>
                        </form>

                        <div class="mui-divider"></div>

                        <h3 class="mb10">Danh sách khách</h3>
                        <div class="mui-row">
                            <div class="mui-col-md-12 mb10">
                                <input type="hidden" name="groupid" value="<?php echo get_the_ID(); ?>">
                                <button class="mui-btn hera-btn" onclick="activateModal()">
                                    <i class="fa fa-user-plus"></i> Thêm mới
                                </button>
                                <a href="#" class="mui-btn hera-btn"><i class="fa fa-cloud-download"></i> Tải file mẫu</a>
                                <a href="#" class="mui-btn hera-btn"><i class="fa fa-cloud-upload"></i> Upload danh sách</a>
                            </div>
                            <div class="mui-col-md-12">
                                <table class="mui-table">
                                    <thead>
                                        <tr>
                                            <th>Tên</th>
                                            <th>Cách xưng hô</th>
                                            <th>Số điện thoại</th>
                                            <th>Link thiệp mời</th>
                                            <th>Đã mời</th>
                                            <th>Tham dự</th>
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

                                                # Xoá khách mời
                                                if (isset($_GET['delete']) && ($_GET['delete'] != "")) {
                                                    $delete_number = $_GET['delete'];

                                                    $stt = get_sub_field('stt');
                                                    if ($stt == $delete_number) {
                                                        $row = get_row_index();
                                                        delete_row('field_61066efde7dbc', $row);
                                                        continue;
                                                    }
                                                }

                                        ?>
                                                <tr>
                                                    <td><?php echo get_sub_field('name'); ?></td>
                                                    <td><?php echo get_sub_field('xung_ho'); ?></td>
                                                    <td><?php echo get_sub_field('phone'); ?></td>
                                                    <td><a href="#">Copy link</a></td>
                                                    <td><input type="checkbox" <?php if ($sent) {
                                                                                    echo "checked";
                                                                                } ?>></td>
                                                    <td><input type="checkbox" <?php if ($joined) {
                                                                                    echo "checked";
                                                                                } ?>></td>
                                                    <td>
                                                        <!-- <button onclick="editguest(<?php echo get_sub_field('stt'); ?>)"><i class="fa fa-pencil"></i></button> -->
                                                        <a href="#" class="edit_guest" data-guest="<?php echo get_sub_field('stt'); ?>"><i class="fa fa-pencil"></i></a>
                                                        <a href="?delete=<?php echo get_sub_field('stt'); ?>"><i class="fa fa-trash"></i></a>
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
                <?php
                wp_nonce_field('post_nonce', 'post_nonce_field');
                ?>
                <button type="submit" class="mui-btn mui-btn--danger">Cập nhật</button>
            </form>
        </div>


<?php
    }
}
get_footer();
