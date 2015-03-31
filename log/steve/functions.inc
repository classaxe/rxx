<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  functions.inc                *
// * Owner:     Martin Francis               *
// * Created:   02/09/2003 (MF)              *
// * Revised:                                *
// *                                         *
// *******************************************


// ***********************************************************************
// * DESCRIPTION:                                                        *
// ***********************************************************************
//   Various useful pure PHP extensions and interface functions
//   No code here which affects or reads from any table!

// ***********************************************************************
// * CHANGES:                                                            *
// ***********************************************************************

// ***********************************************************************
// * ISSUES:                                                             *
// ***********************************************************************

// ***********************************************************************
// * FUNCTION LIST:                                                      *
// ***********************************************************************
//  All functions are defined in alphabetical order.
//
//  S =		date_timestamp_format_user([$timestamp])
//		  Returns formatted timestamp			(e.g. - Tue 11/06/2002 10:26:09)
//
//  S =		date_timestamp_format_file([$timestamp])
//		  Returns formatted timestamp for file naming	(e.g. - 20020611_1026)
//
//  S =		date_time_seconds_format($seconds)
//		  Returns formatted hours, mins and seconds	(e.g. - 91:23:12)
//
//  B =		debug($name,$array)
//		  Produces JS alert listing all passed parameters. Returns true.
//
//  B =		empty_m(...args...)
//		  A multiple version of empty()
//		  Checks multiple variables (call with @ to avoid warning)
//		  Returns true if ANY variable given is empty
//
//  S =		init($test)
//                Checks if variable is set.  Returns value of variable or "" if not set
//
//  B =		isset_m(...args...)
//		  A multiple version of isset()
//		  Checks multiple variables (call with @ to avoid warning)
//		  Returns false if ANY variable given is not set
//
//  Void =	jsAlert($what)
//		  Prints javascript alert box - quotes are converted to HTML entities
//		  (doesn't look good in an alert box - maybe this need to be re-coded)
//
//  Void =	jsAlertParams(...$args)
//		  Prints javascript alert box with all parameters listed in args
//
//  S =		text_error_severity($errorSeverity)
//		  Returns text equivalent for error severity
//
//  S =		text_error_type($errorType)
//		  Returns text equivalent for error type
//
//  S =		text_table_name($table)
//		  Returns name of table for given resource type



// ************************************
// * date_timestamp_format_user()     *
// ************************************
function date_timestamp_format_user($timestamp=false) {
// Later can be adjusted for user's time zone and preferences etc.
  if ($timestamp) {
    return	strftime('%a %d/%m/%Y %H:%M:%S',$timestamp);
  }
  return	strftime('%a %d/%m/%Y %H:%M:%S');
}


// ************************************
// * date_timestamp_format_file()     *
// ************************************
function date_timestamp_format_file($timestamp=false) {
// 20020611_1026
  if ($timestamp) {
    return	strftime('%Y%m%d_%H%M',$timestamp);
  }
  return	strftime('%Y%m%d_%H%M');
}


// ************************************
// * date_time_seconds_format()       *
// ************************************
function date_time_seconds_format($seconds) {
  $ss =		"".$seconds % 60;
  if (strlen($ss)<2) {
    $ss = "0".$ss;
  }
  $mm =		"".(($seconds-$ss)/60) % 60;
  if (strlen($mm)<2) {
    $mm = "0".$mm;
  }
  $hh =		"".(($seconds-$mm)/3600) % 24;
  if (strlen($hh)<2) {
    $hh = "0".$hh;
  }
  return $hh.":".$mm.":".$ss;
}



// ************************************
// * debug()                          *
// ************************************
function debug($name,$array) {
// Call like this:
// if ($user['PermDebug'])
// { $args =	func_get_args();
//   debug("groups_report()",$args); }
//
  $out =	array();
  $out[] =	"DEBUG: $name\\n\\n";
  $out[] =	"Parameters:\\n";

  for ($i=0; $i<count($array); $i++) {
    $out[] =	"  $i)\\t".$array[$i]."\\n";
  }
  $out[] =	"\\nClick OK to continue.";

  jsAlert(implode("",$out));
  return true;
}



// ************************************
// * empty_m()                        *
// ************************************
// Checks that every parameter given is empty:
function empty_m() {
  for ($i = 0; $i < func_num_args(); $i++) {
    $test = func_get_arg($i);
    if (empty($test)) {
      return true;
    }
  }
  return false;
}



// *************************************
// * init()                            *
// *************************************
// Checks if paramameter is set, and if not sets it to a null_string
function init($test) {
  if (!isset($test)) {
    return "";
  }
  return $test;
}



// ************************************
// * isset_m()                        *
// ************************************
// Checks that every parameter given is set:
function isset_m()
{ for ($i = 0; $i < func_num_args(); $i++)
  { $test = func_get_arg($i);
    if (!isset($test))
      return false;
  }
  return true;
}



// ************************************
// * jsAlert()                        *
// ************************************
function jsAlert($what) {
  $what =	str_replace(chr(13).chr(10),"\\n",$what);
  print(	"<script language='JavaScript'>alert('"
		.htmlspecialchars($what)
		."')</script>");
}


// ************************************
// * jsAlertParams()                  *
// ************************************
function jsAlertParams() {
  $out = array();
  for ($i=0; $i<func_num_args(); $i++) {
    $out[] = "\$".$i." = ".func_get_arg($i)."\\n";
  }
  jsAlert(implode("",$out));
}


// ************************************
// * pad_percent()                    *
// ************************************
function pad_percent($value) { 
  $value =	'   '.(round($value*100));
  $padded =	substr($value,strlen($value)-3,3);
  return	(ereg_replace(' ','&nbsp;',$padded));
}



// ************************************
// * php_version_check()              *
// ************************************
function php_version_check($value) { 
  if (!ereg("([^\.]+).([^\.]+).([^\.]+)", phpversion(), $phpVersion)) {
    ereg("([^\.]+).([^\.]+)", phpversion(), $phpVersion);
  }

  ereg("([^\.]+).([^\.]+).([^\.]+)", $value, $value);

  if ($phpVersion[1]<$value[1]) {
    return false;
  }
  if ($phpVersion[2]<$value[2]) {
    return false;
  }
  if ($phpVersion[3]<$value[3]) {
    return false;
  }
  return true;
}



// ************************************
// * text_error_severity()            *
// ************************************
function text_error_severity($severity) {
  switch($severity) {
    case ALL:		return "ALL";
    case WARN:		return "WARN";
    case ERROR:		return "ERROR";
    case CRITICAL:	return "CRITICAL";
    default:		return txt("error");
  }
}


// ************************************
// * text_table_name()                *
// ************************************
function text_table_name($type) {
  switch ($type) {
    case TYPE_LOG:		return 'log';		break;
    case TYPE_STATION:		return 'station';	break;
    default: print($type); break;
  }
}


// ************************************
// * text_month()                     *
// ************************************
function text_month($mm) {
  $months =	array("January","February","March","April","May","June","July","August","September","October","November","December");
  return ($months[((int)$mm)-1]);
}


// ************************************
// * yyyymm_inc()                     *
// ************************************
function yyyymm_inc($yyyymm) {
  $yyyy =	(int) substr($yyyymm,0,4);
  $mm =		(int) substr($yyyymm,4,2);
  $months =	array("01","02","03","04","05","06","07","08","09","10","11","12");
  if ($mm==12) {
    $mm=0;
    $yyyy = $yyyy+1;
  }
  return ($yyyy.$months[$mm]);
}

// ************************************
// * yyyymm_dec()                     *
// ************************************
function yyyymm_dec($yyyymm) {
  $yyyy =	(int) substr($yyyymm,0,4);
  $mm =		(int) substr($yyyymm,4,2);
  $months =	array("01","02","03","04","05","06","07","08","09","10","11","12");
  if ($mm==1) {
    $mm=13;
    $yyyy = $yyyy-1;
  }
  return ($yyyy.$months[$mm-2]);
}


// ************************************
// * text_lead()                      *
// ************************************
function lead($num) {
  if ($num<10) {
    return "0".$num;
  }
  return "".$num;
}



?>
