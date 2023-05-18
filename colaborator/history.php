<h3>Lịch sử rút tiền</h3>
<div class="mui-row" id="withdraw">
    <div class="mui-col-md-12">
        <div class="mui-panel"> 
            <table class="table">
                <tr>
                    <th>Ngày tháng</th>
                    <th>Số tiền có thể rút</th>
                    <th>Số tiền yêu cầu rút</th>
                    <th>Ngày xử lý yêu cầu</th>
                    <th>Trạng thái</th>
                </tr>
                <?php 
                    $current_user_id = get_current_user_id();
                    // $where_update = 'user_' . $current_user_id;
                    $paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
                    $post_per_page = 10;

                    $args   = array(
                        'post_type'     => 'invoice',
                        'posts_per_page' => $post_per_page,
                        'paged'         => $paged,
                        'author'        => $current_user_id,
                        'post_status'   => 'publish',

                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();

                            $withdrawn_amount   = get_field('withdrawn_amount');
                            $request_amount     = get_field('request_amount');
                            $payment_date       = get_field('payment_date');
                            $payment_status     = get_field('payment_status');

                            echo '<tr>
                                    <td>' . get_the_date() . '</td>
                                    <td>' . number_format($withdrawn_amount) . ' đ</td>
                                    <td>' . number_format($request_amount) . ' đ</td>
                                    <td>' . $payment_date . '</td>
                                    <td>' . $payment_status . '</td>
                                </tr>';
                        } wp_reset_postdata();
                    } else {
                        echo '<tr><td colspan="5" style="text-align: center;">Không có dữ liệu.</td></tr>';
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