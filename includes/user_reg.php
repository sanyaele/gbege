<?php
function add_user ($link){
	$link1 = $link;
	$id = random_str(6);
	$email = validate_email($_POST['email']);
	$name = addslash($_POST['name']);
	$phone = addslash(substr($_POST['phone'],0,20));
	
	// if there is no valid phone number
	if ((strpos($phone,'234') === false) && (strpos($phone,'080') === false) && (strpos($phone,'070') === false) && (strpos($phone,'081') === false) && (strpos($phone,'071') === false)):
		return "Thank you!";
	endif;
	
	$sql = "INSERT INTO users SET
	id = '$id',
	name = '$name',
	email = '$email',
	phone = '$phone'";
	
	if (@mysqli_query($link, $sql)):
		if (add_event ($link1, $id)):
			return "You have successfully registered and subscribed for notifications";
		else:
			return "You have successfully registered, but no notifications were set";
		endif;
	elseif ($id = get_user ($link, $email, $phone)):
		if (add_event ($link1, $id)):
			return "You have successfully subscribed for another notification";
		else:
			return "No notifications were set";
		endif;
	else:
		return "There was a problem registering you. Please try again";
	endif;
	
	
}

function add_event ($link, $uid) {
	$link1 = $link;
	
	if (!empty($_POST['area'])):
		$location = addslash($_POST['area']);
	else:
		$location = 'Lagos';
	endif;
	if ($locid = verify_loc ($link1, $location)):
		$sql = "INSERT INTO user_events SET
		userId = '$uid',
		locationId = '$locid'";
		//echo $sql;
		//exit();
		if (@mysqli_query($link, $sql)):
			return TRUE;
		else:
			return FALSE;
		endif;
	else:
		return FALSE;
	endif;
}

function get_user ($link, $email, $phone){
	$sql = "SELECT id FROM users WHERE email = '$email' AND phone = '$phone' LIMIT 1";
	$result = @mysqli_query($link, $sql);
	$row = @mysqli_fetch_assoc ($result);
	if (!empty($row['id'])):
		return $row['id'];
	else:
		return FALSE;
	endif;
}

function verify_loc ($link, $loc){
	$sql = "SELECT id FROM locations WHERE location = '$loc' LIMIT 1";
	//echo $sql;
	//exit();
	$result = @mysqli_query($link, $sql);
	$row = @mysqli_fetch_assoc ($result);
	if (!empty($row['id'])):
		return $row['id'];
	else:
		return FALSE;
	endif;
}
?>