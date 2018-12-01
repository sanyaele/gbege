<?php

/////////////////////////////////////////
require_once '../includes/db.php';
require_once '../includes/common.php';
require_once '../includes/functions.php';
require_once '../includes/add_event.php';

require_once "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
/////////////////////////////////////////
/* Load required lib files. */
session_start();

///////////////////////////////////////////////////   
///////////////////////////////////////////////////

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
			//echo "Location: ".$rows['location']."\n";
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
		
		 
		define('CONSUMER_KEY', 'nrmSw1i24f41hzYoNXYWig');
		define('CONSUMER_SECRET', '7mxMfBeCVDokcRUMPAtoF7Kw7Z1NOtbtqDqrV6b7VA');
		define('ACCESS_TOKEN', '15249547-ZscSd0zkf6DYmsmdhELzhJdi1ToyguDxmqhDRBfoL');
		define('ACCESS_TOKEN_SECRET', 'My64k4xskEk24xnGW2Xshu6Dz4Qu6hdrB1lBe4uYA');
		
		$conn = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
		$query = array(
		 "q" => $loc_url,
		 "count" => 50,
		 "lang" => "en",
		 "geocode" => "6.465422,3.406448,45km",
		 "result_type" => "recent"
		);
		$tweets = $conn->get('search/tweets', $query);
		
		
		/*/////////////////////////
		
		
		
		foreach ($tweets->statuses as $tweet) {
		 echo '<p>'.$tweet->text.'<br>Posted on: <a href="https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id.'">'.date('Y-m-d H:i', strtotime($tweet->created_at)).'</a></p>';
		}
		
		exit();
		
		
		
		///////////////////////*/
		
		
		/*///////////////////////////////////////////////////////////
		
		require_once "TwitterAPIExchange.php";
		
		// Set access tokens here - see: https://dev.twitter.com/apps/ 
		$settings = array(
			'oauth_access_token' => "15249547-ZscSd0zkf6DYmsmdhELzhJdi1ToyguDxmqhDRBfoL",
			'oauth_access_token_secret' => "My64k4xskEk24xnGW2Xshu6Dz4Qu6hdrB1lBe4uYA",
			'consumer_key' => "nrmSw1i24f41hzYoNXYWig",
			'consumer_secret' => "7mxMfBeCVDokcRUMPAtoF7Kw7Z1NOtbtqDqrV6b7VA"
		);
		  
		$url = "https://api.twitter.com/1.1/search/tweets.json";
		 
		$requestMethod = "GET";
		 
		$getfield = '?q='.$loc_url.'&count=50&lang=en&result_type=recent';
		
		$twitter = new TwitterAPIExchange($settings);
		$tweets = json_decode($twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest(),$assoc = TRUE);
		
		if($tweets["errors"][0]["message"] != "") {
			echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$tweets[errors][0]["message"]."</em></p>";
			exit();
		}
		
		////////////////////////////////////////////////////////////////////*/
		
		//echo "<pre>";
		//print_r($tweets);
		//echo "</pre>";
		  
		//$tweets = $twitter->get("https://api.twitter.com/1.1/search/tweets.json?");
	
		  
		//$data = json_encode($tweets);
		
		//$num =  count($tweets); //get how many keys there are in the array
		
		foreach ($tweets->statuses as $data){
		
			 if (isset($data->text)):
				 $d['description'] = stripslashes($data->text);
				$d['pubdate'] = strtotime($data->created_at);
				$d['tweetid'] = stripslashes($data->id_str);
				$d['user_id'] = stripslashes($data->user->id_str);
				$d['profile_image'] = stripslashes($data->user->profile_image_url);
				
				//remove http from description
				$desc = explode(" http",$d['description']);
				$desc1 = explode("http",$desc[0]);
				$d['description'] = $desc1[0];
				//print_r ($d);
				//exit();
				
				if (strpos($d['description'],"RT") === false):
					$this->twitterPosts[]=$d;
				endif;
			 endif;
		}
		
		print_r ($this->twitterPosts);
		exit();
		
		
	}
	
	function add_db (){
		
		if(is_array($this->twitterPosts)){
			foreach($this->twitterPosts as $post){
				$tag = add_event::get_tag ($this->dbl, $post['description']);//get tag
				$location = add_event::get_location ($this->dbl, $post['description']);//get location
				echo "This is location: ".$location."<br />";
				echo "This is tag: ".$tag."<br />";
				continue;
				if (!empty($tag['t']) && !empty($location)):
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