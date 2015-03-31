<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  main.inc                     *
// * Owner:     Martin Francis               *
// * Created:   01/09/2003 (MF)              *
// * Revised:   15/12/2003 (MF)              *
// *                                         *
// *******************************************

$head =		array();
$head[] =	"<html>\n";
$head[] =	"<head>\n";
$head[] =	"<style>\n";
$head[] =	"body { font-family: Verdana,Arial,Helvetica; }\n";
$head[] =	"td   { font-family: courier new, courier regular; width: 20px; text-align: center;}\n";
$head[] =	"th   { font-family: courier new, courier regular; background: rgb(255,255,230); }\n";
$head[] =	"th.l { text-align: left; }\n";
$head[] =	"th.new { background: rgb(255,200,200); }\n";
$head[] =	"th.admin { background: rgb(255,255,160); }\n";
$head[] =	".n   { text-align: center; background: rgb(250,250,250); }\n";
$head[] =	".f   { text-align: center; background: rgb(255,140,140); }\n";
$head[] =	".p   { text-align: center; background: rgb(255,200,200); }\n";
$head[] =	".w   { text-align: center; background: rgb(255,220,200); }\n";
$head[] =	".m   { text-align: center; background: rgb(220,255,220); }\n";
$head[] =	".g   { text-align: center; background: rgb(100,255,100); }\n";
$head[] =	".v   { text-align: center; background: rgb(0,255,0); }\n";
$head[] =	"input.50 { width: 50px; }\n";
$head[] =	"input.80 { width: 80px; }\n";
$head[] =	"a.nav { text-decoration: none; font-family: Arial; }\n";
$head[] =	"a.info { text-decoration: none; color: rgb(0,0,0); }\n";
$head[] =	"</style>\n";
$head[] =	"<script language='javascript' type='text/javascript'>\n";
$head[] =	"function map(lat,lon) {\n";
$head[] =	"  var map_hd =	window.open(\"http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude=\"+lat+\"&longitude=\"+lon+\"&size=big&zoom=1\",\"map\",\"width=800,height=600,resizable=1,scrollbars=1,status=0,menubar=0,location=1,toolbar=0\");\n";
$head[] =	"  map_hd.focus();\n";
$head[] =	"}\n";
$head[] =	"function region_map() {\n";
$head[] =	"  var map2_hd =	window.open(\"http://www.classaxe.com/dx/images/pacific_map.gif\",\"map2\",\"width=360,height=420,resizable=1,scrollbars=0,status=0,menubar=1,location=1,toolbar=0\");\n";
$head[] =	"  map2_hd.focus();\n";
$head[] =	"}\n";
$head[] =	"function get_range_bearing(qth_lat,qth_lon,dx_lat,dx_lon,units) {\n";
$head[] =	"  if (qth_lat == dx_lat && qth_lon==dx_lon) {\n";
$head[] =	"    return new Array(0,0);\n";
$head[] =	"  }\n";
$head[] =	"  var dlon = (dx_lon - qth_lon)\n";
$head[] =	"  if (Math.abs(dlon) > 180) {\n";
$head[] =	"    dlon = (360 - Math.abs(dlon))*(0-(dlon/Math.abs(dlon)));\n";
$head[] =	"  }\n";
$head[] =	"  var rinlat =		qth_lat*0.01745;	// convert to radians\n";
$head[] =	"  var rinlon =		qth_lon*0.01745;\n";
$head[] =	"  var rfnlat =		dx_lat*0.01745;\n";
$head[] =	"  var rdlon =		dlon*0.01745;\n";
$head[] =	"  var rgcdist =	Math.acos(Math.sin(rinlat)*Math.sin(rfnlat)+Math.cos(rinlat)*Math.cos(rfnlat)*Math.cos(rdlon));\n";
$head[] =	"  var rincourse =	(Math.sin(rfnlat)-Math.cos(rgcdist)*Math.sin(rinlat))/(Math.sin(rgcdist)*Math.cos(rinlat));\n";
$head[] =	"  rincourse =		Math.acos(rincourse);\n";
$head[] =	"  incourse =		rincourse*57.3;\n";
$head[] =	"  if (dlon < 0) {\n";
$head[] =	"    incourse =		360 - incourse;\n";
$head[] =	"  }\n";
$head[] =	"  switch (units) {\n";
$head[] =	"    case \"mi\": var dx = Math.round(Math.abs(rgcdist)*3958.284); break;\n";
$head[] =	"    case \"nm\": var dx = Math.round(Math.abs(rgcdist)*3439.719); break;\n";
$head[] =	"    default:   var dx = Math.round(Math.abs(rgcdist)*6370.614); break;\n";
$head[] =	"  }\n";
$head[] =	"  return new Array(Math.round(incourse),dx);\n";
$head[] =	"}\n";
$head[] =	"</script>\n";
$head[] =	"</head>\n";
$head[] =	"<body bgcolor='#ffffff'>\n";
$head[] =	"<form name='form' action='./' method='POST'>\n";


$foot =		array();
$foot[] =	"</form>\n";
$foot[] =	"</body>\n";
$foot[] =	"</html>\n";

// ************************************
// * PHP Environment detect           *
// ************************************
// We want to use $_COOKIE, $_GET and $_POST where possible, but
// only PHP 4.1.0 and later provide this.
// $global_php_assoc = 1 for version 4.1 and later

$global_php_assoc = php_version_check("4.1.0");

if ($global_php_assoc) {
  extract($_REQUEST);		// Extracts all request variables (GET and POST) into global scope.
}

if (!isset($mode)) {
  $mode = "station_list";
}

if (!isset($submode)) {
  $submode = "";
}


// ************************************
// * mode selection switching:        *
// ************************************

$out =	array();
switch($mode) {

  // ************************************
  // * $mode = db_export                *
  // ************************************
  case "db_export":
    if ($admin) {
      $out[] =	db_export_sql_structure();
      $out[] =	db_export_sql_data();

      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=reset.sql");
      header("Content-length: ".strlen(implode($out,'')));
      print(implode($out,''));
    }
  break;



  // ************************************
  // * $mode = log_edit_form            *
  // ************************************
  case "log_edit_form":
    if ($admin) {
      if ($submode == "delete" and $logID != "") {
        table_delete_record($logID,TYPE_LOG);
      }
      if ($submode == "add") {
        log_add($destinationID,$new_yyyymmdd,$new_hhmm,$new_rx,$new_notes);
        $submode = "update";
      }

      if ($submode == "update") {
        if (isset($ID)) {
          for($i=0; $i<count($ID); $i++) {
            log_update($ID[$i],$destinationID,$yyyymmdd[$i],$hhmm[$i],$rx[$i],$notes[$i]);
          }
        }
      }


      $station =		table_get($destinationID,TYPE_STATION);
      $log_entries =	$log_entries =	log_for_station($destinationID);
      $out[] =		implode($head,"");
      $out[] =		"<title>Edit Log: ".$station['khz']."-".$station['call']."</title>\n";
      $out[] =		"<h1>Edit Log: ".$station['khz']."-".$station['call']."</h1>\n";

      $out[] =		"<input type='hidden' name='mode' value='log_edit_form'>\n";
      $out[] =		"<input type='hidden' name='submode' value='add'>\n";
      $out[] =		"<input type='hidden' name='destinationID' value='$destinationID'>\n";
      $out[] =		"<input type='hidden' name='logID' value=''>\n";
      $out[] =		"<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n";
      $out[] =		"  <tr>\n";
      $out[] =		"    <th>yyyymmdd</th>\n";
      $out[] =		"    <th>hhmm</th>\n";
      $out[] =		"    <th>RX</th>\n";
      $out[] =		"    <th>Notes</th>\n";
      $out[] =		"    <th>Action</th>\n";
      $out[] =		"  </tr>\n";

      if ($log_entries) {
        for ($i=0; $i<count($log_entries); $i++) {
          $log_entry =	$log_entries[$i];
          $out[] =	"  <tr>\n";
          $out[] =	"    <th><input type='hidden' name='ID[]' value='".$log_entry['ID']."'>";
          $out[] =	"<input name='yyyymmdd[]' class='80' value='".$log_entry['yyyymmdd']."' maxlength='8' size='8'></th>\n";
          $out[] =	"    <th><input name='hhmm[]' class='50' value='".$log_entry['hhmm']."' maxlength='4' size='4'></th>\n";
          $out[] =	"    <th><select name='rx[]'>\n";
          $out[] =	"<option value='F' class='F'".(($log_entry['rx']=='F')?(" selected"):("")).">Faint</option>\n";
          $out[] =	"<option value='P' class='P'".(($log_entry['rx']=='P')?(" selected"):("")).">Poor</option>\n";
          $out[] =	"<option value='W' class='W'".(($log_entry['rx']=='W')?(" selected"):("")).">Weak</option>\n";
          $out[] =	"<option value='M' class='M'".(($log_entry['rx']=='M')?(" selected"):("")).">Medium</option>\n";
          $out[] =	"<option value='G' class='G'".(($log_entry['rx']=='G')?(" selected"):("")).">Good</option>\n";
          $out[] =	"<option value='V' class='V'".(($log_entry['rx']=='V')?(" selected"):("")).">V.Good</option>\n";
          $out[] =	"</select></th>";
          $out[] =	"    <th><input name='notes[]' value=\"".$log_entry['notes']."\" maxlength='255' size='30'></th>\n";
          $out[] =	"    <th class='l'>";
          $out[] =	"<input type='button' value='Delete' ";
          $out[] =	"onclick='if (confirm(\"DELETE\\n\\nConfirm deletion of ".$station['call']."-".$station['khz']." entry\\nfor ".$log_entry['yyyymmdd']."@".$log_entry['hhmm']."\")) { document.form.submode.value=\"delete\";document.form.logID.value=\"".$log_entry['ID']."\";document.form.submit();}'>";
          $out[] =	"</th>\n";
          $out[] =	"  </tr>\n";
        }
      }
      $out[] =	"  <tr>\n";
      $out[] =	"    <th class='new'>";
      $out[] =	"<input name='new_yyyymmdd' class='80' value='' maxlength='8' size='8'></th>\n";
      $out[] =	"    <th class='new'><input name='new_hhmm' class='50' value='' maxlength='4' size='4'></th>\n";

      $out[] =	"    <th class='new'><select name='new_rx'>\n";
      $out[] =	"<option value='F' class='F'>Faint</option>\n";
      $out[] =	"<option value='P' class='P'>Poor</option>\n";
      $out[] =	"<option value='W' class='W'>Weak</option>\n";
      $out[] =	"<option value='M' class='M'>Medium</option>\n";
      $out[] =	"<option value='G' class='G'>Good</option>\n";
      $out[] =	"<option value='V' class='V'>V.Good</option>\n";
      $out[] =	"</select></th>";

      $out[] =	"    <th class='new'><input name='new_notes' value='' maxlength='255' size='30'></th>\n";
      $out[] =	"    <th class='new'>";
      $out[] =	"<input type='button' value='Add' ";
      $out[] =	"onclick='document.form.submode.value=\"add\";document.form.submit();'>";
      $out[] =	"</th>\n";
      $out[] =	"  </tr>\n";

      $out[] =		"</table>\n";
      $out[] =		"<input type='button' value='Apply Changes' onclick='document.form.submode.value=\"update\";document.form.submit();'>\n";
      $out[] =		"<input type='reset' value='Forget Changes'>\n";

      if ($log_entries) {
        $out[] =		count($log_entries)." log entr".((count($log_entries)==1)?("y"):("ies"))." for this beacon";
      }

      $out[] =		implode($foot,"");
      print(implode($out,""));
    }
  break;



  // ************************************
  // * $mode = log_add_form            *
  // ************************************
  case "log_add_form":
    if ($admin) {
      if ($submode == "add") {
        log_add($destinationID,$new_yyyymmdd,$new_hhmm,$new_rx,$new_notes);
      }

      $station =		table_get($destinationID,TYPE_STATION);
      $out[] =		implode($head,"");
      $out[] =		"<title>Add Log Entry for ".$station['khz']."-".$station['call']."</title>\n";
      $out[] =		"<h1>Add ".($submode=='add' ? "another ": "")."Log Entry for ".$station['khz']."-".$station['call']."</h1>\n";

      $out[] =		"<input type='hidden' name='mode' value='log_add_form'>\n";
      $out[] =		"<input type='hidden' name='submode' value='add'>\n";
      $out[] =		"<input type='hidden' name='destinationID' value='$destinationID'>\n";
      $out[] =		"<input type='hidden' name='logID' value=''>\n";
      $out[] =		"<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n";
      $out[] =		"  <tr>\n";
      $out[] =		"    <th>yyyymmdd</th>\n";
      $out[] =		"    <th>hhmm</th>\n";
      $out[] =		"    <th>RX</th>\n";
      $out[] =		"    <th>Notes</th>\n";
      $out[] =		"    <th>Action</th>\n";
      $out[] =		"  </tr>\n";

      $out[] =		"  <tr>\n";
      $out[] =		"    <th class='new'>";
      $out[] =		"<input name='new_yyyymmdd' class='80' value='' maxlength='8' size='8'></th>\n";
      $out[] =		"    <th class='new'><input name='new_hhmm' class='50' value='' maxlength='4' size='4'></th>\n";

      $out[] =		"    <th class='new'><select name='new_rx'>\n";
      $out[] =		"<option value='F' class='F'>Faint</option>\n";
      $out[] =		"<option value='P' class='P'>Poor</option>\n";
      $out[] =		"<option value='W' class='W'>Weak</option>\n";
      $out[] =		"<option value='M' class='M'>Medium</option>\n";
      $out[] =		"<option value='G' class='G'>Good</option>\n";
      $out[] =		"<option value='V' class='V'>V.Good</option>\n";
      $out[] =		"</select></th>";

      $out[] =		"    <th class='new'><input name='new_notes' value='' maxlength='255' size='30'></th>\n";
      $out[] =		"    <th class='new'>";
      $out[] =		"<input type='button' value='Add' ";
      $out[] =		"onclick='document.form.submode.value=\"add\";document.form.submit();'>";
      $out[] =		"</th>\n";
      $out[] =		"  </tr>\n";

      $out[] =		"</table>\n";
      $out[] =		($submode=='add' ? "(previous record was added)": "");
      $out[] =		implode($foot,"");
      print(implode($out,""));
    }
  break;


  // ************************************
  // * $mode = station_edit_form        *
  // ************************************
  case "station_edit_form":
    if ($admin) {
      if ($submode == "delete" and $destinationID != "") {
        table_delete_record($destinationID,TYPE_STATION);
      }

      if ($submode == "add") {
        station_add($new_khz,$new_call,$new_qth,$new_sta,$new_cnt,$new_lat,$new_lon);
        $submode = "update";
      }

      if ($submode == "update" && isset($ID)) {
        for($i=0; $i<count($ID); $i++) {
          station_update($ID[$i],$khz[$i],$call[$i],$qth[$i],$sta[$i],$cnt[$i],$lat[$i],$lon[$i]);
        }
      }


      $out[] =		implode($head,"");
      $out[] =		"<h1>Edit Beacons</h1>\n";
      $out[] =		"<input type='hidden' name='destinationID' value=''>\n";
      $out[] =		"<input type='hidden' name='mode' value='station_edit_form'>\n";
      $out[] =		"<input type='hidden' name='submode' value=''>\n";
      $out[] =		"<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n";
      $out[] =		"  <tr>\n";
      $out[] =		"    <th>KHz</th>\n";
      $out[] =		"    <th>Call</th>\n";
      $out[] =		"    <th>QTH</th>\n";
      $out[] =		"    <th>State</th>\n";
      $out[] =		"    <th>ITU</th>\n";
      $out[] =		"    <th>Latitude</th>\n";
      $out[] =		"    <th>Longitude</th>\n";
      $out[] =		"    <th>Delete</th>\n";
      $out[] =		"  </tr>\n";
      if ($stations = table_get_all(TYPE_STATION,"khz")) {
        for ($i=0; $i<count($stations); $i++) {
          $out[] =	"  <tr>\n";
          $out[] =	"    <th class='l'><input type='hidden' name='ID[]' value='".$stations[$i]['ID']."'>";
          $out[] =	"<input name='khz[]'  class='50' maxlength='5' size='5' value='".$stations[$i]['khz']."'></th>\n";
          $out[] =	"    <th class='l'><input name='call[]' class='50' size='5' maxlength='5' value='".$stations[$i]['call']."'></th>\n";
          $out[] =	"    <th class='l'><input name='qth[]'  maxlength='20' value='".$stations[$i]['qth']."'></th>\n";
          $out[] =	"    <th><input name='sta[]'  class='50' size='2' maxlength='2' value='".$stations[$i]['sta']."'></th>\n";
          $out[] =	"    <th class='l'><input name='cnt[]'  class='50' size='3' maxlength='3' value='".$stations[$i]['cnt']."'></th>\n";
          $out[] =	"    <th class='l'><input name='lat[]'  class='80' size='10' value='".$stations[$i]['lat']."'></th>\n";
          $out[] =	"    <th class='l'><input name='lon[]'  class='80' size='10' value='".$stations[$i]['lon']."'></th>\n";
          $out[] =	"    <th>";
          $out[] =	"<input type='button' value='Delete' ";
          $out[] =	"onclick='if (confirm(\"DELETE\\n".$stations[$i]['call']."-".$stations[$i]['khz']."\\n\\nAre you sure?\")) { document.form.submode.value=\"delete\";document.form.destinationID.value=\"".$stations[$i]['ID']."\";document.form.submit();}'>";
          $out[] =	"</th>\n";
          $out[] =	"  </tr>\n";
        }
      }
      $out[] =	"  <tr>\n";
      $out[] =	"    <th class='new'>";
      $out[] =	"<input name='new_khz'  class='50' size='5' maxlength='5' value=''></th>\n";
      $out[] =	"    <th class='new'><input name='new_call' class='50' size='5' maxlength='5' value=''></th>\n";
      $out[] =	"    <th class='new'><input name='new_qth'  maxlength='20' value=''></th>\n";
      $out[] =	"    <th class='new'><input name='new_sta'  class='50' size='2' maxlength='2' value=''></th>\n";
      $out[] =	"    <th class='new'><input name='new_cnt'  class='50' size='3' maxlength='3' value=''></th>\n";
      $out[] =	"    <th class='new'><input name='new_lat'  class='80' size='10' value=''></th>\n";
      $out[] =	"    <th class='new'><input name='new_lon'  class='80' size='10' value=''></th>\n";
      $out[] =	"    <th class='new'>";
      $out[] =	"<input type='button' value='Add' ";
      $out[] =	"onclick='document.form.submode.value=\"add\";document.form.submit();'>";
      $out[] =	"</th>\n";
      $out[] =	"  </tr>\n";
      $out[] =		"</table>\n";
      $out[] =		"<p>";
      $out[] =		"<input type='button' value='Apply Changes' onclick='document.form.submode.value=\"update\";document.form.submit();'>\n";
      $out[] =		"<input type='reset' value='Forget Changes'>\n";
      $out[] =		"<p><b>".(($stations)?
				  ((count($stations)==1)?(count($stations)." beacon"):(count($stations)." beacons")):
				  ("No beacons "));
      $out[] =	" in database</b></p>\n";

      $out[] =		implode($foot,"");
      print(implode($out,""));
    }
  break;


  // ************************************
  // * $mode = station_list             *
  // ************************************
  case "station_list":
    if (!isset($yyyymm)) {
      $now =	getdate();
      $yyyy =	$now['year'];
      $mm =	lead($now['mon']);
      $yyyymm =	$yyyy.$mm;
    }
    else {
      $mm =	substr($yyyymm,4,2);
      $yyyy =	substr($yyyymm,0,4);
    }

    $out[] =		implode($head,"");
    $out[] =		"<h1>Steve Ratzlaff's Pacific NDB Beacon Watch</h1>\n";
    $out[] =		"<p><small><b>[ ";
    $out[] =		"<a onmouseover='window.status=\"Click for instructions\";return true;' onmouseout='window.status=\"\";return true;' ";
    $out[] =		"href='javascript:alert(\"HELP\\n";
    $out[] =		"1) Click on left or right arrows by month name to change month;\\n";
    $out[] =		"2) Move mouse over readings and see more info in browser Status Bar;\\n";
    $out[] =		"3) Click location name to open a map;\\n";
    $out[] =		"4) Printing - for best results, alter page setup to \\\"Landscape\\\";\\n";
    $out[] =		"\\n\\\"NDB Beacon Watch\\\" software by Martin Francis\")'>Help</a> | ";
    $out[] =		"<a onmouseover='window.status=\"Click to see Key for reporting\";return true;' onmouseout='window.status=\"\";return true;' ";
    $out[] =		"href='javascript:alert(\"KEY\\n";
    $out[] =		"(In order of signal strength...)\\n";
    $out[] =		"F - Faint\\n";
    $out[] =		"P - Poor\\n";
    $out[] =		"W - Weak\\n";
    $out[] =		"M - Medium\\n";
    $out[] =		"G - Good\\n";
    $out[] =		"V - Very Good\")'>Key</a> | ";
    $out[] =		" <a href='javascript:region_map()' onmouseover='window.status=\"Click to see regional map of Pacific\"; return true;'onmouseout='window.status=\"\";return true;'>";
    $out[] =		"Beacon Map</a> ]";
    if ($admin) {
      $out[] =		"<br>[ <a href='./?mode=db_export'>Export Data</a> ";
      $out[] =		"| <a href='./?mode=station_edit_form' target='_blank'>Edit Beacons</a> ]";
    }
    $out[] =		"</b></small></p>\n";
    if ($temp = table_get_all(TYPE_STATION,"khz")) {
      $stations =	array();
      for ($i=0; $i<count($temp); $i++) {
        $stations[$temp[$i]['ID']] =		$temp[$i];
        $stations[$temp[$i]['ID']]['log'] =	array();

      }

      if ($log_entries = table_get_all(TYPE_LOG)) {
        $temp =	array();
        for ($i=0; $i<count($log_entries); $i++) {
          $log_yyyymmdd =	$log_entries[$i]['yyyymmdd'];
          $log_yyyymm =		substr($log_yyyymmdd,0,6);
          $log_dd =		substr($log_yyyymmdd,6,2);
          if ($log_yyyymm == $yyyymm) {
            $temp[$log_dd] = $log_dd;
            $stations[$log_entries[$i]['stationID']]['log'][$log_dd] = $log_entries[$i];
          }
        }
        $dates =	array();
        foreach ($temp as $ID) {
          $dates[] =	$ID;
        }
        sort($dates);
      }
      $out[] =		"<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n";
      $out[] =		"  <tr>\n";
      $out[] =		"    <th rowspan='2'>Beacon</th>\n";
      $out[] =		($admin ? "    <th rowspan='2' class='admin'>Log</th>\n" : "");
      $out[] =          "    <th colspan='".count($dates)."'".((count($dates))?(""):(" rowspan='2'"))." nowrap>";
      $out[] =          "<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_dec($yyyymm)."'>&lt;-</a>";
      $out[] =          " ".text_month($mm)." ".$yyyy." ";
      $out[] =          "<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_inc($yyyymm)."'>-&gt;</a>";
      $out[] =          "</th>\n";
      $out[] =		"    <th rowspan='2'>Location</th>\n";
      $out[] =		"    <th rowspan='2'>Deg</th>\n";
      $out[] =		"    <th rowspan='2'>Miles</th>\n";
      $out[] =		"  </tr>\n";

      $out[] =		"  <tr>\n";
      for ($i=0; $i<count($dates); $i++) {
        $out[] =	"    <th>".$dates[$i]."</th>\n";
      }
      $out[] =		"  </tr>\n";


      foreach ($stations as $ID=>$t) {
        $out[] =	"  <tr>\n";
        $out[] =	"    <th class='l' nowrap>".$stations[$ID]['khz']."-".$stations[$ID]['call']."</th>\n";
        if ($admin) {
          $out[] =	"<th class='admin'><small><a target='_blank' href='./?mode=log_edit_form&destinationID=$ID' title='Edit Entries'>Edit</a> | ";
          $out[] =	"<a target='_blank' href='./?mode=log_add_form&destinationID=$ID' title='Add Entry'>Add</a></small></th>";
        }
        if (count($dates)) {
          for ($i=0; $i<count($dates); $i++) {
            $rx =		((isset($stations[$ID]['log'][$dates[$i]]) && $stations[$ID]['log'][$dates[$i]]['rx'])?
				 ($stations[$ID]['log'][$dates[$i]]['rx']):(0));
            if ($rx) {
              $tmp =     $stations[$ID]['call']."-".$stations[$ID]['khz']." on "
			.$stations[$ID]['log'][$dates[$i]]['yyyymmdd']
			.(($stations[$ID]['log'][$dates[$i]]['hhmm'])?("@".$stations[$ID]['log'][$dates[$i]]['hhmm']."UTC"):(""))."   ";

              switch($stations[$ID]['log'][$dates[$i]]['rx']) {
                case "F": $tmp.= "Faint"; break;
                case "P": $tmp.= "Poor"; break;
                case "W": $tmp.= "Weak"; break;
                case "M": $tmp.= "Medium"; break;
                case "G": $tmp.= "Good"; break;
                case "V": $tmp.= "Very Good"; break;
              }
              $tmp.=	"   ".str_replace("'","`",$stations[$ID]['log'][$dates[$i]]['notes']);



              $out[] =	"    <td class='".$rx."' title='$tmp'>";
              $out[] =	"<a class='info' href='javascript:void null' onmouseover='window.status=\"$tmp\";return true;' ";
              $out[] =	"onmouseout= \"window.status='';return true;\"";
              $out[] =	">$rx</a></td>\n";
            }
            else {
              $out[] =	"    <td class='n'>&nbsp;</td>";
            }
          }
        }
        else {
          $out[] =	"    <td class='n'>&nbsp;</td>";
        }

        $out[] =	"    <th class='l' nowrap><a class = 'nav' href='javascript:map(".$stations[$ID]['lat'].",".$stations[$ID]['lon'].")'";
        $out[] =	" onmouseover='window.status=\"Show map for this location\"; return true;' onmouseout='window.status=\"\";return true;'>";
        $out[] =	$stations[$ID]['qth'].(($stations[$ID]['sta']!='')?(", ".$stations[$ID]['sta']):("")).(($stations[$ID]['cnt']!='')?(", ".$stations[$ID]['cnt']):(""))."</a></th>\n";

        $out[] =	"    <th class='l' nowrap><script type='text/javascript'>var rab = get_range_bearing($config_qth_lat,$config_qth_lon,".$stations[$ID]['lat'].",".$stations[$ID]['lon'].",'mi'); document.write(rab[0])</script></th>";
        $out[] =	"    <th class='l' nowrap><script type='text/javascript'>document.write(rab[1])</script></th>";

        $out[] =	"  </tr>\n";
      }
      $out[] =		"  <tr>\n";
      $out[] =		"    <th rowspan='2'>Beacon</th>\n";
      $out[] =		($admin ? "    <th rowspan='2' class='admin'>Log</th>\n" : "");
      $out[] =          "    <th colspan='".count($dates)."'".((count($dates))?(""):(" rowspan='2'"))." nowrap>";
      $out[] =          "<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_dec($yyyymm)."'>&lt;-</a>";
      $out[] =          " ".text_month($mm)." ".$yyyy." ";
      $out[] =          "<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_inc($yyyymm)."'>-&gt;</a>";
      $out[] =          "</th>\n";
      $out[] =		"    <th rowspan='2'>Location</th>\n";
      $out[] =		"    <th rowspan='2'>Deg</th>\n";
      $out[] =		"    <th rowspan='2'>Miles</th>\n";
      $out[] =		"  </tr>\n";
      $out[] =	"</table>\n";
    }
    else {
      $out[] =	"No stations have been entered";
    }
    $out[] =		implode($foot,"");
    print(implode($out,""));
  break;
}
?>
