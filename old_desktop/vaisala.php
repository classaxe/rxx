<?php
  if ($my_file = file("https://thunderstorm.vaisala.com/tux/jsp/explorer/explorer.jsp")) {
    $my_file =	implode($my_file,"\n");
    eregi("Lex1NationalMap2hourOverlay([^\.]+)",$my_file,$result1);
    eregi("<B>Total Strikes:</B>[[:space:]]*([0-9]+)<BR>[[:space:]]+<B>Time</B>:[[:space:]]+&nbsp;([0-9]+/[0-9]+/[0-9]+[[:space:]]+[0-9]+:[0-9]+:[0-9]+[[:space:]]+GMT)&nbsp;[[:space:]]+<B>to</B>[[:space:]]+&nbsp;([0-9]+/[0-9]+/[0-9]+[[:space:]]+[0-9]+:[0-9]+:[0-9]+[[:space:]]+GMT)",$my_file,$result2);
  }
  print($result1[1]."\n".$result2[1]."\n".$result2[2]."\n".$result2[3]);
?>
 
