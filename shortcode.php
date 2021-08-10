<?php 

//Shortcode
function showstorelocator_shortcode() {

	if ( empty( get_option('store_locator_settings')['google_maps_api_key'])) {
		$store_locator_google_maps_api_key = NULL; 
	} else { 
		$store_locator_google_maps_api_key = get_option('store_locator_settings')['google_maps_api_key'];
	}

	ob_start();

	echo "<div id='map_canvas'></div>
	<script src='https://maps.google.nl/maps/api/js?key=" . $store_locator_google_maps_api_key .  "' type='text/javascript'></script>
	<script src='https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js'></script>
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

					echo '{ ';

						echo 'phone: "' . $meta['store_locator_phone'][0] . '", ';

						$host = parse_url($meta['store_locator_website'][0], PHP_URL_HOST);
						echo 'website: "' . $host . '", ';

						$scheme = parse_url($meta['store_locator_website'][0], PHP_URL_SCHEME);
						echo 'scheme: "' . $scheme . '", ';

						echo 'email: "' . $meta['store_locator_email'][0] . '", ';

						echo 'shop: "' . get_the_title() . '", ';

						echo 'address: "' . $meta['store_locator_address'][0] . '", ';

						echo 'postal: "' . $meta['store_locator_postal'][0] . '", ';

						echo 'city: "' . $meta['store_locator_city'][0] . '", ';

						echo 'state: "' . $meta['store_locator_state'][0] . '", ';

						echo 'country: "' . $meta['store_locator_country'][0] . '", ';

						echo 'lat: ' . $meta['store_locator_lat'][0] . ', ';

						echo 'lng: ' . $meta['store_locator_lng'][0] . '';

					echo ' }, ';

				endwhile;
			}
		}

		wp_reset_query();

	echo "]
	var map;
	var markers = [];
	var infoWindows = [];
	
	function init(){

		var mapOptions = {
			mapId: 'c62305ec4f432eb',
			zoom: 3,
			center: new google.maps.LatLng(50, -15),
			mapTypeId: 'roadmap'
		};
		map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

		var num_markers = locations.length;

		for (var i = 0; i < num_markers; i++) {

			var textArray = [];

			textArray = [
				('<h5>' + locations[i].shop + '</h5>'),
				('</p>'),
				(locations[i].address !== 'undefined' && locations[i].address + '</br>'),
				(locations[i].postal !== 'undefined' && locations[i].postal + ' '),
				(locations[i].city !== 'undefined' && locations[i].city + '</br>'),
				(locations[i].state !== 'undefined' && locations[i].state),
				('</p>'),
				('<p>'),
				(locations[i].phone !== 'undefined' && locations[i].phone + '</br>'),
				(locations[i].email !== 'undefined' && '<a href=\"mailto:' + locations[i].email + '\">' + locations[i].email + '</a></br>'),
				(locations[i].website !== 'undefined' && '<a href=\"' + locations[i].scheme + '://' + locations[i].website + '\" rel=\"external\">' + locations[i].website + '</a>' + '</br>'),
				('</p>')
			];

			textString = textArray.join(\"\");

			markers[i] = new google.maps.Marker({
				position: { 
					lat: locations[i].lat,
					lng: locations[i].lng
				},
				map: map,
				html: textString,
				id: i
			});

			google.maps.event.addListener(markers[i], 'click', function(){
				for (var i=0;i<infoWindows.length;i++) {
					infoWindows[i].close();
				}

				map.panTo(this.getPosition());

				var infowindow = new google.maps.InfoWindow({
					id: this.id,
					content: this.html,
					position: this.getPosition(),
				});

				infoWindows.push(infowindow);

				google.maps.event.addListenerOnce(infowindow, 'closeclick', function(){
					markers[this.id].setVisible(true);
				});

				//this.setVisible(false);
				infowindow.open(map);
			});

			google.maps.event.addListener(map, 'click', function() {
				for (var i=0;i<infoWindows.length;i++) {
					infoWindows[i].close();
				}
			});
		}
		new MarkerClusterer(map, markers, {
			imagePath:
				'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
		});
	}
	init();
	
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

			echo '<h5>' . $country->name . '</h5>';

			while ($store_query -> have_posts()) : $store_query -> the_post();

				$meta = get_post_meta(get_the_ID());
				$collection_array = get_the_terms( get_the_ID(), 'collection' );

				echo '<ul class="store">';
				if (!empty( $meta['store_locator_city'][0])) {
					echo '<li class="city">' . $meta['store_locator_city'][0] . '</li>';
				}
				echo '<li>' . get_the_title() . '</li>';
				if (!empty( $meta['store_locator_address'][0])) {
					echo '<li>' . $meta['store_locator_address'][0] . '</li>';
				}
				if (!empty( $meta['store_locator_phone'][0])) {
					echo '<li>' . $meta['store_locator_phone'][0] . '</li>';
				}
				if (!empty( $meta['store_locator_email'][0])) {
					echo '<li><a href="mailto:' . antispambot($meta['store_locator_email'][0]) . '">' . antispambot($meta['store_locator_email'][0]) . '</a></li>';
				}
				if (!empty( $meta['store_locator_website'][0])) {
					$host = parse_url($meta['store_locator_website'][0], PHP_URL_HOST);
					echo '<li><a href="'. $meta['store_locator_website'][0] .'" rel="external">' . $host . '</a></li>';
				}
				if ($collection_array) {
					echo '<li>';
					foreach ($collection_array as $collection) {
						echo $collection->name;
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