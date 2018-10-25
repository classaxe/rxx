<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 8:46 PM
 */

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
/*
Version History:
  2015-01-17
    1) export_ndbweblog_stations() now escapes embedded quotes for notes
  2013-12-21
    1) export_ndbweblog_stations() now always includes id and active flags
    2) If there are more than one possible match for ID and frequency, a ;ID suffix is
       added to the station in both the log entries and station entries
  2013-12-16
    1) Moved show_pdf() from functions.php into here as export_pdf()
    2) Now includes active flag in excel export and supports display of absolute
       offsets for sidebands in both Excel and PDF exports
       (Requested by Joachim Rabe, email sent Mon 2013-12-16 2:58 AM)
  2013-11-30
    1) Added support for export of type DSC in excel format (Thanks Vincent!)
*/

namespace Rxx\Tools;

/**
 * Class Export
 * @package Rxx\Tools
 */
class Export
{
    /**
     * @return string
     */
    public static function export_javascript_DGPS()
    {
        $out =  array();
        $sql =
            "SELECT\n"
            ."  `signals`.*\n"
            ."FROM\r\n"
            ."  `signals`,\n"
            ."   `itu`\n"
            ."WHERE\n"
            ."  `signals`.`type` = ".DGPS." AND\n"
            ."  `signals`.`ITU` = `itu`.`ITU`";
//  $out[] =	"<pre>$sql</pre>";

        $result =   @\Rxx\Database::query($sql);

        $out[] =    "dgps =	new Array();\r\n";

        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            if (preg_match("/Ref ID: ([0-9]+)\/*([0-9]+)*; *([0-9]+) *bps/i", $row['notes'], $ID_arr)) {
                $out[] =    "DGPS (\""
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
        return implode($out, "");
    }

    /**
     *
     */
    public static function export_kml_signals()
    {
        global $ID, $mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
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
        $result =   \Rxx\Database::query($sql);
        $out =
            "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\">\r\n"
            ."<Document>\r\n"
            ."  <name>Signals Received</name>\r\n";
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            if ((float)$row['lon'] || (float)$row['lat']) {
                $out.=
                    "  <Placemark>\r\n"
                    ."    <name>"
                    .html_entity_decode(\Rxx\Rxx::translate_chars($row['call']), ENT_QUOTES, "UTF-8")
                    ." ".(float)$row['khz']."KHz</name>\r\n"
                    ."    <description>"
                    .html_entity_decode(\Rxx\Rxx::translate_chars($row['QTH']), ENT_QUOTES, "UTF-8")
                    ." ".$row['SP']." ".$row['ITU']."</description>\r\n"
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

    /**
     *
     */
    public static function export_ndbweblog()
    {
        global $ID,$mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $out =  array();

        $out[] =
            "<html>\r\n"
            ."<head><title>NDB WebLog Export</title>\r\n"
            ."<link href='".BASE_PATH."assets/style.css' rel='stylesheet' type='text/css' media='screen'>\r\n"
            ."</head>\r\n"
            ."<body><form>\n"
            ."<h1>Download Personalised NDB WebLog Files</h1>\r\n"
            ."<p><b>Instructions</b><br>\n"
            ."Thes files are for use with <a href='../../' target='_blank'><b>NDBWebLog</b></a>.<br>"
            ."Click on each link in turn to download replacements for those included in your NDBWebLog Directory - "
            ."only use this for FRESH installations.</p>\r\n"
            ."<ul>\n"
            ."  <li><b><big>Download <a href='".system_URL."/export_ndbweblog_config/".$ID."?save=1'>config.js</a></big></b></li>\r\n"
            ."  <li><b><big>Download <a href='".system_URL."/export_ndbweblog_log/".$ID."?save=1'>log.js</a></big></b></li>\r\n"
            ."  <li><b><big>Download <a href='".system_URL."/export_ndbweblog_stations/".$ID."?save=1'>stations.js</a></big></b></li>\r\n"
            ."</ul>\n"
            ."<p><a target=\"_blank\" href=\"".system_URL."/export_ndbweblog_index/".$ID."\"><b>Click here</b></a> to use the hosted version of this NDBWeblog.</p>\r\n"
            ."</body></html>\r\n";
        print implode($out, "");
    }

    /**
     * @return string
     */
    public static function export_ndbweblog_config()
    {
        global $ID,$mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $sql =  "SELECT * FROM `listeners` WHERE `ID` = \"".addslashes($ID)."\"";
        if (!$result =  \Rxx\Database::query($sql)) {
            return "Invalid User ID";
        }
        $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
        return
             "// ***********************************************************************\r\n"
            ."// * FILE HEADER:                                                        *\r\n"
            ."// ***********************************************************************\r\n"
            ."// * Filename:      config.js                                            *\r\n"
            ."// * Generated by:  "
            .\Rxx\Rxx::pad(system, 7)."                                              *\r\n"
            ."// *                                                                     *\r\n"
            ."// * This is a user editable file with details on the logbook's owner.   *\r\n"
            ."// ***********************************************************************\r\n"
            ."\r\n"
            ."qth_lat =    ".$row['lat'].";\r\n"
            ."qth_lon =    ".$row['lon'].";\r\n"
            ."qth_name =   \"".$row['name'].", "
            .($row['QTH'] ? $row['QTH'].", ":"")
            .($row['SP'] ? $row['SP'].", ":"")
            .$row['ITU']."\";\r\n"
            ."qth_email =  \"".$row['email']."\";\r\n"
            ."qth_home =   \"".$row['website']."\";\r\n"
            ."monthly =    1;\r\n"
            ."utc_offset = ".$row['timezone'].";\r\n";
    }

    /**
     * @return string
     */
    public static function export_ndbweblog_index()
    {
        global $ID,$mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        return
            "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">\r\n"
            ."<HTML>\r\n"
            ."<HEAD>\r\n"
            ."<title>NDB Weblog</title>\r\n"
            ."<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-1252\">\r\n"
            ."<meta http-equiv=\"PICS-Label\""
            ." content='(PICS-1.1 \"http://www.rsac.org/ratingsv01.html\" l gen true r (n 0 s 0 v 0 l 0)'>\r\n"
            ."<meta http-equiv=\"PICS-Label\""
            ." content='(PICS-1.1 \"http://www.classify.org/safesurf/\" l gen true r (SS~~000 1))'>\r\n"
            ."<meta name=\"Description\" content=\"NDB WebLog Site\">\r\n"
            ."<script language=\"javascript\" type=\"text/javascript\""
            ." src=\"".system_URL."/export_ndbweblog_config/".$ID."\"></script>\r\n"
            ."<script language=\"javascript\" type=\"text/javascript\""
            ." src=\"".BASE_PATH."log/functions.js\"></script>\r\n"
            ."<script language=\"javascript\" type=\"text/javascript\""
            ." src=\"".BASE_PATH."log/countries.js\"></script>\r\n"
            ."<script language=\"javascript\" type=\"text/javascript\""
            ." src=\"".system_URL."/export_ndbweblog_stations/".$ID."&amp;ver=1.1.25\"></script>\r\n"
            ."<script language=\"javascript\" type=\"text/javascript\""
            ." src=\"".system_URL."/export_ndbweblog_log/".$ID."\"></script>\r\n"
            ."</HEAD>\r\n"
            ."<FRAMESET ROWS=\"*\" ONLOAD=\"top.list()\">\r\n"
            ."<FRAME NAME=\"main\" src=\"javascript:''\">\r\n"
            ."</FRAMESET>\r\n"
            ."</HTML>\r\n";
    }

    /**
     * @return string
     */
    public static function export_ndbweblog_log()
    {
        global $ID,$mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $sql =
            "SELECT\n"
            ."  DATE_FORMAT(`logs`.`date`,'%Y%m%d') AS `date`,\n"
            ."  `logs`.`time`,\n"
            ."  `signals`.`ID`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`call`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."INNER JOIN `signals` ON\n"
            ."  `signals`.`ID` = `logs`.`signalID`\n"
            ."WHERE\n"
            ."  `listenerID` = \"".addslashes($ID)."\"\n"
            ."ORDER BY\n"
            ."  `date`,\n"
            ."  `time`";
        $result =   \Rxx\Database::query($sql);
        $records = array();
        $unique = array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $r =    \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $records[] = array(
                'ID' =>       $r['ID'],
                'khz' =>      (float)$r['khz'],
                'call' =>     $r['call'],
                'date' =>     $r['date'],
                'time' =>     $r['time']
            );
        }
        $unique = array();
        foreach ($records as $r) {
            $key = $r['khz'].'-'.$r['call'];
            if (!isset($unique[$key])) {
                $unique[$key] = array();
            }
            $unique[$key][$r['ID']] = $r['ID'];
        }
        $modified = array();
        foreach ($records as $r) {
            $key =          $r['khz'].'-'.$r['call'];
            $r['idx'] =     $unique[$key];
            $r['count'] =   count($unique[$key]);
            $modified[] = $r;
        }
        $records = $modified;
        $out =
            "// ***********************************************************************\r\n"
            ."// * FILE HEADER:                                                        *\r\n"
            ."// ***********************************************************************\r\n"
            ."// * Filename:      log.js                                               *\r\n"
            ."// * Generated by:  "
            .\Rxx\Rxx::pad(system, 7)."                                              *\r\n"
            ."// *                                                                     *\r\n"
            ."// * This is a user editable file containing actual log data.            *\r\n"
            ."// *                                                                     *\r\n"
            ."// * Put logged station details in the following format:                 *\r\n"
            ."// * LOG (\"KHz\",\"Call\",\"YYYYMMDD\",\"HHMM\",\"Optional notes on reception\"); *\r\n"
            ."// *                                                                     *\r\n"
            ."// * If any logged station doesn't appear in stations.js, you will see   *\r\n"
            ."// * an error message. Add new stations to the file stations.js          *\r\n"
            ."// * Don't use \"quotes\" in the notes field - single 'quotes' are fine.   *\r\n"
            ."// ***********************************************************************\r\n"
            ."\r\n";
        foreach ($records as $r) {
            $call = $r['call'];
            if ($r['count']>1) {
                sort($r['idx']);
                $call.=';'.(1+array_search($r['ID'], $r['idx']));
            }
            $out.=
                "LOG("
                ."\"".$r['khz']."\","
                ."\"".$call."\","
                ."\"".$r['date']."\","
                ."\"".$r['time']."\","
                ."\"\""
                .");\r\n";
        }
        return $out;
    }

    /**
     * @return string
     */
    public static function export_ndbweblog_stations()
    {
        global $ID,$mode,$ver,$noheader;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $sql =
            "SELECT\n"
            ."  `signals`.`ID`,\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`sec`,\n"
            ."  `signals`.`QTH`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `signals`.`format`,\n"
            ."  `signals`.`LSB`,\n"
            ."  `signals`.`USB`,\n"
            ."  `signals`.`pwr`,\n"
            ."  `signals`.`lat`,\n"
            ."  `signals`.`lon`,\n"
            ."  `signals`.`notes`,\n"
            ."  `signals`.`active`\n"
            ."FROM\n"
            ."  `signals`\n"
            ."INNER JOIN `logs` ON\n"
            ."  `signals`.`ID` = `logs`.`signalID`\n"
            ."WHERE\n"
            ."  `listenerID` = \"".addslashes($ID)."\"\n"
            ."GROUP BY\n"
            ."  `signals`.`ID` \n"
            ."ORDER BY\n"
            ."  `khz`,`call`,`ID`";
        $result =   \Rxx\Database::query($sql);
        $records = array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $r = \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $r['khz'] = (float)$r['khz'];
            $records[] = $r;
        }
        $unique = array();
        foreach ($records as $r) {
            $key = $r['khz'].'-'.$r['call'];
            if (!isset($unique[$key])) {
                $unique[$key] = array();
            }
            $unique[$key][] = $r['ID'];
        }
        $modified = array();
        foreach ($records as $r) {
            $key =          $r['khz'].'-'.$r['call'];
            $r['count'] =   count($unique[$key]);
            $r['idx'] =     $unique[$key];
            $modified[] = $r;
        }
        $records = $modified;
        $out = "";
        if (!isset($noheader)) {
            $out.=
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
                ."// *   id      Serial number of station in REU / RNA / RWW for more info.*\r\n"
                ."// *   active  1 or 0.                                                   *\r\n"
                ."// ***********************************************************************\r\n"
                ."\r\n";
        }
        foreach ($records as $r) {
            $call = $r['call'];
            if ($r['count']>1) {
                sort($r['idx']);
                $call.=';'.(1+array_search($r['ID'], $r['idx']));
            }
            $out.=
                "STATION ("
                ."\"".(float)$r['khz']."\","
                ."\"".$call."\","
                ."\"".\Rxx\Rxx::translate_chars($r['QTH'])."\","
                ."\"".$r['SP']."\","
                ."\"".$r['ITU']."\","
                ."\"".$r['sec']."\","
                ."\"".($r['format']=="DAID" ? "Y" : "N")."\","
                ."\"".$r['LSB']."\","
                ."\"".$r['USB']."\","
                ."\"".($r['pwr'] ? $r['pwr'] : "")."\","
                ."\"".$r['lat']."\","
                ."\"".$r['lon']."\","
                ."\"".str_replace("\"", "\\\"", \Rxx\Rxx::translate_chars($r['notes']))."\","
                ."\"".$r['ID']."\","
                ."\"".$r['active']."\""
                .");\r\n";
        }
        return $out;
    }

    /**
     *
     */
    public static function export_signallist_excel()
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        global $ID, $mode;
        global $filter_active, $offsets;
        global $type_NDB, $type_TIME, $type_DGPS, $type_DSC, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=export_".system.".xls");
        $filter_type =  array();
        if (!($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
            $type_NDB = 1;
        }
        if ($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
            if ($type_NDB) {
                $filter_type[] =     "`type` = ".NDB;
            }
            if ($type_DGPS) {
                $filter_type[] =     "`type` = ".DGPS;
            }
            if ($type_DSC) {
                $filter_type[] =     "`type` = ".DSC;
            }
            if ($type_TIME) {
                $filter_type[] =     "`type` = ".TIME;
            }
            if ($type_HAMBCN) {
                $filter_type[] =     "`type` = ".HAMBCN;
            }
            if ($type_NAVTEX) {
                $filter_type[] =     "`type` = ".NAVTEX;
            }
            if ($type_OTHER) {
                $filter_type[] =     "`type` = ".OTHER;
            }
        }
        $filter_type =  "(".implode($filter_type, " OR ").")";
        switch (system) {
            case "RNA":
                $filter_system_SQL =            "(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
                break;
            case "REU":
                $filter_system_SQL =            "`heard_in_eu` = 1";
                break;
            case "RWW":
                $filter_system_SQL =            "1";
                break;
        }
        $sql =
            "SELECT\n"
            ."  DISTINCT `signals`.*\n"
            ."FROM\n"
            ."  `signals`\n"
            ."WHERE\n"
            ."  ".$filter_system_SQL."\n"
            .($filter_active ? " AND\n `active` = 1" : "")
            .($filter_type ? " AND\n  ".$filter_type : "")
            ." ORDER BY\n"
            ."  `active` DESC,\n"
            ."  `khz` ASC,\n"
            ."  `call` ASC"
//    ."LIMIT 0,10"
        ;
        $result =   @\Rxx\Database::query($sql);
        print
            "<html><head><title>".system."</title></head>\n"
            ."<table border=\"1\" bordercolor=\"#000000\" cellpadding=\"0\" cellspacing=\"0\">\n"
            ."  <tr bgcolor=\"#c0c0c0\">\n"
            ."    <th>KHZ</th>\n"
            ."    <th>ID</th>\n"
            ."    <th>active</th>\n"
            ."    <th>LSB</th>\n"
            ."    <th>USB</th>\n"
            ."    <th>Sec</th>\n"
            ."    <th>Fmt</th>\n"
            ."    <th>QTH</th>\n"
            ."    <th>S/P</th>\n"
            ."    <th>ITU</th>\n"
            ."    <th>GSQ</th>\n"
            ."    <th>Pwr</th>\n"
            ."    <th>Notes</th>\n"
            ."    <th>Heard In</th>\n"
            ."    <th>Logs</th>\n"
            ."    <th>Last Logged</th>\n"
            ."  </tr>\n";

        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $bgcolor =  "";
            if (!$row["active"]) {
                $bgcolor =  " bgcolor=\"#D0D0D0\" title=\"(Reportedly off air or decommissioned)\"";
            } else {
                switch ($row["type"]) {
                    case NDB:       $bgcolor = "";
                        break;
                    case DGPS:      $bgcolor = " bgcolor=\"#00D8FF\" title=\"DGPS Station\"";
                        break;
                    case TIME:      $bgcolor = " bgcolor=\"#FFE0B0\" title=\"Time Signal Station\"";
                        break;
                    case NAVTEX:    $bgcolor = " bgcolor=\"#FFB8D8\" title=\"NAVTEX Station\"";
                        break;
                    case HAMBCN:    $bgcolor = " bgcolor=\"#D8FFE0\" title=\"Amateur signal\"";
                        break;
                    case OTHER:     $bgcolor = " bgcolor=\"#B8F8FF\" title=\"Other form of transmission\"";
                        break;
                }
            }
            $LSB =
                $row["LSB_approx"]
                .($row["LSB"]!="" ?
                    ($offsets=="" ? $row["LSB"] : number_format((float)($row["khz"]-($row["LSB"]/1000)), 3, '.', ''))
                    :
                    "&nbsp;"
                );
            $USB =
                $row["USB_approx"]
                .($row["USB"]!="" ?
                    ($offsets=="" ? $row["USB"] : number_format((float) ($row["khz"]+($row["USB"]/1000)), 3, '.', ''))
                    :
                    "&nbsp;"
                );

            print
                "  <tr".$bgcolor.">\n"
                ."    <td>".$row["khz"]."</td>\n"
                ."    <td>".$row["call"]."</td>\n"
                ."    <td>".$row["active"]."</td>\n"
                ."    <td align=\"right\" x:num>".$LSB."</td>\n"
                ."    <td align=\"right\" x:num>".$USB."</td>\n"
                ."    <td align=\"right\" x:num>".($row["sec"] ? $row["sec"] : "&nbsp;")."</td>\n"
                ."    <td align=\"right\" x:num>".($row["format"] ? $row["format"] : "&nbsp;")."</td>\n"
                ."    <td>".($row["QTH"] ?  $row["QTH"] :   "&nbsp;")."</td>\n"
                ."    <td>".($row["SP"]?    $row["SP"]  :   "&nbsp;")."</td>\n"
                ."    <td>".$row["ITU"]."</td>\n"
                ."    <td>".($row["GSQ"] ?  $row["GSQ"] :   "&nbsp;")."</td>\n"
                ."    <td align=\"right\" x:num>".($row["pwr"]?$row["pwr"]:"&nbsp;")."</td>\n"
                ."    <td>".($row["notes"]?stripslashes($row["notes"]):"&nbsp;")."</td>\n"
                ."    <td>".($row["heard_in"]?$row["heard_in"]:"&nbsp;")."</td>\n"
                ."    <td>".($row["logs"]?$row["logs"]:"&nbsp;")."</td>\n"
                ."    <td align=\"right\" x:num>".($row["last_heard"]?$row["last_heard"]:"&nbsp;")."</td>\n"
                ."  </tr>\n";
        }
        print
            "</table>\r\n"
            ."</body>\r\n"
            ."</html>\r\n";
    }

    /**
     *
     */
    public static function export_signallist_pdf()
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        global $filter_active, $offsets;
        global $type_NDB, $type_TIME, $type_DGPS, $type_DSC, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
        $filter_type =  array();
        if (!($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
            $type_NDB = 1;
        }
        if ($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
            if ($type_NDB) {
                $filter_type[] =     "`type` = ".NDB;
            }
            if ($type_DGPS) {
                $filter_type[] =     "`type` = ".DGPS;
            }
            if ($type_DSC) {
                $filter_type[] =     "`type` = ".DSC;
            }
            if ($type_TIME) {
                $filter_type[] =     "`type` = ".TIME;
            }
            if ($type_HAMBCN) {
                $filter_type[] =     "`type` = ".HAMBCN;
            }
            if ($type_NAVTEX) {
                $filter_type[] =     "`type` = ".NAVTEX;
            }
            if ($type_OTHER) {
                $filter_type[] =     "`type` = ".OTHER;
            }
        }
        $filter_type =  "(".implode($filter_type, " OR ").")";
        switch (system) {
            case "RNA":
                $filter_system_SQL =            "(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
                break;
            case "REU":
                $filter_system_SQL =            "`heard_in_eu` = 1";
                break;
            case "RWW":
                $filter_system_SQL =            "1";
                break;
        }
        $sql =
            "SELECT\n"
            ." `ID`,\n"
            ." `khz`,\n"
            ." `call`,\n"
            ." `active`,\n"
            ." `LSB`,\n"
            ." `LSB_approx`,\n"
            ." `USB`,\n"
            ." `USB_approx`,\n"
            ." `ITU`,\n"
            ." `GSQ`,\n"
            ." `SP`,\n"
            ." `ITU`,\n"
            ." `notes`,\n"
            ." `heard_in`,\n"
            ." `last_heard`\n"
            ."FROM\n"
            ."  `signals`\n"
            ."WHERE\n"
            ."  ".$filter_system_SQL
            .($filter_active ? " AND\n `active` = 1" : "")
            .($filter_type ? " AND\n  ".$filter_type : "")
            ."\n"
            ."ORDER BY `active` DESC,`khz` ASC, `call` ASC\n"
//          ."LIMIT 0,10"
        ;
        $result =   \Rxx\Database::query($sql);
        set_include_path(get_include_path() . PATH_SEPARATOR . 'vendor/php_pdf');
        require_once 'class.ezpdf.php';
        $pdf =new \Cezpdf('LETTER', 'landscape');
        $pdf->selectFont('./vendor/php_pdf/fonts/Courier.afm');
        $pdf->ezText(system_URL.' - '.system.' PDF File', 8);
        $data = array();
        $cols = array(
            'khz'=>         '<b>KHz</b>',
            'call'=>        '<b>ID</b>',
            'active'=>      '<b>Act</b>',
            'lsb'=>         '<b>LSB</b>',
            'usb'=>         '<b>USB</b>',
            'itu'=>         '<b>ITU</b>',
            'gsq'=>         '<b>GSQ</b>',
            'sp'=>          '<b>S/P</b>',
            'notes'=>       '<b>Notes</b>',
            'heard_in'=>    '<b>Heard In</b>',
            'first_heard'=> '<b>First Logged</b>',
            'last_heard'=>  '<b>Last Logged</b>'
        );
        $options = array(
            'shaded' =>         0,
            'showLines' =>      '2',
            'titleFontSize' =>  10,
            'rowGap' =>         '0',
            'colGap' =>         '2',
            'lineCol' =>        '(0,0.5,0)',
            'xPos' =>           'left',
            'xOrientation' =>   'right',
            'cols' => array(
                'khz' =>      array('justification' =>    'right'),
                'call' =>     array('link' =>             'url'),
	            'active' =>   array('width' =>            '10',),
                'lsb' =>      array('justification' =>    'right'),
                'usb' =>      array('justification' =>    'right'),
                'notes' =>    array('width' =>            '205'),
                'heard_in' => array('width' =>            '205')
            )
        );
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $data[] = array(
                'khz' =>            $row['khz'],
                'call' =>           $row['call'], 'url'=>system_URL.'/signal_info/'.$row["ID"], 'target'=>'_blank',
                'active' =>         $row['active'],
                'lsb' =>            $row["LSB_approx"].($row["LSB"]!="" ? ($offsets=="" ? $row["LSB"] : number_format((float)($row["khz"]-($row["LSB"]/1000)), 3, '.', '')) : ""),
                'usb' =>            $row["USB_approx"].($row["USB"]!="" ? ($offsets=="" ? $row["USB"] : number_format((float) ($row["khz"]+($row["USB"]/1000)), 3, '.', '')) : ""),
                'itu' =>            $row["ITU"],
                'gsq' =>            ($row["GSQ"]?$row["GSQ"]:""),
                'sp' =>             ($row["SP"]?$row["SP"]:""),
                'notes' =>          ($row["notes"]?stripslashes($row["notes"]):""),
                'heard_in' =>       ($row["heard_in"]?$row["heard_in"]:""),
                'first_heard' =>    ($row["first_heard"]?$row["first_heard"]:""),
                'last_heard' =>     ($row["last_heard"]?$row["last_heard"]:"")
            );
        }
        $pdf->ezTable($data, $cols, system." - signals listing", $options);
        $pdf->ezStream();
    }

    /**
     *
     */
    public static function export_text_signals()
    {
        global $ID,$mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $sql =  "SELECT * FROM `listeners` WHERE `ID` = \"".addslashes($ID)."\"";
        if (!$result =  \Rxx\Database::query($sql)) {
            print "Invalid User ID";
        }

        header("Content-Type: text/plain");

        $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);

        $out =
            system." Signals for ".$row['name']." on ".date("Y-m-d")."\r\n"
            ."Output sorted by frequency and callsign\r\n";

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
        $result =   \Rxx\Database::query($sql);


        $out.=
             str_repeat("-", 120)."\r\n"
            ."KHz\tCall\tSec\tFmt\tLSB\tUSB\tPwr\tLat\tLon\tQTH\tSP\tITU\r\n"
            .str_repeat("-", 120)."\r\n";
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $out.=
                (float)$row['khz']."\t"
                .htmlentities(\Rxx\Rxx::translate_chars($row['call']))."\t"
                .$row['sec']."\t"
                .$row['format']."\t"
                .$row['LSB']."\t"
                .$row['USB']."\t"
                .$row['pwr']."\t"
                .$row['lat']."\t"
                .$row['lon']."\t"
                .htmlentities(\Rxx\Rxx::translate_chars($row['QTH']))."\t"
                .$row['SP']."\t"
                .$row['ITU']."\r\n";
        }
        print $out;
    }

    /**
     * @return string
     */
    public static function export_raw_log()
    {
        global $ID, $mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }

        set_time_limit(600);    // Extend maximum execution time to 10 mins

        $sql =  "SELECT ID FROM `listeners` WHERE `ID` = \"".addslashes($ID)."\"";
        if (!$result =  \Rxx\Database::query($sql)) {
            print "Invalid User ID";
        }

        header("Content-Type: text/plain");

        print "yyyymmdd\thhmm\tkhz\tcall\tlsb\tusb\tsec\tdx_km\tdx_mi\tgsq\tsp\titu\tqth\r\n";

        $sql =  "SELECT\n"
            ."  DATE_FORMAT(`logs`.`date`,'%Y%m%d') AS `date`,\n"
            ."  `logs`.`time`,\n"
            ."  `logs`.`LSB`,\n"
            ."  `logs`.`USB`,\n"
            ."  `logs`.`sec`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`GSQ`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `signals`.`QTH`\n"
            ."FROM\n"
            ."  `signals`\n"
            ."INNER JOIN `logs` ON\n"
            ."  `signals`.`ID` = `logs`.`signalID`\n"
            ."WHERE\n"
            ."  `listenerID` = \"".addslashes($ID)."\"\n"
            ."ORDER BY\n"
            ."  `logs`.`date`,`logs`.`time`, `signals`.`khz`";
        $result =   \Rxx\Database::query($sql);

        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            print
                $row['date']."\t"
                .$row['time']."\t"
                .(float)$row['khz']."\t"
                .$row['call']."\t"
                .$row['LSB']."\t"
                .$row['USB']."\t"
                .$row['sec']."\t"
                .$row['dx_km']."\t"
                .$row['dx_miles']."\t"
                .$row['GSQ']."\t"
                .$row['SP']."\t"
                .$row['ITU']."\t"
                .$row['QTH']."\r\n";
        }
    }


    /**
     * @return string
     */
    public static function export_text_log()
    {
        global $ID, $mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }

        set_time_limit(600);    // Extend maximum execution time to 10 mins

        $sql =
            "SELECT\n"
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

        $result =   \Rxx\Database::query($sql);
        $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
        $call_len =     $row['call_len'];
        $QTH_len =      $row['QTH_len'];
        $time_len =     $row['time_len'];
        $LSB_len =      $row['LSB_len'];
        $USB_len =      $row['USB_len'];

        $sql =  "SELECT * FROM `listeners` WHERE `ID` = \"".addslashes($ID)."\"";
        if (!$result =  \Rxx\Database::query($sql)) {
            print "Invalid User ID";
        }

        header("Content-Type: text/plain");

        $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);

        print
            system." Log for ".$row['name']." on ".date("Y-m-d")."\r\n"
            ."Output sorted by Date\r\n"
            .str_repeat("-", 80)."\r\n"
            ."YYYYMMDD ".($time_len ? "UTC  " : "")."KHz   "
            .(system=="RWW" ? "   " : "")
            .\Rxx\Rxx::pad("ID", $call_len+1).($LSB_len||$USB_len ? "LSB   USB   " : "")
            ."KM    Miles PWR  GSQ    SP ITU Location\r\n"
            .str_repeat("-", 80)."\r\n";

        $sql =  "SELECT\n"
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
        $result =   \Rxx\Database::query($sql);


        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            print
                $row['date']." "
                .($time_len ? \Rxx\Rxx::pad($row['time'], 5) : "")
                .\Rxx\Rxx::pad((float)$row['khz'], (system=='RWW' ? 9 : 6))
                .\Rxx\Rxx::pad($row['call'], $call_len)." "
                .($LSB_len || $USB_len ? \Rxx\Rxx::pad($row['LSB'], 6).\Rxx\Rxx::pad($row['USB'], 6) : "")
                .\Rxx\Rxx::pad($row['dx_km'], 6)
                .\Rxx\Rxx::pad($row['dx_miles'], 6)
                .\Rxx\Rxx::pad(($row['pwr']?$row['pwr']:""), 5)
                .\Rxx\Rxx::pad($row['GSQ'], 7)
                .\Rxx\Rxx::pad($row['SP'], 3)
                .$row['ITU']." ".$row['QTH']."\r\n";
        }
        print
            "----------------------------------------------------------------------\r\n"
            .\Rxx\Database::numRows($result)." logs listed\r\n\r\n"
            ."Output generated by ".system." 'Listener Log Export' feature.\r\n"
            .system_URL."/?mode=$mode&ID=$ID\r\n";
    }

    /**
     *
     */
    public static function ILGRadio_signallist()
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $out =  array();

        switch (system) {
            case "RNA":     $filter_system_SQL = "(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
                break;
            case "REU":     $filter_system_SQL = "(`heard_in_eu` = 1)";
                break;
            default:    $filter_system_SQL = "(1)";
                break;
        }

        $sql =   "SELECT\n"
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

        $result =   @\Rxx\Database::query($sql);

        $arr_search = array(  "DGPS; Ref ID: ");
        $arr_replace= array(  "");

        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $out[] =     lead($row['khz'], 7)
                .Rxx::pad($row['call'], 22)
                ."0000-2400"
                ."1234567"
                .Rxx::pad("", 18)
                .Rxx::pad("", 48)
                .Rxx::pad("", 8)
                .Rxx::pad(substr(str_replace($arr_search, $arr_replace, Rxx::translate_chars($row['QTH'])), 0, 24), 25)
                .Rxx::pad("", 7)
                .Rxx::pad("", 3)
                .Rxx::pad(substr(str_replace($arr_search, $arr_replace, Rxx::translate_chars($row['notes'])), 0, 14), 15)
                .Rxx::pad("", 1)
                .Rxx::pad("", 18)
                .Rxx::pad("", 30)
                .Rxx::pad("", 5)
                .Rxx::pad("", 3)
                .Rxx::pad($row['ITU'], 18)
                .Rxx::pad("", 6)
                .Rxx::pad("", 5)
                .Rxx::pad("", 20) //not 1
                .Rxx::pad("", 20)
                ." "
                ."\r\n";
        }
        print implode($out, "");
    }
}
