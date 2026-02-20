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
	$height_param = isset( $_GET['height'] ) ? absint( wp_unslash( $_GET['height'] ) ) : 650;
	$height = max( 200, $height_param - 200 );
	$store_locator_settings = get_option('store_locator_settings');
	$store_locator_google_maps_api_key = isset($store_locator_settings['google_maps_api_key']) ? $store_locator_settings['google_maps_api_key'] : '';
	?>
	<script src="//maps.googleapis.com/maps/api/js?key=<?php echo rawurlencode($store_locator_google_maps_api_key); ?>&libraries=marker&loading=async" type="text/javascript" defer></script>
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

					if (empty( $meta['store_locator_lat'][0] ) || empty( $meta['store_locator_lng'][0] ) ) {
						continue;
					}
					echo '{ ';
					echo 'lat: ' . floatval( $meta['store_locator_lat'][0] ) . ', ';
					echo 'lng: ' . floatval( $meta['store_locator_lng'][0] );

					if (!empty( $meta['store_locator_website'][0])) {
						echo ', website: ' . wp_json_encode( esc_url_raw( $meta['store_locator_website'][0] ) );
					}

					echo ' }, ';

				endwhile;
			}
		}

		wp_reset_query(); ?>
	];


	function initMap() {
		const map = new google.maps.Map(document.getElementById("map"), {
			mapId: 'c62305ec4f432eb',
			zoom: 2,
			center: { lat: 0, lng: 0 }
		});
		const useAdvancedMarkers = Boolean(
			google.maps.marker &&
			google.maps.marker.AdvancedMarkerElement
		);

		const markers = locations.map((location, i) => {
			if (useAdvancedMarkers) {
				return new google.maps.marker.AdvancedMarkerElement({
					position: location,
					map: map
				});
			}
			return new google.maps.Marker({
				position: location,
				map: map
			});
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
					<div id="map" style="height:<?php echo esc_attr( $height ); ?>px"></div>
				</td>
				<td class="side_bar">
					<div id="side_bar" style="height:<?php echo esc_attr( $height ); ?>px"></div>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>
