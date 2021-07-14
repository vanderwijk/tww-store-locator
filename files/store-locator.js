// Get lat and lng coordinates from Google Maps for location

jQuery(document).ready(function ($) {
	$('#coordinates').click(function(event){

		event.preventDefault();

		address = $( '#store_locator_address' ).val() + ', ' + $( '#store_locator_postal' ).val() + ', ' + $( '#store_locator_city' ).val() + ', ' + $( '#store_locator_state' ).val() + ', ' + $( '#store_locator_country option:selected' ).text();

		function parseToXML( $htmlStr) {
			$xmlStr = str.replace('<','&lt;',$htmlStr);
			$xmlStr = str.replace('>','&gt;',$xmlStr);
			$xmlStr = str.replace('"','&quot;',$xmlStr);
			$xmlStr = str.replace("'",'&#39;',$xmlStr);
			$xmlStr = str.replace("&",'&amp;',$xmlStr);
			return $xmlStr;
		}

		$.ajax({
			url: 'https://maps.googleapis.com/maps/api/geocode/json',
			data: {
				sensor: false,
				address: parseToXML(address),
				key: store_locator_options.store_locator_google_maps_api_key
			},

			success: function (data) {
				//console.log(data);
				//console.log(address);
				if( data.results.length ) {
					$('#store_locator_lat').val(data.results[0].geometry.location.lat);
					$('#store_locator_lng').val(data.results[0].geometry.location.lng);
				} else {
					$('#store_locator_lat').val('Fout: adres niet gevonden!');
					$('#store_locator_lng').val('Controleer adresgegevens.');
				}
			}
		});

	});
});