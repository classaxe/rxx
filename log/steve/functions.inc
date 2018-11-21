<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  functions.inc                *
// * Owner:     Martin Francis               *
// * Created:   2003-09-02 (MF)              *
// * Revised:   2018-11-21 (MF)              *
// *                                         *
// *******************************************


function date_timestamp_format_user($timestamp = false) {
    if ($timestamp) {
        return  strftime('%a %d/%m/%Y %H:%M:%S', $timestamp);
    }
    return    strftime('%a %d/%m/%Y %H:%M:%S');
}

function date_timestamp_format_file($timestamp = false) {
// 20020611_1026
    if ($timestamp) {
        return  strftime('%Y%m%d_%H%M', $timestamp);
    }
    return    strftime('%Y%m%d_%H%M');
}


function date_time_seconds_format($seconds) {
    $ss =     "".$seconds % 60;
    if (strlen($ss)<2) {
        $ss = "0".$ss;
    }
    $mm =     "".(($seconds-$ss)/60) % 60;
    if (strlen($mm)<2) {
        $mm = "0".$mm;
    }
    $hh =     "".(($seconds-$mm)/3600) % 24;
    if (strlen($hh)<2) {
        $hh = "0".$hh;
    }
    return $hh.":".$mm.":".$ss;
}


function debug($name, $array) {
    $out =    array();
    $out[] =  "DEBUG: $name\\n\\n";
    $out[] =  "Parameters:\\n";

    for ($i=0; $i<count($array); $i++) {
        $out[] =    "  $i)\\t".$array[$i]."\\n";
    }
    $out[] =  "\\nClick OK to continue.";

    jsAlert(implode("", $out));
    return true;
}


function empty_m() {
    for ($i = 0; $i < func_num_args(); $i++) {
        $test = func_get_arg($i);
        if (empty($test)) {
            return true;
        }
    }
    return false;
}


function init($test) {
    if (!isset($test)) {
        return "";
    }
    return $test;
}


function isset_m() {
    for ($i = 0; $i < func_num_args(); $i++) {
        $test = func_get_arg($i);
        if (!isset($test)) {
            return false;
        }
    }
    return true;
}


function jsAlert($what) {
    $what =   str_replace(chr(13).chr(10), "\\n", $what);
    print
        "<script language='JavaScript'>alert('"
        .htmlspecialchars($what)
        ."')</script>";
}


// ************************************
// * text_table_name()                *
// ************************************
function text_table_name($type)
{
    switch ($type) {
        case TYPE_LOG:
            return 'log';
            break;
        case TYPE_STATION:
            return 'station';
            break;
        default:
            print($type);
            break;
    }
}


// ************************************
// * text_month()                     *
// ************************************
function text_month($mm)
{
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
    return ($months[((int)$mm)-1]);
}


// ************************************
// * yyyymm_inc()                     *
// ************************************
function yyyymm_inc($yyyymm)
{
    $yyyy =   (int) substr($yyyymm, 0, 4);
    $mm =     (int) substr($yyyymm, 4, 2);
    $months = array("01","02","03","04","05","06","07","08","09","10","11","12");
    if ($mm==12) {
        $mm=0;
        $yyyy = $yyyy+1;
    }
    return ($yyyy.$months[$mm]);
}

// ************************************
// * yyyymm_dec()                     *
// ************************************
function yyyymm_dec($yyyymm)
{
    $yyyy =   (int) substr($yyyymm, 0, 4);
    $mm =     (int) substr($yyyymm, 4, 2);
    $months = array("01","02","03","04","05","06","07","08","09","10","11","12");
    if ($mm==1) {
        $mm=13;
        $yyyy = $yyyy-1;
    }
    return ($yyyy.$months[$mm-2]);
}


// ************************************
// * text_lead()                      *
// ************************************
function lead($num)
{
    if ($num<10) {
        return "0".$num;
    }
    return "".$num;
}


function y($var){
    return "<pre>".print_r($var,true)."</pre>";
}