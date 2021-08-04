<?php 
// Register Custom Taxonomy
function country() {

	$labels = array(
		'name'                       => _x( 'Country', 'Taxonomy General Name', 'twwsl' ),
		'singular_name'              => _x( 'Country', 'Taxonomy Singular Name', 'twwsl' ),
		'menu_name'                  => __( 'Countries', 'twwsl' ),
		'all_items'                  => __( 'All Countries', 'twwsl' ),
		'parent_item'                => __( 'Parent Country', 'twwsl' ),
		'parent_item_colon'          => __( 'Parent Country:', 'twwsl' ),
		'new_item_name'              => __( 'New Country Name', 'twwsl' ),
		'add_new_item'               => __( 'Add New Country', 'twwsl' ),
		'edit_item'                  => __( 'Edit Country', 'twwsl' ),
		'update_item'                => __( 'Update Country', 'twwsl' ),
		'view_item'                  => __( 'View Country', 'twwsl' ),
		'separate_items_with_commas' => __( 'Separate countries with commas', 'twwsl' ),
		'add_or_remove_items'        => __( 'Add or remove countries', 'twwsl' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'twwsl' ),
		'popular_items'              => __( 'Popular Countries', 'twwsl' ),
		'search_items'               => __( 'Search Countries', 'twwsl' ),
		'not_found'                  => __( 'Not Found', 'twwsl' ),
		'no_terms'                   => __( 'No countries', 'twwsl' ),
		'items_list'                 => __( 'Countries list', 'twwsl' ),
		'items_list_navigation'      => __( 'Countries list navigation', 'twwsl' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'country', array( 'store' ), $args );

}
add_action( 'init', 'country', 0 );

function collection() {

	$labels = array(
		'name'                       => _x( 'Collection', 'Taxonomy General Name', 'twwsl' ),
		'singular_name'              => _x( 'Collection', 'Taxonomy Singular Name', 'twwsl' ),
		'menu_name'                  => __( 'Collections', 'twwsl' ),
		'all_items'                  => __( 'All Collections', 'twwsl' ),
		'parent_item'                => __( 'Parent Collection', 'twwsl' ),
		'parent_item_colon'          => __( 'Parent Collection:', 'twwsl' ),
		'new_item_name'              => __( 'New Collection Name', 'twwsl' ),
		'add_new_item'               => __( 'Add New Collection', 'twwsl' ),
		'edit_item'                  => __( 'Edit Collection', 'twwsl' ),
		'update_item'                => __( 'Update Collection', 'twwsl' ),
		'view_item'                  => __( 'View Collection', 'twwsl' ),
		'separate_items_with_commas' => __( 'Separate collections with commas', 'twwsl' ),
		'add_or_remove_items'        => __( 'Add or remove collections', 'twwsl' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'twwsl' ),
		'popular_items'              => __( 'Popular Collections', 'twwsl' ),
		'search_items'               => __( 'Search Collections', 'twwsl' ),
		'not_found'                  => __( 'Not Found', 'twwsl' ),
		'no_terms'                   => __( 'No collections', 'twwsl' ),
		'items_list'                 => __( 'Collections list', 'twwsl' ),
		'items_list_navigation'      => __( 'Collections list navigation', 'twwsl' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'collection', array( 'store' ), $args );

}
add_action( 'init', 'collection', 0 );