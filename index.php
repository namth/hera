<?php
get_header();
get_template_part('header', 'topbar');

if (isset($_POST['group_name'])) {
    $group_name = strip_tags($_POST['group_name']);
    $category = strip_tags($_POST['category']);
    $permalink = gen_uuid();
    $args = array(
        'post_title'    => $group_name,
        'post_status'   => 'publish',
        'post_type'     => 'thiep_moi',
        'post_name'     => $permalink,
    );

    $inserted = wp_insert_post($args, $error);

    if ($inserted) {
        # Tạo nhóm khách mời xong thì set trạng thái nhóm là đang hoạt động
        update_field('field_62a34ca619e78', "Running", $inserted);

        # Nếu có category thì set nhóm vào category tương ứng của nhà trai hoặc nhà gái.
        if ($category == 'bride') {
            wp_set_object_terms($inserted,"Nhà gái", 'category');
        } else {
            wp_set_object_terms($inserted,"Nhà trai", 'category');
        }
    }
}

# Khai báo một số biến cần dùng
$current_user_id = get_current_user_id();
$cards_array    = array();
$total_customer = 0;
$normal_license = get_field('normal_card', 'user_' . $current_user_id);
$vip_license    = get_field('vip_card', 'user_' . $current_user_id);

?>
<div class="mui-container-fluid">
    <div class="mui-row">
        <div class="mui-col-md-2 npl">
            <?php
            get_sidebar();
            ?>
        </div>
        <div class="mui-col-md-8 mt20">
            <div class="mui-panel" id="list_my_card">
                <h3 class="title_general mui--divider-bottom">Danh sách thiệp mời của nhà trai</h3>
                <div class="heracard_list mui-row">
                    <?php
                    $args   = array(
                        'post_type'     => 'thiep_moi',
                        'posts_per_page' => -1,
                        'author'        => $current_user_id,
                        'post_status'   => 'publish',
                        'category_name' => 'Nhà trai',
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();

                            $image = get_field('thumbnail');
                            $cardid = get_field('card_id');
                            $customer = get_field('guest_list');
                            $status = get_field('status');
                            $_customer = is_array($customer)?count($customer):0;
                            
                            if ($image) {
                                $card_thumbnail = $image;
                            } else {
                                $card_thumbnail = get_template_directory_uri() . '/img/no-img.png';
                            }

                            $total_customer += $_customer;
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
                                        <div class="quantity">
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                            <?php echo $_customer; ?>
                                        </div>
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
                        <button class="addnew_card" onclick="activateModal('groom')">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <h3 class="title_general mui--divider-bottom">Danh sách thiệp mời của nhà gái</h3>
                <div class="heracard_list mui-row">
                    <?php
                    
                    $args   = array(
                        'post_type'     => 'thiep_moi',
                        'posts_per_page' => -1,
                        'author'        => $current_user_id,
                        'post_status'   => 'publish',
                        'category_name' => 'Nhà gái',
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();

                            $image = get_field('thumbnail');
                            $cardid = get_field('card_id');
                            $customer = get_field('guest_list');
                            $status = get_field('status');
                            $_customer = is_array($customer)?count($customer):0;
                            
                            if ($image) {
                                $card_thumbnail = $image;
                            } else {
                                $card_thumbnail = get_template_directory_uri() . '/img/no-img.png';
                            }

                            $total_customer += $_customer;
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
                                        <div class="quantity">
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                            <?php echo $_customer; ?>
                                        </div>
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
                        <button class="addnew_card" onclick="activateModal('bride')">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mui-col-md-2">
            <div id="cart_section">
                
            </div>
        </div>
    </div>
</div>

<div class="mui-panel" id="cart_bar">
    <?php 
        # Cập nhật số lượng thiệp đã dùng.
        update_field('field_63b853e50f9a8', $total_customer, 'user_' . $current_user_id);
        $limit = get_field('total_cards', 'user_' . $current_user_id);
    ?>
    <div class="card_total">Bạn có <b><?php echo $total_customer . '/' . $limit; ?></b> thiệp</div>
    <a href="<?php echo get_bloginfo('url') . "/danh-sach-goi-san-pham/"; ?>" class="card_link"><i class="fa fa-cart-plus" aria-hidden="true"></i> Mua thêm thiệp</a>
</div>

<div class="mui-col-md-4 mui-col-sm-12" id="create_card_form">
    <form class="mui-form" method="POST">
        <legend>Tạo nhóm khách mới</legend>
        <div class="mui-textfield">
            <input id="group_input" type="text" placeholder="VD: Bạn cấp 3" name="group_name">
            <input type="hidden" name="category">
        </div>
        <button type="submit" class="mui-btn mui-btn--danger">Tạo</button>
    </form>
</div>
<script>
    /* Prevent resubmit form when page is reloaded */
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
<?php
get_footer();
?>