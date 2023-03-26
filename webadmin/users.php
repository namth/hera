<h3 class="title_general mui--divider-bottom">Danh sách user</h3>
<?php 
$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

// how many users to show per page
// $role = 'subscriber';
$users_per_page = 20;

// calculate the total number of pages.
$offset = $users_per_page * ($paged - 1);

$args   = array(
    'role'      => $role, /*partner, member, subscriber, contributor, author*/
    'number'    => $users_per_page,
    'paged'     => $paged,
    'offset'    => $offset,
    'orderby'   => 'user_registered',
    'order'     => 'DESC'
);
$query = new WP_User_Query($args);
$users = $query->get_results();

?>
<table class="table table-hover">
    <tr>
        <th>#</th>
        <th>Tên</th>
        <th>Username</th>
        <th>Email</th>
        <th>Ngày đăng ký</th>
    </tr>
    <?php 
        $i = $offset;
        if (!empty($users)) {
            foreach ($users as $user) {
                $i++;
                echo "<tr>";
                echo "<td>" . $i . "</td>";
                echo "<td>" . $user->display_name . "</td>";
                echo "<td>" . $user->user_login . "</td>";
                echo "<td>" . $user->user_email . "</td>";
                echo "<td>" . $user->user_registered . "</td>";
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