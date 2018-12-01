<?php
require_once 'includes/db.php';
require_once 'includes/common.php';
require_once 'includes/functions.php';
///////////////////////////////////

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Event details</title>
<link href="assets/css.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body {
	background-color: #FFF;
	margin-top: 10px;
	margin-right: 0px;
	margin-bottom: 0px;
	margin-left: 7px;
}
</style>
</head>

<body>
<?php
get_src_events ($link, $_GET['location'], $_GET['tag'], $_GET['loc']);
/*
<div class="around">This is a sample. We automatically gather information from Twitter and other open sources, and collate this information to form intelligence that points out trouble spots in Nigeria, grouped by individual states and type of problem.</div>
				<div class="source">Twitter</div><div class="dtime">20 minutes ago</div>
*/
?>
<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" 
        type="text/javascript">
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30599375-1']);
  _gaq.push(['_setDomainName', 'gbege.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
