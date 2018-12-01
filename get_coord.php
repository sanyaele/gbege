<?php
///////////////////////////////////
require_once 'includes/db.php';
require_once 'includes/common.php';
require_once 'includes/functions.php';
////////////////////////////////////
function add_coord ($link, $lid, $coord){
	$sql = "UPDATE locations SET coord = '$coord' WHERE id = '$lid'";
	@mysqli_query($link, $sql);
}

$result = get_locations ($link);
while ($rows = @mysqli_fetch_assoc($result)){
	$location = "'".$rows['location'].", ".$rows['location']."'";
	$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=".urlencode($location)."&sensor=false&region='NG'");
	$json = json_decode($json);

	$lat = substr($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'},0,8);
	$long = substr($json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'},0,8);
	
	$coord = $lat.", ".$long;
	add_coord ($link, $rows['id'], $coord);
	
	sleep(1);
}

?>