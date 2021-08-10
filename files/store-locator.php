<?php 
//Generate Store Locator page 
if ( !defined('ABSPATH') ) {
	require_once('../../../../wp-load.php');
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<title><?php _e( 'Store Locator', 'store_locator_plugin' ); ?></title>
	<link rel="stylesheet" href="style.css" type="text/css" media="all" />
	<?php 
	$height = $_GET['height']; 
	$height = $height - 200;
	?>
	<script src="//unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"></script>
	<script src="//maps.google.nl/maps/api/js?key=AIzaSyCGctauGhQSjXGQNWOMkIXYZJKuvTpMaPM" type="text/javascript"></script>
	<script type="text/javascript">

	const locations = [
		<?php $countries = get_terms( 'country', array(
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

					if (!empty( $meta['store_locator_lat'][0])) {
						echo 'lat: ' . $meta['store_locator_lat'][0] . ', ';
					}

					if (!empty( $meta['store_locator_lng'][0])) {
						echo 'lng: ' . $meta['store_locator_lng'][0] . ', ';
					}

					if (!empty( $meta['store_locator_website'][0])) {
						echo 'website: "' . $meta['store_locator_website'][0] . '"';
					}

					echo ' }, ';

				endwhile;
			}
		}

		wp_reset_query(); ?>
	];


	function initMap() {
		const map = new google.maps.Map(document.getElementById("map"), {
			zoom: 2,
			center: { lat: 0, lng: 0 }
		});

		const markers = locations.map((location, i) => {
			return new google.maps.Marker({
				position: location
			});
		});

		marker.addListener("click", () => {
			infowindow.open({
			anchor: marker,
			map,
			shouldFocus: false,
			});
		});

		// Add a marker clusterer to manage the markers.
		new MarkerClusterer(map, markers, {
			imagePath:
			"https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
		});
	}

	</script>
</head>
<body style="margin:0px; padding:0px;" onload="initMap()" class="storelocator">
	<div class="storewrap">
		<div>
			<label for="addressInput"><?php _e( 'Enter Postal/Zip Code or City and Province/State', 'store_locator_plugin' ); ?>:</label>
			<input type="text" id="addressInput" size="30"/>
			<label for="radiusSelect"><?php _e( 'Within', 'store_locator_plugin' ); ?>:</label>
			<select id="radiusSelect">
				<option value="10" selected>10 <?php _e( 'miles', 'store_locator_plugin' ); ?></option>
				<option value="25">25 <?php _e( 'miles', 'store_locator_plugin' ); ?></option>
				<option value="50">50 <?php _e( 'miles', 'store_locator_plugin' ); ?></option>
			</select>
			<input type="submit" onclick="searchLocations()" value="<?php _e( 'Search', 'store_locator_plugin' ); ?>" />
		</div>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td class="map">
					<div id="map" style="height:<?php echo $height; ?>px"></div>
				</td>
				<td class="side_bar">
					<div id="side_bar" style="height:<?php echo $height; ?>px"></div>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>