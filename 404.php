<?php 
get_header();
get_template_part('header', 'top-nologin');
?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div id="p404">
            <div>
                <dotlottie-player src="<?php echo get_template_directory_uri() . '/img/404cat.json'; ?>" 
                        background="transparent" speed="1" 
                        style="width: 300px; height: 300px" direction="1" playMode="bounce" loop autoplay>
                </dotlottie-player>
            </div>
            <h2>Rất tiếc, trang của bạn tìm không tồn tại!</h2>
            <p>Mời bạn quay lại trang chủ để xem các nội dung khác nhé.</p>
            <a href="<?php echo get_bloginfo('url'); ?>" class="hera-btn mui-btn">Về trang chủ</a>
        </div>
    </div>
</div>
<?php
get_footer();