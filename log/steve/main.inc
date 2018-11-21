<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  main.inc                     *
// * Owner:     Martin Francis               *
// * Created:   2003-09-01 (MF)              *
// * Revised:   2018-11-21 (MF)              *
// *                                         *
// *******************************************

$head =
    "<html>\n"
    ."<head>\n"
    ."<style>\n"
    ."body { font-family: Verdana,Arial,Helvetica; }\n"
    ."td   { font-family: courier new, courier regular; width: 20px; text-align: center;}\n"
    ."th   { font-family: courier new, courier regular; background: rgb(255,255,230); }\n"
    ."th.l { text-align: left; }\n"
    ."th.new { background: rgb(255,200,200); }\n"
    ."th.admin { background: rgb(255,255,160); }\n"
    .".n   { text-align: center; background: rgb(250,250,250); }\n"
    .".f   { text-align: center; background: rgb(255,140,140); }\n"
    .".p   { text-align: center; background: rgb(255,200,200);  cursor: pointer;}\n"
    .".w   { text-align: center; background: rgb(255,220,200);  cursor: pointer;}\n"
    .".m   { text-align: center; background: rgb(220,255,220);  cursor: pointer;}\n"
    .".g   { text-align: center; background: rgb(100,255,100);  cursor: pointer;}\n"
    .".v   { text-align: center; background: rgb(0,255,0);  cursor: pointer;}\n"
    ."input.50 { width: 50px; }\n"
    ."input.80 { width: 80px; }\n"
    ."a.nav { text-decoration: none; font-family: Arial; }\n"
    ."a.info { text-decoration: none; color: rgb(0,0,0); }\n"
    ."</style>\n"
    ."<script type='text/javascript'>\n"
    ."function map(lat,lon) {\n"
    ."  var map_hd =\n"
    ."    window.open(\n"
    ."      \"https://www.google.com/maps/@\"+lat+\",\"+lon+\",11z\",\n"
    ."      \"map\",\n"
    ."      \"width=800,height=600,resizable=1,scrollbars=1,status=0,menubar=0,location=1,toolbar=0\"\n"
    ."    );\n"
    ."  map_hd.focus();\n"
    ."}\n"
    ."function region_map() {\n"
    ."  var map2_hd =\n"
    ."    window.open(\n"
    ."      \"/dx/ndb/assets/images/pacific_map.gif\",\n"
    ."      \"map2\",\n"
    ."      \"width=360,height=420,resizable=1,scrollbars=0,status=0,menubar=1,location=1,toolbar=0\"\n"
    ."    );\n"
    ."  map2_hd.focus();\n"
    ."}\n"
    ."function get_range_bearing(qth_lat,qth_lon,dx_lat,dx_lon,units) {\n"
    ."  if (qth_lat == dx_lat && qth_lon==dx_lon) {\n"
    ."    return new Array(0,0);\n"
    ."  }\n"
    ."  var dlon = (dx_lon - qth_lon)\n"
    ."  if (Math.abs(dlon) > 180) {\n"
    ."    dlon = (360 - Math.abs(dlon))*(0-(dlon/Math.abs(dlon)));\n"
    ."  }\n"
    ."  var rinlat =		qth_lat*0.01745;	// convert to radians\n"
    ."  var rinlon =		qth_lon*0.01745;\n"
    ."  var rfnlat =		dx_lat*0.01745;\n"
    ."  var rdlon =		dlon*0.01745;\n"
    ."  var rgcdist =	Math.acos(Math.sin(rinlat)*Math.sin(rfnlat)+Math.cos(rinlat)*Math.cos(rfnlat)*Math.cos(rdlon));\n"
    ."  var rincourse =	(Math.sin(rfnlat)-Math.cos(rgcdist)*Math.sin(rinlat))/(Math.sin(rgcdist)*Math.cos(rinlat));\n"
    ."  rincourse =		Math.acos(rincourse);\n"
    ."  incourse =		rincourse*57.3;\n"
    ."  if (dlon < 0) {\n"
    ."    incourse =		360 - incourse;\n"
    ."  }\n"
    ."  switch (units) {\n"
    ."    case \"mi\": var dx = Math.round(Math.abs(rgcdist)*3958.284); break;\n"
    ."    case \"nm\": var dx = Math.round(Math.abs(rgcdist)*3439.719); break;\n"
    ."    default:   var dx = Math.round(Math.abs(rgcdist)*6370.614); break;\n"
    ."  }\n"
    ."  return new Array(Math.round(incourse),dx);\n"
    ."}\n"
    ."</script>\n"
    ."</head>\n"
    ."<body bgcolor='#ffffff'>\n"
    ."<form name='form' action='./' method='POST'>\n";


$foot =
    "</form>\n"
    ."</body>\n"
    ."</html>\n";

extract($_REQUEST);       // Extracts all request variables (GET and POST) into global scope.

if (!isset($mode)) {
    $mode = "station_list";
}

if (!isset($submode)) {
    $submode = "";
}


// ************************************
// * mode selection switching:        *
// ************************************

switch ($mode) {
    case "log_edit_form":
        if ($admin) {
            if ($submode == "delete" and $logID != "") {
                table_delete_record($logID, TYPE_LOG);
            }
            if ($submode == "add") {
                log_add($destinationID, $new_yyyymmdd, $new_hhmm, $new_rx, $new_notes);
                $submode = "update";
            }

            if ($submode == "update") {
                if (isset($ID)) {
                    for ($i=0; $i<count($ID); $i++) {
                        log_update($ID[$i], $destinationID, $yyyymmdd[$i], $hhmm[$i], $rx[$i], $notes[$i]);
                    }
                }
            }


            $station =        table_get($destinationID, TYPE_STATION);
            $log_entries =    $log_entries =  log_for_station($destinationID);
            $out =
                $head
                ."<title>Edit Log: ".$station['khz']."-".$station['call']."</title>\n"
                ."<h1>Edit Log: ".$station['khz']."-".$station['call']."</h1>\n"
                ."<input type='hidden' name='mode' value='log_edit_form'>\n"
                ."<input type='hidden' name='submode' value='add'>\n"
                ."<input type='hidden' name='destinationID' value='$destinationID'>\n"
                ."<input type='hidden' name='logID' value=''>\n"
                ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n"
                ."  <tr>\n"
                ."    <th>yyyymmdd</th>\n"
                ."    <th>hhmm</th>\n"
                ."    <th>RX</th>\n"
                ."    <th>Notes</th>\n"
                ."    <th>Action</th>\n"
                ."  </tr>\n";

            if ($log_entries) {
                for ($i=0; $i<count($log_entries); $i++) {
                    $log_entry =  $log_entries[$i];
                    $out.=
                        "  <tr>\n"
                        ."    <th><input type='hidden' name='ID[]' value='".$log_entry['ID']."'>"
                        ."<input name='yyyymmdd[]' class='80' value='".$log_entry['yyyymmdd']."' maxlength='8' size='8'></th>\n"
                        ."    <th><input name='hhmm[]' class='50' value='".$log_entry['hhmm']."' maxlength='4' size='4'></th>\n"
                        ."    <th><select name='rx[]'>\n"
                        ."<option value='F' class='F'".(($log_entry['rx']=='F')?(" selected"):("")).">Faint</option>\n"
                        ."<option value='P' class='P'".(($log_entry['rx']=='P')?(" selected"):("")).">Poor</option>\n"
                        ."<option value='W' class='W'".(($log_entry['rx']=='W')?(" selected"):("")).">Weak</option>\n"
                        ."<option value='M' class='M'".(($log_entry['rx']=='M')?(" selected"):("")).">Medium</option>\n"
                        ."<option value='G' class='G'".(($log_entry['rx']=='G')?(" selected"):("")).">Good</option>\n"
                        ."<option value='V' class='V'".(($log_entry['rx']=='V')?(" selected"):("")).">V.Good</option>\n"
                        ."</select></th>"
                        ."    <th><input name='notes[]' value=\"".$log_entry['notes']."\" maxlength='255' size='30'></th>\n"
                        ."    <th class='l'>"
                        ."<input type='button' value='Delete' "
                        ."onclick='if (confirm(\"DELETE\\n\\nConfirm deletion of ".$station['call']."-".$station['khz']." entry\\nfor ".$log_entry['yyyymmdd']."@".$log_entry['hhmm']."\")) { document.form.submode.value=\"delete\";document.form.logID.value=\"".$log_entry['ID']."\";document.form.submit();}'>"
                        ."</th>\n"
                        ."  </tr>\n";
                }
            }
            $out.=
                "  <tr>\n"
                ."    <th class='new'>"
                ."<input name='new_yyyymmdd' class='80' value='' maxlength='8' size='8'></th>\n"
                ."    <th class='new'><input name='new_hhmm' class='50' value='' maxlength='4' size='4'></th>\n"
                ."    <th class='new'>\n"
                ."<select name='new_rx'>\n"
                ."    <option value='F' class='F'>Faint</option>\n"
                ."    <option value='P' class='P'>Poor</option>\n"
                ."    <option value='W' class='W'>Weak</option>\n"
                ."    <option value='M' class='M'>Medium</option>\n"
                ."    <option value='G' class='G'>Good</option>\n"
                ."    <option value='V' class='V'>V.Good</option>\n"
                ."</select></th>"
                ."    <th class='new'><input name='new_notes' value='' maxlength='255' size='30'></th>\n"
                ."    <th class='new'>"
                ."<input type='button' value='Add' "
                ."onclick='document.form.submode.value=\"add\";document.form.submit();'>"
                ."</th>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<input type='button' value='Apply Changes' onclick='document.form.submode.value=\"update\";document.form.submit();'>\n"
                ."<input type='reset' value='Forget Changes'>\n";

            if ($log_entries) {
                $out.=        count($log_entries)." log entr".((count($log_entries)==1)?("y"):("ies"))." for this beacon";
            }

            $out.= $foot;
            print $out;
        }
        break;


    case "log_add_form":
        if ($admin) {
            if ($submode == "add") {
                log_add($destinationID, $new_yyyymmdd, $new_hhmm, $new_rx, $new_notes);
            }

            $station =        table_get($destinationID, TYPE_STATION);
            $out =
                 "<title>Add Log Entry for ".$station['khz']."-".$station['call']."</title>\n"
                ."<h1>Add ".($submode=='add' ? "another ": "")."Log Entry for ".$station['khz']."-".$station['call']."</h1>\n"
                ."<input type='hidden' name='mode' value='log_add_form'>\n"
                ."<input type='hidden' name='submode' value='add'>\n"
                ."<input type='hidden' name='destinationID' value='$destinationID'>\n"
                ."<input type='hidden' name='logID' value=''>\n"
                ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n"
                ."  <tr>\n"
                ."    <th>yyyymmdd</th>\n"
                ."    <th>hhmm</th>\n"
                ."    <th>RX</th>\n"
                ."    <th>Notes</th>\n"
                ."    <th>Action</th>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                . "    <th class='new'>"
                ."<input name='new_yyyymmdd' class='80' value='' maxlength='8' size='8'></th>\n"
                ."    <th class='new'><input name='new_hhmm' class='50' value='' maxlength='4' size='4'></th>\n"
                ."    <th class='new'><select name='new_rx'>\n"
                ."<option value='F' class='F'>Faint</option>\n"
                ."<option value='P' class='P'>Poor</option>\n"
                ."<option value='W' class='W'>Weak</option>\n"
                ."<option value='M' class='M'>Medium</option>\n"
                ."<option value='G' class='G'>Good</option>\n"
                ."<option value='V' class='V'>V.Good</option>\n"
                ."</select></th>"
                ."    <th class='new'><input name='new_notes' value='' maxlength='255' size='30'></th>\n"
                ."    <th class='new'>"
                ."<input type='button' value='Add' "
                ."onclick='document.form.submode.value=\"add\";document.form.submit();'>"
                ."</th>\n"
                ."  </tr>\n"
                ."</table>\n"
                .($submode=='add' ? "(previous record was added)": "");
            print $head.$out.$foot;
        }
        break;


    case "station_edit_form":
        if ($admin) {
            if ($submode == "delete" and $destinationID != "") {
                table_delete_record($destinationID, TYPE_STATION);
            }

            if ($submode == "add") {
                station_add($new_khz, $new_call, $new_qth, $new_sta, $new_cnt, $new_lat, $new_lon);
                $submode = "update";
            }

            if ($submode == "update" && isset($ID)) {
                for ($i=0; $i<count($ID); $i++) {
                    station_update($ID[$i], $khz[$i], $call[$i], $qth[$i], $sta[$i], $cnt[$i], $lat[$i], $lon[$i]);
                }
            }


            $out =
                 "<h1>Edit Beacons</h1>\n"
                ."<input type='hidden' name='destinationID' value=''>\n"
                ."<input type='hidden' name='mode' value='station_edit_form'>\n"
                ."<input type='hidden' name='submode' value=''>\n"
                ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n"
                ."  <tr>\n"
                ."    <th>KHz</th>\n"
                ."    <th>Call</th>\n"
                ."    <th>QTH</th>\n"
                ."    <th>State</th>\n"
                ."    <th>ITU</th>\n"
                ."    <th>Latitude</th>\n"
                ."    <th>Longitude</th>\n"
                ."    <th>Delete</th>\n"
                ."  </tr>\n";
            if ($stations = table_get_all(TYPE_STATION, "khz")) {
                for ($i=0; $i<count($stations); $i++) {
                    $out .=
                        "  <tr>\n"
                        ."    <th class='l'><input type='hidden' name='ID[]' value='".$stations[$i]['ID']."'>"
                        ."<input name='khz[]'  class='50' maxlength='5' size='5' value='".$stations[$i]['khz']."'></th>\n"
                        ."    <th class='l'><input name='call[]' class='50' size='5' maxlength='5' value='".$stations[$i]['call']."'></th>\n"
                        ."    <th class='l'><input name='qth[]'  maxlength='20' value='".$stations[$i]['qth']."'></th>\n"
                        ."    <th><input name='sta[]'  class='50' size='2' maxlength='2' value='".$stations[$i]['sta']."'></th>\n"
                        ."    <th class='l'><input name='cnt[]'  class='50' size='3' maxlength='3' value='".$stations[$i]['cnt']."'></th>\n"
                        ."    <th class='l'><input name='lat[]'  class='80' size='10' value='".$stations[$i]['lat']."'></th>\n"
                        ."    <th class='l'><input name='lon[]'  class='80' size='10' value='".$stations[$i]['lon']."'></th>\n"
                        ."    <th>"
                        ."<input type='button' value='Delete' "
                        ."onclick='if (confirm(\"DELETE\\n".$stations[$i]['call']."-".$stations[$i]['khz']."\\n\\nAre you sure?\")) { document.form.submode.value=\"delete\";document.form.destinationID.value=\"".$stations[$i]['ID']."\";document.form.submit();}'>"
                        ."</th>\n"
                        ."  </tr>\n";
                }
            }
            $out .=
                "  <tr>\n"
                ."    <th class='new'>"
                ."<input name='new_khz'  class='50' size='5' maxlength='5' value=''></th>\n"
                ."    <th class='new'><input name='new_call' class='50' size='5' maxlength='5' value=''></th>\n"
                ."    <th class='new'><input name='new_qth'  maxlength='20' value=''></th>\n"
                ."    <th class='new'><input name='new_sta'  class='50' size='2' maxlength='2' value=''></th>\n"
                ."    <th class='new'><input name='new_cnt'  class='50' size='3' maxlength='3' value=''></th>\n"
                ."    <th class='new'><input name='new_lat'  class='80' size='10' value=''></th>\n"
                ."    <th class='new'><input name='new_lon'  class='80' size='10' value=''></th>\n"
                ."    <th class='new'>"
                ."<input type='button' value='Add' "
                ."onclick='document.form.submode.value=\"add\";document.form.submit();'>"
                ."</th>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<p>"
                ."<input type='button' value='Apply Changes' onclick='document.form.submode.value=\"update\";document.form.submit();'>\n"
                ."<input type='reset' value='Forget Changes'>\n"
                ."<p><b>"
                .($stations ?
                  (count($stations)==1 ? count($stations)." beacon" : count($stations)." beacons" ):
                  ("No beacons ")
                )
                ." in database</b></p>\n";
            print $head.$out.$foot;
        }
        break;


    case "station_list":
        if (!isset($yyyymm)) {
            $now =    getdate();
            $yyyy =   $now['year'];
            $mm = lead($now['mon']);
            $yyyymm = $yyyy.$mm;
        } else {
            $mm = substr($yyyymm, 4, 2);
            $yyyy =   substr($yyyymm, 0, 4);
        }

        $out =
            "<h1>Steve Ratzlaff's Pacific NDB Beacon Watch</h1>\n"
            ."<p><small><b>[ "
            ."<a href='#' onclick='alert(\"HELP\\n"
            ."1) Click on left or right arrows by month name to change month;\\n"
            ."2) Move mouse over readings and see more info in browser Status Bar;\\n"
            ."3) Click location name to open a map;\\n"
            ."4) Printing - for best results, alter page setup to \\\"Landscape\\\";\\n"
            ."\\n\\\"NDB Beacon Watch\\\" software by Martin Francis\");return false;'>Help</a> | "
            ."<a href='#' onclick='alert(\"KEY\\n"
            ."(In order of signal strength...)\\n"
            ."F - Faint\\n"
            ."P - Poor\\n"
            ."W - Weak\\n"
            ."M - Medium\\n"
            ."G - Good\\n"
            ."V - Very Good\");return false;'>Key</a> | "
            ." <a href='#' onclick='region_map();return false;'>Beacon Map</a> ]";
        if ($admin) {
            $out .=
                "<br>[ <a href='./?mode=station_edit_form' target='_blank'>Edit Beacons</a> ]";
        }
        $out .=
            "</b></small></p>\n";

        if ($temp = table_get_all(TYPE_STATION, "khz")) {
            $stations =   array();
            for ($i=0; $i<count($temp); $i++) {
                $stations[$temp[$i]['ID']] =        $temp[$i];
                $stations[$temp[$i]['ID']]['log'] = array();
            }
            if ($log_entries = table_get_all(TYPE_LOG)) {
                $temp = array();
                for ($i=0; $i<count($log_entries); $i++) {
                    $log_yyyymmdd =   $log_entries[$i]['yyyymmdd'];
                    $log_yyyymm =     substr($log_yyyymmdd, 0, 6);
                    $log_dd =     substr($log_yyyymmdd, 6, 2);
                    if ($log_yyyymm == $yyyymm) {
                        $temp[$log_dd] = $log_dd;
                        $stations[$log_entries[$i]['stationID']]['log'][$log_dd] = $log_entries[$i];
                    }
                }
                $dates =    array();
                foreach ($temp as $ID) {
                    $dates[] =    $ID;
                }
                sort($dates);
            }
            $out .=
                "<table cellpadding='1' cellspacing='0' border='1' bordercolor='#000000'>\n"
                ."  <tr>\n"
                ."    <th rowspan='2'>Beacon</th>\n"
                .($admin ? "    <th rowspan='2' class='admin'>Log</th>\n" : "")
                ."    <th colspan='".count($dates)."'".((count($dates))?(""):(" rowspan='2'"))." nowrap>"
                ."<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_dec($yyyymm)."'>&lt;-</a> "
                .text_month($mm)." ".$yyyy." "
                ."<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_inc($yyyymm)."'>-&gt;</a>"
                ."</th>\n"
                ."    <th rowspan='2'>Location</th>\n"
                ."    <th rowspan='2'>Deg</th>\n"
                ."    <th rowspan='2'>Miles</th>\n"
                ."  </tr>\n"
                ."  <tr>\n";
            for ($i=0; $i<count($dates); $i++) {
                $out .=
                    "    <th>".$dates[$i]."</th>\n";
            }
            $out .=
                "  </tr>\n";


            foreach ($stations as $ID => $t) {
                if (!isset($t['khz'])) {
                    print "<pre>$ID ".print_r($t, true)."</pre>";
                }
                $out .=
                    "  <tr>\n"
                    ."    <th class='l' nowrap>".$t['khz']."-".$t['call']."</th>\n"
                    .($admin ?
                        "<th class='admin'><small><a target='_blank' href='./?mode=log_edit_form&destinationID=$ID' title='Edit Entries'>Edit</a> | "
                        ."<a target='_blank' href='./?mode=log_add_form&destinationID=$ID' title='Add Entry'>Add</a></small></th>"
                    :
                        ""
                    );
                if (count($dates)) {
                    for ($i=0; $i<count($dates); $i++) {
                        $rx =       ((isset($stations[$ID]['log'][$dates[$i]]) && $stations[$ID]['log'][$dates[$i]]['rx'])?
                         ($stations[$ID]['log'][$dates[$i]]['rx']):(0));
                        if ($rx) {
                            $tmp =
                                $stations[$ID]['call']."-".$stations[$ID]['khz']." on "
                                .substr($stations[$ID]['log'][$dates[$i]]['yyyymmdd'],0,4).'-'
                                .substr($stations[$ID]['log'][$dates[$i]]['yyyymmdd'],4,2).'-'
                                .substr($stations[$ID]['log'][$dates[$i]]['yyyymmdd'],6,2)

                                .($stations[$ID]['log'][$dates[$i]]['hhmm'] ? "@".$stations[$ID]['log'][$dates[$i]]['hhmm']."UTC" : "")
                                ."   ";

                            switch ($stations[$ID]['log'][$dates[$i]]['rx']) {
                                case "F":
                                    $tmp.= "Faint";
                                    break;
                                case "P":
                                      $tmp.= "Poor";
                                    break;
                                case "W":
                                    $tmp.= "Weak";
                                    break;
                                case "M":
                                    $tmp.= "Medium";
                                    break;
                                case "G":
                                    $tmp.= "Good";
                                    break;
                                case "V":
                                    $tmp.= "Very Good";
                                    break;
                            }
                                $tmp.=    "   ".str_replace("'", "`", $stations[$ID]['log'][$dates[$i]]['notes']);

                                $out .=
                                    "    <td class='".$rx."' title='$tmp'>$rx</td>\n";
                        } else {
                            $out .=
                                "    <td class='n'>&nbsp;</td>";
                        }
                    }
                } else {
                    $out .=
                        "    <td class='n'>&nbsp;</td>";
                }

                $out .=
                    "    <th class='l' nowrap><a class = 'nav' href='javascript:map(".$stations[$ID]['lat'].",".$stations[$ID]['lon'].")'"
                    ." onmouseover='window.status=\"Show map for this location\"; return true;' onmouseout='window.status=\"\";return true;'>"
                    .$stations[$ID]['qth']
                    .(($stations[$ID]['sta']!='')?(", ".$stations[$ID]['sta']):(""))
                    .(($stations[$ID]['cnt']!='')?(", ".$stations[$ID]['cnt']):(""))
                    ."</a></th>\n"
                    ."    <th class='l' nowrap><script type='text/javascript'>var rab = get_range_bearing($config_qth_lat,$config_qth_lon,".$stations[$ID]['lat'].",".$stations[$ID]['lon'].",'mi'); document.write(rab[0])</script></th>"
                    ."    <th class='l' nowrap><script type='text/javascript'>document.write(rab[1])</script></th>"
                    ."  </tr>\n";
            }
            $out .=
                "  <tr>\n"
                ."    <th rowspan='2'>Beacon</th>\n"
                .($admin ? "    <th rowspan='2' class='admin'>Log</th>\n" : "")
                ."    <th colspan='".count($dates)."'".((count($dates))?(""):(" rowspan='2'"))." nowrap>"
                ."<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_dec($yyyymm)."'>&lt;-</a>"
                ." ".text_month($mm)." ".$yyyy." "
                ."<a class='nav' href='./?mode=station_list&yyyymm=".yyyymm_inc($yyyymm)."'>-&gt;</a>"
                ."</th>\n"
                ."    <th rowspan='2'>Location</th>\n"
                ."    <th rowspan='2'>Deg</th>\n"
                ."    <th rowspan='2'>Miles</th>\n"
                ."  </tr>\n"
                ."</table>\n";
        } else {
            $out .=  "No stations have been entered";
        }
        print $head.$out.$foot;
        break;
}
