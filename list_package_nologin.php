<?php
/* 
* Template Name: List Order Nologin
*/

get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
?>
<div id="header_topbar" class="mui-row">
    <div class="mui-col-md-10 mui-col-md-offset-1">
    <?php 
        wp_nav_menu(array(
            'menu'      => 6,
            'container' => '',
        ));
    ?>
    </div>
</div>
<div class="mui-panel mui-row" id="header">
    <div id="center_logo" class="mui-col-md-10 mui-col-md-offset-1">
        <a href="<?php echo get_bloginfo('url'); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo_hera.png" alt="">
        </a>
    </div>
    <div id="header_bottom" class="mui-col-md-10 mui-col-md-offset-1">
        <?php 
            wp_nav_menu(array(
                'menu'      => 6,
            ));
        ?>
    </div>
</div>
<div class="mui-container-fluid" style="background: white;">
    <div class="mui-row">
        <div class="mui-col-md-1"></div>
        <div class="mui-col-md-10 mt20">
            <div id="list_product">
                <h3 class="title_general" style="text-align: center;">Gói sản phẩm thiệp cưới cơ bản</h3>
                <div class="package">
                    <div class="mui-row">
                    <?php 
                        $args   = array(
                            'post_type'     => 'package',
                            'posts_per_page' => 9,
                            'post_status'   => 'publish',
                        );

                        $query = new WP_Query($args);

                        $i = 0;
                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();
                                
                                $i++;
                                $total_card = get_field('total_card');
                                $price      = get_field('price');
                                $category   = get_field('category');
                                $coupon     = get_field('coupon');
                                $coupon_value = get_value_after_coupon($coupon, get_the_ID());

                                $percent = round($coupon_value/$price * 100 - 100);

                                ?>
                                    <div class="mui-col-md-4">
                                        <div class="package_item box">
                                            <?php 
                                                if ($coupon) {
                                                    echo '<div class="ribbon ribbon-top-right"><span>' . $percent . '%</span></div>';
                                                }
                                            ?>
                                            <div class="package_header">
                                                <h3><?php the_title(); ?></h3>
                                            </div>
                                            <div class="package_content">
                                                <?php 
                                                    $package_id = inova_encrypt( get_the_ID(), 'e');
                                                    // the_post_thumbnail('thumbnail');
                                                ?>
                                                <div class="price">
                                                    <span class="label">Đơn giá</span>
                                                    <?php 
                                                        if ($coupon) {
                                                            echo '<span class="listed_price">' . number_format($price) . ' đ</span>';
                                                            echo '<span class="value">' . number_format($coupon_value) . ' đ</span>';
                                                        } else {
                                                            echo '<span class="value no_promote">' . number_format($price) . ' đ</span>';
                                                        }
                                                    ?>
                                                </div>
                                                <?php 
                                                    the_content(); 

                                                    /* if (get_the_ID() == $current_package_id) {
                                                        echo '<span class="package_locked">Bạn đang sử dụng gói này</span>';
                                                    } else if ($total_card < $current_max_card) {
                                                        echo '<span class="package_locked">Liên hệ để đổi gói</span>';
                                                    } else {
                                                        echo '<a class="mui-btn hera-btn" href="' . get_bloginfo('url') . '/xac-nhan-thanh-toan/?p=' . $package_id . '">Đăng ký</a>';
                                                    } */
                                                    echo '<a class="mui-btn hera-btn" href="' . get_bloginfo('url') . '/xac-nhan-thanh-toan/?p=' . $package_id . '">Đăng ký</a>';
                                                ?>
                                                
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                if ($i%3 == 0) {
                                    echo '</div><div class="mui-row">';
                                }
                            } wp_reset_postdata();
                        }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
    }
}