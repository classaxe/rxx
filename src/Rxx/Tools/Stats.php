<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:52 PM
 */

namespace Rxx\Tools;


/**
 * Class Stats
 * @package Rxx\Tools
 */
class Stats {
    /**
     * @return string
     */
    public static function stats() {
        global $script, $mode, $region, $listenerID, $dx, $dx_units;
        global $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER;

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

        switch (system) {
            case "RNA":
                $filter_system_SQL =			"(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
                $filter_listener_SQL =			"(`listeners`.`region` = 'na' OR `listeners`.`region` = 'ca' OR `listeners`.`SP` = 'hi')";
                $filter_log_SQL =				"(`logs`.`region` = 'na' OR `logs`.`region` = 'ca' OR `logs`.`heard_in` = 'hi')";
                break;
            case "REU":
                $filter_system_SQL =			"`heard_in_eu` = 1";
                $filter_listener_SQL =			"(`region` = 'eu')";
                $filter_log_SQL =				"(`logs`.`region` = 'eu')";
                break;
            case "RWW":
                if ($region!="") {
                    $filter_system_SQL =			"(`heard_in_".$region." = 1)";
                    $filter_listener_SQL =			"(`region` = '$region')";
                    $filter_log_SQL =      			"(`region` = '$region')";
                }
                else {
                    $filter_system_SQL =			"1";
                    $filter_listener_SQL =			"1";
                    $filter_listener_SQL =			"1";
                }
                break;
        }

        $filter_type =	array();
        if (!($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
            switch (system) {
                case "RNA":	$type_NDB =	1;	break;
                case "REU":	$type_NDB =	1;	break;
                case "RWW":	$type_NDB =	1;	break;
            }
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


        if ($dx=="" || (!$listenerID && $dx<2000)) {
            $dx=2000;
        }
        if ($dx_units=="") {
            $dx_units="km";
        }
        $out =
            "<form name='form' action='".system_URL."' method='POST'>\n"
            ."<input type='hidden' name='mode' value='$mode'>\n"
            ."<h2>Statistics</h2><br><br>\n"
            ."<p align='center'><small>Quick Links [\n"
            ."<nobr><a href='#dx'><b>DX Table</b></a></nobr> |\n"
            ."<nobr><a href='#signals_dx'><b>Signals by distance</b></a></nobr> |\n"
            ."<nobr><a href='#csp'><b>Countries, States &amp; Provinces</b></a></nobr> |\n"
            ."<nobr><a href='#n60'><b>North of 60 Degrees</b></a></nobr>\n"
            ."]</small></p><br><br>\n"
            ."<table cellpadding='2' border='0' cellspacing='1'>\n"
            ."  <tr>\n"
            ."    <td align='center' valign='top' colspan='2'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18'></td>\n"
            ."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Customise Report</td>\n"
            ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18'></td>\n"
            ."      </tr>\n"
            ."    </table>\n"
            ."    <table cellpadding='0' cellspacing='0' class='tableForm' border='1' bordercolor='#c0c0c0'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Show for &nbsp;</th>\n"
            ."        <td nowrap><select name='listenerID' class='formfield' onchange='document.form.submit()' style='font-family: monospace;' >\n"
            .get_listener_options_list($filter_listener_SQL,$listenerID,"(Select a listener to see more specific data)")
            ."</select></td>\n"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Types&nbsp;</th>\n"
            ."        <td nowrap style='padding: 0px;'><table cellpadding='0' cellspacing='1' border='0' width='100%' class='tableForm'>\n"
            ."          <tr>\n"
            ."            <td bgcolor='#00D8FF' width='16%' nowrap onclick='toggle(document.form.type_DGPS)'><input type='checkbox' onclick='toggle(document.form.type_DGPS);' name='type_DGPS' value='1'".($type_DGPS? " checked" : "").">DGPS</td>"
            ."            <td bgcolor='#B8FFC0' width='17%' nowrap onclick='toggle(document.form.type_HAMBCN)'><input type='checkbox' onclick='toggle(document.form.type_HAMBCN)' name='type_HAMBCN' value='1'".($type_HAMBCN ? " checked" : "").">Ham</td>"
            ."            <td bgcolor='#FFB8D8' width='17%' nowrap onclick='toggle(document.form.type_NAVTEX)'><input type='checkbox' onclick='toggle(document.form.type_NAVTEX)' name='type_NAVTEX' value='1'".($type_NAVTEX ? " checked" : "").">NAVTEX&nbsp;</td>"
            ."            <td bgcolor='#FFFFFF' width='17%' nowrap onclick='toggle(document.form.type_NDB)'><input type='checkbox' onclick='toggle(document.form.type_NDB)' name='type_NDB' value='1'".($type_NDB? " checked" : "").">NDB</td>"
            ."            <td bgcolor='#FFE0B0' width='17%' nowrap onclick='toggle(document.form.type_TIME)'><input type='checkbox' onclick='toggle(document.form.type_TIME)' name='type_TIME' value='1'".($type_TIME? " checked" : "").">Time</td>"
            ."            <td bgcolor='#B8F8FF' width='16%' nowrap onclick='toggle(document.form.type_OTHER)'><input type='checkbox' onclick='toggle(document.form.type_OTHER)' name='type_OTHER' value='1'".($type_OTHER ? " checked" : "").">Other</td>"
            ."          </tr>\n"
            ."        </table></td>"
            ."      </tr>\n"
            ."      <span class='noprint'><tr class='rowForm'>\n"
            ."        <th colspan='2' align='center'>"
            ."<input type='submit' name='go' value='&nbsp;Go&nbsp;' onclick='document.form.go.disabled=1' class='formButton' title='Execute search'> "
            ."<input type='submit' name='map' value='&nbsp;Map&nbsp;' onclick='listener_map()' class='formButton' title='Show map of listener locations'><br>\n"
            ."</td>"
            ."      </tr></span>\n"
            ."    </table></td>"
            ."  </tr>"
            ."</table><br>"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='dx'></a>DX Table</th>\n"
            ."        <th align='right' class='downloadTableHeadings_nosort'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small></th>\n"
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>"
            ."<select name='dx' class='formfield' onchange='document.form.go.disabled=1;document.form.submit()'>\n"
            ."  <option value='5000'".($dx=="5000" ? " selected":"").">5000+ Miles</option>\n"
            ."  <option value='4000'".($dx=="4000" ? " selected":"").">4000+ Miles</option>\n"
            ."  <option value='3000'".($dx=="3000" ? " selected":"").">3000+ Miles</option>\n"
            ."  <option value='2500'".($dx=="2500" ? " selected":"").">2500+ Miles</option>\n"
            ."  <option value='2000'".($dx=="2000" ? " selected":"").">2000+ Miles</option>\n";
        if ($listenerID) {
            $out.=
                "  <option value='1500'".($dx=="1500" ? " selected":"").">1500+ Miles</option>\n"
                ."  <option value='1000'".($dx=="1000" ? " selected":"").">1000+ Miles</option>\n"
                ."  <option value='500' ".($dx=="500"  ? " selected":"").">500+  Miles</option>\n"
                ."  <option value='0'".($dx=="0" ? " selected":"").">(Any Distance)</option>\n";
        }
        $out.=
            "</select>\n"
            .($listenerID ?  "" : "(Each signal shown once per listener - shorter distances are available for specific listeners.)")
            ."</td></tr>"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>";

        $listener =	get_listener_name($listenerID);

        $sql =
            "SELECT\n"
            ."  `signals`.`call` as `ID`,\n"
            ."  `signals`.`khz` as `KHz`,\n"
            ."  `signals`.`GSQ`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `logs`.`date` as `date`,\n"
            ."  `logs`.`dx_km` as `KM`,\n"
            ."  `logs`.`dx_miles` as `Miles`,\n"
            ."  `listeners`.`name` as `listenerName`,\n"
            ."  `listeners`.`ITU` as `listenerITU`,\n"
            ."  `listeners`.`SP` as `listenerSP`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."INNER JOIN `signals` ON\n"
            ."  `logs`.`signalID` = `signals`.`ID`\n"
            ."LEFT JOIN `listeners` ON\n"
            ."  `logs`.`listenerID` = `listeners`.`ID`\n"
            ."WHERE\n"
            .($region!="" ? "  `logs`.`region` = '$region' AND\n" : "")
            .($dx ?             "  `logs`.`dx_miles`>=$dx AND\n" : "")
            .($listenerID ?     "  `logs`.`listenerID` = ".$listenerID." AND\n" : "")
            .($filter_type ?    "  $filter_type AND\n" : "")
            ."  $filter_system_SQL\n"

            ."GROUP BY\n"
            ."  Concat(`signals`.`ID`,`logs`.`listenerID`)\n"
            ."ORDER BY\n"
            ."`dx_km` is NULL, `dx_km` DESC";

        $result =	mysql_query($sql);
        $out.=
            "<textarea name='results' rows='20' cols='85' nowrap>"
            ."DX Table for ".($listenerID ? $listener : "all listeners")."\n"
            ."-------------------------------------------------------".(!$listenerID ? "-------------------------" : "")."\n"
            ."Date       KM    Mi    KHz      ID       SP  ITU GSQ     ".(!$listenerID ? "Heard By" : "")."\n"
            ."-------------------------------------------------------".(!$listenerID ? "-------------------------" : "")."\n";



        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $row =	mysql_fetch_array($result);
            $out.=
                $row["date"]." "
                .pad($row["KM"],6)
                .pad($row["Miles"],6)
                .pad((float) $row["KHz"],9)
                .pad($row["ID"],9)
                .pad($row["SP"],4)
                .pad($row["ITU"],4)
                .pad($row["GSQ"],7)
                .(!$listenerID ? " ".$row["listenerName"]." (".($row["listenerSP"] ? $row["listenerSP"]." " : "").$row["listenerITU"].")": "")."\n";
        }
        $out.=
            "-------------------------------------------------------".(!$listenerID ? "-------------------------" : "")."\n"
            ."(Produced by ".system.")\n"
            ."</textarea><br>Total loggings shown: ".mysql_num_rows($result)."</td></tr></table>"
            ."<br><br><br>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='signals_dx'></a>Signals by distance</th>\n"
            ."        <th align='right' class='downloadTableHeadings_nosort'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small></th>\n"
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>"
            ."<select name='dx_units' class='formfield' onchange='document.form.go.disabled=1;document.form.submit()'>\n"
            ."  <option value='km'".($dx_units=="km" ? " selected":"").">Distances in KM</option>\n"
            ."  <option value='miles'".($dx_units=="miles" ? " selected":"").">Distances in Miles</option>\n"
            ."</select>\n"
            .($listenerID ?  "" : "(Each signal shown once per listener - shorter distances are available for specific listeners.)")
            ."</td></tr>"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>";

        $listener =	get_listener_name($listenerID);

        $sql =
            "SELECT\n"
            ."  100*TRUNCATE((".($dx_units=='km' ? "dx_km" : "dx_miles")." / 100),0) AS `dx`,\n"
            ."  COUNT(distinct(logs.signalID)) AS `count`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."INNER JOIN `signals` ON\n"
            ."  `signals`.`ID` = `logs`.`signalID`\n"
            ."WHERE\n"
            .($region!="" ? "  `logs`.`region` = '$region' AND\n" : "")
            .($filter_type ? "  $filter_type AND\n" : "")
            .($listenerID ? "  `listenerID` = $listenerID AND\n" : "")
            ."  `logs`.`dx_km` IS NOT NULL\n"
            ."GROUP BY\n"
            ."  `dx`\n"
            ."ORDER BY\n"
            ."  `dx` ASC";
        $result =	mysql_query($sql);

//  print("<pre>$sql</pre>");

        $out.=
            "<textarea name='results' rows='20' cols='85' nowrap>"
            ."Signals by Distance Table for ".($listenerID ? $listener : "all listeners")."\n"
            ."---------------------\n"
            ." DX in ".($dx_units=='km'? "KM  " : "Miles")."  Signals\n"
            ."---------------------\n";



        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $row =	mysql_fetch_array($result);
            $out.=	 pad(substr("      ".$row["dx"],strlen($row["dx"]))."-".(99+(int)$row["dx"]),14)
                .pad($row["count"],6)
                ."\n";
        }
        $out.=
            "---------------------\n"
            ."(Produced by ".system.")\n"
            ."</textarea><br>Total loggings shown: ".mysql_num_rows($result)."</td></tr></table>"
            ."<br><br><br>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='csp'></a>Countries, States and Provinces</th>\n"
            ."        <th align='right' class='downloadTableHeadings_nosort'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small></th>\n"
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>";

        $sql =
            "SELECT\n"
            ."  `signals`.`ITU`,\n"
            ."  `signals`.`SP`,\n"
            ."  COUNT(distinct `signals`.`ID`) AS `signals`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  $filter_system_SQL AND\n"
            .($region!="" ? "  `logs`.`region` = '$region' AND\n" : "")
            .($listenerID ? "  `logs`.`listenerID` = ".$listenerID."\n" : "  `logs`.`listenerID` != '' AND `logs`.`listenerID` IS NOT NULL\n")
            .($filter_type ? " AND\n  $filter_type" : "")
            ."GROUP BY\n"
            ."  CONCAT(`signals`.`ITU`,`signals`.`SP`)\n"
            ."ORDER BY\n"
            ."  `signals`.`ITU`,\n"
            ."  `signals`.`SP`";
        $result =	mysql_query($sql);
//   print("<pre>$sql</pre>");

        $out.=
            "<textarea name='results' rows='20' cols='85'>"
            ."Countries, States and Provinces for ".($listenerID ? $listener : "all listeners")."\n"
            ."-----------------\n"
            ."ITU SP  Stations\n"
            ."-----------------\n";

        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $row =	mysql_fetch_array($result);
            $out.=	pad($row["ITU"],4).pad($row["SP"],4).pad($row["signals"],5)."\n";
        }
        $out.=
            "-----------------\n"
            ."(Produced by ".system.")\n"
            ."</textarea><br>Total Countries, States and Provinces: ".mysql_num_rows($result)."</td></tr></table>"
            ."<br><br><br>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='n60'></a>North of 60</th>\n"
            ."        <th align='right' class='downloadTableHeadings_nosort'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small></th>\n"
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>";

        $sql =
            "SELECT\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`GSQ`,\n"
            ."  `signals`.`lat`,\n"
            ."  `signals`.`lon`,\n"
            ."  `listeners`.`name` as `listenerName`,\n"
            ."  `listeners`.`ITU` as `listenerITU`,\n"
            ."  `listeners`.`SP` as `listenerSP`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."LEFT JOIN\n"
            ."  `listeners`\n"
            ."ON\n"
            ."  `logs`.`listenerID` = `listeners`.`ID`\n"
            ."WHERE\n"
            ."  $filter_system_SQL AND\n"
            .($region!="" ? "  `logs`.`region` = '$region' AND\n" : "")
            .($listenerID ? "  `logs`.`listenerID` = ".$listenerID." AND\n" : "  `logs`.`listenerID` != '' AND `logs`.`listenerID` IS NOT NULL AND\n")
            .($filter_type ? "   $filter_type AND\n" : "")
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`lat`>=60\n"
            ."GROUP BY\n"
            ."  Concat(`signals`.`ID`,`logs`.`listenerID`)\n"
            ."ORDER BY\n"
            ."  `signals`.`lat` DESC,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`call`,"
            ."  `listeners`.`name`,\n"
            ."  `listeners`.`SP`\n";
        $result =	mysql_query($sql);

        //print("<pre>$sql</pre>");
        $out.=
            "<textarea name='results' rows='20' cols='85'>"
            ."Signals North of 60 Degrees for ".($listenerID ? $listener : "all listeners")."\n"
            ."----------------------------------------------".(!$listenerID ? "---------------------------" : "")."\n"
            ."KHZ      ID     SP ITU GSQ    Lat     Lon       ".(!$listenerID ? "Heard By" : "")."\n"
            ."----------------------------------------------".(!$listenerID ? "---------------------------" : "")."\n";

        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $row =	mysql_fetch_array($result);
            $out.=
                pad((float)$row["khz"],9)
                .pad($row["call"],7)
                .pad($row["SP"],3)
                .pad($row["ITU"],4)
                .pad($row["GSQ"],7)
                .pad($row["lat"],8)
                .pad($row["lon"],9)
                .(!$listenerID ? " ".$row["listenerName"]." (".($row["listenerSP"] ? $row["listenerSP"]." " : "").$row["listenerITU"].")": "")."\n";
        }
        $out.=
            "----------------------------------------------".(!$listenerID ? "---------------------------" : "")."\n"
            ."(Produced by ".system.")\n"
            ."</textarea><br>Total entries listed: ".mysql_num_rows($result)."</td></tr></table>";
        return $out;
    }
}