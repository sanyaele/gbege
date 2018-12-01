<?php

////////////////////////
require_once 'includes/db.php';

require_once 'includes/common.php';
///////////////////////////////////

/* E X A M P L E -----------------------------------------------
		$feed = new RSS();
		$feed->title       = "RSS Feed Title";
		$feed->link        = "http://website.com";
		$feed->description = "Recent articles on your website.";

		$db->query($query);
		$result = $db->result;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$item = new RSSItem();
			$item->title = $title;
			$item->link  = $link;
			$item->setPubDate($create_date); 
			$item->description = "<![CDATA[ $html ]]>";
			$feed->addItem($item);
		}
		echo $feed->serve();
	---------------------------------------------------------------- */

	class RSS
	{
		var $title;
		var $link;
		var $description;
		var $language = "en-us";
		var $pubDate;
		var $items;
		var $tags;

		function RSS()
		{
			$this->items = array();
			$this->tags  = array();
		}

		function addItem($item)
		{
			$this->items[] = $item;
		}

		function setPubDate($when)
		{
			if(strtotime($when) == false)
				$this->pubDate = date("D, d M Y H:i:s ", $when) . "GMT";
			else
				$this->pubDate = date("D, d M Y H:i:s ", strtotime($when)) . "GMT";
		}

		function getPubDate()
		{
  			if(empty($this->pubDate))
				return date("D, d M Y H:i:s ") . "GMT";
			else
				return $this->pubDate;
		}

		function addTag($tag, $value)
		{
			$this->tags[$tag] = $value;
		}

		function out()
		{
			$out  = $this->header();
			$out .= "<channel>\n";
			$out .= "<title>" . $this->title . "</title>\n";
			$out .= "<link>" . $this->link . "</link>\n";
			$out .= "<description>" . $this->description . "</description>\n";
			$out .= "<language>" . $this->language . "</language>\n";
			$out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

			foreach($this->tags as $key => $val) $out .= "<$key>$val</$key>\n";
			foreach($this->items as $item) $out .= $item->out();

			$out .= "</channel>\n";
			
			$out .= $this->footer();

			$out = str_replace("&", "&amp;", $out);

			return $out;
		}
		
		function serve($contentType = "application/xml")
		{
			$xml = $this->out();
			header("Content-type: $contentType");
			echo $xml;
		}

		function header()
		{
			$out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			$out .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
			return $out;
		}

		function footer()
		{
			return '</rss>';
		}
	} // End class RSS

	class RSSItem
	{
		var $title;
		var $link;
		var $description;
		var $pubDate;
		var $guid;
		var $tags;
		var $attachment;
		var $length;
		var $mimetype;

		function RSSItem()
		{ 
			$this->tags = array();
		}

		function setPubDate($when)
		{
			if(strtotime($when) == false)
				$this->pubDate = date("D, d M Y H:i:s ", $when) . "GMT";
			else
				$this->pubDate = date("D, d M Y H:i:s ", strtotime($when)) . "GMT";
		}

		function getPubDate()
		{
			if(empty($this->pubDate))
				return date("D, d M Y H:i:s ") . "GMT";
			else
				return $this->pubDate;
		}

		function addTag($tag, $value)
		{
			$this->tags[$tag] = $value;
		}

		function out()
		{
			$out .= "<item>\n";
			$out .= "<title>" . $this->title . "</title>\n";
			$out .= "<link>" . $this->link . "</link>\n";
			$out .= "<description>" . $this->description . "</description>\n";
			$out .= "<pubDate>" . $this->getPubDate() . "</pubDate>\n";

			if($this->attachment != "")
				$out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";

			if(empty($this->guid)) $this->guid = $this->link;
			$out .= "<guid>" . $this->guid . "</guid>\n";

			foreach($this->tags as $key => $val) $out .= "<$key>$val</$key\n>";
			$out .= "</item>\n";
			return $out;
		}

		function enclosure($url, $mimetype, $length)
		{
			$this->attachment = $url;
			$this->mimetype   = $mimetype;
			$this->length     = $length;
		}
	} // End class RSSItem
	
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////
	$feed = new RSS();
	$feed->title       = "Current Problem Spots in NIgeria";
	$feed->link        = "http://gbege.com";
	$feed->description = "View socially circulated trouble hotspots in Nigeria";


//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
function get_noisy ($link){ //Required title, event_id, timedate, description
	$result = get_noisy_feed ($link);
	
	$i=0;
	while ($rows = @mysqli_fetch_assoc($result)){
		$d[$i]['title'] = $rows['tag']." in ".$rows['location'];
		$d[$i]['event_id'] = $rows['id'];
		$d[$i]['description'] = $rows['body']." | Source: ".$rows['source'];
		$d[$i]['timedate'] = $rows['logtime'];
		
		$i++;
	}
	if (!empty($d[0])):
		return $d;
	else:
		return FALSE;
	endif;
}
//////////////////////////////////////////////////////////////////

$details = get_noisy ($link); 


if (is_array($details)):
	foreach ($details as $ind_det){
		$item = new RSSItem();
		$item->title = htmlspecialchars($ind_det['title'], ENT_QUOTES, 'utf-8');
		$item->link  = "http://www.gbege.com/index.php?event=$ind_det[event_id]";
		$item->setPubDate(strtotime($ind_det['timedate'])); 
		$item->guid = $ind_det['event_id'];
		$item->description = "<![CDATA[".$ind_det['description']."]]> ";
		$feed->addItem($item);
	}
	
	echo $feed->serve();
endif;
		
		
?>