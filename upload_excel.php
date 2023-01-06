<?php
/* 
* Template Name: Upload khách hàng qua file excel
*/
require_once get_template_directory() . '/vendor/autoload.php';

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

$thongbao = '';
if (isset($_GET['g']) && ($_GET['g'] != "" )) {
    $group_data = json_decode(inova_encrypt($_GET['g'], 'd'));
    $group_check = true;

    /* Đọc dữ liệu groupID và userID */
    $groupID = $group_data->groupid;
    $userID = $group_data->userid;

    /* Xác định groupID thuộc nhà trai hay nhà gái */
    $terms = get_the_terms($groupID, 'category');

    $ownID = $terms[0]->term_id;
    $ownName = $terms[0]->name;

    $fromCard = true;
}
if (isset($_POST['post_upload_field']) && wp_verify_nonce($_POST['post_upload_field'], 'post_upload')) {

    $current_number = get_field('register_number', 'option');
    $file_excel = $_FILES['fileupload'];

    if (!$userID) {
        $userID = get_current_user_id();
    }

    if ($file_excel) {
        # Reading Excel
        $rowData = wp_reading_excel($file_excel['tmp_name']);
    }

    // In dữ liệu ra file excel
    if ($rowData) {
        # remove the first item, that's the header.
        $excel_header = array_shift($rowData);

        foreach ($rowData as $new_customer) {
            if ($new_customer['A']) {
                # 
                if (!$fromCard) {
                    $groupID = 0;
                    $ownName = "";
                }

                $name       = trim($new_customer['A']);
                $attach     = trim($new_customer['B']);
                $vocative1  = trim($new_customer['C']);
                $vocative2  = trim($new_customer['D']);
                $xung_ho    = ($vocative1 && $vocative2)?implode('/', array($vocative1, $vocative2)):"";
                $phone      = substr(preg_replace("/[^0-9]/", "", $new_customer['E']), 0, 10);

                if (!$ownName) {
                    if (in_array(strtoupper($new_customer[5]),["NHÀ TRAI", "NHÀ GÁI"])) {
                        $ownName    = ucfirst($new_customer[5]);
                    }
                    $groupName  = $new_customer[6];
                }

                if ($ownName) {
                    /* Nếu chưa có groupID thì tìm groupID theo groupName */
                    if (!$groupID && ($groupName != "")) {
                        $groupID = search_group($groupName, $userID);
                    }
                    /* Nếu tìm được groupID thì lấy dữ liệu khách mời trong nhóm đó vào mảng để so sánh phía dưới */
                    if ($groupID) {
                        if ($groupName != $cache) {
                            $guest_array = [];
                            if (have_rows('guest_list', $groupID)) {
                                while (have_rows('guest_list', $groupID)) {
                                    the_row();
                                    $guest_array[get_row_index()] = get_sub_field('phone');
                                    $cache = $groupName;
                                }
                            }    
                        }
                        
                        # Tìm kiếm phone của khách xem đã có hay chưa
                        $row = array_search($phone, $guest_array);
        
                        # Nếu có thì update, không có thì thêm row mới
                        if ($row) {
                            update_sub_field(array('field_61066efde7dbc', $row, 'name'), $name, $groupID);
                            update_sub_field(array('field_61066efde7dbc', $row, 'guest_attach'), $attach, $groupID);
                            update_sub_field(array('field_61066efde7dbc', $row, 'xung_ho'), $xung_ho, $groupID);
                            update_sub_field(array('field_61066efde7dbc', $row, 'phone'), $phone, $groupID);
                        } else {
                            # Nếu có trường $name thì mới thêm mới
                            if ($name) {
                                $row_update = array(
                                    'name'          => $name,
                                    'guest_attach'  => $attach,
                                    'xung_ho'       => $xung_ho,
                                    'phone'         => $phone,
                                    'sent'          => false,
                                    'joined'        => false,
                                );
                                add_row('field_61066efde7dbc', $row_update, $groupID);
                            }
                        }
                    } else {
                        # Nếu search không thấy và thì tạo group mới 
                        $args = array(
                            'post_title'    => $groupName,
                            'post_status'   => 'publish',
                            'post_type'     => 'thiep_moi',
                        );
                        $groupID = wp_insert_post($args);

                        wp_set_object_terms($groupID, $ownName, 'category');
                        
                        # Thêm mới dữ liệu
                        # Nếu có trường $name thì mới thêm mới
                        if ($name) {
                            $row_update = array(
                                'name'          => $name,
                                'guest_attach'  => $attach,
                                'xung_ho'       => $xung_ho,
                                'phone'         => $phone,
                                'sent'          => false,
                                'joined'        => false,
                            );
                            add_row('field_61066efde7dbc', $row_update, $groupID);
                        }
                    }
                }
            }
        }

        wp_redirect(get_permalink($groupID));
    } else {
        $thongbao = "File không đúng định dạng hoặc không có dữ liệu hợp lệ. Hãy chỉnh sửa file hoặc download mẫu bên dưới.";
    }

}
get_header();
get_template_part('header', 'topbar');
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-10">
            <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url'); ?>">Trang chủ</a>
                <i class="fa fa-chevron-right"></i>
                <span><?php echo get_the_title(); ?></span>
            </div>
            <div class="mui-panel" id="upload">
                <form action="" method="post" enctype="multipart/form-data" class="mui-form mui-row">
                    
                    <div class="mui-col-md-4"></div>
                    <div class="mui-col-md-4 mui-col-sm-12">
                        <p><?php if ($thongbao){ echo $thongbao; } ?></p>
                        <div class="mui-textfield">
                            <input name="fileupload" type="file">
                        </div>
                    </div>
                    <div class="mui-col-md-4 mui-col-sm-12"></div>

                    <div class="mui-col-md-5 mui-col-sm-12">
                        <?php
                        wp_nonce_field('post_upload', 'post_upload_field');
                        ?>
                    </div>
                    <div class="mui-col-md-2 mui-col-sm-12">
                        <input type="submit" value="Import" class="mui-btn mui-btn--danger">
                    </div>
                    <div class="mui-col-md-5 mui-col-sm-12"></div>
                </form>
                <?php the_content(); ?>
                <style>
                    .excel_form {
                        display: inline-block;
                        text-align: center;
                        border-spacing: 0px;
                    }

                    .excel_form th,
                    .excel_form td {
                        text-align: center;
                        padding: 5px 15px;
                    }
                    ol li code{
                        font-size: .8em;
                        background: mediumvioletred;
                        color: white;
                        padding: 3px 8px;
                        border-radius: 10px;
                    }
                </style>
                <table border="1" class="excel_form">
                    <tr>
                        <th> </th>
                        <th>A</th>
                        <th>B</th>
                        <th>C</th>
                        <th>D</th>
                        <th>E</th>
                        <?php 
                            if (!$group_check) {
                        ?>
                        <th>F</th>
                        <th>G</th>
                        <?php 
                            }
                        ?>
                    </tr>
                    <tr>
                        <th>1</th>
                        <td>Khách mời</td>
                        <td>Mời cùng</td>
                        <td>Cách mình gọi họ</td>
                        <td>Cách họ gọi mình</td>
                        <td>Số điện thoại</td>
                        <?php 
                            if (!$group_check) {
                        ?>
                        <td>Khách của</td>
                        <td>Nhóm</td>
                        <?php 
                            }
                        ?>
                    </tr>
                    <tr>
                        <th>2</th>
                        <td>Anh Duy</td>
                        <td>gia đình</td>
                        <td>Anh</td>
                        <td>Em</td>
                        <td>0987654321</td>
                        <?php 
                            if (!$group_check) {
                        ?>
                        <td>Nhà trai</td>
                        <td>Công ty cũ</td>
                        <?php 
                            }
                        ?>
                    </tr>
                    <tr>
                        <th>3</th>
                        <td>Đức</td>
                        <td>người thương</td>
                        <td>...</td>
                        <td>...</td>
                        <td>...</td>
                        <?php 
                            if (!$group_check) {
                        ?>
                        <td>...</td>
                        <td>...</td>
                        <?php 
                            }
                        ?>
                    </tr>
                </table>

                <br>
                <h3>Hướng dẫn cập nhật qua file excel</h3>
                <ol>
                    <li>Tạo file excel theo mẫu trên</li>
                    <li>Dữ liệu nào chưa có sẽ được tạo mới, dữ liệu đã có sẽ được cập nhật.</li>
                </ol>
                <a id="download_sample" href="<?php echo get_bloginfo('url'); ?>/download-sample/?type=short" class="mui-btn hera-btn"><i class="fa fa-cloud-download"></i> Tải file mẫu</a>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();