<?php


function is_nempty($param)
{
	if (!empty($param)) {
		return true; 
   }else{
		return false;
	}
 }
 
function is_integer($param)
{
	if (preg_match ("/^([0-9]+)$/", $param)) {
		return true; 
   }else{
		return false;
	}
}

function validate_email ($address)
{
	// check address format
	$address = stripslashes($address);
	if (!ereg ("^.+@.+\\..+$", $address) || empty ($address)) return FALSE;
	if (eregi ("\r", $address) || eregi ("\n", $address)) return FALSE;
	
	return $address;
}


function is_phone($param)
{
	if (preg_match("/^([0-9\-\ ]+)$/", $param)){
		return true;
	}else{
		return false;
	}
}

function is_l_phone($param)
{
	if (preg_match("/^([0-9]{2,2}-[0-9]{7,7})$/", $param)){
		return true;
	}else{
		return false;
	}
}


function is_m_phone($param)
{
	if (preg_match("/^(080[2-7]{1,1}[0-9]{7,7})$/", $param)){
		return true;
	}else{
		return false;
	}
}
?>