<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:54 PM
 */

namespace Rxx\Tools;


/**
 * Class Tools
 * @package Rxx\Tools
 */
class Tools {
    /**
     * @return string
     */
    function tools() {
        global $script, $mode, $submode;
        return
            "<form name='form' action='".system_URL."' method='POST'>\n"
            ."<input type='hidden' name='mode' value='$mode'>\n"
            ."<input type='hidden' name='submode' value=''>\n"
            ."</form>\n"
            ."<h2>Tools</h2>\n"
            ."<p align='center'><small>Quick Links [\n"
            ."<nobr><a href='#dgps'><b>DGPS Lookup</b></a></nobr> |\n"
            ."<nobr><a href='#coords'><b>Co-ordinates Converter</b></a></nobr> |\n"
            ."<nobr><a href='#navtex'><b>NAVTEX Missed Shift Translator</b></a></nobr> |\n"
            ."<nobr><a href='#sunrise'><b>Sunrise Calculator</b></a></nobr> |\n"
            ."<nobr><a href='#links'><b>Other NDB Databases</b></a></nobr>\n"
            ."]</small></p><br><br>\n"
            .tools_DGPS_lookup()."<br><br><br>\n"
            .tools_coordinates_conversion()."<br><br><br>\n"
            .tools_navtex_fixer()."<br><br><br>\n"
            .tools_sunrise_calculator()."<br><br><br>\n"
            .tools_links()
            ."</form></span>\n";
    }

    /**
     * @return string
     */
    function tools_coordinates_conversion() {
        global $mode,$script,$GSQ;
        return
            "<form name='coords' onsubmit='return false;'>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort' colspan='3'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='coords'></a>Coordinates Converter</th>\n"
            .($mode=="tools" ?
                "        <th align='right' class='downloadTableHeadings_nosort'>"
                ."<a href=\"javascript:popWin('$script?mode=tools_coordinates_conversion','tools_coordinates_conversion','scrollbars=0,resizable=0',610,144,'centre')\">"
                ."<img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' colspan='3'>To convert between systems, enter known values then click the button next to them.</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' align='right'>Lat DD&deg;MM'SS\"N/S <input type='text' size='10' name='lat_dd_mm_ss' class='formField' value=''></td>\n"
            ."    <td class='downloadTableContent' align='right'>Lon DDD&deg;MM'SS\"E/W <input type='text' size='10' name='lon_dd_mm_ss' class='formField' value=''></td>\n"
            ."    <td class='downloadTableContent'>"
            ."<input type='button' name='go' value='Convert' onclick='conv_dd_mm_ss(document.coords);deg_gsq(document.coords)' class='formButton'>\n"
            ."<input type='button' name='map' value='Map' onclick='if (conv_dd_mm_ss(document.coords)) { deg_gsq(document.coords);popup_mapquestmap(\"5\",lat_dddd.value,lon_dddd.value);}' class='formButton'>\n"
            ."<input type='button' name='photo' value='Photo' onclick='if (conv_dd_mm_ss(document.coords)) { deg_gsq(document.coords);popup_map(\"5\",lat_dddd.value,lon_dddd.value);}' class='formButton'></td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' align='right'>Lat +/-DD.DDDD <input type='text' size='10' name='lat_dddd' class='formField'></td>\n"
            ."    <td class='downloadTableContent' align='right'>Lon +/-DDD.DDDD <input type='text' size='10' name='lon_dddd' class='formField'></td>\n"
            ."    <td class='downloadTableContent'>"
            ."<input type='button' name='go2' value='Convert' onclick='conv_dd_dddd(document.coords);deg_gsq(document.coords);' class='formButton'>\n"
            ."<input type='button' name='map2' value='Map' onclick='if (conv_dd_dddd(document.coords)) { deg_gsq(document.coords);popup_mapquestmap(\"5\",lat_dddd.value,lon_dddd.value);}' class='formButton'>\n"
            ."<input type='button' name='photo2' value='Photo' onclick='if (conv_dd_dddd(document.coords)) { deg_gsq(document.coords);popup_map(\"5\",lat_dddd.value,lon_dddd.value);}' class='formButton'></td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' align='right' colspan='2'>XXnnxx <input type='text' size='10' name='GSQ' class='formField' value='$GSQ'></td>\n"
            ."    <td class='downloadTableContent'>"
            ."<input type='button' name='go3' value='Convert' onclick='gsq_deg(document.coords);conv_dd_dddd(document.coords)' class='formButton'>\n"
            ."<input type='button' name='map3' value='Map' onclick='if (gsq_deg(document.coords)) { conv_dd_dddd(document.coords);popup_mapquestmap(\"5\",lat_dddd.value,lon_dddd.value);}' class='formButton'>\n"
            ."<input type='button' name='photo3' value='Photo' onclick='if (gsq_deg(document.coords)) { conv_dd_dddd(document.coords);popup_map(\"5\",lat_dddd.value,lon_dddd.value);}' class='formButton'></td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' align='center' colspan='3'><input type='reset' value='Reset' class='formButton'></td>\n"
            ."  </tr>\n"
            ."</table>\n"
            .($GSQ!="" ? "<script language='javascript' type='text/javascript'>gsq_deg(document.coords);conv_dd_dddd(document.coords)</script>" : "")
            ."</form>\n";
    }

    /**
     * @return string
     */
    function tools_DGPS_lookup() {
        global $script, $mode;
        return
            "<script language='JavaScript' type='text/javascript' src='".system_URL."/export_javascript_DGPS'></script>\n"
            ."<script language='javascript' type='text/javascript'>\n"
            ."function signal_info(ID){\n"
            ."  popWin('".system_URL."/signal_info/'+ID,'popsignal','scrollbars=0,resizable=1',640,380,'centre');\n"
            ."}\n"
            ."</script>\n"
            ."<form name='dgps' onsubmit='document.dgps.details.value=dgps_lookup(document.dgps.ref.value);return false;'>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort' colspan='2'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='dgps'></a>DGPS Station ID Lookup</th>\n"
            .($mode=="tools" ?
                "        <th align='right' class='downloadTableHeadings_nosort'>"
                ."<a href=\"#\" onclick=\"popWin('".system_URL."/tools_DGPS_popup','tools_DGPS_popup','scrollbars=0,resizable=0',347,227,'centre')\">"
                ."<img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'>"
                ."<img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' colspan='2'>Enter Decoded Reference Station ID and click 'Lookup' to</br>"
            ."    view Station ID and other details.<br><br>\n"
            .(system=="RNA" || system=="RWW" ?
                "    E.g.: Ref Stations <a href=\"javascript:document.dgps.ref.value='52';document.dgps.go.click();\"><b>52</b></a> and\n"
                ."<a href=\"javascript:document.dgps.ref.value='53';document.dgps.go.click();\"><b>53</b></a> both belong to station <a href=\"javascript:signal_info('1104')\"><b>#823</b></a>" : "")
            .(system=="REU" ?
                "    E.g.: Ref Stations <a href=\"javascript:document.dgps.ref.value='822';document.dgps.go.click();\"><b>822</b></a> and\n"
                ."<a href=\"javascript:document.dgps.ref.value='823';document.dgps.go.click();\"><b>823</b></a> are for station <a href=\"javascript:signal_info('2777')\"><b>#492</b></a> in Helgoland, Germany" : "")
            ."</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>Reference Station ID <input type='text' size='3' name='ref' class='formField'></td>\n"
            ."    <td class='downloadTableContent'><input type='submit' name='go' value='Lookup'  class='formButton'></td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' colspan='2' align='center'>Station Details<br><textarea name='details' cols='40' rows='5' readonly class='formFixed'></textarea></td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' align='center' colspan='2'><input type='reset' value='Reset' class='formButton'></td>\n"
            ."  </tr>\n"
            ."</table>\n"
            ."</form>\n";
    }

    /**
     * @return string
     */
    function tools_navtex_fixer() {
        global $mode,$script;
        return
            "<script language='javascript' type='text/javascript'>\n"
            ."<!--\n"
            ."var mumboChars = \"-?:$3!&#8*().,9014'57=2/6+\";\n"
            ."var textChars  = \"ABCDEFGHIJKLMNOPQRSTUVWXYZ\";\n"
            ."function mumboChar(chars) {\n"
            ."  var pos=textChars.indexOf(chars);\n"
            ."  if(pos>-1)\n"
            ."    return mumboChars.charAt(pos);\n"
            ."  else\n"
            ."    return chars;\n"
            ."}\n"
            ."function textChar(chars) {\n"
            ."  var pos=mumboChars.indexOf(chars);\n"
            ."  if(pos>-1)\n"
            ."    return textChars.charAt(pos);\n"
            ."  else\n"
            ."    return chars;\n"
            ."}\n"
            ."function textToMumbo(input) {\n"
            ."  var output=\"\";\n"
            ."  for(i=0;i<input.length;i++) {\n"
            ."    output += mumboChar(input.charAt(i).toUpperCase());\n"
            ."  }\n"
            ."  return output;\n"
            ."}\n"
            ."function mumboToText(input) {\n"
            ."  var output=\"\";\n"
            ."  for(i=0;i<input.length;i++) {\n"
            ."    output += textChar(input.charAt(i));\n"
            ."  }\n"
            ."  return output;\n"
            ."}\n"
            ."//-->\n"
            ."</script>"
            ."<form name='navtex' onsubmit='return false;'>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort' colspan='3'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='coords'></a>NAVTEX Missed Shift Translator (Version 1.3)</th>\n"
            .($mode=="tools" ?
                "        <th align='right' class='downloadTableHeadings_nosort'>"
                ."<a href=\"javascript:popWin('$script?mode=tools_navtex_fixer','tools_navtex_fixer','scrollbars=0,resizable=0',420,390,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent' colspan='3'>Repairs Naxtex decoded message where 'shift' command was garbled.<br>\n"
            ."Enter characters in appropriate window and press button to translate.<br><br>\n"
            ."Thanks to Tjaerand, Curt and Mr. Mosbron for code and concept.</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'><table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" width=\"100%\">\n"
            ."      <tr>\n"
            ."        <td valign=\"top\" align=\"left\">\n"
            ."        <input name=\"translateMumbo\" type=\"button\" value=\" UNSHIFT \" class='FormButton' onClick=\"document.navtex.navtex2.value=mumboToText(document.navtex.navtex1.value);\">\n"
            ."        Missed Shift Characters</td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td align=\"center\"><textarea name=\"navtex1\" cols=40 rows=5 class='FormField'></textarea></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td align=\"right\"><input name=\"clearoutMumbo\" type=\"button\" class='FormButton' value=\" CLEAR \" onClick=\"document.navtex.navtex1.value=''\"></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td align=\"center\"><input name=\"clearoutAll\" type=\"button\" class='FormButton' value=\" CLEAR ALL \" onClick=\"document.navtex.navtex1.value='';document.navtex.navtex2.value='';\"></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td valign=\"top\" align=\"left\">\n"
            ."        <input name=\"translateText\" type=\"button\" value=\"  SHIFT  \" class='FormButton' onClick=\"document.navtex.navtex1.value=textToMumbo(document.navtex.navtex2.value);\">\n"
            ."        Correct Shift Characters</td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td align=\"center\"><textarea name=\"navtex2\" cols=40 rows=5 class='FormField'></textarea></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td align=\"right\"><input name=\"clearoutText\" type=\"button\" class='FormButton' value=\" CLEAR \" onClick=\"document.navtex.navtex2.value='';\"></td>\n"
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>\n"
            ."</table>\n"
            ."</form>\n";
    }

    /**
     * @return string
     */
    function tools_links() {
        global $mode,$script;
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort' colspan='3'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='links'></a>Other Databases on the Web</th>\n"
        .($mode=="tools" ?
            "        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<a href=\"javascript:popWin('$script?mode=tools_links','tools_links','scrollbars=0,resizable=0',520,112,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>\n"
        ."<ul><li><a href='http://www.gcr1.com/5010WEB/' target='_blank'>5010 Web</a> - FAA sourced data primarily for Pilots</li>\n"
        ."<li><a href='http://www.airnav.com/navaids/' target='_blank'>Airnav.com</a> - another aviation database primarily intended for pilots</li>\n"
        ."<li><a href='http://worldaerodata.com/' target='_blank'>Worldaerodata.com</a> - another aviation database primarily intended for pilots</li>\n"
        ."<li><a href='http://frodo.bruderhof.com/ka2qpg/' target='_blank'>Canadian NDBs</a> - maintained by Pierre Thomson, KA2QPG (but a bit out of date)</li>\n"
        ."<li><a href='http://home.cogeco.ca/~dxinfo/ndb.htm' target='_blank'>LF/MF Radionavigation Stations</a> - Compiled by William Hepburn, LWCA</li>\n"
        ."<li><a href='http://www.csi-wireless.com/support/pdfs/radiolistings.pdf' target='_blank'>DGPS Database</a> - Worldwide list of DGPS Stations by CSI Wireless</li>\n"
        ."    </td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    function tools_sunrise_calculator() {
        global $mode,$script;
        return "<script language='JavaScript' type='text/javascript' src='".BASE_PATH."assets/sunrise.js'></script>\n"
        ."<form NAME='sunForm' onSubmit='formValues(this, 0); set_sunrise_cookies(this); return false;'>\n"
        ."<table cellpadding='0' border='0' cellspacing='0' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort' colspan='3'><table cellpadding='2' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='sunrise'></a>Sunrise / Sunset Calculator</th>\n"
        .($mode=="tools" ?
            "        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<a href=\"javascript:popWin('$script?mode=tools_sunrise_calculator','tools_sunrise_calculator','scrollbars=0,resizable=0',455,312,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td><table BORDER='0' cellspacing='1' cellpadding='2'>\n"
        ."      <tr>\n"
        ."        <td class='downloadTableContent' COLSPAN=\"4\">See <a href='http://nist.time.gov' target='_blank'><b>http://nist.time.gov</b></a> for a sunrise map.\n"
        ."        </td>\n"
        ."      </tr>\n"
        ."      <tr ALIGN=\"CENTER\">\n"
        ."        <td class='downloadTableContent'><table cellspacing='0' cellpadding='0' border='0'>\n"
        ."          <tr>\n"
        ."            <td align='center'>Grid Square<BR>\n"
        ."              <input type=\"text\" NAME=\"GSQ\" SIZE=\"6\" VALUE=\"\" onChange=\"if (!validate_GSQ(this.value)) { alert('Please input a valid GSQ value');}else { gsq_deg(document.sunForm); }\" class=\"formFixed\"></td>\n"
        ."          </tr>\n"
        ."        </table></td>\n"
        ."        <td class='downloadTableContent' ROWSPAN=\"3\">\n"
        ."          <TEXTAREA NAME=\"outpResult\" COLS=\"25\" ROWS=\"9\" class='formFixed' readonly></TEXTAREA></td>\n"
        ."      </tr>\n"
        ."      <tr ALIGN=\"CENTER\">\n"
        ."        <td class='downloadTableContent'><table cellspacing='0' cellpadding='0' border='0'>\n"
        ."          <tr>\n"
        ."            <td align='center'>Latitude (S=neg.)<BR>\n"
        ."              <input type=\"text\" NAME=\"lat_dddd\" SIZE=\"6\" VALUE=\"\" onChange=\"checkFloat(this, -90.0, 90.0, 'Latitude', 0)\" class='formField'><BR>\n"
        ."              &nbsp;-90.0 .. 90.0&nbsp;</td>\n"
        ."            <td align='center' class='downloadTableContent' COLSPAN=\"2\">Longitude (W=neg.)<BR>\n"
        ."              <input type=\"text\" NAME=\"lon_dddd\" SIZE=\"7\" VALUE=\"\" onChange=\"checkFloat(this, -180.0, 180.0, 'Longitude', 0)\" class='formField'><BR>\n"
        ."              &nbsp;-180.0 .. 180.0&nbsp;\n</td>\n"
        ."          </tr>\n"
        ."        </table></td>\n"
        ."      </tr>\n"
        ."      <tr ALIGN=\"CENTER\">\n"
        ."        <td class='downloadTableContent'><table cellspacing='0' cellpadding='0' border='0'>\n"
        ."          <tr>\n"
        ."            <td class='downloadTableContent'>Year<BR>\n"
        ."              <input type='text' NAME='inpYear' SIZE='4' VALUE='' onChange=\"checkInt(this, 1901, 2099, 'Year', 0)\" class='formField'>\n"
        ."            </td>\n"
        ."            <td class='downloadTableContent'>Month<BR>\n"
        ."              <input type='text' NAME='inpMonth' SIZE='2' VALUE='' onChange=\"checkInt(this, 1, 12, 'Month', 0)\" class='formField'>\n"
        ."            </td>\n"
        ."            <td class='downloadTableContent'>Date<BR>\n"
        ."              <input type='text' NAME='inpDay' SIZE='2' VALUE='' onChange=\"checkInt(this, 1, 31, 'Date', 0)\" class='formField'>\n"
        ."            </td>\n"
        ."          </tr>\n"
        ."        </table></td>\n"
        ."      </tr>\n"
        ."      <tr ALIGN=\"CENTER\">\n"
        ."        <td class='downloadTableContent' COLSPAN=\"2\">Sunrise Calculator code kindly provided by <a href='http://www.csgnetwork.com' target='_blank'><b>CSG Network</b><br><img src='".BASE_PATH."assets/csglogo2.jpg' alt='CSG Network Group' width='185' height='74' border='1'></a><br>(Ver. 14.7.2, used with permission)\n"
        ."        </td>\n"
        ."      </tr>\n"
        ."      <tr ALIGN=\"CENTER\">\n"
        ."        <td class='downloadTableContent' COLSPAN=\"2\"><input type=\"submit\" VALUE=\"Calculate\" class='formButton'> <input type=\"reset\" value=\"Clear Values\" class='formButton'>\n"
        ."        </td>\n"
        ."      </tr>\n"
        ."    </table></td>\n"
        ."  </tr>\n"
        ."</table>\n"
        ."</form>\n"
        ."<script language='JavaScript' type='text/javascript'>set_sunrise_form(document.sunForm)</script>\n";
    }
}