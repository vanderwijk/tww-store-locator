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
	$lng = get_post_meta( $post->ID, 'store_locator_lng', true );
	$store_locator_settings = get_option('store_locator_settings');
	$store_locator_google_maps_api_key = isset($store_locator_settings['google_maps_api_key']) ? $store_locator_settings['google_maps_api_key'] : '';
	$store_locator_google_maps_map_id = empty($store_locator_settings['google_maps_map_id']) ? 'c62305ec4f432eb' : $store_locator_settings['google_maps_map_id'];

	if ( ! is_numeric( $lat ) || ! is_numeric( $lng ) ) {
		// Toon standaard coordinaten
		$map_lat = 51.5107008;
		$map_lng = 5.7992583;
	} else {
		$map_lat = (float) $lat;
		$map_lng = (float) $lng;
	}

	$info_lines = array();
	$info_lines[] = '<h4>' . esc_html( get_the_title() ) . '</h4>';
	if ( ! empty( $streetaddress ) ) {
		$info_lines[] = esc_html( $streetaddress );
	}
	if ( ! empty( $zip ) ) {
		$info_lines[] = esc_html( $zip );
	}
	if ( ! empty( $state ) ) {
		$info_lines[] = esc_html( $state );
	}
	if ( ! empty( $city ) ) {
		$info_lines[] = esc_html( $city );
	}
	if ( ! empty( $country ) ) {
		$info_lines[] = esc_html( $country );
	}
	$infowindow = implode( '<br />', $info_lines );
	?>

	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo rawurlencode( $store_locator_google_maps_api_key ); ?>&libraries=marker&loading=async&callback=initialize" defer></script>
	<script type="text/javascript">
	function initialize() {
		var myLatlng = new google.maps.LatLng(<?php echo wp_json_encode( $map_lat ); ?>, <?php echo wp_json_encode( $map_lng ); ?>);
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

		var contentString = <?php echo wp_json_encode( $infowindow ); ?>;

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
					<input type="text" id="store_locator_address" name="store_locator_address" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_address', true ) ); ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'Postal Code', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_postal" name="store_locator_postal" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_postal', true ) ); ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'City', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_city" name="store_locator_city" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_city', true ) ); ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'Province / State', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_state" name="store_locator_state" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_state', true ) ); ?>" class="all-options" />
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
							echo '<option value="' . esc_attr( $country->term_id ) . '" ' . ( ( has_term($country->term_id, 'country'))  ? 'selected="selected"' : "") . '>' . esc_html( $country->name ) . '</option>';
						} ?>
					</select>
				</p>
				<p>
					<label><?php _e( 'Phone', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_phone" name="store_locator_phone" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_phone', true ) ); ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'Website', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_website" name="store_locator_website" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_website', true ) ); ?>" class="all-options" />
				</p>
				<p>
					<label><?php _e( 'E-mail', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_email" name="store_locator_email" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_email', true ) ); ?>" class="all-options" />
				</p>
				<p>
					<strong><?php _e( 'Google Maps Coordinates', 'store_locator_plugin' ); ?></strong>
				</p>
				<p><a href="#" id="coordinates">Get coordinates</a></p>
				<p>
					<label><?php _e( 'Latitude', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_lat" name="store_locator_lat" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_lat', true ) ); ?>" class="all-options" readonly />
				</p>
				<p>
					<label><?php _e( 'Longitude', 'store_locator_plugin' ); ?>:</label><br />
					<input type="text" id="store_locator_lng" name="store_locator_lng" value="<?php echo esc_attr( get_post_meta( $post->ID, 'store_locator_lng', true ) ); ?>" class="all-options" readonly />
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

	if ( ! isset( $_POST['post_type'] ) ) {
		return;
	}

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}

	$storephone = isset( $_POST['store_locator_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['store_locator_phone'] ) ) : '';
	$storewebsite = isset( $_POST['store_locator_website'] ) ? esc_url_raw( wp_unslash( $_POST['store_locator_website'] ) ) : '';
	$storeemail = isset( $_POST['store_locator_email'] ) ? sanitize_email( wp_unslash( $_POST['store_locator_email'] ) ) : '';
	$storeaddress = isset( $_POST['store_locator_address'] ) ? sanitize_text_field( wp_unslash( $_POST['store_locator_address'] ) ) : '';
	$storecity = isset( $_POST['store_locator_city'] ) ? sanitize_text_field( wp_unslash( $_POST['store_locator_city'] ) ) : '';
	$storestate = isset( $_POST['store_locator_state'] ) ? sanitize_text_field( wp_unslash( $_POST['store_locator_state'] ) ) : '';
	$storecountry = isset( $_POST['store_locator_country'] ) ? absint( $_POST['store_locator_country'] ) : 0;
	$storepostal = isset( $_POST['store_locator_postal'] ) ? sanitize_text_field( wp_unslash( $_POST['store_locator_postal'] ) ) : '';
	$storelat = isset( $_POST['store_locator_lat'] ) ? filter_var( wp_unslash( $_POST['store_locator_lat'] ), FILTER_VALIDATE_FLOAT ) : false;
	$storelng = isset( $_POST['store_locator_lng'] ) ? filter_var( wp_unslash( $_POST['store_locator_lng'] ), FILTER_VALIDATE_FLOAT ) : false;

	update_post_meta( $post_id , 'store_locator_phone', $storephone );
	update_post_meta( $post_id , 'store_locator_website', $storewebsite );
	update_post_meta( $post_id , 'store_locator_email', $storeemail );
	update_post_meta( $post_id , 'store_locator_address', $storeaddress );
	update_post_meta( $post_id , 'store_locator_city', $storecity );
	update_post_meta( $post_id , 'store_locator_state', $storestate );
	update_post_meta( $post_id , 'store_locator_country', $storecountry );
	update_post_meta( $post_id , 'store_locator_postal', $storepostal );
	if ( false !== $storelat ) {
		update_post_meta( $post_id , 'store_locator_lat', (string) $storelat );
	}
	if ( false !== $storelng ) {
		update_post_meta( $post_id , 'store_locator_lng', (string) $storelng );
	}

	$taxonomy = 'country';
	if ( $storecountry > 0 ) {
		$term = get_term_by( 'ID', $storecountry, 'country' );
		if ( $term && ! is_wp_error( $term ) ) {
			wp_set_object_terms( $post_id, (int) $storecountry, $taxonomy );
		}
	}

}
