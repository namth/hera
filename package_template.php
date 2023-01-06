<?php 
/* 
* Template Name: Danh sách gói sản phẩm
*/

get_header();
get_template_part('header', 'topbar');

# lấy thông tin người dùng về số lượng thiệp đang có và gói đang sử dụng
$current_user_id = get_current_user_id();
$current_package_id = get_field('package_id', 'user_' . $current_user_id);
$current_max_card = get_field('total_cards', 'user_' . $current_user_id);
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-10 mt20">
            <div class="mui-panel" id="list_product">
                <h3 class="title_general mui--divider-bottom"><?php the_title(); ?></h3>
                <div class="package">
                    <?php 
                        $args   = array(
                            'post_type'     => 'package',
                            'posts_per_page' => 9,
                            'post_status'   => 'publish',
                        );

                        $query = new WP_Query($args);

                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();
                                
                                $total_card = get_field('total_card');
                                $price      = get_field('price');
                                $category   = get_field('category');


                                ?>
                                    <div class="mui-col-md-4">
                                        <div class="package_item">
                                            <div class="package_header">
                                                <h3><?php the_title(); ?></h3>
                                            </div>
                                            <div class="package_content">
                                                <?php 
                                                    $package_id = inova_encrypt( get_the_ID(), 'e');
                                                    the_post_thumbnail('thumbnail');
                                                    the_content(); 
                                                ?>
                                                <div class="price">
                                                    <span class="label">Đơn giá</span>
                                                    <span class="value"><?php echo number_format($price) . ' đ'; ?></span>
                                                </div>
                                                <ul>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i> Số lượng thiệp: <b><?php echo number_format($total_card); ?></b></li>
                                                    <li><i class="fa fa-check" aria-hidden="true"></i> Được truy cập thư viện thiệp cơ bản</li>
                                                </ul>
                                                <?php 
                                                    if (get_the_ID() == $current_package_id) {
                                                        echo '<span class="package_locked">Bạn đang sử dụng gói này</span>';
                                                    } else if ($total_card < $current_max_card) {
                                                        echo '<span class="package_locked">Liên hệ để đổi gói</span>';
                                                    } else {
                                                        echo '<a class="mui-btn hera-btn" href="' . get_bloginfo('url') . '/xac-nhan-thanh-toan/?p=' . $package_id . '">Đăng ký</a>';
                                                    }
                                                ?>
                                                
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            } wp_reset_postdata();
                        }
                    ?>

                </div>
            </div>
        </div>
        <div class="mui-col-md-2">
        </div>
    </div>
</div>
<?php
get_footer();