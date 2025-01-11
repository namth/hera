<?php 
get_header();
get_template_part('header', 'top-nologin');
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
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