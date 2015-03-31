<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   RNA / REU / RWW              *
// * Filename:  export.php                   *
// *                                         *
// * Created:   25/04/2004 (MF)              *
// * Revised:   13/02/2005 (MF)              *
// * Email:     martin@classaxe.com          *
// *******************************************
// Note: all functions are declared in alphabetical order


// ************************************
// * export_javascript_DGPS()         *
// ************************************
function export_javascript_DGPS() {
  $out =	array();
  $sql =	 "SELECT\n"
		."  `signals`.*\n"
		."FROM\r\n"
		."  `signals`,\n"
		."   `itu`\n"
		."WHERE\n"
		."  `signals`.`type` = ".DGPS." AND\n"
		."  `signals`.`ITU` = `itu`.`ITU`";
//  $out[] =	"<pre>$sql</pre>";

  $result =	@mysql_query($sql);

  $out[] =	"dgps =	new Array();\r\n";

  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result, MYSQL_ASSOC);
    if (eregi("Ref ID: ([0-9]+)/*([0-9]+)*; *([0-9]+) *bps",$row['notes'],$ID_arr)) {
      $out[] =	"DGPS (\""
		.(count($ID_arr)>1 ? $ID_arr[1] : "")."\",\""
		.(count($ID_arr)>2 ? $ID_arr[2] : "")."\",\""
		.$row['call']."\",\""
		.(float)$row['khz']."\",\""
		.(count($ID_arr)>2 ? $ID_arr[3] : "")."\",\""
		.$row['QTH']."\",\""
		.$row['SP']."\",\""
		.$row['ITU']."\",\""
		.$row['active']."\");\r\n";
    }
  }
  return implode($out,"");
}

function export_kml_signals() {
  global $ID;
  $sql =
     "SELECT\n"
	."  DISTINCT `signals`.*\n"
	."FROM\n"
	."  `signals`,\n"
	."  `logs`\n"
	."WHERE\n"
	."  `signals`.`ID` = `logs`.`signalID` AND\n"
	."  `listenerID` = \"".addslashes($ID)."\"\n"
	."ORDER BY\n"
	."  `khz`,`call`";
  $result =	mysql_query($sql);
  $out =
     "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\">\r\n"
    ."<Document>\r\n"
    ."  <name>Signals Received</name>\r\n";
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    if ((float)$row['lon'] || (float)$row['lat']){
      $out.=
         "  <Placemark>\r\n"
        ."    <name>".html_entity_decode(translate_chars($row['call']),ENT_QUOTES, "UTF-8")." ".(float)$row['khz']."KHz</name>"
  	    ."    <description>".html_entity_decode(translate_chars($row['QTH']),ENT_QUOTES, "UTF-8")." ".$row['SP']." ".$row['ITU']."</description>\r\n"
        ."    <Point><coordinates>".$row['lon'].",".$row['lat']."</coordinates></Point>\r\n"
        ."  </Placemark>\r\n";
    }
  }
  $out.=
       "</Document>\r\n"
      ."</kml>";
  header("Content-Type: application/vnd.google-earth.kml+xml; charset=UTF-8");
  print $out;
}


function export_ndbweblog() {
  global $ID;
  $out =	array();

  $out[] =
     "<html>\r\n"
    ."<head><title>NDB WebLog Export</title>\r\n"
    ."<link href='../assets/style.css' rel='stylesheet' type='text/css' media='screen'>\r\n"
    ."</head>\r\n"
    ."<body><form>\n"
    ."<h1>Download Personalised NDB WebLog Files -<br><font color='red'>for versions up to 1.1.24</font></h1>\r\n"
    ."<p><b>Instructions</b><br>\n"
    ."Thes files are for use with <a href='../../' target='_blank'><b>NDBWebLog</b></a> prior to version 1.1.25. "
    ."Click on each link in turn to download replacements for those included in your NDBWebLog Directory - "
    ."only use this for FRESH installations.</p>\r\n"
    ."<ul>\n"
    ."  <li><b><big>Download <a href='./?mode=export_ndbweblog_config&ID=$ID&save=1'>config.js</a></big></b></li>\r\n"
    ."  <li><b><big>Download <a href='./?mode=export_ndbweblog_log&ID=$ID&save=1'>log.js</a></big></b></li>\r\n"
    ."  <li><b><big>Download <a href='./?mode=export_ndbweblog_stations&ID=$ID&save=1'>stations.js</a></big></b></li>\r\n"
    ."</ul>\n"
    ."<hr><h1>Download Personalised NDB WebLog Files -<br><font color='red'>for versions 1.1.25 and later</font></h1>\r\n"
    ."<p><b>Instructions</b><br>\n"
    ."<font color='#ff0000'><b>Version 1.1.25 is not yet available - DO NOT use files from this section until it is!.</b></p>\r\n"
    ."<ul>\n"
    ."  <li><b><big>Download <a href='./?mode=export_ndbweblog_config&ID=$ID&save=1' onclick=\"alert('Version 1.1.25 has not ben released - do not use this file!')\">config.js</a></big></b></li>\r\n"
    ."  <li><b><big>Download <a href='./?mode=export_ndbweblog_log&ID=$ID&save=1' onclick=\"alert('Version 1.1.25 has not ben released - do not use this file!')\">log.js</a></big></b></li>\r\n"
    ."  <li><b><big>Download <a href='./?mode=export_ndbweblog_stations&ver=1.1.25&ID=$ID&save=1' onclick=\"alert('Version 1.1.25 has not ben released - do not use this file!')\">stations.js</a></big></b></li>\r\n"
    ."</ul>\n"
    ."</body></html>\r\n";
  print implode($out,"");
}



// ************************************
// * export_ndbweblog_config()        *
// ************************************
function export_ndbweblog_config() {
  global $ID;
  $out =	array();

  $sql =	"SELECT * FROM `listeners` WHERE `ID` = \"".addslashes($ID)."\"";
  if (!$result =	mysql_query($sql)) {
    return "Invalid User ID";
  }
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);

  $out[] =	"// ***********************************************************************\r\n";
  $out[] =	"// * FILE HEADER:                                                        *\r\n";
  $out[] =	"// ***********************************************************************\r\n";
  $out[] =	"// * Filename:      config.js                                            *\r\n";
  $out[] =	"// * Generated by:  ".pad(system,7)."                                              *\r\n";
  $out[] =	"// *                                                                     *\r\n";
  $out[] =	"// * This is a user editable file with details on the logbook's owner.   *\r\n";
  $out[] =	"// ***********************************************************************\r\n";
  $out[] =	"\r\n";
  $out[] =	"qth_lat =    ".$row['lat'].";\r\n";
  $out[] =	"qth_lon =    ".$row['lon'].";\r\n";
  $out[] =	"qth_name =   \"".$row['name'].", ".($row['QTH'] ? $row['QTH'].", ":"").($row['SP'] ? $row['SP'].", ":"").$row['ITU']."\";\r\n";
  $out[] =	"qth_email =  \"".$row['email']."\";\r\n";
  $out[] =	"qth_home =   \"".$row['website']."\";\r\n";
  $out[] =	"monthly =    1;\r\n";
  $out[] =	"utc_offset = ".$row['timezone'].";\r\n";
  return implode($out,"");
}



// ************************************
// * export_ndbweblog_index()         *
// ************************************
function export_ndbweblog_index() {
  global $ID;
  $out =	array();

  $out[] =	"<!doctype html public '-//W3C//DTD HTML 4.01 Transitional//EN'>\r\n";
  $out[] =	"<HTML>\r\n";
  $out[] =	"<HEAD>\r\n";
  $out[] =	"<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-1252\">\r\n";
  $out[] =	"<meta http-equiv=\"PICS-Label\" content='(PICS-1.1 \"http://www.rsac.org/ratingsv01.html\" l gen true r (n 0 s 0 v 0 l 0)'>\r\n";
  $out[] =	"<meta http-equiv=\"PICS-Label\" content='(PICS-1.1 \"http://www.classify.org/safesurf/\" l gen true r (SS~~000 1))'>\r\n";
  $out[] =	"<meta name=\"Description\" content=\"NDB WebLog Site\">\r\n";
  $out[] =	"<script language=\"javascript\" type=\"text/javascript\" src=\"./?mode=export_ndbweblog_config&ID=$ID\"></script>\r\n";
  $out[] =	"<script language=\"javascript\" type=\"text/javascript\" src=\"../log/functions.js\"></script>\r\n";
  $out[] =	"<script language=\"javascript\" type=\"text/javascript\" src=\"../log/countries.js\"></script>\r\n";
  $out[] =	"<script language=\"javascript\" type=\"text/javascript\" src=\"./?mode=export_ndbweblog_stations&ID=$ID&ver=1.1.25\"></script>\r\n";
  $out[] =	"<script language=\"javascript\" type=\"text/javascript\" src=\"./?mode=export_ndbweblog_log&ID=$ID\"></script>\r\n";
  $out[] =	"</HEAD>\r\n";
  $out[] =	"<FRAMESET ROWS=\"*\" ONLOAD=\"top.list()\">\r\n";
  $out[] =	"<FRAME NAME=\"main\" src=\"javascript:''\">\r\n";
  $out[] =	"</FRAMESET>\r\n";
  $out[] =	"</HTML>\r\n";
  return implode($out,"");
}



// ************************************
// * export_ndbweblog_log()           *
// ************************************
function export_ndbweblog_log() {
  global $ID;
  $out =	array();

  $sql =	"SELECT\n"
		."  DATE_FORMAT(`logs`.`date`,'%Y%m%d') AS `date`,\n"
		."  `logs`.`time`,\n"
		."  `signals`.`khz`,\n"
		."  `signals`.`call`\n"
		."FROM\n"
		."  `signals`,\n"
		."  `logs`\n"
		."WHERE\n"
		."  `signals`.`ID` = `logs`.`signalID` AND\n"
		."  `listenerID` = \"".addslashes($ID)."\"\n"
		."ORDER BY\n"
		."  `date`,\n"
		."  `time`";
  $result =	mysql_query($sql);

  $out[] =	"// ***********************************************************************\r\n";
  $out[] =	"// * FILE HEADER:                                                        *\r\n";
  $out[] =	"// ***********************************************************************\r\n";
  $out[] =	"// * Filename:      log.js                                               *\r\n";
  $out[] =	"// * Generated by:  ".pad(system,7)."                                              *\r\n";
  $out[] =	"// *                                                                     *\r\n";
  $out[] =	"// * This is a user editable file containing actual log data.            *\r\n";
  $out[] =	"// *                                                                     *\r\n";
  $out[] =	"// * Put logged station details in the following format:                 *\r\n";
  $out[] =	"// * LOG (\"KHz\",\"Call\",\"YYYYMMDD\",\"HHMM\",\"Optional notes on reception\"); *\r\n";
  $out[] =	"// *                                                                     *\r\n";
  $out[] =	"// * If any logged station doesn't appear in stations.js, you will see   *\r\n";
  $out[] =	"// * an error message. Add new stations to the file stations.js          *\r\n";
  $out[] =	"// * Don't use \"quotes\" in the notes field - single 'quotes' are fine.   *\r\n";
  $out[] =	"// ***********************************************************************\r\n";
  $out[] =	"\r\n";
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $out[] =	"LOG (\"".(float)$row['khz']."\",\"".$row['call']."\",\"".$row['date']."\",\"".$row['time']."\",\"\");\r\n";
  }
  return implode($out,"");
}



// ************************************
// * export_ndbweblog_stations()      *
// ************************************
function export_ndbweblog_stations() {
  global $ID,$ver,$noheader;
  $out =	array();

  $sql =
     "SELECT\n"
	."  DISTINCT `signals`.*\n"
	."FROM\n"
	."  `signals`,\n"
	."  `logs`\n"
	."WHERE\n"
	."  `signals`.`ID` = `logs`.`signalID` AND\n"
	."  `listenerID` = \"".addslashes($ID)."\"\n"
	."ORDER BY\n"
	."  `khz`,`call`";
  $result =	mysql_query($sql);

  if (!isset($noheader)) {
    $out[] =
       "// ***********************************************************************\r\n"
      ."// * FILE HEADER:                                                        *\r\n"
      ."// ***********************************************************************\r\n"
      ."// * Filename:      stations.js                                          *\r\n"
      ."// * Generated by:  ".system."                                                  *\r\n"
      ."// *                                                                     *\r\n"
      ."// * This is a user editable file containing actual log data.            *\r\n"
      ."// *                                                                     *\r\n"
      ."// * Put station details in the following format:                        *\r\n"
      ."// * STATION(khz,call,qth,ste,cnt,cyc,daid,lsb,usb,pwr,lat,lon,notes);   *\r\n"
      ."// *                                                                     *\r\n"
      ."// * Each field should be enclosed with quotes and set to \"\" if unknown. *\r\n"
      ."// * For any given signal:                                               *\r\n"
      ."// *   KHz     is the frequency of the carrier;                          *\r\n"
      ."// *   call    is the callsign -                                         *\r\n"
      ."// *           Indicate DGPS Idents with # before station ident number   *\r\n"
      ."// *   qth     is the town in which the signal is located;               *\r\n"
      ."// *   ste     is the state or province abbreviation (eg MI = Michigan)  *\r\n"
      ."// *           or \"\" if not applicable (e.g. Bahamas)                    *\r\n"
      ."// *   cnt     is the NDB List approved country code;                    *\r\n"
      ."// *   cyc     is the number of seconds between repetitions of the call  *\r\n"
      ."// *   daid    stands for 'Dash after ID' and is either \"Y\" or \"N\"       *\r\n"
      ."// *   lsb     is the offset of the lower sideband from the carrier      *\r\n"
      ."// *           (Note Canadian NDBs are USB only, for these set to \"\")    *\r\n"
      ."// *   usb     is the offset of the upper sideband from the carrier      *\r\n"
      ."// *   pwr     is the power in watts of the transmitter                  *\r\n"
      ."// *   lat     is the decimal latitude value (S values are negative)     *\r\n"
      ."// *   lon     is the decimal longitude value (W values are negative)    *\r\n"
      ."// *   notes   These notes will show with each logging of the station.   *\r\n"
      ."// ***********************************************************************\r\n"
      ."\r\n";
  }
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $out[] =	"STATION (\"".(float)$row['khz']."\","
		."\"".$row['call']."\","
		."\"".translate_chars($row['QTH'])."\","
		."\"".$row['SP']."\","
		."\"".$row['ITU']."\","
		."\"".$row['sec']."\","
		."\"".($row['format']=="DAID" ? "Y" : "N")."\","
		."\"".$row['LSB']."\","
		."\"".$row['USB']."\","
		."\"".($row['pwr'] ? $row['pwr'] : "")."\","
		."\"".$row['lat']."\","
		."\"".$row['lon']."\","
		."\"".translate_chars($row['notes'])."\""
        .($ver=='1.1.25' ? ",\"".$row['ID']."\"" : "")
        .($ver=='1.1.25' ? ",\"".$row['active']."\"" : "")
        .");\r\n";
  }
  return implode($out,"");
}

function export_text_signals() {
  global $ID;
  $sql =
     "SELECT\n"
	."  DISTINCT `signals`.*\n"
	."FROM\n"
	."  `signals`,\n"
	."  `logs`\n"
	."WHERE\n"
	."  `signals`.`ID` = `logs`.`signalID` AND\n"
	."  `listenerID` = \"".addslashes($ID)."\"\n"
	."ORDER BY\n"
	."  `khz`,`call`";
  $result =	mysql_query($sql);
  $out = "KHz\tCall\tQTH\tSP\tITU\tSec\tFmt\tLSB\tUSB\tPwr\tLat\tLon\r\n";
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $out.=
       (float)$row['khz']."\t"
	  .htmlentities(translate_chars($row['call']))."\t"
	  .htmlentities(translate_chars($row['QTH']))."\t"
	  .$row['SP']."\t"
	  .$row['ITU']."\t"
	  .$row['sec']."\t"
	  .$row['format']."\t"
	  .$row['LSB']."\t"
	  .$row['USB']."\t"
	  .$row['pwr']."\t"
	  .$row['lat']."\t"
	  .$row['lon']."\r\n";
  }
  print $out;
}



// ************************************
// * export_text_log()                *
// ************************************
function export_text_log() {
  global $ID, $mode;

  set_time_limit(600);	// Extend maximum execution time to 10 mins

  $sql = "SELECT\n"
		."  MAX(LENGTH(`signals`.`call`)) AS `call_len`,\n"
		."  MAX(LENGTH(`signals`.`QTH`))  AS `QTH_len`,\n"
		."  MAX(LENGTH(`logs`.`time`))    AS `time_len`,\n"
		."  LENGTH(MAX(`logs`.`lsb`))     AS `LSB_len`,\n"
		."  LENGTH(MAX(`logs`.`usb`))     AS `USB_len`\n"
		."FROM\n"
		."  `signals`,\n"
		."  `logs`\n"
		."WHERE\n"
		."  `signals`.`ID` = `logs`.`signalID` AND\n"
		."  `listenerID` = \"".addslashes($ID)."\"\n"
		."ORDER BY\n"
		."  `date`,\n"
		."  `time`";

  $result =	mysql_query($sql);
  $row =	mysql_fetch_array($result, MYSQL_ASSOC);
  $call_len =	$row['call_len'];
  $QTH_len =	$row['QTH_len'];
  $time_len =	$row['time_len'];
  $LSB_len =	$row['LSB_len'];
  $USB_len =	$row['USB_len'];

  $sql =	"SELECT * FROM `listeners` WHERE `ID` = \"".addslashes($ID)."\"";
  if (!$result =	mysql_query($sql)) {
    return "Invalid User ID";
  }
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);

  print
     "<pre>".system." Log for ".$row['name']." on ".date("Y-m-d")."\r\n"
    ."Output sorted by Date\r\n"
    ."----------------------------------------------------------------------\r\n"
    ."YYYYMMDD ".($time_len ? "UTC  " : "")."KHz   ".(system=="RWW" ? "   " : "").pad("ID",$call_len+1).($LSB_len||$USB_len ? "LSB   USB   " : "")."KM    Miles PWR  GSQ    SP ITU Location\r\n"
    ."----------------------------------------------------------------------\r\n";

  $sql =	"SELECT\n"
		."  DATE_FORMAT(`logs`.`date`,'%Y%m%d') AS `date`,\n"
		."  `logs`.`time`,\n"
		."  `logs`.`LSB`,\n"
		."  `logs`.`USB`,\n"
		."  `logs`.`dx_km`,\n"
		."  `logs`.`dx_miles`,\n"
		."  `signals`.`call`,\n"
		."  `signals`.`SP`,\n"
		."  `signals`.`ITU`,\n"
		."  `signals`.`khz`,\n"
		."  `signals`.`QTH`,\n"
		."  `signals`.`pwr`,\n"
		."  `signals`.`GSQ`\n"
		."FROM\n"
		."  `signals`,\n"
		."  `logs`\n"
		."WHERE\n"
		."  `signals`.`ID` = `logs`.`signalID` AND\n"
		."  `listenerID` = \"".addslashes($ID)."\"\n"
		."ORDER BY\n"
		."  `logs`.`date`,`logs`.`time`, `signals`.`khz`";
  $result =	mysql_query($sql);


  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    print
       $row['date']." "
      .($time_len ? pad($row['time'],5) : "")
      .pad((float)$row['khz'],(system=='RWW' ? 9 : 6))
      .pad($row['call'],$call_len)." "
      .($LSB_len || $USB_len ? pad($row['LSB'],6).pad($row['USB'],6) : "")
      .pad($row['dx_km'],6)
      .pad($row['dx_miles'],6)
      .pad(($row['pwr']?$row['pwr']:""),5)
      .pad($row['GSQ'],7)
      .pad($row['SP'],3)
      .$row['ITU']." ".$row['QTH']."\r\n";
  }
  print
    "----------------------------------------------------------------------\r\n"
   .mysql_num_rows($result)." logs listed\r\n\r\n"
   ."Output generated by ".system." 'Listener Log Export' feature.\r\n"
   .system_URL."/?mode=$mode&ID=$ID\r\n</pre>";
}


function export_signallist_excel() {
  set_time_limit(600);	// Extend maximum execution time to 10 mins
  global $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment;filename=export_".system.".xls");
  $filter_type =	array();
  if (!($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
     $type_NDB = 1;
  }
  if ($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
    if ($type_NDB) {
      $filter_type[] =	 "`type` = ".NDB;
    }
    if ($type_DGPS) {
      $filter_type[] =	 "`type` = ".DGPS;
    }
    if ($type_TIME) {
      $filter_type[] =	 "`type` = ".TIME;
    }
    if ($type_HAMBCN) {
      $filter_type[] =	 "`type` = ".HAMBCN;
    }
    if ($type_NAVTEX) {
      $filter_type[] =	 "`type` = ".NAVTEX;
    }
    if ($type_OTHER) {
      $filter_type[] =	 "`type` = ".OTHER;
    }
  }
  $filter_type =	"(".implode($filter_type," OR ").")";
  switch (system) {
    case "RNA":
      $filter_system_SQL =			"(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
    break;
    case "REU":
      $filter_system_SQL =			"`heard_in_eu` = 1";
    break;
    case "RWW":
      $filter_system_SQL =			"1";
    break;
  }
  $sql =
     "SELECT\r\n"
	."  DISTINCT `signals`.*\r\n"
    ."FROM\r\n"
    ."  `signals`\r\n"
    ."WHERE\n  $filter_system_SQL\r\n"
    .($filter_type ? " AND\n  $filter_type" : "")
    ." ORDER BY `active` DESC,`khz` ASC, `call` ASC";
  $result = 	@mysql_query($sql);

  print
     "<html><head><title>".system."</title></head>\r\n"
    ."<table border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspacing=\"0\">\r\n"
    ."  <tr bgcolor=\"#c0c0c0\">\r\n"
    ."    <th>KHZ</th>\r\n"
    ."    <th>ID</th>\r\n"
    ."    <th>LSB</th>\r\n"
    ."    <th>USB</th>\r\n"
    ."    <th>Sec</th>\r\n"
    ."    <th>Fmt</th>\r\n"
    ."    <th>QTH</th>\r\n"
    ."    <th>S/P</th>\r\n"
    ."    <th>ITU</th>\r\n"
    ."    <th>GSQ</th>\r\n"
    ."    <th>Pwr</th>\r\n"
    ."    <th>Notes</th>\r\n"
    ."    <th>Heard In</th>\r\n"
    ."    <th>Logs</th>\r\n"
    ."    <th>Last Heard\r\n";

  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $color =	"";
    if (substr($row["call"],0,1)=="#") {
      $color =	" bgcolor=\"#00ccff\" title=\"DGPS Station\"";
    }
    $bgcolor =	"";
    if (!$row["active"]) {
      $bgcolor =	" bgcolor=\"#d0d0d0\" title=\"(Reportedly off air or decommissioned)\"";
    }
    else {
      switch ($row["type"]) {
        case NDB:	    $bgcolor = "";									break;
        case DGPS:	    $bgcolor = " bgcolor=\"#00D8ff\" title=\"DGPS Station\"";			break;
        case TIME:	    $bgcolor = " bgcolor=\"#FFE0B0\" title=\"Time Signal Station\"";		break;
        case NAVTEX:	$bgcolor = " bgcolor=\"#FFB8D8\" title=\"NAVTEX Station\"";			break;
        case HAMBCN:	$bgcolor = " bgcolor=\"#D8FFE0\" title=\"Amateur signal\"";			break;
        case OTHER:	    $bgcolor = " bgcolor=\"#B8F8FF\" title=\"Other form of transmission\"";		break;
      }
    }
    print
       "<tr$bgcolor>"
	  ."<td>".$row["khz"]
	  ."<td>".$row["call"]
	  ."<td align=\"right\" x:num>".$row["LSB_approx"].($row["LSB"]!="" ? $row["LSB"] : "&nbsp;")
	  ."<td align=\"right\" x:num>".$row["USB_approx"].($row["USB"]!="" ? $row["USB"] : "&nbsp;")
	  ."<td align=\"right\" x:num>".($row["sec"] ? $row["sec"] : "&nbsp;")
	  ."<td align=\"right\" x:num>".($row["format"] ? $row["format"] : "&nbsp;")
	  ."<td>".($row["QTH"] ?  $row["QTH"] :   "&nbsp;")
	  ."<td>".($row["SP"]?    $row["SP"]  :   "&nbsp;")
	  ."<td>".$row["ITU"]
	  ."<td>".($row["GSQ"] ?  $row["GSQ"] :   "&nbsp;")
	  ."<td align=\"right\" x:num>".($row["pwr"]?$row["pwr"]:"&nbsp;")
	  ."<td>".($row["notes"]?stripslashes($row["notes"]):"&nbsp;")
	  ."<td>".($row["heard_in"]?$row["heard_in"]:"&nbsp;")
	  ."<td>".($row["logs"]?$row["logs"]:"&nbsp;")
	  ."<td align=\"right\" x:num>".($row["last_heard"]?$row["last_heard"]:"&nbsp;")."\r\n";
  }
}


// ************************************
// * ILGRadio_signallist()            *
// ************************************
function ILGRadio_signallist() {
  set_time_limit(600);	// Extend maximum execution time to 10 mins
  $out =	array();

  switch (system) {
    case "RNA":	$filter_system_SQL = "(`heard_in_na` = 1 OR `heard_in_ca` = 1)";	break;
    case "REU":	$filter_system_SQL = "(`heard_in_eu` = 1)";				break;
    default:	$filter_system_SQL = "(1)";						break;
  }

  $sql =	 "SELECT\n"
		."  ROUND(`khz`,1) AS `khz`,\n"
		."  `call`,\n"
		."  `QTH`,\n"
		."  `pwr`,\n"
		."  `notes`,\n"
		."  TRIM(CONCAT(`ITU`,\" \",`SP`)) AS `ITU`,\n"
		."  `lat`,\n"
		."  `lon`\n"
		."FROM\n"
		."  `signals`\n"
		."WHERE\n"
		."   $filter_system_SQL AND\n"
		."   `active` = 1 AND\n"
        ." `khz` < 99999\n" // ILG DB can't handle frequencies above this with decimal place
		."ORDER BY\n"
		."  `khz`,\n"
		."  `call`";

  $result =	@mysql_query($sql);

  $arr_search = array(  "DGPS; Ref ID: ");
  $arr_replace= array(  "");

  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $out[] =	 lead($row['khz'],7)
		.pad($row['call'],22)
		."0000-2400"
		."1234567"
		.pad("",18)
		.pad("",48)
		.pad("",8)
		.pad(substr(str_replace($arr_search,$arr_replace,translate_chars($row['QTH'])),0,24),25)
		.pad("",7)
		.pad("",3)
		.pad(substr(str_replace($arr_search,$arr_replace,translate_chars($row['notes'])),0,14),15)
		.pad("",1)
		.pad("",18)
		.pad("",30)
		.pad("",5)
		.pad("",3)
		.pad($row['ITU'],18)
		.pad("",6)
		.pad("",5)
		.pad("",20) //not 1
		.pad("",20)
		." "
		."\r\n";
  }
  print implode($out,"");
}




?>
