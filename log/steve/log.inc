<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  log.inc                      *
// * Owner:     Martin Francis               *
// * Created:   2003-09-02 (MF)              *
// * Revised:   2018-11-21 (MF)              *
// *                                         *
// *******************************************

function log_add($stationID, $yyyymmdd, $hhmm, $rx, $notes)
{
    global $ObjMysql;
    $ID =         table_uniqID(TYPE_LOG);
    $sql =
        "INSERT INTO `log` (\n"
        ."  `ID`,\n"
        ."  `stationID`,\n"
        ."  `yyyymmdd`,\n"
        ."  `hhmm`,\n"
        ."  `rx`,\n"
        ."  `notes`\n"
        .")\n"
        ."VALUES (\n"
        ."  '$ID',\n"
        ."  '$stationID',\n"
        ."  '$yyyymmdd',\n"
        ."  '$hhmm',\n"
        ."  '$rx',\n"
        ."  '$notes'\n"
        .")";
    if (!$result = $ObjMysql->query($sql)) {
        print("<b><pre>".$sql."</pre></b><BR>".$ObjMysql->error);
        return false;
    }
    return $ID;
}

function getLogPeriods() {
    global $ObjMysql;
    $sql =
        "SELECT DISTINCT\n"
        ."  SUBSTR(yyyymmdd, 1,6) as yyyymm\n"
        ."FROM\n"
        ."  log\n"
        ."ORDER BY\n"
        ."  yyyymm";

    if (!$result = $ObjMysql->query($sql)) {
        print("<b><pre>".$sql."</pre></b><BR>".$ObjMysql->error);
        return false;
    }
    if (!$result->num_rows) {         // If there was no match, quit.
        return false;
    }
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] =    $row['yyyymm'];
    }
    return $out;
}

function log_for_station($stationID)
{
    global $ObjMysql;
    $sql =
        "SELECT * from `log`\n"
        ."WHERE stationID = 	'$stationID'\n"
        ."ORDER BY yyyymmdd\n";

    if (!$result = $ObjMysql->query($sql)) {
        print("<b><pre>".$sql."</pre></b><BR>".$ObjMysql->error);
        return false;
    }
    if (!$result->num_rows) {         // If there was no match, quit.
        return false;
    }
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] =    $row;
    }
    return $out;
}


// ************************************
// * log_update()                     *
// ************************************
function log_update($ID, $stationID, $yyyymmdd, $hhmm, $rx, $notes)
{
    global $ObjMysql;
    $sql =
        "UPDATE `log`\n"
        ."SET\n"
        ." stationID  =	'$stationID',\n"
        ." yyyymmdd =	'$yyyymmdd',\n"
        ." hhmm =	'$hhmm',\n"
        ." rx =		'$rx',\n"
        ." notes =  	'$notes'\n"
        ."WHERE ID = 	'$ID'\n";

    if (!$result = $ObjMysql->query($sql)) {
        print("<b><pre>".$sql."</pre></b><BR>".$ObjMysql->error);
        return false;
    }
    return true;
}
