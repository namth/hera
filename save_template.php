<?php 
/* 
    Template Name: Save Cards Template
*/
header("Content-Type:application/json");
$data = json_decode(file_get_contents("php://input"),true);

$assets = $data['gjs-assets'];
$components = $data['gjs-components'];
$css = $data['gjs-css'];
$html = $data['gjs-html'];
$styles = $data['gjs-styles'];

// $content = response("21", $assets, $components, $css, $html, $styles);
if (isset($_GET['id']) && $_GET['id']!="") {
    $postid = $_GET['id'];
    $response['id'] = $postid;
    $response['gjs-css'] = $css;
    $response['gjs-html'] = $html;
    $response['gjs-assets'] = $assets;
    $response['gjs-components'] = $components;
    $response['gjs-styles'] = $styles;
    # update in database
    update_plain_field('field_60e16c5bab331', 'html', $html, $postid );
    update_plain_field('field_60e16c8bab332', 'css', $css, $postid );
    update_plain_field('field_60e2a2eda1482', 'components', $components, $postid );
    update_plain_field('field_60e2a2e0a1481', 'assets', $assets, $postid );
    update_plain_field('field_60e2a2fca1483', 'styles', $styles, $postid );
}else{
    # create new posts
    $response['gjs-css'] = '404';
    $response['gjs-html'] = "Not found id";
}

echo json_encode($response);
 
