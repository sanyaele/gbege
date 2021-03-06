<?php

///////////////////////////////////
require_once 'includes/db.php';
require_once 'includes/common.php';
require_once 'includes/functions.php';
require_once 'includes/add_event.php';
////////////////////////////////////
class tweets extends add_event {
	private $dblink;
	private $twitterPosts;
	public $dbl;
	
	function __construct (){
		global $link;
		$this->dbl = $link;
		
		$result = get_locations ($link);
		$i=0;
		$key=0;
		
		$location[$key]="";
		while ($rows = @mysqli_fetch_assoc($result)){
			$this_loc = '"'.$rows['location'].'"'.' OR ';
			
			$location[$key] .= urlencode($this_loc);
			$i++;
			if ($i == 5):
				$location[$key] = substr($location[$key], 0, -4);
				$i=0;
				$key++; // new key 
				$location[$key]=""; //New key array
			endif;
		}
		
		if (!empty($location[$key])): //trim the last string
			$location[$key] = substr($location[$key], 0, -8);
		endif;
		
		// get Twitter requests for each line of locations
		
		/*$this->get_twitter ("");*/
		foreach($location as $value){
			if (!empty($value)):
				
				//echo $value."<p><br /><br /><br /></p>";
				$this->get_twitter ($value);
				//2 seconds sleep to limit bombarding of twitter
				set_time_limit(0);
				sleep(3); 
				
			endif;
		}
		
	}
	
	function get_twitter ($loc_url){
		/*c
		$ch = url_init();
		
		curl_setopt($ch, CURLOPT_URL, "http://search.twitter.com/search.json?q=".$loc_url."&rpp=20&lang=en&result_type=recent");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		*/
		$twitterJsonUrl =  "http://search.twitter.com/search.json?q=".$loc_url."&rpp=50&lang=en&result_type=recent";
		
		$data = file_get_contents ($twitterJsonUrl);
		
		$data = json_decode ($data);
		
		$num =  count($data->results); //get how many keys there are in the array
		//echo $twitterJsonUrl."; ".$num;
		for ($i=0; $i < $num && isset($data->results[$i]->text); $i++){
			$d['description'] = stripslashes($data->results[$i]->text);
			$d['pubdate'] = strtotime($data->results[$i]->created_at);
			$d['tweetid'] = stripslashes($data->results[$i]->id_str);
			$d['user_id'] = stripslashes($data->results[$i]->from_user_id_str);
			$d['profile_image'] = stripslashes($data->results[$i]->profile_image_url);
			
			if (strpos($d['description'],"RT @") === false):
				$this->twitterPosts[]=$d;
			endif;
		}
		
		
	}
	
	function add_db (){
		
		if(is_array($this->twitterPosts)){
			foreach($this->twitterPosts as $post){
				$tag = add_event::get_tag ($this->dbl, $post['description']);//get tag
				$location = add_event::get_location ($this->dbl, $post['description']);//get location
				// debug: echo "This is location: ".$location."<br />";
				// debug: echo "This is tag: ".$tag."<br />";
				if (!empty($tag) && !empty($location)):
					add_event::store($this->dbl, 'Twitter', $post['tweetid'], $tag, $post['description'], $location, $post['pubdate']); //store twitter update
				endif;
				
				//echo '<li><p>'.$post['tweetid'].$post['description'].'</p><p class="date">Posted On: '.date('l jS \of F Y h:i:s A',$post['pubdate']).'</p></li>';
			}
		}
		
	}
	
	
} //End class tweets

$report = new tweets;
$report->add_db ($link);
?>