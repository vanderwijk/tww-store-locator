<?php
// Register Store Custom Post Type
function store() {

	$labels = array(
		'name'                => _x( 'Stores', 'Post Type General Name', 'store-locator' ),
		'singular_name'       => _x( 'Store', 'Post Type Singular Name', 'store-locator' ),
		'menu_name'           => __( 'Stores', 'store-locator' ),
		'parent_item_colon'   => __( 'Parent Store:', 'store-locator' ),
		'all_items'           => __( 'All Stores', 'store-locator' ),
		'view_item'           => __( 'View Store', 'store-locator' ),
		'add_new_item'        => __( 'Add New Store', 'store-locator' ),
		'add_new'             => __( 'Add New', 'store-locator' ),
		'edit_item'           => __( 'Edit Store', 'store-locator' ),
		'update_item'         => __( 'Update Store', 'store-locator' ),
		'search_items'        => __( 'Search Store', 'store-locator' ),
		'not_found'           => __( 'Not found', 'store-locator' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'store-locator' ),
	);
	$args = array(
		'label'               => __( 'store', 'store-locator' ),
		'description'         => __( 'Stores', 'store-locator' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-book',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'rewrite'             => array( 'slug' => __('store', 'store-locator') )
	);
	register_post_type( 'store', $args );

}

// Hook into the 'init' action
add_action( 'init', 'store', 0 );
