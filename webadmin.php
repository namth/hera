<?php
/* 
    Template Name: Web Admin
*/
if ( is_user_logged_in() && current_user_can('administrator') ) {
    $user = wp_get_current_user();

    $link_avatar = get_avatar_url($user->ID);
    get_header();
    ?>

    <div class="mui-panel" id="header_bar">
        <div class="logo">
            <a href="<?php echo get_bloginfo('url'); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="">
            </a>
        </div>
        <div class="mui-dropdown greeting">
            <span><b><?php echo $user->data->display_name; ?></b></span>
            <img src="<?php echo $link_avatar; ?>" alt="" data-mui-toggle="dropdown">
            <ul class="mui-dropdown__menu">
                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home">
                    <a href="<?php echo get_author_posts_url($user->ID); ?>"><i class="fa fa-id-card-o" aria-hidden="true"></i> Cài đặt tài khoản</a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home">
                    <a href="<?php echo wp_logout_url(); ?>"><i class="fa fa-sign-out" aria-hidden="true"></i> Đăng xuất</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="mui-container-fluid">
        <div class="mui-row">
            <div class="mui-col-md-2 npl">
                <div id="hera_sidebar">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/top.png" alt="">
                    <ul class="main_menu">
                        <li>
                            <a href="<?php echo get_permalink() . '?direct=index'; ?>">Home</a>
                        </li>
                        <li>
                            <a href="<?php echo get_permalink() . '?direct=users'; ?>">Danh sách user</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mui-col-md-10 mt20">
                <div class="mui-panel">
                    <?php 
                        $dir = dirname( __FILE__ );

                        if ( isset( $_GET['direct'] ) && ($_GET['direct'] != "") ) {
                            $page = $_GET['direct'];

                            switch ($page) {
                                case 'users':
                                    require_once( $dir . '/webadmin/users.php');
                                    break;
                                
                                default:
                                    require_once( $dir . '/webadmin/index.php');
                                    break;
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    get_footer();
} else {
    echo 'Error 404.';
}