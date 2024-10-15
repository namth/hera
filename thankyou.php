<?php
/* 
    Template Name: Thank you sau khi thanh toán xong 
*/

get_header();
get_template_part('header', 'topbar');

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-10 mt20">
            <div class="mui-panel">
                <div id="thankyou">
                    <dotlottie-player src="<?php echo get_template_directory_uri() . '/img/payment_success.json'; ?>" 
                            background="transparent" speed="1" 
                            style="width: 300px; height: 300px" direction="1" playMode="normal" autoplay>
                    </dotlottie-player>
                    <h3>
                        Thanh toán thành công. Bạn có thể quay lại trang chủ để tiếp tục.
                    </h3>
                    <a href="<?php echo get_bloginfo('url'); ?>/main/" class="mui-btn hera-btn">Về trang chủ</a>    
                </div>

            </div>
        </div>
    </div>
</div>

<?php

get_footer();
