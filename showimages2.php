<?php
error_reporting(E_ALL);
define ('DefImgNum','5');

require_once 'includes/db.php';
require_once 'includes/common.php';
require_once 'includes/functions.php';
//////////////////////////////////////////

class showimg {
	public $dblink;
	public $search_t;
	
	function __construct($tag="",$location="",$state=""){
		global $link;
		$this->dblink = $link;
		
		$img_num = DefImgNum;
		
		if (!empty($state)):
			$w = " AND locations.state = '".addslash($state)."'";
			$this->get_noisy($this->dblink,$w);
		elseif (!empty($location) && !empty($tag)):
			$w = " AND locations.location = '".addslash($location)."'
			AND tags.tag = '".addslash($tag)."' ";
			$this->get_noisy($this->dblink,$w);
			
			$img_num = ''; // 
		else:
			$this->get_noisy($this->dblink);
		endif;
		
		
		if (!is_array($this->search_t)):
			return FALSE;
		endif;
		
		foreach ($this->search_t as $det){
			echo "
			<div class=\"outimg\">
			  <div class=\"imghead\">".$det['tag']." at ".$det['location']."</div><br />
			  <div class=\"imgbody\">";
			  
			$search_term = $det['keywords']." AND ".$det['location']; // construct search term
			  
			$this->bing_img($search_term,$img_num);
			  
			echo "</div>";
			if ($img_num == 5):
				echo "<div class=\"morelink\"><a href=\"?location=".$det['location']."&tag=".$det['tag']."\">more...</a></div>
				</div>
				";
			endif;
		}
	}
	
	function get_noisy ($link,$where=""){
		$ago = date("Y-m-d H:i:s",strtotime("3 days ago"));//three days ago
		
		
		$sql = "SELECT events.tag, GROUP_CONCAT(DISTINCT keyword SEPARATOR ' OR ') AS keywords, SUM(events.noise) AS noise, locations.location 
		FROM `events`, locations
		WHERE events.logtime > '$ago' ".$where."
		AND events.locationId = locations.id
		AND events.keyword != ''
		GROUP BY `tag`, `location`
		ORDER BY noise DESC
		LIMIT 5";
		
		$result = @mysqli_query($link, $sql);
		
		$i=0;
		while ($rows = @mysqli_fetch_assoc($result)){
			$this->search_t[$i]['tag'] = $rows['tag'];
			$this->search_t[$i]['location'] = $rows['location'];
			$this->search_t[$i]['keywords'] = $rows['keywords'];
			
			$i++;
		}
	}
	
	/////////////////////////////////
	/////////////////////////////////
	function bing_img ($term,$countImage='15'){
	   /* API SETTINGS
		------------------------------------------------------------------------------------------------ */
		$accountKey = 'CR3gwGkOh16K0qlTYIhTYqjyr7Ng3iYD5AHveff1+N4='; //keys changed
		$ServiceRootURL =  'https://api.datamarket.azure.com/Bing/Search/';
		$WebSearchURL = $ServiceRootURL . 'Image?\$format=json&\$Top='.$countImage.'&Adult=%27Off%27&Query='; //top5
		// $WebSearchURL = $ServiceRootURL . 'Image?$format=json&Adult=%27Strict%27&ImageFilters=%27Size%3aMedium%27&Query='; //get max
		$context = stream_context_create(array(
			'http' => array(
				'method'=>'GET',
				// 'proxy' => 'tcp://127.0.0.1:8888',
				// 'proxy' => 'tcp://127.0.0.1:8080',
				'request_fulluri' => true,
				'header'  => "Authorization: Basic " . base64_encode($accountKey . ":" . $accountKey)
			)
		));
		 
		/* SEARCH SETTINGS
		------------------------------------------------------------------------------------------------ */
		//build search query
		//$term = "cars";
		$request = $WebSearchURL . urlencode( '\'' . $term . '\'');
		//debug($request);
		echo $request;
		 
		$response = file_get_contents($request, 0, $context);
		   
		$bingImageResult = json_decode($response);
		//var_dump ($bingImageResult); //Debug
		$count=0;
		
		if(isset($bingImageResult->d->results)){
			foreach($bingImageResult->d->results as $value) {
				if (($count >= 5) && ($countImage == 5)):
					break;
				endif;
				
				if ($i_tburl != $value->Thumbnail->MediaUrl):
					$i_url = $value->MediaUrl;
					$i_title = $value->Title;
					$i_tburl = $value->Thumbnail->MediaUrl;
					
					echo "<div class=\"image\"><a href=\"$i_url\" rel=\"lightbox[events]\" title=\"$i_title\"><img src=\"$i_tburl\" width=\"100\" height=\"100\" border=\"0\" /></a></div>";	
					
					$count++;
				endif;
				////////////////////////////////////////
				////////////////////////////////////////
				//$imageVal ='<img width="'.$value->Thumbnail->Width.'" height="'.$value->Thumbnail->Height.'" alt="'.$getNode->title.'" title="'.$getNode->title.'" src="'.str_replace("&","&amp;",$value->Thumbnail->Url).'" />';
			}
		}
	}
	
	function google_img ($term,$num='8'){
		if (empty($term)):
			return FALSE;
		endif;
		
		$url = "https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=".urlencode($term)."&tbs=qdr:w&rsz=".$num."&safe=off&userip=".$_SERVER['REMOTE_ADDR'];
		//echo $url;
		/*
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_REFERER, 'http://gbege.com'); 
		$body = curl_exec($ch); 
		curl_close($ch); 
		*/
		
		$body = file_get_contents($url);
		// now, process the JSON string 
		$json = json_decode($body, true); 
		
		$i_url = '';
		$i_title = '';
		$i_tburl = '';
		$count = 0;
		
		if (is_array($json)):
			foreach ($json['responseData']['results'] as $jset){
				if (($count >= 5) && ($num == 5)):
					break;
				endif;
				
				if ($i_tburl != $jset['tbUrl']):
					$i_url = $jset['url'];
					$i_title = $jset['titleNoFormatting'];
					$i_tburl = $jset['tbUrl'];
					
					echo "<div class=\"image\"><a href=\"$i_url\" rel=\"lightbox[events]\" title=\"$i_title\"><img src=\"$i_tburl\" width=\"100\" height=\"100\" border=\"0\" /></a></div>";	
					
					$count++;

				endif;
				
			}
		else:
			return FALSE;
		endif;
	}
}

// END CLASS showimage
////////////////////////////////////////
if (!empty($_REQUEST['tag'])):
	$tag = addslash($_REQUEST['tag']);
else:
	$tag = "";
endif;

if (!empty($_REQUEST['location'])):
	$location = addslash($_REQUEST['location']);
else:
	$location = "";
endif;

if (!empty($_REQUEST['state'])):
	$state = addslash($_REQUEST['state']);
else:
	$state = "";
endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Images</title>
<link rel="stylesheet" href="lightbox/css/lightbox.css" type="text/css" media="screen" />

<script type="text/javascript" src="lightbox/js/prototype.js"></script>
<script type="text/javascript" src="lightbox/js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="lightbox/js/lightbox.js"></script>
<link href="assets/css.css" rel="stylesheet" type="text/css" />
<style type="text/css">

.outimg {
	width: 630px;
	position: relative;
	left: 10px;
	clear: both;
	float: left;
	margin-top: 10px;
	padding-left: 5px;
}


.imghead {
	font-size: 16px;
	color: #F00;
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
	display: inline;
	position: relative;
	visibility: visible;
	width: 500px;
	vertical-align: middle;
	top: 0px;
}

.morelink {
	font-size: 11px;
	color: #00F;
	text-align: left;
	width: 100px;
	float: left;
}
.image {
	display: block;
	height: 100px;
	width: 100px;
	border: 1px solid #666;
	overflow: hidden;
	position: relative;
	float: left;
	visibility: visible;
	box-shadow: 2px 2px 2px #888888;
	margin-right: 5px;
	margin-left: 5px;
}
.imgbody {
	width: 100%;
	position: relative;
	float: left;
	visibility: visible;
}
</style>
</head>

<body>

<?php
 $pageimg = new showimg($tag,$location,$state);
 
 
 // Use the comment to hide test below
 /* /////////////////////////////////
?>

<div class="outimg">
  <div class="imghead">Accident at Ikorodu road</div>
  <div class="imgbody">
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  </div>
  <div class="morelink">more...</div>
</div>
<div class="outimg">
  <div class="imghead">Accident at Ikorodu road</div>
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  <div class="image"><img src="assets/logo.png" width="100" height="100" /></div>
  <div class="morelink">more...</div>
</div>

<?php
//*/
?>
</body>
</html>