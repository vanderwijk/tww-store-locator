<?php function store_locator_add_location_box() {
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
	$country_array = get_the_terms( $post->ID, 'country' );
	$country = '';
	if ( is_array( $country_array ) && ! empty( $country_array ) ) {
		$first_country = reset( $country_array );
		if ( $first_country && isset( $first_country->name ) ) {
			$country = $first_country->name;
		}
	}

	$lat = get_post_meta( $post->ID, 'store_locator_lat', true );
	$lng =  get_post_meta( $post->ID, 'store_locator_lng', true );
	$store_locator_settings = get_option('store_locator_settings');
	$store_locator_google_maps_api_key = isset($store_locator_settings['google_maps_api_key']) ? $store_locator_settings['google_maps_api_key'] : '';
	$store_locator_google_maps_map_id = empty($store_locator_settings['google_maps_map_id']) ? 'c62305ec4f432eb' : $store_locator_settings['google_maps_map_id'];

	if ( !$lng||!$lat ) {
		// Toon standaard coordinaten
		$latlng = '51.5107008, 5.7992583';
	} else {
		$latlng = $lat . ',' . $lng;
	}

	$infowindow = '<h4>' . get_the_title() . '</h4>' . $streetaddress . '<br />' . $zip . '<br />' . $state . '<br />' . $city . '<br />' . $country . '<br />';
	?>

	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $store_locator_google_maps_api_key; ?>&libraries=marker&loading=async&callback=initialize" defer></script>
	<script type="text/javascript">
	function initialize() {
		var myLatlng = new google.maps.LatLng(<?php echo $latlng; ?>);
		var myOptions = {
			zoom: 12,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var mapId = <?php echo wp_json_encode($store_locator_google_maps_map_id); ?>;
		if (mapId) {
			myOptions.mapId = mapId;
		}

		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		var contentString = '<?php echo addslashes($infowindow); ?>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});

		var marker;
		var canUseAdvancedMarker = Boolean(
			myOptions.mapId &&
			google.maps.marker &&
			google.maps.marker.AdvancedMarkerElement
		);

		if (canUseAdvancedMarker) {
			marker = new google.maps.marker.AdvancedMarkerElement({
				position: myLatlng,
				map: map,
				title: 'Location'
			});
		} else {
			marker = new google.maps.Marker({
				position: myLatlng,
				map: map,
				title: 'Location'
			});
		}

		infowindow.open({
			anchor: marker,
			map: map
		});

	}
	</script>

	<?php wp_nonce_field( 'store_locator_nonce', 'store_locator_nonce' ); ?>

	<table width="100%" cellpadding="0" cellspacing="0" id="store-locator">
		<tr>
			<td valign="top" width="50%">
				<p>
					<label><?php _e( 'Street Address', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_address" name="store_locator_address" value="<?php echo get_post_meta( $post->ID, 'store_locator_address', true) ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'Postal Code', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_postal" name="store_locator_postal" value="<?php echo get_post_meta( $post->ID, 'store_locator_postal', true) ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'City', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_city" name="store_locator_city" value="<?php echo get_post_meta( $post->ID, 'store_locator_city', true) ?>"  class="all-options" />
				</p>
				<p>
					<label><?php _e( 'Province / State', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_state" name="store_locator_state" value="<?php echo get_post_meta( $post->ID, 'store_locator_state', true) ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'Country', 'store_locator_plugin' ); ?>:</label><br />
					<select id="store_locator_country" name="store_locator_country">
						<?php $countries = get_terms( 'country', array(
							'orderby' => 'name',
							'order' => 'ASC',
							'hide_empty' => false,
						));
						foreach ($countries as $country) {
							echo '<option value="' . $country->term_id . '" ' . ( ( has_term($country->term_id, 'country'))  ? 'selected="selected"' : "") . '>' . __($country->name, 'md') . '</option>';
						} ?>
					</select>
				</p>
				<p>
					<label><?php _e( 'Phone', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_phone" name="store_locator_phone" value="<?php echo get_post_meta( $post->ID, 'store_locator_phone', true) ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'Website', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_website" name="store_locator_website" value="<?php echo get_post_meta( $post->ID, 'store_locator_website', true) ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'E-mail', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_email" name="store_locator_email" value="<?php echo get_post_meta( $post->ID, 'store_locator_email', true) ?>" class="all-options" />
				</p>
				<p>
					<strong><?php _e( 'Google Maps Coordinates', 'store_locator_plugin' ); ?></strong>
				</p>
				<p><a href="#" id="coordinates">Get coordinates</a></p>
				<p>
					<label><?php _e( 'Latitude', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_lat" name="store_locator_lat" value="<?php echo get_post_meta( $post->ID, 'store_locator_lat', true) ?>" class="all-options" readonly />
				</p>
				<p>
					<label><?php _e( 'Longitude', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_lng" name="store_locator_lng" value="<?php echo get_post_meta( $post->ID, 'store_locator_lng', true) ?>" class="all-options"  readonly />
				</p>
			</td>
			<td valign="top" width="50%">
					<div id="map_canvas" style="width: 100%; height: 450px; position: relative; margin-top: 10px;"></div>
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
	$storewebsite = $_POST['store_locator_website'];
	$storeemail = $_POST['store_locator_email'];
	$storeaddress = $_POST['store_locator_address'];
	$storecity = $_POST['store_locator_city'];
	$storestate = $_POST['store_locator_state'];
	$storecountry = $_POST['store_locator_country'];
	$storepostal = $_POST['store_locator_postal'];
	$storelat = $_POST['store_locator_lat'];
	$storelng = $_POST['store_locator_lng'];

	update_post_meta( $post_id , 'store_locator_phone', $storephone );
	update_post_meta( $post_id , 'store_locator_website', $storewebsite );
	update_post_meta( $post_id , 'store_locator_email', $storeemail );
	update_post_meta( $post_id , 'store_locator_address', $storeaddress );
	update_post_meta( $post_id , 'store_locator_city', $storecity );
	update_post_meta( $post_id , 'store_locator_state', $storestate );
	update_post_meta( $post_id , 'store_locator_country', $storecountry );
	update_post_meta( $post_id , 'store_locator_postal', $storepostal );
	update_post_meta( $post_id , 'store_locator_lat', $storelat );
	update_post_meta( $post_id , 'store_locator_lng', $storelng );

	$taxonomy = 'country';
	$term = get_term_by('ID', $storecountry, 'country');
	$term_name = $term->name;
	wp_set_object_terms($post_id, $term_name, $taxonomy);

}
