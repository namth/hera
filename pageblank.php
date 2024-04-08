<?php
/* 
* Template Name: Blank no sidebar
*/

get_header();
get_header('logocenter');
if (have_posts()) {
    while (have_posts()) {
        the_post();

        echo "<div id='front_main_content'>";
        the_content();
        echo "</div>";

        get_footer();
    }
}