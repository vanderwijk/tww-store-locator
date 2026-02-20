<?php

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// shortcode
function showstorelocator_shortcode() {

	$store_locator_settings = get_option('store_locator_settings');
	$store_locator_google_maps_api_key = empty($store_locator_settings['google_maps_api_key']) ? '' : $store_locator_settings['google_maps_api_key'];
	$store_locator_google_maps_map_id = empty($store_locator_settings['google_maps_map_id']) ? 'c62305ec4f432eb' : $store_locator_settings['google_maps_map_id'];

	ob_start();

	echo "<div id='map_canvas'></div>
	<script src='https://maps.googleapis.com/maps/api/js?key=" . rawurlencode($store_locator_google_maps_api_key) .  "&libraries=marker&loading=async&callback=storeLocatorInit' type='text/javascript' async defer></script>
	<script>
	var locations = [";

		$countries = get_terms( 'country', array(
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => true,
		));

		foreach ($countries as $country) {

			$args = array(
				'post_type' => 'store',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'ignore_sticky_posts'=> true,
				'meta_key' => 'store_locator_city',
				'orderby' => 'meta_value',
				'order' => 'ASC',
				'tax_query' => array(
					array(
						'taxonomy' => 'country',
						'terms' => $country->term_id
					)
				)

			);
			$store_query = null;
			$store_query = new WP_Query($args);

				if ( $store_query -> have_posts() ) {

					while ($store_query -> have_posts()) : $store_query -> the_post();

						$meta = get_post_meta(get_the_ID());
						$collection_array = get_the_terms( get_the_ID(), 'collection' );
						$collection_names = array();
						if ( is_array( $collection_array ) ) {
							foreach ( $collection_array as $collection ) {
								$collection_names[] = $collection->name;
							}
						}

						$website_url = ! empty( $meta['store_locator_website'][0] ) ? $meta['store_locator_website'][0] : '';
						$website_host = $website_url ? parse_url( $website_url, PHP_URL_HOST ) : '';

						$popup_items = array();
						if ( ! empty( $meta['store_locator_city'][0] ) ) {
							$popup_items[] = '<div>' . esc_html( $meta['store_locator_city'][0] ) . '</div>';
						}
						if ( ! empty( $meta['store_locator_address'][0] ) ) {
							$popup_items[] = '<div>' . esc_html( $meta['store_locator_address'][0] ) . '</div>';
						}
						if ( ! empty( $meta['store_locator_phone'][0] ) ) {
							$popup_items[] = '<div>' . esc_html( $meta['store_locator_phone'][0] ) . '</div>';
						}
						if ( ! empty( $meta['store_locator_email'][0] ) ) {
							$email = antispambot( $meta['store_locator_email'][0] );
							$popup_items[] = '<div><a href=\"mailto:' . esc_attr( $email ) . '\">' . esc_html( $email ) . '</a></div>';
						}
						if ( $website_url ) {
							$popup_items[] = '<div><a href=\"' . esc_url( $website_url ) . '\" rel=\"external\">' . esc_html( $website_host ) . '</a></div>';
						}
						if ( ! empty( $collection_names ) ) {
							$popup_items[] = '<div>' . esc_html( implode( ', ', $collection_names ) ) . '</div>';
						}

						$popup_html = '<div class="store-popup"><div class="store-popup-name"><strong>' . esc_html( get_the_title() ) . '</strong></div>' . implode( '', $popup_items ) . '</div>';

						if ( empty( $meta['store_locator_lat'][0] ) || empty( $meta['store_locator_lng'][0] ) ) {
							continue;
						}

						echo '{ ';

							echo 'shop: ' . wp_json_encode( get_the_title() ) . ', ';
							echo 'popupHtml: ' . wp_json_encode( $popup_html ) . ', ';
							echo 'lat: ' . floatval( $meta['store_locator_lat'][0] ) . ', ';
							echo 'lng: ' . floatval( $meta['store_locator_lng'][0] );

						echo ' }, ';

				endwhile;
			}
		}

		wp_reset_query();

	echo "]
	var map;
	var markers = [];
	var infoWindows = [];
	var mapId = " . wp_json_encode($store_locator_google_maps_map_id) . ";
		
	function storeLocatorInit(){

		var mapOptions = {
			zoom: 3,
			center: new google.maps.LatLng(45, -15),
			mapTypeId: 'roadmap',
			streetViewControl: false
		};
		if (mapId) {
			mapOptions.mapId = mapId;
		}
		map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		var useAdvancedMarkers = Boolean(
			mapOptions.mapId &&
			google.maps.marker &&
			google.maps.marker.AdvancedMarkerElement
		);
		var openInfoWindowForMarker = function(marker) {
			for (var i=0;i<infoWindows.length;i++) {
				infoWindows[i].close();
			}

			var position = marker.position || (marker.getPosition ? marker.getPosition() : null);
			if (position) {
				map.panTo(position);
			}

			var infowindow = new google.maps.InfoWindow({
				content: marker.storeLocatorHtml,
				position: position,
			});

			infoWindows.push(infowindow);
			infowindow.open({
				anchor: marker,
				map: map
			});
		};

		var num_markers = locations.length;

		for (var i = 0; i < num_markers; i++) {
			var markerPosition = {
				lat: locations[i].lat,
				lng: locations[i].lng
			};

				if (useAdvancedMarkers) {
					markers[i] = new google.maps.marker.AdvancedMarkerElement({
						position: markerPosition,
						map: map,
						title: locations[i].shop,
						gmpClickable: true
					});
				} else {
				markers[i] = new google.maps.Marker({
					position: markerPosition,
					map: map,
					title: locations[i].shop
				});
				}
				markers[i].storeLocatorHtml = locations[i].popupHtml || '';
				if (useAdvancedMarkers) {
					markers[i].addEventListener('gmp-click', (function(marker) {
						return function() {
							openInfoWindowForMarker(marker);
						};
					})(markers[i]));
				} else {
					markers[i].addListener('click', (function(marker) {
						return function() {
							openInfoWindowForMarker(marker);
						};
					})(markers[i]));
				}
			}
		map.addListener('click', function() {
			for (var i=0;i<infoWindows.length;i++) {
				infoWindows[i].close();
			}
		});
			// Marker clustering removed to avoid third-party runtime dependency.
		}
		// Initialized by the Google Maps callback (storeLocatorInit).
	
	</script>";

	$contents = ob_get_clean();
	return $contents;

}
add_shortcode('storelocator', 'showstorelocator_shortcode');


function showstorelist_shortcode() {

	ob_start();

	echo '<div class="store-list">';

	$countries = get_terms( 'country', array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => true,
	));

	foreach ($countries as $country) {

		$args = array(
			'post_type' => 'store',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'ignore_sticky_posts'=> true,
			'meta_key' => 'store_locator_city',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'country',
					'terms' => $country->term_id
				)
			)
		);
		$store_query = null;
		$store_query = new WP_Query($args);

			if ( $store_query -> have_posts() ) {

				echo '<h5>' . esc_html( $country->name ) . '</h5>';

			while ($store_query -> have_posts()) : $store_query -> the_post();

				$meta = get_post_meta(get_the_ID());
				$collection_array = get_the_terms( get_the_ID(), 'collection' );

					echo '<ul class="store">';
					if (!empty( $meta['store_locator_city'][0])) {
						echo '<li class="city">' . esc_html( $meta['store_locator_city'][0] ) . '</li>';
					}
					echo '<li>' . esc_html( get_the_title() ) . '</li>';
					if (!empty( $meta['store_locator_address'][0])) {
						echo '<li>' . esc_html( $meta['store_locator_address'][0] ) . '</li>';
					}
					if (!empty( $meta['store_locator_phone'][0])) {
						echo '<li>' . esc_html( $meta['store_locator_phone'][0] ) . '</li>';
					}
					if (!empty( $meta['store_locator_email'][0])) {
						$email = antispambot( $meta['store_locator_email'][0] );
						echo '<li><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></li>';
					}
					if (!empty( $meta['store_locator_website'][0])) {
						$website_url = esc_url( $meta['store_locator_website'][0] );
						$host = parse_url($meta['store_locator_website'][0], PHP_URL_HOST);
						echo '<li><a href="' . $website_url . '" rel="external">' . esc_html( $host ) . '</a></li>';
					}
					if ( is_array( $collection_array ) ) {
						echo '<li>';
						foreach ($collection_array as $collection) {
							echo esc_html( $collection->name );
						}
						echo '</li>';
					}
				echo '</ul>';

			endwhile;
		}
	}

	wp_reset_query();

	echo '</div>';

	$contents = ob_get_clean();
	return $contents;

}
add_shortcode('storelist', 'showstorelist_shortcode');
