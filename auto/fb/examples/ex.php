<?php
//require '../src/facebook.php';


//////////////////////
//////////////////////
define('APPID','447350965281305');
define('APPSECRET','6f947b65249032228ed5213253d4bae8');
define('CANVAS_URL','http://www.gbege.com');
 
$auth_url = "https://www.facebook.com/dialog/oauth?client_id="
. APPID . "&redirect_uri=" . urlencode(CANVAS_URL)."&scope=user_relationships,user_relationship_details,offline_access";
 
$signed_request = $_REQUEST["signed_request"];
list($encoded_sig, $payload) = @explode('.', $signed_request, 2);
//DECODE THE DATA WHICH CONTAINS THE ACCESS TOKEN
$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
if (empty($data["user_id"])) {
    echo("<script> top.location.href='" . $auth_url . "'</script>");
}
 
$access_token=$data["oauth_token"];
?>