<?php 
/*
Plugin Name: The Web Works Store Locator 
Plugin URI: http://vanderwijk.nl
Description: Store Locator Plugin that lets you embed a Google Maps powered store locator. To embed the store locator, use the following shortcut: [storelocator] . To specify width and height [storelocator width="500" height="500"]
Version: 1.0
Author: Johan van der Wijk
Author URI: http://vanderwijk.nl
*/

// Launch the plugin.
add_action( 'plugins_loaded', 'store_locator_plugin_init' );

define('STORE_LOCATOR_PLUGIN_URL', plugins_url() . '/tww-store-locator/' );
define('STORE_LOCATOR_PLUGIN_VER', '1.0');

// Load the required files needed for the plugin to run in the proper order and add needed functions to the required hooks.
function store_locator_plugin_init() {
	// Load the translation of the plugin.
	load_plugin_textdomain( 'store_locator_plugin', false, 'tww-store-locator/languages' );
	add_action( 'add_meta_boxes', 'store_locator_add_location_box' );
	add_action( 'save_post', 'store_locator_save_postdata' );
}

require_once 'settings.php';
require_once 'custom-post-type.php';
require_once 'meta-box.php';
require_once 'shortcode.php';

function store_locator_enqueues() {

	wp_enqueue_script( 'store_locator', STORE_LOCATOR_PLUGIN_URL . 'files/store-locator.js', array( 'jquery' ), STORE_LOCATOR_PLUGIN_VER, true );

	if ( empty( get_option('store_locator_settings')['google_maps_api_key'])) {
		$store_locator_google_maps_api_key = NULL; 
	} else { 
		$store_locator_google_maps_api_key = get_option('store_locator_settings')['google_maps_api_key'];
	}

	$scriptData = array(
		'store_locator_google_maps_api_key' => $store_locator_google_maps_api_key
	);

	wp_localize_script( 'store_locator', 'store_locator_options', $scriptData );
}
add_action( 'admin_enqueue_scripts', 'store_locator_enqueues' );