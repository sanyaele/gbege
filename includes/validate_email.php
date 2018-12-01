<?php
function validate_email ($address)
	{
		// check address format
		$address = stripslashes($address);
		if (!ereg ("^.+@.+\\..+$", $address) || empty ($address)) return FALSE;
		if (eregi ("\r", $address) || eregi ("\n", $address)) return FALSE;
		
		// safestripslashes
		return $address;
	}
?>