<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  station.inc                  *
// * Owner:     Martin Francis               *
// * Created:   01/09/2003 (MF)              *
// * Revised:                                *
// *                                         *
// *******************************************

// ************************************
// * station_add()                    *
// ************************************
function station_add($khz, $call, $qth, $sta, $cnt, $lat, $lon)
{
    global $ObjMysql;
    $ID =         table_uniqID(TYPE_STATION);
    $sql =
        "INSERT INTO\n"
        ."  `station`\n"
        ."( `ID`,\n"
        ."  `khz`,\n"
        ."  `call`,\n"
        ."  `qth`,\n"
        ."  `sta`,\n"
        ."  `cnt`,\n"
        ."  `lat`,\n"
        ."  `lon`\n"
        .")\n"
        ."VALUES\n"
        ."( '$ID',\n"
        ."  '$khz',\n"
        ."  '$call',\n"
        ."  '$qth',\n"
        ."  '$sta',\n"
        ."  '$cnt',\n"
        ."  '$lat',\n"
        ."  '$lon'\n"
        .")";
    if (!$result = $ObjMysql->query($sql)) {
        print("<b><pre>".$sql."</pre></b><BR>".$ObjMysql->error);
        return false;
    }
    return $ID;
}

// ************************************
// * station_update()                 *
// ************************************
function station_update($ID, $khz, $call, $qth, $sta, $cnt, $lat, $lon)
{
    global $ObjMysql;
    $sql =
        "UPDATE `station`\n"
        ."SET\n"
        ." `khz`  = '$khz',\n"
        ." `call` = '$call',\n"
        ." `qth`  =  '$qth',\n"
        ." `sta`  =  '$sta',\n"
        ." `cnt`  =  '$cnt',\n"
        ." `lat`  =  '$lat',\n"
        ." `lon`  =  '$lon'\n"
        ."WHERE ID = '$ID'\n";

    if (!$result = $ObjMysql->query($sql)) {
        print("<b><pre>".$sql."</pre></b><BR>".$ObjMysql->error);
        return false;
    }
    return true;
}
