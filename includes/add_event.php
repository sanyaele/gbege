<?php
class add_event {
	function get_tag ($link, $body){
		$result = get_keywords ($link);
		while ($rows = @mysqli_fetch_assoc($result)){
			$tags = explode (",", $rows['keywords']);
			foreach ($tags as $tag){
				$tagstr = " ".$tag;
				if (strpos($body, $tagstr) !== false)://search needle in haystack
					$t['t'] = $rows['tag'];
					$t['kw'] = $tag;
					return $t;
				endif;
			}
		}
	}
	
	function get_location ($link, $body){
		$result = get_locations ($link);
		while ($rows = @mysqli_fetch_assoc($result)){
			$loc[] = " ".$rows['location']." ";
			$loc[] = " ".$rows['location'].".";
			$loc[] = " ".$rows['location'].",";
			
			foreach ($loc as $location){
				if (strpos($body, $location) !== false)://search needle in haystack
					return $rows['id'];
				endif;
			}
		}
	}
	
	function store ($link, $ev_source, $ev_id, $ev_tag, $ev_body, $ev_loc, $ev_time){ //Event details
		if (!empty($ev_time)):
			$d = date('Y-m-d H:i:s',$ev_time);
		elseif ($ev_time == ''):
			$d = date('Y-m-d H:i:s');
		else:
			$d = $ev_time;
		endif;
		
		$body = str_replace("<a", "<a target='_blank'",$ev_body);
		//$body = $ev_body;
		
		$sql = "INSERT INTO events SET
		id = '$ev_id',
		tag = '$ev_tag[t]',
		keyword = '$ev_tag[kw]',
		body = '$body',
		source = '$ev_source',
		locationId = '$ev_loc',
		logtime = '$d',
		event_hash = sha1('$body')
		ON DUPLICATE KEY UPDATE 
		noise = noise+1";
		if (@mysqli_query($link, $sql)):
			return TRUE;
		else:
			return FALSE;
		endif;
		
		echo $sql;
	  }
} // End class add_event
?>