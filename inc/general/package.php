<?php
add_shortcode('package', 'package_display');
function package_display( $atts, $content = null  ) {
    extract(shortcode_atts(array(
        'category' => 'Invitation', /* The value that 'category' can have: Invitation, Landingpage  */
    ), $atts ));

    ?>
        <div class="package">
            <div class="mui-row">
            <?php 
                /* query get all package from custom post type with the custom field is category */
                $args   = array(
                    'post_type'     => 'package',
                    'posts_per_page' => 9,
                    'post_status'   => 'publish',
                    'meta_query'    => array(
                        array(
                            'key'       => 'category',
                            'value'     => $category,
                            'compare'   => '=',
                        ),
                    ),
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

                        # if $coupon_value greater than 0, then calculate $percent
                        if($coupon_value < $price){
                            $percent = round($coupon_value/$price * 100 - 100);
                        } else {
                            $percent = 0;
                        }

                        ?>
                            <div class="mui-col-md-4">
                                <div class="package_item box">
                                    <?php 
                                        if ($coupon_value < $price) {
                                            echo '<div class="ribbon ribbon-top-right"><span>' . $percent . '%</span></div>';
                                        }
                                    ?>
                                    <div class="package_header">
                                        <h3><?php the_title(); ?></h3>
                                    </div>
                                    <div class="package_content">
                                        <?php 
                                            $package_id = inova_encrypt( get_the_ID(), 'e');
                                            the_post_thumbnail("full");
                                        ?>
                                        <div class="price">
                                            <span class="label">Đơn giá</span>
                                            <?php 
                                                if ($coupon_value < $price) {
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
    <?php
}