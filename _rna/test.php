<?php 

$mode = "YYYYDDMM";
$mode = "YYYYMMDD";


switch ($mode) {
  case "YYYYMMDD":
    eval("function parse(\$in){ return \"Yes: \$in\"; }");
  break;
  case "YYYYDDMM":
    eval("function parse(\$in){ return \"No: \$in\"; }");
  break;
}

echo parse("here we are");

?>

