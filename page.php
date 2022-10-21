<?php 
get_header();

?>
<div class="mui-panel" id="header_bar">
    <div class="logo">
        <a href="<?php echo get_bloginfo('url'); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="">
        </a>
    </div>
    <div class="mui-dropdown main_menu">
        <button class="mui-btn" data-mui-toggle="dropdown">
            <i class="fa fa-user"></i>
        </button>
        <?php
        wp_nav_menu(array(
            'menu'       => '5',
            'container'     => '',
            'menu_class'    => 'mui-dropdown__menu'
        ));
        ?>
    </div>
</div>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2">
            <?php
            get_sidebar('nologin');
            echo $thongbao;
            ?>
        </div>
        <div class="mui-col-md-10">
            <div class="breadcrumb">
                <a href="<?php echo get_bloginfo('url'); ?>">Trang chủ</a>
                <i class="fa fa-chevron-right"></i>
                <span><?php echo get_the_title(); ?></span>
            </div>
            <div class="mui-panel" id="page">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();