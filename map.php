<?php
require_once 'includes/db.php';
require_once 'includes/common.php';
require_once 'includes/functions.php';

////////////////////////////////////
class mapping {
	public $state = "Lagos";
	public $coord = "6.54, 3.34";
	public $dblink;
	
	function __construct ($state=""){
		global $link;
		$this->dblink  = $link;
		
		if (!empty($state)):
			$state = addslash ($state);
			$this->get_state_coord ($this->dblink, $state);
		endif;
	}
	
	// get id of $address
	function get_state_coord ($link, $state){
		$state = addslash($state);
		$sql = "SELECT coord FROM states WHERE state = '$state' LIMIT 1";
		$result = @mysqli_query ($link, $sql);
		$row = @mysqli_fetch_assoc ($result);
		if (!empty($row['coord'])):
			$this->coord = $row['coord'];
			$this->state = $state;
		endif;
		
	}
	
	// get events
	function get_events ($link, $tag=""){
		$result = get_events ($link, $this->state, $tag);
		while ($rows = @mysqli_fetch_assoc($result)){
			//echo "codeAddress('Location', 'Tags','Content',infowindow)\n";
			if (isset($report [$rows['location']])):
				$report [$rows['location']]['tags'] .= ", ".$rows['tag'];
				$report [$rows['location']]['info'] .= "<br /><i><b>".$rows['tag'].":</b>".$rows['desc']."</i>";
			else:
				$report [$rows['location']]['coord'] = $rows['coord'];
				$report [$rows['location']]['tags'] = $rows['tag'];
				$report [$rows['location']]['info'] = "<b>".$rows['location'].":</b><br /><i><b>".$rows['tag'].":</b>".$rows['desc']."</i>";
				$report [$rows['location']]['more'] = "<br /><br /><a href='source.php?loc=".$rows['locid']."' target='side'>Details</a>";
			endif;
			
		}
		
		if (isset($report)):
			foreach ($report as $details){
				/*
				foreach ($tags as $tag=>$value){
					$title .= $tag . ", ";
				}
				*/
				$coord = explode(", ",$details['coord']);
				$title = $details['tags'];
				$infoW = "<div class='infotxt'>".$details['info'].$details['more']."</div>";
				
				echo "codeAddress(\"".$coord[0]."\", \"".$coord[1]."\", \"".$title."\", \"".$infoW."\", infowindow)\n";
			}
		endif;
	}
}

$map = new mapping($_GET['location']);
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
.infotxt {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
}
</style>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDc4oomUd0HGX1O-tmhEu_Fxw-Vx67enBA&sensor=true">
</script>
<script type="text/javascript">
  var geocoder;
  var map;
  
  function initialize() {
    geocoder = new google.maps.Geocoder();
    //var latlng = new google.maps.LatLng(-34.397, 150.644); 
	//var latlng = new google.maps.LatLng(6.25, -3.27);
	var latlng = new google.maps.LatLng(<?php echo $map->coord;?>);
    var myOptions = {
      zoom: 11,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	// Creating an InfoWindow object
	var infowindow = new google.maps.InfoWindow();
	<?php
	$map->get_events ($link, $_REQUEST['tag'])
	?>
	//codeAddress("6.608855", "3.437127", "This is Ikeja Lagos","This is the <strong>craziest place</strong> in Lagos",infowindow)
	//codeAddress("Shomolu, Lagos, Nigeria","This is Shomolu Lagos","This is a cool place<br /> I recommend it",infowindow)
  }
  
  function codeAddress(longi,lati,title,infos,infowindow) {
        var myLatLng = new google.maps.LatLng(longi, lati);
		map.setCenter(myLatLng);
        var marker = new google.maps.Marker({
            map: map,
            position: myLatLng,
			title: title
        });
		
		google.maps.event.addListener(marker, 'click', function() {
		  infowindow.setContent(infos);
		  infowindow.open(map, marker);
		});
  }
</script>
</head>
<body onLoad="initialize()">
  <div id="map_canvas" style="width:100%; height:100%"></div>
  <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30599375-1']);
  _gaq.push(['_setDomainName', 'gbege.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>