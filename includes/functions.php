<?php
function get_tags ($link){
	$sql = "SELECT tag FROM `tags`";
	$result = @mysqli_query($link,$sql);
	return $result;
}

function get_tags_opt ($link){
	$result = get_tags ($link);
	while ($rows = mysqli_fetch_assoc($result)){
		echo "<option value=\"$rows[tag]\">$rows[tag]</option>\n";
	}
}

function get_keywords ($link){
	$sql = "SELECT `tag`,`keywords` FROM `tags`";
	$result = @mysqli_query($link,$sql);
	return $result;
}

function get_state ($link){
	$sql = "SELECT * FROM `states`";
	$result = @mysqli_query($link,$sql);
	return $result;
}

function get_state_opt ($link){
	$result = get_state ($link);
	while ($rows = mysqli_fetch_assoc($result)){
		echo "<option value=\"$rows[state]\">$rows[state]</option>\n";
	}
}

function get_locations ($link, $state="", $special=" ORDER BY location ASC"){
	if (!empty($state)):
		$sql = "SELECT id, location, state FROM `locations` WHERE state = '$state' $special";
	else:
		$sql = "SELECT id, location, state FROM `locations` $special";
	endif;
	//echo $sql;
	$result = @mysqli_query($link,$sql);
	return $result;
}

function get_twitter_tags ($link){
	$sql = "SELECT keywords FROM `tags`";
	$result = @mysqli_query($link,$sql);
	$keyword_str = "";
	while ($rows = mysqli_fetch_assoc($result)){
		$keyword = explode (",", $rows['keywords']);
		foreach ($keyword as $value){
			$keyword_str .= $value."%20OR%20";
		}
		
	}
	$keyword_str = substr($keyword_str, 0, -8);
	
	return $keyword_str;
}


function get_date ($datetime){ // Reformat date
	$sec_diff = (strtotime(date("Y-m-d H:i:s")) - strtotime(date("$datetime")));
	
	if ($sec_diff < '60'): // Less than a minute
		$time_diff = round( abs($sec_diff), 0 ); // How many seconds ago
		return $time_diff . "sec ago";
	elseif ($sec_diff < '3600'): // Less Than an hour
		$time_diff = round( abs($sec_diff) / 60, 0 ); // How many minutes ago
		$secs_rem = round( abs($sec_diff) % 60, 0 ); // How many seconds remaining
		return  $time_diff ."min ".$secs_rem ."sec ago";
	elseif ($sec_diff < '86400'): // Less Than a day
		$time_diff = round( abs($sec_diff) / 3600, 0 );  //How many hours ago
		$mins_rem = round( (abs($sec_diff) % 3600) / 60, 0 ); //How many minutes remaining
		return  $time_diff ."hr ".$mins_rem ."min ago";
	elseif ($sec_diff < '259200'): // Less Than 3 days
		$time_diff = round( abs($sec_diff) / 86400, 0 ); //How many days ago
		$hrs_rem = round( (abs($sec_diff) % 86400) / 3600, 0 ); //How many hours remaining
		return  $time_diff ."day ".$hrs_rem ."hr ago";
	else: // More than three days
		return date ("Y-m-d", strtotime(date($datetime))); // Display date time stamp on the last report
	endif;
}

function get_events ($link, $state, $tag="", $ord="", $lim=""){
	if (!empty($tag)):
		$exist = 0;
		$t_result = get_tags ($link);
		while ($t_rows = mysqli_fetch_assoc($t_result)){
			if ($tag == $t_rows['tag']):
				$exist = 1;
			endif;
		}
		if (empty($exist)):
			$tag = "";
		endif;
	endif;
	
	if (!empty($lim)):
		$lim = " LIMIT ".$lim;
	endif;
	
	if (!empty($ord)):
		$ord = " ORDER BY events.noise ";
	endif;
	
	$from_date = date("Y-m-d H:i:s", strtotime('-2 days'));
	
	if (empty($tag)):
		$sql = "SELECT DISTINCT events.tag, locations.id AS locid, locations.location, locations.coord, tags.desc FROM events, locations, tags
		WHERE events.locationId = locations.id
		AND events.tag = tags.tag
		AND locations.state = '$state'
		AND events.logtime >= '$from_date'
		AND events.noise > '1'".$ord.$lim;
	else:
		$sql = "SELECT DISTINCT events.tag, locations.id AS locid, locations.location, locations.coord, tags.desc FROM events, locations, tags
		WHERE events.locationId = locations.id
		AND events.tag = tags.tag
		AND locations.state = '$state'
		AND events.tag = '$tag'
		AND events.logtime >= '$from_date'
		AND events.noise > '1'".$ord.$lim;
	endif;
	$result = @mysqli_query($link, $sql);
	return $result;
}

function get_src_events ($link, $state="", $tag="", $location="", $noise_order='0'){
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
	
	if ($noise_order == '1'):
		$where .= " ORDER BY events.noise DESC";
	else:
		$where .= " ORDER BY events.logtime DESC ";
	endif;
	
	$from_date = date("Y-m-d H:i:s", strtotime('-2 days'));
	
	$sql = "SELECT events.id, events.logtime, events.body, events.source, locations.state FROM `events`, `locations`
			WHERE events.locationId = locations.id 
			AND events.logtime >= '$from_date'
			$where
			LIMIT 100";
			
	$result = @mysqli_query ($link, $sql);
	
	while ($rows = @mysqli_fetch_assoc($result)){
		echo "<div class=\"around\">$rows[body]</div>
				<div class=\"source\">$rows[source]
					<div class=\"dtime\">".get_date($rows['logtime'])."</div>
					<div class=\"share\"> <a href=\"map.php?location=".$rows['state']."\" target=\"main\"><img src=\"assets/map.png\" title=\"Show on Map\" border=\"0\" /></a>
					<a href=\"http://www.facebook.com/share.php?u=".urlencode("http://gbege.com/index.php?event=".$rows['id'])."\" target=\"_blank\"><img src=\"assets/fb.png\" title=\"Share on Facebook\" border=\"0\" /></a>
						<a href=\"https://twitter.com/share?url=".urlencode("http://gbege.com/index.php?event=".$rows['id'])."&text=".urlencode($rows['body'])."\" target=\"_blank\"><img src=\"assets/tw.png\" title=\"Share on Twitter\" border=\"0\" /></a></div>
				</div>\n";
	}
	//<a href=\"https://twitter.com/share\" class=\"twitter-share-button\" data-url=\"".urlencode("http://gbege.com/index.php?event=".$rows['id'])."\" data-text=\"$rows[body]\" data-count=\"none\" data-via=\"gbege2\"><img src=\"assets/tw.png\" border=\"0\" /></a>
}

function get_noisy_feed ($link){ //Required title, event_id, timedate, description
	$ago = date("Y-m-d H:i:s",strtotime("24 hours ago"));//three days ago
	
	
	$sql = "SELECT events.tag, events.id, events.body, events.source, events.logtime, SUM(events.noise) AS noise, locations.location 
	FROM `events`, locations
	WHERE events.logtime > '$ago'
	AND events.locationId = locations.id
	AND events.keyword != ''
	GROUP BY `tag`, `location`
	ORDER BY noise DESC
	LIMIT 10";
	//echo $sql;
	$result = @mysqli_query($link, $sql);
	return $result;
}
?>