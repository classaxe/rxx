<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  log.inc                      *
// * Owner:     Martin Francis               *
// * Created:   02/09/2003 (MF)              *
// * Revised:                                *
// *                                         *
// *******************************************

// ************************************
// * log_add()                        *
// ************************************
function log_add($stationID,$yyyymmdd,$hhmm,$rx,$notes) {
  $ID =			table_uniqID(TYPE_LOG);
  $sql =		 "INSERT INTO `log` (\n"
			."  ID,\n"
			."  stationID,\n"
			."  yyyymmdd,\n"
			."  hhmm,\n"
			."  rx,\n"
			."  notes\n"
			.")\n"
			."VALUES (\n"
			." '$ID',\n"
			."  '$stationID',\n"
			."  '$yyyymmdd',\n"
			."  '$hhmm',\n"
			."  '$rx',\n"
			."  '$notes'\n"
			.")";
  if (!$result = SQL_query($sql)) {
    print("<b><pre>".$sql."</pre></b><BR>".SQL_error());
    return false;
  }
  return $ID;
}



// ************************************
// * log_for_station()                *
// ************************************
function log_for_station($stationID) {
  $sql =		 "SELECT * from `log`\n"
			."WHERE stationID = 	'$stationID'\n"
			."ORDER BY yyyymmdd\n";

  if (!$result = SQL_query($sql)) {
    print("<b><pre>".$sql."</pre></b><BR>".SQL_error());
    return false;
  }
  if (!SQL_num_rows($result)) {			// If there was no match, quit.
    return false;
  }
  $out = array();
  while($row = SQL_fetch_array($result,MYSQL_ASSOC)) {
    $out[] =	$row;
  }
  return $out;
}


// ************************************
// * log_update()                     *
// ************************************
function log_update($ID,$stationID,$yyyymmdd,$hhmm,$rx,$notes) {
  $sql =		 "UPDATE `log`\n"
			."SET\n"
			." stationID  =	'$stationID',\n"
			." yyyymmdd =	'$yyyymmdd',\n"
			." hhmm =	'$hhmm',\n"
			." rx =		'$rx',\n"
			." notes =  	'$notes'\n"
			."WHERE ID = 	'$ID'\n";

  if (!$result = SQL_query($sql)) {
    print("<b><pre>".$sql."</pre></b><BR>".SQL_error());
    return false;
  }
  return true;
}

?>
