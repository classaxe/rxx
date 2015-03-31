<?php
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

class Signal extends Record{
  static $colors = array(
    DGPS =>     '#00D8FF',
    DSC =>      '#FFB000',
    HAMBCN =>   '#B8FFC0',
    NAVTEX =>   '#FFB8D8',
    NDB =>      '#FFFFFF',
    TIME =>     '#FFE0B0',
    OTHER =>    '#B8F8FF'
);


  function __construct($ID){
    parent::Record($ID, 'signals');
  }

  function count_dgps_messages() {
    return count_attachments($this->table,$this->ID,'DGPS Message');
  }

  function get_dgps_messages() {
    return get_attachments($this->table,$this->ID,'DGPS Message');
  }

  function tabs() {
    $signal = $this->get_record();
    $out = tabItem("Profile","signal_info",50);
    if (!$signal){
      return $out;
    }
    if ($signal['logs']) {
      $out.=
         tabItem("Listeners","signal_listeners",80)
        .tabItem("Logs (".$signal['logs'].")","signal_log",85);
    }
    if ($signal['GSQ']) {
      $out.=
         tabItem("QNH","signal_QNH",35);
    }
    if ($signal['type']=='1') {
      $messages = $this->count_dgps_messages();
      $out.=
         tabItem("Messages (".$messages.")","signal_dgps_messages",110);
    }
    return $out;
  }
}

// ************************************
// * signal_dgps_messages()           *
// ************************************
function signal_dgps_messages() {
  global $ID, $mode, $submode, $sortBy, $target;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
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
  $Obj =      new Signal($ID);
  if ($ID) {
    $row =		$Obj->get_record();
    $active =		$row["active"];
    $call =		    $row["call"];
    $format =		stripslashes($row["format"]);
    $GSQ =		    $row["GSQ"];
    $heard_in_html =	$row["heard_in_html"];
    $ITU =		$row["ITU"];
    $khz =		$row["khz"];
    $lat =		$row["lat"];
    $lon =		$row["lon"];
    $logs =		$row["logs"];
    $LSB =		$row["LSB"];
    $LSB_approx =	$row["LSB_approx"];
    $last_heard =	$row["last_heard"];
    $notes =		stripslashes($row["notes"]);
    $pwr =		$row["pwr"];
    $QTH =		$row["QTH"];
    $sec =		$row["sec"];
    $SP =		$row["SP"];
    $type =		$row["type"];
    $USB =		$row["USB"];
    $USB_approx =	$row["USB_approx"];
    $submode =		"update";
  }
  else {
    $submode =	"add";
    $active =	"1";
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
  $dgps_messages = $Obj->get_dgps_messages();
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

function signal_info() {
  global $ID, $mode, $submode;
  global $active, $call, $GSQ, $format, $sec, $heard_in_html, $ITU, $khz, $last_heard, $LSB, $LSB_approx, $notes, $pwr, $QTH, $SP, $type, $USB, $USB_approx;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
      $ID = array_pop($path_arr);
    }
  }
  $call =	trim(strToUpper(urldecode($call)));
  $ITU =	trim(strToUpper($ITU));
  $SP =		trim(strToUpper($SP));
  $lat =	0;
  $lon =	0;
  if ($GSQ) {
    $GSQ =	strtoUpper(substr($GSQ,0,4)).strtoLower(substr($GSQ,4,2));
    $a = 	GSQ_deg($GSQ);
    $lat =	$a["lat"];
    $lon =	$a["lon"];
  }
  $error_msg =	"";
  if ($submode=="add" or $submode=="update") {
    $error_msg = check_sp_itu($SP,$ITU);
  }
  if ($error_msg=="") {
    switch ($submode) {
      case "add":
        if ($call && $khz) {
          $sql = "INSERT INTO `signals` SET\n"
		."  `active` = ".addslashes(trim($active)).",\n"
		."  `call` = \"".addslashes(trim($call))."\",\n"
		."  `format` = \"".addslashes(trim($format))."\",\n"
		."  `GSQ` = \"".addslashes(trim($GSQ))."\",\n"
		."  `ITU` = \"".addslashes(trim($ITU))."\",\n"
		."  `khz` = \"".addslashes(trim($khz))."\",\n"
		.($GSQ ? "  `lat` = \"".$lat."\",\n  `lon` = \"".$lon."\",\n" : "")
		.($LSB!="" ? "  `LSB` = \"".addslashes(trim($LSB))."\",\n" : "")
		."  `LSB_approx` = \"$LSB_approx\",\n"
		."  `notes` = \"".addslashes((trim($notes)))."\",\n"
		."  `pwr` = \"".addslashes(trim($pwr))."\",\n"
		."  `QTH` = \"".addslashes(trim($QTH))."\",\n"
		."  `sec` = \"".addslashes(trim($sec))."\",\n"
		."  `SP` = \"".addslashes(trim($SP))."\",\n"
		."  `type` = \"".addslashes(trim($type))."\",\n"
		.($USB!="" ? "  `USB` = \"".addslashes(trim($USB))."\",\n" : "")
		."  `USB_approx` = \"$USB_approx\"\n";
          mysql_query($sql);
          return	"<script language='javascript' type='text/javascript'>window.setTimeout('window.opener.location.reload(1);window.close()',1000)</script>";
        }
        else {
          $out[] =	"<p><font color='red'><b>Error -</b> you must specify at least ID and Frequency</font></p>";
        }
      break;
      case "update":
        $sql =
          "UPDATE `signals` SET\n"
		  ."  `active` = ".addslashes(trim($active)).",\n"
          ."  `call` = \"".addslashes(trim($call))."\",\n"
          ."  `format` = \"".addslashes(trim($format))."\",\n"
          ."  `GSQ` = \"".addslashes(trim($GSQ))."\",\n"
          ."  `ITU` = \"".addslashes(trim($ITU))."\",\n"
          ."  `khz` = \"".addslashes(trim($khz))."\",\n"
          .($GSQ ? "  `lat` = \"".$lat."\",\n  `lon` = \"".$lon."\",\n" : "")
          .($LSB==="" ? "  `LSB` = \N,\n" : "  `LSB` = \"".addslashes(trim($LSB))."\",\n")
          ."  `LSB_approx` = \"$LSB_approx\",\n"
          ."  `notes` = \"".addslashes((trim($notes)))."\",\n"
          ."  `pwr` = \"".addslashes(trim($pwr))."\",\n"
          ."  `QTH` = \"".addslashes(trim($QTH))."\",\n"
          ."  `sec` = \"".addslashes(trim($sec))."\",\n"
          ."  `SP` = \"".addslashes(trim($SP))."\",\n"
          ."  `type` = \"".addslashes(trim($type))."\",\n"
          .($USB==="" ? "  `USB` = \N,\n" : "  `USB` = \"".addslashes(trim($USB))."\",\n")
          ."  `USB_approx` = \"$USB_approx\"\n"
          ."WHERE `ID` = \"".addslashes(trim($ID))."\"";
        mysql_query($sql);
//        $out[] = "<pre>$sql</pre>";
        return	"<script language='javascript' type='text/javascript'>window.close()</script>";
//      return	"<script language='javascript' type='text/javascript'>window.setTimeout('window.opener.location.reload(1);window.close()',500)</script>";
      break;
    }
  }
  $Obj =      new SIGNAL($ID);
  if ($ID) {
    $row =		        $Obj->get_record();
    $active =		    $row["active"];
    $call =		        $row["call"];
    $format =		    stripslashes($row["format"]);
    $GSQ =		        $row["GSQ"];
    $heard_in_html =	$row["heard_in_html"];
    $ITU =		        $row["ITU"];
    $khz =		        $row["khz"];
    $lat =		        $row["lat"];
    $lon =		        $row["lon"];
    $logs =		        $row["logs"];
    $LSB =		        $row["LSB"];
    $LSB_approx =	    $row["LSB_approx"];
    $last_heard =	    $row["last_heard"];
    $notes =		    stripslashes($row["notes"]);
    $pwr =		        $row["pwr"];
    $QTH =		        $row["QTH"];
    $sec =		        $row["sec"];
    $SP =		        $row["SP"];
    $type =		        $row["type"];
    $USB =		        $row["USB"];
    $USB_approx =	    $row["USB_approx"];
    $submode =		    "update";
  }
  else {
    $submode =	        "add";
    $active =	        "1";
  }
  $out =
     "<table border='0' cellpadding='0' cellspacing='0'>\n"
    ."  <tr>\n"
    ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
    ."      <tr>\n"
    ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%'>\n"
    ."          <tr>\n"
    ."            <td><h1>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? ($ID=="" ? "Add Signal" : "Edit Signal")."":"Signal")."</h1></td>\n"
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
    .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
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
    ."            <table width='100%' cellpadding='0' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm'>\n"
    ."              <tr class='rowForm'>\n"
    ."                <th align='left' width='90'>ID</th>\n"
    ."                <td colspan='3'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."                  <tr>\n"
    ."                    <td><input type='text' size='12' maxlength='12' name='call' value='".$call."' class='formfield'></td>\n"
    ."                    <td align='right'><b>KHz</b> <input type='text' size='6' maxlength='9' name='khz' value='".(float)$khz."' class='formfield'></td>\n"
    ."                    <td align='right'><b>Pwr</b> <input type='text' size='5' maxlength='7' name='pwr' value='".($pwr ? $pwr : "")."' class='formfield'>W</td>\n"
    ."                    <td align='right'><b>Type</b> ";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out.=
       "<select name='type' class='formField'>\n"
      ."<option value='".DGPS."'".($type==DGPS ? " selected" : "")." style='background-color:".Signal::$colors[DGPS]."'>DGPS</option>\n"
      ."<option value='".DSC."'".($type==DSC ? " selected" : "")." style='background-color:".Signal::$colors[DSC]."'>DSC</option>\n"
      ."<option value='".HAMBCN."'".($type==HAMBCN ? " selected" : "")." style='background-color:".Signal::$colors[HAMBCN]."'>Amateur Beacon</option>\n"
      ."<option value='".NAVTEX."'".($type==NAVTEX ? " selected" : "")." style='background-color:".Signal::$colors[NAVTEX]."'>NAVTEX</option>\n"
      ."<option value='".NDB."'".($type==NDB ? " selected" : "").">NDB</option>\n"
      ."<option value='".TIME."'".($type==TIME ? " selected" : "")." style='background-color:".Signal::$colors[TIME]."'>Time</option>\n"
      ."<option value='".OTHER."'".($type==OTHER ? " selected" : "")." style='background-color:".Signal::$colors[OTHER]."'>Other</option>\n"
      ."</select>\n"
      ;
  }
  else {
    $out.=	"<input type='text' size='5' maxlength='7' class='formfield' value='";
    switch ($type) {
      case DGPS:	$out.=	"DGPS";			    break;
      case DSC:	    $out.=	"DSC";			    break;
      case HAMBCN:	$out.=	"Amateur Beacon";	break;
      case NAVTEX:	$out.=	"NAVTEX";		    break;
      case NDB:		$out.=	"NDB";			    break;
      case TIME:	$out.=	"Time";			    break;
      case OTHER:	$out.=	"Other";		    break;
    }
    $out.=	"'>";
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
    ."                <td><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"ITU\" value=\"".$ITU."\" class=\"formfield\">".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "" : " (".get_ITU($ITU).")")." <span title='Country Codes'><a href='".system_URL."/show_itu' onclick='show_itu();return false' title='NDBList Country codes'><b>(List)</b></a></span></td>\n"
    ."              <tr class='rowForm'>\n"
    ."                <th align='left'>GSQ</th>\n"
    ."                <td colspan='3'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."                  <tr class='rowForm'>"
    ."                    <td><input type='text' size='6' maxlength='6' name='GSQ' value='".$GSQ."' class='formfield'> [ "
    ."<a onmouseover='window.status=\"Use coordinate converter\";return true;' onmouseout='window.status=\"\";return true;' href=\"".system_URL."/tools_coordinates_conversion?GSQ=".$GSQ."\" onclick=\"popWin('".system_URL."/tools_coordinates_conversion?GSQ=".$GSQ."','tools_coordinates_conversion','scrollbars=0,resizable=0',610,144,'centre');return false;\" title='Use coordinate converter'><b>Convert</b></a>"
    .(isset($row) ?
        " | <a onmouseover='window.status=\"View Mapquest map for ".(float)$row["khz"]."-".$row["call"]."\";return true;' onmouseout='window.status=\"\";return true;' href='javascript:popup_mapquestmap(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show Mapquest map\\\n(accuracy limited to nearest Grid Square)'><b>Mapquest</b></a>"
       ." | <a onmouseover='window.status=\"View Google map for ".(float)$row["khz"]."-".$row["call"]."\";return true;' onmouseout='window.status=\"\";return true;' href='javascript:popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show Google map\\\n(accuracy limited to nearest Grid Square)'><b>Google</b></a>"
      : ""
     )
    ." ]</td>\n"
    ."                    <th>Lat</th><td><input type='text' size='10' name='lat_dddd' class='formField' value='".$lat."'></td>\n"
    ."                    <th>Lon</th><td><input type='text' size='10' name='lon_dddd' class='formField' value='".$lon."'></td>\n"
    ."                  </tr>\n"
    ."                </table></td>\n"
    ."              <tr class='rowForm'>\n"
    ."                <td class='downloadTableHeadings_nosort' align='left' colspan='4'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."                  <tr>\n"
    ."                    <td class='downloadTableHeadings_nosort'>&nbsp;Latest Values</td>\n"
    .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
        "                    <td class='downloadTableHeadings_nosort' align='right'><small>(All fields except Status and Notes are updated each time signal is logged)</small></td>\n"
      : ""
     )
    ."                  </tr>\n"
    ."                  </table></td>\n"
    ."              <tr class='rowForm'>\n"
    ."                <th align='left'>LSB</th>\n"
    ."                <td>"
    .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
        "~<input type='text' size='1' maxlength='1' name='LSB_approx' value='".$LSB_approx."' class='formField'> <input type='text' size='4' maxlength='5' name='LSB' value='".$LSB."' class='formField'>"
     :
        "<input type='text' size='4' maxlength='5' name='LSB' value='".$LSB_approx.$LSB."' class='formField'>"
     )
    ."</td>\n"
    ."                <th align='right'>USB</th>\n"
    ."                <td>"
    .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
	   "~<input type='text' size='1' maxlength='1' name='USB_approx' value='".$USB_approx."' class='formField'> <input type='text' size='4' maxlength='5' name='USB' value='".$USB."' class='formField'>"
     :
	   "<input type='text' size='4' maxlength='5' name='USB' value='".$USB_approx.$USB."' class='formField'>"
     )
     ."</td>\n"
     ."              <tr class='rowForm'>\n"
     ."                <th align='left'>Cycle Time (sec)</th>\n"
     ."                <td><input type='text' size='9' maxlength='12' name='sec' value='".$sec."' class='formfield'><a href='javascript: alert(\"INFORMATION\\n\\nWhere two values are given for Cycle Time (e.g. 8.0/28.0),\\nthe first value is for the ID portion of the cycle and the second value is\\nfor the whole thing.\")'><b>(Info)</b></a></td>\n"
     ."                <th align='right'>Format</th>\n"
     ."                <td><input type='text' size='12' maxlength='25' name='format' value='".$format."' class='formfield'></td>\n"
     ."              <tr class='rowForm'>\n"
     ."                <th align='left'>Last Heard</th>\n"
     ."                <td>".$last_heard."</td>\n"
     ."                <th align='right'>Status</th>\n"
     ."                <td>"
     .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
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
     ."                <td colspan='3' valign='top'><textarea class='formField' name='notes' rows='3' cols='40' style='width: 450px; height: ".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "70px;" : "80px;")."'>".$notes."</textarea></td>\n"
     ."              <tr class='rowForm'>\n"
     ."                <td colspan='4' align='center' style='height: 30px;'>\n"
     ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
     ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> "
     .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
         "<input type='submit' value='".($submode=="update" ? "Save" : "Add")."' class='formbutton' style='width: 60px;'>"
       : ""
      )
     ."</td>\n"
     ."              </tr>\n"
     ."            </table>\n"
     .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
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


function signal_list() {
  global $mode, $submode, $targetID, $filter_active, $filter_date_1, $filter_date_2;
  global $filter_dx_gsq, $filter_dx_max, $filter_channels, $filter_dx_min, $filter_dx_units;
  global $filter_heard, $filter_id, $filter_system, $region, $filter_khz_1, $filter_khz_2;
  global $filter_sp, $filter_itu, $listenerID, $sortBy, $grp, $limit, $offset, $offsets;
  global $filter_custom;
  global $type_NDB, $type_TIME, $type_DGPS, $type_DSC, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
  $out =	'';
  // If there's no valid listener data, blank it all out
  if (!($listenerID && is_array($listenerID) && count($listenerID) && $listenerID[0])) {
    $listenerID =	false;
  }
  if ($filter_heard=="(All States and Countries)") {
    $filter_heard="";
  }
  if ($region=="") {
    switch (system) {
      case "REU":	$region="eu";	break;
      case "RNA":	$region="na";	break;
    }
  }
  if ($filter_id && substr($filter_id,0,1)=="#") {
    $type_DGPS=1;
  }
  if ($filter_id && substr($filter_id,0,1)=="$") {
    $type_NAVTEX=1;
  }
  if ($filter_system=="") {
    switch(system) {
      case "RNA":	$filter_system=1;	break;
      case "REU":	$filter_system=2;	break;
      case "RWW":	$filter_system=3;	break;
    }
  }
  switch ($filter_system) {
    case "1":
      $filter_system_SQL =			        "(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
      $filter_log_SQL =				        "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `heard_in` = 'hi'))";
      $filter_listener_SQL =			    "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `SP` = 'hi'))";
      $filter_system_listener_log_SQL =	    "`region` = 'na'";
    break;
    case "2":
      $filter_system_SQL =			        "`heard_in_eu` = 1";
      $filter_log_SQL =				        "(`region` = 'eu')";
      $filter_listener_SQL =			    "(`region` = 'eu')";
      $filter_system_listener_log_SQL =	    "`region` = 'eu'";
    break;
    case "3":
      if ($region!="") {
        $filter_system_SQL =			    "`heard_in_$region`=1";
        $filter_log_SQL =      			    "(`region` = '$region')";
        $filter_listener_SQL =			    "(`region` = '$region')";
        $filter_system_listener_log_SQL =	"1";
      }
      else {
        $filter_system_SQL =			    "1";
        $filter_log_SQL =			        "1";
        $filter_listener_SQL =			    "1";
        $filter_system_listener_log_SQL =	"1";
      }
    break;
    case "not_logged":
      $filter_system_SQL =
         "(`heard_in_af` = 0 AND `heard_in_as` = 0 AND `heard_in_ca` = 0 AND `heard_in_eu` = 0 AND\n"
		."`heard_in_iw` = 0 AND `heard_in_na` = 0 AND `heard_in_oc` = 0 AND `heard_in_sa` = 0)";
      $filter_log_SQL =				        "0";
      $filter_listener_SQL =			    "0";
      $filter_system_listener_log_SQL =		"0";
    break;
    case "all":
      $filter_system_SQL = 			        "1";
      $filter_log_SQL =				        "1";
      $filter_listener_SQL =			    "1";
      $filter_system_listener_log_SQL = 	"1";
    break;
  }
  switch ($filter_custom){
    case 'cle160':
      $filter_custom_SQL =
         "`signals`.`ITU` IN('"
        .implode("','",explode(' ','ABW AFG AFS AGL AIA ALB ALG ALS AND ANI AOE ARG ARM ARS ASC ATA ATG ATN AUI AUS AUT AZE AZR BAH BAL BAR BDI BEL BEN BER BFA BGD BHR BIH BLR BLZ BOL BOT BRA BRB BRI BRM BRU BTN BUL CAB CAF CBG CEU CHL CHN CHR CKH CKS CLI CLM CLN CME CNR COD COG COM COR CPV CTI CTR CUB CVA CYM CYP CZE'))
        ."') OR `signals`.`SP` IN('"
        .implode("','",explode(' ','AB AK AL AR AT AZ BC CA CO CT'))
        ."')";
    break;
  }
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    switch ($submode){
      case "delete":
        $sql =	"DELETE FROM `logs` WHERE `signalID` = \"".addslashes($targetID)."\"";
        mysql_query($sql);
        $sql =	"DELETE FROM `signals` WHERE `ID` = \"".addslashes($targetID)."\"";
        mysql_query($sql);
      break;
    }
  }
  $filter_type =	array();
  if (!($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
    switch (system) {
      case "RNA":	$type_NDB =	1;	break;
      case "REU":	$type_NDB =	1;	break;
      case "RWW":	$type_NDB =	1;	break;
    }
  }
  if ($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
    if ($type_NDB) {
      $filter_type[] =	 "`type` = ".NDB;
    }
    if ($type_DGPS) {
      $filter_type[] =	 "`type` = ".DGPS;
    }
    if ($type_DSC) {
      $filter_type[] =	 "`type` = ".DSC;
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
  if ($filter_heard) {
    $tmp =		explode(" ",strToUpper($filter_heard));
    sort($tmp);
    $filter_heard =	implode(" ",$tmp);
  }
  // Filter on 'Heard in':
  $filter_heard_SQL =	explode(" ",strToUpper($filter_heard));
  if ($grp=="all") {
    $filter_heard_SQL =	"`signals`.`heard_in` LIKE '%".implode($filter_heard_SQL,"%' AND `signals`.`heard_in` LIKE '%")."%'";
  }
  else {
    $filter_heard_SQL =	"`logs`.`heard_in` = '".implode($filter_heard_SQL,"' OR `logs`.`heard_in` = '")."'";
  }
  if ($filter_sp) {
    $tmp =		explode(" ",strToUpper($filter_sp));
    sort($tmp);
    $filter_sp =	    implode(" ",$tmp);
    $filter_sp_SQL =	explode(" ",strToUpper($filter_sp));
    $filter_sp_SQL =	"`signals`.`SP` = '".implode($filter_sp_SQL,"' OR `signals`.`SP` = '")."'";
  }
  if ($filter_itu) {
    $tmp =		explode(" ",strToUpper($filter_itu));
    sort($tmp);
    $filter_itu =	implode(" ",$tmp);
    $filter_itu_SQL =	explode(" ",strToUpper($filter_itu));
    $filter_itu_SQL =	"`signals`.`ITU` = '".implode($filter_itu_SQL,"' OR `signals`.`ITU` = '")."'";
  }
  // Filter on Date Last Heard:
  if ($filter_date_1 || $filter_date_2) {
    if ($filter_date_1 == "") {
      $filter_date_1 = "1900-01-01";
    }
    if ($filter_date_2 == "") {
      $filter_date_2 = "2020-01-01";
    }
  }
  // Filter on Frequencies:
  if ($filter_khz_1 || $filter_khz_2) {
    if ($filter_khz_1 == "") {
      $filter_khz_1 = 0;
    }
    if ($filter_khz_2 == "") {
      $filter_khz_2 = 1000000;
    }
    $filter_khz_1 =	(float)$filter_khz_1;
    $filter_khz_2 =	(float)$filter_khz_2;
  }
  $filter_sp =		strToUpper($filter_sp);
  $filter_itu =		strToUpper($filter_itu);
  $filter_id =		strToUpper($filter_id);
  if (!isset($filter_dx_units)) {
    $filter_dx_units = "km";
  }
  if ($filter_dx_gsq) {
    $filter_dx_gsq =	strtoUpper(substr($filter_dx_gsq,0,4)).strtoLower(substr($filter_dx_gsq,4,2));
    $a = 		GSQ_deg($filter_dx_gsq);
    $filter_dx_lat =	$a["lat"];
    $filter_dx_lon =	$a["lon"];
  }
  $filter_by_range = ($filter_dx_gsq && ($filter_dx_min || $filter_dx_max));
  if (!$filter_by_range && ($sortBy == "range" || $sortBy == "range_d")) {
    $sortBy =	"khz";
  }
  $sql =
     "SELECT\n"
	."  DATE_FORMAT(MIN(`date`),'%e %b %Y') AS `first`,\n"
	."  DATE_FORMAT(MAX(`date`),'%e %b %Y') AS `last`\n"
	."FROM\n"
	."  `logs`\n"
	."WHERE\n"
	."  ".$filter_log_SQL." AND\n"
	."  `date` !=\"\" AND\n"
	."  `date` !=\"0000-00-0000\"";
  $result = @mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $first =	$row["first"];
  $last =	$row["last"];
  if ($region=="") {
    switch (system) {
      case "REU":	$region="eu";	break;
      case "RNA":	$region="na";	break;
    }
  }
  $listeners =	listener_get_count($region);
  $sql =
     "SELECT COUNT(*) AS `signals` from `signals` WHERE ("
	."`heard_in_af`=0 AND `heard_in_as`=0 AND `heard_in_ca`=0 AND `heard_in_eu`=0 AND\n"
	."`heard_in_iw`=0 AND `heard_in_na`=1 AND `heard_in_oc`=0 AND `heard_in_sa`=0)";
  $result = 	mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $RNA =	$row["signals"];
  $sql =
     "SELECT COUNT(*) AS `signals` from `signals` WHERE ("
	."`heard_in_af`=0 AND `heard_in_as`=0 AND `heard_in_ca`=0 AND `heard_in_eu`=1 AND\n"
	."`heard_in_iw`=0 AND `heard_in_na`=0 AND `heard_in_oc`=0 AND `heard_in_sa`=0)";
  $result = 	mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $REU =	$row["signals"];
  $sql =
     "SELECT COUNT(*) AS `signals` from `signals` WHERE ("
	."`heard_in_eu`=1 AND `heard_in_na`=1)";
  $result = 	mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $both =	$row["signals"];
  $sql =
     "SELECT COUNT(*) AS `signals` from `signals` WHERE (`logs`>0)";
  $result = 	mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $RWW =	$row["signals"];
  $sql =
     "SELECT COUNT(*) AS `signals` from `signals` WHERE ("
	."`heard_in_af`=0 AND `heard_in_as`=0 AND `heard_in_ca`=0 AND `heard_in_eu`=0 AND\n"
	."`heard_in_iw`=0 AND `heard_in_na`=0 AND `heard_in_oc`=0 AND `heard_in_sa`=0)";
  $result = 	@mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $unassigned =	$row["signals"];
  $sql =
     "SELECT COUNT(*) AS `logs` from `logs` WHERE $filter_system_listener_log_SQL";
  $result = 	@mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $logs =	$row["logs"];
  $sql =
     "SELECT\n"
	.($filter_heard || $listenerID ?
	    "  COUNT(distinct `signals`.`ID`) AS `count`\n"
	   ."FROM\n  `signals`,\n  `logs`\n"
	   ."WHERE\n  `signals`.`ID` = `logs`.`signalID` AND\n"
	   ."  ".$filter_system_SQL." "
	   .($filter_heard ? "AND\n ($filter_heard_SQL)" : "")
	   .($listenerID ? "AND\n(`logs`.`listenerID`=".implode($listenerID," OR `logs`.`listenerID`=").") " : "")
	 :
        "  COUNT(*) AS `count`\n"
       ."FROM\n  `signals`\nWHERE\n  ".$filter_system_SQL
     )
	.($filter_active ? " AND\n `active` = 1" : "")
	.($filter_by_range && $filter_dx_min ? " AND\n round(degrees(acos(sin(radians($filter_dx_lat)) * sin(radians(signals.lat)) + cos(radians($filter_dx_lat)) * cos(radians(signals.lat)) * cos(radians($filter_dx_lon - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) > $filter_dx_min" : "")
	.($filter_by_range && $filter_dx_max ? " AND\n round(degrees(acos(sin(radians($filter_dx_lat)) * sin(radians(signals.lat)) + cos(radians($filter_dx_lat)) * cos(radians(signals.lat)) * cos(radians($filter_dx_lon - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) < $filter_dx_max" : "")
	.($filter_custom ? " AND\n  ($filter_custom_SQL)" : "")
	.($filter_date_2 ? " AND\n (`last_heard` >= \"$filter_date_1\" AND `last_heard` <= \"$filter_date_2\")" : "")
	.($filter_id ? " AND\n (`signals`.`call` LIKE \"%$filter_id%\")" : "")
	.($filter_itu ? " AND\n ($filter_itu_SQL)" : "")
	.($filter_khz_2 ? " AND\n (`khz` >= $filter_khz_1 AND `khz` <= $filter_khz_2)" : "")
	.($filter_channels==1 ? " AND\n MOD((`khz`* 1000),1000) = 0" : "")
	.($filter_channels==2 ? " AND\n MOD((`khz`* 1000),1000) != 0" : "")
	.($filter_sp ? " AND\n ($filter_sp_SQL)" : "")
	.($filter_type ? " AND\n $filter_type" : "");
//  print("<pre>$sql</pre>");
  $total =	0;
  if ($result = @mysql_query($sql)) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $total =	$row["count"];
  }
  if (empty($limit))  { $limit = 50; } else { $limit = (int) $limit; }
  if (empty($offset)) { $offset = 0; } else { $offset = (int) $offset; }
  if ($offset<0) { $offset=0; }
  if ($sortBy =='CLE64' and $filter_dx_gsq=='') {
     $sortBy = '';
  }
  if ($sortBy=="") {
    $sortBy = "khz";
  }
  $sortBy_SQL =		"";
  switch ($sortBy) {
    case "call":		$sortBy_SQL =	"`active` DESC, `call` ASC, `khz` ASC";					break;
    case "call_d":		$sortBy_SQL =	"`active` DESC, `call` DESC, `khz` ASC";				break;
    case "dx":			$sortBy_SQL =	"`active` DESC, `dx_km` ASC";						break;
    case "dx_d":		$sortBy_SQL =	"`active` DESC, `dx_km` DESC";						break;
    case "dx_deg":	    $sortBy_SQL =	"`active` DESC, CAST(`dx_range` AS UNSIGNED) ASC";						break;
    case "dx_deg_d":	$sortBy_SQL =	"`active` DESC, CAST(`dx_range` AS UNSIGNED) DESC";						break;
    case "format":		$sortBy_SQL =	"`active` DESC, `signals`.`format`='' OR `signals`.`format` IS NULL, `signals`.`format` ASC";		break;
    case "format_d":	$sortBy_SQL =	"`active` DESC, `format`='' OR `format` IS NULL, `format` DESC";	break;
    case "gsq":			$sortBy_SQL =	"`active` DESC, `GSQ` ASC";						break;
    case "gsq_d":		$sortBy_SQL =	"`active` DESC, `GSQ` DESC";						break;
    case "heard_in":	$sortBy_SQL =	"`active` DESC, `heard_in` ASC, `khz` ASC, `call` ASC";			break;
    case "heard_in_d":	$sortBy_SQL =	"`active` DESC, `heard_in` DESC, `khz` ASC, `call` ASC";		break;
    case "itu":			$sortBy_SQL =	"`active` DESC, `ITU` ASC, `SP` ASC, `khz` ASC, `call` ASC";		break;
    case "itu_d":		$sortBy_SQL =	"`active` DESC, `ITU` DESC, `SP` ASC, `khz` ASC, `call` ASC";		break;
    case "khz":			$sortBy_SQL =	"`active` DESC, `khz` ASC, `call` ASC";					break;
    case "khz_d":		$sortBy_SQL =	"`active` DESC, `khz` DESC, `call` ASC";				break;
    case "last_heard":	$sortBy_SQL =	"`active` DESC, `last_heard` IS NULL, `last_heard` ASC";		break;
    case "last_heard_d":$sortBy_SQL =	"`active` DESC, `last_heard` IS NULL, `last_heard` DESC";		break;
    case "logs":		$sortBy_SQL =	"`active` DESC, `logs` IS NULL, `logs` ASC";				break;
    case "logs_d":		$sortBy_SQL =	"`active` DESC, `logs` IS NULL, `logs` DESC";				break;
    case "LSB":
      if ($offsets=='') {
        $sortBy_SQL =		"`active` DESC, `signals`.`LSB` IS NULL, `signals`.`LSB` ASC";
      }
      else {
        $sortBy_SQL =		"`active` DESC, `signals`.`LSB` IS NULL, `signals`.`khz`-(`signals`.`LSB`/1000) ASC";
      }
    break;
    case "LSB_d":
      if ($offsets=='') {
        $sortBy_SQL =		"`active` DESC, `signals`.`LSB` IS NULL, `signals`.`LSB` DESC";
      }
      else {
        $sortBy_SQL =		"`active` DESC, `signals`.`LSB` IS NULL, `signals`.`khz`-(`signals`.`LSB`/1000) DESC";
      }
    break;
    case "notes":		$sortBy_SQL =	"`active` DESC, `notes`='' OR `notes` IS NULL, `notes` ASC";		break;
    case "notes_d":		$sortBy_SQL =	"`active` DESC, `notes`='' OR `notes` IS NULL, `notes` DESC";		break;
    case "QTH":			$sortBy_SQL =	"`active` DESC, `QTH`='' OR `QTH` IS NULL, `QTH` ASC";			break;
    case "QTH_d":		$sortBy_SQL =	"`active` DESC, `QTH`='' OR `QTH` IS NULL, `QTH` DESC";			break;
    case "pwr":			$sortBy_SQL =	"`active` DESC, `pwr`=0, `pwr` ASC";					break;
    case "pwr_d":		$sortBy_SQL =	"`active` DESC, `pwr`=0, `pwr` DESC";					break;
    case "range_dx_km":		$sortBy_SQL =	"`active` DESC, `lat` IS NULL, `range_dx_km` ASC";		break;
    case "range_dx_km_d":	$sortBy_SQL =	"`active` DESC, `lat` IS NULL, `range_dx_km` DESC";		break;
    case "range_dx_deg":	$sortBy_SQL =	"`active` DESC, `lat` IS NULL, CAST(`range_dx_deg` AS UNSIGNED) ASC";		break;
    case "range_dx_deg_d":	$sortBy_SQL =	"`active` DESC, `lat` IS NULL, CAST(`range_dx_deg` AS UNSIGNED) DESC";		break;
    case "sec":			$sortBy_SQL =	"`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL, CAST(`signals`.`sec` AS UNSIGNED) ASC";			break;
    case "sec_d":		$sortBy_SQL =	"`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL, CAST(`signals`.`sec` AS UNSIGNED) DESC";			break;
    case "sp":			$sortBy_SQL =	"`active` DESC, `SP`='' or `SP` IS NULL,`SP` ASC,`ITU` ASC, `khz` ASC, `call` ASC";	break;
    case "sp_d":		$sortBy_SQL =	"`active` DESC, `SP`='' or `SP` IS NULL,`SP` DESC,`ITU` ASC, `khz` ASC, `call` ASC";	break;
    case "USB":
      if ($offsets=='') {
        $sortBy_SQL =		"`active` DESC, `signals`.`USB` IS NULL, `signals`.`USB` ASC";
      }
      else {
        $sortBy_SQL =		"`active` DESC, `signals`.`USB` IS NULL, `signals`.`khz`+(`signals`.`USB`/1000) ASC";
      }
    break;
    case "USB_d":
      if ($offsets=='') {
        $sortBy_SQL =		"`active` DESC, `signals`.`USB` IS NULL, `signals`.`USB` DESC";
      }
      else {
        $sortBy_SQL =		"`active` DESC, `signals`.`USB` IS NULL, `signals`.`khz`+(`signals`.`USB`/1000) DESC";
      }
    break;
    case "CLE64":
      if ($filter_dx_gsq) {
				$sortBy_SQL =	"`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)>='A' DESC, LEFT(`signals`.`call`,1) ASC, `range_dx_km` DESC";
      }
    break;
    case "CLE64_d":
      if ($filter_dx_gsq) {
				$sortBy_SQL =	"`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)<='Z' ASC, LEFT(`signals`.`call`,1) DESC, `range_dx_km` DESC";
      }
    break;
  }
  if ($filter_id) {
    $sortBy_SQL =	"`call` = '$filter_id' DESC, $sortBy_SQL";
  }
  $sql =
     "SELECT\n"
    .($filter_heard || $listenerID ?
        ($listenerID ?
           "  DISTINCT `signals`.*,\n"
          ." `logs`.`dx_km`,\n"
          ." `logs`.`dx_miles`\n"
        :
           "  DISTINCT `signals`.*\n"
        )
		.($filter_dx_gsq ?
           ",\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS($filter_dx_lon - signals.lon))))*69, 2),'') AS UNSIGNED) AS `range_dx_miles`,\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS($filter_dx_lon - signals.lon))))*111.05, 2),'') AS UNSIGNED) AS `range_dx_km`,\n"
          ."  CAST(COALESCE(ROUND((DEGREES(atan2(sin(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")) * cos(RADIANS(signals.lat)), cos(RADIANS(".$filter_dx_lat.")) * sin(RADIANS(signals.lat)) - sin(RADIANS(".$filter_dx_lat.")) * cos(RADIANS(signals.lat)) * cos(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")))) + 360) mod 360),'') AS UNSIGNED) AS `range_dx_deg`\n"
		:
           ""
        )
		."FROM\n"
        ."  `signals`,\n"
        ."  `logs`\n"
        ."WHERE\n"
        ."  `signals`.`ID` = `logs`.`signalID` AND\n"
        ."  ".$filter_system_SQL." "
        .($filter_heard ?
            "AND\n"
           ."  (".$filter_heard_SQL.")"
         :  ""
         )
        .($listenerID ?
            "AND\n"
           ."  (`logs`.`listenerID`=".implode($listenerID," OR `logs`.`listenerID`=").") "
         :
            ""
         )
      :
         " `signals`.*"
		.($filter_dx_gsq ?
           ",\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS(".$filter_dx_lon." - signals.lon))))*69, 2),'') AS UNSIGNED) AS `range_dx_miles`,\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS(".$filter_dx_lon." - signals.lon))))*111.05, 2),'') AS UNSIGNED) AS `range_dx_km`,\n"
          ."  CAST(COALESCE(ROUND((DEGREES(atan2(sin(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")) * cos(RADIANS(signals.lat)), cos(RADIANS(".$filter_dx_lat.")) * sin(RADIANS(signals.lat)) - sin(RADIANS(".$filter_dx_lat.")) * cos(RADIANS(signals.lat)) * cos(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")))) + 360) mod 360),'') AS UNSIGNED) AS `range_dx_deg`\n"
         :
            "\n"
         )
		."FROM\n"
        ."  `signals`\n"
        ."WHERE\n"
        ."  ".$filter_system_SQL
      )
	  .($filter_active ?
         " AND\n"
        ."  `active` = 1"
        :
         ""
       )
	  .($filter_by_range && $filter_dx_min ?
          " AND\n"
         ."  round(degrees(acos(sin(radians(".$filter_dx_lat.")) * sin(radians(signals.lat)) + cos(radians(".$filter_dx_lat.")) * cos(radians(signals.lat)) * cos(radians(".$filter_dx_lon." - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) > ".$filter_dx_min
        : ""
       )
	  .($filter_by_range && $filter_dx_max ?
          " AND\n"
         ."  round(degrees(acos(sin(radians(".$filter_dx_lat.")) * sin(radians(signals.lat)) + cos(radians(".$filter_dx_lat.")) * cos(radians(signals.lat)) * cos(radians(".$filter_dx_lon." - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) < ".$filter_dx_max
        : ""
       )
      .($filter_custom ? " AND\n  ($filter_custom_SQL)" : "")
	  .($filter_date_2 ?
          " AND\n  (`last_heard` >= \"$filter_date_1\" AND `last_heard` <= \"$filter_date_2\")"
        : ""
       )
	  .($filter_id ?
          " AND\n  (`signals`.`call` LIKE \"%$filter_id%\")"
        : ""
       )
	  .($filter_itu ? " AND\n ($filter_itu_SQL)" : "")
	  .($filter_khz_2 ? " AND\n  (`khz` >= $filter_khz_1 AND `khz` <= $filter_khz_2)" : "")
	  .($filter_channels==1 ? " AND\n MOD((`khz`* 1000),1000) = 0" : "")
	  .($filter_channels==2 ? " AND\n MOD((`khz`* 1000),1000) != 0" : "")
	  .($filter_sp ? " AND\n ($filter_sp_SQL)" : "")
	  .($filter_type ? " AND\n  $filter_type" : "")
	  .($sortBy_SQL ? "\nORDER BY\n  ".$sortBy_SQL : "")
	  .($limit!=-1 ? "\nLIMIT\n  $offset, $limit" : "");
//  print("<pre>$sql</pre>");
//  $out.=	"<!--\n\n $sql \n\n-->";
  $result = 	@mysql_query($sql); // Use @ to prevent tablescan warning in RWW
  $out.=
     "<script type='text/javascript'>\n"
	."function get_type(form){\n"
	."  if (form.type_DGPS.checked)   return ".DGPS.";\n"
	."  if (form.type_DSC.checked)    return ".DSC.";\n"
	."  if (form.type_HAMBCN.checked) return ".HAMBCN.";\n"
	."  if (form.type_NAVTEX.checked) return ".NAVTEX.";\n"
	."  if (form.type_NDB.checked)    return ".NDB.";\n"
	."  if (form.type_HAMBCN.checked) return ".HAMBCN.";\n"
	."  if (form.type_OTHER.checked)  return ".OTHER.";\n"
	."  if (form.type_TIME.checked)   return ".TIME.";\n"
	."  return '';\n"
	." }\n"
	."</script>"
	."<form name='form' action='".system_URL."/".$mode."' method='POST'>\n"
	."<input type='hidden' name='mode' value='$mode'>\n"
	."<input type='hidden' name='submode' value=''>\n"
	."<input type='hidden' name='targetID' value=''>\n"
	."<input type='hidden' name='sortBy' value='$sortBy'>\n"
	."<table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Signal List</h2><br>\n"
	."<ul>\n"
    ."<li>Click on any station <b>ID</b> for details, <b>GSQ</b> for location map, <b>Heard In</b> list for reception map and <b>Logs</b> value to see all logs for the station.</li>\n"
	."<li>To list different types of signals, check the boxes shown for 'Types' below. Inactive stations are normally shown at the end of the report.</li>\n"
	."<li>This report prints best in Landscape.</li></ul></td></tr></table>\n";
  if ($type_NDB) {
    $out.=
     "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting NDBs</h2><br>Please use the following list as an additional data source - the ship listings from around 404KHz may prove particularly useful:<br>\n"
	."[ <a href='http://www.dxinfocentre.com/ndb.htm' target='_blank'><b>William Hepburn's LF List</b></a> ]</td></tr></table>\n";
  }
  if ($type_NAVTEX) {
    $out.=
     "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting Navtex Stations</h2><br>Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:<br>\n"
	."[ <a href='http://www.dxinfocentre.com/navtex.htm' target='_blank'><b>William Hepburn's LF List</b></a> |"
    ."  <a href='http://www.dxinfocentre.com/maritimesafetyinfo.htm' target='_blank'><b>William Hepburn's HF List</b></a> ]</td></tr></table>\n";
  }
  if ($type_HAMBCN) {
    $out.=
     "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting Ham Beacons</h2><br>Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:<br>\n"
	."[ <a href='http://www.lwca.org/sitepage/part15' target='_blank'><b>LOWFERS</b></a> |  <a href='http://www.keele.ac.uk/depts/por/28.htm' target='_blank'><b>HF</b></a> | <a href='http://www.keele.ac.uk/depts/por/50.htm' target='_blank'><b>50MHz</b></a> ]</td></tr></table>\n";
  }
  if ($type_DGPS) {
    $out.=
     "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting DGPS Stations</h2><br>Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:<br>\n"
	."[ <a href='http://www.ndblist.info/dgnavinfo/datamodes/worldDGPSdatabase.pdf' target='_blank'><b>NDB List PDF (by Frequency)</b></a> | <a href='http://www.navcen.uscg.gov/?pageName=dgpsSiteInfo&All' target='_blank'><b>USCG DGPS Site List</b></a> ]</td></tr></table>\n";
  }
  $out.=
	 "<table cellpadding='2' border='0' cellspacing='1'>\n"
	."  <tr>\n"
	."    <td align='center' valign='top' colspan='2'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
	."      <tr>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Customise Report</td>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."      </tr>\n"
	."    </table>\n"
	."    <table cellpadding='0' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm'>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Show</th>\n"
	."        <td nowrap>".show_page_bar($total, $limit, $offset, 1,1,1)."</td>\n"
	."      </tr>"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Types&nbsp;</th>\n"
	."        <td nowrap style='padding: 0px;'><table cellpadding='0' cellspacing='1' border='0' width='100%' class='tableForm'>\n"
	."          <tr>\n"
	."            <td bgcolor='".Signal::$colors[DGPS]."' width='14%' nowrap onclick='toggle(document.form.type_DGPS)'><input type='checkbox' onclick='toggle(document.form.type_DGPS);' name='type_DGPS' value='1'".($type_DGPS? " checked" : "").">DGPS</td>"
	."            <td bgcolor='".Signal::$colors[DSC]."' width='14%' nowrap onclick='toggle(document.form.type_DSC)'><input type='checkbox' onclick='toggle(document.form.type_DSC);' name='type_DSC' value='1'".($type_DSC? " checked" : "").">DSC</td>"
	."            <td bgcolor='".Signal::$colors[HAMBCN]."' width='14%' nowrap onclick='toggle(document.form.type_HAMBCN)'><input type='checkbox' onclick='toggle(document.form.type_HAMBCN)' name='type_HAMBCN' value='1'".($type_HAMBCN ? " checked" : "").">Ham</td>"
	."            <td bgcolor='".Signal::$colors[NAVTEX]."' width='15%' nowrap onclick='toggle(document.form.type_NAVTEX)'><input type='checkbox' onclick='toggle(document.form.type_NAVTEX)' name='type_NAVTEX' value='1'".($type_NAVTEX ? " checked" : "").">NAVTEX&nbsp;</td>"
	."            <td bgcolor='".Signal::$colors[NDB]."' width='14%' nowrap onclick='toggle(document.form.type_NDB)'><input type='checkbox' onclick='toggle(document.form.type_NDB)' name='type_NDB' value='1'".($type_NDB? " checked" : "").">NDB</td>"
	."            <td bgcolor='".Signal::$colors[TIME]."' width='14%' nowrap onclick='toggle(document.form.type_TIME)'><input type='checkbox' onclick='toggle(document.form.type_TIME)' name='type_TIME' value='1'".($type_TIME? " checked" : "").">Time</td>"
	."            <td bgcolor='".Signal::$colors[OTHER]."' width='15%' nowrap onclick='toggle(document.form.type_OTHER)'><input type='checkbox' onclick='toggle(document.form.type_OTHER)' name='type_OTHER' value='1'".($type_OTHER ? " checked" : "").">Other</td>"
	."          </tr>\n"
	."        </table></td>"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Frequencies&nbsp;</th>\n"
	."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
	."          <tr>\n"
	."            <td><input title='Lowest frequency (or leave blank)' type='text' name='filter_khz_1' size='6' maxlength='9' value='".($filter_khz_1 !="0" ? $filter_khz_1 : "")."' class='formfield'> - <input title='Highest frequency (or leave bank)' type='text' name='filter_khz_2' size='6' maxlength='9' value='".($filter_khz_2 != 1000000 ? $filter_khz_2 : "")."' class='formfield'> KHz</td>\n"
	."            <td>Channels</td>\n"
	."            <td><select name='filter_channels' class='formField'>\n"
	."<option value=''".($filter_channels=='' ? ' selected' : '').">All</option>\n"
	."<option value='1'".($filter_channels=='1' ? ' selected' : '').">Only 1 KHz</option>\n"
	."<option value='2'".($filter_channels=='2' ? ' selected' : '').">Not 1 KHz</option>\n"
	."</select></td>\n"
	."            <td align='right'><span title='Callsign or DGPS ID (Exact matches are shown at the top of the report, partial matches are shown later)'><b>Call / ID</b></span> <input type='text' name='filter_id' size='6' maxlength='12' value='$filter_id' class='formfield' title='Limit results to signals with this ID or partial ID -\nuse _ to indicate a wildcard character'></td>"
	."          </tr>\n"
	."        </table></td>"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Locations&nbsp;</th>\n"
	."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
	."          <tr>\n"
	."            <td nowrap>&nbsp;<span title='List of States or Provinces'><a href='".system_URL."/show_sp' onclick='show_sp();return false' title='NDBList State and Province codes'><b>States</b></a></span> <input title='Enter one or more states or provinces (e.g. MI or NB) to show only signals physically located there' type='text' name='filter_sp' size='20' value='$filter_sp' class='formfield'></td>\n"
	."            <td nowrap align='right'><span title='List of Countries'><a href='".system_URL."/show_itu' onclick='show_itu();return false' title='NDBList Country codes'>&nbsp;<b>Countries</b></a></span> <input title='Enter one or more NDBList approved 3-letter country codes (e.g. CAN or BRA) to show only signals physically located there' type='text' name='filter_itu' size='20' value='$filter_itu' class='formfield'></td>"
	."          </tr>\n"
	."	 </table></td>"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Range</th>\n"
	."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
	."          <tr>\n"
	."            <td>&nbsp;<b>From GSQ</b> <input title='Enter a grid square to show only signals physically located between the distances indicated' type='text' name='filter_dx_gsq' size='6' maxlength='6' value='$filter_dx_gsq' class='formfield' onKeyUp='set_range(form)' onchange='set_range(form)'></td>"
	."            <td><b><span title='Distance'>DX</span></b> <input title='Enter a value to show only signals equal or greater to this distance' type='text' name='filter_dx_min' size='5' maxlength='5' value='$filter_dx_min' onKeyUp='set_range(form)' onchange='set_range(form)'".($filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")."> - "
	."<input title='Enter a value to show only signals up to this distance' type='text' name='filter_dx_max' size='5' maxlength='5' value='$filter_dx_max' onKeyUp='set_range(form)' onchange='set_range(form)'".($filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")."></td>"
	."            <td width='45'><label for='filter_dx_units_km'><input type='radio' id='filter_dx_units_km' name='filter_dx_units' value='km'".($filter_dx_units=="km" ? " checked" : "").($filter_dx_gsq && ($filter_dx_min || $filter_dx_max) ? "" : " disabled").">km</label></td>"
	."            <td width='55'><label for='filter_dx_units_miles'><input type='radio' id='filter_dx_units_miles' name='filter_dx_units' value='miles'".($filter_dx_units=="miles" ? " checked" : "").($filter_dx_gsq && ($filter_dx_min || $filter_dx_max) ? "" : " disabled").">miles&nbsp;</label></td>"
	."          </tr>\n"
	."	 </table></td>"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left' valign='top'><span title='Only signals heard by the selected listener'>Heard by<br><br><span style='font-weight: normal;'>Use SHIFT or <br>CONTROL to<br>select multiple<br>values</span></span></th>"
	."        <td><select name='listenerID[]' multiple class='formfield' onchange='set_listener_and_heard_in(document.form)' style='font-family: monospace; width: 425; height: 90px;' >\n"
	.get_listener_options_list($filter_listener_SQL,$listenerID,"Anyone (or enter values in \"Heard here\" box)")
	."</select></td>\n"
	."      </tr>\n";
  if (system=="RWW") {
    $out.=
	 "     <tr class='rowForm'>\n"
	."       <th align='left'>Heard in&nbsp;</th>\n"
	."       <td>\n"
	."<select name='region' onchange='document.form.go.disabled=1;document.form.submit()' class='formField' style='width: 100%;'>\n"
	.get_region_options_list($region,"(All Continents)")
	."</select>"
	."</td>"
	."      </tr>\n";
  }
  $out.=
	 "      <tr class='rowForm'>\n"
	."        <th align='left'><span title='Only signals heard in these states and countries'>Heard here</span></th>\n"
	."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
	."          <tr>\n"
	."            <td title='Separate multiple options using spaces' nowrap>\n"
	."<input type='text' name='filter_heard' size='41' value='".($filter_heard ? strToUpper($filter_heard) : "(All States and Countries)")."'\n"
	.($filter_heard=="" ? "style='color: #0000ff' ":"")
	."onclick=\"if(this.value=='(All States and Countries)') { this.value=''; this.style.color='#000000'}\"\n"
	."onblur=\"if(this.value=='') { this.value='(All States and Countries)'; this.style.color='#0000ff';}\"\n"
	."onchange='set_listener_and_heard_in(form)' onKeyUp='set_listener_and_heard_in(form)' ".($listenerID ? "class='formfield_disabled' disabled" : "class='formfield'").">"
	."            <td width='45'><label for='radio_grp_any' title='Show where any terms match'><input id='radio_grp_any' type='radio' value='any' name='grp'".($grp!="all" ? " checked" : "").($listenerID || !$filter_heard ? " disabled" : "").">Any</label></td>\n"
	."            <td width='55'><label for='radio_grp_all' title='Show where all terms match'><input id='radio_grp_all' type='radio' value='all' name='grp'".($grp=="all" ? " checked" : "").($listenerID || !$filter_heard ? " disabled" : "").">All</label></td>\n"
	."          </tr>\n"
	."	 </table></td>"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Last Heard</th>\n"
	."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
	."          <tr>\n"
	."            <td><input title='Enter a start date to show only signals last heard after this date (YYYY-MM-DD format)' type='text' name='filter_date_1' size='10' maxlength='10' value='".($filter_date_1 != "1900-01-01" ? $filter_date_1 : "")."' class='formfield'> -\n"
	."<input title='Enter an end date to show only signals last heard before this date (YYYY-MM-DD format)' type='text' name='filter_date_2' size='10' maxlength='10' value='".($filter_date_2 != "2020-01-01" ? $filter_date_2 : "")."' class='formfield'></td>"
	."            <td align='right'><b>Offsets</b> <select name='offsets' class='formField'>\n"
	."<option value=''".($offsets=="" ? " selected" : "") .">Relative</option>\n"
	."<option value='abs'".($offsets=="" ? "" : " selected") .">Absolute</option>\n"
	."</select></td>"
	."          </tr>\n"
	."	 </table></td>"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Sort By</th>\n"
	."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
	."          <tr>\n"
	."            <td><select name='sortBy_column' class='fixed'>\n"
	."<option value='khz'".($sortBy=="khz" || $sortBy=="khz_d" ? " selected" : "") .">KHz - Nominal carrier</option>\n"
	."<option value='call'".($sortBy=="call" || $sortBy=="call_d" ? " selected" : "") .">ID &nbsp;- Callsign or ID</option>\n"
	."<option value='LSB'".($sortBy=="LSB" || $sortBy=="LSB_d" ? " selected" : "") .">LSB - Offset value in Hz</option>\n"
	."<option value='USB'".($sortBy=="USB" || $sortBy=="USB_d" ? " selected" : "") .">USB - Offset value in Hz</option>\n"
	."<option value='sec'".($sortBy=="sec" || $sortBy=="sec_d" ? " selected" : "") .">Sec - Cycle time in sec</option>\n"
	."<option value='format'".($sortBy=="format" || $sortBy=="format_d" ? " selected" : "") .">Fmt - Signal Format</option>\n"
	."<option value='QTH'".($sortBy=="QTH" || $sortBy=="QTH_d" ? " selected" : "") .">QTH - 'Name' and location</option>\n"
	."<option value='sp'".($sortBy=="sp" || $sortBy=="sp_d" ? " selected" : "") .">S/P - State or Province</option>\n"
	."<option value='itu'".($sortBy=="itu" || $sortBy=="itu_d" ? " selected" : "") .">ITU - Country code</option>\n"
	."<option value='gsq'".($sortBy=="gsq" || $sortBy=="gsq_d" ? " selected" : "") .">GSQ - Grid Square</option>\n"
	."<option value='pwr'".($sortBy=="pwr" || $sortBy=="pwr_d" ? " selected" : "") .">PWR - TX power in watts</option>\n"
	."<option value='notes'".($sortBy=="notes" || $sortBy=="notes_d" ? " selected" : "") .">Notes column</option>\n"
	."<option value='heard_in'".($sortBy=="heard_in" || $sortBy=="heard_in_d" ? " selected" : "") .">Heard In column</option>\n"
	."<option value='logs'".($sortBy=="logs" || $sortBy=="logs_d" ? " selected" : "") .">Logs - Number of loggings</option>\n"
	."<option value='last_heard'".($sortBy=="last_heard" || $sortBy=="last_heard_d" ? " selected" : "") .">Date last heard</option>\n"
	."<option value='CLE64'".($sortBy=="CLE64" || $sortBy=="CLE64_d" ? " selected" : "") ." style='color: #ff0000;'>CLE64 - First letter / DX</option>\n"
	."</select></td>"
	."            <td width='45'><label for='sortBy_d'><input type='checkbox' id='sortBy_d' name='sortBy_d' value='_d'".(substr($sortBy,strlen($sortBy)-2,2)=="_d" ? " checked" : "").">Z-A</label></td>"
	."            <td align='right'><label for='chk_filter_active'><input id='chk_filter_active' type='checkbox' name='filter_active' value='1'".($filter_active ? " checked" : "").">Only active&nbsp;</label></td>"
	."          </tr>\n"
	."	 </table></td>"
	."      </tr>\n"
	.(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
	   "      <tr class='rowForm'>\n"
	  ."        <th align='left'>Admin:</th>\n"
	  ."        <td nowrap><select name='filter_system' class='formField'>\n"
	  ."<option value='1'".($filter_system=='1' ? " selected" : "").">RNA</option>\n"
	  ."<option value='2'".($filter_system=='2' ? " selected" : "").">REU</option>\n"
	  ."<option value='3'".($filter_system=='3' ? " selected" : "").">RWW</option>\n"
	  ."<option value='not_logged'".($filter_system=='not_logged' ? " selected" : "").">Unlogged signals</option>\n"
	  ."<option value='all'".($filter_system=='all' ? " selected" : "").">Show everything</option>\n"
	  ."</select> Select system</td>\n"
	  ."      </tr>"
	 : ""
	)
    ."      <tr class='rowForm'>\n"
    ."        <th align='left'>Custom Filter:</th>\n"
    ."        <td nowrap><select name='filter_custom' class='formField'>\n"
	."<option value=''".($filter_custom=='' ? " selected" : "").">(None)</option>\n"
	."<option value='cle160'".($filter_custom=='cle160' ? " selected" : "").">CLE160</option>\n"
	."</select></td>\n"
	."      </tr>"
	."      <tr class='rowForm noprint'>\n"
	."        <th colspan='2'><input type='submit' onclick='return send_form(form)' name='go' value='Go' style='width: 100px;' class='formButton' title='Execute search'>\n"
	."<input name='clear' type='button' class='formButton' value='Clear' style='width: 100px;' onclick='clear_signal_list(document.form)'></th>"
	."      </tr>\n"
	."    </table>"
	."    </td>"
	."    <td>&nbsp;</td>\n"
	."    <td align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
	."      <tr>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Signals</td>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."      </tr>\n"
	."    </table>\n"
	."    <table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm' width='100%'>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>RNA only</th>\n"
	."        <td align='right'>".$RNA."</td>\n"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>REU only</th>\n"
	."        <td align='right'>".$REU."</td>\n"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>RNA + REU</th>\n"
	."        <td align='right'>".$both."</td>\n"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>RWW</th>\n"
	."        <td align='right'>".$RWW."</td>\n"
	."      </tr>\n"
	.(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
	   "      <tr class='rowForm'>\n"
	  ."        <th align='left'>Unassigned signals</th>\n"
	  ."        <td align='right'>".$unassigned."</td>\n"
	  ."      </tr>\n"
	 : ""
	)
	."    </table><br>\n"
	."    <table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
	."      <tr>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."        <td width='100%' class='downloadTableHeadings_nosort' align='center' nowrap>".system." Listeners</td>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."      </tr>\n"
	."    </table>\n"
	."    <table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm' width='100%'>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Locations</th>\n"
	."        <td align='right'>".$listeners."</td>\n"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Loggings</th>\n"
	."        <td align='right'>".$logs."</td>\n"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>First log</th>\n"
	."        <td align='right'>".$first."</td>\n"
	."      </tr>\n"
	."      <tr class='rowForm'>\n"
	."        <th align='left'>Last log</th>\n"
	."        <td align='right'>".$last."</td>\n"
	."      </tr>\n"
	."    </table></td>"
	."    <td>&nbsp;</td>\n"
	."    <td align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
	."      <tr>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."        <td width='100%' class='downloadTableHeadings_nosort' align='center' nowrap>Poll - Vote Now</td>\n"
	."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
	."      </tr>\n"
	."    </table>\n"
	."    <table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm' width='100%'>\n"
	."      <tr class='rowForm'>\n"
	."        <td align='left'>".doNow()."</td>\n"
	."      </tr>\n"
	."    </table><br>\n"
	."  </tr>"
	."</table></form><br>\n";

  if (mysql_num_rows($result)) {
    if ($sortBy=='CLE64') {
      $out.=	"<table cellpadding='0' cellspacing='0' border='0'><tr><td><ul><li><b><font color='#ff0000'>CLE64 Custom sort order applied:</font></b><br> - Show <b>active</b> beacons first<br> - Sort by <b>first letter</b> of callsign: <b>A-Z</b><br> - Sort by <b>DX</b> from Grid Square <b>$filter_dx_gsq</b>.<br><b>Tip:</b> You can further <b>refine this search</b> by entering values in 'Heard here', 'Heard by' or adding other criteria such as range limits.</li></ul></td></tr></table>\n";
    }
    if ($sortBy=='CLE64_d') {
      $out.=	"<table cellpadding='0' cellspacing='0' border='0'><tr><td><ul><li><b><font color='#ff0000'>CLE64 Custom sort order applied:</font></b><br> - Show <b>active</b> beacons first<br> - Sort by <b>first letter</b> of callsign: <b>Z-A</b><br> - Sort by <b>DX</b> from Grid Square <b>$filter_dx_gsq</b>.<br><b>Tip:</b> You can further <b>refine this search</b> by entering values in 'Heard here', 'Heard by' or adding other criteria such as range limits.</li></ul></td></tr></table>\n";
    }
    if ($filter_id) {
      $out.=	"<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>Note:</b> Any exact matches for <b>$filter_id</b> will shown at the top of this list, regardless of the station's current status.</td></tr></table>\n";
    }
    $out.=
	 "<table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' bgcolor='#ffffff' class='downloadtable'>\n"
	."  <thead>\n"
	."  <tr id=\"header\">\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='khz".($sortBy=="khz" ? "_d" : "")."';document.form.submit()\" title=\"Sort by Frequency\">KHz ".($sortBy=='khz' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='khz_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='call".($sortBy=="call" ? "_d" : "")."';document.form.submit()\" title=\"Sort by Callign or DGPS Station ID\">ID ".($sortBy=='call' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='call_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	.($type_NDB ?
	   "    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='LSB".($sortBy=="LSB" ? "_d" : "")."';document.form.submit()\" title=\"Sort by LSB (-ve Offset)\">LSB ".($sortBy=='LSB' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='LSB_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	  ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='USB".($sortBy=="USB" ? "_d" : "")."';document.form.submit()\" title=\"Sort by USB (+ve Offset)\">USB ".($sortBy=='USB' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='USB_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	  ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='sec".($sortBy=="sec" ? "_d" : "")."';document.form.submit()\" title=\"Sort by cycle duration\">Sec ".($sortBy=='sec' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='sec_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	  ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='format".($sortBy=="format" ? "_d" : "")."';document.form.submit()\" title=\"Sort by cycle format\">Fmt ".($sortBy=='format' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='format_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	 : ""
	)
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='QTH".($sortBy=="QTH" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Name' and Location\">'Name' and Location ".($sortBy=='QTH' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='QTH_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='sp".($sortBy=="sp" ? "_d" : "")."';document.form.submit()\" title=\"Sort by State / Province\">S/P ".($sortBy=='sp' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='sp_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='itu".($sortBy=="itu" ? "_d" : "")."';document.form.submit()\" title=\"Sort by NDB List Country Code\">ITU ".($sortBy=='itu' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='itu_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='gsq".($sortBy=="gsq" ? "_d" : "")."';document.form.submit()\" title=\"Sort by GSQ Grid Locator Square\">GSQ ".($sortBy=='gsq' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='gsq_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="pwr_d" ? "pwr" : "pwr_d")."';document.form.submit()\" title=\"Sort by Transmitter Power\">PWR ".($sortBy=='pwr' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='pwr_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='notes".($sortBy=="notes" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Notes' column\">Notes ".($sortBy=='notes' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='notes_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='heard_in".($sortBy=="heard_in" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Heard In' column\">Heard In <span style='font-weight: normal'>(Click for Map - <b>bold</b> = daytime logging)</span>".($sortBy=='heard_in' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='heard_in_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='logs".($sortBy=="logs" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Logs' column\" nowrap>Logs ".($sortBy=='logs' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='logs_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
	."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='last_heard".($sortBy=="last_heard" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Last Heard' column (YYYY-MM-DD)\" nowrap>Last Heard ".($sortBy=='last_heard' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='last_heard_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";

    if ($listenerID) {
      $out.=	"    <th class='downloadTableHeadings_nosort' colspan='2'>Range from<br>Listener</th>\n";
    }
    if ($filter_dx_gsq) {
      $out.=	"    <th class='downloadTableHeadings_nosort' colspan='3'>Range from<br>GSQ</th>\n";
    }
    if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
      $out.=	"    <th class='downloadTableHeadings_nosort' rowspan='2' valign='bottom'>&nbsp;</th>\n";
    }
    if ($listenerID || $filter_dx_gsq) {
      $out.=	"  <tr>\n";
      if ($listenerID) {
        $out.=
          "    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="dx_d" ? "dx" : "dx_d")."';document.form.submit()\" title=\"Sort by 'KM' column\"  nowrap>KM ".($sortBy=='dx' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='dx_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
		 ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="dx_d" ? "dx" : "dx_d")."';document.form.submit()\" title=\"Sort by 'Miles' column\" nowrap>Miles ".($sortBy=='dx' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='dx_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
//		 ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="dx_deg" ? "dx_deg_d" : "dx_deg")."';document.form.submit()\" title=\"Sort by 'Degrees' column\" nowrap>Deg ".($sortBy=='dx_deg' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='dx_deg_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>": "")."</th>\n"
         ;
      }
      if ($filter_dx_gsq) {
        $out.=
          "    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="range_dx_km_d" ? "range_dx_km" : "range_dx_km_d")."';document.form.submit()\" title=\"Sort by 'Range KM' column\" nowrap>KM ".($sortBy=='range_dx_km' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='range_dx_km_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
		 ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="range_dx_km_d" ? "range_dx_km" : "range_dx_km_d")."';document.form.submit()\" title=\"Sort by 'Range Miles' column\" nowrap>Miles ".($sortBy=='range_dx_km' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='range_dx_km_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
		 ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="range_dx_deg" ? "range_dx_deg_d" : "range_dx_deg")."';document.form.submit()\" title=\"Sort by 'Degrees' column\" nowrap>Deg ".($sortBy=='range_dx_deg' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='range_dx_deg_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
      }
      $out.=		"  </tr>\n";
    }
    $out.=
	 "  </tr>\n"
	."  </thead>\n"
	."  <tbody>";
    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
      if (isset($filter_by_dx) && $filter_by_dx) {
        $dx =		get_dx($filter_by_lat,$filter_by_lon,$row["lat"],$row["lon"]);
      }
      if (!$row["active"]) {
        $class='inactive';
        $title = '(Reportedly off air or decommissioned)';
      }
      else {
        switch ($row["type"]) {
          case NDB:	    $class='ndb';		$title = 'NDB';                     break;
          case DGPS:	$class='dgps';		$title = 'DGPS Station';            break;
          case DSC:	    $class='dsc';		$title = 'DSC Station';             break;
          case TIME:	$class='time';		$title = 'Time Signal Station';     break;
          case NAVTEX:	$class='navtex';	$title = 'NAVTEX Station';          break;
          case HAMBCN:	$class='hambcn';	$title = 'Amateur signal';          break;
          case OTHER:	$class='other';		$title = 'Other Utility Station';   break;
        }
      }
      $call =	($filter_id ? highlight($row["call"],$filter_id) : $row["call"]);
      $heard_in = ($filter_heard ? highlight($row["heard_in_html"],str_replace(" ","|",$filter_heard)) : $row["heard_in_html"]);
      $SP =	$row["SP"];
      $ITU =	($filter_itu ? highlight($row["ITU"],str_replace(" ","|",$filter_itu)) : $row["ITU"]);
      $out.=
	 "<tr class='rownormal ".$class."' title='".$title."'>"
	."<td><a href='".system_URL."/signal_list?filter_khz_1=".(float)$row["khz"]."&amp;filter_khz_2=".(float)$row["khz"]."&amp;limit=-1' title='Filter on this value'>".(float)$row["khz"]."</a></td>\n"
	."<td><a onmouseover='window.status=\"View profile for ".(float)$row["khz"]."-".$row["call"]."\";return true;' onmouseout='window.status=\"\";return true;' href=\"".system_URL."/".$row["ID"]."\" onclick=\"signal_info('".$row["ID"]."');return false\"><b>$call</b></a></td>\n";
      if ($type_NDB) {
        $out.=
	   "<td align='right'>".$row["LSB_approx"].($row["LSB"]<>"" ? ($offsets=="" ? $row["LSB"] : number_format((float) ($row["khz"]-($row["LSB"]/1000)),3,'.','')): "&nbsp;")."</td>\n"
	  ."<td align='right'>".$row["USB_approx"].($row["USB"]<>"" ? ($offsets=="" ? $row["USB"] : number_format((float) ($row["khz"]+($row["USB"]/1000)),3,'.','')): "&nbsp;")."</td>\n"
	  ."<td>".($row["sec"] ? $row["sec"] : "&nbsp;")."</td>\n"
	  ."<td>".($row["format"] ? stripslashes($row["format"]) : "&nbsp;")."</td>\n";
      }
      $out.=
	 "<td>".($row["QTH"] ? get_sp_maplinks($SP,$row['ID'],$row["QTH"]) : "&nbsp;")."</td>\n"
	."<td>".($SP ? "<a href='".system_URL."/signal_list?filter_sp=".$row["SP"]."' title='Filter on this value'>".$SP."</a>" : "&nbsp;")."</td>\n"
	."<td>".($ITU ? "<a href='".system_URL."/signal_list?filter_itu=".$row["ITU"]."' title='Filter on this value'>".$ITU."</a>" : "&nbsp;")."</td>\n"
	."<td>".($row["GSQ"] ? "<a href='.' onclick='popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\");return false;' title='Show map (accuracy limited to nearest Grid Square)'><span class='fixed'>".$row["GSQ"]."</span></a>" : "&nbsp;")."</td>\n"
	."<td>".($row["pwr"] ? $row["pwr"] : "&nbsp;")."</td>\n"
	."<td>".($row["notes"] ? stripslashes($row["notes"]) : "&nbsp;")."</td>\n"
	."<td>".($heard_in ? $heard_in : "&nbsp;")."</td>\n"
	."<td align='right'>"
    .($row["logs"] ? "<a href=\"".system_URL."/signal_log/".$row["ID"]."\" onclick='signal_log(\"".$row["ID"]."\");return false;'><b>".$row["logs"]."</b></a>" : "&nbsp;")."</td>\n"
	."<td>".($row["last_heard"]!="0000-00-00" ? $row["last_heard"] : "&nbsp;")."</td>\n";

      if ($listenerID) {
        $out.=
	   "<td align='right'>".($row["dx_km"]!=='' ? $row["dx_km"] : "&nbsp;")."</td>\n"
	  ."<td align='right'>".($row["dx_miles"]!=='' ? $row["dx_miles"] : "&nbsp;")."</td>\n"
//	  ."<td align='right'>".($row["dx_deg"] ? $row["dx_deg"] : "&nbsp;")."</td>\n"
      ;
      }

      if ($filter_dx_gsq) {
        $out.=
	   "<td align='right'>".($row["range_dx_km"]!=='' ? round($row["range_dx_km"]) : "&nbsp;")."</td>\n"
	  ."<td align='right'>".($row["range_dx_miles"]!=='' ? round($row["range_dx_miles"]) : "&nbsp;")."</td>\n"
	  ."<td align='right'>".($row["range_dx_deg"]!=='' ? round($row["range_dx_deg"]) : "&nbsp;")."</td>\n";
      }

      if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
        $out.=
	   "<td nowrap><a href='javascript: if (confirm(\"CONFIRM\\n\\nAre you sure you wish to delete this signal and\\nall associated logs?\")) { document.form.submode.value=\"delete\"; document.form.targetID.value=\"".$row["ID"]."\"; document.form.submit();}'>Del</a>\n"
	  ."<a href='javascript:signal_merge(".$row["ID"].")'>Merge</a></td>\n";
      }
    }
    $out.=	 "  </tr>"
		."</tbody>"
		."</table>\n"
		."<br>\n"
		."<span class='noscreen'>\n"
		."<b><i>(End of printout)</i></b>\n"
		."</span>\n";
  }
  else {
    $out.=	"<h2>Results</h2><br><br><h3>No results for search criteria</h3><br><br><br>\n";
  }

  $out.=
	 "<span class='noprint'>\n"
	."<input type='button' value='Print...' onclick='".(($limit!=-1 && $limit<$total) ? "if (confirm(\"Information\\n\\nThis printout works best in Landscape.\\n\\nYou are not presently displaying all $total available records.\\nContinue anyway?\")) { window.print(); }": "window.print()")."' class='formbutton' style='width: 150px;'> ";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out.=	"<input type='button' class='formbutton' value='Add signal...' style='width: 150px' onclick='signal_add(document.form.filter_id.value,document.form.filter_khz_1.value,\"\",\"\",\"\",\"\",\"\",get_type(document.form))'> ";
  }
  $out.=
	 "<input type='button' class='formbutton' value='All ".system." > Excel' style='width: 150px' title='Get the whole database as Excel' onclick='export_signallist_excel();'> "
	."<input type='button' class='formbutton' value='All ".system." > PDF'   style='width: 150px' title='get the whole database as PDF' onclick='export_signallist_pdf();'> "
	."<input type='button' class='formbutton' value='All ".system." > ILG'   style='width: 150px' title='get the whole database as ILGRadio format for Ham Radio Deluxe' onclick='if (confirm(\"EXPORT ENTIRE ".system." DATABASE TO IRGRadio Database format?\\n\\nThis can be a time consuming process - typically 5 minutes or more.\")) { show_ILG(); }'> "
	."</span>\n"
	."<script type='text/javascript'>document.form.filter_id.focus();document.form.filter_id.select();</script>\n";
  return $out;
}


// ************************************
// * signal_listeners()               *
// ************************************
function signal_listeners() {
  global $ID, $mode, $submode, $sortBy, $targetID;
  global $active, $call, $GSQ, $heard_in, $ITU, $khz, $last_heard, $LSB, $notes, $SP, $USB;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
      $ID = array_pop($path_arr);
    }
  }
  $call =	strToUpper(urldecode($call));
  $ITU =	strToUpper($ITU);
  $SP =		strToUpper($SP);
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    switch ($submode) {
      case "delete":
        log_delete($targetID);
      break;
    }
  }
  $Obj =      new Signal($ID);
  if ($ID) {
    $row =		$Obj->get_record();
    $active =		$row["active"];
    $call =		$row["call"];
    $GSQ =		$row["GSQ"];
    $heard_in =		$row["heard_in"];
    $ITU =		$row["ITU"];
    $khz =		$row["khz"];
    $logs =		$row["logs"];
    $LSB =		$row["LSB"];
    $notes =		stripslashes($row["notes"]);
    $QTH =		$row["QTH"];
    $SP =		$row["SP"];
    $USB =		$row["USB"];
  }

  if ($sortBy=="") {
    $sortBy = "name";
  }
  $sortBy_SQL =		"";
  switch ($sortBy) {
    case "dx":		$sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL,`dx_km` ASC";	break;
    case "dx_d":	$sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL,`dx_km` DESC";	break;
    case "ITU":		$sortBy_SQL =	"`ITU` ASC, `SP` ASC, `QTH` ASC";		break;
    case "ITU_d":	$sortBy_SQL =	"`ITU` DESC, `SP` ASC, `QTH` ASC";		break;
    case "logs":	$sortBy_SQL =	"`logs` ASC";					break;
    case "logs_d":	$sortBy_SQL =	"`logs` DESC";					break;
    case "name":	$sortBy_SQL =	"`listeners`.`name` ASC";			break;
    case "name_d":	$sortBy_SQL =	"`listeners`.`name` DESC";			break;
    case "SP":		$sortBy_SQL =	"`SP` ASC, `QTH` ASC";				break;
    case "SP_d":	$sortBy_SQL =	"`SP` DESC, `QTH` ASC";				break;
    case "QTH":		$sortBy_SQL =	"`QTH` ASC";					break;
    case "QTH_d":	$sortBy_SQL =	"`QTH` DESC";					break;
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

  $result =	mysql_query($sql);
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
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out.=
       "            <form action='".system_URL.'/'.$mode."' name='form' method='POST' onsubmit='if (window.opener) { window.opener.location.reload(1)};return true;'>\n"
      ."            <input type='hidden' name='ID' value='$ID'>\n"
      ."            <input type='hidden' name='mode' value='$mode'>\n"
      ."            <input type='hidden' name='submode' value=''>\n";
  }
  if (mysql_num_rows($result)) {
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
    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
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
  $out.=	"</form>\n";
  return $out;
}


// ************************************
// * signal_log()                     *
// ************************************
function signal_log() {
  global $ID, $mode, $submode, $sortBy, $targetID;
  global $active, $call, $GSQ, $heard_in, $ITU, $khz, $last_heard, $LSB, $notes, $SP, $USB;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
      $ID = array_pop($path_arr);
    }
  }
  $call =	strToUpper(urldecode($call));
  $ITU =	strToUpper($ITU);
  $SP =		strToUpper($SP);
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    switch ($submode) {
      case "delete":
        log_delete($targetID);
      break;
    }
  }
  $Obj =      new Signal($ID);
  if ($ID) {
    $row =		$Obj->get_record();
    $active =	$row["active"];
    $call =		$row["call"];
    $GSQ =		$row["GSQ"];
    $heard_in =	$row["heard_in"];
    $ITU =		$row["ITU"];
    $khz =		$row["khz"];
    $logs =		$row["logs"];
    $LSB =		$row["LSB"];
    $notes =	stripslashes($row["notes"]);
    $QTH =		$row["QTH"];
    $SP =		$row["SP"];
    $USB =		$row["USB"];
  }
  if ($sortBy=="") {
    $sortBy = "date";
  }
  $sortBy_SQL =		"";
  switch ($sortBy) {
    case "date":	    $sortBy_SQL =	"`date` DESC, `time` DESC";			break;
    case "date_d":	    $sortBy_SQL =	"`date` ASC, `time` ASC";			break;
    case "dx":		    $sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL,`dx_km` ASC";	break;
    case "dx_d":	    $sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL,`dx_km` DESC";	break;
    case "format":	    $sortBy_SQL =	"`format`='' OR `format` IS NULL,`format` ASC";	break;
    case "format_d":    $sortBy_SQL =	"`format`='' OR `format` IS NULL,`format` DESC";break;
    case "gsq":		    $sortBy_SQL =	"`GSQ`='' OR `GSQ` IS NULL,`GSQ` ASC";		break;
    case "gsq_d":	    $sortBy_SQL =	"`GSQ`='' OR `GSQ` IS NULL,`GSQ` DESC";		break;
    case "ID":		    $sortBy_SQL =	"`logs`.`ID` ASC";				break;
    case "ID_d":	    $sortBy_SQL =	"`logs`.`ID` DESC";				break;
    case "itu":		    $sortBy_SQL =	"`ITU` ASC, `signals`.`SP` ASC, `khz` ASC, `call` ASC";	break;
    case "itu_d":	    $sortBy_SQL =	"`ITU` DESC, `signals`.`SP` ASC, `khz` ASC, `call` ASC";break;
    case "khz":		    $sortBy_SQL =	"`khz` ASC, `call` ASC";			break;
    case "khz_d":	    $sortBy_SQL =	"`khz` DESC, `call` ASC";			break;
    case "heard_in":    $sortBy_SQL =	"`logs`.`heard_in` ASC";			break;
    case "heard_in_d":  $sortBy_SQL =	"`logs`.`heard_in` DESC";			break;
    case "sec":		    $sortBy_SQL =	"`sec`='' OR `sec` IS NULL,`sec` ASC";		break;
    case "sec_d":	    $sortBy_SQL =	"`sec`='' OR `sec` IS NULL,`sec` DESC";		break;
    case "time":	    $sortBy_SQL =	"`time` IS NULL,`time` ASC";			break;
    case "time_d":	    $sortBy_SQL =	"`time` IS NULL,`time` DESC";			break;
    case "listener":    $sortBy_SQL =	"`listeners`.`name` ASC";			break;
    case "listener_d":  $sortBy_SQL =	"`listeners`.`name` DESC";			break;
    case "LSB":		    $sortBy_SQL =	"`logs`.`LSB` IS NULL, `logs`.`LSB` ASC";	break;
    case "LSB_d":	    $sortBy_SQL =	"`logs`.`LSB` IS NULL, `logs`.`LSB` DESC";	break;
    case "USB":		    $sortBy_SQL =	"`logs`.`USB` IS NULL, `logs`.`USB` ASC";	break;
    case "USB_d":	    $sortBy_SQL =	"`logs`.`USB` IS NULL, `logs`.`USB` DESC";	break;
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
  $result =	mysql_query($sql);
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
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out.=
       "            <form action='".system_URL."/".$mode."' name='form' method='POST' onsubmit='if (window.opener) { window.opener.location.reload(1)};return true;'>\n"
      ."            <input type='hidden' name='ID' value='$ID'>\n"
      ."            <input type='hidden' name='mode' value='$mode'>\n"
      ."            <input type='hidden' name='submode' value=''>\n";
  }

  if (mysql_num_rows($result)) {
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
       .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
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
      .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
         "                    <th class='scroll_list'><small>Del</small></th>\n"
       :
         ""
       )
      ."                  </tr>\n"
      ."                  </thead>\n";
    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
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
        .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
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
  $out.=	"</form>\n";
//  $out[] =	"<pre>$sql</pre>";

  return $out;
}



// ************************************
// * signal_map_eu()                  *
// ************************************
function signal_map_eu() {
  global $ID,$mode;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
      $ID = array_pop($path_arr);
    }
  }
  $out =	array();
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
  $result =	mysql_query($sql);
  $name_idx =	0;
  $out[] =
     "<img usemap=\"#map\" galleryimg=\"no\""
    ." src=\"".system_URL."/generate_station_map/2/".$ID."\"\n"
    ." border=\"0\" alt=\"\" />\n"
    ."<map name=\"map\">\n";


  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
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
      }
      else {
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
  return implode($out,"");
}


// ************************************
// * signal_map_na()                  *
// ************************************
function signal_map_na() {
  global $ID,$mode;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
      $ID = array_pop($path_arr);
    }
  }
  $out =	array();
  $out[] =	"<img usemap=\"#map\" galleryimg=\"no\" src=\"".system_URL."/generate_station_map/1/".$ID."\" border=\"0\">\n";
  $out[] =	"<map name=\"map\">\n";
  $sql =	 "SELECT\n"
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

  $result =	mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $total_rx =	$row['listeners'];


  $sql =	"SELECT DISTINCT\n"
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

  $result =	@mysql_query($sql);
  $name_idx =	0;
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
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
      }
      else {
        $out[] = "<area shape=\"circle\""
		." title=\"".$row['name']." (No)\""
		." href=\"javascript:listener_edit(".$row['listenerID'].")\""
		."coords=\"".$row['map_x'].",".$row['map_y'].",".($row['primary_QTH'] ? "4" : "2")."\">\n";
      }
    }
  }
  $out[] =	 "</map>\n"
		."<script language='javascript' type='text/javascript'>\n"
		."document.write(\"<div ID='point_here' style='position: absolute; display: none;'><img src='".BASE_PATH."assets/map_point_here.gif'></div>\");\n"
		."document.write(\"<div ID='name_here' style='position: absolute; display: none;'><img src='".BASE_PATH."assets/map_name_here.gif'></div>\");\n"
		."</script>\n";

  return implode($out,"");
}



// ************************************
// * signal_merge()                   *
// ************************************
function signal_merge() {
  global $mode, $submode;
  global $ID, $destinationID;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
      $ID = array_pop($path_arr);
    }
  }
  $out =	array();
  $merged =	0;
  switch($submode) {
    case "merge":
      $sql =	"UPDATE `logs` SET `signalID` = ".addslashes($destinationID)." WHERE `signalID` = ".addslashes($ID);
      $result =	mysql_query($sql);
      $merged =	mysql_affected_rows();
      signal_update_heard_in($ID);

      $sql =	"select count(*) as `logs` from `logs` where `signalID` = $ID";
      $result =	mysql_query($sql);
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
      $sql =	"UPDATE `signals` SET `logs` = ".$row['logs']." WHERE `ID` = $ID";
      $result =	mysql_query($sql);

      signal_update_heard_in($destinationID);
      $sql =	"select count(*) as `logs` from `logs` where `signalID` = $destinationID";
      $result =	mysql_query($sql);
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
      $sql =	"UPDATE `signals` SET `logs` = ".$row['logs']." WHERE `ID` = $destinationID";
      $result =	mysql_query($sql);

    break;
  }

  $out[] =	"<h1>Signal Merge</h1><br>\n";
  $out[] =	"<p>This function moves <b>all</b> logs for the selected signal to another signal record. It should be used only to combine logs entered against duplicate records for the same signal. This operation is NOT reversible.</p>";
  $sql =	"SELECT * FROM `signals` WHERE `ID` = '".addslashes($ID)."'";
  $result =	mysql_query($sql);
  $row =	mysql_fetch_array($result, MYSQL_ASSOC);
  $out[] =	"<form action='".system_URL."/".$mode."/".$ID."?submode=merge' method='POST'>\n";
  $out[] =	"<h2>Source Signal:</h2><br>\n";
  $out[] =	"<p>".(float)$row['khz']."-".$row['call']."</p>\n";
  $out[] =	"<h2>Destination Signal:</h2><br>\n";

  if ($submode!="merge") {
    $out[] =	"<select name='destinationID' style='font-family: monospace;' class='formField'>\n";
    $sql =	"SELECT `ID`,`khz`,`call`,`SP`,`ITU` FROM `signals` ORDER BY `khz`,`call`,`ITU`,`SP`";
    $result =	@mysql_query($sql);
    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row =	mysql_fetch_array($result, MYSQL_ASSOC);
      $out[] =	"<option value='".$row['ID']."'".($ID==$row['ID'] ? " selected" : "").">".pad_nbsp((float)$row['khz'],10).pad_nbsp($row['call'],12)." ".pad_nbsp($row['SP'],3).$row['ITU']."</option>\n";
    }
    $out[] =	"</select>\n";
    $out[] =	"<input type='submit' value='Go' class='formButton'>\n";
  }
  else {
    $sql =	"SELECT * FROM `signals` WHERE `ID` = '".addslashes($destinationID)."'";
    $result =	mysql_query($sql);
    $row =	mysql_fetch_array($result, MYSQL_ASSOC);
    $out[] =	"<p>".(float)$row['khz']."-".$row['call']."</p>\n";
    $out[] =	"<h2>Result</h2><p>$merged log(s) were moved to the signal record given.";
    $out[] =	"<p align='center'><input type='button' value='Close' onClick='window.close();'></p>";
  }
  $out[] =	"</form>\n";
  return implode($out,"");
}



// ************************************
// * signal_QNH()                     *
// ************************************
function signal_QNH() {
  global $ID, $mode, $submode, $ICAO_signal, $hours_signal;
  if (!$ID){
    $path_arr = (explode('?',$_SERVER["REQUEST_URI"]));
    $path_arr = explode('/',$path_arr[0]);
    if ($path_arr[count($path_arr)-2]==$mode){
      $ID = array_pop($path_arr);
    }
  }
  $pressure =	array();

  $Obj =      new Signal($ID);
  $row =		$Obj->get_record();
  $call =	$row["call"];
  $GSQ =	$row["GSQ"];
  $ITU =	$row["ITU"];
  $khz =	(float)$row["khz"];
  $lat =	$row["lat"];
  $logs =	$row["logs"];
  $lon =	$row["lon"];
  $QTH =	$row["QTH"];
  $SP =		$row["SP"];


  if ($ICAO_signal && $hours_signal) {
    if ($METAR = METAR($ICAO_signal,$hours_signal,0)) {
      $sql =		"SELECT * FROM `icao` WHERE `icao` = \"$ICAO_signal\"";
      $result =		mysql_query($sql);
      $row =		mysql_fetch_array($result,MYSQL_ASSOC);
      $dx =		get_dx($lat,$lon,$row["lat"],$row["lon"]);
      $pressure =
        "QNH at $ICAO_signal - ".$row["name"].($row["SP"] ? ", ".$row["SP"] : "").", ".$row["CNT"]."\n"
        ."(".$dx[0]." miles from $khz-".$call.")\n"
        ."----------------------\n"
        ."DD UTC  MB     SLP \n"
        ."----------------------\n"
        .$METAR."\n"
        ."----------------------\n"
        ."(From ".system.")\n";
    }
    else {
      $pressure =	"QNH data not available at $ICAO_signal for last $hours_signal hours";
    }
  }
  else {
    $pressure = "HELP\nEnter hours to display, select first valid station in list and press 'QNH'";
  }
  $options = "";
  $icao_arr =	get_local_icao($GSQ,10,$ICAO_signal);
  for ($i=0; $i<10; $i++) {
    $options.=	"              <option value='".$icao_arr[$i]['ICAO']."'".($ICAO_signal==$icao_arr[$i]['ICAO'] ? " selected" : "").">".$icao_arr[$i]['ICAO']." (".$icao_arr[$i]['miles']." Miles, ".$icao_arr[$i]['km']." KM)</option>\n";
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


// ************************************
// * signal_seeklist()                *
// ************************************
function signal_seeklist() {
  global $mode, $submode, $paper, $createFor, $region, $targetID, $filter_active, $filter_custom, $filter_date_1, $filter_date_2;
  global $filter_dx_gsq, $filter_dx_max, $filter_dx_min, $filter_dx_units;
  global $filter_heard, $filter_id, $filter_system, $filter_khz_1, $filter_khz_2, $filter_channels;
  global $filter_sp, $filter_itu, $listenerID, $sortBy, $grp, $limit, $offset;
  global $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
// No array used as page size cause memory fault on linux box.   Just print.

//  $filter_sp = "ON";
//print "active=$filter_active";

  // If there's no valid listener data, blank it all out
  if (!($listenerID && is_array($listenerID) && count($listenerID) && $listenerID[0])) {
    $listenerID =	false;
  }

  if ($filter_heard=="(All States and Countries)") {
    $filter_heard="";
  }


  if ($filter_system=="") {
    switch (system) {
      case "RNA":	$filter_system=1;	break;
      case "REU":	$filter_system=2;	break;
      case "RWW":	$filter_system=3;	break;
    }
  }

  switch ($filter_system) {
    case "1":
      $filter_system_SQL =			"(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
      $filter_log_SQL =				"(`region` = 'na' OR `region` = 'ca' OR `heard_in` = 'hi')";
      $filter_listener_SQL =			"(`region` = 'na' OR `region` = 'ca' OR `SP` = 'hi')";
    break;
    case "2":
      $filter_system_SQL =			"(`heard_in_eu` = 1)";
      $filter_log_SQL =				"(`region` = 'eu')";
      $filter_listener_SQL =			"(`region` = 'eu')";
    break;
    case "3":
      if ($region!="") {
        $filter_system_SQL =			"(`heard_in_$region`=1)";
        $filter_listener_SQL =			"(`region` = '$region')";
        $filter_log_SQL =      			"(`region` = '$region')";
      }
      else {
        $filter_system_SQL =			 "(1)";
        $filter_listener_SQL =			"(1)";
        $filter_log_SQL =			"(1)";
      }
    break;
  }

  switch ($filter_custom){
    case 'cle160':
      $filter_custom_SQL =
         "`signals`.`ITU` IN('"
        .implode("','",explode(' ','ABW AFG AFS AGL AIA ALB ALG ALS AND ANI AOE ARG ARM ARS ASC ATA ATG ATN AUI AUS AUT AZE AZR BAH BAL BAR BDI BEL BEN BER BFA BGD BHR BIH BLR BLZ BOL BOT BRA BRB BRI BRM BRU BTN BUL CAB CAF CBG CEU CHL CHN CHR CKH CKS CLI CLM CLN CME CNR COD COG COM COR CPV CTI CTR CUB CVA CYM CYP CZE'))
        ."') OR `signals`.`SP` IN('"
        .implode("','",explode(' ','AB AK AL AR AT AZ BC CA CO CT'))
        ."')";
    break;
  }

  $sql =	"SELECT\n"
		."  DATE_FORMAT(MAX(`date`),'%e %b %Y') AS `last`\n"
		."FROM\n"
		."  `logs`\n"
		."WHERE\n"
		."  $filter_log_SQL";

//  print("<pre>$sql</pre>");
  $result = 	@mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  $last =	$row["last"];


  $filter_size =	270;
  $line_height =	14;
  $heading_height =	20;
  $heading_gap =	11;

  if (!$paper) {
    $paper =	"ltr";
  }
  switch ($paper) {
    case "ltr":
      $page_len =	710; // px length
      $page_cols =	5;
    break;
    case "lgl":
      $page_len =	906; // px length
      $page_cols =	5;
    break;
    case "a4":
      $page_len =	755; // px length
      $page_cols =	4;
    break;
    case "ltr_l":
      $page_len =	490; // px length
      $page_cols =	6;
    break;
    case "lgl_l":
      $page_len =	490; // px length
      $page_cols =	9;
    break;
    case "a4_l":
      $page_len =	470; // px length
      $page_cols =	7;
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
  $filter_type =	(count($filter_type) ? "(".implode($filter_type," OR ").")" : "");

  if ($filter_heard) {
    $tmp =		explode(" ",strToUpper($filter_heard));
    sort($tmp);
    $filter_heard =	implode(" ",$tmp);
  }

  // Filter on 'Heard in':
  $filter_heard_SQL =	explode(" ",strToUpper($filter_heard));
  if ($grp=="all") {
    $filter_heard_SQL =	"`signals`.`heard_in` LIKE '%".implode($filter_heard_SQL,"%' AND `signals`.`heard_in` LIKE '%")."%'";
  }
  else {
    $filter_heard_SQL =	"`logs`.`heard_in` = '".implode($filter_heard_SQL,"' OR `logs`.`heard_in` = '")."'";
  }
  // Filter on Frequencies:
  if ($filter_khz_1 || $filter_khz_2) {
    if ($filter_khz_1 == "") {
      $filter_khz_1 = 0;
    }
    if ($filter_khz_2 == "") {
      $filter_khz_2 = 100000;
    }
    $filter_khz_1 =	(float)$filter_khz_1;
    $filter_khz_2 =	(float)$filter_khz_2;
  }
  $filter_sp =		strToUpper($filter_sp);
  $filter_itu =		strToUpper($filter_itu);

  if ($filter_sp) {
    $tmp =		explode(" ",strToUpper($filter_sp));
    sort($tmp);
    $filter_sp =	implode(" ",$tmp);

    $filter_sp_SQL =	explode(" ",strToUpper($filter_sp));
    $filter_sp_SQL =	"`signals`.`SP` = '".implode($filter_sp_SQL,"' OR `signals`.`SP` = '")."'";
  }

  if ($filter_itu) {
    $tmp =		explode(" ",strToUpper($filter_itu));
    sort($tmp);
    $filter_itu =	implode(" ",$tmp);

    $filter_itu_SQL =	explode(" ",strToUpper($filter_itu));
    $filter_itu_SQL =	"`signals`.`ITU` = '".implode($filter_itu_SQL,"' OR `signals`.`ITU` = '")."'";
  }



  // Filter on Date Last Heard:
  if ($filter_date_1 || $filter_date_2) {
    if ($filter_date_1 == "") {
      $filter_date_1 = "1900-01-01";
    }
    if ($filter_date_2 == "") {
      $filter_date_2 = "2020-01-01";
    }
  }

  if (!isset($filter_dx_units)) {
    $filter_dx_units = "km";
  }

  $filter_by_range = false;
  if ($filter_dx_gsq && ($filter_dx_min || $filter_dx_max)) {
    $filter_dx_gsq = strtoUpper(substr($filter_dx_gsq,0,4)).strtoLower(substr($filter_dx_gsq,4,2));
    $filter_by_range =	true;
    $a = 		GSQ_deg($filter_dx_gsq);
    $filter_dx_lat =	$a["lat"];
    $filter_dx_lon =	$a["lon"];
  }

  if (!$filter_by_range && ($sortBy == "range" || $sortBy == "range_d")) {
    $sortBy =	"khz";
  }


  $sql =	 "SELECT DISTINCT\n"
            ."  `signals`.`ID`,\n"
            ."  `signals`.`active`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`type`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`\n"
		.($filter_heard || $listenerID ?
             "FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  (`signals`.`ID` = `logs`.`signalID`) AND\n"
            .($filter_heard ?   "  ($filter_heard_SQL) AND\n" : "")
            .($listenerID ?     "  (`logs`.`listenerID`=".implode($listenerID," OR `logs`.`listenerID`=").") AND\n" : "")
        :
             "FROM\n"
            ."  `signals`\n"
            ."WHERE\n"
        )
        ."  $filter_system_SQL AND\n"
        .($filter_active ?                      "  (`active` = 1) AND\n" : "")
		.($filter_by_range && $filter_dx_min ?  "  round(degrees(acos(sin(radians($filter_dx_lat)) * sin(radians(signals.lat)) + cos(radians($filter_dx_lat)) * cos(radians(signals.lat)) * cos(radians($filter_dx_lon - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) > $filter_dx_min AND\n" : "")
    	.($filter_by_range && $filter_dx_max ?  "  round(degrees(acos(sin(radians($filter_dx_lat)) * sin(radians(signals.lat)) + cos(radians($filter_dx_lat)) * cos(radians(signals.lat)) * cos(radians($filter_dx_lon - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) < $filter_dx_max AND\n" : "")
		.($filter_custom ?                      " ($filter_custom_SQL) AND\n" : "")
	    .($filter_date_2 ?                      "  (`last_heard` >= \"$filter_date_1\" AND `last_heard` <= \"$filter_date_2\") AND\n" : "")
		.($filter_id ?                          "  (`signals`.`call` LIKE \"%$filter_id%\") AND\n" : "")
    	.($filter_itu ?                         "  ($filter_itu_SQL) AND\n" : "")
	    .($filter_khz_2 ?                       "  (`khz` >= $filter_khz_1 AND `khz` <= $filter_khz_2) AND\n" : "")
		.($filter_channels==1 ?                 "  MOD((`khz`* 1000),1000) = 0 AND\n" : "")
    	.($filter_channels==2 ?                 "  MOD((`khz`* 1000),1000) != 0 AND\n" : "")
	    .($filter_sp ?                          "  ($filter_sp_SQL) AND\n" : "")
		.($filter_type ?                        "  $filter_type AND\n" : "")
        ."  (1)\n"
    	."ORDER BY\n"
        ."  `signals`.`ITU`,\n"
        ."  `signals`.`SP`,\n"
        ."  `signals`.`khz`,\n"
        ."  `signals`.`call`";

//        print "<pre>$sql</pre>";
  $result = 	@mysql_query($sql);
  $total =	mysql_num_rows($result);
  $heard =	0;

  //print("<pre>$sql</pre>");


  $signals =	array();
  $itu_sp =	array();
  for ($i=0; $i<$total; $i++) {
    $row = mysql_fetch_array($result,MYSQL_ASSOC);
    $signals[$row['ID']] = array('active'=>$row['active'],'khz'=>(float)$row['khz'],'call'=>$row['call'],'type'=>$row['type'],'SP'=>$row['SP'],'ITU'=>$row['ITU'],'heard'=>0);
    $itu_sp_index = $row['ITU']."_".$row['SP'];
    if (!isset($itu_sp[$itu_sp_index])) {
      $itu_sp[$itu_sp_index] = array('total'=>1,'heard'=>0);
    }
    else {
      $itu_sp[$itu_sp_index]['total']++;
    }
  }
  mysql_free_result($result);

  if ($createFor) {
    $sql =	"select DISTINCT `signals`.`ID` FROM `signals`,`logs` WHERE `signals`.`ID` = `logs`.`signalID` AND `listenerID` = $createFor";
    $result = 	mysql_query($sql);
    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row = mysql_fetch_array($result,MYSQL_ASSOC);
      $ID = $row['ID'];
      if (isset($signals[$ID])) {
        $signals[$ID]['heard'] = 1;
        $itu_sp[$signals[$ID]['ITU']."_".$signals[$ID]['SP']]['heard']++;
        $heard++;
      }
    }
    mysql_free_result($result);
  }
  $out =
     "<form name='form' action='".system_URL."/".$mode."' method='POST'>\n"
    ."<input type='hidden' name='mode' value='$mode'>\n"
    ."<input type='hidden' name='submode' value=''>\n"
    ."<input type='hidden' name='targetID' value=''>\n"
    ."<h2><a name='top'></a>Signal Seeklist</h2>\n"
    ."<table cellpadding='2' border='0' cellspacing='1'>\n"
    ."  <tr>\n"
    ."    <td align='center' valign='top' colspan='2'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
    ."      <tr>\n"
    ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
    ."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Customise Report</td>\n"
    ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
    ."      </tr>\n"
    ."    </table>\n"
    ."    <table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm'>\n"
    ."      <tr class='rowForm'>\n"
    ."        <th align='left'>Page Size</th>\n"
    ."        <td nowrap><select name='paper' class='formField' onchange='document.form.go.value=\"Please wait...\";document.form.go.disabled=1;document.form.submit()'>\n"
    ."          <option value='ltr'".($paper=='ltr' ? " selected" : "").">Letter (Portrait) - 8.5&quot; x 11&quot;</option>"
    ."          <option value='lgl'".($paper=='lgl' ? " selected" : "").">Legal (Portrait) - 8.5&quot; x 14&quot;</option>"
    ."          <option value='a4'".($paper=='a4' ? " selected" : "").">A4 (Portrait) - 21.6cm x 27.9cm</option>"
    ."          <option value='ltr_l'".($paper=='ltr_l' ? " selected" : "").">Letter (Landscape) - 11&quot; x 8.5&quot;</option>"
    ."          <option value='lgl_l'".($paper=='lgl_l' ? " selected" : "").">Legal (Landscape) - 14&quot; x 8.5&quot;</option>"
    ."          <option value='a4_l'".($paper=='a4_l' ? " selected" : "").">A4 (Landscape) - 27.9cm x 21.6cm</option>"
    ."</select> <span class='noprint'>Click <a href='javascript:alert(\"Tips:\\n\\nYou should make sure that the size chosen matches the\\npaper size selected in your browser.\\n\\nUse \\\"Print Preview\\\" if available to check that report will fit.\\n\\nYou do not need to print the last page - this just contains\\nsoftware copyright info - save trees!\\n\\nSorry - page breaks don`t quite work in Netscape.\")'><b>here</b></a> for tips...</span>"
    ."        </td>"
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
    ."      <tr class='rowForm'>\n"
    ."        <th align='left' title='Select listener to generate Seeklist for'>Create for</th>\n"
    ."        <td><select name='createFor' class='formfield' style='font-family: monospace' onchange='set_listener_and_heard_in(document.form)'>\n"
    .get_listener_options_list($filter_listener_SQL,$createFor,"Don't customise list for any listener")
    ."</select></td>\n"
    ."      </tr>\n"
    ."      <tr class='rowForm'>\n"
    ."        <th align='left'>Frequencies&nbsp;</th>\n"
    ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."          <tr>\n"
    ."            <td><input title='Lowest frequency (or leave blank)' type='text' name='filter_khz_1' size='6' maxlength='9' value='".($filter_khz_1 !="0" ? $filter_khz_1 : "")."' class='formfield'> - <input title='Highest frequency (or leave bank)' type='text' name='filter_khz_2' size='6' maxlength='9' value='".($filter_khz_2 != 1000000 ? $filter_khz_2 : "")."' class='formfield'> KHz</td>\n"
    ."            <td>Channels</td>\n"
        ."            <td><select name='filter_channels' class='formField'>\n"
        ."<option value=''".($filter_channels=='' ? ' selected' : '').">All</option>\n"
        ."<option value='1'".($filter_channels=='1' ? ' selected' : '').">Only 1 KHz</option>\n"
        ."<option value='2'".($filter_channels=='2' ? ' selected' : '').">Not 1 KHz</option>\n"
        ."</select></td>\n"
    ."            <td align='right'><span title='Callsign or DGPS ID (Exact matches are shown at the top of the report, partial matches are shown later)'><b>Call / ID</b></span> <input type='text' name='filter_id' size='6' maxlength='12' value='$filter_id' class='formfield' title='Limit results to signals with this ID or partial ID -\nuse _ to indicate a wildcard character'></nobr></td>"
    ."          </tr>\n"
    ."        </table></td>"
    ."      </tr>\n"
    ."      <tr class='rowForm'>\n"
    ."        <th align='left'>Locations&nbsp;</th>\n"
    ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."          <tr>\n"
    ."            <td nowrap>&nbsp;<span title='List of States or Provinces'><a href='".system_URL."/show_sp' onclick='show_sp();return false' title='NDBList State and Province codes'><b>States</b></a></span> <input title='Enter one or more states or provinces (e.g. MI or NB) to show only signals physically located there' type='text' name='filter_sp' size='20' value='$filter_sp' class='formfield'></nobr></td>\n"
    ."            <td nowrap align='right'><span title='List of Countries'><a href='".system_URL."/show_itu' onclick='show_itu();return false' title='NDBList Country codes'>&nbsp;<b>Countries</b></a></span> <input title='Enter one or more NDBList approved 3-letter country codes (e.g. CAN or BRA) to show only signals physically located there' type='text' name='filter_itu' size='20' value='$filter_itu' class='formfield'></nobr></td>"
    ."          </tr>\n"
    ."	 </table></td>"
    ."      </tr>\n"
    ."      <tr class='rowForm'>\n"
    ."        <th align='left'>Range</th>\n"
    ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."          <tr>\n"
    ."            <td>&nbsp;<b>From GSQ</b> <input title='Enter a grid square to show only signals physically located between the distances indicated' type='text' name='filter_dx_gsq' size='6' maxlength='6' value='$filter_dx_gsq' class='formfield' onKeyUp='set_range(form)' onchange='set_range(form)'></td>"
    ."            <td><b><span title='Distance'>DX</span></b> <input title='Enter a value to show only signals equal or greater to this distance' type='text' name='filter_dx_min' size='5' maxlength='5' value='$filter_dx_min'".($filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")." onKeyUp='set_range(form)' onchange='set_range(form)'> - "
    ."<input title='Enter a value to show only signals up to this distance' type='text' name='filter_dx_max' size='5' maxlength='5' value='$filter_dx_max'".($filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")." onKeyUp='set_range(form)' onchange='set_range(form)'></td>"
    ."            <td width='45'><label for='filter_dx_units_km'><input type='radio' id='filter_dx_units_km' name='filter_dx_units' value='km'".($filter_dx_units=="km" ? " checked" : "").($filter_dx_gsq ? "" : " disabled").">km</label></td>"
    ."            <td width='55'><label for='filter_dx_units_miles'><input type='radio' id='filter_dx_units_miles' name='filter_dx_units' value='miles'".($filter_dx_units=="miles" ? " checked" : "").($filter_dx_gsq ? "" : " disabled").">miles&nbsp;</label></td>"
    ."          </tr>\n"
    ."	 </table></td>"
    ."      </tr>\n"
    ."      <tr class='rowForm'>\n"
    ."        <th align='left' valign='top'><span title='Only signals heard by the selected listener'>Heard by<br><br><span style='font-weight: normal;'>Use SHIFT or <br>CONTROL to<br>select multiple<br>values</span></th>"
    ."        <td><select name='listenerID[]' multiple class='formfield' onchange='set_listener_and_heard_in(document.form)' style='font-family: monospace; width: 425; height: 90px;' >\n"
    .get_listener_options_list($filter_listener_SQL,$listenerID,"Anyone (or enter values in \"Heard In\" box)")
    ."</select></td>\n"
    ."      </tr>\n";
  if (system=="RWW") {
    $out.=
       "     <tr class='rowForm'>\n"
      ."       <th align='left'>Heard in&nbsp;</th>\n"
      ."       <th align='left'>\n"
      ."<select name='region' onchange='document.form.go.disabled=1;document.form.submit()' class='formField' style='width: 100%;'>\n"
      .get_region_options_list($region,"(All Continents)")
      ."</select>"
      ."</th>"
      ."      </tr>\n";
  }
    $out.=
         "      <tr class='rowForm'>\n"
		."        <th align='left'><span title='Only signals heard in these states and countries'>Heard here</span></th>\n"
		."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		."          <tr>\n"
		."            <td title='Separate multiple options using spaces' nowrap>\n"
		."<input type='text' name='filter_heard' size='41' value='".($filter_heard ? strToUpper($filter_heard) : "(All States and Countries)")."'\n"
		.($filter_heard=="" ? "style='color: #0000ff' ":"")
		."onclick=\"if(this.value=='(All States and Countries)') { this.value=''; this.style.color='#000000'}\"\n"
		."onblur=\"if(this.value=='') { this.value='(All States and Countries)'; this.style.color='#0000ff';}\"\n"
		."onchange='set_listener_and_heard_in(form)' onKeyUp='set_listener_and_heard_in(form)' ".($listenerID ? "class='formfield_disabled' disabled" : "class='formfield'").">"
		."            <td width='45'><label for='radio_grp_any' title='Show where any terms match'><input id='radio_grp_any' type='radio' value='any' name='grp'".($grp!="all" ? " checked" : "").($listenerID || !$filter_heard ? " disabled" : "").">Any</label></td>\n"
		."            <td width='55'><label for='radio_grp_all' title='Show where all terms match'><input id='radio_grp_all' type='radio' value='all' name='grp'".($grp=="all" ? " checked" : "").($listenerID || !$filter_heard ? " disabled" : "").">All</label></td>\n"
		."          </tr>\n"
		."	 </table></td>"
		."      </tr>\n"
		."      <tr class='rowForm'>\n"
		."        <th align='left'>Last Heard</th>\n"
		."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		."          <tr>\n"
		."            <td><input title='Enter a start date to show only signals last heard after this date (YYYY-MM-DD format)' type='text' name='filter_date_1' size='10' maxlength='10' value='".($filter_date_1 != "1900-01-01" ? $filter_date_1 : "")."' class='formfield'> -\n"
		."<input title='Enter an end date to show only signals last heard before this date (YYYY-MM-DD format)' type='text' name='filter_date_2' size='10' maxlength='10' value='".($filter_date_2 != "2020-01-01" ? $filter_date_2 : "")."' class='formfield'></nobr></td>"
		."            <td align='right'><label for='chk_filter_active'><input id='chk_filter_active'type='checkbox' name='filter_active' value='1'".($filter_active ? " checked" : "").">Only active stations&nbsp;</label></td>"
		."          </tr>\n"
		."	 </table></td>"
		."      </tr>\n"
    ."      <tr class='rowForm'>\n"
    ."        <th align='left'>Custom Filter:</th>\n"
    ."        <td nowrap><select name='filter_custom' class='formField'>\n"
	."<option value=''".($filter_custom=='' ? " selected" : "").">(None)</option>\n"
	."<option value='cle160'".($filter_custom=='cle160' ? " selected" : "").">CLE160</option>\n"
	."</select></td>\n"
	."      </tr>"
		."      <span class='noprint'><tr class='rowForm'>\n"
		."        <th colspan='2'><center><input type='submit' onclick='return send_form(form)' name='go' value='Go' style='width: 100px;' class='formButton' title='Execute search'>\n"
		."<input name='clear' type='button' class='formButton' value='Clear' style='width: 100px;' onclick='clear_signal_list(document.form)'></center></th>"
		."      </tr></span>\n"
		."    </table></td>\n"
		."  </tr>\n"
		."</table><br>\n";

  $page =	1;


  $SP =		"";
  $ITU =	"";
  $xpos =	0;
  $col =	0;
  $row =	$page_len-$filter_size;
  $listener =	get_listener_name($createFor);
  $out.=
     "<table cellpadding='2' cellspacing='1' border='0' style='page-break-after: always;' valign='top' class='downloadtable'>\n"
    ."  <tr>\n"
    ."    <th colspan='$page_cols' class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."      <tr>\n"
    ."        <th align='left' class='downloadTableHeadings_nosort' width='20%'>&nbsp;<b>".system." Seeklist</b></th>\n"
    ."        <th class='downloadTableHeadings_nosort' width='60%'>".($listener ? "$listener" : "")."</th>\n"
    ."        <th class='downloadTableHeadings_nosort' align='right' width='20%'>&nbsp; Page $page\n"
    ."<span class='noprint'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small>&nbsp;</span></th>\n"
    ."      </tr>\n"
    ."    </table></th>\n"
    ."  </tr>\n"
    ."  <tr>\n"
    ."    <td colspan='$page_cols' class='downloadTableContent' width='100%'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."      <tr>\n"
    ."        <td height='18' valign='top'>Signals\n"
    .($filter_heard ? " heard in [<b>$filter_heard</b>]" : "")
    .($filter_khz_1 ? " from ".$filter_khz_1."KHz" : "")
    .($filter_khz_2 && $filter_khz_2 != "100000" ? " to ".$filter_khz_2."KHz" : "")
    .($filter_sp || $filter_itu ? " located in " : "")
    .($filter_sp ? " ".$filter_sp : "")
    .($filter_itu ? " ".$filter_itu : "")
    .($filter_id ? " with ID containing ".$filter_id : "")
    .($filter_active!="1" ? " including <span style='border-bottom: 1px dashed #000000; background-color: #d0d0d0;' title='(Reportedly Out Of Service or Decomissioned)'>inactive stations</span>" : "")
    ." (".($createFor ? $heard." of " : "total: ")."$total)"
    ."</td>\n"
    ."        <td align='right'>Updated $last</td>\n"
    ."      </tr>\n"
    ."    </table></td>\n"
    ."  </tr>\n";
  $page++;
  $out.=
     "  <tr>\n"
	."    <td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'>\n";
  foreach($signals as $key=>$value) {
    if ($value['SP'] != $SP || $value['ITU'] != $ITU) {
      if($SP.$ITU!="" && $xpos) {
        $out.=	"<br>";
        $xpos+=	$heading_gap;
      }
      $out.=	"    <table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><b>".$value['ITU']." ".$value['SP']."</b></td><td align='right'>(".($createFor!="" ? $itu_sp[$value['ITU']."_".$value['SP']]['heard']." of " : "").$itu_sp[$value['ITU']."_".$value['SP']]['total'].")</td></tr></table>\n";
      $xpos+=	$heading_height;
      $SP =	$value['SP'];
      $ITU =	$value['ITU'];
    }
    if ($value["active"]!="1") {
      $bgcolor =	" style='border: 1px dashed #000000; background-color: #d0d0d0;' title='(Reportedly Out Of Service or Decomissioned)'";
    }
    else {
      switch ($value['type']) {
        case NDB:	$bgcolor = " title='NDB'";								break;
        case DGPS:	$bgcolor = " style='background-color: #00D8ff' title='DGPS Station'";			break;
        case TIME:	$bgcolor = " style='background-color: #FFE0B0' title='Time Signal Station'";		break;
        case NAVTEX:	$bgcolor = " style='background-color: #FFB8D8' title='NAVTEX Station'";			break;
        case HAMBCN:	$bgcolor = " style='background-color: #D8FFE0' title='Amateur signal'";			break;
        case OTHER:	$bgcolor = " style='background-color: #B8F8FF' title='Other form of transmission'";	break;
      }
    }
    $out.=	 "<span class='fixed'$bgcolor>".pad_dot($value["khz"],8).pad_dot($value['call'],8)
		.($value['heard'] ? "<img src='".BASE_PATH."assets/icon-tick-on.gif'>" : "<img src='".BASE_PATH."assets/icon-tick-off.gif'>")."<br></span>\n";
    $xpos+=	$line_height;
    if ($xpos>$row) {
      $xpos=0;
      $col++;
      $out.=	"</td>\n";
      if ($col<$page_cols) {
        $out.=		"<td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'>";
      }
      else {
        $out.=		 "</td>\n"
			."</tr>\n"
			."</table>\n"
			."<br>\n"
			."<br>\n"
			."<table cellpadding='2' cellspacing='1' border='0' style='page-break-after: always;' valign='top' class='downloadtable'>\n"
			."  <tr>\n"
			."    <th colspan='$page_cols' class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
			."      <tr>\n"
			."        <th align='left' class='downloadTableHeadings_nosort' width='20%'>&nbsp;<b>".system." Seeklist</b></th>\n"
			."        <th class='downloadTableHeadings_nosort' width='60%'>".($listener ? "$listener" : "")."</th>\n"
			."        <th class='downloadTableHeadings_nosort' align='right' width='20%'>&nbsp; Page $page\n"
			."<span class='noprint'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small>&nbsp;</span></th>\n"
			."      </tr>\n"
			."    </table></th>\n"
			."  </tr>\n"
			."  <tr>\n"
			."    <td colspan='$page_cols' class='downloadTableContent' width='100%'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
			."      <tr>\n"
			."        <td>Signals Seeklist (continued)</td>\n"
			."        <td align='right'>Updated $last</td>\n"
			."      </tr>\n"
			."    </table></td>\n"
			."  </tr>\n";
        $page++;

        $out.=		"  <tr>\n"
			."    <td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'>\n"
			."    <table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><b>...".$value['ITU']." ".$value['SP']."</b></td><td align='right'>(".($createFor!="" ? $itu_sp[$value['ITU']."_".$value['SP']]['heard']." of " : "").$itu_sp[$value['ITU']."_".$value['SP']]['total'].")</td></tr></table>\n";
        $xpos+=	$heading_height;
        $col = 0;
        $row = $page_len;
      }
    }

  }

  if ($col<$page_cols) {
    $out.=	"&nbsp;</td>\n";
    for ($col;$col<$page_cols-1; $col++) {
      $out.=	"<td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'><span class='fixed'>&nbsp;</span></td>\n";
    }
  }

  $out.=		 "</tr>\n"
		."</table>\n"
		."</form>\n"
		."<script language='javascript' type='text/javascript'>document.form.filter_id.focus();document.form.filter_id.select();</script>\n";
  return $out;
}

// ************************************
// * signal_update_full               *
// ************************************
function signal_update_full($ID,$LSB,$LSB_approx,$USB,$USB_approx,$sec,$fmt,$logs,$last_heard,$region) {
  $sql =
     "UPDATE `signals` SET\n"
    .($LSB!="" ?	  "  `LSB` = \"$LSB\",\n"
    ."  `LSB_approx` = \"".$LSB_approx."\",\n" : "")
    .($USB!="" ?	  "  `USB` = \"$USB\",\n"
    ."  `USB_approx` = \"".$USB_approx."\",\n" : "")
    .($sec!="" ?	  "  `sec` =	\"$sec\",\n" : "")
    .($fmt ?	  "  `format` =	\"".$fmt."\",\n" : "")
    ."  `heard_in_$region` = 1,\n"
    ."  `logs` = $logs,\n"
    ."  `last_heard` = \"$last_heard\"\n"
    ."WHERE `ID` = '$ID'";
  mysql_query($sql);
  //print "<pre>$sql</pre>";
}




// ************************************
// * signal_update_heard_in           *
// ************************************
function signal_update_heard_in($ID){
  $sql =		 "SELECT DISTINCT\n"
			." `heard_in`,\n"
			."  MAX(`daytime`) as `daytime`,\n"
			." `region`\n"
			."FROM\n"
			."  `logs`\n"
			."WHERE\n"
			."  `signalID` = \"$ID\"\n"
			."GROUP BY\n"
			."  `heard_in`\n"
			."ORDER BY\n"
			."  (`region`='na' OR `region`='ca' OR (`region`='oc' AND `heard_in`='HI')),\n"
			."  `region`,\n"
			."  `heard_in`";
  $result =		@mysql_query($sql);
  $arr =		array();
  $html_arr =		array();
  $region =		"";
  $old_link =		"";
  for ($j = 0; $j<mysql_num_rows($result); $j++) {
    $row =		mysql_fetch_array($result);
    $heard_in =		$row["heard_in"];
    $daytime =		$row["daytime"];
    $region =		$row["region"];
    $link =		"";
    switch ($region) {
      case "ca":
        $link =		"<a class='hover' href='javascript:void signal_map_na($ID)' title='North American Reception Map'>";
      break;
      case "na":
        $link =		"<a class='hover' href='javascript:void signal_map_na($ID)' title='North American Reception Map'>";
      break;
      case "oc":
        if ($heard_in=='HI') {
          $link =	"<a class='hover' href='javascript:void signal_map_na($ID)' title='North American Reception Map'>";
        }
      break;
      case "eu":
        $link =		"<a class='hover' href='javascript:void signal_map_eu($ID)' title='European Reception Map'>";
      break;
    }
    $html_arr[] =	 ($old_link!="" && $old_link != $link ? "</a>" : "")
			.($link != $old_link ? $link : "")
			.($daytime ? "<b>$heard_in</b>" : $heard_in);
    $arr[] =		$heard_in;
    $old_link =		$link;
  }

  $sql =		 "UPDATE `signals` SET\n"
			."  `heard_in` = \"".implode($arr," ")."\",\n"
			."  `heard_in_html` = \"".implode($html_arr," ")."</a>\"\n"
			."WHERE `ID` = $ID";
  $result =		mysql_query($sql);
  return (mysql_affected_rows());
}
?>
