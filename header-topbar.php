<?php
if (is_user_logged_in()) {
    $user = wp_get_current_user();

    $login_amount = get_user_meta($user->ID, 'login_amount', true);

    $link_avatar = get_avatar_url($user->ID);
?>
    <div class="mui-panel" id="header_bar">
        <div class="logo">
            <a href="<?php echo get_bloginfo('url'); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="">
            </a>
        </div>
        <div class="mui-dropdown greeting">
            <span>Xin chào, <b><?php echo $user->data->display_name . " (" . $login_amount . ")"; ?></b></span>
            <img src="<?php echo $link_avatar; ?>" alt="" data-mui-toggle="dropdown">
            <ul class="mui-dropdown__menu">
                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home">
                    <a href="<?php echo get_author_posts_url($user->ID); ?>"><i class="fa fa-id-card-o" aria-hidden="true"></i> Cài đặt tài khoản</a>
                </li>
            </ul>
        </div>
    </div>
<?php
} else if (!is_page('login')) {
    wp_redirect(get_permalink(5));
    exit;
    // echo 'redirect to login';
}
?>