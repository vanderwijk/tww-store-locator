<?php

function store_locator_add_admin_menu() { 
	add_options_page(
		'Store Locator Settings', // page title
		'Store Locator', // menu title
		'manage_options', // capability
		'store-locator-settings', // menu slug
		'store_locator_options_page', // function
		'dashicons-list-view', // icon
		null // position
	);
}
add_action( 'admin_menu', 'store_locator_add_admin_menu' );

function store_locator_settings_init() { 

	add_settings_field( 
		'google_maps_api_key', 
		__( 'Google', 'store_locator' ), 
		'store_locator_google_maps_api_key_render', 
		'api_keys', 
		'store_locator_api_keys_section' 
	);

	add_settings_field(
		'google_maps_map_id',
		__( 'Google Map ID', 'store_locator' ),
		'store_locator_google_maps_map_id_render',
		'api_keys',
		'store_locator_api_keys_section'
	);

	add_settings_field( 
		'store_locator_postmark_api_key', 
		__( 'Postmark', 'store_locator' ), 
		'store_locator_postmark_api_key_render', 
		'api_keys', 
		'store_locator_api_keys_section' 
	);

	// used for displaying API key fields
	register_setting( 'api_keys', 'store_locator_settings' );

	add_settings_section(
		'store_locator_api_keys_section', 
		__( 'API Keys', 'store_locator' ), 
		'store_locator_api_keys_section_callback', 
		'api_keys'
	);
	

}
add_action( 'admin_init', 'store_locator_settings_init' );

function store_locator_postmark_api_key_render() {
	$store_locator_settings = get_option( 'store_locator_settings' );
	$value = isset($store_locator_settings['store_locator_postmark_api_key']) ? $store_locator_settings['store_locator_postmark_api_key'] : '';
	echo '<input type="text" class="regular-text" name="store_locator_settings[store_locator_postmark_api_key]" value="' . esc_attr($value) . '">';
}

function store_locator_google_maps_api_key_render() {
	$store_locator_settings = get_option( 'store_locator_settings' );
	$value = isset($store_locator_settings['google_maps_api_key']) ? $store_locator_settings['google_maps_api_key'] : '';
	echo '<input type="text" class="regular-text" name="store_locator_settings[google_maps_api_key]" value="' . esc_attr($value) . '">';
}

function store_locator_google_maps_map_id_render() {
	$store_locator_settings = get_option( 'store_locator_settings' );
	$value = isset($store_locator_settings['google_maps_map_id']) ? $store_locator_settings['google_maps_map_id'] : '';
	echo '<input type="text" class="regular-text" name="store_locator_settings[google_maps_map_id]" value="' . esc_attr($value) . '">';
}

function store_locator_api_keys_section_callback() { 
	echo __( 'These API keys are used for connecting to external services. Stored here to keep them out of GitHub.', 'store_locator' );
}

function store_locator_options_page() { ?>
<div class="wrap">
	<form action='options.php' method='post'>
		<h1><?php _e('Store Locator Settings','store_locator'); ?></h1>

		<?php
			do_settings_sections( 'api_keys' );
			settings_fields( 'api_keys' );

			submit_button();
		?>

	</form>
</div>
	<?php

} ?>
