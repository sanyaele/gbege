<?php // common.inc 
function error($msg) { 
   echo "
   <html> 
   <head> 
   
   <script language=\"JavaScript\"> 
   <!-- 
       alert(\"$msg\"); 
       history.back(); 
   //--> 
   </script> 
   </head> 
   <body> 
   </body> 
   </html> 
   ";
   exit(); 
} // End of error
#############################################################################
function addslash ($value,$allowed_tags="") { // Add slashes if magic_quotes_gpc is off (i.e. 0)
	/*
	if (!get_magic_quotes_gpc()):
	   return addslashes(htmlspecialchars ($str));
	else:
		if(strpos(str_replace("\'",""," $str"),"'")!=false):
			return addslashes(htmlspecialchars ($str));
		else:
			return $str;
		endif;
	endif;
	*/
	
	if(get_magic_quotes_gpc($value) || strpos(str_replace("\'",""," $value"),"'")==false)
	{
	$value=stripslashes($value);
	}
	$value=strip_tags($value,$allowed_tags);
	//$value=htmlspecialchars($value);
	$value=addslashes($value);
	return $value;
}
#############################################################################
function random_str($numchar) {
   $str = bin2hex( md5( time(), TRUE ) );
   $start = mt_rand(1, (strlen($str)-$numchar));
   $suff_str = str_shuffle($str);
   $encr_str = substr($suff_str,$start,$numchar);
   return($encr_str);
}
#############################################################################
function validate_email ($address){
	// check address format
	$address = stripslashes($address);
	if (!preg_match ('/^.+@.+\\..+$/', $address) || empty ($address)) return FALSE;
	if (preg_match ('/\r/i', $address) || preg_match ('/\n/i', $address)) return FALSE;
	
	return $address;
}
#############################################################################
function session_page ($error_mess) {
	if (isset ($_COOKIE['email'])):
		$email = $_COOKIE['email'];
		$password = $_COOKIE['password'];
	elseif (isset ($_POST['username'])):
		$email = $_POST['email'];
		$password = $_POST['password'];
	endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>User Login</title>
<style type="text/css">
<!--
.style1 {
	color: #FFFF00;
	font-weight: bold;
}
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: small;
}
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: small;
}
h1 {
	font-size: large;
}
.style3 {
	color: #009900;
	font-weight: bold;
}
.style30 {	color: #FF0000;
	font-size: 10px;
}
	-->
	</style>
</head>
	
	<body>
	<h1 align="center">&nbsp;<?php echo $error_mess; ?>	</h1>
	<p align="center"><span class="top_bar"><a href="index.php">Home</a></span></p>
	<table width="300" border="0" align="center" cellpadding="4" cellspacing="0" class="style2">
	  <tr>
		<td align="left" valign="top" bgcolor="#006600"><span class="style1">Login </span></td>
	  </tr>
	  <tr>
		<td align="right" valign="top" bgcolor="#99FF66"><form id="hosts" name="hosts" method="post" action="<?php if (!empty ($_REQUEST['logoff'])){ echo "index.php"; } ?>">
		    
		  <p>
		    Email
			  <input name="email" type="text" id="email" value="<?php echo $email; ?>" size="25" />
		  </p>
	  <p>Password
		  <input name="password" type="password" id="password" value="<?php echo $password; ?>" size="25" />
		  </p>
		  <p><span class="style30">
		    <input name="seenlogin" type="hidden" id="seenlogin" value="1" />
Forgot password, <a href="http://personalogs.com/resetpass.php">reset here</a>.</span><br />
			<input type="submit" name="Submit" value="Submit" />
		  </p>
		  </form></td>
	  </tr>
	</table>
</body>
	</html>
	<?php
	exit ();
}

function admin_session_page ($error_mess) {
	if (isset ($_COOKIE['username'])):
		$username = $_COOKIE['username'];
		$password = $_COOKIE['password'];
	elseif (isset ($_POST['username'])):
		$username = $_POST['username'];
		$password = $_POST['password'];
	endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MyNigerianBlog Admin panel</title>
<style type="text/css">
<!--
.style1 {
	color: #FFFF00;
	font-weight: bold;
}
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: small;
}
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: small;
}
-->
</style>
</head>

<body>
<h1 align="center">&nbsp;<?php echo $error_mess; ?>	</h1>
<p align="center"><span class="top_bar"><a href="index.php">Home</a></span></p>
<table width="300" border="0" align="center" cellpadding="4" cellspacing="0" class="style2">
  <tr>
	<td align="left" valign="top" bgcolor="#006600"><span class="style1">Login </span></td>
  </tr>
  <tr>
	<td align="left" valign="top" bgcolor="#99FF66"><form id="hosts" name="hosts" method="post" action="<?php if (!empty ($_REQUEST['logoff'])){ echo "home.php"; } ?>">
	  <p>
        Username
        <input name="username" type="text" id="username" value="<?php echo $username; ?>" size="25" />
      </p>
	  <p>Password
		  <input name="password" type="password" id="password" value="<?php echo $password; ?>" size="25" />
		  </p>
	  <p>
		<input type="submit" name="Submit" value="Submit" />
	  </p>
	  </form></td>
  </tr>
</table>
</body>
</html>
<?php
exit ();
}
?>