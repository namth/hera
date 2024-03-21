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
        if (!empty($listcards)) {
            foreach ($listcards as $card) {
                echo "<tr>";
                echo "<td>" . $card->ID . "</td>";
                echo "<td><img src='" . $card->thumbnail . "' width=80/></td>";
                echo "<td>" . $card->title . "</td>";
                echo "<td><a href='" . get_bloginfo('url') . "/view-demo/?cardid=" . $card->ID . "' target='_blank'><i class='fa fa-eye' aria-hidden='true'></i> Xem mẫu</a></td>";
                echo "</tr>";
            }
        }
    ?>
</table>
