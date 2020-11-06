<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:49 PM
 */
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   RNA / REU / RWW              *
// * Filename:  signal.php                   *
// * Email:     martin@classaxe.com          *
// *******************************************
// Note: all functions are declared in alphabetical order

/*
Version History:
  1.0.5 (2013-09-27)
    1) Bug report from Leszek SP7VCY:
       "Could you take a look at the distance sorting at Rxx (KM and Miles columns)?
       It seems that there is a sorting by letters instead of numbers."
       Changes to signal_list() to deal with this
  1.0.4 (2013-09-24)
    1) Added static colors to standardise display of signal colours per type
    2) Added support for DSC in signal_info()
    3) Added support for DSC in signal_list()
  1.0.3 (2009-08-24)
    1) Changes for XHTML strict and to remove legacy support for NS4
  1.0.2 (2009-02-07)
    1) New references for DGPS lists (thanks Alan Gale)
  1.0.1 (2008-11-30)
    1) Added links for khz, SP and ITU selection
    2) State maps now linked via location string not SP - frees it up for filter selections instead
  1.0.0 (2004-04-25)
    Initial release
*/

namespace Rxx\Tools;

/**
 * Class Signal
 * @package Rxx\Tools
 */
class Signal
{
    /**
     * @return string
     */
    public static function signal_dgps_messages()
    {
        global $ID, $mode, $submode, $sortBy;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        if ($sortBy=='') {
            $sortBy = "`attachment`.`type`,`attachment`.`title`";
        }
        switch ($submode) {
            case "view":
                break;
        }
        $Obj =      new \Rxx\Signal($ID);
        if ($ID) {
            $row =              $Obj->getRecord();
            $active =           $row["active"];
            $call =             $row["call"];
            $first_heard =      $row["first_heard"];
            $format =           stripslashes($row["format"]);
            $GSQ =              $row["GSQ"];
            $heard_in_html =    $row["heard_in_html"];
            $ITU =              $row["ITU"];
            $khz =              $row["khz"];
            $lat =              $row["lat"];
            $lon =              $row["lon"];
            $logs =             $row["logs"];
            $LSB =              $row["LSB"];
            $LSB_approx =       $row["LSB_approx"];
            $last_heard =       $row["last_heard"];
            $notes =            stripslashes($row["notes"]);
            $pwr =              $row["pwr"];
            $QTH =              $row["QTH"];
            $sec =              $row["sec"];
            $SP =               $row["SP"];
            $type =             $row["type"];
            $USB =              $row["USB"];
            $USB_approx =       $row["USB_approx"];
            $submode =          "update";
        } else {
            $submode =    "add";
            $active =    "1";
        }
        $out =
            "<table border='0' cellpadding='0' cellspacing='0' width='100%'>\n"
            ."  <tr>\n"
            ."    <td><h1>DGPS Messages</h1></td>\n"
            ."    <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
            ."      <tr>\n"
            .$Obj->tabs()
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>\n"
            ."</table>\n"
            ."<table border='0' align='center' cellpadding='0' cellspacing='1' class='tableContainer' width='100%'>\n"
            ."  <tr>\n"
            ."    <td bgcolor='#F5F5F5' class='itemTextCell' height='100%' valign='top'>\n";
        $dgps_messages = $Obj->getDgpsMessages();
        if (count($dgps_messages)) {
            $out.=
                "      <table width='100%' border='0' cellpadding='2' cellspacing='1' class='downloadTable'>\n"
                ."        <tr>\n"
                ."          <th class='downloadTableHeadings_nosort' align='left'>"
                ."&nbsp;DGPS Messages for ".(float)$khz."-".$call.($QTH ? ", $QTH" : "").($SP ? ", $SP" : "").($ITU ? ", $ITU" : "")
                ."</th>\n"
                ."        </tr>\n"
                ."        <tr>\n"
                ."          <td bgcolor='white'>\n"
                ."          <table cellpadding='0' cellspacing='1' border='0' class='noprint'>\n"
                ."            <tr>\n"
                ."              <th class='scroll_list' width='90' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=".($sortBy=='type' ? 'type_d' : 'type')."'>".($sortBy=='type'||$sortBy=='type_d' ? '<font color="#ff0000">Type</font>':'Type')."</a></small></th>\n"
                ."              <th class='scroll_list' width='220' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=".($sortBy=='description' ? 'description_d' : 'description')."'>".($sortBy=='description'||$sortBy=='description_d'?'<font color="#ff0000">Transmission Times</font>':'Transmission Times')."</a></small></th>\n"
                ."            </tr>\n"
                ."          </table></td>\n"
                ."        </tr>\n"
                ."        <tr>\n"
                ."          <td class='rownormal' bgcolor='#ffffff'>\n"
                ."          <div class='scrollbox_230'>\n"
                ."          <table cellpadding='0' cellspacing='1' border='0' bgcolor='#ffffff'>\n"
                ."            <tr class='noscreen'>\n"
                ."              <th class='scroll_list' width='90' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=type".($sortBy=='type' ? '_d' : '')."'>".($sortBy=='type'||$sortBy=='type_d'?'<font color="#ff0000">Type</font>':'Type')."</a></small></th>\n"
                ."              <th class='scroll_list' width='220' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=description".($sortBy=='description' ? '_d' : '')."'>".($sortBy=='description'||$sortBy=='description_d'?'<font color="#ff0000">Transmission Times</font>':'Transmission Times')."</a></small></th>\n"
                ."            </tr>\n"
                ."          </table>\n"
                ."          <table cellpadding='0' cellspacing='1' border='0' bgcolor='#ffffff'>\n";
            foreach ($dgps_messages as $row) {
                $out.=
                    "            <tr>\n"
                    ."              <td class='scroll_list' width='90'><b>".$row["title"]."</b></td>"
                    ."              <td class='scroll_list' width='220'>".$row["description"]."</td>\n"
                    ."            </tr>\n";
            }
            $out.=
                "          </table>\n"
                ."          </div>\n"
                ."          </td>\n"
                ."        </tr>\n"
                ."        <tr class='noprint'>\n"
                ."          <td class='downloadTableContent' align='center'>\n"
                ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
                ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> "
                ."</td>\n"
                ."        </tr>\n"
                ."      </table></td>\n"
                ."    </tr>\n"
                ."  </table>\n";
        }
        return $out;
    }

    /**
     * @return array|string
     */
    public static function signal_info()
    {
        global $ID, $mode, $submode;
        global $active, $call, $first_heard, $GSQ, $format, $sec, $heard_in_html, $ITU, $khz, $last_heard, $LSB, $LSB_approx;
        global $notes, $pwr, $QTH, $SP, $type, $USB, $USB_approx;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $call =    trim(strToUpper(urldecode($call)));
        $ITU =    trim(strToUpper($ITU));
        $SP =        trim(strToUpper($SP));
        $lat =    0;
        $lon =    0;
        if ($GSQ) {
            $GSQ =    strtoUpper(substr($GSQ, 0, 4)).strtoLower(substr($GSQ, 4, 2));
            $a =     \Rxx\Rxx::GSQ_deg($GSQ);
            $lat =    $a["lat"];
            $lon =    $a["lon"];
        }
        $error_msg =    "";
        if ($submode=="add" or $submode=="update") {
            $error_msg = \Rxx\Rxx::check_sp_itu($SP, $ITU);
        }
        if ($error_msg=="") {
            $region = \Rxx\Rxx::get_region_for_itu($ITU);
            if ($region=="") {
                switch (system) {
                    case "REU":    $region = "eu";
                        break;
                    case "RNA":    $region = "na";
                        break;
                }
            }
            switch ($submode) {
                case "add":
                    if ($call && $khz) {
                        $sql = "INSERT INTO `signals` SET\n"
                            ."  `active` = ".addslashes(trim($active)).",\n"
                            ."  `call` = \"".addslashes(htmlentities(trim($call)))."\",\n"
                            ."  `format` = \"".addslashes(trim($format))."\",\n"
                            ."  `GSQ` = \"".addslashes(trim($GSQ))."\",\n"
                            ."  `ITU` = \"".addslashes(trim($ITU))."\",\n"
                            ."  `khz` = \"".addslashes(trim($khz))."\",\n"
                            .($GSQ ? "  `lat` = \"".$lat."\",\n  `lon` = \"".$lon."\",\n" : "")
                            .($LSB!="" ? "  `LSB` = \"".addslashes(trim($LSB))."\",\n" : "")
                            ."  `LSB_approx` = \"$LSB_approx\",\n"
                            ."  `notes` = \"".addslashes(htmlentities(trim($notes)))."\",\n"
                            ."  `pwr` = \"".addslashes(trim($pwr))."\",\n"
                            ."  `QTH` = \"".addslashes(htmlentities(trim($QTH)))."\",\n"
                            ."  `region` = \"".addslashes(trim($region))."\",\n"
                            ."  `sec` = \"".addslashes(trim($sec))."\",\n"
                            ."  `SP` = \"".addslashes(trim($SP))."\",\n"
                            ."  `type` = \"".addslashes(trim($type))."\",\n"
                            .($USB!="" ? "  `USB` = \"".addslashes(trim($USB))."\",\n" : "")
                            ."  `USB_approx` = \"$USB_approx\"\n";
                        \Rxx\Database::query($sql);
                        return    "<script language='javascript' type='text/javascript'>window.setTimeout('window.opener.location.reload(1);',1000)</script>";
                    } else {
                        $out[] =    "<p><font color='red'><b>Error -</b> you must specify at least ID and Frequency</font></p>";
                    }
                    break;
                case "update":
                    $sql =
                        "UPDATE `signals` SET\n"
                        ."  `active` = ".addslashes(trim($active)).",\n"
                        ."  `call` = \"".addslashes(htmlentities(trim($call)))."\",\n"
                        ."  `format` = \"".addslashes(trim($format))."\",\n"
                        ."  `GSQ` = \"".addslashes(trim($GSQ))."\",\n"
                        ."  `ITU` = \"".addslashes(trim($ITU))."\",\n"
                        ."  `khz` = \"".addslashes(trim($khz))."\",\n"
                        .($GSQ ? "  `lat` = \"".$lat."\",\n  `lon` = \"".$lon."\",\n" : "")
                        .($LSB==="" ? "  `LSB` = \N,\n" : "  `LSB` = \"".addslashes(trim($LSB))."\",\n")
                        ."  `LSB_approx` = \"$LSB_approx\",\n"
                        ."  `notes` = \"".addslashes(htmlentities(trim($notes)))."\",\n"
                        ."  `pwr` = \"".addslashes(trim($pwr))."\",\n"
                        ."  `QTH` = \"".addslashes(htmlentities(trim($QTH)))."\",\n"
                        ."  `region` = \"".addslashes(trim($region))."\",\n"
                        ."  `sec` = \"".addslashes(trim($sec))."\",\n"
                        ."  `SP` = \"".addslashes(trim($SP))."\",\n"
                        ."  `type` = \"".addslashes(trim($type))."\",\n"
                        .($USB==="" ? "  `USB` = \N,\n" : "  `USB` = \"".addslashes(trim($USB))."\",\n")
                        ."  `USB_approx` = \"$USB_approx\"\n"
                        ."WHERE `ID` = \"".addslashes(trim($ID))."\"";
                    \Rxx\Database::query($sql);
                    return    "<script type='text/javascript'>window.close()</script>";
                    break;
            }
        }
        $Obj =      new \Rxx\Signal($ID);
        if ($ID) {
            $row =                  $Obj->getRecord();
            $active =               $row["active"];
            $call =                 $row["call"];
            $first_heard =          $row["first_heard"];
            $format =               stripslashes($row["format"]);
            $GSQ =                  $row["GSQ"];
            $heard_in_html =        $row["heard_in_html"];
            $ITU =                  $row["ITU"];
            $khz =                  $row["khz"];
            $lat =                  $row["lat"];
            $lon =                  $row["lon"];
            $LSB =                  $row["LSB"];
            $LSB_approx =           $row["LSB_approx"];
            $last_heard =           $row["last_heard"];
            $notes =                stripslashes($row["notes"]);
            $pwr =                  $row["pwr"];
            $QTH =                  $row["QTH"];
            $sec =                  $row["sec"];
            $SP =                   $row["SP"];
            $type =                 $row["type"];
            $USB =                  $row["USB"];
            $USB_approx =           $row["USB_approx"];
            $submode =              "update";
        } else {
            $submode =              "add";
            $active =               "1";
        }
        $out =
            "<table border='0' cellpadding='0' cellspacing='0'>\n"
            ."  <tr>\n"
            ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
            ."      <tr>\n"
            ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td><h1>".(\Rxx\Rxx::isAdmin() ? ($ID=="" ? "Add Signal" : "Edit Signal")."":"Signal")."</h1></td>\n"
            ."            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
            ."              <tr>\n"
            .$Obj->tabs()
            ."              </tr>\n"
            ."            </table></td>\n"
            ."          </tr>\n"
            ."        </table>\n"
            ."        <table border='0' align='center' cellpadding='2' cellspacing='1' class='tableContainer' width='100%' height='100%'>\n"
            ."          <tr>\n"
            ."            <td bgcolor='#F5F5F5' class='itemTextCell' valign='top'>\n"
            .(\Rxx\Rxx::isAdmin() ?
                "            <form action='".system_URL."/".$mode."' name='form' method='POST'>\n"
                ."            <input type='hidden' name='ID' value='".$ID."'>\n"
                ."            <input type='hidden' name='mode' value='".$mode."'>\n"
                ."            <input type='hidden' name='submode' value='".$submode."'>\n"
                : ""
            )
            ."            <table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."              <tr>\n"
            ."                <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' alt=''></td>\n"
            ."                <td width='100%' class='downloadTableHeadings_nosort'>Profile</td>\n"
            ."                <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' alt=''></td>\n"
            ."              </tr>\n"
            ."            </table>\n"
            ."            <table width='100%' cellpadding='0' cellspacing='0' border='1' class='tableForm'>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left' width='90'>ID</th>\n"
            ."                <td colspan='3'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."                  <tr>\n"
            ."                    <td><input type='text' size='12' maxlength='12' name='call' value='".$call."' class='formfield'></td>\n"
            ."                    <td align='right'><b>KHz</b> <input type='text' size='6' maxlength='9' name='khz' value='".(float)$khz."' class='formfield'></td>\n"
            ."                    <td align='right'><b>Pwr</b> <input type='text' size='5' maxlength='7' name='pwr' value='".($pwr ? $pwr : "")."' class='formfield'>W</td>\n"
            ."                    <td align='right'><b>Type</b> ";
        if (\Rxx\Rxx::isAdmin()) {
            $out.=
                "<select name='type' class='formField'>\n"
                ."<option value='".DGPS."'".($type==DGPS ? " selected" : "")." style='background-color:".\Rxx\Signal::$colors[DGPS]."'>DGPS</option>\n"
                ."<option value='".DSC."'".($type==DSC ? " selected" : "")." style='background-color:".\Rxx\Signal::$colors[DSC]."'>DSC</option>\n"
                ."<option value='".HAMBCN."'".($type==HAMBCN ? " selected" : "")." style='background-color:".\Rxx\Signal::$colors[HAMBCN]."'>Amateur Beacon</option>\n"
                ."<option value='".NAVTEX."'".($type==NAVTEX ? " selected" : "")." style='background-color:".\Rxx\Signal::$colors[NAVTEX]."'>NAVTEX</option>\n"
                ."<option value='".NDB."'".($type==NDB ? " selected" : "").">NDB</option>\n"
                ."<option value='".TIME."'".($type==TIME ? " selected" : "")." style='background-color:".\Rxx\Signal::$colors[TIME]."'>Time</option>\n"
                ."<option value='".OTHER."'".($type==OTHER ? " selected" : "")." style='background-color:".\Rxx\Signal::$colors[OTHER]."'>Other</option>\n"
                ."</select>\n"
            ;
        } else {
            $out.=    "<input type='text' size='5' maxlength='7' class='formfield' value='";
            switch ($type) {
                case DGPS:    $out.=    "DGPS";
                    break;
                case DSC:        $out.=    "DSC";
                    break;
                case HAMBCN:    $out.=    "Amateur Beacon";
                    break;
                case NAVTEX:    $out.=    "NAVTEX";
                    break;
                case NDB:        $out.=    "NDB";
                    break;
                case TIME:    $out.=    "Time";
                    break;
                case OTHER:    $out.=    "Other";
                    break;
            }
            $out.=    "'>";
        }
        $out.=
            "&nbsp;</td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>'Name' and QTH</th>\n"
            ."                <td colspan='5'><input type=\"text\" size=\"50\" maxlength=\"50\" name=\"QTH\" value=\"".$QTH."\" class=\"formfield\"></td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>State/Province</th>\n"
            ."                <td><input type=\"text\" size=\"2\" maxlength=\"2\" name=\"SP\" value=\"".$SP."\" class=\"formfield\"><span title='State or Province'> <a href='".system_URL."/show_sp' onclick='show_sp();return false' title='NDBList State and Province codes'><b>(List)</b></a></span></td>\n"
            ."                <th align='right'>Country</th>\n"
            ."                <td><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"ITU\" value=\"".$ITU."\" class=\"formfield\">".(\Rxx\Rxx::isAdmin() ? "" : " (".\Rxx\Rxx::get_ITU($ITU).")")." <span title='Country Codes'><a href='".system_URL."/show_itu' onclick='show_itu();return false' title='NDBList Country codes'><b>(List)</b></a></span></td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>GSQ</th>\n"
            ."                <td colspan='3'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."                  <tr class='rowForm'>"
            ."                    <td><input type='text' size='6' maxlength='6' name='GSQ' value='".$GSQ."' class='formfield'> [ "
            ."<a onmouseover='window.status=\"Use coordinate converter\";return true;' onmouseout='window.status=\"\";return true;' href=\"".system_URL."/tools_coordinates_conversion?GSQ=".$GSQ."\" onclick=\"popWin('".system_URL."/tools_coordinates_conversion?GSQ=".$GSQ."','tools_coordinates_conversion','scrollbars=0,resizable=0',610,144,'centre');return false;\" title='Use coordinate converter'><b>Convert</b></a>"
            .(isset($row) ?
                " | <a href='javascript:popup_mapquestmap(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show Mapquest map\\\n(accuracy limited to nearest Grid Square)'><b>Mapquest</b></a>"
                ." | <a href='javascript:popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show Google map\\\n(accuracy limited to nearest Grid Square)'><b>Google</b></a>"
                : ""
            )
            ." ]</td>\n"
            ."                    <th>Lat</th><td><input type='text' size='10' name='lat_dddd' style='width:7em' class='formField' value='".$lat."'></td>\n"
            ."                    <th>Lon</th><td><input type='text' size='10' name='lon_dddd' style='width:7em' class='formField' value='".$lon."'></td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <td class='downloadTableHeadings_nosort' align='left' colspan='4'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."                  <tr>\n"
            ."                    <td class='downloadTableHeadings_nosort'>&nbsp;Latest Values</td>\n"
            .(\Rxx\Rxx::isAdmin() ?
                "                    <td class='downloadTableHeadings_nosort' align='right'><small>(All fields except Status and Notes are updated each time signal is logged)</small></td>\n"
                : ""
            )
            ."                  </tr>\n"
            ."                  </table></td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>LSB</th>\n"
            ."                <td>"
            .(\Rxx\Rxx::isAdmin() ?
                "~<input type='text' size='1' maxlength='1' name='LSB_approx' value='".$LSB_approx."' class='formField'> <input type='text' size='4' maxlength='5' name='LSB' value='".$LSB."' class='formField'>"
                :
                "<input type='text' size='4' maxlength='5' name='LSB' value='".$LSB_approx.$LSB."' class='formField'>"
            )
            ."</td>\n"
            ."                <th align='right'>USB</th>\n"
            ."                <td>"
            .(\Rxx\Rxx::isAdmin() ?
                "~<input type='text' size='1' maxlength='1' name='USB_approx' value='".$USB_approx."' class='formField'> <input type='text' size='4' maxlength='5' name='USB' value='".$USB."' class='formField'>"
                :
                "<input type='text' size='4' maxlength='5' name='USB' value='".$USB_approx.$USB."' class='formField'>"
            )
            ."</td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>Cycle (sec)</th>\n"
            ."                <td><input type='text' size='9' maxlength='12' name='sec' value='".$sec."' class='formfield'><a href='javascript: alert(\"INFORMATION\\n\\nWhere two values are given for Cycle Time (e.g. 8.0/28.0),\\nthe first value is for the ID portion of the cycle and the second value is\\nfor the whole thing.\")'><b>(Info)</b></a></td>\n"
            ."                <th align='right'>Format</th>\n"
            ."                <td><input type='text' size='12' maxlength='25' name='format' value='".$format."' class='formfield'></td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>First Logged</th>\n"
            ."                <td>".$first_heard."</td>\n"
            ."                <th align='left'>Last Logged</th>\n"
            ."                <td>".$last_heard."</td>\n"

            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>Status</th>\n"
            ."                <td colspan='3'>"
            .(\Rxx\Rxx::isAdmin() ?
                "<select name='active' class='formfield'>\n"
                ."  <option value='0'".($active ? "": " selected").">Out Of Service</option>\n"
                ."  <option value='1'".($active ? " selected": "").">Active</option>\n"
                ."</select>"
                :
                "<input type='text' size='8' maxlength='8' name='active' value='".($active ? "Active" : "Inactive")."' class='formField'>"
            )
            ."</td>\n"

            ."              <tr class='rowForm'>\n"
            ."                <th align='left'>Heard In</th>\n"
            ."                <td colspan='3'>".$heard_in_html."</td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <th align='left' valign='top'>Notes</th align='left'>\n"
            ."                <td colspan='3' valign='top'><textarea class='formField' name='notes' rows='3' cols='40' style='width: 450px; height: ".(\Rxx\Rxx::isAdmin() ? "70px;" : "80px;")."'>".$notes."</textarea></td>\n"
            ."              <tr class='rowForm'>\n"
            ."                <td colspan='4' align='center' style='height: 30px;'>\n"
            ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
            ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> "
            .(\Rxx\Rxx::isAdmin() ?
                "<input type='submit' value='".($submode=="update" ? "Save" : "Add")."' class='formbutton' style='width: 60px;'>"
                : ""
            )
            ."</td>\n"
            ."              </tr>\n"
            ."            </table>\n"
            .(\Rxx\Rxx::isAdmin() ?
                "            </form>\n"
                : ""
            )
            ."            </td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>\n"
            ."</table>\n"
            .($error_msg!="" ?
                "<script type='text/javascript'>window.setTimeout(\"alert('ERROR\\\\n\\\\n".$error_msg."')\",1000);</script>\n"
                : "")
        ;
        return $out;
    }

    /**
     * @return string
     */
    public static function signal_listeners()
    {
        global $ID, $mode, $submode, $sortBy, $targetID;
        global $call, $ITU, $khz, $QTH, $SP;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $call =     strToUpper(urldecode($call));
        $ITU =      strToUpper($ITU);
        $SP =       strToUpper($SP);
        if (\Rxx\Rxx::isAdmin()) {
            switch ($submode) {
                case "delete":
                    $log = new \Rxx\Log($targetID);
                    $log->delete();
                    break;
            }
        }
        $Obj =      new \Rxx\Signal($ID);
        if ($ID) {
            $row =          $Obj->getRecord();
            $call =         $row["call"];
            $ITU =          $row["ITU"];
            $khz =          $row["khz"];
            $QTH =          $row["QTH"];
            $SP =           $row["SP"];
        }

        if ($sortBy=="") {
            $sortBy = "name";
        }
        $sortBy_SQL =        "";
        switch ($sortBy) {
            case "dx":
                $sortBy_SQL =   "`dx_km`='' OR `dx_km` IS NULL,`dx_km` ASC";
                break;
            case "dx_d":
                $sortBy_SQL =   "`dx_km`='' OR `dx_km` IS NULL,`dx_km` DESC";
                break;
            case "ITU":
                $sortBy_SQL =   "`ITU` ASC, `SP` ASC, `QTH` ASC";
                break;
            case "ITU_d":
                $sortBy_SQL =   "`ITU` DESC, `SP` ASC, `QTH` ASC";
                break;
            case "logs":
                $sortBy_SQL =   "`logs` ASC";
                break;
            case "logs_d":
                $sortBy_SQL =   "`logs` DESC";
                break;
            case "name":
                $sortBy_SQL =   "`listeners`.`name` ASC";
                break;
            case "name_d":
                $sortBy_SQL =   "`listeners`.`name` DESC";
                break;
            case "SP":
                $sortBy_SQL =   "`SP` ASC, `QTH` ASC";
                break;
            case "SP_d":
                $sortBy_SQL =   "`SP` DESC, `QTH` ASC";
                break;
            case "QTH":
                $sortBy_SQL =   "`QTH` ASC";
                break;
            case "QTH_d":
                $sortBy_SQL =   "`QTH` DESC";
                break;
        }
        $sql =
            "SELECT\n"
            ."  `logs`.`listenerID`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`,\n"
            ."  `listeners`.`name`,\n"
            ."  `listeners`.`ITU`,\n"
            ."  `listeners`.`QTH`,\n"
            ."  `listeners`.`SP`,\n"
            ."  count(*) AS `logs`\n"
            ."FROM\n"
            ."  `logs`,\n"
            ."  `listeners`\n"
            ."WHERE\n"
            ."  `logs`.`listenerID` = `listeners`.`ID` AND\n"
            ."  `signalID` = ".addslashes($ID)."\n"
            ."GROUP BY\n"
            ."  `logs`.`listenerID`\n"
            ."ORDER BY\n"
            ."  ".$sortBy_SQL."\n";

        $result =    \Rxx\Database::query($sql);
        $out=
            "<table border='0' cellpadding='0' cellspacing='0'>\n"
            ."  <tr>\n"
            ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
            ."      <tr>\n"
            ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td><h2>Signal Listeners</h2></td>\n"
            ."            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
            ."              <tr>\n"
            .$Obj->tabs()
            ."              </tr>\n"
            ."            </table></td>\n"
            ."          </tr>\n"
            ."        </table>\n"
            ."        <table border='0' align='center' cellpadding='2' cellspacing='1' class='tableContainer' width='100%' height='100%'>\n"
            ."          <tr>\n"
            ."            <td bgcolor='#F5F5F5' class='itemTextCell' valign='top'>\n";
        if (\Rxx\Rxx::isAdmin()) {
            $out.=
                "            <form action='".system_URL.'/'.$mode."' name='form' method='POST' onsubmit='if (window.opener) { window.opener.location.reload(1)};return true;'>\n"
                ."            <input type='hidden' name='ID' value='$ID'>\n"
                ."            <input type='hidden' name='mode' value='$mode'>\n"
                ."            <input type='hidden' name='submode' value=''>\n";
        }
        if (\Rxx\Database::numRows($result)) {
            $out.=
                "            <table width='100%'  border='0' cellpadding='2' cellspacing='1' class='downloadTable'>\n"
                ."              <tr>\n"
                ."                <th class='downloadTableHeadings_nosort' align='left'>&nbsp;Logs for ".(float)$khz."-".$call.($QTH ? ", $QTH" : "").($SP ? ", $SP" : "").($ITU ? ", $ITU" : "")."</th>\n"
                ."              </tr>\n"
                ."              <tr>\n"
                ."                <td bgcolor='white'><table cellpadding='0' cellspacing='1' border='0' class='noprint'>\n"
                ."                  <thead>\n"
                ."                  <tr>\n"
                ."                    <th class='scroll_list' width='140' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=name".($sortBy=='name' ? '_d' : '')."'>".($sortBy=='name'||$sortBy=='name_d'?'<font color="#ff0000">Name</font>':'Name')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='140' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=QTH".($sortBy=='QTH' ? '_d' : '')."'>".($sortBy=='QTH'||$sortBy=='QTH_in_d'?'<font color="#ff0000">QTH</font>':'QTH')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='40' align='right'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=SP".($sortBy=='SP' ? '_d' : '')."'>".($sortBy=='SP'||$sortBy=='SP_d'?'<font color="#ff0000">SP</font>':'SP')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='40' align='right'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=ITU".($sortBy=='ITU' ? '_d' : '')."'>".($sortBy=='ITU'||$sortBy=='ITU_d'?'<font color="#ff0000">ITU</font>':'ITU')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='40' align='right'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=".($sortBy=='logs_d' ? 'logs' : 'logs_d')."'>".($sortBy=='logs'||$sortBy=='logs_d'?'<font color="#ff0000">Logs</font>':'Logs')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='40' align='right'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=".($sortBy=='dx_d' ? 'dx' : 'dx_d')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">KM</font>':'KM')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='40' align='right'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=".($sortBy=='dx_d' ? 'dx' : 'dx_d')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">Miles</font>':'Miles')."</a></small></th>\n"
                ."                  </tr>\n"
                ."                  </thead>\n"
                ."                </table></td>\n"
                ."              </tr>\n"
                ."              <tr>\n"
                ."                <td class='rownormal' bgcolor='#ffffff'>\n"
                ."                <div class='scrollbox_230'>\n"
                ."                <table cellpadding='0' cellspacing='1' border='0' bgcolor='#ffffff'>\n"
                ."                  <thead>\n"
                ."                  <tr class='noscreen'>\n"
                ."                    <th class='scroll_list'><small>Name</small></th>\n"
                ."                    <th class='scroll_list'><small>QTH</small></th>\n"
                ."                    <th class='scroll_list'><small>SP</small></th>\n"
                ."                    <th class='scroll_list'><small>ITU</small></th>\n"
                ."                    <th class='scroll_list'><small>Logs</small></th>\n"
                ."                    <th class='scroll_list'><small>KM</small></th>\n"
                ."                    <th class='scroll_list'><small>Miles</small></th>\n"
                ."                  </tr>\n"
                ."                  </thead>\n";
            for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                $row =    \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
                $out.=
                    "                  <tr>\n"
                    ."                    <td class='scroll_list' width='140'><a href=\"javascript:listener_signals('".$row["listenerID"]."')\"><b>".$row["name"]."</b></a></td>"
                    ."                    <td class='scroll_list' width='140'>".($row["QTH"]   ? $row["QTH"]   : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='40' align='right'>".($row["SP"]   ? $row["SP"]   : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='40' align='right'>".($row["ITU"]   ? $row["ITU"]   : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='40' align='right'>".($row["logs"]   ? $row["logs"]   : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='40' align='right'>".($row["dx_km"]   ? $row["dx_km"]   : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='40' align='right'>".($row["dx_miles"]   ? $row["dx_miles"]   : "&nbsp;")."</td>\n"
                    ."                  </tr>\n";
            }
            $out.=
                "                  </div></table></td>\n"
                ."              </tr>\n"
                ."              <tr class='noprint'>\n"
                ."                <td class='downloadTableContent' align='center'>\n"
                ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
                ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> "
                ."</td>\n"
                ."              </tr>\n"
                ."            </table></td>\n"
                ."          </tr>\n"
                ."        </table></form></td>\n"
                ."      </tr>\n"
                ."    </table></td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."</table>\n";
        }
        $out.=    "</form>\n";
        return $out;
    }

    /**
     * @return string
     */
    public static function signal_map_eu()
    {
        global $ID,$mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $out =    array();
        $sql =
            "SELECT DISTINCT\n"
            ."  MAX(`daytime`) AS `daytime`,\n"
            ."  `listeners`.`ID` as `listenerID`,\n"
            ."  `logs`.`heard_in`,\n"
            ."  `map_x`,\n"
            ."  `map_y`,\n"
            ."  `name`,\n"
            ."  `logs`.`dx_miles`,\n"
            ."  `primary_QTH`\n"
            ."FROM\n"
            ."  `listeners`\n"
            ."LEFT JOIN\n"
            ."  `logs`\n"
            ."ON\n"
            ."  `listeners`.`ID` = `logs`.`listenerID` AND\n"
            ."  `logs`.`signalID` = $ID\n"
            ."WHERE\n"
            ."  `listeners`.`map_x` IS NOT NULL AND\n"
            ."  `listeners`.`region` = 'eu'\n"
            ."GROUP BY\n"
            ."  `listeners`.`ID`\n"
            ."ORDER BY\n"
            ."  `heard_in`,`name`";
        $result =    \Rxx\Database::query($sql);
        $name_idx =    0;
        $out[] =
            "<img usemap=\"#map\" galleryimg=\"no\""
            ." src=\"".system_URL."/generate_station_map/2/".$ID."\"\n"
            ." border=\"0\" alt=\"\" />\n"
            ."<map name=\"map\">\n";


        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =    \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            if ($row['map_x']) {
                if ($row['heard_in']!='') {
                    $out[] =
                        "<area shape=\"circle\""
                        ." title=\"".$row['name']." (Yes".($row['daytime'] ? " - daytime" : "").")\""
                        ." onmouseover=\"show_point(702,24+$name_idx*8);return true;\""
                        ." onmouseout=\"show_point(0,0);return true;\""
                        ." href=\"javascript:listener_edit(".$row['listenerID'].")\""
                        ." coords=\"".$row['map_x'].",".$row['map_y'].",".($row['primary_QTH'] ? "4" : "2")."\""
                        ." alt=\"\" />\n";
                    $out[] =
                        "<area shape=\"rect\""
                        ." title=\"".$row['name']." (Yes".($row['daytime'] ? " - daytime" : "").")\""
                        ." onmouseover=\"show_point(".$row['map_x'].",".$row['map_y'].");return true;\""
                        ." onmouseout=\"show_point(0,0);return true;\""
                        ." href=\"javascript:listener_edit(".$row['listenerID'].")\""
                        ." coords=\"702,".(22+$name_idx*8).",857,".(31+$name_idx*8)."\""
                        ." alt=\"\" />\n";
                    $name_idx++;
                } else {
                    $out[] =
                        "<area shape=\"circle\""
                        ." title=\"".$row['name']." (No)\""
                        ." href=\"javascript:listener_edit(".$row['listenerID'].")\""
                        ." coords=\"".$row['map_x'].",".$row['map_y'].",".($row['primary_QTH'] ? "4" : "2")."\""
                        ." alt=\"\" />\n";
                }
            }
        }
        $out[] =
            "</map>\n"
            ."<div ID='point_here' style='position: absolute; display: none;'><img alt=\"\" src='".BASE_PATH."assets/map_point_here.gif' /></div>\n"
            ."<div ID='name_here' style='position: absolute; display: none;'><img alt=\"\" src='".BASE_PATH."assets/map_name_here.gif' /></div>\n";
        return implode($out, "");
    }

    /**
     * @return string
     */
    public static function signal_map_na()
    {
        global $ID,$mode;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $out =    array();
        $out[] =    "<img usemap=\"#map\" galleryimg=\"no\" src=\"".system_URL."/generate_station_map/1/".$ID."\" border=\"0\">\n";
        $out[] =    "<map name=\"map\">\n";
        $sql =     "SELECT\n"
            ."  COUNT(DISTINCT(`listeners`.`ID`)) AS `listeners`\n"
            ."FROM\n"
            ."  `listeners`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `listeners`.`ID` = `logs`.`listenerID` AND\n"
            ."  `listeners`.`map_x` IS NOT NULL AND\n"
            ."  `logs`.`signalID` = \"$ID\" AND\n"
            ."  (`listeners`.`region`='na' OR `listeners`.`region`='ca' OR `listeners`.`itu` = 'HWA')";
//print("<pre>$sql</pre>");

        $result =    \Rxx\Database::query($sql);
        $row =    \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
        $total_rx =    $row['listeners'];


        $sql =    "SELECT DISTINCT\n"
            ."  MAX(`daytime`) AS `daytime`,\n"
            ."  `listeners`.`ID` as `listenerID`,\n"
            ."  `logs`.`heard_in`,\n"
            ."  `map_x`,\n"
            ."  `map_y`,\n"
            ."  `name`,\n"
            ."  `logs`.`dx_miles`,\n"
            ."  `primary_QTH`\n"
            ."FROM\n"
            ."  `listeners`\n"
            ."LEFT JOIN\n"
            ."  `logs`\n"
            ."ON\n"
            ."  `listeners`.`ID` = `logs`.`listenerID` AND\n"
            ."  `logs`.`signalID` = $ID\n"
            ."WHERE\n"
            ."  `listeners`.`map_x` IS NOT NULL AND\n"
            ."  (`listeners`.`region`='na' OR `listeners`.`region`='ca' OR `listeners`.`itu` = 'HWA')"
            ."GROUP BY\n"
            ."  `listeners`.`ID`\n"
            ."ORDER BY\n"
            ."  `heard_in`,`name`";

        $result =    @\Rxx\Database::query($sql);
        $name_idx =    0;
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =    \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            if ($row['map_x']) {
                if ($row['heard_in']!='') {
                    $out[] = "<area shape=\"circle\""
                        ." title=\"".$row['name']." (Yes".($row['daytime'] ? " - daytime" : "").")\""
                        ." onmouseover=\"show_point(10,(602-$total_rx*8)+$name_idx*8);return true;\""
                        ." onmouseout=\"show_point(0,0);return true;\""
                        ." href=\"javascript:listener_edit(".$row['listenerID'].")\""
                        ."coords=\"".$row['map_x'].",".$row['map_y'].",".($row['primary_QTH'] ? "4" : "2")."\">\n";

                    $out[] = "<area shape=\"rect\""
                        ." title=\"".$row['name']." (Yes".($row['daytime'] ? " - daytime" : "").")\""
                        ." onmouseover=\"show_point(".$row['map_x'].",".$row['map_y'].");return true;\""
                        ." onmouseout=\"show_point(0,0);return true;\""
                        ." href=\"javascript:listener_edit(".$row['listenerID'].")\""
                        ." coords=\"5,".(597-$total_rx*8+$name_idx*8).",160,".(607-$total_rx*8+$name_idx*8)."\">\n";

                    $name_idx++;
                } else {
                    $out[] = "<area shape=\"circle\""
                        ." title=\"".$row['name']." (No)\""
                        ." href=\"javascript:listener_edit(".$row['listenerID'].")\""
                        ."coords=\"".$row['map_x'].",".$row['map_y'].",".($row['primary_QTH'] ? "4" : "2")."\">\n";
                }
            }
        }
        $out[] =     "</map>\n"
            ."<script language='javascript' type='text/javascript'>\n"
            ."document.write(\"<div ID='point_here' style='position: absolute; display: none;'><img src='".BASE_PATH."assets/map_point_here.gif'></div>\");\n"
            ."document.write(\"<div ID='name_here' style='position: absolute; display: none;'><img src='".BASE_PATH."assets/map_name_here.gif'></div>\");\n"
            ."</script>\n";

        return implode($out, "");
    }

    /**
     * @return string
     */
    public static function signal_merge()
    {
        global $mode, $submode;
        global $ID, $destinationID;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $merged =   0;

        $sql =      "SELECT * FROM `signals` WHERE `ID` = '".addslashes($ID)."'";
        $result =   \Rxx\Database::query($sql);
        $row =      \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
        $source =   (float)$row['khz'] . "-" . $row['call'];

        switch($submode) {
            case "merge":
                if ($ID === $destinationID) {
                    print "<p>Error - source is same as destination.</p>";
                    $submode = "";
                } else {
                    $sql =      "UPDATE `logs` SET `signalID` = ".addslashes($destinationID)." WHERE `signalID` = ".addslashes($ID);
                    $result =   \Rxx\Database::query($sql);
                    $merged =   \Rxx\Database::affectedRows();


                    $signal = new \Rxx\Signal($ID);
                    $signal->delete();

                    $signal = new \Rxx\Signal;
                    $signal->updateFromLogs($destinationID, false);
                    break;
                }
        }

        $options = false;
        if ($submode != "merge") {
            $sql = "SELECT `ID`,`khz`,`call`,`SP`,`ITU` FROM `signals` ORDER BY `khz`,`call`,`ITU`,`SP`";
            $rows = @\Rxx\Database::query($sql);
            foreach ($rows as $row) {
                $options[] =
                    "<option value='" . $row['ID'] . "'" . ($ID == $row['ID'] ? " selected" : "") . ">"
                    . \Rxx\Rxx::pad_nbsp((float)$row['khz'], 10)
                    . \Rxx\Rxx::pad_nbsp($row['call'], 12) . " "
                    . \Rxx\Rxx::pad_nbsp($row['SP'], 3)
                    . $row['ITU']
                    . "</option>";
            }
        } else {
            $sql =      "SELECT * FROM `signals` WHERE `ID` = '".addslashes($destinationID)."'";
            $result =   \Rxx\Database::query($sql);
            $row =      \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $destination =   (float)$row['khz'] . "-" . $row['call'];
        }
        $out =
            "<div style='padding: 1em'>"
            . "<h1>Signal Merge</h1><br>\n"
            . ($submode !== 'merge' ?
                 "<p>This function moves <b>all</b> logs for the selected signal to another signal record.</p>"
                ." <p>The Source signal will be deleted and all its logs will be moved to the new signal.</p>"
                ." <p>This operation is <strong>NOT</strong> reversible.</p>"
                . "<form action='".system_URL."/".$mode."/".$ID."?submode=merge' method='POST'>\n"
                . "<table border='1' cellspacing='0' cellpadding='5'>\n"
                . "  <thead>\n"
                . "    <tr>\n"
                . "      <th>Source Signal</th>\n"
                . "      <th>Destination Signal</th>\n"
                . "    </tr>\n"
                . "  </thead>\n"
                . "  <tbody>\n"
                . "    <tr>\n"
                . "      <td valign='top'>$source</td>\n"
                . "      <td valign='top'>"
                . "<select name='destinationID' id='sourceID' size='5' style='font-family: monospace;' class='formField'>\n"
                . implode("\n", $options) ."\n"
                . "</select></td>\n"
                . "    </tr>\n"
                . "  </tbody>\n"
                . "</table>\n"
                . "<p class='txt_c'>\n"
                . "<input type='submit' value='Merge Signals' class='formButton'"
                . " onclick=\"return confirm('Merge these two signals and PERMANENTLY delete $source from the database?')\">\n"
                . "</p>"
                . "</form>\n"
            :
                "<p>$merged log"  .($merged === 1 ? '' : 's') . " were moved from $source to $destination, and signal $source was deleted.</p>"
                . "<p align='center'><input type='button' value='Close' onClick='window.close();'></p>"
            );
        $out .=    "</div>\n";
        return $out;
    }

    /**
     * @return string
     */
    public static function signal_QNH()
    {
        global $ID, $mode, $submode, $ICAO_signal, $hours_signal;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $pressure =    array();

        $Obj =      new \Rxx\Signal($ID);
        $row =        $Obj->getRecord();
        $call =    $row["call"];
        $GSQ =    $row["GSQ"];
        $ITU =    $row["ITU"];
        $khz =    (float)$row["khz"];
        $lat =    $row["lat"];
        $logs =    $row["logs"];
        $lon =    $row["lon"];
        $QTH =    $row["QTH"];
        $SP =        $row["SP"];


        if ($ICAO_signal && $hours_signal) {
            if ($METAR = \Rxx\Tools\Weather::METAR($ICAO_signal, $hours_signal, 0)) {
                $sql =      "SELECT * FROM `icao` WHERE `icao` = \"$ICAO_signal\"";
                $result =   \Rxx\Database::query($sql);
                $row =      \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
                $dx =       \Rxx\Rxx::get_dx($lat, $lon, $row["lat"], $row["lon"]);
                $pressure =
                    "QNH at $ICAO_signal - ".$row["name"].($row["SP"] ? ", ".$row["SP"] : "").", ".$row["CNT"]."\n"
                    ."(".$dx[0]." miles from $khz-".$call.")\n"
                    ."----------------------\n"
                    ."DD UTC  MB     SLP \n"
                    ."----------------------\n"
                    .$METAR."\n"
                    ."----------------------\n"
                    ."(From ".system.")\n";
            } else {
                $pressure =    "QNH data not available at $ICAO_signal for last $hours_signal hours";
            }
        } else {
            $pressure = "HELP\nEnter hours to display, select first valid station in list and press 'QNH'";
        }
        $options = "";
        $icao_arr =  \Rxx\Rxx::get_local_icao($GSQ, 10, $ICAO_signal);
        for ($i=0; $i<10; $i++) {
            $options.=    "              <option value='".$icao_arr[$i]['ICAO']."'".($ICAO_signal==$icao_arr[$i]['ICAO'] ? " selected" : "").">".$icao_arr[$i]['ICAO']." (".$icao_arr[$i]['miles']." Miles, ".$icao_arr[$i]['km']." KM)</option>\n";
        }
        $out =
            "<table border='0' cellpadding='0' cellspacing='0' width='100%'>\n"
            ."  <tr>\n"
            ."    <td><h1>Signal QNH</h1></td>\n"
            ."    <td align='right' valign='bottom'>\n"
            ."    <table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
            ."      <tr>\n"
            .$Obj->tabs()
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>\n"
            ."</table>\n"
            ."<table border='0' align='center' cellpadding='2' cellspacing='1' class='tableContainer' width='100%'>\n"
            ."  <tr>\n"
            ."    <td bgcolor='#F5F5F5' class='itemTextCell' valign='top' height='320'>\n"
            ."    <table cellpadding='0' border='0' cellspacing='1' class='downloadtable' width='100%' height='100%'>\n"
            ."      <tr>\n"
            ."        <th class='downloadTableHeadings_nosort' align='left'>&nbsp;QNH History for ".(float)$khz."-".$call.($QTH ? ", $QTH" : "").($SP ? ", $SP" : "").($ITU ? ", $ITU" : "")."</th>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td class='downloadTableContent' width='100%'>\n"
            ."          <form name='pressure' action='".system_URL."/".$mode."' method='GET'>\n"
            ."            <input type='hidden' name='mode' value='$mode'>\n"
            ."            <input type='hidden' name='submode' value=''>\n"
            ."            <input type='hidden' name='ID' value='$ID'>\n"
            ."            <select class='formFixed' name='ICAO_signal'>\n"
            .$options
            ."            </select>\n"
            ."            <input type='text' size='2' maxlength='2' name='hours_signal' value='".($hours_signal ? $hours_signal : "12")."' class='formField'> Hours\n"
            ."            <input type='submit' value='QNH' class='formButton'>\n"
            ."            <input type='button' value='METAR' class='formButton' onclick='popWin(\"http://www.aviationweather.gov/adds/metars/?station_ids=\"+document.pressure.ICAO_signal.value+\"&amp;std_trans=standard&amp;chk_metars=on&amp;hoursStr=past+\"+document.pressure.hours_signal.value+\"+hours\",\"popMETAR\",\"scrollbars=1,resizable=1,location=1\",640,380,\"\");'>\n"
            ."            <input type='button' value='Decoded' class='formButton' onclick='popWin(\"http://www.aviationweather.gov/adds/metars/?station_ids=\"+document.pressure.ICAO_signal.value+\"&amp;std_trans=translated&amp;chk_metars=on&amp;hoursStr=past+\"+document.pressure.hours_signal.value+\"+hours\",\"popDecoded\",\"scrollbars=1,resizable=1,location=1\",640,380,\"\");'>\n"
            ."          </form>\n"
            ."        </td>\n"
            ."      </tr>\n"
            ."      <tr height='100%'>\n"
            ."        <td class='downloadTableContent' width='100%'><textarea rows='10' cols='60' style='width: 580px; height: 95%;' class='formFixed'>".$pressure."</textarea></td>\n"
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>\n"
            ."</table>\n";
        return $out;
    }

    /**
     * @return string
     */
    public static function signal_log()
    {
        global $ID, $mode, $submode, $sortBy, $targetID;
        global $active, $call, $GSQ, $heard_in, $ITU, $khz, $LSB, $notes, $SP, $USB;
        if (!$ID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $ID = array_pop($path_arr);
            }
        }
        $call =     strToUpper(urldecode($call));
        $ITU =  strToUpper($ITU);
        $SP =       strToUpper($SP);
        if (\Rxx\Rxx::isAdmin()) {
            switch ($submode) {
                case "delete":
                    $log = new \Rxx\Log($targetID);
                    $log->delete();
                    break;
            }
        }
        $Obj =      new \Rxx\Signal($ID);
        if ($ID) {
            $row =      $Obj->getRecord();
            $active =   $row["active"];
            $call =         $row["call"];
            $GSQ =      $row["GSQ"];
            $heard_in =     $row["heard_in"];
            $ITU =      $row["ITU"];
            $khz =      $row["khz"];
            $logs =         $row["logs"];
            $LSB =      $row["LSB"];
            $notes =    stripslashes($row["notes"]);
            $QTH =      $row["QTH"];
            $SP =       $row["SP"];
            $USB =      $row["USB"];
        }
        if ($sortBy=="") {
            $sortBy = "date";
        }
        $sortBy_SQL =       "";
        switch ($sortBy) {
            case "date":        $sortBy_SQL =   "`date` DESC, `time` DESC";
                break;
            case "date_d":      $sortBy_SQL =   "`date` ASC, `time` ASC";
                break;
            case "dx":          $sortBy_SQL =   "`dx_km`='' OR `dx_km` IS NULL,`dx_km` ASC";
                break;
            case "dx_d":        $sortBy_SQL =   "`dx_km`='' OR `dx_km` IS NULL,`dx_km` DESC";
                break;
            case "format":      $sortBy_SQL =   "`format`='' OR `format` IS NULL,`format` ASC";
                break;
            case "format_d":    $sortBy_SQL =   "`format`='' OR `format` IS NULL,`format` DESC";
                break;
            case "gsq":         $sortBy_SQL =   "`GSQ`='' OR `GSQ` IS NULL,`GSQ` ASC";
                break;
            case "gsq_d":       $sortBy_SQL =   "`GSQ`='' OR `GSQ` IS NULL,`GSQ` DESC";
                break;
            case "ID":          $sortBy_SQL =   "`logs`.`ID` ASC";
                break;
            case "ID_d":        $sortBy_SQL =   "`logs`.`ID` DESC";
                break;
            case "itu":         $sortBy_SQL =   "`ITU` ASC, `signals`.`SP` ASC, `khz` ASC, `call` ASC";
                break;
            case "itu_d":       $sortBy_SQL =   "`ITU` DESC, `signals`.`SP` ASC, `khz` ASC, `call` ASC";
                break;
            case "khz":         $sortBy_SQL =   "`khz` ASC, `call` ASC";
                break;
            case "khz_d":       $sortBy_SQL =   "`khz` DESC, `call` ASC";
                break;
            case "heard_in":    $sortBy_SQL =   "`logs`.`heard_in` ASC";
                break;
            case "heard_in_d":  $sortBy_SQL =   "`logs`.`heard_in` DESC";
                break;
            case "sec":         $sortBy_SQL =   "`sec`='' OR `sec` IS NULL,`sec` ASC";
                break;
            case "sec_d":       $sortBy_SQL =   "`sec`='' OR `sec` IS NULL,`sec` DESC";
                break;
            case "time":        $sortBy_SQL =   "`time` IS NULL,`time` ASC";
                break;
            case "time_d":      $sortBy_SQL =   "`time` IS NULL,`time` DESC";
                break;
            case "listener":    $sortBy_SQL =   "`listeners`.`name` ASC";
                break;
            case "listener_d":  $sortBy_SQL =   "`listeners`.`name` DESC";
                break;
            case "LSB":         $sortBy_SQL =   "`logs`.`LSB` IS NULL, `logs`.`LSB` ASC";
                break;
            case "LSB_d":       $sortBy_SQL =   "`logs`.`LSB` IS NULL, `logs`.`LSB` DESC";
                break;
            case "USB":         $sortBy_SQL =   "`logs`.`USB` IS NULL, `logs`.`USB` ASC";
                break;
            case "USB_d":       $sortBy_SQL =   "`logs`.`USB` IS NULL, `logs`.`USB` DESC";
                break;
        }
        $sql =
            "SELECT\n"
            ."  `logs`.*,\n"
            ."  `listeners`.`ID` AS `listenerID`,\n"
            ."  `listeners`.`name`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."LEFT JOIN\n"
            ."  `listeners`\n"
            ."ON\n"
            ."  `logs`.`listenerID` = `listeners`.`ID`\n"
            ."WHERE\n"
            ."  `signalID` = \"".addslashes("$ID")."\" AND\n"
            ."  `listenerID` is not NULL\n"
            ."GROUP BY\n"
            ."  `logs`.`ID`\n"
            ."ORDER BY\n"
            ."  $sortBy_SQL";
        $result =   \Rxx\Database::query($sql);
//  print("<pre>$sql</pre>");

        $out=
            "<table border='0' cellpadding='0' cellspacing='0'>\n"
            ."  <tr>\n"
            ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
            ."      <tr>\n"
            ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td><h1>Signal Logs</h1></td>\n"
            ."            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
            ."              <tr>\n"
            .$Obj->tabs()
            ."              </tr>\n"
            ."            </table></td>\n"
            ."          </tr>\n"
            ."        </table>\n"
            ."        <table border='0' align='center' cellpadding='2' cellspacing='1' class='tableContainer' width='100%' height='100%'>\n"
            ."          <tr>\n"
            ."            <td bgcolor='#F5F5F5' class='itemTextCell' valign='top'>\n";
        if (\Rxx\Rxx::isAdmin()) {
            $out.=
                "            <form action='".system_URL."/".$mode."' name='form' method='POST' onsubmit='if (window.opener) { window.opener.location.reload(1)};return true;'>\n"
                ."            <input type='hidden' name='ID' value='$ID'>\n"
                ."            <input type='hidden' name='mode' value='$mode'>\n"
                ."            <input type='hidden' name='submode' value=''>\n";
        }

        if (\Rxx\Database::numRows($result)) {
            $out.=
                "            <table width='100%'  border='0' cellpadding='2' cellspacing='1' class='downloadTable'>\n"
                ."              <tr>\n"
                ."                <th class='downloadTableHeadings_nosort' align='left'><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td  class='downloadTableHeadings_nosort'>&nbsp;Logs for ".(float)$khz."-".$call.($QTH ? ", $QTH" : "").($SP ? ", $SP" : "").($ITU ? ", $ITU" : "")."</td><td class='downloadTableHeadings_nosort' align='right'><span style='font-weight: normal'>(daytime logs in <b>bold</b>)</span></td></tr></table></th>\n"
                ."              </tr>\n"
                ."              <tr>\n"
                ."                <td bgcolor='white'><table cellpadding='0' cellspacing='1' border='0' class='noprint'>\n"
                ."                  <thead>\n"
                ."                  <tr>\n"
                ."                    <th class='scroll_list' width='75' title='YYYY-MM-DD' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=date".($sortBy=='date' ? '_d' : '')."'>".($sortBy=='date'||$sortBy=='date_d'?'<font color="#ff0000">Date</font>':'Date')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='35' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=time".($sortBy=='time' ? '_d' : '')."'>".($sortBy=='time'||$sortBy=='time_d'?'<font color="#ff0000">UTC</font>':'UTC')."</a></small></th>\n";
            if (system!="RWW") {
                $out.=
                    "                    <th class='scroll_list' width='40' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=LSB".($sortBy=='LSB' ? '_d' : '')."'>".($sortBy=='LSB'||$sortBy=='LSB_d'?'<font color="#ff0000">LSB</font>':'LSB')."</a></small></th>\n"
                    ."                    <th class='scroll_list' width='40' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=USB".($sortBy=='USB' ? '_d' : '')."'>".($sortBy=='USB'||$sortBy=='USB_d'?'<font color="#ff0000">USB</font>':'USB')."</a></small></th>\n"
                    ."                    <th class='scroll_list' width='40' align='left'><a href='".system_URL."/".$mode."/".$ID."?sortBy=sec".($sortBy=='sec' ? '_d' : '')."'>".($sortBy=='sec'||$sortBy=='sec_d'?'<font color="#ff0000">Sec</font>':'Sec')."</a></small></th>\n"
                    ."                    <th class='scroll_list' width='65' align='left'><a href='".system_URL."/".$mode."/".$ID."?sortBy=format".($sortBy=='format' ? '_d' : '')."'>".($sortBy=='format'||$sortBy=='format_d'?'<font color="#ff0000">Format</font>':'Format')."</a></small></th>\n";
            }
            $out.=
                "                    <th class='scroll_list' width='140' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=listener".($sortBy=='listener' ? '_d' : '')."'>".($sortBy=='listener'||$sortBy=='listener_d'?'<font color="#ff0000">Listener</font>':'Listener')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='35' align='left'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=heard_in".($sortBy=='heard_in' ? '_d' : '')."'>".($sortBy=='heard_in'||$sortBy=='heard_in_d'?'<font color="#ff0000">In</font>':'In')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='40' align='right'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=dx".($sortBy=='dx' ? '_d' : '')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">KM</font>':'KM')."</a></small></th>\n"
                ."                    <th class='scroll_list' width='40' align='right'><small><a href='".system_URL."/".$mode."/".$ID."?sortBy=dx".($sortBy=='dx' ? '_d' : '')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">Miles</font>':'Miles')."</a></small></th>\n"
                .(\Rxx\Rxx::isAdmin() ?
                    "                    <th class='scroll_list' width='35' align='left'><small>Del</small></th>\n"
                    :
                    ""
                )
                ."                  </tr>\n"
                ."                  </thead>\n"
                ."                </table></td>\n"
                ."              </tr>\n"
                ."              <tr>\n"
                ."                <td class='rownormal' bgcolor='#ffffff'>\n"
                ."                <div class='scrollbox_230'>\n"
                ."                <table cellpadding='0' cellspacing='1' border='0' bgcolor='#ffffff'>\n"
                ."                  <thead>\n"
                ."                  <tr class='noscreen'>\n"
                ."                    <th class='scroll_list'><small>Date</small></th>\n"
                ."                    <th class='scroll_list'><small>UTC</small></th>\n";
            if (system!="RWW") {
                $out.=
                    "                    <th class='scroll_list'><small>LSB</small></th>\n"
                    ."                    <th class='scroll_list'><small>USB</small></th>\n"
                    ."                    <th class='scroll_list'><small>Sec</small></th>\n"
                    ."                    <th class='scroll_list'><small>Format</small></th>\n";
            }
            $out.=
                "                    <th class='scroll_list'><small>Listener</small></th>\n"
                ."                    <th class='scroll_list'><small>In</small></th>\n"
                ."                    <th class='scroll_list'><small>KM</small></th>\n"
                ."                    <th class='scroll_list'><small>Mi</small></th>\n"
                .(\Rxx\Rxx::isAdmin() ?
                    "                    <th class='scroll_list'><small>Del</small></th>\n"
                    :
                    ""
                )
                ."                  </tr>\n"
                ."                  </thead>\n";
            for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                $row =  \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
                $out.=
                    "                  <tr>\n"
                    ."                    <td class='scroll_list' width='75'>".($row["date"] ? $row["date"] : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='35'>".($row["time"] ? ($row['daytime'] ? "<b>".$row["time"]."</b>" : $row["time"]) : "&nbsp;")."</td>\n";
                if (system!="RWW") {
                    $out.=
                        "                    <td class='scroll_list' width='40'>".$row["LSB_approx"].($row["LSB"]<>""  ? $row["LSB"]  : "&nbsp;")."</td>\n"
                        ."                    <td class='scroll_list' width='40'>".$row["USB_approx"].($row["USB"]<>""  ? $row["USB"]  : "&nbsp;")."</td>\n"
                        ."                    <td class='scroll_list' width='40'>".($row["sec"]  ? $row["sec"]  : "&nbsp;")."</td>\n"
                        ."                    <td class='scroll_list' width='65'>".($row["format"]  ? $row["format"]  : "&nbsp;")."</td>\n";
                }
                $out.=
                    "                    <td class='scroll_list' width='140'><a href=\"javascript:listener_log('".$row["listenerID"]."')\"><b>".$row["name"]."</b></a></td>"
                    ."                    <td class='scroll_list' width='35'>".($row["heard_in"]   ? $row["heard_in"]   : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='40' align='right'>".($row["dx_km"]   ? $row["dx_km"]   : "&nbsp;")."</td>\n"
                    ."                    <td class='scroll_list' width='40' align='right'>".($row["dx_miles"]   ? $row["dx_miles"]   : "&nbsp;")."</td>\n"
                    .(\Rxx\Rxx::isAdmin() ?
                        "                    <td class='scroll_list' width='35' align='right'><a href='".system_URL."/".$mode."?submode=delete&ID=$ID&targetID=".$row["ID"]."'>X</a></td>\n"
                        :
                        ""
                    )
                    ."                  </tr>\n";
            }
            $out.=
                "                  </div></table></td>\n"
                ."              </tr>\n"
                ."              <tr class='noprint'>\n"
                ."                <td class='downloadTableContent' align='center'>\n"
                ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
                ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> "
                ."</td>\n"
                ."              </tr>\n"
                ."            </table></td>\n"
                ."          </tr>\n"
                ."        </table></form></td>\n"
                ."      </tr>\n"
                ."    </table></td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."</table>\n";
        }
        $out.=  "</form>\n";
//  $out[] =	"<pre>$sql</pre>";

        return $out;
    }
}
