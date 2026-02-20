// Get lat and lng coordinates from Google Maps for location.
jQuery(document).ready(function ($) {
	$('#coordinates').on('click', function (event) {
		event.preventDefault();

		if (typeof google === 'undefined' || !google.maps || !google.maps.Geocoder) {
			$('#store_locator_lat').val('Fout: Google Maps niet geladen.');
			$('#store_locator_lng').val('Controleer API-instellingen.');
			return;
		}

		var parts = [
			$('#store_locator_address').val(),
			$('#store_locator_postal').val(),
			$('#store_locator_city').val(),
			$('#store_locator_state').val(),
			$('#store_locator_country option:selected').text()
		];

		var address = $.map(parts, function (part) {
			var value = $.trim(part || '');
			return value.length ? value : null;
		}).join(', ');

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({ address: address }, function (results, status) {
			if (status === 'OK' && results.length) {
				$('#store_locator_lat').val(results[0].geometry.location.lat());
				$('#store_locator_lng').val(results[0].geometry.location.lng());
				return;
			}

			if (status === 'REQUEST_DENIED') {
				$('#store_locator_lat').val('Fout: API-sleutel niet geautoriseerd.');
				$('#store_locator_lng').val('Controleer Google API-restricties.');
				return;
			}

			if (status === 'OVER_QUERY_LIMIT') {
				$('#store_locator_lat').val('Fout: geocoding limiet bereikt.');
				$('#store_locator_lng').val('Probeer later opnieuw.');
				return;
			}

			$('#store_locator_lat').val('Fout: adres niet gevonden!');
			$('#store_locator_lng').val('Controleer adresgegevens (' + status + ').');
		});
	});
});
