<?php 
add_action('rest_api_init', function(){
    # casso endpoint
    register_rest_route('hera/v1/', 'momo_endpoint', array(
        'methods'   => 'POST',
        'callback'  => 'momo_endpoint',
    ));
});

function momo_endpoint(WP_REST_Request $request) {
    $json_result = json_decode($request->get_body());

    update_field('field_6394b76112cf9', $json_result, 308);
}