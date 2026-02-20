<?php 
/*
Plugin Name: The Web Works Store Locator 
Plugin URI: http://vanderwijk.nl
Description: Store Locator Plugin that lets you embed a Google Maps powered store locator. To embed the store locator, use the following shortcut: [storelocator] . To specify width and height [storelocator width="500" height="500"]
Version: 1.0.1
Author: Johan van der Wijk
Author URI: http://vanderwijk.nl
*/

// Launch the plugin.
add_action( 'plugins_loaded', 'store_locator_plugin_init' );

define('STORE_LOCATOR_PLUGIN_URL', plugins_url() . '/tww-store-locator/' );
define('STORE_LOCATOR_PLUGIN_VER', '1.0');

// Load the required files needed for the plugin to run in the proper order and add needed functions to the required hooks.
function store_locator_plugin_init() {
	load_plugin_textdomain( 'store_locator_plugin', false, 'tww-store-locator/languages' );
	add_action( 'add_meta_boxes', 'store_locator_add_location_box' );
	add_action( 'save_post', 'store_locator_save_postdata' );
}

require_once 'settings.php';
require_once 'custom-post-type.php';
require_once 'taxonomy.php';
require_once 'meta-box.php';
require_once 'shortcode.php';

function store_locator_enqueues() {
	$style_path = plugin_dir_path( __FILE__ ) . 'files/style.css';
	$style_ver = file_exists( $style_path ) ? filemtime( $style_path ) : STORE_LOCATOR_PLUGIN_VER;
	wp_enqueue_style( 'store_locator', STORE_LOCATOR_PLUGIN_URL . 'files/style.css', '', $style_ver, 'screen');
}
add_action( 'wp_enqueue_scripts', 'store_locator_enqueues' );

function store_locator_admin_enqueues() {
	$script_path = plugin_dir_path( __FILE__ ) . 'files/store-locator.js';
	$script_ver = file_exists( $script_path ) ? filemtime( $script_path ) : STORE_LOCATOR_PLUGIN_VER;
	wp_enqueue_script( 'store_locator', STORE_LOCATOR_PLUGIN_URL . 'files/store-locator.js', array( 'jquery' ), $script_ver, true );
}
add_action( 'admin_enqueue_scripts', 'store_locator_admin_enqueues' );