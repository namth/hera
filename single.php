<?php
get_header();
get_header('logocenter');
if (have_posts()) {
    while (have_posts()) {
        the_post();
?>
        <div id="single_post">
            <div class="mui-container">
                <div class="mui-row">
                    <div class="mui-col-md-12">
                        <div class="breadcrumb">
                            <a href="<?php echo get_bloginfo('url') . "/main/" . $uid_slug; ?>"><i class="fa fa-home" aria-hidden="true"></i></a>
                            <i class="fa fa-chevron-right"></i>
                            <?php
                            $categories = get_the_category();

                            // print_r($categories);
                            $cate_in    = array();
                            if ($categories) {
                                foreach ($categories as $cate) {
                                    $cate_in[] = $cate->term_id;
                                    echo "<a href='" . get_category_link($cate->term_id) . "'>" . $cate->name . "</a>";
                                    echo '<i class="fa fa-chevron-right"></i>';
                                }
                            }

                            the_title();
                            ?>
                        </div>
                        <div id="post_title">
                            <h1><?php the_title(); ?></h1>
                        </div>
                        <div id="post_content">
                            <?php the_content(); ?>
                        </div>
                        <div id="related_posts">
                            <h2>BÀI VIẾT LIÊN QUAN</h2>

                            <div class="mui-container">

                                <?php
                                $args   = array(
                                    'post_type'     => 'post',
                                    'posts_per_page' => 8,
                                    'post_status'   => 'publish',
                                    'category__in' => $cate_in,
                                );

                                $query = new WP_Query($args);

                                echo '<div class="mui-row">';
                                if ($query->have_posts()) {
                                    $count = 0;
                                    while ($query->have_posts()) {
                                        $query->the_post();

                                        if ($count % 4 == 0) {
                                            echo '<div class="mui-row">';
                                        }

                                        echo '<div class="mui-col-md-3 post-item">';
                                        echo '<div class="post-thumbnail">';
                                        echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail() . '</a>';
                                        echo '</div>';
                                        echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
                                        echo '</div>';

                                        if (($count + 1) % 4 == 0) {
                                            echo '</div>';
                                        }

                                        $count++;
                                    }

                                    if ($count % 4 != 0) {
                                        echo '</div>';
                                    }
                                }
                                echo '</div>';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php

    }
}
get_footer('top');
get_footer();
