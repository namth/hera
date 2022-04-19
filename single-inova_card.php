<?php 
    if( have_posts() ) {
        while ( have_posts() ) {
            the_post();

            $html = get_field('html');
            $css = '<style type="text/css">' . get_field('css') . '</style>';
        } 
    }
    print_r($css);
    print_r($html);
?>

