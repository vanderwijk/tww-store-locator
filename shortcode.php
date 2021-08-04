<?php 

//Shortcode
function showstorelocator_shortcode( $atts ) {
	extract(shortcode_atts(array(
		'width' => '650',
		'height' => '500',
	), $atts));
	$storecode ='<h3>Store locator</h3><iframe name="storelocator" src ="' . plugins_url() . '/tww-store-locator/files/store-locator.php?height=' . $height . '" width="' . $width . 'px" height="' . $height . 'px" scrolling="no" frameborder="0" ></iframe>';
	return $storecode;
}
add_shortcode('storelocator', 'showstorelocator_shortcode');


function showstorelist_shortcode() {

	echo "<style>
	.store-list {
		padding: 0 0 0 30px;
		border-left: 4px solid var(--paletteColor1);
	}
	ul.store {
		margin-bottom: 0;
		padding-left: 0;
	}
	@media screen and (max-width: 600px) {
		.store-list {
			padding: 0;
			border: none;
		}
		ul.store {
			margin-bottom: 15px;
		}
	}
	ul.store li {
		display: inline;
		padding-right: 6px;
	}
	ul.store li:after {
		margin-left: 6px;
		content: '•';
	}
	ul.store li:last-child:after {
		margin-left: 0;
		content: '';
	}
	ul.store .city {
		text-transform: uppercase;
	}
	</style>";

	echo '<div class="store-list">';

	$countries = get_terms( 'country', array(
		'orderby' => 'count',
		'order' => 'DESC',
		'hide_empty' => true,
	));

	foreach ($countries as $country) {

		$args = array(
			'post_type' => 'store',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'ignore_sticky_posts'=> true,
			'orderby'  => array( 'store_locator_city' => 'DESC'),
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
}
add_shortcode('storelist', 'showstorelist_shortcode');