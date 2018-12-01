<?
$accountKey = 'CR3gwGkOh16K0qlTYIhTYqjyr7Ng3iYD5AHveff1+N4=';
$q = $_GET['q'];
if (get_magic_quotes_gpc()) $q = stripslashes($q);
// note that $q is unsafe!
?>

<form method="get" action="">
  <input name="q" type="text" value="<?=htmlentities($q)?>" />
  <input type="submit" value="Search" />
</form>

<?
function sitesearch ($query, $site, $accountKey, $count=NULL){
  // code from http://go.microsoft.com/fwlink/?LinkID=248077
  $ServiceRootURL =  'https://api.datamarket.azure.com/Bing/Search/';
  $WebSearchURL = $ServiceRootURL . 'Web?$format=json&Query=';
  $context = stream_context_create(array(
    'http' => array(
      'request_fulluri' => true,       
      'header'  => "Authorization: Basic " . base64_encode($accountKey . ":" . $accountKey)
    ) 
  )); 
  $request = $WebSearchURL . urlencode("'$query site:$site'"); // note the extra single quotes
  if ($count) $request .= "&\$top=$count"; // note the dollar sign before $top--it's not a variable!
  return json_decode(file_get_contents($request, 0, $context));
}

function showresponse ($q, $next, $results){
  $count = count($results);
  if ($count==0){
    $resultwords = 'No results';
  }else if ($next){
    $resultwords = "More than $count results";
  }else if ($count == 1){
    $resultwords = '1 result';
  }else{
    $resultwords = "$count results";
  }
  echo "<p>$resultwords found for <strong>".htmlentities($q)."</strong></p>\n";
  if ($count == 0) return;
  echo "<dl>\n";
  foreach ($results as $result) echo "<dt><a href='{$result->Url}'>{$result->Title}</a></dt>\n<dd>{$result->Description}</dd>\n";
  echo "</dl>\n";    
}

if ($q){
  // get search results
  $result = sitesearch ($q, $_SERVER['HTTP_HOST'], $accountKey, 10);
  showresponse($q, $result->d->{__next}, $result->d->results);
  $searchURL = 'http://www.bing.com/search?q='.urlencode("$q site:{$_SERVER['HTTP_HOST']}");
  echo "<p><a href='$searchURL'>See all results</a><p>\n";
}
?>