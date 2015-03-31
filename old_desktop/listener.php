<?php
//  2007-07-02
//    Added Listener::get_log() and Listener::get_log_count()
//    Implemented paging for listener logs

class Listener extends Record{
  function Listener($ID){
    parent::Record($ID, 'listeners');
  }

  function tabs() {
    $record = $this->get_record();
  return
     tabItem("Profile","listener_edit",50)
    .tabItem("Signals (".$record['count_signals'].")","listener_signals",105)
    .tabItem("Logs (".$record['count_logs'].")","listener_log",85)
    .tabItem("Export","listener_log_export",45)
    .(isset($GSQ) ? tabItem("QNH","listener_QNH",35) : "")
    .tabItem("Stats","listener_stats",45)
    .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
         "                <td class='tabOff' onclick='log_upload(\"".$this->ID."\");' onMouseOver='return tabOver(this,1);' onMouseOut='return tabOver(this,0);' width='45'>Add...</td>\n"
       : ""
     );
  }
  function get_log($sortBy_SQL,$limit=-1,$offset=0) {
    if (!$this->get_ID()){
      return array();
    }
    $sql =
       "SELECT\n"
      ."  `logs`.`ID`,\n"
      ."  `logs`.`date`,\n"
      ."  `logs`.`daytime`,\n"
      ."  `logs`.`dx_km`,\n"
      ."  `logs`.`dx_miles`,\n"
      ."  `logs`.`format`,\n"
      ."  `logs`.`sec`,\n"
      ."  `logs`.`time`,\n"
      ."  `logs`.`LSB`,\n"
      ."  `logs`.`USB`,\n"
      ."  `logs`.`LSB_approx`,\n"
      ."  `logs`.`USB_approx`,\n"
      ."  `signals`.`active`,\n"
      ."  `signals`.`call`,\n"
      ."  `signals`.`GSQ`,\n"
      ."  `signals`.`ITU`,\n"
      ."  `signals`.`khz`,\n"
      ."  `signals`.`type`,\n"
      ."  `signals`.`SP` AS `signalSP`,\n"
      ."  `signals`.`ID` AS `signalID`\n"
      ."FROM\n"
      ."  `signals`\n"
      ."INNER JOIN `logs` ON\n"
      ." `signals`.`ID` = `logs`.`signalID`\n"
      ."WHERE\n"
      ." `listenerID` = ".$this->get_ID()."\n"
      .($sortBy_SQL ?
         "ORDER BY\n"
        ."  $sortBy_SQL"
       : "")
      .($limit<>-1 ?
         "\nLIMIT\n  $offset, $limit"
       : "")
      ;
//  z($sql);
    return $this->get_records_for_sql($sql);
  }
  function get_log_count(){
    if (!$this->get_ID()){
      return 0;
    }
    $sql =
       "SELECT\n"
      ."  COUNT(*) AS `count`\n"
      ."FROM\n"
      ."  `logs`\n"
      ."WHERE\n"
      ." `listenerID` = ".$this->get_ID();
//  z($sql);
    $result = $this->get_record_for_sql($sql);
    return $result['count'];
  }
}
// ************************************
// * listener_edit()                  *
// ************************************
function listener_edit() {
  global $script, $mode, $submode;
  global $ID, $callsign, $email, $equipment, $GSQ, $ITU, $name, $notes, $QTH, $primary_QTH, $region, $SP, $timezone, $website, $map_x, $map_y;
  $out =	array();
  $ITU =	trim(strToUpper($ITU));
  $SP =		trim(strToUpper($SP));


  $region = get_region_for_itu($ITU);

  if ($region=="") {
    switch (system) {
      case "REU":	$region = "eu";		break;
      case "RNA":	$region = "na";		break;
    }
  }

  $Obj = new Listener($ID);
  $error_msg =	"";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    if ($submode=="add" or $submode=="update") {
      $lat =	0;
      $lon =	0;
      if ($GSQ) {
        $GSQ =	strtoUpper(substr($GSQ,0,4)).strtoLower(substr($GSQ,4,2));
        $a = 		GSQ_deg($GSQ);
        $lat =	$a["lat"];
        $lon =	$a["lon"];
      }
      $error_msg =	"";
      if ($submode=="add" or $submode=="update") {
        $error_msg = check_sp_itu($SP,$ITU);
      }
    }
  
    if ($error_msg=="") {
      switch ($submode) {
        case "add":
          $sql = "INSERT INTO `listeners` SET\n"
  		."  `callsign` =	\"".htmlentities(strtoupper(trim($callsign)))."\",\n"
  		."  `email` =		\"".htmlentities(trim($email))."\",\n"
  		."  `equipment` =	\"".htmlentities(trim($equipment))."\",\n"
  		."  `GSQ` =		\"".htmlentities(trim($GSQ))."\",\n"
  		.($GSQ ? "  `lat` = \"".$lat."\",\n  `lon` = \"".$lon."\",\n" : "")
  		."  `ITU` =		\"".htmlentities(trim($ITU))."\",\n"
  		."  `map_x` =		\"".htmlentities(trim($map_x))."\",\n"
  		."  `map_y` =		\"".htmlentities(trim($map_y))."\",\n"
  		."  `name` =		\"".htmlentities(trim($name))."\",\n"
  		."  `notes` =		\"".htmlentities(trim($notes))."\",\n"
  		."  `primary_QTH` =	\"".htmlentities(trim($primary_QTH))."\",\n"
  		."  `QTH` =		\"".htmlentities(trim($QTH))."\",\n"
  		."  `SP` =		\"".htmlentities(trim($SP))."\",\n"
  		."  `region` =		\"$region\",\n"
  		."  `timezone` =	\"".htmlentities(trim($timezone))."\",\n"
  		."  `website` =		\"".htmlentities(trim($website))."\"\n";
          mysql_query($sql);
          $ID =	mysql_insert_id();
          $out[] ="<script language='javascript' type='text/javascript'>if (window.opener) { window.opener.document.form.submit();}</script>";
        break;	

        case "update":
          $sql = "UPDATE `listeners` SET\n"
  		."  `callsign` =	\"".htmlentities(strtoupper(trim($callsign)))."\",\n"
		."  `email` =		\"".htmlentities(trim($email))."\",\n"
		."  `equipment` =	\"".htmlentities(trim($equipment))."\",\n"
		."  `GSQ` =		\"".htmlentities(trim($GSQ))."\",\n"
		.($GSQ ? "  `lat` = \"".$lat."\",\n  `lon` = \"".$lon."\",\n" : "")
		."  `ITU` =		\"".htmlentities(trim($ITU))."\",\n"
  		."  `map_x` =		\"".htmlentities(trim($map_x))."\",\n"
  		."  `map_y` =		\"".htmlentities(trim($map_y))."\",\n"
		."  `name` =		\"".htmlentities(trim($name))."\",\n"
		."  `notes` =		\"".htmlentities(trim($notes))."\",\n"
		."  `primary_QTH` =	\"".htmlentities(trim($primary_QTH))."\",\n"
		."  `QTH` =		\"".htmlentities(trim($QTH))."\",\n"
  		."  `region` =		\"$region\",\n"
		."  `SP` =		\"".htmlentities(trim($SP))."\",\n"
  		."  `timezone` =	\"".htmlentities(trim($timezone))."\",\n"
		."  `website` =		\"".htmlentities(trim($website))."\"\n"
		."WHERE `ID` =		\"".addslashes($ID)."\"";
          mysql_query($sql);
          $out[] =	"<script language='javascript' type='text/javascript'>if (window.opener) { window.opener.document.form.submit();}</script>";
        break;	
      }
    }
  }

  if ($ID=="") {
    $submode="add";
  }
  else {
    $submode="update";
  }
  if ($ID=="") {
    $signals = 0;
    $logs = 0;
    $lat = 0;
    $lon = 0;
  }
  else {
    $sql =		"SELECT * FROM `listeners` WHERE `ID` = '$ID'";
    $result = 		mysql_query($sql);
    $row =	    	mysql_fetch_array($result,MYSQL_ASSOC);
    $signals =		$row['count_signals'];
    $callsign =		$row['callsign'];
    $email =		$row['email'];
    $equipment =	stripslashes($row['equipment']);
    $GSQ =		    $row['GSQ'];
    $ITU =		    $row['ITU'];
    $logs =		    $row['count_logs'];
    $lat =	    	$row['lat'];
    $lon =	    	$row['lon'];
    $map_x =		$row['map_x'];
    $map_y =		$row['map_y'];
    $name =		    $row['name'];
    $notes =		stripslashes($row["notes"]);
    $primary_QTH =	$row['primary_QTH'];
    $QTH =		    $row['QTH'];
    $region =	    $row['region'];
    $SP =		    $row['SP'];
    $timezone =		$row['timezone'];
    $website =		$row['website'];
  }
  $out[] =
     "<table border='0' cellpadding='0' cellspacing='0'>\n"
    ."  <tr>\n"
    ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
    ."      <tr>\n"
    ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%'>\n"
    ."          <tr>\n"
    ."            <td><h1>Listener</h1></td>\n"
    ."            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
    ."              <tr>\n"
    .$Obj->tabs()
    ."              </tr>\n"
            ."            </table></td>\n"
            ."          </tr>\n"
            ."        </table>\n"
            ."        <table border='0' align='center' cellspacing='0' cellpadding='0' class='tableContainer' width='100%' height='100%'>\n"
            ."          <tr>\n"
            ."            <td bgcolor='#F5F5F5' class='itemTextCell' height='325' valign='top'><p></p>\n";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out[] =   "            <form action='$script' name='form' method='POST'>\n"
              ."            <input type='hidden' name='ID' value='$ID'>\n"
              ."            <input type='hidden' name='mode' value='$mode'>\n"
              ."            <input type='hidden' name='submode' value=''>\n";
  }
  $out[] =	 "            <table width='100%'  border='0' cellpadding='2' cellspacing='1' class='downloadTable' height='100%'>\n"
            ."              <tr>\n"
            ."                <th colspan='5' class='downloadTableHeadings_nosort' align='left'>&nbsp;Contact Details</th>\n"
            ."              </tr>\n"
            ."              <tr class='rownormal'>\n"
            ."                <th class='downloadTableContent' align='left' width='107'>Name</th>\n"
            ."                <td class='downloadTableContent' colspan='2'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' title='Listener Name' name='name' value=\"$name\" size='20' style='width: 100%;'>":($name ? $name : "&nbsp;"))."</td>\n"
            ."                <td class='downloadTableContent' align='left'><b>Callsign</b></td>\n"
            ."                <td class='downloadTableContent' align='right'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' title='Listener Ham Radio Callsign' name=\"callsign\" value=\"$callsign\" size='10'>":($callsign ? $callsign : "&nbsp;"))."</td>\n"
            ."              </tr>\n"
            ."              <tr class='rownormal'>\n"
            ."                <th class='downloadTableContent' align='left'>Email Address</th>\n"
            ."                <td class='downloadTableContent' colspan='4'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='email' value=\"$email\" size='40' style='width: 100%;'>":($email!="" ? "<a href='mailto:$email?subject=".system."'><b>$email</b></a>" : "&nbsp;"))."</td>\n"
            ."              </tr>\n"
            ."              <tr class='rownormal'>\n"
            ."                <th class='downloadTableContent' align='left'>Web Site</th>\n"
            ."                <td class='downloadTableContent' colspan='4'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='website' value=\"$website\" size='40' style='width: 100%;'>":($website!="" ? "<a href='$website' target='_blank'><b>$website</b></a>" : "&nbsp;"))."</td>\n"
            ."              </tr>\n"
            ."              <tr class='rownormal'>\n"
            ."                <th colspan='5' class='downloadTableHeadings_nosort' align='left'>&nbsp;Other Details</th>\n"
            ."              </tr>\n"
            ."              <tr class='rownormal'>\n"
            ."                <th class='downloadTableContent' align='left'>Location</th>\n"
            ."                <td class='downloadTableContent'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='QTH' value=\"$QTH\" size='15'>":($QTH ? $QTH : "&nbsp;"))."</td>\n"
            ."                <td class='downloadTableContent' align='left'><table cellpadding='0' cellspacing='0' width='100%'>"
            ."                  <tr>\n"
            ."                    <td><span title='State or Province'><a href='javascript:show_sp()' title='NDBList State and Province codes'><b>State / Province</b></a></span></td>\n"
            ."                    <td align='right'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='SP' value=\"$SP\" size='2' maxlength='2'>":($SP ? $SP : "&nbsp;"))."</td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."                <td class='downloadTableContent' align='left'><table cellpadding='0' cellspacing='0' width='100%'>"
            ."                  <tr>\n"
            ."                    <td><span title='Country Codes'><a href='javascript:show_itu()' title='NDBList Country codes'><b>Country</b></a></span></td>\n"
            ."                    <td align='right'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='ITU' value=\"$ITU\" size='3' maxlength='3'>":($ITU ? $ITU : "&nbsp;"))."</td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."                <td class='downloadTableContent' align='left'><table cellpadding='0' cellspacing='0' width='100%'>"
            ."                  <tr>\n"
            ."                    <td><a href=\"javascript:popWin('/dx/ndb/rna/index.php?mode=tools_coordinates_conversion','tools_coordinates_conversion','scrollbars=0,resizable=0',610,144,'centre')\"><b>GSQ</b></a></td>\n"
            ."                    <td align='right'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='GSQ' value=\"$GSQ\" size='6' maxlength='6'>":($GSQ ? $GSQ : "&nbsp;"))."</td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."              </tr>\n"
            ."              <tr>\n"
            ."                <th class='downloadTableContent' align='left'>Timezone</th>\n"
            ."                <td class='downloadTableContent'><table cellpadding='0' cellspacing='0' width='100%'>"
            ."                  <tr>\n"
            ."                    <td><span title='Number of hours behind UTC for Standard time (Not DST)'><b>After UTC</b></span></td>\n"
            ."                    <td align='right'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='timezone' value=\"$timezone\" size='2' maxlength='3'>":($timezone!="" ? $timezone : "&nbsp;"))."</td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."                <td class='downloadTableContent' align='left'><table cellpadding='0' cellspacing='0' width='100%'>"
            ."                  <tr>\n"
            ."                    <td><b>Primary QTH?</b></td>\n"
            ."                    <td align='right'>"
            .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
               "<select name='primary_QTH' class='formField'>"
              ."  <option value='0'".($primary_QTH==1 ? " selected" : "").">No</option>\n"
              ."  <option value='1'".($primary_QTH==1 ? " selected" : "").">Yes</option>\n"
              ."</select>"
            :
              ($primary_QTH ? "Yes" : "No"))
            ."</td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."                <td class='downloadTableContent' align='left' colspan='2'><table cellpadding='0' cellspacing='0' width='100%'>"
            ."                  <tr>\n"
            ."                    <td><b>"
            .(($region=="eu" || $region=="na" || $region=="ca") ? "<a href=\"javascript: map_locator('".($region=="na" || $region=="ca" ? "na" : "eu")."','$map_x','$map_y','".addslashes($name)."','".addslashes($QTH)."','$lat','$lon')\">" : "")
            ."Map Position"
            .(($region=="eu" || $region=="na" || $region=="ca") ? "</a>" : "")
            ."</b></td>\n"
            ."                    <td align='right'>X&nbsp;".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='map_x' value=\"$map_x\" size='3' maxlength='3'>":($map_x!="" ? $map_x : "&nbsp;"))."</td>\n"
            ."                    <td align='right'>Y&nbsp;".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<input class='formField' name='map_y' value=\"$map_y\" size='3' maxlength='3'>":($map_y!="" ? $map_y : "&nbsp;"))."</td>\n"
            ."                  </tr>\n"
            ."                </table></td>\n"
            ."              </tr>\n"
            ."              <tr>\n"
            ."                <th class='downloadTableContent' align='left' valign='top' height='25%'>Notes</th>\n"
            ."                <td class='downloadTableContent' colspan='4'><textarea class='formField' name='notes' rows='2' cols='60' style='width: 100%; height: 95%;'>$notes</textarea></td>\n"
            ."              </tr>\n"
            ."              <tr>\n"
            ."                <th class='downloadTableContent' valign='top' align='left' height='100%'>Equipment</th>\n"
            ."                <td class='downloadTableContent' colspan='4' valign='top'><textarea class='formField' name='equipment' rows='5' cols='60' style='width: 100%; height: 95%;'>$equipment</textarea></td>\n"
            ."              </tr>\n"
            ."              <tr>\n"
            ."                <td class='downloadTableContent' colspan='5' align='center'>\n"
            ."<input type='button' name='print' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
            ."<input type='button' name='close' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> ";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin'] == admin_session) {
    $out[] =	"<input type='button' name='save' class='formButton' value='Save' onclick='document.form.save.disabled=1;document.form.close.disabled=1;document.form.print.disabled=1;document.form.submode.value=\"$submode\";document.form.submit()' style='width: 60px;'>";
  }
  $out[] =	"</td>\n"
            ."              </tr>\n"
            ."            </table></form></td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>\n"
            ."</table>\n";
  if ($error_msg!="") {
     $out[] =	"<script language='javascript' type='text/javascript'>window.setTimeout(\"alert('ERROR\\\\n\\\\n$error_msg')\",1000);</script>\n";
  }
  return implode($out,"");
}


// ************************************
// * listener_get_count()             *
// ************************************
function listener_get_count($region) {

  $region_SQL =	"";
  if ($region=="") {
    switch (system) {
      case "REU":	$region_SQL = "(`listeners`.`region`='eu')";	break;
      case "RNA":	$region_SQL = "(`listeners`.`region`='na' OR `listeners`.`region`='ca' OR (`listeners`.`region`='oc' AND `listeners`.`itu` = 'hwa'))";	break;
    }
  }
  else {
    $region_SQL = "(`listeners`.`region`='$region')";
  }	


  if ($region_SQL=="") {
    $sql =	 "SELECT\n"
		."  COUNT(*) AS `count`\n"
		."FROM\n"
		."  `listeners`\n";
  }
  else {
    $sql =	 "SELECT\n"
		."  COUNT(*) AS `count`\n"
		."FROM\n"
		."  `listeners`\n"
		."WHERE\n"
		."  $region_SQL\n";
  }
  $result = 	@mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);
  return $row["count"];
}



// ************************************
// * listener_list()                  *
// ************************************
function listener_list() {
  global $script, $mode, $submode, $targetID, $filter, $region, $sortBy;
  global $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
  $listener_list_limit = 25;
  $out =	array();

  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    switch ($submode) {
      case "delete":
        $sql =	"SELECT COUNT(*) AS `logs` FROM `logs` WHERE `listenerID` = \"".addslashes($targetID)."\"";
        $result =	mysql_query($sql);
        $row =	mysql_fetch_array($result, MYSQL_ASSOC);
        if ($row["logs"] == "0") {
          $sql =	"DELETE FROM `listeners` WHERE `ID` = \"".addslashes($targetID)."\"";
          mysql_query($sql);
        }
        else {
          $error_msg =	"This listener has submitted ".$row["logs"]." logs and so cannot be deleted.";
        }
      break;
    }
  }

  if (!($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
    switch (system) {
      case "RNA":	$type_NDB =	1;	break;
      case "REU":	$type_NDB =	1;	break;
      case "RWW":	$type_NDB =	1;	break;
    }
  }

  $filter = 	addslashes($filter);
  $filter_SQL =	"`listeners`.`name` LIKE '%$filter%' OR `listeners`.`callsign` LIKE '%$filter%' OR `listeners`.`qth` LIKE '%$filter%'";

  if ($sortBy=="") {
    $sortBy = "name";
  }

  $total =	listener_get_count($region);

  $region_SQL =	"";
  if ($region=="") {
    switch (system) {
      case "REU":	$region_SQL = "(`listeners`.`region`='eu')";	break;
      case "RNA":	$region_SQL = "(`listeners`.`region`='na' OR `listeners`.`region`='ca' OR (`listeners`.`region`='oc' AND `listeners`.`itu` = 'hwa'))";	break;
    }
  }
  else {
    $region_SQL = "(`listeners`.`region`='$region')";
  }	

  $sortBy_SQL =		"";

  switch ($sortBy) {
    case "callsign":		$sortBy_SQL =	"`callsign`='', `callsign` ASC,`name`,`primary_QTH` DESC, `SP` ASC";	break;
    case "callsign_d":		$sortBy_SQL =	"`callsign`='', `callsign` DESC,`name`,`primary_QTH` DESC, `SP` ASC";	break;
    case "GSQ":			$sortBy_SQL =	"`GSQ`='', `GSQ` ASC";						break;
    case "GSQ_d"	:	$sortBy_SQL =	"`GSQ`='', `GSQ` DESC";						break;
    case "ITU":			$sortBy_SQL =	"`ITU` ASC,`SP` ASC";						break;
    case "ITU_d":		$sortBy_SQL =	"`ITU` DESC,`SP` ASC";						break;
    case "log_latest":		$sortBy_SQL =	"`log_latest`=\"0000-00-00\", `log_latest` ASC";		break;
    case "log_latest_d":	$sortBy_SQL =	"`log_latest`=\"0000-00-00\", `log_latest` DESC";		break;
    case "count_DGPS":		$sortBy_SQL =	"`count_DGPS`=0, `count_DGPS` ASC";				break;
    case "count_DGPS_d":	$sortBy_SQL =	"`count_DGPS`=0, `count_DGPS` DESC";				break;
    case "count_HAMBCN":	$sortBy_SQL =	"`count_HAMBCN`=0, `count_HAMBCN` ASC";				break;
    case "count_HAMBCN_d":	$sortBy_SQL =	"`count_HAMBCN`=0, `count_HAMBCN` DESC";			break;
    case "count_logs":		$sortBy_SQL =	"`count_logs`=0, `count_logs` ASC";				break;
    case "count_logs_d":	$sortBy_SQL =	"`count_logs`=0, `count_logs` DESC";				break;
    case "count_NAVTEX":	$sortBy_SQL =	"`count_NAVTEX`=0, `count_NAVTEX` ASC";				break;
    case "count_NAVTEX_d":	$sortBy_SQL =	"`count_NAVTEX`=0, `count_NAVTEX` DESC";			break;
    case "count_NDB":		$sortBy_SQL =	"`count_NDB`=0, `count_NDB` ASC";				break;
    case "count_NDB_d":		$sortBy_SQL =	"`count_NDB`=0, `count_NDB` DESC";				break;
    case "count_OTHER":		$sortBy_SQL =	"`count_OTHER`=0, `count_OTHER` ASC";				break;
    case "count_OTHER_d":	$sortBy_SQL =	"`count_OTHER`=0, `count_OTHER` DESC";				break;
    case "count_TIME":		$sortBy_SQL =	"`count_TIME`=0, `count_TIME` ASC";				break;
    case "count_TIME_d":	$sortBy_SQL =	"`count_TIME`=0, `count_TIME` DESC";				break;
    case "count_signals":	$sortBy_SQL =	"`count_signals`=0, `count_signals` ASC";			break;
    case "count_signals_d":	$sortBy_SQL =	"`count_signals`=0, `count_signals` DESC";			break;
    case "name":		$sortBy_SQL =	"`name`, `primary_QTH` DESC, `ITU`,`SP`,`QTH`";			break;
    case "name_d":		$sortBy_SQL =	"`name` DESC, `primary_QTH` DESC, `ITU`,`SP`,`QTH`";		break;
    case "map_x":		$sortBy_SQL =	"`map_x`, `map_y`";			break;
    case "map_x_d":		$sortBy_SQL =	"`map_x` DESC, `map_y`";		break;
    case "NDBWebLog":		$sortBy_SQL =	"`count_signals`=0 ASC, `name`,`primary_QTH` DESC, `SP` ASC";	break;
    case "NDBWebLog_d":		$sortBy_SQL =	"`count_signals`=0 DESC, `name`, `primary_QTH` DESC, `SP` ASC";	break;
    case "notes":		$sortBy_SQL =	"`notes` IS NULL, `notes` ASC";					break;
    case "notes_d":		$sortBy_SQL =	"`notes` IS NULL, `notes` DESC";				break;
    case "region":		$sortBy_SQL =	"`region` ASC, `ITU` ASC,`SP` ASC,`QTH` ASC";			break;
    case "region_d":		$sortBy_SQL =	"`region` DESC,`ITU` ASC,`SP` ASC,`QTH` ASC";			break;
    case "QTH":			$sortBy_SQL =	"`QTH` ASC";							break;
    case "QTH_d":		$sortBy_SQL =	"`QTH` DESC";							break;
    case "SP":			$sortBy_SQL =	"`SP`='',`SP` ASC";						break;
    case "SP_d":		$sortBy_SQL =	"`SP`='',`SP` DESC";						break;
    case "timezone":		$sortBy_SQL =	"`timezone`='',`timezone` ASC, `SP` ASC";				break;
    case "timezone_d":		$sortBy_SQL =	"`timezone`='',`timezone` DESC, `SP` ASC";				break;
    case "WWW":			$sortBy_SQL =	"(`website` is NULL or `website`='') ASC, `name`,`primary_QTH` DESC";	break;
    case "WWW_d":		$sortBy_SQL =	"(`website` is NULL or `website`='') DESC, `name`, `primary_QTH` DESC";	break;
  }

  $out[] =
     "<form name='form' action='$script' method='POST'>\n"
	."<h1>Listener List</h1>\n"
    ."<ul><li>Log and station counts are updated each time new log data is added - figures are for logs in the system at this time.</li>\n"
	."<li>To see stats for different types of signals, check the boxes shown for 'Types' below.</li>\n"
	."<li>This report prints best in Landscape.</li></ul>\n"
	."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable noprint'>\n"
	."  <tr>\n"
	."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
	."      <tr>\n"
	."        <th align='left' class='downloadTableHeadings_nosort'><a name='listeners'></a>Maps showing Listener Locations</th>\n"
	."        <th align='right' class='downloadTableHeadings_nosort'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small></th>\n"
	."      </tr>\n"
	."    </table></th>\n"
	."  </tr>\n"
	."  <tr>\n"
	."    <td class='downloadTableContent'>\n"
	."      <li><a href='javascript:listener_map(1)'><b>RNA</b> (North &amp; Central America + Hawaii)</a></li>\n"
	."      <li><a href='javascript:listener_map(2)'><b>REU</b> (Europe)</a></li>\n"
	."    </td>\n"
	."  </tr>\n"
	."</table>"
	;
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session  & !READONLY) {
    $sql =
       "SELECT\n"
      ."  MAX(`log_latest`) as `log_latest`\n"
	  ."FROM\n"
	  ."  `listeners`\n"
	  ."WHERE\n"
	  .($region_SQL !="" ? "  $region_SQL AND\n" : "")
	  ."  `primary_QTH` = '1'";
    $result = @mysql_query($sql);
    $row = mysql_fetch_array($result,MYSQL_ASSOC);
    $log_latest = $row['log_latest'];
    $sql =
       "SELECT\n"
      ."  `name`\n"
      ."FROM\n"
      ."  `listeners`\n"
      ."WHERE\n"
	  .($region_SQL !="" ? "  $region_SQL AND\n" : "")
	  ."  `primary_QTH` = '1' AND\n"
      ."  `log_latest` = \"".$row['log_latest']."\"";
    $result = mysql_query($sql);
    $latest_arr = array();
    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row = mysql_fetch_array($result,MYSQL_ASSOC);
      $latest_arr[] = $row['name'];
    }
    $out[] =
       "<br><small><b>Latest Log".(count($latest_arr)==1 ? "" : "s").": $log_latest</b> for "
      .implode(", ",$latest_arr)
      ."</small><br><br>\n"
      ."<table cellpadding='0' cellspacing='0' border='0'>\n"
      ."  <tr>\n"
      ."    <td nowrap valign='top'><small>\n"
      ."<b>Add log for:</b> [&nbsp;</small></td><td><small><nobr>\n";

    $sql =
       "SELECT\n"
      ."  `listeners`.`ID`,\n"
      ."  `listeners`.`name`,\n"
      ."  `listeners`.`log_latest`,\n"
      ."  `listeners`.`SP`,\n"
      ."  `listeners`.`ITU`\n"
	  ."FROM\n"
	  ."  `listeners`\n"
	  ."WHERE\n"
	  .($region_SQL !="" ? "  $region_SQL AND\n" : "")
	  ."  `primary_QTH` = '1'\n"
	  ."ORDER BY\n"
      ."  `log_latest` DESC\n"
      ."LIMIT 0,$listener_list_limit";
//    z($sql);

    $result = 	mysql_query($sql);
    $listener_arr = array();

    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $listener_arr[] =	mysql_fetch_array($result,MYSQL_ASSOC);
    }
    usort($listener_arr,"listener_name_sort");

    $listener_links = array();
    foreach ($listener_arr as $row){
      $listener_links[] =
         "<a href='javascript:log_upload(\"".$row['ID']."\")'>"
        ."<b><span title='(latest log: ".$row['log_latest'].")'>".$row['name']."</span></b></a>"
        ." (".($row['SP']!='' ? $row['SP'] : $row['ITU']).")\n";
    }
    $out[] =
       implode($listener_links," | </nobr><nobr>\n")
      ." ] (last $listener_list_limit contributors)</nobr></small></b></td></tr></table><br>";
  }
  else {
    if (READONLY) {
      $out[] = "<br><h3 style='margin: 0;'>Admin Notice:</h3><br>This system is currently in 'Read Only' mode - please don't try to add any logs right now.<br><br>";
    }
  }

  if ($region_SQL=="") {
    $sql =	 "SELECT\n"
		."  `listeners`.*\n"
		."FROM\n"
		."  `listeners`\n"
		.($filter ? "WHERE\n  ($filter_SQL)\n" : "")
		.($sortBy_SQL ? " ORDER BY $sortBy_SQL" : "");
  }
  else {
    $sql =	 "SELECT\n"
		."  `listeners`.*\n"
		."FROM\n"
		."  `listeners`\n"
		."WHERE\n"
		."  $region_SQL\n"
		.($filter ? " AND ($filter_SQL)" : "")
		.($sortBy_SQL ? " ORDER BY $sortBy_SQL" : "");
  }
  //$out[] =	"<pre>$sql</pre>";

  $result = 	@mysql_query($sql);

  $out[] =	 "<input type='hidden' name='mode' value='$mode'>\n"
		."<input type='hidden' name='submode' value=''>\n"
		."<input type='hidden' name='targetID' value=''>\n"
		."<input type='hidden' name='sortBy' value='$sortBy'>\n"
		."<table cellpadding='2' border='0' cellspacing='1'>\n"
		."  <tr>\n"
		."    <td align='center' valign='top' colspan='2'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
		."      <tr>\n"
		."        <td width='18'><img class='noprint' src='../assets/corner_top_left.gif' width='15' height='18' alt='' /></td>\n"
		."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Customise Report</td>\n"
		."        <td width='18'><img class='noprint' src='../assets/corner_top_right.gif' width='15' height='18' alt='' /></td>\n"
		."      </tr>\n"
		."    </table>\n"
		."    <table cellpadding='0' cellspacing='0' class='tableForm' border='1' bordercolor='#c0c0c0'>\n"
		."      <tr class='rowForm'>\n"
		."        <th align='left'>Search for &nbsp;</th>\n"
		."        <td nowrap><input type='text' name='filter' size='30' value='".stripslashes($filter)."' class='formfield'> "
		.(mysql_num_rows($result)==$total ? "(Showing all $total listeners)" : "(Showing ".mysql_num_rows($result)." of $total listeners)")
		."&nbsp;</td>\n"
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
		."      </tr>\n";
  if (system=="RWW") {
    $out[] =	 "      <tr class='rowForm'>\n"
		."        <th align='left'>Continent&nbsp;</th>\n"
		."        <th align='left'>\n"
		."<select name='region' onchange='document.form.go.disabled=1;document.form.submit()' class='formField' style='width: 100%;'>\n"
		.get_region_options_list($region,"(All Continents)")
		."</select>"
		."</th>"
		."      </tr>\n";
  }
  $out[] =	 "      <tr class='rowForm noprint'>\n"
		."        <th colspan='2' align='center'>\n"
		."<input type='submit' name='go' value='&nbsp;Go&nbsp;' onclick='document.form.go.disabled=1;document.form.submit()' class='formButton' title='Execute search'> "
		."<input type='submit' name='map' value='&nbsp;Map&nbsp;' onclick='listener_map()' class='formButton' title='Show map of listener locations'><br>\n"
		."</th>\n"
		."      </tr>\n"
		."    </table></td>"
		."  </tr>"
		."</table>";



  if (mysql_num_rows($result)) {
    $out[] =	"<table cellpadding='2' cellspacing='1' border='0' bgcolor='#ffffff' class='downloadtable'>\n"
		."  <thead>\n"
		."  <tr height='75'>\n"
		."    ".show_sortable_column_head("Sort by Name","Name",$sortBy,"name","A-Z",false)
		.(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session && !READONLY ?
		    "    <th class=\"downloadTableHeadings_nosort\" valign=\"bottom\" align=\"left\">Log</th>\n" :
		    "")
		."    ".show_sortable_column_head("Sort by Callsign","Callsign",$sortBy,"callsign","A-Z",false)
		."    ".show_sortable_column_head("Sort by Location","QTH",$sortBy,"QTH","A-Z",false)
		."    ".show_sortable_column_head("Sort by State / Province","S/P",$sortBy,"SP","A-Z",false)
		."    ".show_sortable_column_head("Sort by Country","ITU",$sortBy,"ITU","A-Z",false)
		.(system=="RWW" || system=="RNA" ?
    		    "    ".show_sortable_column_head("Sort by Continent","<img src='../assets/txt_continent.gif' alt='Continent'>",$sortBy,"region","A-Z",true):
		    "")
		."    ".show_sortable_column_head("Sort by GSQ Grid Locator Square","GSQ",$sortBy,"GSQ","A-Z",false)
		."    ".show_sortable_column_head("Sort by Time zone (relative to UTC)","<img src='../assets/txt_timezone.gif' alt='Timezone'>",$sortBy,"timezone","A-Z",true)
		."    ".show_sortable_column_head("Sort by Loggings","Logs",$sortBy,"count_logs","Z-A",false)
		."    ".show_sortable_column_head("Sort by Latest log","Latest Log",$sortBy,"log_latest","Z-A",false)

		.($type_DGPS ?   "    ".show_sortable_column_head("Sort by DGPS count","<img src='../assets/txt_DGPS.gif' alt='DGPS'>",$sortBy,"count_DGPS","Z-A",true) : "")

		.($type_HAMBCN ? "    ".show_sortable_column_head("Sort by Amateur Radio signal count","<img src='../assets/txt_HAMBCN.gif' alt='HAM'>",$sortBy,"count_HAMBCN","Z-A",true) : "")
		.($type_NAVTEX ? "    ".show_sortable_column_head("Sort by NAVTEX station count","<img src='../assets/txt_NAVTEX.gif' alt='NAVTEX'>",$sortBy,"count_NAVTEX","Z-A",true) : "")
		.($type_NDB ?    "    ".show_sortable_column_head("Sort by NDB count","<img src='../assets/txt_NDB.gif' alt='NDB'>",$sortBy,"count_NDB","Z-A",true) : "")
		.($type_OTHER ?  "    ".show_sortable_column_head("Sort by Other stations count","<img src='../assets/txt_OTHER.gif' alt='OTHER'>",$sortBy,"count_OTHER","Z-A",true) : "")
		.($type_TIME ?   "    ".show_sortable_column_head("Sort by Time Station count","<img src='../assets/txt_TIME.gif' alt='Time signal station'>",$sortBy,"count_TIME","Z-A",true) : "")
		.(strlen($type_NDB.$type_DGPS.$type_TIME.$type_HAMBCN.$type_NAVTEX.$type_OTHER)>1 ?
		    "    ".show_sortable_column_head("Sort by Total signals received","Total",$sortBy,"count_signals","Z-A",false) :
		    "")
		."    ".show_sortable_column_head("Sort by Web Site","Web",$sortBy,"WWW","A-Z",false)
		."    ".show_sortable_column_head("Sort by NDB WebLog","NWL",$sortBy,"NDBWebLog","A-Z",false)
		.(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
            "    ".show_sortable_column_head("Sort by Map Position","Map Pos",$sortBy,"map_x","A-Z",false)
		   ."    <th class='downloadTableHeadings_nosort' valign='bottom'>Admin</th>\n" :
		    "")
		."  </tr>\n"
		."  </thead>\n";

    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
      $notes =	stripslashes($row["notes"]);
      $out[] =	"<tr class='rownormal'>";
      $out[] =	"<td>".($row['primary_QTH'] ? "" : "&nbsp; &nbsp; &nbsp;")."<a onmouseover=\"window.status='View details and log history for ".addslashes($row["name"])." (".addslashes($row["QTH"])." ".addslashes($row["SP"]).")';return true;\" onmouseout='window.status=\"\";return true;' href=\"javascript:listener_edit('".$row["ID"]."')\" title='Show Details'>".($row['primary_QTH'] ? "<b>".highlight(stripslashes($row["name"]),$filter)."</b>": highlight(stripslashes($row["name"]),$filter))."</a></td>";
      if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session && !READONLY) {
        $out[] =	"<td nowrap><a href='javascript:log_upload(\"".$row["ID"]."\")'>Add...</a></td>\n";
      }
      $out[] =	"<td>".($row["callsign"] ? "<a href='http://www.qrz.com/callsign?callsign=".$row["callsign"]."' target='_blank' onmouseover=\"window.status='Lookup callsign ".addslashes($row["callsign"])." at QRZ';return true;\" onmouseout='window.status=\"\";return true;' title='Lookup callsign at QRZ.com'>".highlight($row["callsign"],$filter)."</a>" : "&nbsp;")."</td>\n";
      $out[] =	"<td>".highlight($row["QTH"],$filter)."</td>\n";
      $out[] =	"<td>".($row["SP"] ? $row["SP"] : "&nbsp;")."</td>\n";
      $out[] =	"<td>".($row["ITU"] ? $row["ITU"] : "&nbsp;")."</td>\n";
      $out[] =	(system=="RWW" || system=="RNA" ? "<td>".($row["region"] ? strtoupper($row["region"]) : "&nbsp;")."</td>\n" : "");
      $out[] =	"<td>".($row["GSQ"] ? "<a onmouseover=\"window.status='View map for ".addslashes($row["name"])." (".addslashes($row["QTH"])." ".addslashes($row["SP"]).")';return true;\" onmouseout='window.status=\"\";return true;' href='javascript:popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show map (accuracy limited to nearest Grid Square)'><span class='fixed'>".$row["GSQ"]."</span></a>" : "&nbsp;")."</td>\n";
      $out[] =	"<td align='right'>".($row["timezone"]!="" ? $row["timezone"] : "&nbsp;")."</td>\n";
//      $out[] =	"<td>".($notes ? $notes : "&nbsp;")."</td>\n";
      $out[] =	"<td align='right'>".($row["count_logs"] ? "<a onmouseover=\"window.status='View log history for ".addslashes($row["name"])." (".addslashes($row["QTH"])." ".addslashes($row["SP"]).")';return true;\" onmouseout='window.status=\"\";return true;' href=\"javascript:listener_log('".$row["ID"]."')\" title='Show Log'><b>".$row["count_logs"]."</b></a>" : "&nbsp;")."</td>\n";
      $out[] =	"<td nowrap>".($row["log_latest"]!="0000-00-00" ? $row["log_latest"] : "&nbsp;")."</td>\n";
      if ($type_DGPS) {
        $out[] =	"<td bgcolor='#00D8FF' nowrap align='right'>".($row["count_DGPS"]   ? $row["count_DGPS"] :   "&nbsp;")."</td>";
      }
      if ($type_HAMBCN) {
        $out[] =	"<td bgcolor='#B8FFC0' nowrap align='right'>".($row["count_HAMBCN"] ? $row["count_HAMBCN"] : "&nbsp;")."</td>";
      }
      if ($type_NAVTEX) {
        $out[] =	"<td bgcolor='#FFB8D8' nowrap align='right'>".($row["count_NAVTEX"] ? $row["count_NAVTEX"] : "&nbsp;")."</td>";
      }
      if ($type_NDB) {
        $out[] =	"<td bgcolor='#FFFFFF' nowrap align='right'>".($row["count_NDB"]    ? $row["count_NDB"] :    "&nbsp;")."</td>";
      }
      if ($type_OTHER) {
        $out[] =	"<td bgcolor='#B8F8FF' nowrap align='right'>".($row["count_OTHER"]  ? $row["count_OTHER"] :  "&nbsp;")."</td>";
      }
      if ($type_TIME) {
        $out[] =	"<td bgcolor='#FFE0B0' nowrap align='right'>".($row["count_TIME"]   ? $row["count_TIME"] :   "&nbsp;")."</td>";
      }

      if (strlen($type_NDB.$type_DGPS.$type_TIME.$type_HAMBCN.$type_NAVTEX.$type_OTHER)>1) {
        $out[] =	"<td align='right'>".($row["count_signals"] ? "<a onmouseover=\"window.status='View signals logged by ".addslashes($row["name"])." (".addslashes($row["QTH"])." ".addslashes($row["SP"]).")';return true;\" onmouseout='window.status=\"\";return true;' href=\"javascript:listener_signals('".$row["ID"]."')\" title='Show all signals received'><b>".$row["count_signals"]."</b></a>" : "&nbsp;")."</td>\n";
      }
      $out[] =	"<td>".($row["website"]!="" ? "<a title='View Web Page for this listener' href='".$row["website"]."' target='_blank'>WWW</a>" : "&nbsp;")."</td>\n";
      $out[] =	"<td>".($row["count_signals"]!=0 ? "<a title='View NDB WebLog for this listener -\nthis may take a while to load' href='$script?mode=export_ndbweblog_index&amp;ID=".$row['ID']."' target='_blank'>NWL</a>" : "&nbsp;")."</td>\n";

      if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
        $out[] =	"<td>".$row["map_x"].",".$row["map_y"]."</td>\n";
        $out[] =	"<td><a href='javascript:if(confirm(\"CONFIRM\\n\\nDelete this listener?\")){ document.form.submode.value=\"delete\";document.form.targetID.value=\"".$row["ID"]."\";document.form.submit();}'>Delete</a></td>\n";
      }
      $out[] =	"</tr>";
    }
    $out[] =	"</table>\n";
  }
  else {
    $out[] =	"<p>No results matched query for this region</p>\n";
  }
  $out[] =	"<p>\n";
  $out[] =	"<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 120px;'> ";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out[] =	"<input type='button' value='Add listener...' style='width: 120px;' onclick='listener_edit(\"\",document.form.filter.value)' class='formbutton'>\n";
  }
  $out[] =	"</p>\n";
  $out[] =	"</form>\n";
  if (isset($error_msg)) {
    $out[] =	"<script language='javascript' type='text/javascript'>window.setTimeout('alert(\"$error_msg\")',1000);</script>\n";
  }
  return implode($out,"");
}


// ************************************
// * listener_log()                   *
// ************************************
function listener_log() {
  global $ID, $mode, $script, $submode, $sortBy, $targetID, $limit, $offset;
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    switch ($submode) {
      case "delete":
        log_delete($targetID);
      break;
    }
  }
  $Obj = new Listener($ID);
  $row =	$Obj->get_record();
  $name =	$row["name"];
  $signals =	$row["count_signals"];
  $logs =	$row["count_logs"];
  $GSQ =	$row["GSQ"];
  $QTH =	$row["QTH"];
  $SP =		$row["SP"];
  $ITU =	$row["ITU"];
  if ($sortBy=="") {
    $sortBy = "date";
  }
  if (empty($limit))  { $limit = 100; } else { $limit = (int) $limit; }
  if (empty($offset)) { $offset = 0; } else { $offset = (int) $offset; }
  if ($offset<0) { $offset=0; }
  $total = $Obj->get_log_count();
  if ($limit >= $total) {
    $limit = -1;
  }
  $sortBy_SQL =		"";
  switch ($sortBy) {
    case "call":	$sortBy_SQL =	"`call` ASC, `khz` ASC";			break;
    case "call_d":	$sortBy_SQL =	"`call` DESC, `khz` ASC";			break;
    case "date":	$sortBy_SQL =	"`date` DESC, `time` ASC";			break;
    case "date_d":	$sortBy_SQL =	"`date` ASC, `time` ASC";			break;
    case "dx":		$sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL,`dx_km` ASC";	break;
    case "dx_d":	$sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL,`dx_km` DESC";	break;
    case "gsq":		$sortBy_SQL =	"`GSQ`='' OR `GSQ` IS NULL,`GSQ` ASC";		break;
    case "gsq_d":	$sortBy_SQL =	"`GSQ`='' OR `GSQ` IS NULL,`GSQ` DESC";		break;
    case "itu":		$sortBy_SQL =	"`ITU` ASC, `signals`.`SP` ASC, `khz` ASC, `call` ASC";	break;
    case "itu_d":	$sortBy_SQL =	"`ITU` DESC, `signals`.`SP` ASC, `khz` ASC, `call` ASC";break;
    case "khz":		$sortBy_SQL =	"`khz` ASC, `call` ASC";			break;
    case "khz_d":	$sortBy_SQL =	"`khz` DESC, `call` ASC";			break;
    case "sp":		$sortBy_SQL =	"`signals`.`SP`='',`signals`.`SP` ASC,`ITU` ASC, `khz` ASC, `call` ASC";	break;
    case "sp_d":	$sortBy_SQL =	"`signals`.`SP`='',`signals`.`SP` DESC,`ITU` ASC, `khz` ASC, `call` ASC";	break;
    case "time":	$sortBy_SQL =	"`time` IS NULL,`time` ASC";			break;
    case "time_d":	$sortBy_SQL =	"`time` IS NULL,`time` DESC";			break;
    case "LSB":		$sortBy_SQL =	"`logs`.`LSB` IS NULL, `logs`.`LSB` ASC";	break;
    case "LSB_d":	$sortBy_SQL =	"`logs`.`LSB` IS NULL, `logs`.`LSB` DESC";	break;
    case "USB":		$sortBy_SQL =	"`logs`.`USB` IS NULL, `logs`.`USB` ASC";	break;
    case "USB_d":	$sortBy_SQL =	"`logs`.`USB` IS NULL, `logs`.`USB` DESC";	break;
  }


  $out =	array();
  $out[] =
     "<script type='text/javascript'>\n"
    ."function send_form(frm) {\n"
    ."  frm.submit();\n"
    ."}\n"
    ."</script>\n"
    ."<form action='$script' name='form' method='POST' onsubmit='if (window.opener) { window.opener.location.reload(1)};return true;'>\n"
    ."<input type='hidden' name='ID' value='$ID'>\n"
    ."<input type='hidden' name='mode' value='$mode'>\n"
    ."<input type='hidden' name='submode' value='$submode'>\n"
    ."<table border='0' cellpadding='0' cellspacing='0'>\n"
    ."  <tr>\n"
    ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
    ."      <tr>\n"
    ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%' class='noprint'>\n"
    ."          <tr>\n"
    ."            <td><h1>Listener</h1></td>\n"
    ."            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
    ."              <tr>\n"
    .$Obj->tabs()
    ."              </tr>\n"
    ."            </table></td>\n"
    ."          </tr>\n"
    ."        </table>\n"
    ."        <table border='0' align='center' cellpadding='0' cellspacing='0' class='tableContainer' width='100%'>\n"
    ."          <tr class='rowForm'>\n"
    ."            <td nowrap style='padding-left:10px;padding-top:2px;'><b>Show</b> ".show_page_bar($total, $limit, $offset, 1,1,1)."</td>\n"
    ."          </tr>"
    ."          <tr>\n"
    ."            <td bgcolor='#F5F5F5' class='itemTextCell' height='325' valign='top'>\n"
    ."            <table width='100%'  border='0' cellpadding='2' cellspacing='1' class='downloadTable' class='noprint'>\n"
    ."              <tr>\n"
    ."                <th class='downloadTableHeadings_nosort' align='left'><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td  class='downloadTableHeadings_nosort'>&nbsp;Logs for $name".($QTH ? ", $QTH" : "").($SP ? ", $SP" : "").($ITU ? ", $ITU" : "")."</td><td class='downloadTableHeadings_nosort' align='right'><span style='font-weight: normal'>(daytime logs in <b>bold</b>)</span></td></tr></table></th>\n"
    ."              </tr>\n"
    ."              <tr>\n"
    ."                <td class='rownormal'><table cellpadding='1' cellspacing='0' border='0' class='noprint'>\n"
    ."                  <thead>\n"
    ."                  <tr>\n"
    ."                    <th class='scroll_list' width='70' title='YYYY-MM-DD (this logging)' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=date".($sortBy=='date' ? '_d' : '')."'>".($sortBy=='date'||$sortBy=='date_d'?'<font color="#ff0000">Date</font>':'Date')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='35' title='hhmm (this logging)' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=time".($sortBy=='time' ? '_d' : '')."'>".($sortBy=='time'||$sortBy=='time_d'?'<font color="#ff0000">UTC</font>':'UTC')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='55' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=khz".($sortBy=='khz' ? '_d' : '')."'>".($sortBy=='khz'||$sortBy=='khz_d'?'<font color="#ff0000">KHZ</font>':'KHZ')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='65' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=call".($sortBy=='call' ? '_d' : '')."'>".($sortBy=='call'||$sortBy=='call_d'?'<font color="#ff0000">ID</font>':'ID')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='25' title='State / Province for this signal' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=sp".($sortBy=='sp' ? '_d' : '')."'>".($sortBy=='sp'||$sortBy=='sp_d'?'<font color="#ff0000">SP</font>':'SP')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='30' title='Country for this signal' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=itu".($sortBy=='sp' ? '_d' : '')."'>".($sortBy=='itu'||$sortBy=='itu_d'?'<font color="#ff0000">ITU</font>':'ITU')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='50' title='Grid Square for this signal' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=gsq".($sortBy=='gsq' ? '_d' : '')."'>".($sortBy=='gsq'||$sortBy=='gsq_d'?'<font color="#ff0000">GSQ</font>':'GSQ')."</a></small></th>\n"
    .(system!="RWW" ?
        "                    <th class='scroll_list' width='30' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=LSB".($sortBy=='LSB' ? '_d' : '')."'>".($sortBy=='LSB'||$sortBy=='LSB_d'?'<font color="#ff0000">LSB</font>':'LSB')."</a></small></th>\n"
       ."                    <th class='scroll_list' width='30' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=USB".($sortBy=='USB' ? '_d' : '')."'>".($sortBy=='USB'||$sortBy=='USB_d'?'<font color="#ff0000">USB</font>':'USB')."</a></small></th>\n"
       ."                    <th class='scroll_list' width='40' align='center'><a href='$script?mode=$mode&ID=$ID&sortBy=sec".($sortBy=='sec' ? '_d' : '')."'>".($sortBy=='sec'||$sortBy=='sec_d'?'<font color="#ff0000">Sec</font>':'Sec')."</a></small></th>\n"
       ."                    <th class='scroll_list' width='45' align='right'><a href='$script?mode=$mode&ID=$ID&sortBy=format".($sortBy=='format' ? '_d' : '')."'>".($sortBy=='format'||$sortBy=='format_d'?'<font color="#ff0000">Format</font>':'Format')."</a></small></th>\n"
     : "")
    ."                    <th class='scroll_list' width='40' align='right'><small><a href='$script?mode=$mode&ID=$ID&sortBy=dx".($sortBy=='dx' ? '_d' : '')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">KM</font>':'KM')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='40' align='right'><small><a href='$script?mode=$mode&ID=$ID&sortBy=dx".($sortBy=='dx' ? '_d' : '')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">Miles</font>':'Miles')."</a></small></th>\n"
    .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
        "                    <th class='scroll_list' width='5' align='left'><small>X</small></th>\n"
     : "")
    ."                  </tr>\n"
    ."                  </thead>\n"
    ."                </table></td>\n"
    ."              </tr>\n"
    ."              <tr>\n"
    ."                <td class='rownormal' bgcolor='#ffffff'>\n"
    ."                <div class='scrollbox_230' style='width:100%;'>\n"
    ."                <table cellpadding='1' cellspacing='0' border='0' bgcolor='#ffffff'>\n"
    ."                  <thead>\n"
    ."                  <tr class='noscreen'>\n"
    ."                    <th class='scroll_list'><small>Date</small></th>\n"
    ."                    <th class='scroll_list'><small>UTC</small></th>\n"
    ."                    <th class='scroll_list'><small>KHZ</small></th>\n"
    ."                    <th class='scroll_list'><small>ID</small></th>\n"
    ."                    <th class='scroll_list'><small>SP</small></th>\n"
    ."                    <th class='scroll_list'><small>ITU</small></th>\n"
    ."                    <th class='scroll_list'><small>GSQ</small></th>\n"
    .(system!="RWW" ?
       "                    <th class='scroll_list'><small>LSB</small></th>\n"
      ."                    <th class='scroll_list'><small>USB</small></th>\n"
      ."                    <th class='scroll_list'><small>Sec</small></th>\n"
      ."                    <th class='scroll_list'><small>Format</small></th>\n"
    : "")
    ."                    <th class='scroll_list'><small>KM</small></th>\n"
    ."                    <th class='scroll_list'><small>Miles</small></th>\n"
    .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
       "                    <th class='scroll_list'><small>X</small></th>\n"
     : "")
    ."                  </tr>\n"
    ."                  </thead>\n";

  $log_arr = $Obj->get_log($sortBy_SQL,$limit,$offset);

  foreach ($log_arr as $row) {
    $bgcolor =	"";
    if (!$row["active"]) {
      $bgcolor =	" bgcolor='#d0d0d0' title='(Reportedly off air or decommissioned)'";
    }
    else {
      switch ($row["type"]) {
        case NDB:	$bgcolor = "";								break;
        case DGPS:	$bgcolor = " bgcolor='#00D8ff' title='DGPS Station'";			break;
        case TIME:	$bgcolor = " bgcolor='#FFE0B0' title='Time Signal Station'";		break;
        case NAVTEX:	$bgcolor = " bgcolor='#FFB8D8' title='NAVTEX Station'";			break;
        case HAMBCN:	$bgcolor = " bgcolor='#D8FFE0' title='Amateur signal'";			break;
        case OTHER:	$bgcolor = " bgcolor='#B8F8FF' title='Other form of transmission'";	break;
      }
    }

    $out[] =
       "<tr$bgcolor>"
      ."<td class='scroll_list' width='70' nowrap>".$row["date"]."</td>\n"
      ."<td class='scroll_list' width='35'>".($row['daytime'] ? "<b>".$row["time"]."</b>" : $row["time"])."</td>\n"
      ."<td class='scroll_list' width='55'>".(float) $row["khz"]."</td>\n"
      ."<td class='scroll_list' width='65'><a href='javascript:signal_log(".$row["signalID"].")'><b>".$row["call"]."</b></a></td>\n"
      ."<td class='scroll_list' width='25'>".$row["signalSP"]."</td>\n"
      ."<td class='scroll_list' width='30'>".$row["ITU"]."</td>\n"
      ."<td class='scroll_list' width='50'>".$row["GSQ"]."</td>\n"
      .(system!="RWW" ?
          "<td class='scroll_list' width='30' align='right'>".($row["LSB"] ? $row["LSB_approx"].$row["LSB"] : "&nbsp;")."</td>\n"
         ."<td class='scroll_list' width='30' align='right'>".($row["USB"] ? $row["USB_approx"].$row["USB"] : "&nbsp;")."</td>\n"
         ."<td class='scroll_list' width='40' align='right'>".($row["sec"]  ? $row["sec"]  : "&nbsp;")."</td>\n"
         ."<td class='scroll_list' width='45' align='right'>".($row["format"]  ? $row["format"]  : "&nbsp;")."</td>\n"
       : "")
      ."<td class='scroll_list' width='40' align='right'>".($row["dx_km"]? $row["dx_km"] : "&nbsp;")."</td>\n"
      ."<td class='scroll_list' width='40' align='right'>".($row["dx_miles"]? $row["dx_miles"] : "&nbsp;")."</td>\n"
      .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<td class='scroll_list' width='5'><a href='$script?mode=$mode&submode=delete&ID=$ID&targetID=".$row["ID"]."'>X</a></td>\n" : "")
      ."</tr>\n";
  }
  $out[] =
     "                  </div></table></td>\n"
    ."              </tr>\n"
    ."              <tr>\n"
    ."                <td class='rownormal' align='center'>\n"
    ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
    ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'>"
    ."</td>\n"
    ."              </tr>\n"
    ."            </table></td>\n"
    ."          </tr>\n"
    ."        </table></td>\n"
    ."      </tr>\n"
    ."    </table></td>\n"
    ."  </tr>\n"
    ."</table>\n"
    ."</form>";
  return implode($out,"");
}


// ************************************
// * listener_log_export()            *
// ************************************
function listener_log_export() {
  global $ID, $mode, $script, $submode, $sortBy, $targetID;

  $Obj =    new Listener($ID);
  $row =	$Obj->get_record();
  $name =	$row["name"];
  $signals =	$row["count_signals"];
  $logs =	$row["count_logs"];
  $GSQ =	$row["GSQ"];
  $QTH =	$row["QTH"];
  $SP =		$row["SP"];
  $ITU =	$row["ITU"];
  return
     "<table border='0' cellpadding='0' cellspacing='0'>\n"
    ."  <tr>\n"
    ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
    ."      <tr>\n"
    ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%' class='noprint'>\n"
    ."          <tr>\n"
    ."            <td><h1>Listener</h1></td>\n"
    ."            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
    ."              <tr>\n"
    .$Obj->tabs()
    ."              </tr>\n"
    ."            </table></td>\n"
    ."          </tr>\n"
    ."        </table>\n"
    ."        <table border='0' align='center' cellpadding='0' cellspacing='0' class='tableContainer' width='100%'>\n"
    ."          <tr>\n"
    ."            <td bgcolor='#F5F5F5' class='itemTextCell' height='325' valign='top'><table cellpadding='2' border='0' cellspacing='1' class='downloadtable' width='100%'>\n"
    ."              <tr>\n"
    ."                <th class='downloadTableHeadings_nosort' colspan='2'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."                  <tr>\n"
    ."                    <th align='left' class='downloadTableHeadings_nosort'>Export Loggings</th>\n"
    ."                  </tr>\n"
    ."                </table></th>\n"
    ."              </tr>\n"
    ."              <tr>\n"
    ."                <td class='downloadTableContent' valign='top'><BR>Click to access logs in these formats:<ul>\n"
    ."<li><a href='".system_URL."/export_text_log?ID=".$ID."' target='_blank'>Text listing showing all logs</a></li>\n"
    ."<li><a href='".system_URL."/export_text_signals?ID=".$ID."' target='_blank'>Text listing showing all signals</a></li>\n"
    ."<li><a href='".system_URL."/export_kml_signals?ID=".$ID."'>Google Maps KML format showing all signals</a></li>\n"
    ."<li><a href='".system_URL."/export_ndbweblog?ID=".$ID."' target='_blank'>NDB WebLog Files</a></li>\n"
    ."<li><a href='".system_URL."/export_ndbweblog_index?ID=".$ID."' target='_blank'>Use NDB WebLog for this listener</a></li>\n"
    ."</ul></td>\n"
    ."              </tr>\n"
    ."            </table></td>\n"
    ."          </tr>\n"
    ."        </table></td>\n"
    ."      </tr>\n"
    ."    </table>\n";
}



// ************************************
// * listener_map()                   *
// ************************************
function listener_map() {
  global $ID, $system_ID, $script, $mode, $sortBy;
  $out =	array();

  switch ($system_ID) {
    case "1":	$region='na'; $region_SQL= "(`listeners`.`region`='na' OR `listeners`.`region`='ca' OR (`listeners`.`region`='oc' AND `listeners`.`itu` = 'hwa'))";	break;
    case "2":	$region='eu'; $region_SQL= "`listeners`.`region`='eu'";	break;
  }

  switch($sortBy) {
    case 'location':
      $sortBy_SQL =	"`itu`,`sp`,`name`,`primary_QTH` DESC";
    break;
    default:
      $sortBy_SQL =	"`name`,`primary_QTH` DESC,`itu`,`sp`";
    break;
  }
  $sql =	"SELECT DISTINCT\n"
		."  `listeners`.`ID` as `listenerID`,\n"
		."  `QTH`,\n"
		."  `sp`,\n"
		."  `itu`,\n"
		."  `map_x`,\n"
		."  `map_y`,\n"
		."  `name`,\n"
		."  `primary_QTH`\n"
		."FROM\n"
		."  `listeners`\n"
		."WHERE\n"
		."  $region_SQL AND\n"
		."  `listeners`.`map_x` IS NOT NULL\n"
		."GROUP BY\n"
		."  `listeners`.`ID`\n"
		."ORDER BY\n"
		."  $sortBy_SQL";
//  $out[] = "<pre>$sql</pre>";

  $out[] =	 "<form name='form' action='$script' method='POST'>\n"
		."<input type='hidden' name='system_ID' value='$system_ID'>\n"
		."<input type='hidden' name='mode' value='$mode'>\n"
		."<table cellpadding='0' cellspacing='0' border='0'>\n"
		."  <tr>\n"
		."    <td valign='top'>"
		."<img ID='listenerMap' usemap=\"#map\" galleryimg=\"no\" src=\"./?mode=generate_listener_map&system_ID=$system_ID\" border=\"0\" ".($system_ID==1 ? "width=\"653\" height=\"620\"" : "width=\"688\" height=\"665\"").">\n"
		."<map name=\"map\">\n";

  $result =	@mysql_query($sql);
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    if ($row['map_x']) {
      $out[] =	"<area shape=\"circle\" href=\"javascript:listener_edit(".$row['listenerID'].")\""
		." onmouseover=\"show_name('text_".$row['listenerID']."',1);return true;\""
		." onmouseout=\"show_name('text_".$row['listenerID']."',0);return true;\""
		." title=\"".$row['name'].($row['sp'] ? " ".$row['sp']:"")." ".$row['itu'].($row['primary_QTH']=="1" ? " (Primary QTH)":"")."\""
		." coords=\"".$row['map_x'].",".$row['map_y'].",".($row['primary_QTH'] ? "4" : "2")."\">\n";
    }
  }

  $out[] =	 "</map></td>\n"
		."    <td width='30'>&nbsp;</td>\n"
		."    <td valign='top'><center><h1 align='center'>Listener Locations</h1><br>"
		."Move mouse over name to show position.<br>\n"
		."Locations in <b>bold</b> are Primary QTH.<br>\n"
		."Click on a name or point for details.<br>\n"

		."<small>[ "
		.($region=='na' ?
		    "<a href='javascript:show_itu(\"na|ca\")' title='NDBList State and Province codes'><b>Country Codes</b></a></nobr> | "
		   ."<a href='javascript:show_sp()' title='NDBList State and Province codes'><b>States</b></a></nobr>" : "")
		.($region=='eu' ?
		    "<a href='javascript:show_itu(\"eu\")' title='NDBList State and Province codes'><b>Country Codes</b></a></nobr>" : "")
		." ]</small></center><br>\n"

		."<b>Sort By:</b>\n"
		."<select name='sortBy' class='formField' onchange='document.form.submit()'>\n"
		."  <option value='name'".($sortBy=='name' ? " selected" : "").">Listener Name</option>\n"
		."  <option value='location'".($sortBy=='location' ? " selected" : "").">Listener Location</option>\n"
		."</select>\n"
		."<script language='javascript' type='text/javascript'>\n"
		."if (!isNS4)\n"
		."  document.write(\"<div class='scrollbox_230' style='height: 505; border: solid 1px #808080; background-color: #ffffff;'>\")\n"
		."</script>\n";

  $result =	@mysql_query($sql);
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    if ($row['map_x']) {
      $out[] =	"<li><a ID='text_".$row['listenerID']."' title='Location: ".$row['QTH'].($row['sp'] ? ", ".$row['sp']:"").", ".$row['itu']."\nClick for details.' href=\"javascript:listener_edit(".$row['listenerID'].")\""
		." onmouseover=\"show_point(".$row['map_x'].",".$row['map_y'].");return true;\""
		." onmouseout=\"show_point(0,0);return true;\""
		.">"
		.($row['primary_QTH']=="1" ? "<b>":"").$row['name'].($row['sp'] ? " ".$row['sp']:"")." ".$row['itu'].($row['primary_QTH']=="1" ? "</b>":"")."</li>\n";
    }
  }
  $out[] =	 "</ul></div></td>\n"
		."  </tr>\n"
		."</table>\n"
		."<script language='javascript' type='text/javascript'>\n"
		."if (isNS4)\n"
		."  document.write(\"<layer name='point_here'><img src='../assets/map_point_here.gif'></layer>\")\n"
		."else\n"
		."  document.write(\"<div ID='point_here' style='position: absolute; display: none;'><img src='../assets/map_point_here.gif'></div>\")\n"
		."</script>\n"
		."</form>\n";

  return implode($out,"");

}



// ************************************
// * listener_QNH()                   *
// ************************************
function listener_QNH() {
  global $ID, $mode, $script, $submode, $ICAO_listener, $hours_listener;
  $sql =	"select * from `listeners` where `ID` = $ID";
  $result =	mysql_query($sql);
  $row =	mysql_fetch_array($result,MYSQL_ASSOC);

  $signals =	$row["count_signals"];
  $logs =	$row["count_logs"];
  $GSQ =	$row["GSQ"];
  $name =	$row["name"];
  $QTH =	$row["QTH"];
  $SP =		$row["SP"];
  $ITU =	$row["ITU"];
  $lat =	$row["lat"];
  $lon =	$row["lon"];

  $pressure =	array();


  if ($ICAO_listener && $hours_listener) {
    if ($METAR = METAR($ICAO_listener,$hours_listener,0)) {
      $sql =		"SELECT * FROM `icao` WHERE `icao` = \"$ICAO_listener\"";
      $result =		@mysql_query($sql);
      $row =		mysql_fetch_array($result,MYSQL_ASSOC);
      $dx =		get_dx($lat,$lon,$row["lat"],$row["lon"]);
      $pressure[] =	"QNH at $ICAO_listener -\n".$row["name"].($row["SP"] ? ", ".$row["SP"] : "").", ".$row["CNT"]."\n(".$dx[0]." from QTH)\n";
      $pressure[] =	"----------------------\nDD UTC  MB     SLP \n----------------------";
      $pressure[] =	$METAR;
      $pressure[] =	"----------------------\n(From ".system.")\n";
    }
    else {
      $pressure[] =	"QNH data not available at $ICAO_listener for last $hours_listener hours";
    }
  }
  else {
    $pressure[] = "HELP\nEnter hours to display, select first valid station in list and press 'QNH'";
  }

  $out =	array();

  $out[] =	"<table border='0' cellpadding='0' cellspacing='0'>\n";
  $out[] =	"  <tr>\n";
  $out[] =	"    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n";
  $out[] =	"      <tr>\n";
  $out[] =	"        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%' class='noprint'>\n";
  $out[] =	"          <tr>\n";
  $out[] =	"            <td><h1>Listener</h1></td>\n";
  $out[] =	"            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td ".tabItem("listener_edit")." width='50'>Profile</td>\n";
  if ($signals) {
    $out[] =	"                <td ".tabItem("listener_log")." width='105'>Signals ($signals)</td>\n";
  }
  if ($logs) {
    $out[] =	"                <td ".tabItem("listener_log")." width='85'>Logs ($logs)</td>\n";
    $out[] =	"                <td ".tabItem("listener_log_export")." width='45'>Export</td>\n";
  }
  $out[] =	"                <td ".tabItem("listener_QNH")." width='35'>QNH</td>\n";
  if ($logs) {
    $out[] =	"                <td ".tabItem("listener_stats")." width='45'>Stats</td>\n";
  }
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out[] =	"                <td class='tabOff' onclick='log_upload(\"$ID\");' onMouseOver='return tabOver(this,1);' onMouseOut='return tabOver(this,0);' width='45'>Add...</td>\n";
  }
  $out[] =	"              </tr>\n";
  $out[] =	"            </table></td>\n";
  $out[] =	"          </tr>\n";
  $out[] =	"        </table>\n";
  $out[] =	"        <table border='0' align='center' cellpadding='0' cellspacing='0' class='tableContainer' width='100%'>\n";
  $out[] =	"          <tr>\n";
  $out[] =	"            <td bgcolor='#F5F5F5' class='itemTextCell' height='325' valign='top'>\n";

  $out[] =	"<form name='pressure' action='$script' method='GET'>\n";
  $out[] =	"<input type='hidden' name='ID' value='$ID'>\n";
  $out[] =	"<input type='hidden' name='mode' value='$mode'>\n";
  $out[] =	"<input type='hidden' name='submode' value=''>\n";
  $out[] =	"<table cellpadding='2' border='0' cellspacing='1' class='downloadtable' width='100%' height='100%'>\n";
  $out[] =	"  <tr>\n";
  $out[] =	"    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
  $out[] =	"      <tr>\n";
  $out[] =	"        <th align='left' class='downloadTableHeadings_nosort'>&nbsp;QNH History for $name".($QTH ? ", $QTH" : "").($SP ? ", $SP" : "").($ITU ? ", $ITU" : "")."</th>\n";
  $out[] =	"      </tr>\n";
  $out[] =	"    </table></th>\n";
  $out[] =	"  </tr>\n";

  $out[] =	"  <tr>\n";
  $out[] =	"    <td class='downloadTableContent' height='100%'>";
  $out[] =	"<select class='formFixed' name='ICAO_listener'>\n";

  $icao_arr =	get_local_icao($GSQ,10,$ICAO_listener);
  for ($i=0; $i<10; $i++) {
    $out[] =	"<option value='".$icao_arr[$i]['ICAO']."'".($ICAO_listener==$icao_arr[$i]['ICAO'] ? " selected" : "").">".$icao_arr[$i]['ICAO']." (".$icao_arr[$i]['miles']." Miles, ".$icao_arr[$i]['km']." KM)</option>\n";
  }
  $out[] =	"</select>\n";
  $out[] =	"<input type='text' size='2' maxlength='2' name='hours_listener' value='".($hours_listener ? $hours_listener : "12")."' class='formField'> Hours\n";
  $out[] =	"<input type='submit' value='QNH' class='formButton'>\n";
  $out[] =	"<input type='button' value='METAR' class='formButton' onclick='popWin(\"http://adds.aviationweather.noaa.gov/metars/index.php?station_ids=\"+document.pressure.ICAO_listener.value+\"&std_trans=&chk_metars=on&hoursStr=past+\"+document.pressure.hours_listener.value+\"+hours\",\"popMETAR\",\"scrollbars=1,resizable=1,location=1\",640,380,\"\");'>\n";
  $out[] =	"<input type='button' value='Decoded' class='formButton' onclick='popWin(\"http://adds.aviationweather.noaa.gov/metars/index.php?station_ids=\"+document.pressure.ICAO_listener.value+\"&std_trans=translated&chk_metars=on&hoursStr=past+\"+document.pressure.hours_listener.value+\"+hours\",\"popDecoded\",\"scrollbars=1,resizable=1,location=1\",640,380,\"\");'><br>\n";

  $out[] =	"<textarea rows='10' cols='60' style='width: 580px; height: 90%;' class='formFixed'>".implode($pressure,"\n")."</textarea><br><br></td>\n";
  $out[] =	"  </tr>\n";
  $out[] =	"</table></form></td>\n";
  $out[] =	"          </tr>\n";
  $out[] =	"        </table></form></td>\n";
  $out[] =	"      </tr>\n";
  $out[] =	"    </table></td>\n";
  $out[] =	"  </tr>\n";
  $out[] =	"</table>\n";
  return implode($out,"");
}


// ************************************
// * listener_signals()               *
// ************************************
function listener_signals() {
  global $ID, $mode, $script, $submode, $sortBy, $targetID;

  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    switch ($submode) {
      case "delete":
        log_delete($targetID);
      break;
    }
  }

  $Obj = new Listener($ID);
  $row =	$Obj->get_record();
  $name =	$row["name"];
  $signals =	$row["count_signals"];
  $logs =	$row["count_logs"];
  $GSQ =	$row["GSQ"];
  $QTH =	$row["QTH"];
  $SP =		$row["SP"];
  $ITU =	$row["ITU"];


  if ($sortBy=="") {
    $sortBy = "khz";
  }

  $sortBy_SQL =		"";

  switch ($sortBy) {
    case "dx":		$sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL, `dx_km` ASC";	break;
    case "dx_d":	$sortBy_SQL =	"`dx_km`='' OR `dx_km` IS NULL, `dx_km` DESC";	break;
    case "call":	$sortBy_SQL =	"`call` ASC, `khz` ASC";			break;
    case "call_d":	$sortBy_SQL =	"`call` DESC, `khz` ASC";			break;
    case "gsq":		$sortBy_SQL =	"`GSQ`='' OR `GSQ` IS NULL, `GSQ` ASC";		break;
    case "gsq_d":	$sortBy_SQL =	"`GSQ`='' OR `GSQ` IS NULL, `GSQ` DESC";	break;
    case "itu":		$sortBy_SQL =	"`signals`.`ITU`='' OR `signals`.`ITU` IS NULL, `signals`.`ITU` ASC, `SP` ASC";	break;
    case "itu_d":	$sortBy_SQL =	"`signals`.`ITU`='' OR `signals`.`ITU` IS NULL, `signals`.`ITU` DESC, `SP` ASC";	break;
    case "khz":		$sortBy_SQL =	"`khz` ASC, `call` ASC";			break;
    case "khz_d":	$sortBy_SQL =	"`khz` DESC, `call` ASC";			break;
    case "latest":	$sortBy_SQL =	"`latest` ASC";					break;
    case "latest_d":	$sortBy_SQL =	"`latest` DESC";				break;
    case "logs":	$sortBy_SQL =	"`logs` ASC";					break;
    case "logs_d":	$sortBy_SQL =	"`logs` DESC";					break;
    case "sp":		$sortBy_SQL =	"`signals`.`SP`='' OR `signals`.`SP` IS NULL, `signals`.`SP` ASC, `ITU` ASC";	break;
    case "sp_d":	$sortBy_SQL =	"`signals`.`SP`='' OR `signals`.`SP` IS NULL, `signals`.`SP` DESC, `ITU` ASC";	break;
  }
  $out =
     "<table border='0' cellpadding='0' cellspacing='0'>\n"
    ."  <tr>\n"
    ."    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n"
    ."      <tr>\n"
    ."        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%' class='noprint'>\n"
    ."          <tr>\n"
    ."            <td><h1>Listener</h1></td>\n"
    ."            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n"
    ."              <tr>\n"
    .$Obj->tabs()
    ."              </tr>\n"
    ."            </table></td>\n"
    ."          </tr>\n"
    ."        </table>\n"
    ."        <table border='0' align='center' cellpadding='0' cellspacing='0' class='tableContainer' width='100%' height='100%'>\n"
    ."          <tr>\n"
    ."            <td bgcolor='#F5F5F5' class='itemTextCell' height='325' valign='top'><p></p>\n"
    ."            <form action='$script' name='form' method='POST' onsubmit='if (window.opener) { window.opener.location.reload(1)};return true;'>\n"
    ."            <input type='hidden' name='ID' value='$ID'>\n"
    ."            <input type='hidden' name='mode' value='$mode'>\n"
    ."            <input type='hidden' name='submode' value='$submode'>\n"
    ."            <table width='100%'  border='0' cellpadding='2' cellspacing='1' class='downloadTable' class='noprint'>\n"
    ."              <tr>\n"
    ."                <th class='downloadTableHeadings_nosort' align='left'>&nbsp;Signals for $name".($QTH ? ", $QTH" : "").($SP ? ", $SP" : "").($ITU ? ", $ITU" : "")."</th>\n"
    ."              </tr>\n"
    ."              <tr>\n"
    ."                <td class='rownormal'><table cellpadding='1' cellspacing='0' border='0' class='noprint'>\n"
    ."                  <thead>\n"
    ."                  <tr>\n"
    ."                    <th class='scroll_list' width='55' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=khz".($sortBy=='khz' ? '_d' : '')."'>".($sortBy=='khz'||$sortBy=='khz_d'?'<font color="#ff0000">KHZ</font>':'KHZ')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='90' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=call".($sortBy=='call' ? '_d' : '')."'>".($sortBy=='call'||$sortBy=='call_d'?'<font color="#ff0000">ID</font>':'ID')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='25' align='left' title='State / Province for this signal'><small><a href='$script?mode=$mode&ID=$ID&sortBy=sp".($sortBy=='sp' ? '_d' : '')."'>".($sortBy=='sp'||$sortBy=='sp_d'?'<font color="#ff0000">SP</font>':'SP')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='30' align='left' title='Country for this signal'><small><a href='$script?mode=$mode&ID=$ID&sortBy=itu".($sortBy=='itu' ? '_d' : '')."'>".($sortBy=='itu'||$sortBy=='itu_d'?'<font color="#ff0000">ITU</font>':'ITU')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='50' align='left' title='Grid Square for this signal'><small><a href='$script?mode=$mode&ID=$ID&sortBy=gsq".($sortBy=='gsq' ? '_d' : '')."'>".($sortBy=='gsq'||$sortBy=='gsq_d'?'<font color="#ff0000">GSQ</font>':'GSQ')."</a></small></th>\n"
    .(system!="RWW" ?
       "                    <th class='scroll_list' width='40' align='left'><a href='$script?mode=$mode&ID=$ID&sortBy=sec".($sortBy=='sec' ? '_d' : '')."'>".($sortBy=='sec'||$sortBy=='sec_d'?'<font color="#ff0000">Sec</font>':'Sec')."</a></small></th>\n"
      ."                    <th class='scroll_list' width='70' align='left'><a href='$script?mode=$mode&ID=$ID&sortBy=format".($sortBy=='format' ? '_d' : '')."'>".($sortBy=='format'||$sortBy=='format_d'?'<font color="#ff0000">Format</font>':'Format')."</a></small></th>\n"
     : ""
     )
    ."                    <th class='scroll_list' width='35' align='center'><small><a href='$script?mode=$mode&ID=$ID&sortBy=".($sortBy=='logs_d' ? 'logs' : 'logs_d')."'>".($sortBy=='logs'||$sortBy=='logs_d'?'<font color="#ff0000">Logs</font>':'Logs')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='75' align='left'><small><a href='$script?mode=$mode&ID=$ID&sortBy=".($sortBy=='latest_d' ? 'latest' : 'latest_d')."'>".($sortBy=='latest'||$sortBy=='latest_d'?'<font color="#ff0000">Latest</font>':'Latest')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='35' align='right'><small><a href='$script?mode=$mode&ID=$ID&sortBy=".($sortBy=='dx_d' ? 'dx' : 'dx_d')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">KM</font>':'KM')."</a></small></th>\n"
    ."                    <th class='scroll_list' width='35' align='right'><small><a href='$script?mode=$mode&ID=$ID&sortBy=".($sortBy=='dx_d' ? 'dx' : 'dx_d')."'>".($sortBy=='dx'||$sortBy=='dx_d'?'<font color="#ff0000">Miles</font>':'Miles')."</a></small></th>\n"
    ."                  </tr>\n"
    ."                  </thead>\n"
    ."                </table></td>\n"
    ."              </tr>\n"
    ."              <tr>\n"
    ."                <td class='rownormal' bgcolor='#ffffff'>\n"
    ."                <div class='scrollbox_230' style='width:100%;'>\n"
    ."                <table cellpadding='1' cellspacing='0' border='0' bgcolor='#ffffff'>\n"
    ."                  <thead>\n"
    ."                  <tr class='noscreen'>\n"
    ."                    <th class='scroll_list'><small>KHZ</small></th>\n"
    ."                    <th class='scroll_list'><small>ID</small></th>\n"
    ."                    <th class='scroll_list'><small>SP</small></th>\n"
    ."                    <th class='scroll_list'><small>ITU</small></th>\n"
    ."                    <th class='scroll_list'><small>GSQ</small></th>\n"
    ."                    <th class='scroll_list'><small>Sec</small></th>\n"
    .(system!="RWW" ?
       "                    <th class='scroll_list'><small>Format</small></th>\n"
      ."                    <th class='scroll_list'><small>Logs</small></th>\n"
     : ""
     )
    ."                    <th class='scroll_list'><small>Latest</small></th>\n"
    ."                    <th class='scroll_list'><small>KM</small></th>\n"
    ."                    <th class='scroll_list'><small>Miles</small></th>\n"
    ."                  </tr>\n"
    ."                  </thead>\n";
  if ($ID){
    $sql =
       "SELECT\n"
  	."  `logs`.`dx_km`,\n"
      ."  `logs`.`dx_miles`,\n"
      ."  COUNT(`logs`.`signalID`) as `logs`,\n"
      ."  MAX(`logs`.`date`) as `latest`,\n"
      ."  `signals`.`active` AS `active`,\n"
      ."  `signals`.`call`,\n"
      ."  `signals`.`format`,\n"
      ."  `signals`.`GSQ`,\n"
      ."  `signals`.`ID`,\n"
      ."  `signals`.`ITU`,\n"
      ."  `signals`.`KHZ`,\n"
      ."  `signals`.`lat`,\n"
      ."  `signals`.`lon`,\n"
      ."  `signals`.`sec`,\n"
      ."  `signals`.`SP`,\n"
      ."  `signals`.`type`\n"
      ."FROM\n"
      ."  `logs`,\n"
      ."  `signals`\n"
      ."WHERE\n"
      ."  `signalID` = `signals`.`ID` AND\n"
      ."  `listenerID` = ".$ID."\n"
      ."GROUP BY\n"
      ."  `signalID`\n"
      .($sortBy_SQL ? "ORDER BY\n  $sortBy_SQL" : "");
  //  print("<pre>$sql</pre>");

    $result =	mysql_query($sql);

    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
      $bgcolor =	"";
      if (!$row["active"]) {
        $bgcolor =	" bgcolor='#d0d0d0' title='(Reportedly off air or decommissioned)'";
      }
      else {
        switch ($row["type"]) {
          case NDB:	$bgcolor = "";								break;
          case DGPS:	$bgcolor = " bgcolor='#00D8ff' title='DGPS Station'";			break;
          case TIME:	$bgcolor = " bgcolor='#FFE0B0' title='Time Signal Station'";		break;
          case NAVTEX:	$bgcolor = " bgcolor='#FFB8D8' title='NAVTEX Station'";			break;
          case HAMBCN:	$bgcolor = " bgcolor='#D8FFE0' title='Amateur signal'";			break;
          case OTHER:	$bgcolor = " bgcolor='#B8F8FF' title='Other form of transmission'";	break;
        }
      }
      $out.=
         "<tr$bgcolor>"
  	  ."<td class='scroll_list' width='55'>".(float) $row["KHZ"]."</td>\n"
  	  ."<td class='scroll_list' width='90'><a href='javascript:signal_info(".$row["ID"].")'><b>".$row["call"]."</b></a></td>\n"
  	  ."<td class='scroll_list' width='25'>".$row["SP"]."</td>\n"
  	  ."<td class='scroll_list' width='30'>".$row["ITU"]."</td>\n"
  	  ."<td class='scroll_list' width='50'>".($row["GSQ"] ? "<a href='javascript:popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show map (accuracy limited to nearest Grid Square)'>".$row["GSQ"]."</a>" : "&nbsp;")."</td>\n"
  	  .(system!="RWW" ?
  	     "<td class='scroll_list' width='40'>".($row["sec"]  ? $row["sec"]  : "&nbsp;")."</td>\n"
  	    ."<td class='scroll_list' width='70'>".($row["format"]  ? $row["format"]  : "&nbsp;")."</td>\n"
         : ""
        )
  	  ."<td class='scroll_list' width='35' align='center'>".$row["logs"]."</td>\n"
  	  ."<td class='scroll_list' width='75'>".$row["latest"]."</td>\n"
  	  ."<td class='scroll_list' width='35' align='right'>".($row["dx_km"]? $row["dx_km"] : "&nbsp;")."</td>\n"
  	  ."<td class='scroll_list' width='35' align='right'>".($row["dx_miles"]? $row["dx_miles"] : "&nbsp;")."</td>\n"
  	  ."</tr>\n";
    }
  }
  $out.=
     "                  </div></table></td>\n"
    ."              </tr>\n"
    ."              <tr>\n"
    ."                <td class='rownormal' align='center'>\n"
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
    ."</table>\n";
  return $out;
}

function listener_name_sort($a, $b){
   return strcmp($a["name"], $b["name"]);
}

// ************************************
// * listener_stats()                 *
// ************************************
function listener_stats() {
  global $ID, $mode, $script, $submode, $ICAO_listener, $hours_listener;
  $Obj = new Listener($ID);
  $row =	$Obj->get_record();

  $signals =	$row["count_signals"];
  $logs =	$row["count_logs"];
  $GSQ =	$row["GSQ"];
  $name =	$row["name"];
  $QTH =	$row["QTH"];
  $SP =		$row["SP"];
  $ITU =	$row["ITU"];
  $lat =	$row["lat"];
  $lon =	$row["lon"];

  $pressure =	array();
  $out[] =	"<table border='0' cellpadding='0' cellspacing='0'>\n";
  $out[] =	"  <tr>\n";
  $out[] =	"    <td><table border='0' align='center' cellpadding='0' cellspacing='0' width='620'>\n";
  $out[] =	"      <tr>\n";
  $out[] =	"        <td colspan='2' width='100%'><table border='0' cellpadding='0' cellspacing='0' width='100%' class='noprint'>\n";
  $out[] =	"          <tr>\n";
  $out[] =	"            <td><h1>Listener</h1></td>\n";
  $out[] =	"            <td align='right' valign='bottom'><table border='0' cellpadding='2' cellspacing='0' class='tabTable'>\n";
  $out[] =	"              <tr>\n";
  $out[] =	$Obj->tabs();
  $out[] =	"              </tr>\n";
  $out[] =	"            </table></td>\n";
  $out[] =	"          </tr>\n";
  $out[] =	"        </table>\n";
  $out[] =	"        <table border='0' align='center' cellpadding='0' cellspacing='0' class='tableContainer' width='100%'>\n";
  $out[] =	"          <tr>\n";
  $out[] =	"            <td bgcolor='#F5F5F5' class='itemTextCell' height='325' valign='top'><table cellpadding='2' border='0' cellspacing='1' class='downloadtable' width='100%'>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <th class='downloadTableHeadings_nosort' colspan='2'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
  $out[] =	"                  <tr>\n";
  $out[] =	"                    <th align='left' class='downloadTableHeadings_nosort'>Signal Statistics</th>\n";
  $out[] =	"                  </tr>\n";
  $out[] =	"                </table></th>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td bgcolor='#00D8FF' width='120'><b>DGPS Stations:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_DGPS']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td bgcolor='#B8FFC0'><b>Ham Beacons:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_HAMBCN']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td bgcolor='#FFB8D8'><b>Navtex stations:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_NAVTEX']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td bgcolor='#FFFFFF'><b>NDBs:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_NDB']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td bgcolor='#B8F8FF'><b>Other:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_OTHER']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td bgcolor='#FFE0B0'><b>Time Signal:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_TIME']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td bgcolor='#FFFFFF'><b>Total:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_signals']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <th class='downloadTableHeadings_nosort' colspan='2'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
  $out[] =	"                  <tr>\n";
  $out[] =	"                    <th align='left' class='downloadTableHeadings_nosort'>Log Statistics</th>\n";
  $out[] =	"                  </tr>\n";
  $out[] =	"                </table></th>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td class='downloadTableContent' width='120'><b>Total logs:</b></td>\n";
  $out[] =	"                <td class='downloadTableContent'>".$row['count_logs']."</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td class='downloadTableContent' colspan='6' align='center'>\n";
  $out[] =	"<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> ";
  $out[] =	"<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> ";
  $out[] =	"</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"            </table></td>\n";
  $out[] =	"          </tr>\n";
  $out[] =	"        </table></td>\n";
  $out[] =	"      </tr>\n";
  $out[] =	"    </table>\n";
  return implode($out,"");
}

// ************************************
// * listener_update_format()         *
// ************************************
function listener_update_format($listenerID,$log_format) {
  $sql = "UPDATE\n"
	."  `listeners`\n"
	."SET\n"
	."  `log_format` = ".	($log_format ?	"\"".addslashes($log_format)."\"" : "NULL").	"\n"
	."WHERE"
	."  `ID` = \"".addslashes($listenerID)."\"";
  mysql_query($sql);
}

?>
