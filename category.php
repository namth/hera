<?php

get_header();
get_header('logocenter');
?>

<div id="single_post">
    <div class="mui-container">
        <div id="related_posts">
            <?php 
            
            /**
             * Renders the category archive page.
             * 
             * This code displays the title and description of the category archive, followed by a grid of post items.
             * Each post item consists of a thumbnail, title, and a link to the post.
             * The posts are displayed in rows of three, with each row wrapped in a div element.
             * 
             * @since 1.0.0
             */

            the_archive_title( '<h1 class="page-title">', '</h1>' ); 
            the_archive_description( '<div class="taxonomy-description">', '</div>' );

            echo '<div class="mui-row">';
            if (have_posts()) {
                $count = 0;
                while (have_posts()) {
                    the_post();

                    if ($count % 3 == 0) {
                        echo '<div class="mui-row">';
                    }

                    echo '<div class="mui-col-md-4 post-item">';
                    echo '<div class="post-thumbnail">';
                    echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail() . '</a>';
                    echo '</div>';
                    echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                    echo '</div>';

                    if (($count + 1) % 3 == 0) {
                        echo '</div>';
                    }

                    $count++;
                }

                if ($count % 3 != 0) {
                    echo '</div>';
                }
            }
            echo '</div>';
            ?>
        </div>
    </div>
</div>

<?php
get_footer('top');
get_footer();
