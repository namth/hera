<?php
if (is_user_logged_in()) {
    $user = wp_get_current_user();

    $link_avatar = get_avatar_url($user->ID);
?>
    <div class="mui-panel" id="header_bar">
        <div class="logo">
            <a href="<?php echo get_bloginfo('url'); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="">
            </a>
        </div>
        <div class="mui-dropdown greeting">
            <span>Xin chào, <b><?php echo $user->data->display_name; ?></b></span>
            <img src="<?php echo $link_avatar; ?>" alt="" data-mui-toggle="dropdown">
            <?php 
                wp_nav_menu(array(
                    'menu'      => 2,
                    'container' => '',
                    'menu_class' => 'mui-dropdown__menu'
                ));
            ?>
        </div>
    </div>
<?php
} else if (!is_page('login')) {
    wp_redirect(get_permalink(5));
    exit;
    // echo 'redirect to login';
}
?>