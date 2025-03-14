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
        <th>Cô dâu chú rể</th>
        <th>Số lượng thiệp đã sử dụng</th>
        <th>Ngày đăng ký</th>
        <th>Lần login cuối</th>
        <th>Logged in</th>
    </tr>
    <?php 
        $i = $offset;
        if (!empty($users)) {
            foreach ($users as $user) {

                // $partner_id     = get_field('inviter', 'user_' . $user->ID);
                $total_cards    = get_field('total_cards', 'user_' . $user->ID);
                $used_cards     = get_field('used_cards', 'user_' . $user->ID);
                // $partner        = get_user_by('ID', $partner_id);
                // $partnername    = $partner?$partner->display_name:"-";

                $lastlogin_timestamp = get_user_meta( $user->ID, '_last_login', true );
                $lastlogin_date = $lastlogin_timestamp?date('Y-m-d H:i:s', $lastlogin_timestamp):"-";
                $login_amount   = get_user_meta($user->ID, 'login_amount', true);
                $groom          = get_field('groom', 'user_' . $user->ID);
                $bride          = get_field('bride', 'user_' . $user->ID);
                $groom_bride    = ($groom != "" & $bride != "") ? implode(" & ", [$groom,$bride]) : "-";


                $total_cards    = $total_cards?$total_cards:"-";
                $used_cards     = $used_cards?$used_cards:"-";

                $i++;
                echo "<tr>";
                echo "<td>" . $user->ID . "</td>";
                echo "<td><a href='" . get_bloginfo('url') . "/main?uid=" . $user->ID . "' target='_blank'>" . $user->display_name . "</a></td>";
                echo "<td>" . $user->user_login . "</td>";
                echo "<td>" . $user->user_email . "</td>";
                echo "<td>" . $groom_bride . "</td>";
                echo "<td>" . $used_cards . "/" . $total_cards . "</td>";
                echo "<td>" . $user->user_registered . "</td>";
                echo "<td>" . $lastlogin_date . "</td>";
                echo "<td>" . $login_amount . "</td>";
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