<!-- <div id="header_topbar" class="mui-row">
    <div class="mui-col-md-10 mui-col-md-offset-1">
    <?php 
        wp_nav_menu(array(
            'menu'      => 6,
            'container' => '',
        ));
    ?>
    </div>
</div> -->
<div class="mui-panel mui-row" id="header">
    <div id="center_logo" class="mui-col-md-10 mui-col-md-offset-1">
        <a href="<?php echo get_bloginfo('url'); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo_hera.png" alt="">
        </a>
    </div>
</div>
<div id="header_bottom" class="mui-panel mui-row">
    <?php 
        wp_nav_menu(array(
            'menu'      => 6,
        ));
    ?>
</div>
