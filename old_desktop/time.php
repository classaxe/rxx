<?php
// ************************************
// * php_version_check()              *
// ************************************
function php_version_check($value) { 
  if (!ereg("([^\.]+).([^\.]+).([^\.]+)", phpversion(), $phpVersion)) {
    ereg("([^\.]+).([^\.]+)", phpversion(), $phpVersion);
  }
  ereg("([^\.]+).([^\.]+).([^\.]+)", $value, $value);
  if ($phpVersion[1]<$value[1]) {  return false; }
  if ($phpVersion[2]<$value[2]) {  return false; }
  if ($phpVersion[3]<$value[3]) {  return false; }
  return true;
}

if (php_version_check("4.1.0")) {
  extract($_REQUEST);		// Extracts all request variables (GET and POST) into global scope.
}

  global $query,$CiID,$type,$country;
  $url =	"http://www.worldtimezone.com/time/"
		.($query || $type ? "wtzsearch.php?forma=&query=".urlencode($query):"")
		.($type ? "&type=".urlencode($type):"")
		.($country ? "&country=".urlencode($country):"")
		.($CiID ? "wtzresult.php?forma=24h&CiID=$CiID" : "");
  if ($my_file = file($url)) {
    $my_file =	implode($my_file,"\n");
    eregi("<!-- MAIN start worldtimezone\.com --\>([^\!]*)<!-- MAIN end worldtimezone\.com -->",$my_file,$result);
  }
  $script =	getenv("SCRIPT_NAME");
  $page =	str_replace("wtzsearch.php",$script,$result[1]);
  $page =	str_replace("wtzresult.php",$script,$page);
  $page =	str_replace("<img src=\"map.php?Longitude=","Lon: ",$page);
  $page =	str_replace("&Latitude=","<br>Lat: ",$page);
  $page =	str_replace("\" alt=\"","<!-- ",$page);
  print($page);
?>
