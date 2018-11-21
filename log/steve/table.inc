<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  table.inc                    *
// * Owner:     Martin Francis               *
// * Created:   2003-09-02 (MF)              *
// * Revised:   2018-11-21 (MF)              *
// *                                         *
// *******************************************

function table_delete_record($destinationID, $destinationType)
{
    global $ObjMysql;

    $destinationType =    text_table_name($destinationType);
    $sql =
        "DELETE FROM\n"
        ."  `$destinationType`\n"
        ."WHERE\n"
        ."  ID = '$destinationID'";

    if (!$result = $ObjMysql->query($sql)) {
        print($ObjMysql->error);
        return false;
    }
    return true;
}

function table_exists_record($destinationID, $destinationType)
{
    global $ObjMysql;

    $destinationType =    text_table_name($destinationType);
    $sql =
        "SELECT\n"
        ."  COUNT(*)\n"
        ."FROM\n"
        ."  `$destinationType`\n"
        ."WHERE\n"
        ."  ID = '$destinationID'";
    if (!$result = $ObjMysql->query($sql)) {
        print($ObjMysql->error);
        return false;
    }
    $row = $result->fetch_row();
    return $row[0]>0;
}

function table_get($destinationID, $destinationType)
{
    global $ObjMysql;

    $destinationType =    text_table_name($destinationType);
    $sql =
        "SELECT\n"
        ."  *\n"
        ."FROM\n"
        ."  `$destinationType`\n"
        ."WHERE\n"
        ."  ID = '$destinationID'";

    if (!$result = $ObjMysql->query($sql)) {
        print($ObjMysql->error);
        return false;
    }
    if (!$result->num_rows) {         // If there was no match, quit.
        return false;
    }
    return $result->fetch_assoc();
}

function table_get_all($destinationType, $orderBy = false)
{
    global $ObjMysql;

    $destinationType =    text_table_name($destinationType);
    $sql =
        "SELECT\n"
        ."  *\n"
        ."FROM\n"
        ."  `$destinationType`\n"
        .($orderBy ? "ORDER BY '$orderBy'" : '');

    $result = $ObjMysql->query($sql);

    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }

    return $out;
}

function table_uniqID($destinationType)
{
    $notDone =        true;
    $n = 0 ;
    while ($notDone) {
        $ID =       uniqid('');
        $notDone =      table_exists_record($ID, $destinationType);
    }
    return $ID;
}
