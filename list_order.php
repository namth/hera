<?php 
/* 
* Template Name: Danh sách đơn hàng
*/
get_header();
get_template_part('header', 'topbar');

$current_user_id = get_current_user_id();
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-8 mt20">
            <div class="mui-panel" id="checkout">
                <h3 class="title_general mui--divider-bottom">Danh sách đơn hàng</h3>
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Ngày tháng</th>
                        <th>Mã đơn hàng</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                    <?php 
                        $paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
                        $post_per_page = 10;
                        $i = ($paged - 1) * $post_per_page;

                        $args   = array(
                            'post_type'     => 'inova_order',
                            'posts_per_page' => $post_per_page,
                            'paged'         => $paged,
                            'author'        => $current_user_id,
                            'post_status'   => 'publish',

                        );
    
                        $query = new WP_Query($args);
    
                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();

                                $status = get_field('status');
                                $final_total = (int) get_field('final_total');
                                $package_id = get_field('package');
                                $total_card = (int) get_field('total_card', $package_id);
                                $product_name = $package_id?get_the_title($package_id):"Thiệp cưới online";
                                
                                // Change status for free orders
                                if ($status == "Chưa thanh toán" && $final_total == 0) {
                                    $status = "Chưa kích hoạt";
                                }
                                
                                if ($status=="Chưa thanh toán") {
                                    $status_div = '<span class="error_notification">'. $status .'</span>';
                                } else if ( $status=="Đã thanh toán" ){
                                    $status_div = '<span class="success_notification">'. $status .'</span>';
                                } else {
                                    $status_div = '<span class="notification">'. $status .'</span>';
                                }
                                echo "<tr data-url='" . get_permalink() . "'>
                                        <td>" . ++$i . "</td>
                                        <td><a href='" . get_permalink() . "'>". get_the_date('d/m/Y') ."</a></td>
                                        <td><a href='" . get_permalink() . "'>". get_the_title() ."</a></td>
                                        <td><a href='" . get_permalink() . "'>" . $product_name . "</td>
                                        <td>" . number_format($total_card) . "</td>
                                        <td>" . number_format($final_total) . "</td>
                                        <td>". $status_div ."</td>
                                    </tr>";
                            } wp_reset_postdata();
                        }
                    ?>
                       
                </table>
                <?php 
                    $big = 999999999; // need an unlikely integer

                    echo paginate_links(array(
                        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format'    => '?paged=%#%',
                        'current'   => max(1, get_query_var('paged')),
                        'total'     => $query->max_num_pages,
                        'type'      => 'list',
                    ));
                ?>
            </div>
        </div>
        <div class="mui-col-md-2 left_sidebar">
            <?php
            $menu = 'Hướng dẫn thanh toán';
            $guideline = wp_nav_menu(array(
                'menu'          => $menu,
                'container_id'  => 'guide_section',
                'container_class'   => '',
                'items_wrap'    => '<a href="#" class="maximize"><i class="fa fa-external-link" aria-hidden="true"></i> Khôi phục</a><img src="' . get_template_directory_uri() . '/img/thaochi.jpg"><ul class="playlist"><h4>Hướng dẫn nhanh</h4>%3$s</ul><a href="#" class="minimize"><i class="fa fa-level-down" aria-hidden="true"></i> Thu nhỏ</a>',
                'menu_class'    => 'main_menu mb20',
                'echo' => FALSE,
                'fallback_cb' => '__return_false'
            ));

            if ( ! empty ( $guideline ) ){
                echo $guideline;
            }
            ?>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/js/soundmanager2-jsmin.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/soundmanager2-player.js"></script>
<link href="<?php echo get_template_directory_uri(); ?>/css/soundmanager2-player.css" rel="stylesheet" type="text/css">
<?php
get_footer();