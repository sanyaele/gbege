<?php
// Set Frame Width
if (!empty($_POST['width'])):
	$framewidth = $_POST['width'];
else:
	$framewidth = 300;
endif;

// Set Frame Height
if (!empty($_POST['height'])):
	$frameheight = $_POST['height'];
else:
	$frameheight = 300;
endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="widget.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.codebox {
	font-family: Tahoma, Verdana;
	font-size: 12px;
	height: 40px;
	width: 100%;
	border: 1px solid #333333;
	padding: 5px;
	background-color: #F0F0F0;
}
.style1 {
	font-size: 11px;
	font-weight: bold;
	color: #FFFFFF;
}
.style2 {
	color: #FF0000;
	font-weight: bold;
}
body {
	background-image: url(preview.gif);
	margin: 0px;
}
.style3 {color: #FFFFFF}
-->
</style>
</head>

<body>
<table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center"><table width="94%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="250" height="25" align="center" bgcolor="#333333"><span class="style1">Copy the code Below into your website</span></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><textarea name="iframecode" cols="45" rows="3" class="codebox" id="iframecode"><iframe src="http://gbege.com/widget/?bc=<?php if (!empty($_POST['border_color'])){echo $_POST['border_color'];}?>&amp;hc=<?php if (!empty($_POST['header_color'])){echo $_POST['header_color'];}?>&amp;f=<?php if (!empty($_POST['font'])){echo $_POST['font'];}?>&amp;n=<?php if (!empty($_POST['number'])){echo $_POST['number'];}?>&amp;mh=<?php if (!empty($_POST['show_head'])){echo $_POST['show_head'];}else{echo "off";}?>" frameborder="0" height="<?php echo $frameheight;?>" width="<?php echo $framewidth;?>" scrolling="auto"></iframe></textarea></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="580" border="0" align="center" cellpadding="5" cellspacing="0">
  
  
  
  <tr>
    <td height="40" align="center" bordercolor="#FF0000">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" bordercolor="#FF0000"><iframe src="index.php?bc=<?php if (!empty($_POST['border_color'])){echo $_POST['border_color'];}?>&hc=<?php if (!empty($_POST['header_color'])){echo $_POST['header_color'];}?>&f=<?php if (!empty($_POST['font'])){echo $_POST['font'];}?>&n=<?php if (!empty($_POST['number'])){echo $_POST['number'];}?>&mh=<?php if (!empty($_POST['show_head'])){echo $_POST['show_head'];}else{echo "off";}?>" frameborder="0" height="<?php echo $frameheight;?>" width="<?php echo $framewidth;?>" id="widgetframe" name="widgetframe" scrolling="auto"></iframe>      </td>
  </tr>
</table>
</body>
</html>
