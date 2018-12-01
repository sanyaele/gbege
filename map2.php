<?php
if (!empty($_REQUEST['address'])):
	$address = $_REQUEST['address'].", Nigeria";
else:
	$address = "Lagos, Nigeria";
endif;

// get id of $address
function get_add_id ($link){

}
/////////////////////////////
// Google map code
// <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDc4oomUd0HGX1O-tmhEu_Fxw-Vx67enBA&sensor=true">
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true">
</script>
<script type="text/javascript">
  var geocoder;
  var map;
  
  
  function codeAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var loc = results[0].geometry.location;
		
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
	  return loc;
    });
	
  }
  
  function initialize() {
    geocoder = new google.maps.Geocoder();
    //var latlng = new google.maps.LatLng(-34.397, 150.644);
	var latlng = new google.maps.LatLng(6.25, -3.27);
    var myOptions = {
      zoom: 10,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	var areaMarker = codeAddress("Lagos, Nigeria");
	alert ('This is '+areaMarker);
	map.setCenter(areaMarker);
	var marker = new google.maps.Marker({
		map: map,
		position: areaMarker
	});
	//var infowindow = new google.maps.InfoWindow();
	
	//Create marker array
	/*
	var location = new Array();
	
	location[0] = codeAddress("Ikeja, Lagos, Nigeria");
	location[1] = codeAddress("Shomolu, Lagos, Nigeria");
	
	for (var i = 0, location; location = location[i]; i++) {
		var marker = new google.maps.Marker({
			map: map,
			position: location[i]
		});
		
		// Add a listener
		google.maps.event.addListener(marker, 'click', function() {
		  infowindow.setContent("WHere is this");
		  infowindow.open(map, marker);
		});
	}
	*/
  }
</script>
</head>
<body onLoad="initialize()">
  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>