<?php 
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
            <!-- <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                
            </div> -->
            <div class="mui-panel">
                <h3 class="title_general">Thông tin tài khoản</h3>
                <div class="user_infomation">
                <?php 
                    $user = wp_get_current_user();
                    $link_avatar = get_avatar_url($user->ID);
                    $phone = get_field('phone', 'user_' . $user->ID);
                    $address = get_field('address', 'user_' . $user->ID);
                    echo '<div class="avatar">
                            <img src="' . $link_avatar . '">
                            <a href="" class="upload"><i class="fa fa-camera-retro" aria-hidden="true"></i></a>
                        </div>';
                    echo '<table>
                            <tr>
                                <td>Tên đăng nhập:</td>
                                <td>' . $user->user_login . '</td>
                            </tr>
                            <tr>
                                <td>Họ và tên:</td>
                                <td>' . $user->display_name . '</td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td>' . $user->user_email . '</td>
                            </tr>';
                    if ($phone) {
                        echo '<tr>
                                <td>Điện thoại:</td>
                                <td>' . $phone . '</td>
                            </tr>';
                    }
                    echo '</table>';
                ?>
                </div>

                <div class="update_user_button">
                    <a href="http://" class="mui-btn hera-btn">Sửa thông tin tài khoản</a>
                    <a href="http://" class="mui-btn hera-btn">Đổi mật khẩu</a>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2"></div>
    </div>
</div>

<?php
get_footer();