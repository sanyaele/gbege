<?php
require_once 'includes/db.php';
require_once 'includes/common.php';
require_once 'includes/functions.php';
///////////////////////////////////
function get_src_events ($link, $state="", $tag="", $location=""){
	$where = "";
	if (!empty($state)):
		$state = addslash ($state);
		$where .= " AND locations.state = '$state'";
	endif;
	
	if (!empty($tag)):
		$tag = addslash ($tag);
		$where .= "AND events.tag = '$tag'";
	endif;
	
	if (!empty($location)):
		$loc = addslash ($location);
		$where .= " AND events.locationId = '$loc'";
	endif;
	
	$from_date = date("Y-m-d H:i:s", strtotime('-2 days'));
	
	$sql = "SELECT events.id, events.logtime, events.body, events.source, locations.state FROM `events`, `locations`
			WHERE events.locationId = locations.id $where
			AND events.logtime >= '$from_date'
			ORDER BY events.logtime DESC LIMIT 100";
			
	$result = @mysqli_query ($link, $sql);
	
	while ($rows = @mysqli_fetch_assoc($result)){
		echo "<div class=\"around\">$rows[body]</div>
				<div class=\"source\">$rows[source]
					<div class=\"dtime\">".get_date($rows['logtime'])."</div>
					<div class=\"map\"><a href=\"map.php?location=".$rows['state']."\" target=\"main\">MAP</a></div>
					<div class=\"share\"> 
					<a href=\"http://www.facebook.com/share.php?u=".urlencode("http://gbege.com/index.php?event=".$rows['id'])."\" target=\"_blank\"><img src=\"assets/fb.png\" alt=\"Share on Facebook\" border=\"0\" /></a>
						<a href=\"https://twitter.com/share?url=".urlencode("http://gbege.com/index.php?event=".$rows['id'])."&text=".urlencode($rows['body'])."\" target=\"_blank\"><img src=\"assets/tw.png\" alt=\"Share on Twitter\" border=\"0\" /></a></div>
				</div>\n";
	}
	//<a href=\"https://twitter.com/share\" class=\"twitter-share-button\" data-url=\"".urlencode("http://gbege.com/index.php?event=".$rows['id'])."\" data-text=\"$rows[body]\" data-count=\"none\" data-via=\"gbege2\"><img src=\"assets/tw.png\" border=\"0\" /></a>
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Event details</title>
<link href="assets/css.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body {
	background-color: #FFF;
	margin-top: 10px;
	margin-right: 0px;
	margin-bottom: 0px;
	margin-left: 7px;
}
</style>
</head>

<body>
<?php
get_src_events ($link, $_GET['location'], $_GET['tag'], $_GET['loc']);
/*
<div class="around">This is a sample. We automatically gather information from Twitter and other open sources, and collate this information to form intelligence that points out trouble spots in Nigeria, grouped by individual states and type of problem.</div>
				<div class="source">Twitter</div><div class="dtime">20 minutes ago</div>
*/
?>
<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" 
        type="text/javascript">
</script>
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
