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
	<script src="https://maps.google.nl/maps/api/js?key=AIzaSyCGctauGhQSjXGQNWOMkIXYZJKuvTpMaPM&sensor=false&language=nl" type="text/javascript"></script>
	<script type="text/javascript">
	//<![CDATA[
	var map;
	var markers = [];
	var side_bar_html = "";
	var infoWindow;

	function load() {
		map = new google.maps.Map(document.getElementById("map"), {
			center: new google.maps.LatLng(51.5107008, 5.7992583),
			zoom: 6,
			mapTypeId: 'roadmap',
			mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
		});
		infoWindow = new google.maps.InfoWindow();
	}

	function searchLocations() {
		var address = document.getElementById("addressInput").value;
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({address: address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				searchLocationsNear(results[0].geometry.location);
			} else {
				alert(address + '<?php _e( 'Enter Postal/Zip Code or City and Province/State', 'store_locator_plugin' ); ?>');
			}
		});
	}

	function clearLocations() {
		infoWindow.close();
		for (var i = 0; i < markers.length; i++) {
			markers[i].setMap(null);
		}
		markers.length = 0;
		side_bar_html = "";
	}

	function searchLocationsNear(center) {
		clearLocations(); 

		var radius = document.getElementById('radiusSelect').value;
		var searchUrl = 'xml-map.php?lat=' + center.lat() + '&lng=' + center.lng() + '&radius=' + radius;
		downloadUrl(searchUrl, function(data) {
			var xml = parseXml(data);
			var markerNodes = xml.documentElement.getElementsByTagName("marker");
			var bounds = new google.maps.LatLngBounds();
			for (var i = 0; i < markerNodes.length; i++) {
				var name = markerNodes[i].getAttribute("name");
				var address = markerNodes[i].getAttribute("address");
				var distance = parseFloat(markerNodes[i].getAttribute("distance"));
				var phone = markerNodes[i].getAttribute("phone");
				var latlng = new google.maps.LatLng(
					parseFloat(markerNodes[i].getAttribute("lat")),
					parseFloat(markerNodes[i].getAttribute("lng")));
				createMarker(latlng, name, address, phone);
				bounds.extend(latlng);
			}
			map.fitBounds(bounds);
			if (side_bar_html == "") {
				document.getElementById("side_bar").innerHTML = '<strong><?php _e( 'No Matches Found', 'store_locator_plugin' ); ?></strong>';
			} else {
				document.getElementById("side_bar").innerHTML = side_bar_html;
			}
		});
	}

	function createMarker(latlng, name, address, phone) {
		var html = "<b style='white-space: nowrap;'>" + name + "</b> <br/>" + address + "<br/> <br/>" + phone + "<br/>";
		var cleanaddy = address.replace(/<\/?[^>]+(>|$)/g, " ");
		html +='<form action="https://maps.google.nl/maps" method="get"" target="_blank">'+
			'<input value="<?php _e( 'Get Directions', 'store_locator_plugin' ); ?>" type="submit">' +
			'<input type="hidden" name="daddr" value="' + cleanaddy + '"/>';
		var marker = new google.maps.Marker({
			map: map,
			position: latlng
		});
		google.maps.event.addListener(marker, 'click', function() {
			infoWindow.setContent(html);
			infoWindow.open(map, marker);
		});
		markers.push(marker);
		side_bar_html += '<a href="javascript:triggerMarker(' + (markers.length-1) + ')">' + name + '<\/a><br>'+ address +'<br><br>'+ phone +'<br><br>';
	}

	function triggerMarker(i) {
		google.maps.event.trigger(markers[i], "click");
	}

	function downloadUrl(url, callback) {
	var request = window.ActiveXObject ?
		new ActiveXObject('Microsoft.XMLHTTP') :
		new XMLHttpRequest;
	
		request.onreadystatechange = function() {
			if (request.readyState == 4) {
				request.onreadystatechange = doNothing;
				callback(request.responseText, request.status);
			}
		};

		request.open('GET', url, true);
		request.send(null);
	}

	function parseXml(str) {
		if (window.ActiveXObject) {
			var doc = new ActiveXObject('Microsoft.XMLDOM');
			doc.loadXML(str);
			return doc;
		} else if (window.DOMParser) {
			return (new DOMParser).parseFromString(str, 'text/xml');
		}
	}

	function doNothing() {}
</script>
</head>
<body style="margin:0px; padding:0px;" onload="load()" class="storelocator">
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