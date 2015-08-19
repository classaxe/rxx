<?php

function check_sp_itu($SP, $ITU)
{
    $error_msg =    "";
    if ($SP) {
        $sql =    "SELECT `ITU` FROM `sp` WHERE `SP` = '$SP'";
        $result =    mysql_query($sql);
        if (!mysql_num_rows($result)) {
            $error_msg .=    "The S/P code $SP is not valid.\\\\n";
        } else {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            if ($row['ITU']!=$ITU) {
                $error_msg .=    "$SP belongs in ".$row['ITU'].($ITU ? ", not $ITU" : "").".\\\\n";
            }
        }
    }
    if ($ITU) {
        $sql =    "SELECT `ITU` FROM `itu` WHERE `ITU` = '$ITU'";
        $result =    mysql_query($sql);
        if (!mysql_num_rows($result)) {
            $error_msg .=    "The ITU code $ITU is not valid.\\\\n";
        }
    }
    return $error_msg;
}


function get_bestDX($listenerID, $dayonly, $min_dx, $max_dx)
{
    $sql =
         "SELECT\n"
        ."  `signals`.`ID`,\n"
        ."  `signals`.`khz`,\n"
        ."  `signals`.`call`,\n"
        ."  `signals`.`ITU`,\n"
        ."  `signals`.`QTH`,\n"
        ."  `signals`.`SP`,\n"
        ."  `signals`.`pwr`,\n"
        ."  `logs`.`date`,\n"
        ."  `logs`.`time`,\n"
        ."  `logs`.`dx_km`,\n"
        ."  `logs`.`dx_miles`\n"
        ."FROM\n"
        ."  `logs`,\n"
        ."  `signals`\n"
        ."WHERE\n"
        ."  `logs`.`signalID` = `signals`.`ID` AND\n"
        ."  `signals`.`type` = 0 AND\n"
        ."  `dx_miles` >=$min_dx AND\n"
        .($max_dx ? "  `dx_miles` <=$max_dx AND\n" : "")
        .($dayonly ? "  `daytime` = 1 AND\n" : "")
        ."  `listenerID` = $listenerID\n"
        ."ORDER BY\n"
        ."  `logs`.`dx_miles` DESC\n"
        ."LIMIT\n"
        ."  1";
//  z($sql);
    $result =    mysql_query($sql);
    if (!mysql_num_rows($result)) {
        return false;
    }
    return mysql_fetch_array($result, MYSQL_ASSOC);
}



// ************************************
// * get_dx()                         *
// ************************************
function get_dx($qth_lat, $qth_lon, $dx_lat, $dx_lon)
{
// Check for same point:
    if ($qth_lat == $dx_lat && $qth_lon==$dx_lon) {
        return array(0,0);
    }
    $dlon = ($dx_lon - $qth_lon);
    if (abs($dlon) > 180) {
        $dlon = (360 - abs($dlon))*(0-($dlon/abs($dlon)));
    }
    $rinlat =        $qth_lat*0.01745;    // convert to radians
    $rinlon =        $qth_lon*0.01745;
    $rfnlat =        $dx_lat*0.01745;
    $rdlon =        $dlon*0.01745;
    $rgcdist =        acos(sin($rinlat)*sin($rfnlat)+cos($rinlat)*cos($rfnlat)*cos($rdlon));

    return array(round(abs($rgcdist)*3958.284),round(abs($rgcdist)*6370.614));
}



// ************************************
// * get_region_for_itu()             *
// ************************************
function get_region_for_itu($itu)
{
    if (!$itu) {
        return "";
    }
    $sql =    "SELECT `region` FROM `itu` WHERE `itu` = \"$itu\"";
    $result =    mysql_query($sql);
    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
    return $row["region"];
}



// ************************************
// * get_sp_maplinks()                *
// ************************************
function get_sp_maplinks($SP, $ID, $text)
{
    if (preg_match(
        "/AL|AK|AR|AZ|CA|CO|CT|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|"
        ."NC|ND|NE|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY/i",
        $SP
    )) {
        return
             "<a href='".system_URL."/state_map?type_DGPS=1&amp;type_HAMBCN=1&amp;type_NAVTEX=1&amp;type_NDB=1"
            ."&amp;type_TIME=1&amp;type_OTHER=1&amp;simple=1&amp;SP=$SP&amp;ID=".$ID."'"
            ." title='Show signal map for ".$SP."' target='blank'><b>".$text."</b></a>";
    }
    return $text;
}



// ************************************
// * get_signal_dx()                  *
// ************************************
function get_signal_dx($ID, $qth_lat, $qth_lon)
{
    if (!$qth_lat) {
        return array(false,false);
    }
    $sql =    "SELECT `lat`,`lon` FROM `signals` WHERE `ID` = \"$ID\"";
    $result =    mysql_query($sql);
    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
    $dx_lat =    $row["lat"];
    $dx_lon =    $row["lon"];
    if (!$dx_lat) {
        return array(false,false);
    }
    return get_dx($qth_lat, $qth_lon, $dx_lat, $dx_lon);
}


// ************************************
// * get_ITU()                        *
// ************************************
function get_ITU($ITU)
{
    $sql =    "SELECT `name` FROM `itu` WHERE `ITU` = \"$ITU\"";
    $result =    mysql_query($sql);
    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
    return $row["name"];
}


// ************************************
// * get_listener_name()              *
// ************************************
function get_listener_name($listenerID)
{
    if ($listenerID=="") {
        return "";
    }
    $sql =    "SELECT `name` FROM `listeners` WHERE `ID` = $listenerID";
    $result =    mysql_query($sql);
    if (!mysql_num_rows($result)) {
        return "";
    }
    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
    return    $row['name'];
}



// ************************************
// * get_listener_email()             *
// ************************************
function get_listener_email($listenerID)
{
    if ($listenerID=="") {
        return "";
    }
    $sql =    "SELECT `email` FROM `listeners` WHERE `ID` = $listenerID";
    $result =    mysql_query($sql);
    if (!mysql_num_rows($result)) {
        return "";
    }
    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
    return    $row['email'];
}



// ************************************
// * get_listener_region()            *
// ************************************
function get_listener_region($listenerID)
{
    if ($listenerID=="") {
        return "";
    }
    $sql =    "SELECT `region` FROM `listeners` WHERE `listeners`.`ID` = $listenerID";
    $result =    mysql_query($sql);
    if (!mysql_num_rows($result)) {
        return "";
    }
    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
    return    $row['region'];
}



// ************************************
// * get_listener_details()           *
// ************************************
function get_listener_details($listenerID)
{
    if ($listenerID=="") {
        return "";
    }
    $sql =    "SELECT * FROM `listeners` WHERE `ID` = $listenerID";
    $result =    mysql_query($sql);
    if (!mysql_num_rows($result)) {
        return "";
    }
    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
    return    $row["name"].", ".$row["QTH"].($row['SP'] ? " ".$row["SP"]:"")." ".$row["ITU"];
}



// ************************************
// * get_listener_options_list()      *
// ************************************
function get_listener_options_list($filter, $selectedID, $chooseText)
{
    $out =    array();
    $out[] =
        "<option value=''".($selectedID == '' ? " selected" : "")." style='color: #0000ff;'>$chooseText</option>\n";


    $sql =
         "SELECT\n"
        ."  `listeners`.`ID`,\n"
        ."  TRIM(`listeners`.`name`) AS `name`,\n"
        ."  `listeners`.`primary_QTH`,\n"
        ."  `listeners`.`callsign`,\n"
        ."  `listeners`.`QTH`,\n"
        ."  `listeners`.`SP`,\n"
        ."  `listeners`.`ITU`\n"
        ."FROM\n"
        ."  `listeners`\n"
        ."WHERE\n"
        ."  $filter\n"
        ." ORDER BY `name`,`primary_QTH` DESC,`qth`";
    $result =     @mysql_query($sql);
//  print("<pre>$sql</pre>");
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =    mysql_fetch_array($result, MYSQL_ASSOC);
        $out[] =    "<option value=\"".$row["ID"]."\"";
        if ($selectedID && is_array($selectedID)) {
            for ($j=0; $j<count($selectedID); $j++) {
                if ($selectedID && ($selectedID[$j] == $row["ID"])) {
                    $out[] =    " selected";
                }
            }
        } else {
            if ($selectedID == $row["ID"]) {
                $out[] =    " selected";
            }
        }
        $out[] =
             " style='font-family: monospace; color: ".($row['primary_QTH'] ? "#000000" : "#666666")."'>"
            .pad_dot(
                ($row['primary_QTH'] ? "" : "  ")
                .$row["name"].", ".$row["QTH"]." ".$row["callsign"],
                50
            )
            .($row['SP'] ? " ".$row["SP"]:"...")." "
            .$row["ITU"]."</option>\n";
    }
    mysql_free_result($result);
    return implode($out, "");
}



// ************************************
// * get_local_icao()                 *
// ************************************
function get_local_icao($GSQ, $num, $selected)
{
    $out =        array();
    $deg =        GSQ_deg($GSQ);
    $icao_arr =   array();
    $sql =        "SELECT * FROM `icao`";
    $result =     @mysql_query($sql);
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =    mysql_fetch_array($result, MYSQL_ASSOC);
        $dx =     get_dx($deg["lat"], $deg["lon"], $row["lat"], $row["lon"]);
        $icao_arr[] =    array("miles" => $dx[0],"km" => $dx[1],"ICAO" => $row["ICAO"], );
    }
    sort($icao_arr);
    return $icao_arr;
}

function get_var($key, $default = false)
{
    return (isset($_GET[$key]) ? $_GET[$key] : (isset($_POST[$key]) ? $_POST[$key] : $default));
}

function set_var($key, $value)
{
    $_GET[$key] =     $value;
    $_POST[$key] =    $value;
}


// ************************************
// * get_region_options_list()        *
// ************************************
function get_region_options_list($selectedID, $chooseText)
{
    $out =
         "<option value=''"
        .($selectedID == '' ? " selected='selected'" : "")
        ." style='color: #0000ff;'>".$chooseText."</option>\n";
    $sql =    "SELECT * FROM `region` ORDER BY `name`";
    $result =     @mysql_query($sql);
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =    mysql_fetch_array($result, MYSQL_ASSOC);
        $out.=
             "<option value=\"".$row["region"]."\""
            .($selectedID == $row["region"] ? " selected='selected'" : "")
            .">".$row['name']."</option>\n";
    }
    return $out;
}



// ************************************
// * get_SP()                         *
// ************************************
function get_SP($SP)
{
    $sql =      "SELECT `name` FROM `sp` WHERE `SP` = \"$SP\"";
    $result =   mysql_query($sql);
    $row =      mysql_fetch_array($result, MYSQL_ASSOC);
    return      $row["name"];
}


// ************************************
// * GSQ_deg()                        *
// ************************************
function GSQ_deg($GSQ)
{
    $GSQ = strToUpper($GSQ);
    $offset =    (strlen($GSQ)==6 ? 1/48 : 0);

    if (strlen($GSQ) == 4) {
        $GSQ = $GSQ."MM";
    }

    $lon_d = ord(substr($GSQ, 0, 1))-65;
    $lon_m = substr($GSQ, 2, 1);
    $lon_s = ord(substr($GSQ, 4, 1))-65;

    $lat_d = ord(substr($GSQ, 1, 1))-65;
    $lat_m = substr($GSQ, 3, 1);
    $lat_s = ord(substr($GSQ, 5, 1))-65;

    $lon = round((2 * ($lon_d*10 + $lon_m + $lon_s/24 + $offset) - 180)*10000)/10000;
    $lat = round(($lat_d*10 + $lat_m + $lat_s/24 + $offset - 90)*10000)/10000;

    return array("lat" => $lat, "lon" => $lon);
}


// ************************************
// * deg_GSQ()                        *
// ************************************
function deg_GSQ($lat, $lon)
{
    $letters = "abcdefghijklmnopqrstuvwxyz";
    if ($lat==""||$lon=="") {
        return false;
    }

    $lat =    (float) $lat + 90;
    $lat_a =    strtoUpper(substr($letters, floor($lat/10), 1));
    $lat_b =    floor($lat%10);
    $lat_c =    substr($letters, 24*($lat-(int)$lat), 1);

    $lon =    ((float) $lon + 180)/2;
    $lon_a =    strtoUpper(substr($letters, floor($lon/10), 1));
    $lon_b =    floor($lon%10);
    $lon_c =    substr($letters, 24*($lon-(int)$lon), 1);
    return    $lon_a.$lat_a.$lon_b.$lat_b.$lon_c.$lat_c;
}



// ************************************
// * help()                           *
// ************************************
function help()
{
    $file =    fopen("assets/help.html", "r");
    $out =    fread($file, 1000000);
    fclose($file);
    return $out;
}



// ************************************
// * admin_help()                     *
// ************************************
function admin_help()
{
    $file =    fopen("assets/admin_help.html", "r");
    $out =    fread($file, 1000000);
    fclose($file);
    return $out;
}



// ************************************
// * highlight()                      *
// ************************************
function highlight($string, $find)
{
    $find = str_replace("_", "[A-Z0-9]", $find);
    return ($find ?
        (preg_replace("/($find)/", "<span style='font-weight:bold;color:".g_highlight."'>\\1</span>", $string))
     :
        $string
    );
}

function isAdmin()
{
    return (isset($_SESSION['admin']) && $_SESSION['admin']===true);
}

function piwik_tracking()
{
    return
         "var _paq = _paq || [];\n"
        ."(function(){\n"
        ."  var u=document.location.protocol+'//'+document.location.hostname+'/piwik/';\n"
        ."  _paq.push(['setSiteId', 4]);\n"
        ."  _paq.push(['setTrackerUrl', u+'piwik.php']);\n"
        ."  _paq.push(['trackPageView']);\n"
        ."  _paq.push(['enableLinkTracking']);\n"
        ."  var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];\n"
        ."  g.type='text/javascript'; g.defer=true; g.async=true; g.src=u+'piwik.js';\n"
        ."  s.parentNode.insertBefore(g,s);\n"
        ."})();\n";
}


// ************************************
// * main()                           *
// ************************************
function main()
{
    global $mode, $submode, $script;
    global $system;


    switch ($mode) {
        case 'admin_manage':
            $Obj = new \Managers\Admin;
            break;
        case 'awards':
            $Obj = new Awards;
            break;
        case 'signal_list':
            $Obj = new SignalList;
            $Obj->draw();
            break;
        case 'poll_list':
            $Obj = new Poll;
            break;
        default:
            $Obj = false;
            break;
    }



    $out =
     "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n"
    ."<html><head>\n"
    ."<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>\n"
    ."<title>".system." > ";
    switch ($mode) {
        case "admin_help":
            $out.=    "Administrator Help";
            break;
        case "admin_manage":
            $out.=    "Administrator Management Tools";
            break;
        case "awards":
            $out.=    "Awards";
            break;
        case "cle":
            $out.=    "CLE";
            break;
        case "help":
            $out."Help";
            break;
        case "listener_list":
            $out.=    "Listeners";
            break;
        case "logon":
            $out.=    "Administrator Logon";
            break;
        case "maps":
            $out.=    "Maps";
            break;
        case "poll_list":
            $out.=    "Polls";
            break;
        case "signal_list":
            $out.=    "signals";
            break;
        case "signal_seeklist":
            $out.=    "signal Seeklist";
            break;
        case "stats":
            $out.=    "Statistics";
            break;
        case "sys_info":
            $out.=    "System Info";
            break;
        case "tools":
            $out.=    "tools";
            break;
        case "weather":
            $out.=    "Weather";
            break;
    }
    $out.=
     (isAdmin() ? " (ADMIN)" : "")
    ."</title>\n"
    ."<link type='text/css' rel='stylesheet' href='//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css'>\n"
    ."<link href='".BASE_PATH."assets/style.css' rel='stylesheet' type='text/css'>\n"
    ."<link href='".BASE_PATH.strtoLower(system).".css' rel='stylesheet' type='text/css' media='screen'>\n"
    .($Obj && isset($Obj->head) ? $Obj->head : "")
    ."<script type='text/javascript' src='//code.jquery.com/jquery-1.10.2.js'></script>\n"
    ."<script type='text/javascript' src='//code.jquery.com/ui/1.11.4/jquery-ui.js'></script>\n"
    ."<script type='text/javascript'>\n"
    ."//<!--\n"
    .piwik_tracking()
    ."var system =     '".system."';\n"
    ."var system_URL = '".system_URL."';\n"
    ."//-->\n"
    ."</script>\n"
    ."<script type='text/javascript' src='".BASE_PATH."assets/functions.js'></script>\n"
    ."</head>\n"
    ."<body onload='show_time()'><span><a name='top'></a></span>\n"
    ."<table cellpadding='10' cellspacing='0' width='616' class='titleTable'>\n"
    ."  <tr>\n"
    ."    <td align='center'>"
    ."<h1 title='Version ".system_version." (".system_date.")' style='cursor:pointer; cursor:hand;'>"
    .system_title."</h1></td>\n"
    ."  </tr>\n"
    ."</table>\n"
    ."<table cellpadding='0' cellspacing='0' border='0' class='noprint'>\n"
    ."  <tr>\n"
    ."    <td><table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class='navTable' bgcolor='#ffffff'>\n"
    ."      <tr>\n";
    switch (system) {
        case "RNA":
            $out.=
             menuItem_selected("<b>North America</b>", 200)
            .menuItem("system_REU", "Europe", "sys", 0, 200)
            .menuItem("system_RWW", "Worldwide", "sys", 0, 200);
            break;
        case "REU":
            $out.=
             menuItem("system_RNA", "North America", "sys", 0, 200)
            .menuItem_selected("<b>Europe</b>", 200)
            .menuItem("system_RWW", "Worldwide", "sys", 0, 200);
            break;
        case "RWW":
            $out.=
             menuItem("system_RNA", "North America", "sys", 0, 200)
            .menuItem("system_REU", "Europe", "sys", 0, 200)
            .menuItem_selected("<b>Worldwide</b>", 200);
            break;
    }
    $out.=
     "      </tr>\n"
    ."    </table></td>\n"
    ."  </tr>\n"
    ."  <tr>\n"
    ."    <td><table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class='navTable' bgcolor='#ffffff'>\n"
    ."      <tr>\n"
    .menuItem("signal_list", "Signals", "mode", 0, 50)
    .menuItem("signal_seeklist", "Seeklist", "mode", 0, 50)
    .menuItem("listener_list", "Listeners", "mode", 0, 55)
    .menuItem("cle", "CLE", "mode", 0, 30)
    .menuItem("maps", "Maps", "mode", 0, 35)
    .menuItem("tools", "Tools", "mode", 0, 35)
    .menuItem("weather", "Weather", "mode", 0, 55)
    .menuItem("stats", "Stats", "mode", 0, 35)
    .menuItem("awards", "Awards", "mode", 0, 45)
    .menuItem("poll_list", "Polls", "mode", 0, 35)
    .(isAdmin() ?
        menuItem("logoff", "Log Off", "mode", 0, 45)
     :
       menuItem("logon", "Log On", "mode", 0, 45)
     )
    .menuItem("help", "Help", "mode", 0, 35)
    .menuItem("donate", "Donate", "mode", 0, 45)
    ."      </tr>\n"
    ."    </table></td>\n"
    ."  </tr>\n";

    if (isAdmin()) {
        $out.=
         "  <tr><td><img src='".BASE_PATH."assets/spacer.gif' height='3' width='1' alt=' '></td></tr>\n"
        ."  <tr>\n"
        ."    <td><table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class='navTable' bgcolor='#ffffff'>\n"
        ."      <tr>\n"
        ."      <th class='downloadTableContent' align='right' width='51'><font color='#004400'>Admin</font></th>\n"
        .menuItem_box("admin_help", "Help", "mode", 0, 34)
        .menuItem_box("sys_info", "Info", "mode", 0, 30)
        .menuItem_box("admin_manage", "Manage", "mode", 0, 50)
        ."      <th class='downloadTableContent' align='right' width='42'><font color='#004400'>NDBList</font></th>\n"
        .menuItem_box("http://groups.yahoo.com/group/ndblist/", "Yahoo", "", 1, 32)
        ."      <th class='downloadTableContent' align='right' width='30'><font color='#004400'>XML</font></th>\n"
        .menuItem_box("xml_listener_stats", "Listener Stats", "mode", 1, 87)
        .menuItem_box("xml_signallist", "Signals", "mode", 1, 53)
        ."      </tr>\n"
        ."    </table></td>\n"
        ."  </tr>\n";
    }
    $out.=
     "</table>&nbsp;\n"
    ."<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."  <tr>\n"
    ."    <td width='100%' valign='top'>";
    switch ($mode) {
        case 'admin_manage':
            $out.= $Obj->draw();
            break;
        case 'awards':
            $out.= $Obj->draw();
            break;
        case 'signal_list':
            $out.= $Obj->html;
            break;
        case 'poll_list':
            $out.= $Obj->drawList();
            break;
        default:
            $out.= call_user_func($mode);
            break;
    }
    $out.=
     "    </td>\n"
    ."  </tr>\n"
    ."</table>\n"
    ."<br><br><hr noshade>\n"
    ."<div class='footer'>"
    ."<p>Your ".system." Editors are:</b></p>\n"
    ."<p>".system_editor."</p>"
    ."<p>Concept".(system=='RNA' ? " and initial data" : "").": Andy Robins.<br />"
    ."&copy;2015 "
    ."<script type='text/javascript'>//<!--\ndocument.write(\"<a title='Contact the Developer' href='mail\"+\"to\"+\":martin\"+\"@\"+\"classaxe\"+\".\"+\"com?subject=".system."%20System'>Martin Francis\"+\"<\/a>\");\n//--></script>"
    ."</p>\n"
    ."</div>\n"
    ."</body>\n"
    ."</html>\n";
    print $out;
}



// ************************************
// * menuitem()                       *
// ************************************
function menuItem($test, $text, $type, $new, $width)
{
    global $mode;
    if ($type=="sys") {
        if (system!=$test) {
            return
            "<td width='$width' class='navOff' onmouseover='return navOver(this,1);' onMouseOut='return navOver(this,0);' title='Click here to go to this page'><a href='".system_URL."/".$mode."?sys=$test'".($new ? " target='_blank'" : "").">$text</a></td>\n";
        }
        return    "<td width='$width' class='navSelected' title='Reload this page'><a href='".system_URL."/".$test."'".($new ? " target='_blank'" : "")."><font color='white'>$text</font></a></td>\n";
    } else {
        if ($mode!=$test) {
            return     "<td width='$width' class='navOff' onMouseOver='return navOver(this,1);' onMouseOut='return navOver(this,0);' title='Click here to go to this page'><a href='".system_URL."/".$test."'".($new ? " target='_blank'" : "").">$text</a></td>\n";
        }
        return    "<td width='$width' class='navSelected' title='Reload this page'><a href='".system_URL."/".$test."'".($new ? " target='_blank'" : "")."><font color='white'>$text</font></a></td>\n";
    }
}


// ************************************
// * menuitem_box()                   *
// ************************************
function menuItem_box($test, $text, $type, $new, $width)
{
    global $mode;
    if ($type=="sys") {
        if (system!=$test) {
            return
            "<td width='$width' class='navOff_box' title='Click here to go to this page'"
            ." onmouseover='return navOver_box(this,1);' onmouseout='return navOver_box(this,0);'>"
            ."<a href='".system_URL."/".$mode."?sys=$test'".($new ? " target='_blank'" : "").">"
            ."$text</a></td>\n";
        }
        return
         "<td width='$width' class='navSelected_box' title='Reload this page'>"
        ."<a href='".system_URL."/".$test."'".($new ? " target='_blank'" : "").">"
        ."<font color='white'>$text</font></a></td>\n";
    } elseif ($type=="mode") {
        if ($mode!=$test) {
            return
             "<td width='$width' title='Click here to go to this page'"
            ." class='navOff_box' onmouseover='return navOver_box(this,1);' onmouseout='return navOver_box(this,0);'><a href='".system_URL."/".$test."'".($new ? " target='_blank'" : "").">$text</a></td>\n";
        }
        return
         "<td width='$width' class='navSelected_box' title='Reload this page'><a href='".system_URL."/".$test."'".($new ? " target='_blank'" : "")."><font color='white'>$text</font></a></td>\n";
    } else {
        return
         "<td width='$width' class='navOff_box' title='Click here to go to this page'"
        ." onmouseover='return navOver_box(this,1);' onmouseout='return navOver_box(this,0);'>"
        ."<a href='$test'".($new ? " target='_blank'" : "").">$text</a></td>\n";
    }
}



// ************************************
// * menuitem_selected()              *
// ************************************
function menuitem_selected($text, $width)
{
    return ("<td width='$width' class='navSelected' title='Currently selected system'><font color='white'>$text</font></td>\n");
}


// ************************************
// * popup()                          *
// ************************************
function popup()
{
    global $mode, $submode, $SP;
    $out =
     "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n"
    ."<html><head>\n"
    ."<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>\n"
    ."<title>".system." > ";
    switch ($mode) {
        case "find_ICAO":            $out.= "Find ICAO Weather Station";
            break;
        case "listener_signals":    $out.= "Listener Signals";
            break;
        case "listener_edit":        $out.= "Listener Info";
            break;
        case "listener_log":        $out.= "Listener Log";
            break;
        case "listener_log_export":    $out.= "Listener Log Export";
            break;
        case "listener_map":        $out.= "Listener Map";
            break;
        case "listener_QNH":        $out.= "Listener QNH";
            break;
        case "listener_stats":        $out.= "Listener Stats";
            break;
        case "log_upload":
            switch ($submode) {
                case "":                $out.= "Log Upload (Step 1)";
                    break;
                case "save_format":        $out.= "Log Upload (Step 1)";
                    break;
                case "parse_log":        $out.= "Log Upload (Step 2)";
                    break;
                case "submit_log":        $out.= "Log Upload (Step 3)";
                    break;
            }
            break;
        case "poll_edit":            $out.= "Poll Edit";
            break;
        case "show_sp":                $out.= "State / Province Code Locator";
            break;
        case "show_itu":            $out.= "Country Code Locator";
            break;
        case "signal_attachments":    $out.= "Signal Attachments";
            break;
        case "signal_dgps_messages":$out.= "Signal DGPS Messages";
            break;
        case "signal_info":            $out.= "Signal Info";
            break;
        case "signal_listeners":    $out.= "Signal Listeners";
            break;
        case "signal_log":            $out.= "Signal Log";
            break;
        case "signal_map_eu":        $out.= "Reception Map > EU";
            break;
        case "signal_map_na":        $out.= "Reception Map > NA";
            break;
        case "signal_merge":        $out.= "Signal Move";
            break;
        case "signal_QNH":            $out.= "signal QNH";
            break;
        case "state_map":            $out.= "Detailed State Map > $SP";
            break;
    }
    if (isAdmin()) {
        $out.=    " (ADMIN)";
    }
    $out.=
     "</title>\n"
    ."<link href='".BASE_PATH."assets/style.css' rel='stylesheet' type='text/css' media='screen'>\n"
    ."<link href='".BASE_PATH.strtoLower(system).".css' rel='stylesheet' type='text/css' media='screen'>\n"
    ."<link href='".BASE_PATH."assets/print.css' rel='stylesheet' type='text/css' media='print'>\n"
    ."<script type='text/javascript' src='".BASE_PATH."assets/functions.js'></script>\n"
    ."<script type='text/javascript'>\n"
    ."//<![CDATA[\n"
    ."system_URL = '".system_URL."';\n"
    ."function map_locator(system,map_x,map_y,name,QTH,lat,lon){\n"
    ."  switch(system) {\n"
    ."    case 'eu':\n"
    ."      popWin('".system_URL."/map_locator?system=eu&map_x='+map_x+'&map_y='+map_y+'&name='+name+'&QTH='+QTH+'&lat='+lat+'&lon='+lon,'popMapLocatorEu','scrollbars=0,resizable=1',688,695,'centre');\n"
    ."    break;\n"
    ."    case 'na':\n"
    ."      popWin('".system_URL."/map_locator?system=na&map_x='+map_x+'&map_y='+map_y+'&name='+name+'&QTH='+QTH+'&lat='+lat+'&lon='+lon,'popMapLocatorNa','scrollbars=0,resizable=1',653,680,'centre');\n"
    ."    break;\n"
    ."  }\n"
    ."}\n"
    ."function signal_listeners(ID){\n"
    ."  popWin('".system_URL."/signal_listeners/'+ID,'popsignal','scrollbars=0,resizable=1',640,380,'centre');\n"
    ."}\n"
    ."//]]>\n"
    ."</script>\n"
    ."</head>\n"
    ."<body>\n";
    switch ($mode) {
        case "tools_DGPS_popup":
        case "find_ICAO":
        case "listener_signals":
        case "listener_edit":
        case "listener_log":
        case "listener_log_export":
        case "listener_map":
        case "listener_QNH":
        case "listener_stats":
        case "log_upload":
        case "show_itu":
        case "show_sp":
        case "signal_attachments":
        case "signal_dgps_messages":
        case "signal_info":
        case "signal_listeners":
        case "signal_log":
        case "signal_map_eu":
        case "signal_map_na":
        case "signal_merge":
        case "signal_QNH":
        case "state_map":
            $out.= call_user_func($mode);
            break;
        case "poll_edit":
            $Obj = new Poll;
            $out.= $Obj->edit();
            break;
    }
    $out.=
     "</body>\n"
    ."</html>\n";
    print $out;
}



// ************************************
// * mini_popup()                     *
// ************************************
function mini_popup()
{
    global $mode;
    $out =    array();
    $out[] =    "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n"
    ."<html><head>\n"
    ."<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>\n"
    ."<title>".system." > ";
    switch ($mode) {
        case "map_af":                $out[] = "Africa";
            break;
        case "map_alaska":                $out[] = "Alaska";
            break;
        case "map_as":                $out[] = "Asia";
            break;
        case "map_au":                $out[] = "Australia";
            break;
        case "map_eu":                $out[] = "Europe";
            break;
        case "map_na":                $out[] = "N.&amp; C.America + Hawaii";
            break;
        case "map_locator":                $out[] = "Map locator for Listeners";
            break;
        case "map_pacific":                $out[] = "Pacific";
            break;
        case "map_polynesia":            $out[] = "Polynesia";
            break;
        case "map_sa":                $out[] = "S.America";
            break;
        case "tools_DGPS_popup":            $out[] = "DGPS Lookup";
            break;
        case "tools_coordinates_conversion":    $out[] = "Coordinates Conversion";
            break;
        case "tools_navtex_fixer":                $out[] = "Navtex Fixer";
            break;
        case "tools_links":                $out[] = "links";
            break;
        case "tools_sunrise_calculator":        $out[] = "Sunrise Calculator";
            break;
        case "weather_lightning_canada":        $out[] = "Lightning in Canada";
            break;
        case "weather_lightning_europe":        $out[] = "Lightning in Europe";
            break;
        case "weather_lightning_na":        $out[] = "Lightning in North America";
            break;
        case "weather_metar":            $out[] = "METAR Report";
            break;
        case "weather_pressure_au":            $out[] = "Presure map for Australia";
            break;
        case "weather_pressure_europe":        $out[] = "Presure map for Europe";
            break;
        case "weather_pressure_na":            $out[] = "Presure map for North America";
            break;
        case "weather_solar_map":            $out[] = "Solar Activity Chart";
            break;
    }

    $out[] =     "</title>\n"
    ."<script language='javascript' type='text/javascript' src='".BASE_PATH."assets/functions.js'></script>\n"
    ."<script language='javascript' type='text/javascript'>\n"
        ."system_URL = '".system_URL."';\n"
    ."</script>\n"
    ."<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>\n"
    ."<link href='".BASE_PATH."assets/style.css' rel='stylesheet' type='text/css' media='screen'>\n"
        ."<link href='".BASE_PATH.strtoLower(system).".css' rel='stylesheet' type='text/css' media='screen' />\n"
    ."<link href='".BASE_PATH."assets/print.css' rel='stylesheet' type='text/css' media='print'>\n"
    ."</head>\n"
    ."<body leftmargin='0' topmargin='0' marginheight='0' marginwidth='0'>\n";
    switch ($mode) {
        case "map_af":                $out[] = map_af();
            break;
        case "map_alaska":                $out[] = map_alaska();
            break;
        case "map_as":                $out[] = map_as();
            break;
        case "map_au":                $out[] = map_au();
            break;
        case "map_eu":                $out[] = map_eu();
            break;
        case "map_locator":                $out[] = map_locator();
            break;
        case "map_na":                $out[] = map_na();
            break;
        case "map_pacific":                $out[] = map_pacific();
            break;
        case "map_polynesia":            $out[] = map_polynesia();
            break;
        case "map_sa":                $out[] = map_sa();
            break;
        case "tools_DGPS_popup":            $out[] = tools_DGPS_lookup();
            break;
        case "tools_coordinates_conversion":    $out[] = tools_coordinates_conversion();
            break;
        case "tools_navtex_fixer":                $out[] = tools_navtex_fixer();
            break;
        case "tools_links":                $out[] = tools_links();
            break;
        case "tools_sunrise_calculator":        $out[] = tools_sunrise_calculator();
            break;
        case "weather_lightning_canada":        $out[] = weather_lightning_canada();
            break;
        case "weather_lightning_europe":        $out[] = weather_lightning_europe();
            break;
        case "weather_lightning_na":        $out[] = weather_lightning_na();
            break;
        case "weather_metar":            $out[] = weather_metar();
            break;
        case "weather_pressure_europe":        $out[] = weather_pressure_europe();
            break;
        case "weather_pressure_au":            $out[] = weather_pressure_au();
            break;
        case "weather_pressure_na":            $out[] = weather_pressure_na();
            break;
        case "weather_solar_map":            $out[] = weather_solar_map();
            break;
    }
    $out[] =    "</body>\n"
    ."</html>\n";
    print (implode($out, ""));
}



function translate_chars($string)
{
    $arr_search = array(  "\n\r",
                        "\n",
                        "\r\n",
                        "\r",
                        "\\\"",
                        "\\'",
                        "&quot;",
                        "&deg;",
                        "&egrave;",
                        "&aelig;",
                        "&amp;",
                        "&aacute;",
                        "&acirc;",
                        "&aring;",
                        "&atilde;",
                        "&auml;",
                        "&ccedil;",
                        "&eacute;",
                        "&ecirc;",
                        "&iacute;",
                        "&Icirc;",
                        "&ocirc;",
                        "&Ouml;",
                        "&oslash;",
                        "&Oslash;",
                        "&ouml;",
                        "&uuml;",
                        "&Uuml;"
                        );
    $arr_replace= array(  " ",
                        " ",
                        " ",
                        " ",
                        "\"",
                        "'",
                        "\"",
                        "°",
                        "è",
                        "æ",
                        "&",
                        "á",
                        "â",
                        "å",
                        "ã",
                        "ä",
                        "ç",
                        "é",
                        "ê",
                        "í",
                        "Î",
                        "ô",
                        "Ö",
                        "ø",
                        "Ø",
                        "ö",
                        "ü",
                        "Ü"
                        );
    return str_replace($arr_search, $arr_replace, $string);
}


// ************************************
// * show_page_bar()                  *
// ************************************
function show_page_bar($record_count, $limit, $offset)
{
    $out = '';
    if ($limit>$record_count) {
        if ($record_count>10) {
            $limit = 10;
        }
        if ($record_count>25) {
            $limit = 10;
        }
        if ($record_count>50) {
            $limit =    25;
        }
        if ($record_count>100) {
            $limit = 50;
        }
        if ($record_count>250) {
            $limit = 100;
        }
        if ($record_count>1000) {
            $limit = 250;
        }
    }
    if ($record_count>10) {
        $out.=
         "<select name=\"limit\" onchange=\"send_form(document.form)\" class=\"formField\">\n"
        ."  <option value=\"10\"".($limit==10 ? " selected":"").">10 Results</option>\n"
        .($record_count>25 ?   "  <option value=\"25\"".($limit==25 ? " selected":"").">25 Results</option>\n" : "")
        .($record_count>50 ?   "  <option value=\"50\"".($limit==50 ? " selected":"").">50 Results</option>\n" : "")
        .($record_count>100 ?  "  <option value=\"100\"".($limit==100 ? " selected":"").">100 Results</option>\n" : "")
        .($record_count>250 ?  "  <option value=\"250\"".($limit==250 ? " selected":"").">250 Results</option>\n" : "")
        ."  <option value=\"-1\"". ($limit==-1 ?" selected":"").">All Results</option>\n"
        ."</select>\n"
        .($limit!=-1 ?
             "<input type='button' class='formbutton'".($offset==0 ? " disabled": "")." name='previous' value='&lt;'"
            ." onclick='document.form.offset.selectedIndex=document.form.offset.selectedIndex-1;send_form(form);'>\n"
            ."<input type='button' class='formbutton'".($offset+$limit>$record_count ? " disabled": "")." name='next' value='&gt;'"
            ." onclick='document.form.offset.selectedIndex=document.form.offset.selectedIndex+1;send_form(form);'>\n"
         :
            ""
         );
        if ($limit!=-1) {
            $out.=
            "<select name=\"offset\" onchange=\"send_form(document.form)\" class=\"formField\">\n";
            for ($i=0; $i<$record_count; $i = $i+$limit) {
                $out.="  <option value=\"".$i."\"".($offset==$i ? " selected":"").">".($i+1)."-".($i+$limit>$record_count ? $record_count : $i+$limit)."</option>\n";
            }
            $out.=
            "</select> of ".$record_count."\n";
        } else {
            $out.=
            "<input type=\"hidden\" name=\"offset\" value=\"".$offset."\">Showing "
            .($limit==-1 ? " all of " : " ".(1+$offset)." to ".($offset+$limit > $record_count ? $record_count : $offset+$limit)." of ").$record_count." records&nbsp;\n";
        }
    } else {
        $out.=
         "<input type=\"hidden\" name=\"limit\" value=\"".$limit."\"><input type=\"hidden\" name=\"offset\" value=\"0\">\n"
        .$record_count." record".($record_count<>1 ? "s" : "").".\n";
    }
    return $out;
}



// ************************************
// * show_itu()                        *
// ************************************
function show_itu()
{
    global $region;
    $cols =        2;
    $out =        array();
    $out[] =        "<form><a name='top'></a>\n";
    $out[] =        "<table border='0' align='center' cellpadding='0' cellspacing='1' class='tableContainer'>\n";
    $out[] =        "  <tr>\n";
    $out[] =        "    <td><table border='0' align='center' cellpadding='0' cellspacing='0'>\n";
    $out[] =        "      <tr>\n";
    $out[] =        "        <td class='downloadTableContent' colspan='2' width='100%'><h1>".system." Country Code Locator</h1>\n";
    $out[] =        "        <p class='help'>Countries</b> in this system are given by NDB List approved <a href='http://www.beaconworld.org.uk/files/countrylist.pdf' target='_blank' title='NDBList country, state and province codes'><b>standard codes</b></a>.<br>\n";
    $out[] =        "<script language=javascript' type='text/javascript'>if (window.opener && window.opener.form && (window.opener.form.ITU || window.opener.form.filter_itu)) { document.write(\"<b>Click</b> on any entry to copy it automatically to the form.</b>\"); }</script></p>\n";

    $regions =        array();
    $sql =         "SELECT\n"
    ."  `name`,\n"
    ."  `region`\n"
    ."FROM\n"
    ."  `region`\n"
    .($region!="" ? "WHERE `region` IN ('".implode("','", explode("|", $region))."')" : "")
    ."ORDER BY `ID`";

    $result =        @mysql_query($sql);
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =        mysql_fetch_array($result, MYSQL_ASSOC);
        $regions[] =    array("name"=>$row["name"], "region"=>$row["region"]);
    }

    $links =        array();
    for ($i=0; $i<count($regions); $i++) {
        $links[] =        "<nobr><a href='#".$regions[$i]["region"]."'><b>".$regions[$i]["name"]."</b></a></nobr>";
    }
    $out[] =        "<p align='center'><small>[ ".implode($links, " |\n")." ]</small></p>\n";

    for ($h=0; $h<count($regions); $h++) {
        $out[] =        "        <table cellpadding='2' border='0' cellspacing='1' class='downloadtable' width='100%'>\n";
        $out[] =        "          <tr class='rownormal'>\n";
        $out[] =        "            <th class='downloadTableHeadings_nosort' align='center'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
        $out[] =        "              <tr>\n";
        $out[] =        "                <th class='downloadTableHeadings_nosort' align='left'><a name='".$regions[$h]["region"]."'></a>".$regions[$h]["name"]."</th>\n";
        $out[] =        "                <th class='downloadTableHeadings_nosort' align='right'>[";

        switch($regions[$h]["region"]) {
            case "af":    $out[] = "<a href='/dx/images/af_map.gif' target='_blank' class='yellow'><b>Map</b></a> | ";
                break;
            case "au":    $out[] = "<a href='/dx/images/au_map.gif' target='_blank' class='yellow'><b>Map</b></a> | ";
                break;
            case "ca":    $out[] = "<a href='".system_URL."/generate_map_na' target='_blank' class='yellow'><b>Map</b></a> | ";
                break;
            case "eu":    $out[] = "<a href='".system_URL."/generate_map_eu' target='_blank' class='yellow'><b>Map</b></a> | ";
                break;
            case "na":    $out[] = "<a href='".system_URL."/generate_map_na' target='_blank' class='yellow'><b>Map</b></a> | ";
                break;
            case "sa":    $out[] = "<a href='/dx/images/sa_map.gif' target='_blank' class='yellow'><b>Map</b></a> | ";
                break;
        }
        $out[] =        "<a href='#top' class='yellow'><b>Top</b></a>]</th>\n";
        $out[] =        "              </tr>\n";
        $out[] =        "            </table></th>\n";
        $out[] =        "          </tr>\n";
        $out[] =        "          <tr class='rownormal'>\n";
        $out[] =        "            <td class='downloadTableContent'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
        $sql =        "SELECT `ITU`,`name` FROM `itu` WHERE `region` = '".$regions[$h]["region"]."' ORDER BY `name`";
        $result =        mysql_query($sql);
        $itu_arr =        array();
        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $row =        mysql_fetch_array($result, MYSQL_ASSOC);
            $itu_arr[] =    array("ITU"=>$row['ITU'],"name"=>$row['name']);
        }
        $cells_col =        ceil(count($itu_arr)/$cols);
        for ($i=0; $i<$cells_col; $i++) {
            $out[] =        "          <tr>\n";
            for ($j=0; $j<$cols; $j++) {
                if ($i+($j*$cells_col) < count($itu_arr)) {
                    $out[] =    "            <td onclick=\"itu('".$itu_arr[$i+($j*$cells_col)]["ITU"]."')\" onMouseOver='return sp_itu_over(this,1);' onMouseOut='return sp_itu_over(this,0);' width='".(int)100/$cols."%'>\n";
                    $out[] =    "            <table cellpadding='0' cellspacing='0' width='100%'>\n";
                    $out[] =    "              <tr>\n";
                    $out[] =    "                <td nowrap valign='top'>".$itu_arr[$i+($j*$cells_col)]["name"]."</td>\n";
                    $out[] =    "                <td nowrap valign='top' align='right'>".$itu_arr[$i+($j*$cells_col)]["ITU"]."</td>\n";
                    $out[] =    "              </tr>\n";
                    $out[] =    "            </table></td>\n";
                } else {
                    $out[] =    "            <td>&nbsp;</td>\n";
                }
                if ($j!=$cols-1) {
                    $out[] =    "            <td width='30' nowrap>&nbsp;</td>\n";
                }
            }
            $out[] =        "          </tr>\n";
        }
        $out[] =        "        </table></td>\n";
        $out[] =        "          </tr>\n";
        $out[] =        "        </table>\n";
        $out[] =        "<br><br>\n";
    }
    $out[] =        "<p align='center'>";
    $out[] =        "<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> ";
    $out[] =        "<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> ";
    $out[] =        "</p>";

    return implode($out, "");
}



// ************************************
// * show_sortable_column_head()      *
// ************************************
function show_sortable_column_head($hint, $label, $sortBy, $test, $order, $lblImage)
{
    return
     "<th class='downloadTableHeadings' valign='bottom' align='left'"
    ." onmouseover=\"column_over(this,1);\""
    ." onmouseout=\"column_over(this,0);\""
    ." onmousedown=\"column_over(this,2);\""
    ." onclick=\"document.form.sortBy.value='"
    .$test
    .($order=='A-Z' ? ($sortBy==$test ? '_d' : '') : ('_d'.$sortBy==$test.'_d' ? '' : '_d'))
    ."';document.form.submit()\""
    ." title=\"".$hint."\">"
    .($lblImage ? "" : $label.' ')
    .($sortBy==$test ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : '')
    .($sortBy==$test."_d" ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : '')
    .($lblImage ? ($sortBy==$test || $sortBy==$test."_d" ? "<br><br>" : "").$label.' ': '')
    ."</th>\n";
}




// ************************************
// * show_sp()                        *
// ************************************
function show_sp()
{
    $cols =        3;
    $out =
     "<form>\n"
    ."<table border='0' align='center' cellpadding='0' cellspacing='1' class='tableContainer'>\n"
    ."  <tr>\n"
    ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0'>\n"
    ."      <tr>\n"
    ."        <td class='downloadTableContent' colspan='2' width='100%'><h1>".system." State and Province Locator</h1>\n"
    ."        <p class='help'>States and provinces</b> in this system are given by NDB List approved <a href='http://www.beaconworld.org.uk/files/countrylist.pdf' target='_blank' title='NDBList country, state and province codes'><b>standard codes</b></a>.<br>\n"
    ."<script language=javascript' type='text/javascript'>if (window.opener && window.opener.form && (window.opener.form.SP || window.opener.form.filter_sp)) { document.write(\"<b>Click</b> on any entry to copy it automatically to the form.</b>\"); }</script></p>\n"
    ."        <table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
    ."          <tr class='rownormal'>\n"
    ."            <th class='downloadTableHeadings' align='center'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."              <tr>\n"
    ."                <th class='downloadTableHeadings_nosort' align='left'>Canadian Provinces</th>\n"
    ."                <th class='downloadTableHeadings_nosort' align='right'>[<a href='".system_URL."/map_na' target='_blank' class='yellow'><b>Map</b></a>]</td>\n"
    ."              </tr>\n"
    ."            </table></th>\n"
    ."          </tr>\n"
    ."          <tr class='rownormal'>\n"
    ."            <td class='downloadTableContent'><table cellpadding='0' cellspacing='0' border='0'>\n";
    $sql =        "SELECT * FROM `sp` WHERE `ITU` = 'CAN'";
    $result =        mysql_query($sql);
    $sp_arr =        array();
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =        mysql_fetch_array($result, MYSQL_ASSOC);
        $sp_arr[] =        array('SP' => $row['SP'], 'ITU' => $row['ITU'], 'name' => $row['name']);
    }
    $cells_col =        ceil(count($sp_arr)/$cols);
    for ($i=0; $i<$cells_col; $i++) {
        $out.=        "              <tr>\n";
        for ($j=0; $j<$cols; $j++) {
            if ($i+($j*$cells_col) < count($sp_arr)) {
                $out.=
                 "                <td onclick=\"sp('".$sp_arr[$i+($j*$cells_col)]['SP']."','".$sp_arr[$i+($j*$cells_col)]['ITU']."')\" onMouseOver='return sp_itu_over(this,1);' onMouseOut='return sp_itu_over(this,0);'>\n"
                ."                <table cellpadding='0' cellspacing='0' border='0'>\n"
                ."                  <tr>\n"
                ."                    <td width='140' nowrap>".$sp_arr[$i+($j*$cells_col)]['name']."</td>\n"
                ."                    <td width='20'>".$sp_arr[$i+($j*$cells_col)]['SP']."</td>\n"
                ."                  </tr>\n"
                ."                </table></td>\n";
                if ($j<$cols-1) {
                    $out.=    "                <td width='10'>&nbsp;</td>\n";
                }
            } else {
                $out.=    "                <td>&nbsp;</td>\n";
                if ($j<$cols-1) {
                    $out.=    "                <td width='10'>&nbsp;</td>\n";
                }
            }
        }
        $out.=        "              </tr>\n";
    }
    $out.=
     "            </table></td>\n"
    ."          </tr>\n"
    ."        </table>\n"
    ."        <br><br>\n"
    ."        <table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
    ."          <tr class='rownormal'>\n"
    ."            <th class='downloadTableHeadings' align='center'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."              <tr>\n"
    ."                <th class='downloadTableHeadings_nosort' align='left'>USA States</th>\n"
    ."                <th class='downloadTableHeadings_nosort' align='right'>[<a href='".system_URL."/map_na' target='_blank' class='yellow'><b>Map</b></a>]</td>\n"
    ."              </tr>\n"
    ."            </table></th>\n"
    ."          </tr>\n"
    ."          <tr class='rownormal'>\n"
    ."            <td class='downloadTableContent'>\n"
    ."            <table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
    $sql =        "SELECT * FROM `sp`";
    $result =        @mysql_query($sql);
    $sp_arr =        array();
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =        mysql_fetch_array($result, MYSQL_ASSOC);
        $sp_arr[] =        array('SP' => $row['SP'], 'ITU' => $row['ITU'], 'name' => $row['name']);
    }
    $cells_col =        ceil(count($sp_arr)/$cols);
    for ($i=0; $i<$cells_col; $i++) {
        $out.=        "              <tr>\n";
        for ($j=0; $j<$cols; $j++) {
            if ($i+($j*$cells_col) < count($sp_arr)) {
                $out.=
                 "                <td onclick=\"sp('".$sp_arr[$i+($j*$cells_col)]['SP']."','".$sp_arr[$i+($j*$cells_col)]['ITU']."')\" onMouseOver='return sp_itu_over(this,1);' onMouseOut='return sp_itu_over(this,0);'>\n"
                ."                <table cellpadding='0' cellspacing='0'>\n"
                ."                  <tr>\n"
                ."                    <td width='140' nowrap>".$sp_arr[$i+($j*$cells_col)]['name']."</td>\n"
                ."                    <td width='20'>".$sp_arr[$i+($j*$cells_col)]['SP']."</td>\n"
                ."                  </tr>\n"
                ."                </table></td>\n";
                if ($j<$cols-1) {
                    $out.=    "                <td width='10'>&nbsp;</td>\n";
                }
            } else {
                $out.=    "                <td>&nbsp;</td>\n";
                if ($j<$cols-1) {
                    $out.=    "                <td width='10'>&nbsp;</td>\n";
                }
            }
        }
        $out.=        "              </tr>\n";
    }
    $out.=
     "            </table></td>\n"
    ."          </tr>\n"
    ."        </table>\n";
    $cols =        2;

    $out.=
     "        <br><br>\n"
    ."        <table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
    ."          <tr class='rownormal'>\n"
    ."            <th class='downloadTableHeadings' align='center'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."              <tr>\n"
    ."                <th class='downloadTableHeadings_nosort' align='left'>Australian Territories</th>\n"
    ."                <th class='downloadTableHeadings_nosort' align='right'>[<a href='".system_URL."/map_au' target='_blank' class='yellow'><b>Map</b></a>]</td>\n"
    ."              </tr>\n"
    ."            </table></th>\n"
    ."          </tr>\n"
    ."          <tr class='rownormal'>\n"
    ."            <td class='downloadTableContent'>\n"
    ."            <table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
    $sql =        "SELECT * FROM `sp` WHERE `ITU` = 'AUS'";
    $result =        mysql_query($sql);
    $sp_arr =        array();
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =        mysql_fetch_array($result, MYSQL_ASSOC);
        $sp_arr[] =        array('SP' => $row['SP'], 'ITU' => $row['ITU'], 'name' => $row['name']);
    }
    $cells_col =        ceil(count($sp_arr)/$cols);
    for ($i=0; $i<$cells_col; $i++) {
        $out.=        "              <tr>\n";
        for ($j=0; $j<$cols; $j++) {
            if ($i+($j*$cells_col) < count($sp_arr)) {
                $out.=
                 "                <td onclick=\"sp('".$sp_arr[$i+($j*$cells_col)]['SP']."','".$sp_arr[$i+($j*$cells_col)]['ITU']."')\" onMouseOver='return sp_itu_over(this,1);' onMouseOut='return sp_itu_over(this,0);'>\n"
                ."                <table cellpadding='0' cellspacing='0'>\n"
                ."                  <tr>\n"
                ."                    <td width='210' nowrap>".$sp_arr[$i+($j*$cells_col)]['name']."</td>\n"
                ."                    <td width='30'>".$sp_arr[$i+($j*$cells_col)]['SP']."</td>\n"
                ."                  </tr>\n"
                ."                </table></td>\n";
                if ($j<$cols-1) {
                    $out.=    "                <td width='10'>&nbsp;</td>\n";
                }
            } else {
                $out.=    "                <td>&nbsp;</td>\n";
                if ($j<$cols-1) {
                    $out.=    "                <td width='10'>&nbsp;</td>\n";
                }
            }
        }
        $out.=        "              </tr>\n";
    }
    $out.=
     "            </table></td>\n"
    ."          </tr>\n"
    ."        </table><br>\n"
    ."        </td>\n"
    ."      </tr>\n"
    ."    </table></td>\n"
    ."  </tr>\n"
    ."</table>\n"
    ."<p align='center'>"
    ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
    ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> "
    ."</p>";
    return $out;
}



// ************************************
// * table_uniqID()                   *
// ************************************
function table_uniqID($table)
{
    $notDone =    true;
    $n =         0 ;
    while ($notDone) {
        $ID =     uniqid('');
        $sql =    "SELECT COUNT(*) FROM `$table` WHERE ID = '$ID'";
        $result =    mysql_query($sql);
        $row =     mysql_fetch_row($result);
        $notDone =    $row[0]>0;
    }
    return $ID;
}


function update_listener_log_count($listenerID)
{
    $listener = new Listener($listenerID);
    return $listener->updateLogCount();
}


function find_ICAO()
{
    global $mode,$submode,$GSQ_icao;
    $out =
     "<form action='".system_URL."' method='POST'>\n"
    ."<input type='hidden' name='mode' value='$mode'>\n"
    ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable' width='100%'>\n"
    ."  <tr>\n"
    ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."      <tr>\n"
    ."        <th align='left' class='downloadTableHeadings_nosort'>ICAO Lookup</th>\n"
    ."      </tr>\n"
    ."    </table></th>\n"
    ."  </tr>\n"
    ."  <tr>\n"
    ."    <td class='downloadTableContent'>GSQ <input name='GSQ_icao' value='$GSQ_icao' class='formField' size='6' maxlength='6'>\n"
    ."<input type='submit' value='GO' class='formButton'><br>\n";
    if ($GSQ_icao) {
        $out.=
         "Showing nearest 100 stations<br><span class='formFixed'>ICAO KM   Miles</span><br>\n"
        ."<select size='5' class='formFixed'>\n";
        $icao_arr =    get_local_icao($GSQ_icao, 100, 0);
        for ($i=0; $i<100; $i++) {
            $out.=    "<option value='".$icao_arr[$i]['ICAO']."'>".$icao_arr[$i]['ICAO']." ".pad_nbsp($icao_arr[$i]['km'], 5).pad_nbsp($icao_arr[$i]['miles'], 5)."</option>\n";
        }
        $out.=    "</select>\n";
    }
    $out.=
     "</td>\n"
    ."  </tr>\n"
    ."</table>\n"
    ."</form>\n";
    return implode($out, "");
}



// ************************************
// * xml_signallist()                 *
// ************************************
function xml_signallist()
{
    set_time_limit(600);    // Extend maximum execution time to 10 mins
    $out =    array();
    $out[] =    '<?xml version="1.0" encoding="UTF-8"?>';
    $out[] =    "<signallist>\n";
    $sql =    "SELECT * FROM `signals`";
    $result =    mysql_query($sql);
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =    mysql_fetch_array($result, MYSQL_ASSOC);
        $out[] =    "  <signal"
        ." ID=\"".$row['ID']."\""
        ." active=\"".$row['active']."\""
        ." call=\"".$row['call']."\""
        ." GSQ=\"".$row['GSQ']."\""
        ." heard_in=\"".$row['heard_in']."\""
        ." ITU=\"".$row['ITU']."\""
        ." khz=\"".$row['khz']."\""
        ." last_heard=\"".$row['last_heard']."\""
        ." lat=\"".$row['lat']."\""
        ." logs=\"".$row['logs']."\""
        ." lon=\"".$row['lon']."\""
        ." LSB=\"".$row['LSB']."\""
        ." LSB_approx=\"".$row['LSB_approx']."\""
        ." notes=\"".$row['notes']."\""
        ." SP=\"".$row['SP']."\""
        ." USB=\"".$row['USB']."\""
        ." USB_approx=\"".$row['USB_approx']."\""
        .">\n";
        $sql =    "SELECT * FROM `logs` WHERE `signalID` = ".$row['ID']." AND `listenerID` !=''";
        $result2 =    mysql_query($sql);
        for ($j=0; $j<mysql_num_rows($result2); $j++) {
            $row2 =    mysql_fetch_array($result2, MYSQL_ASSOC);
            $out[] =    "    <log"
            ." ID=\"".$row2['ID']."\""
            ." date=\"".$row2['date']."\""
            ." dx_km=\"".$row2['dx_km']."\""
            ." dx_miles=\"".$row2['dx_miles']."\""
            ." listenerID=\"".$row2['listenerID']."\""
            ." LSB=\"".$row2['LSB']."\""
            ." LSB_approx=\"".$row2['LSB_approx']."\""
            ." heard_in=\"".$row2['heard_in']."\""
            ." time=\"".$row2['time']."\""
            ." USB=\"".$row2['USB']."\""
            ." USB_approx=\"".$row2['USB_approx']."\""
            ."/>\n";
        }
        $out[] =    "</signal>\n";
    }
    $out[] =    "</signallist>\n";
    print implode($out, "");
}


function xml_listener_stats()
{
    global $listenerID;
    $out =
     "<"."?xml version=\"1.0\" encoding=\"UTF-8\" ?".">\n"
    ."<!DOCTYPE listeners [\n"
    ."<!ENTITY Agrave \"&#192;\">\n"
    ."<!ENTITY Aacute \"&#193;\">\n"
    ."<!ENTITY Acirc  \"&#194;\">\n"
    ."<!ENTITY Atilde \"&#195;\">\n"
    ."<!ENTITY Auml   \"&#196;\">\n"
    ."<!ENTITY Aring  \"&#197;\">\n"
    ."<!ENTITY AElig  \"&#198;\">\n"
    ."<!ENTITY Ccedil \"&#199;\">\n"
    ."<!ENTITY Egrave \"&#200;\">\n"
    ."<!ENTITY Eacute \"&#201;\">\n"
    ."<!ENTITY Ecirc  \"&#202;\">\n"
    ."<!ENTITY Euml   \"&#203;\">\n"
    ."<!ENTITY Igrave \"&#204;\">\n"
    ."<!ENTITY Iacute \"&#205;\">\n"
    ."<!ENTITY Icirc  \"&#206;\">\n"
    ."<!ENTITY Iuml   \"&#207;\">\n"
    ."<!ENTITY ETH    \"&#208;\">\n"
    ."<!ENTITY Ntilde \"&#209;\">\n"
    ."<!ENTITY Ograve \"&#210;\">\n"
    ."<!ENTITY Oacute \"&#211;\">\n"
    ."<!ENTITY Ocirc  \"&#212;\">\n"
    ."<!ENTITY Otilde \"&#213;\">\n"
    ."<!ENTITY Ouml   \"&#214;\">\n"
    ."<!ENTITY Oslash \"&#216;\">\n"
    ."<!ENTITY Ugrave \"&#217;\">\n"
    ."<!ENTITY Uacute \"&#218;\">\n"
    ."<!ENTITY Ucirc  \"&#219;\">\n"
    ."<!ENTITY Uuml   \"&#220;\">\n"
    ."<!ENTITY Yacute \"&#221;\">\n"
    ."<!ENTITY THORN  \"&#222;\">\n"
    ."<!ENTITY szlig  \"&#223;\">\n"
    ."<!ENTITY agrave \"&#224;\">\n"
    ."<!ENTITY aacute \"&#225;\">\n"
    ."<!ENTITY acirc  \"&#226;\">\n"
    ."<!ENTITY atilde \"&#227;\">\n"
    ."<!ENTITY auml   \"&#228;\">\n"
    ."<!ENTITY aring  \"&#229;\">\n"
    ."<!ENTITY aelig  \"&#230;\">\n"
    ."<!ENTITY ccedil \"&#231;\">\n"
    ."<!ENTITY egrave \"&#232;\">\n"
    ."<!ENTITY eacute \"&#233;\">\n"
    ."<!ENTITY ecirc  \"&#234;\">\n"
    ."<!ENTITY euml   \"&#235;\">\n"
    ."<!ENTITY igrave \"&#236;\">\n"
    ."<!ENTITY iacute \"&#237;\">\n"
    ."<!ENTITY icirc  \"&#238;\">\n"
    ."<!ENTITY iuml   \"&#239;\">\n"
    ."<!ENTITY eth    \"&#240;\">\n"
    ."<!ENTITY ntilde \"&#241;\">\n"
    ."<!ENTITY ograve \"&#242;\">\n"
    ."<!ENTITY oacute \"&#243;\">\n"
    ."<!ENTITY ocirc  \"&#244;\">\n"
    ."<!ENTITY otilde \"&#245;\">\n"
    ."<!ENTITY ouml   \"&#246;\">\n"
    ."<!ENTITY oslash \"&#248;\">\n"
    ."<!ENTITY ugrave \"&#249;\">\n"
    ."<!ENTITY uacute \"&#250;\">\n"
    ."<!ENTITY ucirc  \"&#251;\">\n"
    ."<!ENTITY uuml   \"&#252;\">\n"
    ."<!ENTITY yacute \"&#253;\">\n"
    ."<!ENTITY thorn  \"&#254;\">\n"
    ."<!ENTITY yuml   \"&#255;\">\n"
    ."]>\n"
    ."<listeners>\n";

    $listeners =    array();
    $sql =
     "SELECT\n"
    ."  *\n"
    ."FROM\n"
    ."  `listeners`\n"
    .($listenerID ? "WHERE `ID` = ".addslashes($listenerID)."\n" : "")
    ."ORDER BY `name`,`SP`,`ITU`";

    $result =    @mysql_query($sql);
    for ($i=0; $i<mysql_num_rows($result); $i++) {
        $row =    mysql_fetch_array($result, MYSQL_ASSOC);
        $listeners[$row["ID"]] =
        array(
        "count_signals" =>    $row["count_signals"],
        "callsign" =>        $row["callsign"],
        "equipment" =>        $row["equipment"],
        "GSQ" =>        $row["GSQ"],
        "ITU" =>        $row["ITU"],
        "lat" =>        $row["lat"],
        "count_logs" =>        $row["count_logs"],
        "log_latest" =>        $row["log_latest"],
        "lon" =>        $row["lon"],
        "name" =>        $row["name"],
        "notes" =>        $row["notes"],
        "QTH" =>        $row["QTH"],
        "SP" =>            $row["SP"],
        "website" =>        $row["website"],
        "log_dx" =>array(
         "dx0"=>0,
        "dx1000"=>0,
        "dx2000"=>0,
        "dx3000"=>0,
        "dx4000"=>0,
        "dx5000"=>0,
        "dx6000"=>0,
        "dx7000"=>0
        )
        );
    }

    for ($i=0; $i<8000; $i+=1000) {
        $sql =
         "SELECT\n"
        ."  `listeners`.`ID`,\n"
        ."  COUNT(*) AS `logs`\n"
        ."FROM\n"
        ."  `listeners`,\n"
        ."  `logs`\n"
        ."WHERE\n"
        ."  `listeners`.`ID` = `logs`.`listenerID` AND\n"
        ."  `logs`.`dx_miles`>=$i AND\n"
        ."  `logs`.`dx_miles`<=".($i+1000)."\n"
        .($listenerID ? "AND `listenerID` = ".addslashes($listenerID)."\n" : "")
        ."GROUP BY\n"
        ."  `listeners`.`ID`";
  //  print("<pre>$sql</pre>");
        $result =    @mysql_query($sql);
        for ($j=0; $j<mysql_num_rows($result); $j++) {
            $row =    mysql_fetch_array($result, MYSQL_ASSOC);
            $listeners[$row["ID"]]["log_dx"]["dx".$i] = $row["logs"];
        }
    }

    foreach ($listeners as $key => $value) {
        $out.=
         "<listener"
        ."  ID=\"".$key."\""
        ."  name=\"".urlencode($value["name"])."\""
        ."  callsign=\"".$value["callsign"]."\""
        ."  QTH=\"".$value["QTH"]."\""
        ."  SP=\"".$value["SP"]."\""
        ."  ITU=\"".$value["ITU"]."\""
        .">\n"
        ."  <log_stats"
        ."  count_total=\"".$value["count_signals"]."\""
        ."  count_logs=\"".$value["count_logs"]."\""
        ."  log_latest=\"".($value["log_latest"]!="0000-00-00" ? $value["log_latest"] : "")."\""
        .">\n"
        ."    <dx ";
        for ($i=0; $i<8000; $i+=1000) {
            $out.=    "dx$i=\"".$value["log_dx"]["dx$i"]."\" ";
        }
        $out.=
         "/>\n"
        ."  </log_stats>\n"
        ."  </listener>\n";
    }
    $out.=    "</listeners>\n";
    header('Content-Type: application/xml');
    print $out;
}



function MMM_to_MM($MMM)
{
    switch (strToUpper($MMM)) {
        case "JAN":
            return "01";
        break;
        case "FEB":
            return "02";
        break;
        case "MAR":
            return "03";
        break;
        case "APR":
            return "04";
        break;
        case "MAY":
            return "05";
        break;
        case "JUN":
            return "06";
        break;
        case "JUL":
            return "07";
        break;
        case "AUG":
            return "08";
        break;
        case "SEP":
            return "09";
        break;
        case "OCT":
            return "10";
        break;
        case "NOV":
            return "11";
        break;
        case "DEC":
            return "12";
        break;
    }
}

function MM_to_MMM($MM)
{
    switch ($MM) {
        case "01":
            return "Jan";
        break;
        case "02":
            return "Feb";
        break;
        case "03":
            return "Mar";
        break;
        case "04":
            return "Apr";
        break;
        case "05":
            return "May";
        break;
        case "06":
            return "Jun";
        break;
        case "07":
            return "Jul";
        break;
        case "08":
            return "Aug";
        break;
        case "09":
            return "Sep";
        break;
        case "10":
            return "Oct";
        break;
        case "11":
            return "Nov";
        break;
        case "12":
            return "Dec";
        break;
    }
}


function tabItem($text, $test, $width)
{
    global $mode, $ID;
    return
     "<td class='".($test==$mode ? 'tabSelected' : 'tabOff')."'"
    ." title='".($test==$mode ? 'Reload this page' : 'Change mode')."'"
    ." onclick='document.location=\"".system_URL."/".$test."/".$ID."\"'"
    .($test!=$mode ? " onmouseover='return tabOver(this,1);'" : "")
    .($test!=$mode ? " onmouseout='return tabOver(this,0);'" : "")
    ." width='$width'>$text</td>\n";
}


function YY_to_YYYY($YY)
{
    $YY =    trim($YY);
    if (strLen($YY)==4) {
        return $YY;
    }
    if ($YY<70) {
// Dates from 1970 to 2069 acceptable
        return "20".$YY;
    }
    return "19".$YY;
}


function M_to_MM($M)
{
    $M =    trim($M);
    if (strLen($M)==1) {
        return "0".$M;
    }
    return $M;
}

function D_to_DD($D)
{
    $D =    trim($D);
    if (strLen($D)==1) {
        return "0".$D;
    }
    return $D;
}

function pad($text, $places)
{
    return $text.(substr("                                                   ", 0, $places-strLen(preg_replace("/&[^;]+;/", " ", $text))));
}

function lead($text, $places)
{
    return (substr("                                                   ", 0, $places-strLen(preg_replace("/&[^;]+;/", " ", $text))).$text);
}

function lead_zero($text, $places)
{
    return (substr("0000", 0, $places-strlen($text)).$text);
}

function pad_dot($text, $places)
{
    $text = translate_chars($text);
    if (strlen($text)>=$places) {
        return str_replace(" ", "&nbsp;", substr($text, 0, $places));
    }
    $text = $text.(substr("..................................................", 0, $places-strLen($text)));
    return str_replace(" ", "&nbsp;", $text);
}

function pad_nbsp($text, $places)
{
    $text = translate_chars($text);
    $text = $text.(substr("                                                   ", 0, $places-strLen($text)));
    return str_replace(" ", "&nbsp;", $text);
}

// No longer used - had problems with quoted strings etc.
function titleCase($string)
{
    $tmp =    explode(" ", $string);
    for ($i=0; $i<count($tmp); $i++) {
        if (substr($tmp[$i], 0, 1)=="(" or substr($tmp[$i], 0, 1)=="'") {
            $tmp[$i] =    strToUpper(substr($tmp[$i], 0, 2)).strToLower(substr($tmp[$i], 2));
        } else {
            $tmp[$i] =    strToUpper(substr($tmp[$i], 0, 1)).strToLower(substr($tmp[$i], 1));
        }
    }
    return implode($tmp, " ");
}

function y($var)
{
    var_dump($var);
}

function z($sql)
{
    print("<pre>$sql</pre>");
}
