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
            <div class="mui-panel" id="thankyou">
                <div>
                    <img src="<?php echo get_template_directory_uri(); ?>/img/thankyou.gif" alt="" />
                    <h3>
                        Thanh toán thành công. Bạn có thể quay lại trang chủ để tiếp tục.
                    </h3>
                    <a href="<?php echo get_bloginfo('url'); ?>" class="mui-btn hera-btn">Về trang chủ</a>    
                </div>

            </div>
        </div>
    </div>
</div>

<?php

get_footer();
