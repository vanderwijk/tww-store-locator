// When clicking publish post, get lat and lng coordinates from Google Maps for location

jQuery(document).ready(function ($) {
	$('#coordinates').click(function(){

		address = $( '#store_locator_address' ).val() + ', ' + $( '#store_locator_postal' ).val() + ', ' + $( '#store_locator_city' ).val() + ', ' + $( '#store_locator_state' ).val() + ', ' + $( '#store_locator_country option:selected' ).text();

		$.ajax({
			url: 'https://maps.googleapis.com/maps/api/geocode/json',
			data: {
				sensor: false,
				address: address,
				key: 'AIzaSyAah5QLwGRMrfQ8W5MKvpQUxUuuzG-Upzo'
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