<?php
function cptui_register_my_cpts() {

	/**
	 * Post Type: Thiệp mời.
	 */

	$labels = [
		"name" => esc_html__( "Thiệp mời", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Thiệp mời", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Thiệp mời", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "thiep_moi", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt" ],
		"taxonomies" => [ "category", "post_tag" ],
		"show_in_graphql" => false,
	];

	register_post_type( "thiep_moi", $args );

	/**
	 * Post Type: Đơn hàng.
	 */

	$labels = [
		"name" => esc_html__( "Đơn hàng", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Đơn hàng", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Đơn hàng", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "inova_order", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "category" ],
		"show_in_graphql" => false,
	];

	register_post_type( "inova_order", $args );

	/**
	 * Post Type: Mã giảm giá.
	 */

	$labels = [
		"name" => esc_html__( "Mã giảm giá", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Mã giảm giá", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Mã giảm giá", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "coupon", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "category", "post_tag" ],
		"show_in_graphql" => false,
	];

	register_post_type( "coupon", $args );

	/**
	 * Post Type: Gói sản phẩm.
	 */

	$labels = [
		"name" => esc_html__( "Gói sản phẩm", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Gói sản phẩm", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Gói sản phẩm", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "package", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "package", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
