<?php
/* 
* Template Name: Blank no sidebar
*/

get_header();
get_header('logocenter');
if (have_posts()) {
    while (have_posts()) {
        the_post();

        the_content();

        get_footer();
    }
}