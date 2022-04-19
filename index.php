<?php 
    get_header();
    get_template_part('header','topbar');

    if (isset($_POST['card_name'])) {
        $args = array(
            'post_title'    => $_POST['card_name'],
            'post_status'   => 'publish',
            'post_type'     => 'inova_card',
        );

        $inserted = wp_insert_post( $args, $error );
    }

    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
?>
    <div class="mui-container-fluid">
        <div class="mui-row">
            <div class="mui-col-md-2">
                <button class="mui-btn mui-btn--danger" onclick="activateModal()">Tạo thiệp mới</button>
            </div>
            <div class="mui-col-md-7">
                <div class="mui-panel">
                    <h3>Danh sách thiệp của tôi</h3>
                    <div class="mui-divider"></div>
                    <div class="mui-row">
                        <?php 
                            $current_user = wp_get_current_user();

                            // xử lý phân trang
                            $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

                            $args   = array(
                                'post_type'     => 'inova_card',
                                'paged'         => $paged,
                                'posts_per_page'=> 20,
                            );

                            $query = new WP_Query( $args );

                            if( $query->have_posts() ) {
                                while ( $query->have_posts() ) {
                                    $query->the_post();

                        ?>
                        <div class="mui-col-md-4 inova_card">
                            <a href="<?php echo get_permalink( 16 ) . '?postid=' . get_the_ID(); ?>">
                                <div class="mui-panel">
                                    <img src="" alt="">
                                    <?php the_title(); ?>
                                </div>
                            </a>
                        </div>
                        <?php 
                                } wp_reset_postdata();
                            }
                        ?>
                    </div>
                    <div class="pagination justify-content-center">
                        <?php 
                            $big = 999999999; // need an unlikely integer

                            echo paginate_links( array(
                                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                'format'    => '?paged=%#%',
                                'current'   => max( 1, get_query_var('paged') ),
                                'total'     => $query->max_num_pages,
                                'type'      => 'list',
                            ) );
                        ?>
                    </div>
                </div>
            </div>
            <div class="mui-col-md-3"></div>
        </div>
    </div>
    <div class="mui-col-md-4" id="create_card_form">
        <form class="mui-form" method="POST">
            <legend>Tạo thiệp mới</legend>
            <div class="mui-textfield">
                <input type="text" placeholder="Tên mẫu" name="card_name">
            </div>
            <button type="submit" class="mui-btn mui-btn--primary">Submit</button>
        </form>
    </div>
<?php 
        }
    }
    get_footer();
?>