<?php
get_header();
get_template_part('header', 'topbar');

if (isset($_POST['group_name'])) {
    $group_name = strip_tags($_POST['group_name']);
    $args = array(
        'post_title'    => $group_name,
        'post_status'   => 'publish',
        'post_type'     => 'thiep_moi',
    );

    $inserted = wp_insert_post($args, $error);

    if ($inserted) {
        # Tạo nhóm khách mời xong thì set trạng thái nhóm là đang hoạt động
        update_field('field_62a34ca619e78', "Running", $inserted);
    }
}

$current_user_id = get_current_user_id();

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-8">
            <div class="mui-panel" id="list_my_card">
                <h3 class="title_general mui--divider-bottom">Danh sách thiệp của tôi</h3>
                <div class="heracard_list mui-row">
                    <?php
                    $token = get_field('token', 'option');
                    $api_base_url = get_field('api_base_url', 'option');
                    
                    $args   = array(
                        'post_type'     => 'thiep_moi',
                        'posts_per_page' => -1,
                        'author'        => $current_user_id,
                        'post_status'   => 'publish',
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();

                            $image = get_field('thumbnail');
                            $cardid = get_field('card_id');
                            
                            if ($image) {
                                $card_thumbnail = $image;
                            } else {
                                $card_thumbnail = get_template_directory_uri() . '/img/no-img.png';
                            }


                    ?>
                            <div class="mui-col-md-3">
                                <a href="<?php the_permalink(); ?>">
                                <div class="heracard">
                                    <div class="images" style="<?php
                                                                echo 'background: url(' . $card_thumbnail . ') no-repeat 50% 50%;';
                                                                echo 'background-size: contain;';
                                                                ?>">

                                    </div>
                                    <div class="info_card">
                                        <?php echo get_the_title(); ?>
                                    </div>
                                </div>
                                </a>
                            </div>
                    <?php
                        }
                        wp_reset_postdata();
                    }

                    ?>
                    <div class="mui-col-md-3">
                        <button class="addnew_card" onclick="activateModal()">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2"></div>
    </div>
</div>

<div class="mui-panel" id="cart_bar">
    <span>Ban co: 115 thiep.</span>
    <span>Tong tien: 456.000d</span>
    <a href="#">Xem chi tiet</a>
    <button class="mui-btn mui-btn--danger">Thanh toán</button>
</div>

<div class="mui-col-md-4 mui-col-sm-12" id="create_card_form">
    <form class="mui-form" method="POST">
        <legend>Tạo nhóm khách mới</legend>
        <div class="mui-textfield">
            <input id="group_input" type="text" placeholder="VD: Bạn cấp 3" name="group_name">
        </div>
        <button type="submit" class="mui-btn mui-btn--danger">Tạo</button>
    </form>
</div>

<?php
get_footer();
?>