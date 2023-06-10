<div class="mui-panel" id="header_bar">
    <div class="logo">
        <a href="<?php echo get_bloginfo('url'); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="">
        </a>
    </div>
    <div class="mobile_menu">
        <a href="#" class="mobile_menu_icon">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </a>
        <div class="menu">
            <img src="<?php echo get_template_directory_uri(); ?>/img/top.png" alt="">
            <?php 
                wp_nav_menu(array(
                    'menu'      => 6,
                    'container' => '',
                    'menu_class' => 'main_menu mb20'
                ));
            ?>
            <div class="overlay"></div>
        </div>
    </div>
    <div class="mui-dropdown greeting">
        <a href="<?php echo get_permalink(5); ?>">Đăng nhập | Đăng ký</a>
    </div>
</div>