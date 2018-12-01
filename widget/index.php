<?php
require_once '../includes/db.php';
require_once '../includes/common.php';
require_once '../includes/functions.php';
//////////////////////////////////////


function get_contributions ($link,$num='10'){
	$result = get_noisy_feed ($link);
	while ($rows = @mysqli_fetch_assoc($result)){
		$title = $rows['tag']." in ".$rows['location'];
		
		echo "<div class=\"widget_inner\">
		<div class=\"widget_header\"><a href=\"http://www.gbege.com/index.php?event=$rows[id]\" target=\"_blank\">$title</a></div>
		<div class=\"widget_content\">$rows[body]<br />
		<strong>Source:</strong> $rows[source] | ".get_date($rows['logtime'])."</div>
	  </div>";
	}
}

//////////////////////////////////////
if (!empty($_GET['bc'])):
	$border_col = $_GET['bc'];
else:
	$border_col = '#7A96DF';
endif;

if (!empty($_GET['hc'])):
	$head_col = $_GET['hc'];
else:
	$head_col = '#003366';
endif;

if (!empty($_GET['f'])):
	$font = $_GET['f'];
else:
	$font = 'Tahoma';
endif;

if (!empty($_GET['n'])):
	$number = substr($_GET['n'],0,2);
else:
	$number = '10';
endif;

if (!empty($_GET['mh']) && $_GET['mh']=='on'):
	$header_v = 'none';
else:
	$header_v = 'block';
endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Gbege.com Widget</title>
<link href="widget.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	font-family: <?php echo $font;?>;
	margin: 0px;
	background-color: #FFFFFF;
}
.widget_outer {
	font-size: 11px;
	width: 99%;
	position: relative;
	float: left;
	overflow: visible;
	height: 100%;
	margin-left: 2px;
	margin-bottom: 3px;
}
.widget_inner {
	border: 1px solid <?php echo $border_col;?>;
	overflow: visible;
	position: relative;
	width: 97%;
	padding: 2px;
	visibility: visible;
	float: left;
	margin-top: 5px;
}
.widget_header {
	font-size: 12px;
	font-weight: bold;
	padding: 2px;
}
.widget_header a {
	text-decoration: none;
	color: <?php echo $head_col;?>;
}
.mainhead {
	font-family: Geneva, Tahoma, Verdana;
	font-size: 14px;
	margin-left: 0px;
	margin-top: 5px;
	display: <?php echo $header_v;?>;
}
.mainhead a{
	text-decoration: none;
	color: #666;
}
.widget_content {
	font-size: 11px;
	padding: 2px;
}
-->
</style>
<meta http-equiv="Refresh" content="300" />
</head>

<body>
<div class="widget_outer">
  <div class="mainhead"><a href="http://www.gbege.com" target="_blank">Current Trouble Spots In Nigeria</a></div>
  <?php get_contributions ($link,$number);?>
</div>
</body>
</html>
