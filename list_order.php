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
        <div class="mui-col-md-2">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-10">
            <div class="mui-panel" id="checkout">
                <h3 class="title_general mui--divider-bottom">Danh sách đơn hàng</h3>
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Ngày tháng</th>
                        <th>Mã đơn hàng</th>
                        <th>Số thiệp thường</th>
                        <th>Số thiệp VIP</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                    <?php 
                        $paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

                        $args   = array(
                            'post_type'     => 'inova_order',
                            'posts_per_page' => 20,
                            'paged'         => $paged,
                            'author'        => $current_user_id,
                            'post_status'   => 'publish',

                        );
    
                        $query = new WP_Query($args);
    
                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();
                                $status = get_field('status');
                                $normal_cards = get_field('normal_cards');
                                $vip_cards = get_field('vip_cards');
                                $final_total = get_field('final_total');
                                
                                if ($status=="Chưa thanh toán") {
                                    $status_div = '<span class="error_notification">'. $status .'</span>';
                                } else if ( $status=="Đã thanh toán" ){
                                    $status_div = '<span class="success_notification">'. $status .'</span>';
                                } else {
                                    $status_div = '<span class="notification">'. $status .'</span>';
                                }
                                echo "<tr data-url='" . get_permalink() . "'>
                                        <a href='#'>
                                        <td>" . get_the_ID() . "</td>
                                        <td><a href='" . get_permalink() . "'>". get_the_date('d/m/Y') ."</a></td>
                                        <td><a href='" . get_permalink() . "'>". get_the_title() ."</a></td>
                                        <td>" . number_format($normal_cards) . "</td>
                                        <td>" . number_format($vip_cards) . "</td>
                                        <td>" . number_format($final_total) . "</td>
                                        <td>". $status_div ."</td>
                                        </a>
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
    </div>
</div>
<?php
get_footer();