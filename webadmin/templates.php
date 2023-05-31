<h3 class="title_general mui--divider-bottom">Danh sách thiệp chờ duyệt</h3>
<?php
if (isset($_POST['retoken']) && ($_POST['retoken'] == 1)) {
    $token = refresh_token();
} else {
    $token = get_field('token', 'option');
    # Kiểm tra nếu token vẫn hoạt động thì thôi, nếu không thì phải lấy lại token mới.
    if (!check_token($token)) {
        $token = refresh_token();
    }
}
$api_base_url = get_field('api_base_url', 'option');
$api_url = $api_base_url . '/wp-json/inova/v1/cards?status=private';
$listcards = inova_api($api_url, $token, 'GET', '');

// print_r($listcards);

?>
<table class="table table-hover">
    <tr>
        <th>ID</th>
        <th>Ảnh</th>
        <th>Tên</th>
        <th>Thao tác</th>
    </tr>
    <?php 
        $i = $offset;
        if (!empty($listcards)) {
            foreach ($listcards as $card) {
                $i++;
                echo "<tr>";
                echo "<td>" . $card->ID . "</td>";
                echo "<td><img src='" . $card->thumbnail . "' width=80/></td>";
                echo "<td>" . $card->title . "</td>";
                echo "<td><a href='" . get_bloginfo('url') . "/view-demo/?cardid=" . $card->ID . "' target='_blank'>Xem mẫu</a></td>";
                echo "</tr>";
            }
        }
    ?>
</table>
<div class="pagination justify-content-center">
    <?php
    $total_user = $query->total_users; 
    $total_pages = ceil($total_user / $users_per_page);
    
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