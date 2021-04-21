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
require_once 'cpt-store.php';

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

function store_locator_add_location_box() {
	add_meta_box( 
		'store_locator_sectionid',
		__( 'Store Location', 'store_locator_plugin' ),
		'store_locator_inner_custom_box',
		'store' 
		,'normal'
		,'high'
	);
}

function store_locator_inner_custom_box( $post ) { 

	$streetaddress = get_post_meta( $post->ID, 'store_locator_address', true );
	$city = get_post_meta( $post->ID, 'store_locator_city', true );
	$state = get_post_meta( $post->ID, 'store_locator_state', true );
	$zip = get_post_meta( $post->ID, 'store_locator_postal', true );
	$country = get_post_meta( $post->ID, 'store_locator_country', true );
	$lat = get_post_meta( $post->ID, 'store_locator_lat', true );
	$lng =  get_post_meta( $post->ID, 'store_locator_lng', true );
	$store_locator_google_maps_api_key = get_option('store_locator_settings')['google_maps_api_key'];

	if ( !$lng||!$lat ) {
		// Toon standaard coordinaten
		$latlng = '51.5107008, 5.7992583';
	} else {
		$latlng = $lat . ',' . $lng;
	}
	$page_data = get_page( $post->ID );
	$content = apply_filters( 'the_content', $page_data -> post_content );
	$cont = str_replace( array("\n", "\r"), ' ', esc_attr( strip_tags( @html_entity_decode( $content, ENT_QUOTES, get_option('blog_charset') ) ) ) );
	$cont = esc_html( $cont );
	$infowindow = '<h4>' . get_the_title() . '</h4>' . $streetaddress . ',<br /> ' . $city . ', ' . $state.'<br /> ' . $zip.' ' . $country.'<br /><br />' . $cont;
	$googlesearch = str_replace( "<br />", " ", $infowindow ); ?>

	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $store_locator_google_maps_api_key; ?>"></script>
	<script type="text/javascript">
	function initialize() {
		var myLatlng = new google.maps.LatLng(<?php echo $latlng; ?>);
		var myOptions = {
			zoom: 12,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}

		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		var contentString = '<?php echo $infowindow; ?>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});

		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			title: 'Location'
		});

		infowindow.open(map,marker);

	}
	</script>

	<?php wp_nonce_field( 'store_locator_nonce', 'store_locator_nonce' ); ?>

	<table width="100%" cellpadding="0" cellspacing="0" id="store-locator">
		<tr>
			<td valign="top" width="50%">
				<p>
					<label><?php _e( 'Website', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_website" name="store_locator_website" value="<?php echo get_post_meta( $post->ID, 'store_locator_website', true) ?>" class="regular-text" />
				</p>
				<p>
					<label><?php _e( 'Phone', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_phone" name="store_locator_phone" value="<?php echo get_post_meta( $post->ID, 'store_locator_phone', true) ?>" class="regular-text" />
				</p>
				<p>
					<label><?php _e( 'Street Address', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_address" name="store_locator_address" value="<?php echo get_post_meta( $post->ID, 'store_locator_address', true) ?>" class="regular-text" />
				</p>
				<p>
					<label><?php _e( 'Postal Code', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_postal" name="store_locator_postal" value="<?php echo get_post_meta( $post->ID, 'store_locator_postal', true) ?>" class="regular-text" />
				</p>
				<p>
					<label><?php _e( 'City', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_city" name="store_locator_city" value="<?php echo get_post_meta( $post->ID, 'store_locator_city', true) ?>"  class="regular-text" />
				</p>
				<p>
					<label><?php _e( 'Province / State', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_state" name="store_locator_state" value="<?php echo get_post_meta( $post->ID, 'store_locator_state', true) ?>" class="regular-text" />
				</p>
				<p>
					<label><?php _e( 'Country', 'store_locator_plugin' ); ?>:</label><br />
					<select id="store_locator_country" name="store_locator_country">
					<?php $countries_array = WC()->countries->get_countries();
					foreach ( $countries_array as $country_iso => $country_name ) {
						echo '<option value="' . $country_iso . '" ' . ( ( $country_iso == ( get_field('store_locator_country') ) ) ? 'selected="selected"' : "") . '>' . __($country_name, 'md') . '</option>';
					} ?>
					</select>
				</p>
				<p>
					<strong><?php _e( 'Google Maps Coordinates', 'store_locator_plugin' ); ?></strong>
				</p>
				<p><a href="#" id="coordinates">Get coordinates</a></p>
				<p>
					<label><?php _e( 'Latitude', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_lat" name="store_locator_lat" value="<?php echo get_post_meta( $post->ID, 'store_locator_lat', true) ?>" class="regular-text" readonly />
				</p>
				<p>
					<label><?php _e( 'Longitude', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_lng" name="store_locator_lng" value="<?php echo get_post_meta( $post->ID, 'store_locator_lng', true) ?>" class="regular-text"  readonly />
				</p>
			</td>
			<td valign="top" width="50%">
				<div id="map_canvas" style="width: 100%; height: 450px; position: relative; margin-top: 10px;">
					<script type="text/javascript">
						initialize();
					</script>
				</div>
			</td>
		</tr>
	</table>

<?php
}

function store_locator_save_postdata( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;

	if ( ! isset( $_POST['store_locator_nonce'] ) || ! wp_verify_nonce( $_POST['store_locator_nonce'], 'store_locator_nonce' ) )
		return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}

	$storephone = $_POST['store_locator_phone'];
	$storecode = $_POST['store_locator_website'];
	$storeaddress = $_POST['store_locator_address'];
	$storecity = $_POST['store_locator_city'];
	$storestate = $_POST['store_locator_state'];
	$storecountry = $_POST['store_locator_country'];
	$storepostal = $_POST['store_locator_postal'];
	$storelat = $_POST['store_locator_lat'];
	$storelng = $_POST['store_locator_lng'];

	update_post_meta( $post_id , 'store_locator_phone', $storephone );
	update_post_meta( $post_id , 'store_locator_website', $storecode );
	update_post_meta( $post_id , 'store_locator_address', $storeaddress );
	update_post_meta( $post_id , 'store_locator_city', $storecity );
	update_post_meta( $post_id , 'store_locator_state', $storestate );
	update_post_meta( $post_id , 'store_locator_country', $storecountry );
	update_post_meta( $post_id , 'store_locator_postal', $storepostal );
	update_post_meta( $post_id , 'store_locator_lat', $storelat );
	update_post_meta( $post_id , 'store_locator_lng', $storelng );

}

//Shortcode
function showstorelocator_shortcode( $atts ) {
	extract(shortcode_atts(array(
		'width' => '650',
		'height' => '500',
	), $atts));
	$storecode ='<iframe name="storelocator" src ="' . plugins_url() . '/tww-store-locator/files/store-locator.php?height=' . $height . '" width="' . $width . 'px" height="' . $height . 'px" scrolling="no" frameborder="0" ></iframe>';
	return $storecode;
}
add_shortcode('storelocator', 'showstorelocator_shortcode');