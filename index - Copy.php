<?php
require_once 'includes/db.php';
require_once 'includes/common.php';
require_once 'includes/functions.php';

function summary ($link, $state, $tag=""){
	if (empty($state)):
		$state = "Lagos";
	endif;
	$result = get_events ($link, $state, $tag);
	$report = array();
	while ($rows = mysqli_fetch_assoc($result)){
		//echo "codeAddress('Location', 'Tags','Content',infowindow)\n";
		if (isset($report [$rows['location']])):
			$report [$rows['location']] .= ", ".$rows['tag'];
		else:
			$report [$rows['location']] = "<b>".$rows['location'].":</b> ".$rows['tag'];
		endif;
		
	}
	
	if (isset($report)):
		foreach ($report as $detail){
			echo $detail."<br />\n";
		}
	endif;
}

function get_event ($link, $event){
	$det = "";
	$eid = addslash($event);
	$sql = "SELECT events.id, events.tag, events.locationId, events.logtime, events.body, events.source, locations.location, locations.state FROM `events`, `locations`
			WHERE events.locationId = locations.id
			AND events.id = '$eid'
			LIMIT 1";
	$result = @mysqli_query ($link, $sql);
	if ($rows = @mysqli_fetch_assoc($result)):
		$det['formated'] = "<div class=\"around\">$rows[body]</div>
		<div class=\"source\">$rows[source]
			<div class=\"dtime\">".get_date($rows['logtime'])."</div>
			<div class=\"map\"><a href=\"map.php?location=".$rows['state']."\" target=\"main\">MAP</a></div>
			
			<div class=\"share\"> 
				<a name=\"fb_share\" type=\"icon\" class=\"fb\"></a> 
				<a href=\"https://twitter.com/share?url=".urlencode("http://gbege.com/index.php?event=".$rows['id'])."&text=".urlencode($rows['body'])."\" target=\"_blank\"><img src=\"assets/tw.png\" border=\"0\" /></a></div>
		</div>\n";
		$det['locid'] = $rows['locationId'];
		$det['desc'] = $rows['body'];
		$det['title'] = $rows['tag']." at ".$rows['location'];
		
		return $det;
	endif;
	
	
}


////////////////////////////////////////
////////////////////////////////////////
if (!empty($_GET['location'])):
	$location = $_GET['location'];
else:
	$location = "";
endif;

if (!empty($_GET['tag'])):
	$tag = $_GET['tag'];
else:
	$tag = "";
endif;

if (!empty($_GET['loc'])):
	$loc = $_GET['loc'];
else:
	$loc = "";
endif;
//////////////////////////////////
if (!empty($_POST['seenform'])):
	if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['phone'])):
		require_once 'includes/user_reg.php';
		$mess = add_user ($link);
	else:
		$mess = "You are required to fill all fields";
	endif;
endif;
// If user requests specific event
if (!empty($_REQUEST['event'])):
	$ev = get_event ($link, $_REQUEST['event']); // Return a location so the Source Iframe below can show only information about that particular location
	if (!empty($ev['locid'])):
		$loc = $ev['locid'];
	endif;
endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" itemscope itemtype="http://schema.org/Organization">
<head profile="http://www.w3.org/2005/10/profile">
<link rel="icon" type="image/png" href="favicon.png" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="1800" />
<!-- Add the following three tags inside head -->
<meta itemprop="name" content="Trouble Hotspots in Nigeria">
<meta itemprop="description" content="View troble hotspots in Nigeria">
<meta itemprop="image" content="http://gbege.com/assets/gbege.png">
<?php
if (!empty($ev['title']) && !empty($ev['desc'])):
?>
<!-- Facebook Meta tags -->
<meta property="og:title" content="<?php echo $ev['title'];?>" />
<meta property="og:description" content="<?php echo $ev['desc'];?>" />
<meta property="og:image" content="" />
<?php
endif; // End Facebook Meta If
?>
<title>View Trouble spots in Nigeria | Gbege.com</title>
<link rel="alternate" type="application/rss+xml" href="http://feeds.feedburner.com/gbege" title="Current trouble spots in Nigeria" />
<link href="assets/css.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style3 {
	color: #666666;
	font-size: 10px;
}
.style4 {
	color: #FF9900;
	font-weight: bold;
}
.style7 {color: #00FFFF; font-weight: bold; font-size: 10px; font-family: Verdana, Arial, Helvetica, sans-serif; }
.email {
	color: #FFF;
}
.style10 {
	color: #FFFFFF;
	font-family: Tahoma, Verdana;
	font-size: 20px;
	letter-spacing: 1px;
}
.style11 {
	font-size: 11px;
	color: #FFFFFF;
	font-family: Tahoma, Verdana, Arial;
	font-weight: bold;
}
.style12 {
	color: #FFFF00;
	font-family: Tahoma, Verdana, Arial;
	font-size: 12px;
	font-weight: bold;
}
-->
</style>
<script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>

<body>
<table width="1000" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="26%"><a href="http://www.gbege.com"><img src="assets/gbege.png" alt="NaijaHotSpots" width="250" height="74" border="0" /></a></td>
    <td colspan="2" align="right" valign="middle">
	<script type="text/javascript"><!--
					google_ad_client = "pub-2732469417891860";
					/* 728x90, created 10/5/09 */
					google_ad_slot = "9338416308";
					google_ad_width = 728;
					google_ad_height = 90;
					//-->
					</script>
    <script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script></td>
  </tr>
  <tr>
    <td height="28" align="left" valign="top">&nbsp;</td>
    <td align="center" valign="top">&nbsp;</td>
    <td align="left" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top"><div class="topdiv">
      <h1>Trouble Hotspots in Nigeria <a href="http://feeds.feedburner.com/gbege" title="Subscribe to Gbege.com feed" rel="alternate" type="application/rss+xml"><img src="assets/rss.png" alt="RSS" width="20" height="20" border="0" /></a></h1>
      <p>Discover trouble spots in Nigeria right here on Gbege.com, we continously harvest reports from the social media around the clock, thus providing mostly unbiased accounts of happenings in and around Nigeria. Welcome to our version of the curated web. See problem, crime, and disaster spots in Nigeria on the Google Map. Click on each marker to see more information about the location, and what is happening where.</p>
    </div>
      <div id="middiv" class="middiv"><span class="style7">Trouble Spots Include:</span>
          <?php
		  $tag_result = get_tags ($link);
			$tag_str = "";
			while ($tag_rows = mysqli_fetch_assoc($tag_result)){
				$tag_str .= "$tag_rows[tag], ";
			}
			echo substr($tag_str,0,-2);
		  ?>
          <br />
          <span class="style7">Locations searched include:</span>
          <?php
		  $loc_result = get_locations ($link, "", "ORDER BY RAND() LIMIT 20");
			$loc_str = "";
			while ($loc_rows = mysqli_fetch_assoc($loc_result)){
				$loc_str .= "$loc_rows[location], ";
			}
			echo substr($loc_str,0,-2);
		  ?>
      </div>
	  
	  <script type="text/javascript">
	<!--
	var _adynamo_client = "8637338b-d6d9-4af4-bbc0-e9d9b274caab";
	var _adynamo_width = 250;
	var _adynamo_height = 250;
	//-->
        </script>
              <script type="text/javascript" src="http://static.addynamo.net/ad/js/deliverAds.js"></script>			  </td>
    <td width="53%" align="center" valign="top"><table width="450" border="0" cellpadding="1" cellspacing="0">
      <tr>
        <td width="76%"><div id="searchdiv" style="white-space:nowrap;">
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get" name="search" id="search">

              <select name="location" class="formbox" id="location">
                <option selected="selected" value="">All Locations</option>
                <?php get_state_opt ($link);?>
              </select>
              <select name="tag" class="formbox" id="tag">
                <option selected="selected" value="">All Events</option>
                <?php get_tags_opt ($link);?>
              </select>
              <label>
              <input name="search2" type="submit" class="formbutton" id="search2" value="Search" />
              </label>
            </form>
        </div></td>
        <td width="24%" align="right"><!-- Place this tag where you want the +1 button to render -->
            <g:plusone></g:plusone>
            <!-- Place this render call where appropriate -->
            <script type="text/javascript">
          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
      </tr>
    </table>
      <div id="showmap">
        <iframe frameborder="0" id="main" name="main" width="450" height="200" scrolling="Auto" src="map.php?location=<?php echo $location;?>&amp;tag=<?php echo $tag;?>"></iframe>
    </div>
	<div id="altmap">
              <?php
			summary ($link, $location, $tag);
			?>
      </div>
      
      <table width="450" border="0" cellspacing="0" cellpadding="2">
        
        <tr>
          <td align="center" valign="top" bordercolor="#8DBA72" bgcolor="#000000" style="background-image:url(assets/sub_back1.gif); background-repeat:no-repeat;"><form id="subscribe" name="subscribe" method="post" action="">
		  <?php
		  if (!empty($mess)):
		  ?>
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td align="center"><span class="style12"><?php echo $mess;?></span></td>
              </tr>
            </table>
		<?php
		endif;
		?>
            <span class="style10">Send Me Important Notifications </span><br />
            <table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#000000">
              <tr>
                <td width="9%" align="right"><span class="style11">Name</span></td>
                <td width="24%" align="left"><span class="style11">
                  <input name="name" type="text" class="sub_box" id="name" value="<?php if (!empty($_POST['name'])){ echo $_POST['name'];}?>" />
                </span></td>
                <td width="9%" align="right"><span class="style11">Email</span></td>
                <td width="24%" align="left"><span class="style11">
                  <input name="email" type="text" class="sub_box" id="email" value="<?php if (!empty($_POST['email'])){ echo $_POST['email'];}?>" />
                </span></td>
                <td width="10%" align="right"><span class="style11">Phone</span></td>
                <td width="24%" align="left"><span class="style11">
                  <input name="phone" type="text" class="sub_box" id="phone" value="<?php if (!empty($_POST['phone'])){ echo $_POST['phone'];}?>" />
                </span></td>
              </tr>
              <tr>
                <td colspan="6" align="center"><span class="style11">
                  <select name="area" class="boxblack" id="area">
                    <option value="<?php echo $location;?>">Select an Area of Interest</option>
                    <?php
	if  (!empty($location)):
		$state = $location;
	else:
		$state = 'Lagos';
	endif;
  $result = get_locations ($link, $state);
  
  while ($rows = @mysqli_fetch_assoc($result)){
  	echo "<option value=\"$rows[location]\">$rows[location]</option>\n";
  }
  ?>
                  </select>
                  <input name="notifyme" type="image" id="notifyme" src="assets/notify.gif" />
                  <input name="seenform" type="hidden" id="seenform" value="1" />
                </span></td>
                </tr>
            </table>
            </form></td>
        </tr>
        <tr>
          <td>
		  <div id="disqus_thread"></div>
              <script type="text/javascript">
            /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
            var disqus_shortname = 'gbege'; // required: replace example with your forum shortname
			var disqus_identifier = '<?php echo date("W-Y"); ?>';
			var disqus_url = 'http://gbege.com/index.php';

            /* * * DON'T EDIT BELOW THIS LINE * * */
            (function() {
                var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
            })();
        </script>
              <noscript>
                Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a>
            </noscript>
            <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>		  </td>
        </tr>
      </table></td>
    <td width="21%" align="left" valign="top">
    <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fgbegenaija" scrolling="No" frameborder="0" style="border:none; width:250px; height:100px;"></iframe><br />
	<?php
		if (!empty($ev['formated'])):
			echo $ev['formated'];
        endif;
        ?>
	
	<br />
	<iframe frameborder="0" id="side" name="side" width="250px" height="500" scrolling="Auto" src="source.php?location=<?php echo $location;?>&amp;tag=<?php echo $tag;?>&amp;loc=<?php echo $loc;?>" style="overflow-x: hidden;"></iframe>
	<br />
	<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td align="center"><a href="#"> <img src="assets/showimg.gif" alt="Show related images" width="203" height="58" border="0" onclick="MM_openBrWindow('showimages.php?state=<?php echo $location;?>&amp;tag=<?php echo $tag;?>','showimg','toolbar=yes,scrollbars=yes,width=500,height=500')" /></a></td>
      </tr>
    </table>
	</td>
  </tr>
  <tr>
    <td colspan="3" align="center" valign="middle">
	<script type="text/javascript"><!--
        google_ad_client = "ca-pub-2732469417891860";
        /* Blacklink */
        google_ad_slot = "5221291766";
        google_ad_width = 728;
        google_ad_height = 15;
        //-->
        </script>
        <script type="text/javascript"
        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>	</td>
  </tr>
  <tr>
    <td colspan="3" align="center" valign="middle"><p class="style3">&copy; Copyright 2012,<span class="style4"> GoldenSteps Enterprises</span>. All Rights Reserved | <strong>Contact Email: <span class="email">admin&copy;gbege.com</span>.</strong></p></td>
  </tr>
</table>

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
