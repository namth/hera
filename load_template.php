<?php 
/* 
    Template Name: Load Cards Template
*/
header("Content-Type:application/json");
if (isset($_GET['id']) && $_GET['id']!="") {
    # read in db
    $postid = $_GET['id'];
    $response['id'] = $postid;
    $response['gjs-html'] = get_field('html', $postid);
    $response['gjs-css'] = get_field('css', $postid);
    $response['gjs-assets'] = get_field('assets', $postid);
    $response['gjs-components'] = get_field('components', $postid);
    $response['gjs-styles'] = get_field('styles', $postid);
    
}else{
    $response['id'] = NULL;
    $response['gjs-assets'] = NULL;
    $response['gjs-components'] = 200;
    $response['gjs-css'] = "No found data";
    
}

$json_response = json_encode($response);
echo $json_response;

