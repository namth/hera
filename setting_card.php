<?php
/* 
    Template Name: Setting Card 
*/

get_header();
get_template_part('header', 'topbar');
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
                <span> Ban cap 3 </span>
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
                        <a href="#" class="mui-btn hera-btn"><i class="fa fa-user-plus"></i> Thêm mới</a>
                        <a href="#" class="mui-btn hera-btn"><i class="fa fa-cloud-download"></i> Tải file mẫu</a>
                        <a href="#" class="mui-btn hera-btn"><i class="fa fa-cloud-upload"></i> Upload danh sách</a>
                    </div>
                    <div class="mui-col-md-12">
                        <table class="mui-table">
                            <thead>
                                <tr>
                                    <th>Khách mời</th>
                                    <th>Cách xưng hô</th>
                                    <th>Số điện thoại</th>
                                    <th>Link thiệp mời</th>
                                    <th>Đã mời</th>
                                    <th>Tham dự</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Trường</td>
                                    <td>ông/tôi</td>
                                    <td>0986896800</td>
                                    <td><a href="#">Copy link</a></td>
                                    <td><input type="checkbox" value="" checked></td>
                                    <td><input type="checkbox" value="" ></td>
                                    <td>
                                        <a href=""><i class="fa fa-pencil"></i></a>
                                        <a href=""><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Trường</td>
                                    <td>ông/tôi</td>
                                    <td>0986896800</td>
                                    <td><a href="#">Copy link</a></td>
                                    <td><input type="checkbox" value="" checked></td>
                                    <td><input type="checkbox" value="" ></td>
                                    <td>
                                        <a href=""><i class="fa fa-pencil"></i></a>
                                        <a href=""><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2"></div>
    </div>
</div>

<?php
get_footer();
?>