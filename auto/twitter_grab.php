<?php

///////////////////////////////////
require_once '../includes/db.php';
require_once '../includes/common.php';
require_once '../includes/functions.php';
require_once '../includes/add_event.php';
////////////////////////////////////
class tweets extends add_event {
	private $dblink;
	private $twitterPosts;
	function __construct (){
		global $link;
		$this->dblink = $link;
		
		/*// Debug: fake data
		//Sample 1
		$d['title'] = 'Sample1';
		$d['description'] = 'Serious rioting at Oshodi, please avoid';
		$d['pubdate'] = strtotime(date("Y-m-d"));
		$d['guid'] = 'GUID1';
		$d['link'] = 'http://www.ex.com';
		$d['user'] = 'Me';
		$d['tweetid'] = 'guid1';
		$this->twitterPosts[]=$d;
		
		//
		//Sample 2
		$d['title'] = 'Sample2';
		$d['description'] = 'There is an accident on awolowo road, fatal';
		$d['pubdate'] = strtotime(date("Y-m-d"));
		$d['guid'] = 'GUID2';
		$d['link'] = 'http://www.ex2.com';
		$d['user'] = 'You';
		$d['tweetid'] = 'guid2';
		$this->twitterPosts[]=$d;
		/////////////
		/////////////
		//*/
		$twitterRssFeedUrl =  "http://search.twitter.com/search.rss?q=".get_twitter_tags ($this->dblink)."%20geocode:6.4530556,3.3958333,50km";
		$twitterUsername = "Gbege2";
		$amountToShow = 500;
		$twitterPosts = false;
		$xml = @simplexml_load_file($twitterRssFeedUrl);
		if(is_object($xml)){
			foreach($xml->channel->item as $twit){
				if(is_array($twitterPosts) && count($twitterPosts)==$amountToShow){
					break;
				}
				$d['title'] = stripslashes(htmlentities($twit->title,ENT_QUOTES,'UTF-8'));
				$description = stripslashes($twit->description);
				if(strtolower(substr($description,0,strlen($twitterUsername))) == strtolower($twitterUsername)){
					$description = substr($description,strlen($twitterUsername)+1);
				}
				$d['description'] = $description;
				$d['pubdate'] = strtotime($twit->pubDate);
				$d['guid'] = stripslashes(htmlentities($twit->guid,ENT_QUOTES,'UTF-8'));
				$d['link'] = stripslashes(htmlentities($twit->link,ENT_QUOTES,'UTF-8'));
				$d['user'] = $twitterUsername;
				$break_guid = explode("/",$d['guid']);
				$d['tweetid'] = end ($break_guid);
				
				$this->twitterPosts[]=$d;
			}
		}else{
			die('cannot connect to twitter feed');
		}
		//*/
		
	}
	
	function add_db (){
		
		if(is_array($this->twitterPosts)){
			foreach($this->twitterPosts as $post){
				$tag = add_event::get_tag ($this->dblink, $post['description']);//get tag
				$location = add_event::get_location ($this->dblink, $post['description']);//get location
				// debug: 
				echo "This is location: ".$location."<br />";
				// debug: 
				echo "This is tag: ".$tag."<br />";
				if (!empty($location)):
					add_event::store($this->dblink, 'Twitter', $post['tweetid'], $tag, $post['description'], $location, $post['pubdate']); //store twitter update
				endif;
				
				//echo '<li><p>'.$post['tweetid'].$post['description'].'</p><p class="date">Posted On: '.date('l jS \of F Y h:i:s A',$post['pubdate']).'</p></li>';
			}
		}
		
	}
	
	
} //End class tweets

$report = new tweets;
$report->add_db ($link);
?>