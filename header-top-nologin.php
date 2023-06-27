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
        <?php
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
        
            $login_amount = get_user_meta($user->ID, 'login_amount', true);
        
            $link_avatar = get_avatar_url($user->ID);
        
            ?>
                <span>Xin chào, <b><?php echo $user->data->display_name; ?></b></span>
                <img src="<?php echo $link_avatar; ?>" alt="login-<?php echo $login_amount; ?>" data-mui-toggle="dropdown">
                <ul class="mui-dropdown__menu">
                    <?php 
                        wp_nav_menu(array(
                            'menu'      => 5,
                            'container' => '',
                            'items_wrap' => '%3$s'
                        ));
                    ?>
                    <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home">
                        <a href="<?php echo get_author_posts_url($user->ID); ?>"><i class="fa fa-id-card-o" aria-hidden="true"></i> Cài đặt tài khoản</a>
                    </li>
                    <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home">
                        <a href="<?php echo wp_logout_url(); ?>"><i class="fa fa-sign-out" aria-hidden="true"></i> Đăng xuất</a>
                    </li>
                </ul>
            <?php
        } else {
            echo '<a href="' . get_permalink(5) . '">Đăng nhập | Đăng ký</a>';
        }
        ?>
        
    </div>
</div>