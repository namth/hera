<?php
/* 
* Template Name: Sửa nội dung thiệp
*/
if (isset($_GET['g']) && ($_GET['g'] != "" )) {
    $group_data = json_decode(inova_encrypt($_GET['g'], 'd'));
    $group_check = true;

    /* Đọc dữ liệu groupID và userID */
    $postid = $group_data->groupid;
    $userID = $group_data->userid;
    $noi_dung_1 = get_field('content_1', $postid);
    $noi_dung_2 = get_field('content_2', $postid);
    $noi_dung_3 = get_field('content_3', $postid);
    $loi_moi    = get_field('custom_invite', $postid)?get_field('custom_invite', $postid):get_field('wedding_invitation', 'option');

    /* Kiểm tra xem có đúng là của user đó không, nếu đúng thì cho sửa, nếu không thì không hiển thị */
    $current_user_id = get_current_user_id();

    if ($userID == $current_user_id) {

        get_header();
        get_template_part('header', 'topbar');
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <div class="mui-container-fluid">
                    <div class="mui-row">
                        <div class="mui-col-md-2 npl">
                            <?php
                            get_sidebar();
                            ?>
                        </div>
                        <div class="mui-col-md-8">
                            <div class="breadcrumb">
                                <a href="<?php echo get_bloginfo('url'); ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                                <i class="fa fa-chevron-right"></i>
                                <span contenteditable="true" oncut="return false" onpaste="return false" class="title" data-guestid="<?php echo get_the_ID(); ?>"> <?php the_title(); ?></span>
                                <span class="loader"><img src="<?php echo get_template_directory_uri() . '/img/heart-preloader.gif'; ?>" alt=""></span>
                            </div>
                            <div class="mui-panel">
                                <a href="<?php echo get_permalink($postid) ?>"><span><i class="fa fa-arrow-left" aria-hidden="true"></i></span> Quay lại</a>
                                <h3><?php the_title(); ?></h3>
                                <div class="wedding_content">
                                    <span class="diveditable content_editable" contenteditable=true data-field="field_63ceb66556861" data-where="<?php echo $postid; ?>"><?php echo $noi_dung_1; ?></span>
                                    <span class="none_edit">Tên khách mời</span>
                                    <span class="diveditable content_editable" contenteditable=true data-field="field_63ceb69856862" data-where="<?php echo $postid; ?>"><?php echo $noi_dung_2; ?></span>
                                    <span class="none_edit">Chú rể & Cô dâu</span>
                                    <span class="diveditable content_editable" contenteditable=true data-field="field_63ceb6e956863" data-where="<?php echo $postid; ?>"><?php echo $noi_dung_3; ?></span>
                                    <span class="none_edit">Thời gian và địa điểm tổ chức</span>
                                    <span class="diveditable content_editable" contenteditable=true data-field="field_610eb5f9cdc28" data-where="<?php echo $postid; ?>"><?php echo $loi_moi; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="<?php echo get_template_directory_uri(); ?>/js/wedding-infomation.js"></script>
                <?php
            }
        }
        get_footer();

    }
}
