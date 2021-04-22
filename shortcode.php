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