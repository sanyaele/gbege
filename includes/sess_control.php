<?php 
session_start();////////
////////////////////////
require_once 'db.php';
require_once 'common.php';
////==============//////
class sess_control{
	private $email;
	private $password;
	private $dblink;
	
	function __construct(){
		global $link;
		$this->dblink = $link;
		if (!empty($_POST['email']) && !empty($_POST['password'])):
			$this->email = addslash ($_POST['email']);
			$this->password = addslash ($_POST['password']);
		else:
			session_page("Please login to proceed ...");
		endif;
		
		//authenticate
		$this->authenticate($this->dblink);
		
	}
	
	function authenticate ($link){
		$sql = "SELECT * FROM reg WHERE email = '$this->email' AND password = PASSWORD('$this->password') LIMIT 1";
		if (!$result = @mysqli_query ($link, $sql)):
			session_page("There was a problem processing your login request, please try again later.");
		else:
			if (@mysqli_num_rows ($result) < 1):
				session_page("Please provide valid credentials to proceed");
			else:
				$row = mysqli_fetch_assoc ($result);
				if ($row['accountStatus'] != 'active'):
					session_page("Your account is not yet activated, check your email address for the activation link, or contact <a href=\"mailto:support@goldenstepsng.com\">Support</a> to have it resent");
				else:
					// Set session data
					$_SESSION['user_session'] = session_id();
					$_SESSION['id'] = $row["id"];
					$_SESSION['email'] = $this->email;
					$_SESSION['fname'] = $row["fname"];
					$_SESSION['lname'] = $row["lname"];
					$_SESSION['domain'] = $row["domain"];
					$_SESSION['packageType'] = $row["packageType"];
					$_SESSION['defaultMail'] = $row["defaultMail"];
					$_SESSION['domainExpiry'] = $row["domainExpiry"];
					$_SESSION['domainstatus'] = $row["domainStatus"];
					$_SESSION['accountStatus'] = $row["accountStatus"];
					$_SESSION['balance'] = $row["balance"];
					$_SESSION['trial'] = $row["trial"];

				endif;
			endif;
		endif;
	}
}

// If user request log-off //////////////////////////////////////////////////////////////
if (isset ($_REQUEST['logoff'])):
	$_SESSION = array(); 
	session_destroy();
	session_page ("You have Successfully logged off");
endif;

// SEARCH FOR USER SESSION //////////////////////////////////////
if ($_SESSION['user_session'] != session_id()):
	$session = new sess_control();
endif
?>