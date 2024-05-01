<?php

get_header();
get_header('logocenter');
?>

<div id="single_post">
    <div class="mui-container">
        <div class="mui-row" id="related_posts">
            <?php 
            
            the_archive_title( '<h1 class="page-title">', '</h1>' ); 
            the_archive_description( '<div class="taxonomy-description">', '</div>' );

            if (have_posts()) {
                while (have_posts()) {
                    the_post();

                    echo '<div class="mui-col-md-4 post-item">';
                    echo    get_the_post_thumbnail();
                    echo    '<h3>' . get_the_title() . '</h3>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>

<?php
get_footer('top');
get_footer();
