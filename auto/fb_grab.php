<?php
/////////////////////////////////////////
require_once '../includes/db.php';
require_once '../includes/common.php';
require_once '../includes/functions.php';
require_once '../includes/add_event.php';
/////////////////////////////////////////
/* Load required lib files. */
session_start();
require 'fb/src/facebook.php';


///////////////////////////////////////////////////   
///////////////////////////////////////////////////

class status extends add_event {
	private $dblink;
	private $statusPosts;
	public $dbl;
	
	function __construct (){
		global $link;
		$this->dbl = $link;
		
		$result = get_locations ($link,""," ORDER BY RAND() LIMIT 10");
		$i=0;
		$key=0;
		
		$location[$key]="";
		while ($rows = @mysqli_fetch_assoc($result)){
			$this_loc = rawurlencode(''.$rows['location'].'').'%20|%20';
			
			$location[$key] .= $this_loc;
			$i++;
			if ($i == 5):
				$location[$key] = substr($location[$key], 0, -7);
				$i=0;
				$key++; // new key 
				$location[$key]=""; //New key array
			endif;
		}
		
		if (!empty($location[$key])): //trim the last string
			$location[$key] = substr($location[$key], 0, -7);
		endif;
		
		// get Twitter requests for each line of locations
		
		/*$this->get_twitter ("");*/
		foreach($location as $value){
			if (!empty($value)):
				
				//echo $value."<p><br /><br /><br /></p>";
				$this->get_fb ($value);
				//2 seconds sleep to limit bombarding of twitter
				set_time_limit(0);
				sleep(3); 
				
			endif;
		}
		
	}
	
	function get_fb ($loc_url){
		
		$facebook = new Facebook(array(
		  'appId'  => '447350965281305',
		  'secret' => '6f947b65249032228ed5213253d4bae8',
		));
		
		$fbs = $facebook->api('/search?q=lagos&type=post');
		//echo '/search?q='.$loc_url;
		//print_r ($fbs);
		//$fbs = json_decode($fb);
		//$data = json_encode($tweets);
		//print_r ($data);
		
		//$num =  count($fbs->data); //get how many keys there are in the array
		//echo $twitterJsonUrl."; ".$num;
		//print_r ($tweets);
		foreach ($fbs['data'] as $data){
			 echo $data['message'];
			 //print_r ($data);
			 //exit();
			 if (isset($data['message'])):
				if (!empty($data['description'])):
					$big_desc = $data['description'];
				else:
					$big_desc = $data['message'];
				endif;
				
				
				$d['description'] = stripslashes($big_desc);
				$d['pubdate'] = strtotime($data['created_time']);
				$d['statusid'] = stripslashes($data['id']);
				$d['user_id'] = stripslashes($data['from']['id']);
				
				if (!empty($data['picture'])):
					$d['profile_image'] = $data['picture'];
				else:
					$d['profile_image'] = "";
				endif;
				
				
				
				//remove http from description
				$desc = explode(" http",$d['description']);
				$desc1 = explode("http",$desc[0]);
				$d['description'] = $desc1[0];
				//print_r ($d);
				
				$this->statusPosts[]=$d;
			 endif;
		}
		
		//print_r ($this->statusPosts);
		//exit();
		
		
	}
	
	function add_db (){
		
		if(is_array($this->statusPosts)){
			foreach($this->statusPosts as $post){
				$tag = add_event::get_tag ($this->dbl, $post['description']);//get tag
				$location = add_event::get_location ($this->dbl, $post['description']);//get location
				// debug: echo "This is location: ".$location."<br />";
				// debug: echo "This is tag: ".$tag."<br />";
				if (!empty($tag['t']) && !empty($location)):
					$post['description'] = substr($post['description'],0,300)."...";
					add_event::store($this->dbl, 'Facebook', $post['statusid'], $tag, $post['description'], $location, $post['pubdate']); //store facebook update
				endif;
				
				//echo '<li><p>'.$post['tweetid'].$post['description'].'</p><p class="date">Posted On: '.date('l jS \of F Y h:i:s A',$post['pubdate']).'</p></li>';
			}
		}
		
	}
	
	
} //End class tweets

$report = new status;
$report->add_db ($link);
?>