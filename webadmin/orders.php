<h3 class="title_general mui--divider-bottom">Danh sách đơn hàng</h3>
<?php 
$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

// how many orders to show per page
$orders_per_page = 20;

// calculate the total number of pages.
$offset = $orders_per_page * ($paged - 1);

$args = array(
    'post_type'      => 'inova_order',
    'posts_per_page' => $orders_per_page,
    'paged'          => $paged,
    'offset'         => $offset,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish'
);
$query = new WP_Query($args);
?>
<table class="table table-hover">
    <tr>
        <th>#</th>
        <th>Ngày tháng</th>
        <th>Mã đơn hàng</th>
        <th>Khách hàng</th>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>
    <?php 
        $i = $offset;
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $order_id = get_the_ID();
                
                $status = get_field('status');
                $final_total = (int) get_field('final_total');
                $package_id = get_field('package');
                $total_card = (int) get_field('total_card', $package_id);
                $product_name = $package_id ? get_the_title($package_id) : "Thiệp cưới online";
                
                // Get customer info
                $author_id = get_post_field('post_author', $order_id);
                $customer = get_user_by('id', $author_id);
                $customer_name = $customer ? $customer->display_name : "N/A";
                
                if ($status == "Chưa thanh toán") {
                    $status_div = '<span class="error_notification">'. $status .'</span>';
                } else if ($status == "Đã thanh toán") {
                    $status_div = '<span class="success_notification">'. $status .'</span>';
                } else {
                    $status_div = '<span class="notification">'. $status .'</span>';
                }
                
                $i++;
                echo "<tr>
                    <td>" . $i . "</td>
                    <td>" . get_the_date('d/m/Y') . "</td>
                    <td><a href='" . get_permalink() . "' target='_blank'>" . get_the_title() . "</a></td>
                    <td><a href='" . get_bloginfo('url') . "/main?uid=" . $author_id . "' target='_blank'>" . $customer_name . "</a></td>
                    <td>" . $product_name . "</td>
                    <td>" . number_format($total_card) . "</td>
                    <td>" . number_format($final_total) . "</td>
                    <td>" . $status_div . "</td>
                    <td>
                        <a href='" . admin_url('post.php?post=' . $order_id . '&action=edit') . "' class='mui-btn mui-btn--small mui-btn--primary' target='_blank' title='Edit'><i class='ph ph-pencil'></i></a>
                        <a href='" . get_delete_post_link($order_id) . "' class='mui-btn mui-btn--small mui-btn--danger' onclick='return confirm(\"Bạn có chắc chắn muốn xóa đơn hàng này?\")' title='Delete'><i class='ph ph-trash'></i></a>
                    </td>
                </tr>";
            }
            wp_reset_postdata();
        } else {
            echo "<tr><td colspan='9'>Không tìm thấy đơn hàng nào.</td></tr>";
        }
    ?>
</table>
<div class="pagination justify-content-center">
    <?php
    $total_pages = $query->max_num_pages;
    
    $big = 999999999; // need an unlikely integer

    echo paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, get_query_var('paged')),
        'total'     => $total_pages,
        'type'      => 'list',
    ));
    ?>
</div>
