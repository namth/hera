<div class="mui-panel mui-row" id="header">
    <div id="center_logo" class="mui-col-md-10 mui-col-md-offset-1">
        <a href="<?php echo get_bloginfo('url'); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo_hera.png" alt="">
        </a>
    </div>
</div>
<div class="mobile_menu_nologin">
    <a href="#" class="mobile_menu_icon">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </a>
    <div class="menu">
        <img src="<?php echo get_template_directory_uri(); ?>/img/top.png" alt="">
        <span class="close_mobile_menu_button">X</span>
        <?php
        wp_nav_menu(array(
            'menu'      => 6,
            'container' => '',
            'menu_class' => 'main_menu nologin-mobile-menu mb20'
        ));
        ?>
        <div class="overlay"></div>
    </div>
</div>
<div id="header_bottom" class="mui-panel mui-row">
    <?php
    wp_nav_menu(array(
        'menu'      => 6,
    ));
    ?>
</div>