<?php
if (is_user_logged_in()) {
?>
    <div class="mui-panel" id="header_bar">
        <div class="logo">
            <a href="<?php echo get_bloginfo('url'); ?>"><i class="fas fa-award"></i></a>
        </div>
        <div class="mui-dropdown main_menu">
            <button class="mui-btn" data-mui-toggle="dropdown">
                <i class="fas fa-bars"></i>
            </button>
            <?php
            wp_nav_menu(array(
                'container' => '',
                'menu_class' => 'mui-dropdown__menu'
            ));
            ?>
        </div>
    </div>
<?php
} else if (!is_page('login')) {
    wp_redirect(get_permalink(42));
    exit;
    // echo 'redirect to login';
}
?>