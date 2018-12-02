<?php
require_once '../includes/db.php';
require_once '../includes/common.php';
require_once '../includes/functions.php';
////////////////////////////////////
// Get events with more than 10db of noise
class send_noti {
	private $dblink;
	
	private $ev; //events array
	private $users; //Users array
	
	private $loc;
	private $event;
	private $ev_state;
	private $ev_area;
	private $ev_noise;
	private $ev_desc;
	
	private $gen;
	
	function __construct (){
		global $link;
		$this->dblink = $link;
		
		$this->get_events ($link);
		
		//Break the event array down
		foreach ($this->ev as $loc=>$tag_array){
			foreach ($tag_array as $tag=>$det){
				//If noise is not up to 10db, ignore entry
				if ($det['noise'] < 10){ continue;}
				///////////////////////////////////
				$this->loc = $loc;
				$this->event = $tag;
				$this->ev_state = $det['state'];
				$this->ev_area = $det['area'];
				$this->ev_noise = $det['noise'];
				$this->ev_desc = substr ($det['desc'], 0, 500);
				
				//Get subscribers list
				$this->get_subs ($link);
				
				//Send mails
				$this->send_mail();
			}
		}
		
		
	}
	
	function get_events ($link){
		$ago = date("Y-m-d H:i:s",strtotime("2 hours ago"));//three days ago
		
		$sql = "SELECT events.tag, events.noise, events.body, events.locationId, locations.location, locations.state
		FROM `events`, locations
		WHERE events.logtime > '$ago' AND events.locationId = locations.id";
		//
		//echo $sql;
		$result = @mysqli_query($link, $sql);
		
		while ($rows = @mysqli_fetch_assoc($result)){
			$this->ev[$rows['locationId']][$rows['tag']]['state'] = $rows['state'];
			$this->ev[$rows['locationId']][$rows['tag']]['area'] = $rows['location'];
			
			if (!empty($this->ev[$rows['locationId']][$rows['tag']]['noise'])):
				$this->ev[$rows['locationId']][$rows['tag']]['noise'] += $rows['noise']; 
			else:
				$this->ev[$rows['locationId']][$rows['tag']]['noise'] = $rows['noise']; 
			endif;
			
			if (!empty($this->ev[$rows['locationId']][$rows['tag']]['desc'])):
				$this->ev[$rows['locationId']][$rows['tag']]['desc'] .= "<br />\n".$rows['body'];
			else:
				$this->ev[$rows['locationId']][$rows['tag']]['desc'] = "<br />\n".$rows['body'];
			endif;
			
			$this->gen[$rows['location']][$rows['tag']] = "1";
		}
	}
	
	function get_subs ($link){
		$sql = "SELECT `users`.`id`, `name`, `email` 
		FROM `users`, `user_events` 
		WHERE `users`.`id` = `user_events`.`userId` 
		AND `user_events`.`locationId` = '$this->loc'
		AND `users`.`status` = 'active'";
		
		$result = @mysqli_query($link, $sql);
		
		while ($rows = @mysqli_fetch_assoc($result)){
			$this->users[$rows['name']]['email'] = $rows['email'];
			$this->users[$rows['name']]['id'] = $rows['id'];
			
			if (!empty($this->users[$rows['name']]['body'])):
				$this->users[$rows['name']]['body'] .= $this->prep_body();
			else:
				$this->users[$rows['name']]['body'] = $this->prep_body();
			endif;
		}
	}
	
	function prep_body (){
		$body = "<p style=\"font-size: 14px; font-weight: bold; color: #FF0000;\">
		There has been reported $this->event at $this->ev_area<br />
		<span style=\"font-size: 11px; color: #666666;\">$this->ev_desc</span>
		</p>";
		return $body;
	}
	
	function add_noti ($link, $uid){
		$sql = "INSERT INTO `user_notification` SET
		`userId` = '$uid',
		`tag` = 'General',
		`dateSent` = '".date("Y-m-d")."'";
		
		if (@mysqli_query($link, $sql)):
			return TRUE;
		else:
			return FALSE;
		endif;
	}
	
	
	function send_mail (){
		require_once '../includes/smail/class.phpmailer.php';
		//Break user events down
		if (is_array($this->users)):
			foreach ($this->users as $name=>$u_det){
				if (!$this->add_noti($this->dblink,$u_det['id'])):
					continue;
				endif;
				
				$body = "<html><head></head><body>
				<div style=\"font-family: Tahoma, Verdana; font-size: 12px; color: #000000;\">
				<p> Hello $name,<br />
				<br />
				We have notifications for areas of your interest. <br />
				Find below current events, we think you might be interested in.<br />
				</p>";
				$body .= $u_det['body'];
				
				//Get generic reports:
				$body .= "<p>
				<hr />
				<strong>View these and other currently hot locations below:</strong><br />
				<div style=\"font-size: 11px;\">";
				
				foreach ($this->gen as $dloc=>$dtag_array){
					$dtag_str = '';
					foreach ($dtag_array as $dtag=>$dconstant){
						$dtag_str .= ucfirst($dtag) . ", ";
					}
					$dtag_str = substr($dtag_str, 0, -2);
					$body .= $dtag_str. " at " .ucfirst($dloc). "<br />";
				}
				
				$body .= "</div></p>
				<p>
				Feel free to share your comments on these and other events on<br />
				<a href=\"http://www.Gbege.com\">www.Gbege.com</a><br />
				Have a splendid day.
				</p>
				<p>
				The administrator,<br />
				<strong>www.Gbege.com</strong>
				</p>
				</div>
				</body>";
				
				//echo $body;
				//start send mail process;
				$mail = new PHPMailer();
				$mail->IsMail();
				$mail->IsHTML(true);
				$mail->From = 'admin@gbege.com';
				$mail->FromName = "Gbege";
				$mail->AddAddress("$u_det[email]");
				$mail->Subject = 'Current trouble spots in Nigeria';
				$mail->Body  = $body;
		
				if ($mail->Send()):
					$success = 1;
				endif;
				
				//mail trottle
				sleep(1);
			}
		endif;
	}
} //end class send_noti


// Start Notification
$new_noti = new send_noti;
?>