<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   RNA / REU / RWW              *
// * Filename:  admin.php                    *
// *                                         *
// * Created:   25/04/2004 (MF)              *
// * Revised:   21/06/2005 (MF)              *
// * Email:     martin@classaxe.com          *
// *******************************************
// Note: all functions are declared in alphabetical order



// ************************************
// * admin_manage()                   *
// ************************************
function admin_manage() {
  global $mode, $submode;
  global $HTTP_POST_FILES;
  $msg =	"";
  switch ($submode) {
    case "upload":
      $source = $HTTP_POST_FILES['file1']['tmp_name'];
      $dest = "../".$HTTP_POST_FILES['file1']['name'];

      if ( move_uploaded_file( $source, $dest ) ) {
        $msg = 'Saved '.$HTTP_POST_FILES['file1']['name'];
      }
      else {
        $msg = 'File could not be stored';
      }
    break;
  }
  $out =
     "<form name='form' method='post' enctype='multipart/form-data' action='".system_URL."'>\n"
    ."<input type='hidden' name='mode' value='$mode'>\n"
    ."<input type='hidden' name='submode' value=''>\n"
    ."<input type='hidden' name='MAX_FILE_SIZE' value='800000'>\n"
    ."<h2>Administrator Management Tools</h2><br>\n<ol class='p'>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_signalUpdateFromGSQ\"'> <b>Signals: Update Lat and Lon values from GSQ</b> (use after importing new signals using phpMyAdmin)</li>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_signalUpdateLogCount\"'> <b>Signals: Update log counts and 'Heard In' lists</b> (run periodically)</li>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_signalUpdateRegionsHeard\"'> <b>Signals: Update all for regions heard and heard_in lists</b> (run if problems are seen)</li><br><br>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_logsUpdateDX\"'> <b>Logs: Recalculate all distances</b> (use after adding GSQs for existing logging or function above)</li>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_setDaytimeLogs\"'> <b>Logs: Mark daytime loggings</b> (run periodically)</li><br><br>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_listenersUpdateLogCount\"'> <b>Listeners: Update log counts</b> (run periodically)</li><br><br>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_importICAO\"'> <b>ICAO: Get latest data</b> (run once a month)</li><br><br>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='document.location=\"".system_URL."/db_export\"'> <b>System: Export Database</b> (run periodically)</li>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='this.disabled=1;document.location=\"".system_URL."/".$mode."?submode=admin_systemSendTestEmail&sendToEmail=\"+document.getElementById(\"sendToEmail\").value'> <b>System: Send Test Email to </b><input type='text' class='formField' id='sendToEmail' name='sendToEmail' value=''></li><br><br>\n"
    ."<li><input type='button' class='formButton' value='Go' onclick='document.form.submode.value=\"upload\";document.form.submit()'> <b>System: Upload</b> <INPUT TYPE='FILE' NAME='file1' SIZE='20' class='formField'> (saves file to http://www.classaxe.com/dx/ndb/) <font color='red'><b>$msg</b></font></li>\n"
    ."</ol><br>\n";
  switch ($submode) {
    case "admin_logsUpdateDX":			    $out.= admin_logsUpdateDX();					                break;
    case "admin_importICAO":			    $out.= admin_importICAO();					                    break;
    case "admin_setDaytimeLogs":		    $out.= admin_setDaytimeLogs();				                    break;
    case "admin_signalUpdateRegionsHeard":	$out.= admin_signalUpdateRegionsHeard()." stations updated";	break;
    case "admin_signalUpdateLogCount":		$out.= admin_signalUpdateLogCount();				            break;
    case "admin_signalUpdateFromGSQ":		$out.= admin_signalUpdateFromGSQ();				                break;
    case "admin_listenersUpdateLogCount":	$out.= admin_listenersUpdateLogCount();			                break;
    case "admin_systemSendTestEmail":		$out.= admin_systemSendTestEmail();				                break;
    case "db_backup":				        $out.= "<h2>Create System Restore Point</h2><br>".db_dump();	break;
    case "admin_dbRestore":			        $out.= "<h2>Restore using System Restore Point</h2><br>".admin_dbRestore(); break;
  }
  return $out;
}

// ************************************
// * admin_dbDump()                   *
// ************************************
function admin_dbDump() {
  $filename =	strftime('%Y%m%d_%H%M',mktime()).".sql";
  $cmd =	"mysqldump -undb-ndbrna -pkj356y9945m -a -Q -a -r backup/$filename ndb-ndbrna";
  exec($cmd,$result,$status);
  return
     implode($result,"<br>\n")."<br>\n"
    ."Dumped data to ".$filename;
}



// ************************************
// * admin_dbRestore()                *
// ************************************
function admin_dbRestore() {
  set_time_limit(600);	// Extend maximum execution time to 10 mins

  global $mode, $submode, $file;
  $out = '';

  if (!$file) {
    $path =	system_backup;

    if (!$dir = opendir($path)) {
      return "Directory Error reading $path - please check your system configuration";
    }

    $files =	array();
    while($temp = readdir($dir)) {
      // Only get backup files for this site:
      if (preg_match('/(.+)\.sql$/',$temp,$name)) {
        $files[] =	$name[1];
      }
    }

    closedir($dir);
    sort($files);
 
    $out.=
       "<form name='form' action='".system_URL."/".$mode."?submode=$submode' method='POST' onsubmit='this.go.disabled=1'>\n"
      ."<input type='hidden' name='mode' value='$mode'>\n"
      ."<input type='hidden' name='submode' value='$submode'>\n"
      ."<select name='file' size='15' class='formField'>\n";

    for ($i=0; $i<count($files); $i++) {
      $out.=	"<option value='".$files[$i].".sql'>$files[$i]</option>\n";
    }
    $out.=
       "</select>\n"
      ."<input type='submit' value='Go' name='go'>\n"
      ."</form>\n";
  }
  else {
    $cmd =	system_mysql." -undb-ndbrna -pkj356y9945m -Dndb-ndbrna <".system_backup.$file." 2>&1";
    exec($cmd,$result,$status);
    $out.=implode($result,"<br>\n")."<br>\n";
    $out.="Restored data from $file";
  }
  return $out;
}



// ************************************
// * admin_logsUpdateDX()             *
// ************************************
function admin_logsUpdateDX() {
  set_time_limit(600);	// Extend maximum execution time to 10 mins
  $sql =	 "SELECT\n"
		."  `logs`.`ID`,\n"
		."  `signals`.`lat`   AS `dx_lat`,\n"
		."  `signals`.`lon`   AS `dx_lon`,\n"
		."  `listeners`.`lat` AS `qth_lat`,\n"
		."  `listeners`.`lon` AS `qth_lon`\n"
		."FROM\n"
		."  `signals`,\n"
		."  `listeners`,\n"
		."  `logs`\n"
		."WHERE\n"
		."  `logs`.`signalID` = `signals`.`ID` AND\n"
		."  `logs`.`listenerID` = `listeners`.`ID`";

  $result =	@mysql_query($sql);

//  print("<pre>$sql</pre>");

  $updated =	0;

  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result, MYSQL_ASSOC);
    if ($row["qth_lat"] && $row["dx_lat"]) {
      $a =	get_dx($row["qth_lat"],$row["qth_lon"],$row["dx_lat"],$row["dx_lon"]);
      $sql =	"UPDATE `logs` SET `dx_miles` = ".$a[0].", `dx_km` = ".$a[1]." WHERE `ID` = ".$row["ID"];
      mysql_query($sql);
      if (mysql_affected_rows()) {
        $updated++;
      }
    }
  }
  return "<h2>Updating DX values</h2><br><br><p>Done. $updated logs updated</p>";
}



// ************************************
// * admin_signalUpdateRegionsHeard() *
// ************************************
function admin_signalUpdateRegionsHeard(){
  $sql =
     "UPDATE\n"
	."  `signals`\n"
	."SET\n"
	."  `heard_in_af` =	0,\n"
	."  `heard_in_as` =	0,\n"
	."  `heard_in_ca` =	0,\n"
	."  `heard_in_eu` =	0,\n"
	."  `heard_in_iw` =	0,\n"
	."  `heard_in_na` =	0,\n"
	."  `heard_in_oc` =	0,\n"
	."  `heard_in_sa` =	0,\n"
	."  `heard_in` =	'',\n"
	."  `heard_in_html` =	''\n";
  mysql_query($sql);

  $sql =
     "SELECT DISTINCT\n"
	." `region`,\n"
	." `signalID`\n"
	."FROM\n"
	."  `logs`\n";
  $result =	@mysql_query($sql);

  $affected =	mysql_num_rows($result);
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $ID =	$row['signalID'];
    $region =	$row['region'];
    signal_update_heard_in($ID);
    $sql =
       "UPDATE\n"
	  ."  `signals`\n"
	  ."SET\n"
	  ."  `heard_in_".$region."` = 1\n"
	  ."WHERE\n"
	  ."  `ID` = \"$ID\"";
    mysql_query($sql);
  }
  return $affected;
}


// ************************************
// * admin_setDaytimeLogs()           *
// ************************************
function admin_setDaytimeLogs() {
  $sql =	"UPDATE `logs` SET `daytime` = 0";
  mysql_query($sql);

  $sql =
     "SELECT\n"
	."  CONCAT(\"UPDATE `logs` set `logs`.`daytime`=1 where `ID`=\",`logs`.`ID`,\";\") AS `query`\n"
	."FROM\n"
	."  `logs`,`listeners`\n"
	."WHERE\n"
	."  `logs`.`listenerID` = `listeners`.`ID` AND\n"
	."  (`logs`.`time`+2400 >=3400+(`listeners`.`timezone`*100) AND\n"
	."   `logs`.`time`+2400 < 3800+(`listeners`.`timezone`*100))\n";
  $result = @mysql_query($sql);
  $affected = mysql_num_rows($result);
  for ($i=0; $i<$affected; $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    mysql_query($row['query']);
  }
  return $affected;
}



// ************************************
// * admin_importICAO()               *
// ************************************
function admin_importICAO() {
  global $mode, $submode, $subsubmode, $data;
  $url =	"http://www.rap.ucar.edu/weather/surface/stations.txt";
  $out =
     "<form name='form' action='".system_URL."' method='POST'>\n"
    ."<input type='hidden' name='mode' value='$mode'>\n"
    ."<input type='hidden' name='submode' value='$submode'>\n"
    ."<input type='hidden' name='subsubmode' value=''>\n"
    ."<h2>Import ICAO</h2><br><br>\n"
    ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
    ."  <tr>\n"
    ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
    ."      <tr>\n"
    ."        <th align='left' class='downloadTableHeadings_nosort'><a name='dx'></a>ICAO Data</th>\n"
    ."        <th align='right' class='downloadTableHeadings_nosort'><small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small></th>\n"
    ."      </tr>\n"
    ."    </table></th>\n"
    ."  </tr>\n";
  if ($subsubmode=="Update") {
    $data =	explode(chr(13).chr(10),stripslashes($data));
    if ($data[3] == "\n---CONNECTION ERROR---") {
      $num =	0;
    }
    else {
      $num =	count($data)-7;
      $sql =	"DELETE FROM `icao`";
      mysql_query($sql);
      for ($i=5; $i<$num+5; $i++) {
        $icao_name =	trim(substr($data[$i],0,16));
        $icao_cnt =	trim(substr($data[$i],20,2));
        $icao_sp =	trim(substr($data[$i],17,2));
        $icao_ele =	trim(substr($data[$i],45,4));
        $lat =	(int) trim(substr($data[$i],29,2)) + (int) substr($data[$i],32,2) /60 ;
        if (substr($data[$i],35,1)=="S") {
          $lat *=-1;
        }
        $lon =	(int) trim(substr($data[$i],37,3)) + (int) substr($data[$i],41,2) /60 ;
        if (substr($data[$i],43,1)=="W") {
          $lon *=-1;
        }
        $icao_gsq =	deg_GSQ($lat,$lon);
        $sql =
           "INSERT INTO `icao` SET \n"
          ."  `name` = \"$icao_name\",\n"
          ."  `CNT` = \"$icao_cnt\",\n"
          ."  `elevation` = $icao_ele,\n"
          ."  `GSQ` = \"$icao_gsq\",\n"
          ."  `ICAO` = \"".(substr($data[$i],23,4))."\",\n"
          ."  `lat` = $lat,\n"
          ."  `lon` = $lon,\n"
          ."  `SP` = \"$icao_sp\"";
        mysql_query($sql);
        if (mysql_errno()) {
          $out.= "<pre>$data[$i]<br>$sql</pre>";
        }
      }
      $out.=
         "  <tr>\n"
        ."    <td class='downloadTableContent'>Success: Database updated, $num records processed</td>"
        ."  </tr>\n";
    }
  }
  else {
    $out.=
       "  <tr>\n"
      ."    <td class='downloadTableContent'><textarea class='formFixed' cols='55' rows='30' name='data'>"
      ."Data extracted from\n".$url."\n\n";

    if ($my_file = @file($url)) {
      $out.=	"STATION          SP CN ICAO  LAT     LONG    ELEV\n--------------------------------------------------\n";
      $num =	0;
      for ($i=0; $i<count($my_file); $i++) {
        if (substr($my_file[$i],62,1)=="X") {
          $num++;
          $out.=	substr($my_file[$i],3,17).substr($my_file[$i],0,3).substr($my_file[$i],81,2)." ".substr($my_file[$i],20,5)." ".substr($my_file[$i],39,20)."\n";
        }
      }
      $out.=
         "--------------------------------------------------\nTotal entries: $num"
        ."</textarea></td>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent' align='center'><input type='submit' name='go' value='Update' class='formButton' onclick='document.form.subsubmode.value=\"Update\";document.form.go.disabled=true;document.form.submit()'></td>"
        ."  </tr>\n";
    }
    else {
      $out.=
         "---CONNECTION ERROR---"
        ."</textarea></td>\n"
        ."  </tr>\n";
    }
  }
  $out.=
     "</table>\n"
    ."</form>\n";
  return $out;
}


// ************************************
// * admin_signalUpdateLogCount()     *
// ************************************
function admin_signalUpdateLogCount() {
  set_time_limit(600);	// Extend maximum execution time to 10 mins
  $updated =	0;
  $sql =
     "SELECT\n"
	."  `ID`"
	."FROM\n"
	."  `signals`";
  $result =	mysql_query($sql);
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result, MYSQL_ASSOC);
    $ID =	$row["ID"];
    $sql =
       "SELECT\n"
      ."  COUNT(*) as `logs`\n"
      ."FROM\n"
      ."  `logs`\n"
      ."WHERE\n"
      ."  `signalID` = ".$ID." AND\n"
      ."  `listenerID` != ''";
    $result2 =	@mysql_query($sql);
    $row =	mysql_fetch_array($result2, MYSQL_ASSOC);
    $logs =	$row["logs"];
    signal_update_heard_in($ID);
    $sql =
       "UPDATE\n"
      ."  `signals`\n"
      ."SET\n"
	  ."  `logs` = $logs\n"
	  ."WHERE\n"
      ."  `ID` = \"$ID\"";
    mysql_query($sql);
    if (mysql_affected_rows()) {
      $updated++;
    }
  }
  return "<h2>Updating Log Counts...</h2><br><br><p>Done. $updated signals updated.</p>";
}


// ************************************
// * admin_signalUpdateFromGSQ()      *
// ************************************
function admin_signalUpdateFromGSQ() {
  $updated =	0;
  $sql =	 "SELECT\n"
		."  `ID`,"
		."  `GSQ`"
		."FROM\n"
		."  `signals`";
  $result =	@mysql_query($sql);

  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result, MYSQL_ASSOC);
    $ID =	$row["ID"];
    $GSQ =	$row["GSQ"];
    if ($GSQ) {
      $a = 	GSQ_deg($GSQ);
      $lat =	$a["lat"];
      $lon =	$a["lon"];
      $sql =	 "UPDATE\n"
		."  `signals`\n"
		."SET\n"
		."  `lat` = $lat,\n"
		."  `lon` = $lon\n"
		."WHERE\n"
		."  `ID` = $ID";
      mysql_query($sql);
      if (mysql_affected_rows()) {
        $updated++;
      }
    }
  }
  return "<h2>Updating Signal Locations...</h2><br><br><p>Done. $updated signals updated.</p>";
}


// ************************************
// * admin_listenersUpdateLogCount()  *
// ************************************
function admin_listenersUpdateLogCount() {
  set_time_limit(600);	// Extend maximum execution time to 10 mins

  $updated =	0;

  $sql =		"SELECT `ID` FROM `listeners`";
  $result2=	 	 mysql_query($sql);
  for ($i=0;$i<mysql_num_rows($result2); $i++) {
    $row2 =		mysql_fetch_array($result2);
    $listenerID =	$row2["ID"];
    if (update_listener_log_count($listenerID)) {
      $updated++;
    }
  }
  return "<h2>Updating Log Counts...</h2><br><br><p>Done. $updated Listeners updated.</p>";
}



// ************************************
// * admin_systemSendTestEmail()      *
// ************************************
function admin_systemSendTestEmail() {
  global $sendToEmail;
  $mail = new PHPMailer();
    $mail->PluginDir =      "../";
    $mail->IsHtml(true);
    $mail->Mailer =         "smtp";

  $mail->From =		"martin@classaxe.com";
  $mail->FromName =	"RNA / REU / RWW system";
  $mail->Host =		smtp_host;
  $mail->Mailer =	"smtp";

  $mail->AddAddress($sendToEmail, "Test Email");
  $mail->Subject = "RNA / REU / RWW System";
  $mail->Body    = "Test Message to ".$sendToEmail." via ".smtp_host." from ".getenv("SERVER_NAME");
  if ($mail->Send()) {
    return "<h2>Sent test email to ".$sendToEmail." via ".smtp_host."</h2>";
  }
  return "<h2>Test email to ".$sendToEmail." via ".smtp_host." failed.</h2>";
}

// ************************************
// * sys_info()                       *
// ************************************
function sys_info() {
  ob_start();
  phpinfo();
  $tmp = ob_get_contents();
  $tmp = str_replace(" width=\"600\"", " width=\"600\"", $tmp);
  ob_end_clean();
  $out = preg_split("/<body>|<\/body>/i",$tmp);
//  print_r($out);die;
//  return $tmp;

  return
     "<style type=\"text/css\">\n"
    ."pre {margin: 0px; font-family: monospace;}\n"
		."a:link {color: #000099; text-decoration: none;}\n"
		."a:hover {text-decoration: underline;}\n"
		.".center table { margin-left: auto; margin-right: auto; text-align: left;}\n"
		.".center th { text-align: center; !important }\n"
		."h1 {font-size: 150%;}\n"
		."h2 {font-size: 125%;}\n"
		.".p {text-align: left;}\n"
		.".e {background-color: #ccccff; font-weight: bold;}\n"
		.".h {background-color: #9999cc; font-weight: bold;}\n"
	.".v {background-color: #cccccc; width: 700px; word-wrap: break-word;}\n"
		."i {color: #666666;}\n"
		."img {float: right; border: 0px;}\n"
    ."#phpinfo table { width: 800px; }\n"
    ."</style>\n"
		."<h2>RNA / REU / RWW SYSTEM</h2><br>\n"
	."<table border=\"0\" cellpadding=\"3\" width=\"800\">\n"
		."<tr><td class=\"e\">system</td><td class=\"v\">".system."</td></tr>\n"
		."<tr><td class=\"e\">system_software</td><td class=\"v\">".system_software." UTC</td></tr>\n"
		."<tr><td class=\"e\">system_editor</td><td class=\"v\">".system_editor."</td></tr>\n"
		."<tr><td class=\"e\">system_ID</td><td class=\"v\">".system_ID."</td></tr>\n"
		."<tr><td class=\"e\">system_title</td><td class=\"v\">".system_title."</td></tr>\n"
		."<tr><td class=\"e\">system_URL</td><td class=\"v\">".system_URL."</td></tr>\n"
	."</table>\n"
	."<div id='phpinfo'>\n"
    ."<hr />\n"
    ."<h2>PHP INFO</h2>\n"
	.$out[1]
	."</div>";
}


// ************************************
// * log_delete()                     *
// ************************************
function log_delete($ID) {
    $log = new Log($ID);
    $log->delete();
}


// ************************************
// * log_insert()                     *
// ************************************
function log_insert(	$signalID, $date, $daytime, $dx_km, $dx_miles, $format, $heard_in,
			$listenerID, $LSB, $LSB_approx, $region, $sec, $time, $USB, $USB_approx) {
  $sql =
     "INSERT INTO `logs` SET\n"
   	.		"  `date` =		\"$date\",\n"
	.		"  `daytime` =		\"".($daytime ? 1 : 0)."\",\n"
	.($dx_km ?	"  `dx_km` =		\"$dx_km\",\n"
		       ."  `dx_miles` =		\"$dx_miles\",\n" : "")
	.($format ?	"  `format` =		\"$format\",\n" : "")
	.		"  `heard_in` =		\"$heard_in\",\n"
	.($LSB != "" ?	"  `LSB` = 		\"$LSB\",\n" : "")
	.($LSB_approx ?	"  `LSB_approx` =	\"~\",\n" : "")
	.($sec != "" ?	"  `sec` =		\"$sec\",\n" : "")
	.($time != "" ?	"  `time` =		\"$time\",\n" : "")
	.($USB != "" ?	"  `USB` =		\"$USB\",\n" : "")
	.($USB_approx ?	"  `USB_approx` =	\"~\",\n" : "")
	.		"  `signalID` =		\"$signalID\",\n"
	.		"  `listenerID` =	\"$listenerID\",\n"
	.		"  `region` =		\"$region\"\n";
  if (!mysql_query($sql)) {
    print("<pre>$sql</pre>");
  }
}


// ************************************
// * log_update()                     *
// ************************************
function log_update(	$ID, $date, $daytime, $dx_km, $dx_miles, $format, $heard_in,
			$listenerID, $LSB, $LSB_approx, $sec, $time, $USB, $USB_approx) {
  // Add listener name and log details for an existing non-specific log matching the listener's heard_in location (original NDBRNA import)
  // Don't need to set system or signalID - these haven't changed
  $sql =
    "UPDATE `logs` SET\n"
	."  `date` =		\"$date\",\n"
	."  `daytime` =		\"".($daytime ? 1 : 0)."\",\n"
	.($dx_km ?
       "  `dx_km` =		\"$dx_km\",\n"
	  ."  `dx_miles` =		\"$dx_miles\",\n" : "")
	.($format ?
       "  `format` =		\"$format\",\n" : "")
	."  `heard_in` =		\"$heard_in\",\n"
	.($LSB != "" ?	"  `LSB` = 		\"$LSB\",\n" : "")
	.($LSB_approx ?	"  `LSB_approx` =	\"~\",\n" : "")
	.($sec != "" ?	"  `sec` =		\"$sec\",\n" : "")
	.($time != "" ?	"  `time` =		\"$time\",\n" : "")
	.($USB != "" ?	"  `USB` =		\"$USB\",\n" : "")
	.($USB_approx ?	"  `USB_approx` =	\"~\",\n" : "")
	."  `listenerID` =	\"$listenerID\"\n"
	."WHERE `ID` = \"$ID\"";

  if (!mysql_query($sql)) {
    print("<pre>$sql</pre>");
  }
}


// ************************************
// * log_upload()                     *
// ************************************
function log_upload() {
  global $mode, $submode, $log_format, $log_entries, $log_dd, $log_mm, $log_yyyy, $listener_in, $listener_timezone;
  global $CS, $dx_lat, $dx_lon, $fmt, $sec, $ID, $KHZ, $LSB, $LSB_approx, $USB, $USB_approx, $YYYYMMDD, $hhmm;
  global $listenerID, $callsign, $gsq, $name, $notes, $password, $qth, $username;
  global $debug;
  $debug = 0;
  $out =	array();
  $out[] =	"<form name='form' action='".system_URL."/".$mode."' method='POST'>";
  $out[] =	"<input type='hidden' name='submode' value=''>";
  switch ($submode) {
    case "save_format":
      listener_update_format($listenerID,$log_format);
      $submode= '';
    break;
    case "submit_log":
      set_time_limit(600);	// Extend maximum execution time to 10 mins
      $log_first_for_listener =		0;	// First time listener has been named for this signal
      $log_first_for_state_or_itu =	0;	// First time signal has been logged specifically from this state
      $log_first_for_system =		0;	// First time signal has been logged at all
      $log_repeat_for_listener =	0;	// Listener has logged this signal before
      $log_exact_duplicate =		0;	// This logging has been submitted once already
      $signal_updates =			0;	// The signal record has been updated - this data is most recent
      $sql =		"SELECT `lat`, `lon`, `region` FROM `listeners` WHERE `ID` = $listenerID";
      $result =		mysql_query($sql);
      $row =		mysql_fetch_array($result, MYSQL_ASSOC);
      $region =		$row["region"];
      $qth_lat =	$row["lat"];
      $qth_lon =	$row["lon"];
      if ($debug) {
        $out[] =
           "1: Logged at least once from this state<br>"
		  ."2: No listener yet listed in this state<br>"
		  ."3: Listener listed, but this is not a duplicate logging so add a new one<br>"
		  ."4: signal never logged in this state<br>";
      }
      for ($i=0; $i<count($ID); $i++) {
        if ($debug) {
          $out[] =	"<li>ID=".$ID[$i]." ";
        }
        $update_signal =		    false;
        $update_signal_heard_in =	true; //false;
        // ++++++++++++++++++++++++++++++++++++
        // + Get DX (if possible)             +
        // ++++++++++++++++++++++++++++++++++++
        $tmp =		get_signal_dx($ID[$i], $qth_lat, $qth_lon);
        $dx_miles =	$tmp[0];
        $dx_km =	$tmp[1];
        $daytime =	($hhmm[$i]+2400 >= ($listener_timezone*100) + 3400 && $hhmm[$i]+2400 < ($listener_timezone*100)+3800 ? 1 : 0);
        // ++++++++++++++++++++++++++++++++++++++++++++
        // + See if log is first for state or country +
        // ++++++++++++++++++++++++++++++++++++++++++++
        $sql =		"SELECT `ID`,`listenerID` FROM `logs` WHERE `signalID` = ".$ID[$i]." AND `heard_in` = \"$listener_in\"";
        $result =	mysql_query($sql);
        if (mysql_num_rows($result)) {						// No, signal has been logged at least once from this state:
          if ($debug)	$out[] =	"1 ";
          $update_signal = true;						// Update signal record (IF this data is the most recent...)						
          $row =	mysql_fetch_array($result);
          if ($row["listenerID"] == "") {					// First row doesn't list listener, so must be first time
            // First time listener from this state named, so 
            if ($debug)	$out[] =	"2 ";
            log_update(	$row["ID"], $YYYYMMDD[$i], $daytime, $dx_km, $dx_miles, htmlentities($fmt[$i]), $listener_in,
			$listenerID, $LSB[$i], $LSB_approx[$i], $sec[$i], $hhmm[$i], $USB[$i], $USB_approx[$i]);
            if ($debug)	$out[] =	"<pre>$sql</pre>";
            mysql_query($sql);							// Write in name for this listener
            $update_signal = 		true;					// Update signal record (IF this data is the most recent...)
            $log_first_for_listener++;
          }
          else {								// A listener from this state has been named before
            // ++++++++++++++++++++++++++++++++++++
            // + See if log is exact duplicate    +
            // ++++++++++++++++++++++++++++++++++++
            $sql =	 "SELECT `ID` FROM `logs`\n"
			."WHERE\n"
			.($hhmm[$i] ?	"  `time` = \"".$hhmm[$i]."\" AND\n" : "")
			.		"  `signalID` = ".$ID[$i]." AND\n"
			.		"  `date` = \"".$YYYYMMDD[$i]."\" AND\n"
			.		"  `listenerID` = ".$listenerID;

            if ($debug)	$out[] =	"<pre>$sql</pre>";
            $result =	mysql_query($sql);

            if (mysql_num_rows($result)) {					// Yes, it's a duplicate
              $log_exact_duplicate++;
            }
            else {								// No, not a duplicate
              // +++++++++++++++++++++++++++++++++++++++
              // + this is a new logging for old state +
              // +++++++++++++++++++++++++++++++++++++++
              if ($debug)	$out[] = "3 ";

              $sql =	"SELECT COUNT(*) AS `log_repeat_for_listener`FROM `logs` WHERE `listenerID` = \"$listenerID\" AND `signalID` = \"".$ID[$i]."\"";
              $result =	mysql_query($sql);
              $row =	mysql_fetch_array($result,MYSQL_ASSOC);
              if ($row["log_repeat_for_listener"]) {
                $log_repeat_for_listener++;
              }
              else {
                $log_first_for_listener++;
              }


              log_insert(	$ID[$i], $YYYYMMDD[$i], $daytime, $dx_km, $dx_miles, htmlentities($fmt[$i]), $listener_in, $listenerID,
				$LSB[$i], $LSB_approx[$i], $region, $sec[$i], $hhmm[$i], $USB[$i], $USB_approx[$i]);
              $update_signal = true;						// Update signal record (IF this data is the most recent...)
            }
          }
        }
        else {									// signal not logged from this state before (but could be 'everywhere')
          if ($debug) {
            $out[] =	"4 ";
          }
          // +++++++++++++++++++++++++++++++++++++++
          // + this is a new logging for new state +
          // +++++++++++++++++++++++++++++++++++++++
          log_insert(	$ID[$i], $YYYYMMDD[$i], $daytime, $dx_km, $dx_miles, htmlentities($fmt[$i]), $listener_in, $listenerID,
			$LSB[$i], $LSB_approx[$i], $region, $sec[$i], $hhmm[$i], $USB[$i], $USB_approx[$i]);

          $update_signal = true;						// Update signal record (IF this data is the most recent...)						
          $update_signal_heard_in =	true;					// Update signal heard in record
          $log_first_for_state_or_itu++;
          $log_first_for_listener++;
        }

        if ($debug)	$out[] =	"<li>update_signal = $update_signal, update_signal_heard_in = $update_signal_heard_in</li>\n";




        // +++++++++++++++++++++++++++++++++++++++
        // + State Heard in list has changed     +
        // +++++++++++++++++++++++++++++++++++++++
        if ($update_signal_heard_in) {
          signal_update_heard_in($ID[$i]);
        }
        // +++++++++++++++++++++++++++++++++++++++
        // + Update signal request- is data new? +
        // +++++++++++++++++++++++++++++++++++++++
        if ($update_signal) {
          // See if the data is more recent than MLR:
          $sql =	 "SELECT\n"
			."  *,\n"
			."  DATE_FORMAT(`last_heard`,'%Y%m%d') AS `f_last_heard`\n"
			."FROM\n"
			."  `signals`\n"
			."WHERE\n"
			."  `ID` = \"".$ID[$i]."\"";
          $result =	mysql_query($sql);
          $row =	mysql_fetch_array($result,MYSQL_ASSOC);
          $this_YYYY =	substr($YYYYMMDD[$i],0,4);
          $this_MM =	substr($YYYYMMDD[$i],4,2);
          $this_DD =	substr($YYYYMMDD[$i],6,2);
          $last_heard_YYYY =	substr($row["f_last_heard"],0,4);
          $last_heard_MM =	substr($row["f_last_heard"],4,2);
          $last_heard_DD =	substr($row["f_last_heard"],6,2);

          if ($debug) {
            $out[] =	"<br>This: $this_YYYY$this_MM$this_DD<br>";
            $out[] =	"Last: $last_heard_YYYY$last_heard_MM$last_heard_DD<br>";
          }

          if ((int)($last_heard_YYYY."".$last_heard_MM."".$last_heard_DD) >= (int)($this_YYYY."".$this_MM."".$this_DD)) {
            $update_signal =		false;						// So clear update flag
          }
        }

        $sql =		"SELECT COUNT(*) as `logs` FROM `logs` WHERE `signalID` = ".$ID[$i]." AND `listenerID` != ''";
        $result =	mysql_query($sql);
        $row =		mysql_fetch_array($result, MYSQL_ASSOC);
        $logs =		$row["logs"];

        if ($update_signal) {
          $signal_updates++;
          $last_heard = $this_YYYY."-".$this_MM."-".$this_DD;
	  signal_update_full($ID[$i],$LSB[$i],$LSB_approx[$i],$USB[$i],$USB_approx[$i],$sec[$i],htmlentities($fmt[$i]),$logs,$last_heard,$region);
        }
        else {
          $sql =	 "UPDATE `signals` SET `logs` = $logs, `heard_in_$region`=1 WHERE `ID` = ".$ID[$i];
          mysql_query($sql);
          if ($debug)	$out[] =	"<pre>$sql</pre>";
        }
      }
      update_listener_log_count($listenerID);
    break;
  }

  switch ($submode) {
    case "":
      $sql =		"SELECT * FROM `listeners` WHERE `ID` = '$listenerID'";
      $result = 	mysql_query($sql);
      $row =		mysql_fetch_array($result,MYSQL_ASSOC);
      $log_format =	$row["log_format"];
      $out[] =
         "<h1>Add Log > Parse Data</h1><br><img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>"
        ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
        ."  <tr>\n"
        ."    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n"
        ."  </tr>\n"
        ."  <tr class='rownormal'>\n"
        ."    <th align='left'>Listener</th>"
        ."    <td colspan='3'>"
        ."<select name='listenerID' class='formfield' onchange='document.form.submit()' style='font-family: monospace;'> "
        .get_listener_options_list("1",$listenerID,"Select Listener")
        ."</select>"
        ."</td>\n"
        ."  </tr>\n"
        ."</table>\n";
    break;

    case "parse_log":
      listener_update_format($listenerID,$log_format);

      $sql =	"SELECT * FROM `listeners` WHERE `ID` = \"".$listenerID."\"";
      $result = 	mysql_query($sql);
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
      if ($row['SP']) {
        $listener_in = $row['SP'];
      }
      else {
        $listener_in = $row['ITU'];
      }
      $listener_timezone =	$row['timezone'];

      $out[] =	"<h1>Add Log > Confirm Data</h1><br><img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>";
      $out[] =	"<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n";
      $out[] =	"  <tr>\n";
      $out[] =	"    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n";
      $out[] =	"  </tr>\n";
      $out[] =	"  <tr class='rownormal'>\n";
      $out[] =	"    <th align='left'>Listener</th>\n";
      $out[] =	"    <td colspan='3'>\n";
      $out[] =	"    <input type='hidden' name='listener_in' value='$listener_in'>\n";
      $out[] =	"    <input type='hidden' name='listenerID' value='$listenerID'>";
      $out[] =	"    <input type='hidden' name='listener_timezone' value='$listener_timezone'>";
      $out[] =	$row["name"].($row["callsign"] ? " <b>".$row["callsign"]."</b>" : "")." ".$row["QTH"].", ".$row["SP"].", ".$row["ITU"].($row["notes"] ? " (".stripslashes($row["notes"]).")" : "");
      $out[] =	"</td>\n";
      $out[] =	"  </tr>\n";
      $out[] =	"</table>\n";
    break;

    case "submit_log":
      $sql =	"SELECT * FROM `listeners` WHERE `ID` = \"".$listenerID."\"";
      $result = mysql_query($sql);
      $row =	mysql_fetch_array($result,MYSQL_ASSOC);
      $out[] =	"<h1>Add Log > Results</h1><br><img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>";
      $out[] =	"<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n";
      $out[] =	"  <tr>\n";
      $out[] =	"    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n";
      $out[] =	"  </tr>\n";
      $out[] =	"  <tr class='rownormal'>\n";
      $out[] =	"    <th align='left'>Listener</th>";
      $out[] =	"    <td colspan='3'><input type='hidden' name='listenerID' value='$listenerID'>";
      $out[] =	$row["name"].($row["callsign"] ? " <b>".$row["callsign"]."</b>" : "")." ".$row["QTH"].", ".$row["SP"].", ".$row["ITU"].($row["notes"] ? " (".stripslashes($row["notes"]).")" : "");
      $out[] =	"</td>\n";
      $out[] =	"  </tr>\n";
      $out[] =	"</table><br><br>\n";

      $out[] =	 "<h1>Statistics for this update:</h1><br>\n"
		."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
		."  <tr class='downloadTableHeadings'>\n"
		."    <th width='200'>Statistic</th>\n"
		."    <th width='30'>Value</th>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>Loggings checked</td>\n"
		."    <td>".count($ID)."</td>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>Exact Duplicates</td>\n"
		."    <td>$log_exact_duplicate</td>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>First for State or ITU</td>\n"
		."    <td>$log_first_for_state_or_itu</td>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>First for Listener</td>\n"
		."    <td>$log_first_for_listener</td>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>Repeat logging for Listener</td>\n"
		."    <td>$log_repeat_for_listener</td>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>Latest signal logging</td>\n"
		."    <td>$signal_updates</td>\n"
		."  </tr>\n"
		."</table>\n"
		."<br><h1>Statistics for listener:</h1><br>\n"
		."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
		."  <tr class='downloadTableHeadings'>\n"
		."    <th width='200'>Statistic</th>\n"
		."    <th width='30'>Value</th>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>Signals Logged</td>\n"
		."    <td>".$row["count_signals"]."</td>\n"
		."  </tr>\n"
		."  <tr class='rownormal'>\n"
		."    <td>Logs in database</td>\n"
		."    <td>".$row["count_logs"]."</td>\n"
		."  </tr>\n"
		."</table>\n";
    break;
  }

  $param_array =	array();
  $start =	0;
  $len =	0;

  $log_format_parse =	$log_format." ";
  while (substr($log_format_parse,$start,1)==" ") {
    $start++;
  }
//  print "<pre>$log_format</pre>\n";
  $log_format_errors = "";
  while ($start<strlen($log_format_parse)) {
    $len =		strpos(substr($log_format_parse,$start)," ");
    $param_name =	substr($log_format_parse,$start,$len);
    if ($len) {
      while (substr($log_format_parse,$start+$len,1)==" ") {
        $len++;
      }
      if ($param_name=="X" || !isset($param_array[$param_name])) {
        $param_array[$param_name] = array($start,$len+1);
      }
      else {
        $log_format_errors.=
           "<tr class='rownormal'>\n"
          ."  <th>$param_name</th>\n"
          ."  <td>Occurs twice: char ".$param_array[$param_name][0]." and char $start</td>\n"
          ."</tr>\n";
      }
    }
    $start = $start+$len;
  }
  if ($submode=="parse_log" && $log_format_errors!="") {
    $out[] =
	   "<br><span class='p'><b>Log Format Errors</b></span>\n"
      ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0' width='100%'>\n"
	  ."  <tr class='downloadTableHeadings'>\n"
      ."    <th colspan='2'>Input Format Errors</th>\n"
      ."  </tr>\n"
	  ."  <tr class='rownormal'>\n"
      ."    <th>Input</th>\n"
      ."    <td><pre style='margin:0;'>$log_format</pre></td>\n"
      ."  </tr>\n"
      .$log_format_errors
      ."</table>\n\n"
      ."Click <a href='javascript:history.back()'><b><u>here</u></b></a> to check your log format and try again.</p>";
    return implode("",$out);
  }

//   foreach($param_array as $key=>$value){ print("<li>$key = ".$value[0].", ".$value[1]."</li>\n"); }


  $log_shows_YYYY =	false;
  $log_shows_MM =	false;
  $log_shows_DD =	false;

  if (	isset($param_array["DDMMYY"]) ||	isset($param_array["DD.MM.YY"]) ||
	isset($param_array["DDYYMM"]) ||	isset($param_array["DD.YY.MM"]) ||
	isset($param_array["MMDDYY"]) ||	isset($param_array["MM.DD.YY"]) ||
	isset($param_array["MMYYDD"]) ||	isset($param_array["MM.YY.DD"]) ||
	isset($param_array["YYDDMM"]) ||	isset($param_array["YY.DD.MM"]) ||
	isset($param_array["YYMMDD"]) ||	isset($param_array["YY.MM.DD"]) ||

	isset($param_array["DDMMMYY"]) ||	isset($param_array["DD.MMM.YY"]) ||
	isset($param_array["DDYYMMM"]) ||	isset($param_array["DD.YY.MMM"]) ||
	isset($param_array["MMMDDYY"]) ||	isset($param_array["MMM.DD.YY"]) ||
	isset($param_array["MMMYYDD"]) ||	isset($param_array["MMM.YY.DD"]) ||
	isset($param_array["YYDDMMM"]) ||	isset($param_array["YY.DD.MMM"]) ||
	isset($param_array["YYMMMDD"]) ||	isset($param_array["YY.MMM.DD"]) ||

	isset($param_array["DDMMYYYY"]) ||	isset($param_array["DD.MM.YYYY"]) ||
	isset($param_array["DDYYYYMM"]) ||	isset($param_array["DD.YYYY.MM"]) ||
	isset($param_array["MMDDYYYY"]) ||	isset($param_array["MM.DD.YYYY"]) ||
	isset($param_array["MMYYYYDD"]) ||	isset($param_array["MM.YYYY.DD"]) ||
	isset($param_array["YYYYDDMM"]) ||	isset($param_array["YYYY.DD.MM"]) ||
	isset($param_array["YYYYMMDD"]) ||	isset($param_array["YYYY.MM.DD"]) ||

	isset($param_array["DDMMMYYYY"]) ||	isset($param_array["DD.MMM.YYYY"]) ||
	isset($param_array["DDYYYYMMM"]) ||	isset($param_array["DD.YYYY.MMM"]) ||
	isset($param_array["MMMDDYYYY"]) ||	isset($param_array["MMM.DD.YYYY"]) ||
	isset($param_array["MMMYYYYDD"]) ||	isset($param_array["MMM.YYYY.DD"]) ||
	isset($param_array["YYYYDDMMM"]) ||	isset($param_array["YYYY.DD.MMM"]) ||
	isset($param_array["YYYYMMMDD"]) ||	isset($param_array["YYYY.MMM.DD"])) {

    $log_shows_YYYY =	true;
    $log_shows_MM =	true;
    $log_shows_DD =	true;
  }
  if (	isset($param_array["DM"]) ||		isset($param_array["D.M"]) ||
	isset($param_array["DDM"]) ||		isset($param_array["DD.M"]) ||
	isset($param_array["DMM"]) ||		isset($param_array["D.MM"]) ||
	isset($param_array["DDMM"]) ||		isset($param_array["DD.MM"]) ||
	isset($param_array["DMMM"]) ||		isset($param_array["D.MMM"]) ||
	isset($param_array["DDMMM"]) ||		isset($param_array["DD.MMM"]) ||

	isset($param_array["MD"]) ||		isset($param_array["M.D"]) ||
	isset($param_array["MDD"]) ||		isset($param_array["M.DD"]) ||
	isset($param_array["MMD"]) ||		isset($param_array["MM.D"]) ||
	isset($param_array["MMDD"]) ||		isset($param_array["MM.DD"]) ||
	isset($param_array["MMMD"]) ||		isset($param_array["MMM.D"]) ||
	isset($param_array["MMMDD"]) ||		isset($param_array["MMM.DD"])) {
    $log_shows_MM =	true;
    $log_shows_DD =	true;
  }
  if (	isset($param_array["MM"])) {
    $log_shows_MM =	true;
  }
  if (	isset($param_array["M"])) {
    $log_shows_MM =	true;
  }
  if (	isset($param_array["D"])) {
    $log_shows_DD =	true;
  }
  if (	isset($param_array["DD"])) {
    $log_shows_DD =	true;
  }
  if (	isset($param_array["YY"])) {
    $log_shows_YYYY =	true;
  }
  if (	isset($param_array["YYYY"])) {
    $log_shows_YYYY =	true;
  }
  


  switch ($submode) {
    case "parse_log":
      if (!isset($param_array["ID"])) {
        $out[] =	"<br><h1>Error</h1><p>Your log format must include the ID field.<br>";
        $out[] =	"Click <a href='javascript:history.back()'><b><u>here</u></b></a> to check your log format and try again.</p>";
      }
      else {
        $out[] =	"<br><span class='p'><b>Parser Results</b><small> - see <a href='#next'><b>below</b></a> for suggested <b>Next Steps</b></small></span>\n";
        $out[] =	"<table border='0' cellpadding='2' cellspacing='1' bgcolor='#c0c0c0'>\n";
        $out[] =	"  <tr class='downloadTableHeadings_nosort'>\n";
        $out[] =	"    <th>KHz</th>\n";
        $out[] =	"    <th>ID</th>\n";
        $out[] =	"    <th>QTH</th>\n";
        $out[] =	"    <th>SP</th>\n";
        $out[] =	"    <th>ITU</th>\n";
        $out[] =	"    <th>GSQ</th>\n";
        $out[] =	"    <th>Heard In</th>\n";
        $out[] =	"    <th>YYYYMMDD</th>\n";
        $out[] =	"    <th>HHMM</th>\n";
        $out[] =	"    <th>LSB</th>\n";
        $out[] =	"    <th>USB</th>\n";
        $out[] =	"    <th>Sec</th>\n";
        $out[] =	"    <th>Format</th>\n";
        $out[] =	"    <th>New?</th>\n";
        $out[] =	"  </tr>\n";

        $lines =		explode("\r"," ".stripslashes($log_entries));
        $unresolved_signals =	array();


        $total_loggings =	0;
        $date_fail =		false;
             if (isset($param_array["DM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DM\"][0])); return (\$Y.M_to_MM(substr(\$t,1,1)).D_to_DD(substr(\$t,0,1))); }" );
        else if (isset($param_array["D.M"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.M\"][0])); return (\$Y.M_to_MM(substr(\$t,2,1)).D_to_DD(substr(\$t,0,1))); }" );
        else if (isset($param_array["DDM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDM\"][0])); return (\$Y.M_to_MM(substr(\$t,2,1)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.M"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.M\"][0])); return (\$Y.M_to_MM(substr(\$t,3,1)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DMM\"][0])); return (\$Y.substr(\$t,1,2).D_to_DD(substr(\$t,0,1))); }" );
        else if (isset($param_array["D.MM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.MM\"][0])); return (\$Y.substr(\$t,2,2).D_to_DD(substr(\$t,0,1))); }" );
        else if (isset($param_array["DDMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMM\"][0])); return (\$Y.substr(\$t,2,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.MM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM\"][0])); return (\$Y.substr(\$t,3,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DMMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DMMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,1,3)).D_to_DD(substr(\$t,0,1))); }" );
        else if (isset($param_array["D.MMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.MMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,2,3)).D_to_DD(substr(\$t,0,1))); }" );
        else if (isset($param_array["DDMMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.MMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }" );

             if (isset($param_array["MD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).D_to_DD(substr(\$t,1,1))); }" );
        else if (isset($param_array["M.D"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"M.D\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).D_to_DD(substr(\$t,2,1))); }" );
        else if (isset($param_array["MDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MDD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).substr(\$t,1,2)); }" );
        else if (isset($param_array["M.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"M.DD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).substr(\$t,2,2)); }" );
        else if (isset($param_array["MMD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMD\"][0])); return (\$Y.substr(\$t,0,2).D_to_DD(substr(\$t,2,1))); }" );
        else if (isset($param_array["MM.D"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.D\"][0])); return (\$Y.substr(\$t,0,2).D_to_DD(substr(\$t,3,1))); }" );
        else if (isset($param_array["MMDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDD\"][0])); return (\$Y.substr(\$t,0,2).substr(\$t,2,2)); }" );
        else if (isset($param_array["MM.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD\"][0])); return (\$Y.substr(\$t,0,2).substr(\$t,3,2)); }" );
        else if (isset($param_array["MMMD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).D_to_DD(substr(\$t,3,1))); }" );
        else if (isset($param_array["MMM.D"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.D\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).D_to_DD(substr(\$t,4,1))); }" );
        else if (isset($param_array["MMMDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }" );
        else if (isset($param_array["MMM.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }" );

        else if (isset($param_array["DDMMYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMYY\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).substr(\$t,2,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.MM.YY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM.YY\"][0])); return (YY_to_YYYY(substr(\$t,6,2)).substr(\$t,3,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DDYYMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYMM\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).substr(\$t,4,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.YY.MM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YY.MM\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).substr(\$t,6,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["MMDDYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDDYY\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).substr(\$t,0,2).substr(\$t,2,2)); }" );
        else if (isset($param_array["MM.DD.YY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD.YY\"][0])); return (YY_to_YYYY(substr(\$t,6,2)).substr(\$t,0,2).substr(\$t,3,2)); }" );
        else if (isset($param_array["MMYYDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMYYDD\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).substr(\$t,0,2).substr(\$t,4,2)); }" );
        else if (isset($param_array["MM.YY.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.YY.DD\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).substr(\$t,0,2).substr(\$t,6,2)); }" );
        else if (isset($param_array["YYDDMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYDDMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,4,2).substr(\$t,2,2)); }" );
        else if (isset($param_array["YY.DD.MM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.DD.MM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,6,2).substr(\$t,3,2)); }" );
        else if (isset($param_array["YYMMDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYMMDD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,2,2).substr(\$t,4,2)); }" );
        else if (isset($param_array["YY.MM.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.MM.DD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,3,2).substr(\$t,6,2)); }" );

        else if (isset($param_array["DDMMMYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMMYY\"][0])); return (YY_to_YYYY(substr(\$t,5,2)).MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.MMM.YY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM.YY\"][0])); return (YY_to_YYYY(substr(\$t,7,2)).MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DDYYMMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYMMM\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).MMM_to_MM(substr(\$t,4,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.YY.MMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YY.MMM\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).MMM_to_MM(substr(\$t,6,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["MMMDDYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDDYY\"][0])); return (YY_to_YYYY(substr(\$t,5,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }" );
        else if (isset($param_array["MMM.DD.YY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD.YY\"][0])); return (YY_to_YYYY(substr(\$t,7,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }" );
        else if (isset($param_array["MMMYYDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMYYDD\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,5,2)); }" );
        else if (isset($param_array["MMM.YY.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.YY.DD\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,7,2)); }" );
        else if (isset($param_array["YYDDMMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYDDMMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,4,3)).substr(\$t,2,2)); }" );
        else if (isset($param_array["YY.DD.MMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.DD.MMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,6,3)).substr(\$t,3,2)); }" );
        else if (isset($param_array["YYMMMDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYMMMDD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,2,3)).substr(\$t,5,2)); }" );
        else if (isset($param_array["YY.MMM.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.MMM.DD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,3,3)).substr(\$t,7,2)); }" );

        else if (isset($param_array["DDMMYYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMYYYY\"][0])); return (substr(\$t,4,4).substr(\$t,2,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.MM.YYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM.YYYY\"][0])); return (substr(\$t,6,4).substr(\$t,3,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DDYYYYMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYYYMM\"][0])); return (substr(\$t,2,4).substr(\$t,6,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.YYYY.MM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YYYY.MM\"][0])); return (substr(\$t,3,4).substr(\$t,8,2).substr(\$t,0,2)); }" );
        else if (isset($param_array["MMDDYYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDDYYYY\"][0])); return (substr(\$t,4,4).substr(\$t,0,2).substr(\$t,2,2)); }" );
        else if (isset($param_array["MM.DD.YYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD.YYYY\"][0])); return (substr(\$t,6,4).substr(\$t,0,2).substr(\$t,3,2)); }" );
        else if (isset($param_array["MMYYYYDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMYYYYDD\"][0])); return (substr(\$t,2,4).substr(\$t,0,2).substr(\$t,6,2)); }" );
        else if (isset($param_array["MM.YYYY.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.YYYY.DD\"][0])); return (substr(\$t,3,4).substr(\$t,0,2).substr(\$t,8,2)); }" );
        else if (isset($param_array["YYYYDDMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYDDMM\"][0])); return (substr(\$t,0,4).substr(\$t,6,2).substr(\$t,4,2)); }" );
        else if (isset($param_array["YYYY.DD.MM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.DD.MM\"][0])); return (substr(\$t,0,4).substr(\$t,8,2).substr(\$t,5,2)); }" );
        else if (isset($param_array["YYYYMMDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYMMDD\"][0])); return (substr(\$t,0,4).substr(\$t,4,2).substr(\$t,6,2)); }" );
        else if (isset($param_array["YYYY.MM.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.MM.DD\"][0])); return (substr(\$t,0,4).substr(\$t,5,2).substr(\$t,8,2)); }" );

        else if (isset($param_array["DDMMMYYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMMYYYY\"][0])); return (substr(\$t,5,4).MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.MMM.YYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM.YYYY\"][0])); return (substr(\$t,7,4).MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DDYYYYMMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYYYMMM\"][0])); return (substr(\$t,2,4).MMM_to_MM(substr(\$t,6,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["DD.YYYY.MMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YYYY.MMM\"][0])); return (substr(\$t,3,4).MMM_to_MM(substr(\$t,8,3)).substr(\$t,0,2)); }" );
        else if (isset($param_array["MMMDDYYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDDYYYY\"][0])); return (substr(\$t,5,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }" );
        else if (isset($param_array["MMM.DD.YYYY"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD.YYYY\"][0])); return (substr(\$t,7,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }" );
        else if (isset($param_array["MMMYYYYDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMYYYYDD\"][0])); return (substr(\$t,3,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,7,2)); }" );
        else if (isset($param_array["MMM.YYYY.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.YYYY.DD\"][0])); return (substr(\$t,4,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,9,2)); }" );
        else if (isset($param_array["YYYYDDMMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYDDMMM\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,6,3)).substr(\$t,4,2)); }" );
        else if (isset($param_array["YYYY.DD.MMM"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.DD.MMM\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,8,3)).substr(\$t,5,2)); }" );
        else if (isset($param_array["YYYYMMMDD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYMMMDD\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,4,3)).substr(\$t,7,2)); }" );
        else if (isset($param_array["YYYY.MMM.DD"]))
            eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.MMM.DD\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,5,3)).substr(\$t,9,2)); }" );


        for($i=0; $i<count($lines); $i++){
//          print "<pre>".$lines[$i]."</pre>";
          $YYYY =	YY_to_YYYY($log_yyyy);
          $MM =		M_to_MM($log_mm);
          $DD =		D_to_DD($log_dd);

          if (function_exists("parse")) {
            $YYYYMMDD =	parse($param_array,$lines[$i],$YYYY,$MM,$DD);
            $YYYY =	substr($YYYYMMDD,0,4);
            $MM =	substr($YYYYMMDD,4,2);
            $DD =	substr($YYYYMMDD,6,2);
          }
          else if (isset($param_array["D"]) || isset($param_array["DD"]) || isset($param_array["M"]) || isset($param_array["MM"])){
            if (isset($param_array["D"])) {
              $DD =		D_to_DD(trim(substr($lines[$i],$param_array["D"][0],2)));
            }
            if (isset($param_array["DD"])) {
              $DD =		trim(substr($lines[$i],$param_array["DD"][0],$param_array["DD"][1]));
            }
            if (isset($param_array["M"])){		// DD shown in log
              $MM =		M_to_MM(trim(substr($lines[$i],$param_array["M"][0],$param_array["M"][1])));
            }
            if (isset($param_array["MM"])){		// DD shown in log
              $MM =		trim(substr($lines[$i],$param_array["MM"][0],$param_array["MM"][1]));
            }
            if (isset($param_array["YY"])){		// DD shown in log
              $YYYY =		YY_to_YYYY(trim(substr($lines[$i],$param_array["YY"][0],$param_array["YY"][1])));
            }
            if (isset($param_array["YYYY"])){		// DD shown in log
              $YYYY =		trim(substr($lines[$i],$param_array["YYYY"][0],$param_array["YYYY"][1]));
            }
          }

          $YYYYMMDD =	$YYYY.$MM.$DD;


          // Parse Time: Options are hh:mm and hhmm
          $hhmm =		"";
          if (isset($param_array["hh:mm"])){		// hh:mm shown in log
            $hhmm_arr = explode(":",trim(substr($lines[$i],$param_array["hh:mm"][0],6)));
            if (isset($hhmm_arr[1])) {
              $hhmm =		(strlen($hhmm_arr[0])==1 ? "0" : "").$hhmm_arr[0].$hhmm_arr[1];
            }
          }
          if (isset($param_array["hhmm"])){		// hhmm shown in log
            $hhmm =		substr(trim(substr($lines[$i],$param_array["hhmm"][0],$param_array["hhmm"][1])),0,4);
          }
          if (!is_numeric($hhmm)) {
            $hhmm =	"";
          }

          $KHZ =	(float)(isset($param_array["KHZ"]) ? str_replace(",",".",trim(substr($lines[$i],$param_array["KHZ"][0],$param_array["KHZ"][1]))) : "");
          $ID =		strtoUpper(trim(substr($lines[$i],$param_array["ID"][0],$param_array["ID"][1])));

          $sec =	(isset($param_array["sec"]) ? htmlentities(trim(substr($lines[$i],$param_array["sec"][0],$param_array["sec"][1]))) : "");
          $fmt =	(isset($param_array["fmt"]) ? htmlentities(trim(substr($lines[$i],$param_array["fmt"][0],$param_array["fmt"][1]))) : "");

          $LSB =	"";
          $USB =	"";
          $LSB_approx =	"";
          $USB_approx =	"";

          if (isset($param_array["LSB"])) {
            $LSB =		trim(substr($lines[$i],$param_array["LSB"][0],$param_array["LSB"][1]));
            if (substr($LSB,0,1)=="~") {
              $LSB =	substr($LSB,1);
              $LSB_approx =	"~";
            }
            if ($LSB=="---") {  // Andy Robins logs use --- as blank
              $LSB = "";
            }
          }
          if (isset($param_array["USB"])) {
            $USB =		trim(substr($lines[$i],$param_array["USB"][0],$param_array["USB"][1]));
            if (substr($USB,0,1)=="~") {
              $USB =	substr($USB,1);
              $USB_approx =	"~";
            }
            if ($USB=="---") {
              $USB = "";
            }
          }
          if (isset($param_array["~LSB"])) {
            $LSB =		trim(substr($lines[$i],$param_array["~LSB"][0],$param_array["~LSB"][1]));
            $LSB_approx =	"~";
          }
          if (isset($param_array["~USB"])) {
            $USB =		trim(substr($lines[$i],$param_array["~USB"][0],$param_array["~USB"][1]));
            $USB_approx =	"~";
          }

          // The following parameters are only used for simplifying adding of new signals if the input format happens to include them:
          $GSQ =	(isset($param_array["GSQ"]) ?  trim(substr($lines[$i],$param_array["GSQ"][0],$param_array["GSQ"][1])) : "");
          $QTH =	(isset($param_array["QTH"]) ?  trim(substr($lines[$i],$param_array["QTH"][0],$param_array["QTH"][1])) : "");
          $ITU =	(isset($param_array["ITU"]) ?  trim(substr($lines[$i],$param_array["ITU"][0],$param_array["ITU"][1])) : "");
          $SP =		(isset($param_array["SP"]) ?   trim(substr($lines[$i],$param_array["SP"][0], $param_array["SP"][1])) : "");
          $PWR =	(isset($param_array["PWR"]) ?  trim(substr($lines[$i],$param_array["PWR"][0],$param_array["PWR"][1])) : "");


          if (isset($param_array["+SB-"])) {
            $sb =	str_replace("–","-",trim(substr($lines[$i],$param_array["+SB-"][0],$param_array["+SB-"][1]))); // Convert hyphen symbol to - (For Steve R's Offsets)
            $sb_arr =	explode(" ",$sb);
            for ($j=0; $j<count($sb_arr); $j++){
              $sb =	trim($sb_arr[$j]);
              if ($sb=="X" || $sb=="X-") { // Format used by Jim Smith to indicate sb not present
                $sb="";
              }
              if ($sb=="DAID" or $sb=="DA2ID" or $sb=="DA3ID" or $sb=="DBID" or $sb=="DB2ID" or $sb=="DB3ID") {
                $fmt = $sb;
              }
              if ((substr($sb,0,1)=="+" && substr($sb,strlen($sb)-1,1)=="-") || (substr($sb,0,1)=="-" && substr($sb,strlen($sb)-1,1)=="+")) {
                $USB = abs($sb);
                $LSB = $USB;
              }
              else if(substr($sb,0,1)=="±") {
                $USB = abs(substr($sb,1));
                $LSB = $USB;
              }
              else if(substr($sb,0,3)=="+/-" or substr($sb,0,3)=="-/+") {
                $USB = abs(substr($sb,3));
                $LSB = $USB;
              }
              else if(substr($sb,0,2)=="+-" or substr($sb,0,2)=="-+") {
                $USB = abs(substr($sb,2));
                $LSB = $USB;
              }
              else {
                $approx =	"";
                if (substr($sb,0,1)=="~") {
                  $approx = "~";
                  $sb = substr($sb,1);
                }

                if (substr($sb,0,1)=="+" || substr($sb,strlen($sb)-1,1)=="+") {          // + at start or end
                  $USB = abs($sb);
                  $USB_approx =	$approx;
                } else if (substr($sb,0,1)=="-" || substr($sb,strlen($sb)-1,1)=="-") {   // - at start or end
		    $LSB = abs($sb);
                    $LSB_approx =	$approx;
                } else if(substr($sb,0,1)=="±") {
                    $USB = abs(substr($sb,1));
                    $LSB = $USB;
                } else if (is_numeric($sb)) {
                    $USB = $sb;								 // neither + nor -, therefore USB
                    $USB_approx =	$approx;
                }
              }
            }
          }

          if (isset($param_array["+~SB-"])) {
            $sb =	str_replace("–","-",trim(substr($lines[$i],$param_array["+~SB-"][0],$param_array["+~SB-"][1]))); // Convert hyphen symbol to - (For Steve R's Offsets)
            $sb =	str_replace("~","",$sb); // Remove ~ symbol now we know it's approx
            $sb_arr =	explode(" ",$sb);
            for ($j=0; $j<count($sb_arr); $j++){
              $sb =	trim($sb_arr[$j]);
              if ($sb=="DAID" or $sb=="DA2ID" or $sb=="DA3ID" or $sb=="DBID" or $sb=="DB2ID" or $sb=="DB3ID") {
                $fmt = $sb;
              }
              else if ((substr($sb,0,1)=="+" && substr($sb,strlen($sb)-1,1)=="-") || (substr($sb,0,1)=="-" && substr($sb,strlen($sb)-1,1)=="+")) {
                $USB_approx =	"~";
                $LSB_approx =	"~";
                $USB = abs($sb);
                $LSB = $USB;
              }
              else if(substr($sb,0,1)=="±") {
                $USB_approx =	"~";
                $LSB_approx =	"~";
                $USB = abs(substr($sb,1));
                $LSB = $USB;
              }
              else if(substr($sb,0,3)=="+/-" or substr($sb,0,3)=="-/+") {
                $USB_approx =	"~";
                $LSB_approx =	"~";
                $USB = abs(substr($sb,3));
                $LSB = $USB;
              }
              else if(substr($sb,0,2)=="+-" or substr($sb,0,2)=="-+") {
                $USB_approx =	"~";
                $LSB_approx =	"~";
                $USB = abs(substr($sb,2));
                $LSB = $USB;
              }
              else {
                if (substr($sb,0,1)=="+" || substr($sb,strlen($sb)-1,1)=="+") {          // + at start or end
                  $USB_approx =	"~";
                  $USB = abs($sb);
                } else if (substr($sb,0,1)=="-" || substr($sb,strlen($sb)-1,1)=="-") {   // - at start or end
                  $LSB_approx =	"~";
                  $LSB = 	abs($sb);
                } else {
                  if (is_numeric($sb)) {
                    $USB_approx =	"~";
                    $USB = 		$sb;								 // neither + nor -, therefore USB
                  }
                }
              }
            }
          }

          // Cope with Brian Keyte's +0.4 1- offsets
          if (isset($param_array["+K-"])) {
            $sb =	trim(str_replace("–","-",trim(substr($lines[$i],$param_array["+K-"][0],$param_array["+K-"][1])))); // Convert hyphen symbol to -
            if ($sb ===	"0.4") {
              $USB_approx =	"~";
              $LSB_approx =	"~";
              $USB = "400";
              $LSB = "400";
            }
            else if ($sb ===	"+0.4") {
              $USB_approx =	"~";
              $USB = "400";
            }
            else if ($sb ===	"-0.4") {
              $LSB_approx =	"~";
              $LSB = "400";
            }
            else if ($sb ===	"1") {
              $USB_approx =	"~";
              $LSB_approx =	"~";
              $USB = "1020";
              $LSB = "1020";
            }
            else if ($sb ===	"+1") {
              $USB_approx =	"~";
              $USB = "1020";
            }
            else if ($sb ===	"-1") {
              $LSB_approx =	"~";
              $LSB = "1020";
            }
          }


          if (isset($param_array["ABS"])) {
            $ABS =	trim(substr($lines[$i],$param_array["ABS"][0],$param_array["ABS"][1]));
            $ABS_arr =	explode(" ",$ABS);
            for ($j=0; $j<count($ABS_arr); $j++){
//              print "ABS=$ABS, KHZ=$KHZ";
              $ABS = (double)trim($ABS_arr[$j]);
              if ($ABS) {
                if ($ABS>(float)$KHZ) {
                  $USB = round((1000*($ABS-$KHZ)));
                }
                else {
                  $LSB = round((1000*($KHZ-$ABS)));
                }
              }
            }
          }

          if (isset($param_array["~ABS"])) {
            $ABS =	trim(substr($lines[$i],$param_array["~ABS"][0],$param_array["~ABS"][1]));
            $ABS_arr =	explode(" ",$ABS);
            for ($j=0; $j<count($ABS_arr); $j++){
              $ABS = (double)trim($ABS_arr[$j]);
              if ($ABS) {
                if ($ABS>(float)$KHZ) {
                  $USB = round((1000*($ABS-$KHZ)));
                  $USB_approx = "~";
                }
                else {
                  $LSB = round((1000*($KHZ-$ABS)));
                  $LSB_approx = "~";
                }
              }
            }
          }

          $YYYYMMDD =	abs($YYYYMMDD);


          if($ID && $YYYYMMDD) {
            $sta_sel =	"";
            if($ID && $YYYYMMDD) {
              $sql =
                "SELECT\n"
			   ."  *\n"
			   ."FROM\n"
			   ."  `signals`\n"
			   ."WHERE\n"
			   .($KHZ ?
                  ($KHZ>1740 ?
                    "  `khz` >= $KHZ-".swing_HF." AND `khz` <= $KHZ+".swing_HF." AND\n"
                  :
                    "  `khz` >= $KHZ-".swing_LF." AND `khz` <= $KHZ+".swing_LF." AND\n")
               :
                  ""
               )
			." `call` = \"$ID\"";
//              print("<pre>$sql</pre>");

              $result = @mysql_query($sql);
              if (mysql_error()) {
                $out[] = "Problem looking up station - frequency was $KHZ";
              }
              if ($result && mysql_num_rows($result)) {
                $total_loggings++;
                if (mysql_num_rows($result) == 1) {
                  $out[] =	"<tr class='rownormal'>\n";
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
                      case OTHER:	$bgcolor = " bgcolor='#B8F8FF' title='Other Utility Station'";		break;
                    }
                  }
                  $out[] =	"  <td><input type='hidden' name='ID[]' value='".$row["ID"]."'>".(((float)$row['khz']<198 || (float)$row['khz'] > 530) ? "<font color='darkorange'><b>".(float)$row['khz']."</b></font>" : (float)$row['khz'])."</td>\n";
                  $out[] =	"  <td$bgcolor><a href='javascript:signal_info(\"".$row["ID"]."\")'>$ID</a></td>\n";
                  $out[] =	"  <td".($row['QTH']?"":" bgcolor='#FFE7B9' title='Please provide a value for QTH if you have one'")."><font color='#606060'>".$row['QTH']."</font></td>\n";
                  $out[] =	"  <td><font color='#606060'>".$row['SP']."</font></td>\n";
                  $out[] =	"  <td><font color='#606060'>".$row['ITU']."</font></td>\n";
                  $out[] =	"  <td><font color='#606060'>".($row["GSQ"] ? "<a onmouseover='window.status=\"View map for ".(float)$row["khz"]."-".$row["call"]."\";return true;' onmouseout='window.status=\"\";return true;' href='javascript:popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show map (accuracy limited to nearest Grid Square)'>".$row["GSQ"]."</a>" : "&nbsp;")."</font></td>\n";
                  $out[] =	"  <td><font color='#606060'>".(strpos($row['heard_in'],$listener_in)===false ? "<font color='#008000'><b>".$row['heard_in']."</b></font>" : highlight($row['heard_in'],$listener_in))."</font></td>\n";
                }
                else {            
                  $out[] =	"<tr bgcolor='#ffe0a0'>\n";
                  $out[] =	"  <td colspan='7'><select name='ID[]' class='formfixed'>\n";
                  for ($j=0; $j<mysql_num_rows($result); $j++) {
                    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
                    $out[] =	"<option".($row["active"]=="0" ? " style='background-color: #d0d0d0'" : "")." value='".$row["ID"]."'>".(float)$row["khz"]." ".$ID." ".$row["QTH"]." ".pad_nbsp($row["SP"],3)." ".$row["ITU"]." ".$row["GSQ"]." ".$row["heard_in"]."</option>\n";
                  }
                  $out[] =	"</select></td>\n";
                }
                $out[] =	"  <td align='center'><input type='hidden' name='YYYYMMDD[]' value='$YYYYMMDD'>";
                
                if (strlen($YYYYMMDD)!=8){
                  $date_fail = true;
                  $out[] =	"<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                }
                else if ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                  $date_fail = true;
                  $out[] =	"<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                }
                else {
                  $out[] =	($YYYY<2005 ? "<font color='darkorange'><b>$YYYY</b></font>" : "$YYYY");
                  if (!checkdate($MM,$DD,$YYYY)) {
                    $date_fail = true;
                    $out[] =	"<font color='red'><b><strike>$MM</strike></b></font>";
                  }
                  else {
                    $out[] =	$MM;
                  }
                  if (!checkdate($MM,$DD,$YYYY)) {
                    $date_fail = true;
                    $out[] =	"<font color='red'><b><strike>$DD</strike></b></font>";
                  }
                  else {
                    $out[] =	$DD;
                  }
                }
                $out[] =	"</td>\n";
                $out[] =	"  <td align='center'><input type='hidden' name='hhmm[]' value='$hhmm'>";
                if ((strlen($hhmm)!=0 && strlen($hhmm)!=4) || substr($hhmm,0,2)>23 || substr($hhmm,2,2)>59) {
                  $date_fail = true;
                  $out[] =	"<font color='red'><b><strike>$hhmm</strike></b></font>";
                }
                else {
                  $out[] =	$hhmm;
                }
                $out[] =	"</td>\n";
                $out[] =	"  <td><input type='hidden' name='LSB_approx[]' value='$LSB_approx'><input type='hidden' name='LSB[]' value='$LSB'>".((($LSB>0 && $LSB<350) || ($LSB>450 && $LSB<960) || ($LSB>1080)) ? "<font color='darkorange'><b>$LSB_approx$LSB</b></font>" : "$LSB_approx$LSB")."</td>\n";
                $out[] =	"  <td><input type='hidden' name='USB_approx[]' value='$USB_approx'><input type='hidden' name='USB[]' value='$USB'>".((($USB>0 && $USB<350) || ($USB>450 && $USB<960) || ($USB>1080)) ? "<font color='darkorange'><b>$USB_approx$USB</b></font>" : "$USB_approx$USB")."</td>\n";
                $out[] =	"  <td><input type='hidden' name='sec[]' value=\"$sec\">$sec</td>\n";
                $out[] =	"  <td><input type='hidden' name='fmt[]' value=\"$fmt\">$fmt</td>\n";
                $out[] =	"  <td>&nbsp;</td>\n";
                $out[] =	"</tr>\n";
              }
              else {
                $out[] =	"<tr bgcolor='#ffd0d0' title='signal not listed in database'>\n";
                $out[] =	"  <td>".(((float)$KHZ<198 || (float)$KHZ > 530) ? "<font color='darkorange'><b>".(float)$KHZ."</b></font>" : (float)$KHZ)."</td>\n";
                $out[] =	"  <td>$ID</td>\n";
                $out[] =	"  <td>$QTH</td>\n";
                $out[] =	"  <td>$SP</td>\n";
                $out[] =	"  <td>$ITU</td>\n";
                $out[] =	"  <td>$GSQ</td>\n";
                $out[] =	"  <td>&nbsp;</td>\n";
                $out[] =	"  <td align='center'>";
                if (strlen($YYYYMMDD)!=8){
                  $date_fail = true;
                  $out[] =	"<font color='red'><b><strike>$YYYYMMDD</strike></i></b></font>";
                }
                else if ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                  $date_fail = true;
                  $out[] =	"<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                }
                else {
                  $out[] =	($YYYY<2003 ? "<font color='darkorange'><b>$YYYY</b></font>" : "$YYYY");
                  if (!checkdate($MM,$DD,$YYYY)) {
                    $date_fail = true;
                    $out[] =	"<font color='red'><b><strike>$MM</strike></b></font>";
                  }
                  else {
                    $out[] =	$MM;
                  }
                  if (!checkdate($MM,$DD,$YYYY)) {
                    $date_fail = true;
                    $out[] =	"<font color='red'><b><strike>$DD</strike></b></font>";
                  }
                  else {
                    $out[] =	$DD;
                  }
                }
                $out[] =	"</td>\n";

                $out[] =	"  <td align='center'>$hhmm</td>\n";
                $out[] =	"  <td>$LSB_approx$LSB</td>\n";
                $out[] =	"  <td>$USB_approx$USB</td>\n";
                $out[] =	"  <td><input type='hidden' name='sec[]' value=\"$sec\">$sec</td>\n";
                $out[] =	"  <td><input type='hidden' name='fmt[]' value=\"$fmt\">$fmt</td>\n";
                $out[] =	"  <td><a href='javascript:signal_add(\"$ID\",\"$KHZ\",\"$GSQ\",\"$QTH\",\"$SP\",\"$ITU\",\"$PWR\")'><b>Add...</b></a></td>\n";
                $out[] =	"</tr>";
                $unresolved_signals[] =	$lines[$i];
              }
            }
          }
        }
        if (!count($unresolved_signals) && !$date_fail) {
          $out[] =	"  <tr class='downloadTableHeadings_nosort'>\n";
          $out[] =	"    <th colspan='14'><input type='button' value='Submit Log' class='formbutton' name='go' onclick='this.value=\"Please wait..\";this.disabled=true;submit_log()'></th>\n";
          $out[] =	"  </tr>\n";
          $out[] =	"<script language='javascript' type='text/javascript'>document.form.go.focus()</script>\n";
        }
        else {
          $out[] =	"  <tr class='downloadTableHeadings_nosort'>\n";
          $out[] =	"    <th colspan='14'><input type='button' value='Serious errors found - Go Back...' class='formbutton' name='go' onclick='history.back()'></th>\n";
          $out[] =	"  </tr>\n";
          $out[] =	"<script language='javascript' type='text/javascript'>document.form.go.focus()</script>\n";
       }

        $out[] =	"</table>\n";

        if (count($unresolved_signals)) {
          $out[] =	"<p><b>Issues:</b><br>\n";
          $out[] =	"<small>There ".(count($unresolved_signals)!=1 ? "are <b><font color='red'>".count($unresolved_signals)." unresolved signals</font></b>" : "<b><font color='red'>is one</font></b> unresolved signal")." contained in the log</b>.</small><br>";
          $out[] =	"<textarea rows='10' cols='90'>Unresolved records\n---------------------------------\n".implode("",$unresolved_signals)."</textarea>";
        }
        else {
          $out[] =	"<span class='p'><small><b>Total Loggings in this report: $total_loggings</b></small></span>";
        }


        $out[] =	"<p><a name='next'></a><b>Next Steps...</b><small>\n";
        $out[] =	"<ul>\n";
        $out[] =	"<li>Please review the results shown above, especially warnings (<font color='darkorange'><b>orange</b></font>) and serious errors (<font color='red'><b>red</b></font>).</li>\n";
        $out[] =	"<li>Serious errors (invalid dates and unrecognised signals) prevent the <b>Submit Log</b> button from appearing.</li>";
        $out[] =	"<li>If LSB or USB appear to be too high or low, click on the link shown for the signal ID to see the offset history.</li>\n";
        $out[] =	"<li>If data seems to have been misread, click <a href='javascript:history.back()'><b><u>here</u></b></a> to check your formatting and try again.<br>\n";
        $out[] =	"(See the <b>Help</b> page on the main menu for details on acceptable formats).</li>\n";
        $out[] =	"<li>If a signal is not in the system, a link is provided to add the signal - check the details carefully before you add a new signal.</li>\n";
        $out[] =	"<li>If you have just added a signal for this log and you are asked by your browser whether you wish to 'Repost Data', say 'Yes'.</li>\n";

        if (!count($unresolved_signals) && !$date_fail) {
          $out[] =	"<li>If you are happy with the results shown above, press <b>Submit Log</b> to process the data.</li>\n";
        }
        $out[] =	"</ul>\n";
      }
    break;

    case "submit_log":
      $out[] =
         "<br><input type='submit' value='&nbsp;Done&nbsp;' name='go' onclick='window.close();' class='formbutton'>"
        ."<script language='javascript' type='text/javascript'>"
        ."document.form.go.focus();\n"
        ."</script>\n";
    break;
  }

  if ($listenerID!="" && $submode=="") {
    $out[] =
       "&nbsp;"
      ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
      ."  <tr>\n"
      ."    <th class='downloadTableHeadings_nosort'>Log to Parse</th>\n"
      ."  </tr>\n"
      ."  <tr class='rownormal'>\n"
      ."    <td><input name='log_format' class='fixed_heading' size='105' value='$log_format'><input class='formbutton' name='save' type='button' value='Save' onclick='this.disabled=true;document.form.go.disabled=true;document.form.conv.disabled=true;document.form.submode.value=\"save_format\";document.form.submit()'>"
      ."  </tr>\n"
      ."  <tr class='rownormal'>\n"
      ."    <td><textarea rows='30' cols='110' class='fixed' name='log_entries' onKeyUp='check_for_tabs(document.form);' onchange='check_for_tabs(document.form);'>".stripslashes($log_entries)."</textarea>\n"
      ."  </tr>\n";
    if ((!$log_shows_YYYY || !$log_shows_MM || !$log_shows_DD)) {
      $out[] =
         "  <tr class='rownormal'>\n"
        ."    <td>The following details are also required: &nbsp; \n";
      if (!$log_shows_DD) {
        $out[] =	"Day <input type='text' name='log_dd' size='2' maxlength='2' class='formfield' value='$log_dd'>\n";
      }
      if (!$log_shows_MM) {
        $out[] =	"Month <input type='text' name='log_mm' size='2' maxlength='2' class='formfield' value='$log_mm'>\n";
      }
      if (!$log_shows_YYYY) {
        $out[] =	"Year <input type='text' name='log_yyyy' size='4' maxlength='4' class='formfield' value='$log_yyyy'>\n";
      }
      $now =	    mktime();
      $now_DD =	    gmdate("d",$now);
      $now_MM =	    gmdate("m",$now);
      $now_YYYY =	gmdate("Y",$now);

      $out[] =
         "<input type='button' value='&lt;-- Current' class='formButton' onclick=\""
        .(!$log_shows_DD ?   "if (document.form.log_dd.value=='')   { document.form.log_dd.value='$now_DD'; };" : "")
        .(!$log_shows_MM ?   "if (document.form.log_mm.value=='')   { document.form.log_mm.value='$now_MM'; };" : "")
        .(!$log_shows_YYYY ? "if (document.form.log_yyyy.value=='') { document.form.log_yyyy.value='$now_YYYY'; };" : "")
        ."\"></td>\n"
        ."  </tr>\n";
    }

    $out[] =
       "  <tr class='rownormal'>\n"
      ."    <th>"
      ."<input type='button' value='Tabs > Spaces' class='formbutton' name='conv' onclick='tabs_to_spaces(document.form)'"
      .(!preg_match("/	/",$log_entries) ? " disabled='1'" : "").">\n"
      ."<input type='button' value='Line Up' class='formbutton' name='lineup' onclick='line_up(document.form)'>\n"
      ."<input type='button' name='go' value='Parse Log' class='formbutton' onclick='if (parse_log(document.form)) { document.form.go.value=\"Please wait..\";document.form.go.disabled=true;document.form.conv.disabled=true;document.form.save.disabled=true;document.form.submode.value=\"parse_log\";document.form.submit();}'> "
      ."<script language='javascript' type='text/javascript'>"
      ."document.form.go.focus();\n"
      ."</script>"
      ."</th>\n"
      ."  </tr>\n"
      ."</table>\n";
  }
  $out[] =	"</form>";
  return implode($out,"");
}

function admin_polls() {
  global $mode, $submode;
  global $HTTP_POST_FILES;

  $out = array();

  $out[] =	"<h2>Manage Polls</h2>";
  return implode($out,"");
}



// ************************************
// * logon()                          *
// ************************************
function logon() {
  global $server,$mode,$submode;
  global $user,$password;

  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    return 	 "<h2>Logon</h2><p>You are now logged on as an Administrator and may perform administrative functions.<br><br>\nTo log off, select <b>Log Off</b> from the main menu.</p>";
  }
  return "<h2>Logon</h2><p>You must logon in order to perform administrative functions.</p>"
	."<form name='form' action='".system_URL."' method='post'>\n"
	."<input type='hidden' name='mode' value='$mode'>\n"
	."<input type='hidden' name='submode' value='logon'>\n"
	."<br><br><table cellpadding='4' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
	."  <tr>\n"
	."    <td colspan='2' class='downloadTableHeadings_nosort'>Administrator Logon</td>"
	."  </tr>\n"
	."  <tr class='rownormal'>\n"
	."    <td>Username</td>"
	."    <td><input name='user' value='$user' size='20'</td>"
	."  </tr>\n"
	."  <tr class='rownormal'>\n"
	."    <td>Password</td>"
	."    <td><input type='password' name='password' size='20'</td>"
	."  </tr>\n"
	."  <tr class='rownormal'>\n"
	."    <td colspan='2' align='center'><input type='submit' value='Logon'></td>"
	."  </tr>\n"
	."</table><script language='javascript' type='text/javascript'>document.form.user.focus();</script>\n";
}
?>
