<?php

function sys_info()
{
    ob_start();
    phpinfo();
    $tmp = ob_get_contents();
    ob_end_clean();
    $out = preg_split("/<body>|<\/body>/i", $tmp);
    $phpinfo = $out[1];

    $changelog = explode("\n", `git log master --pretty=format:"%ad %s" --date=short`);
    foreach($changelog as &$entry) {
        $bits =     explode(' ', $entry);
        $date =     array_shift($bits);
        $version =  trim(array_shift($bits), ':');
        $details =  implode(' ', $bits);
        $entry =    $date.'  '.pad($version, 7).' '.$details;
    }
    $changelog = implode("\n", $changelog);

    return
         "<div id='phpinfo'>\n"
        ."<table border=\"0\" cellpadding=\"3\">\n"
        ."<tr class='h'><td colspan='2'><h1 class='p'>RNA / REU / RWW SYSTEM</h1></td></tr>\n"
        ."<tr><td class=\"e\">system</td><td class=\"v\">".system."</td></tr>\n"
        ."<tr><td class=\"e\">system_title</td><td class=\"v\">".system_title."</td></tr>\n"
        ."<tr><td class=\"e\">system_URL</td><td class=\"v\">".system_URL."</td></tr>\n"
        ."<tr><td class=\"e\">system_ID</td><td class=\"v\">".system_ID."</td></tr>\n"
        ."<tr><td class=\"e\">system_editor</td><td class=\"v\">".system.': '.system_editor."</td></tr>\n"
        ."<tr><td class=\"e\">system_date</td><td class=\"v\">".system_date."</td></tr>\n"
        ."<tr><td class=\"e\">system_version</td><td class=\"v\">".system_version."</td></tr>\n"
        ."<tr><td class=\"e\">system_revision</td><td class=\"v\">".system_revision."</td></tr>\n"
        ."<tr><td class=\"e\">Recent Changes</td><td class=\"v\"><pre>".$changelog."</pre></td></tr>\n"
        ."<tr><td class=\"e\">awardsAdminEmail</td><td class=\"v\">".awardsAdminEmail."</td></tr>\n"
        ."<tr><td class=\"e\">awardsAdminName</td><td class=\"v\">".awardsAdminName."</td></tr>\n"
        ."</table>\n"
        ."<br>\n"
        .$phpinfo
        ."</div>";
}


function log_update(
    $ID,
    $date,
    $daytime,
    $dx_km,
    $dx_miles,
    $format,
    $heard_in,
    $listenerID,
    $LSB,
    $LSB_approx,
    $sec,
    $time,
    $USB,
    $USB_approx
) {
  // Add listener name and log details for an existing non-specific log matching
  // the listener's heard_in location (original NDBRNA import)
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
        .($LSB != "" ?    "  `LSB` = 		\"$LSB\",\n" : "")
        .($LSB_approx ?    "  `LSB_approx` =	\"~\",\n" : "")
        .($sec != "" ?    "  `sec` =		\"$sec\",\n" : "")
        .($time != "" ?    "  `time` =		\"$time\",\n" : "")
        .($USB != "" ?    "  `USB` =		\"$USB\",\n" : "")
        .($USB_approx ?    "  `USB_approx` =	\"~\",\n" : "")
        ."  `listenerID` =	\"$listenerID\"\n"
        ."WHERE `ID` = \"$ID\"";

    if (!mysql_query($sql)) {
        print("<pre>$sql</pre>");
    }
}


function admin_polls()
{
    global $mode, $submode;
    global $HTTP_POST_FILES;

    $out = array();

    $out[] =    "<h2>Manage Polls</h2>";
    return implode($out, "");
}


function logon()
{
    global $server,$mode,$submode;
    global $user,$password;

    if (isAdmin()) {
        return
             "<h2>Logon</h2><p>You are now logged on as an Administrator and may perform administrative functions."
            ."<br><br>\nTo log off, select <b>Log Off</b> from the main menu.</p>";
    }
    return
         "<h2>Logon</h2><p>You must logon in order to perform administrative functions.</p>"
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
        ."</table><script type='text/javascript'>document.form.user.focus();</script>\n";
}


function log_upload()
{
    global $mode, $submode, $log_format, $log_entries, $log_dd, $log_mm, $log_yyyy, $listener_in, $listener_timezone;
    global $CS, $dx_lat, $dx_lon, $fmt, $sec, $ID, $KHZ, $LSB, $LSB_approx, $USB, $USB_approx, $YYYYMMDD, $hhmm;
    global $listenerID, $callsign, $gsq, $name, $notes, $password, $qth, $username;
    global $debug;
    $debug = 0;
    $out =
         "<form name='form' action='".system_URL."/".$mode."' method='POST'>"
        ."<input type='hidden' name='submode' value=''>";
    switch ($submode) {
        case "save_format":
            listener_update_format($listenerID, $log_format);
            $submode= '';
            break;
        case "submit_log":
            set_time_limit(600);    // Extend maximum execution time to 10 mins
            $log_first_for_listener =       0;    // First time listener has been named for this signal
            $log_first_for_state_or_itu =   0;    // First time signal has been logged specifically from this state
            $log_first_for_system =         0;    // First time signal has been logged at all
            $log_repeat_for_listener =      0;    // Listener has logged this signal before
            $log_exact_duplicate =          0;    // This logging has been submitted once already
            $signal_updates =               0;    // The signal record has been updated - this data is most recent
            $sql =
                 "SELECT\n"
                ."  `lat`,\n"
                ."  `lon`,\n"
                ."  `region`\n"
                ."FROM\n"
                ."  `listeners`\n"
                ."WHERE\n"
                ."  `ID` = ".$listenerID;
            $result =        mysql_query($sql);
            $row =        mysql_fetch_array($result, MYSQL_ASSOC);
            $region =        $row["region"];
            $qth_lat =    $row["lat"];
            $qth_lon =    $row["lon"];
            if ($debug) {
                $out.=
                     "1: Logged at least once from this state<br>"
                    ."2: No listener yet listed in this state<br>"
                    ."3: Listener listed, but this is not a duplicate logging so add a new one<br>"
                    ."4: signal never logged in this state<br>";
            }
            for ($i=0; $i<count($ID); $i++) {
                if ($debug) {
                    $out.=    "<li>ID=".$ID[$i]." ";
                }
                $update_signal =            false;
                $update_signal_heard_in =    true; //false;
                // ++++++++++++++++++++++++++++++++++++
                // + Get DX (if possible)             +
                // ++++++++++++++++++++++++++++++++++++
                $tmp =      get_signal_dx($ID[$i], $qth_lat, $qth_lon);
                $dx_miles = $tmp[0];
                $dx_km =    $tmp[1];
                $daytime = (
                    (
                        $hhmm[$i]+2400 >= ($listener_timezone*100) + 3400 &&
                        $hhmm[$i]+2400 < ($listener_timezone*100)+3800
                    ) ?
                        1
                    :
                        0
                );
                // ++++++++++++++++++++++++++++++++++++++++++++
                // + See if log is first for state or country +
                // ++++++++++++++++++++++++++++++++++++++++++++
                $sql =
                     "SELECT\n"
                    ."  `ID`,\n"
                    ."  `listenerID`\n"
                    ."FROM\n"
                    ."  `logs`\n"
                    ."WHERE\n"
                    ."  `signalID` = ".$ID[$i]." AND\n"
                    ."  `heard_in` = \"".$listener_in."\"";
                $result =    mysql_query($sql);
                if (mysql_num_rows($result)) {
                    // No, signal has been logged at least once from this state:
                    if ($debug) {
                        $out.=    "1 ";
                    }
                    $update_signal = true;
                    // Update signal record (IF this data is the most recent...)
                    $row =    mysql_fetch_array($result);
                    if ($row["listenerID"] == "") {
                        // First row doesn't list listener, so must be first time
                        // First time listener from this state named, so
                        if ($debug) {
                            $out.=    "2 ";
                        }
                        log_update(
                            $row["ID"],
                            $YYYYMMDD[$i],
                            $daytime,
                            $dx_km,
                            $dx_miles,
                            htmlentities($fmt[$i]),
                            $listener_in,
                            $listenerID,
                            $LSB[$i],
                            $LSB_approx[$i],
                            $sec[$i],
                            $hhmm[$i],
                            $USB[$i],
                            $USB_approx[$i]
                        );
                        if ($debug) {
                            $out.=    "<pre>$sql</pre>";
                        }
                        mysql_query($sql);
                        // Write in name for this listener
                        $update_signal =         true;
                         // Update signal record (IF this data is the most recent...)
                        $log_first_for_listener++;
                    } else {
                        // A listener from this state has been named before
                        // ++++++++++++++++++++++++++++++++++++
                        // + See if log is exact duplicate    +
                        // ++++++++++++++++++++++++++++++++++++
                        $sql =
                             "SELECT `ID` FROM `logs`\n"
                            ."WHERE\n"
                            .($hhmm[$i] ?    "  `time` = \"".$hhmm[$i]."\" AND\n" : "")
                            .        "  `signalID` = ".$ID[$i]." AND\n"
                            .        "  `date` = \"".$YYYYMMDD[$i]."\" AND\n"
                            .        "  `listenerID` = ".$listenerID;

                        if ($debug) {
                            $out.=    "<pre>$sql</pre>";
                        }
                        $result =    mysql_query($sql);

                        if (mysql_num_rows($result)) {
                            // Yes, it's a duplicate
                            $log_exact_duplicate++;
                        } else {
                            // No, not a duplicate
                            // +++++++++++++++++++++++++++++++++++++++
                            // + this is a new logging for old state +
                            // +++++++++++++++++++++++++++++++++++++++
                            if ($debug) {
                                $out.= "3 ";
                            }

                            $sql =
                                 "SELECT\n"
                                ."  COUNT(*) AS `log_repeat_for_listener`\n"
                                ."FROM\n"
                                ."  `logs`\n"
                                ."WHERE\n"
                                ."  `listenerID` = \"".$listenerID."\" AND\n"
                                ."  `signalID` = \"".$ID[$i]."\"";
                            $result =    mysql_query($sql);
                            $row =    mysql_fetch_array($result, MYSQL_ASSOC);
                            if ($row["log_repeat_for_listener"]) {
                                $log_repeat_for_listener++;
                            } else {
                                $log_first_for_listener++;
                            }
                            $data = array(
                                'signalID' =>   $ID[$i],
                                'date' =>       $YYYYMMDD[$i],
                                'daytime' =>    ($daytime ? 1 : 0),
                                'heard_in' =>   $listener_in,
                                'listenerID' => $listenerID,
                                'region' =>     $region
                            );
                            if ($dx_km) {
                                $data['dx_km'] =      $dx_km;
                                $data['dx_miles'] =   $dx_miles;
                            }
                            if (htmlentities($fmt[$i])) {
                                $data['format'] =     htmlentities($fmt[$i]);
                            }
                            if ($LSB[$i] !== "") {
                                $data['LSB'] =        $LSB[$i];
                            }
                            if ($LSB_approx[$i]) {
                                $data['LSB_approx'] = "~";
                            }
                            if ($USB[$i] !== "") {
                                $data['USB'] =        $USB[$i];
                            }
                            if ($USB_approx[$i]) {
                                $data['USB_approx'] = "~";
                            }
                            if ($sec[$i]) {
                                $data['sec'] =        $sec[$i];
                            }
                            if ($hhmm[$i]) {
                                $data['time'] =        $hhmm[$i];
                            }
                            $log = new Log;
                            $log->insert($data);
                            $update_signal = true;
                            // Update signal record (IF this data is the most recent...)
                        }
                    }
                } else {
                    // signal not logged from this state before (but could be 'everywhere')
                    if ($debug) {
                        $out.=    "4 ";
                    }
                    // +++++++++++++++++++++++++++++++++++++++
                    // + this is a new logging for new state +
                    // +++++++++++++++++++++++++++++++++++++++
                    $data = array(
                        'signalID' =>   $ID[$i],
                        'date' =>       $YYYYMMDD[$i],
                        'daytime' =>    ($daytime ? 1 : 0),
                        'heard_in' =>   $listener_in,
                        'listenerID' => $listenerID,
                        'region' =>     $region
                    );
                    if ($dx_km) {
                        $data['dx_km'] =      $dx_km;
                        $data['dx_miles'] =   $dx_miles;
                    }
                    if (htmlentities($fmt[$i])) {
                        $data['format'] =     htmlentities($fmt[$i]);
                    }
                    if ($LSB[$i] !== "") {
                        $data['LSB'] =        $LSB[$i];
                    }
                    if ($LSB_approx[$i]) {
                        $data['LSB_approx'] = "~";
                    }
                    if ($USB[$i] !== "") {
                        $data['USB'] =        $USB[$i];
                    }
                    if ($USB_approx[$i]) {
                        $data['USB_approx'] = "~";
                    }
                    if ($sec[$i]) {
                        $data['sec'] =        $sec[$i];
                    }
                    if ($hhmm[$i]) {
                        $data['time'] =        $hhmm[$i];
                    }
                    $log = new Log;
                    $log->insert($data);

                    $update_signal = true;
                    // Update signal record (IF this data is the most recent...)
                    $update_signal_heard_in =    true;
                    // Update signal heard in record
                    $log_first_for_state_or_itu++;
                    $log_first_for_listener++;
                }
                if ($debug) {
                    $out.=
                        "<li>update_signal = $update_signal, update_signal_heard_in = $update_signal_heard_in</li>\n";
                }




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
                    $sql =
                         "SELECT\n"
                        ."  *,\n"
                        ."  DATE_FORMAT(`last_heard`,'%Y%m%d') AS `f_last_heard`\n"
                        ."FROM\n"
                        ."  `signals`\n"
                        ."WHERE\n"
                        ."  `ID` = \"".$ID[$i]."\"";
                    $result =    mysql_query($sql);
                    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
                    $this_YYYY =    substr($YYYYMMDD[$i], 0, 4);
                    $this_MM =    substr($YYYYMMDD[$i], 4, 2);
                    $this_DD =    substr($YYYYMMDD[$i], 6, 2);
                    $last_heard_YYYY =    substr($row["f_last_heard"], 0, 4);
                    $last_heard_MM =    substr($row["f_last_heard"], 4, 2);
                    $last_heard_DD =    substr($row["f_last_heard"], 6, 2);

                    if ($debug) {
                        $out.=
                             "<br>This: $this_YYYY$this_MM$this_DD<br>"
                            ."Last: $last_heard_YYYY$last_heard_MM$last_heard_DD<br>";
                    }

                    if (
                        (int)($last_heard_YYYY."".$last_heard_MM."".$last_heard_DD) >=
                        (int)($this_YYYY."".$this_MM."".$this_DD)
                    ) {
                        $update_signal =        false;                        // So clear update flag
                    }
                }

                $sql =
                     "SELECT\n"
                    ."  COUNT(*) as `logs`\n"
                    ."FROM\n"
                    ."  `logs`\n"
                    ."WHERE\n"
                    ."  `signalID` = ".$ID[$i]." AND\n"
                    ."  `listenerID` != ''";
                $result =    mysql_query($sql);
                $row =        mysql_fetch_array($result, MYSQL_ASSOC);
                $logs =        $row["logs"];

                if ($update_signal) {
                    $signal_updates++;
                    $last_heard = $this_YYYY."-".$this_MM."-".$this_DD;
                    signal_update_full(
                        $ID[$i],
                        $LSB[$i],
                        $LSB_approx[$i],
                        $USB[$i],
                        $USB_approx[$i],
                        $sec[$i],
                        htmlentities($fmt[$i]),
                        $logs,
                        $last_heard,
                        $region
                    );
                } else {
                    $sql =
                         "UPDATE\n"
                        ."  `signals`\n"
                        ."SET\n"
                        ."  `logs` = $logs,\n"
                        ."  `heard_in_$region`=1\n"
                        ."WHERE\n"
                        ."  `ID` = ".$ID[$i];
                    mysql_query($sql);
                    if ($debug) {
                        $out.=    "<pre>$sql</pre>";
                    }
                }
            }
            update_listener_log_count($listenerID);
            break;
    }

    switch ($submode) {
        case "":
            $sql =        "SELECT * FROM `listeners` WHERE `ID` = '$listenerID'";
            $result =     mysql_query($sql);
            $row =        mysql_fetch_array($result, MYSQL_ASSOC);
            $log_format =    $row["log_format"];
            $out.=
                 "<h1>Add Log > Parse Data</h1><br>"
                ."<img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>"
                ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
                ."  <tr>\n"
                ."    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n"
                ."  </tr>\n"
                ."  <tr class='rownormal'>\n"
                ."    <th align='left'>Listener</th>"
                ."    <td colspan='3'>"
                ."<select name='listenerID' class='formfield' onchange='document.form.submit()'"
                ." style='font-family: monospace;'> "
                .get_listener_options_list("1", $listenerID, "Select Listener")
                ."</select>"
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n";
            break;

        case "parse_log":
            listener_update_format($listenerID, $log_format);
            $sql =    "SELECT * FROM `listeners` WHERE `ID` = \"".$listenerID."\"";
            $result =     mysql_query($sql);
            $row =    mysql_fetch_array($result, MYSQL_ASSOC);
            if ($row['SP']) {
                $listener_in = $row['SP'];
            } else {
                $listener_in = $row['ITU'];
            }
            $listener_timezone =    $row['timezone'];
            $out.=
                 "<h1>Add Log > Confirm Data</h1><br>"
                ."<img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>"
                ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
                ."  <tr>\n"
                ."    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n"
                ."  </tr>\n"
                ."  <tr class='rownormal'>\n"
                ."    <th align='left'>Listener</th>\n"
                ."    <td colspan='3'>\n"
                ."    <input type='hidden' name='listener_in' value='$listener_in'>\n"
                ."    <input type='hidden' name='listenerID' value='$listenerID'>"
                ."    <input type='hidden' name='listener_timezone' value='$listener_timezone'>"
                .$row["name"]
                .($row["callsign"] ? " <b>".$row["callsign"]."</b>" : "")." "
                .$row["QTH"].", "
                .($row["SP"] ? $row["SP"].", " : "")
                .$row["ITU"]
                .($row["notes"] ? " (".stripslashes($row["notes"]).")" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n";
            break;

        case "submit_log":
            $sql =    "SELECT * FROM `listeners` WHERE `ID` = \"".$listenerID."\"";
            $result = mysql_query($sql);
            $row =    mysql_fetch_array($result, MYSQL_ASSOC);
            $out.=
                 "<h1>Add Log > Results</h1><br>"
                ."<img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>"
                ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
                ."  <tr>\n"
                ."    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n"
                ."  </tr>\n"
                ."  <tr class='rownormal'>\n"
                ."    <th align='left'>Listener</th>"
                ."    <td colspan='3'><input type='hidden' name='listenerID' value='$listenerID'>"
                .$row["name"]
                .($row["callsign"] ? " <b>".$row["callsign"]."</b>" : "")." "
                .$row["QTH"].", "
                .($row["SP"] ? $row["SP"].", " : "")
                .$row["ITU"]
                .($row["notes"] ? " (".stripslashes($row["notes"]).")" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table><br><br>\n"
                ."<h1>Statistics for this update:</h1><br>\n"
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

    $param_array =    array();
    $start =    0;
    $len =    0;

    $log_format_parse =    $log_format." ";
    while (substr($log_format_parse, $start, 1)==" ") {
        $start++;
    }
    //  print "<pre>$log_format</pre>\n";
    $log_format_errors = "";
    while ($start<strlen($log_format_parse)) {
        $len =        strpos(substr($log_format_parse, $start), " ");
        $param_name =    substr($log_format_parse, $start, $len);
        if ($len) {
            while (substr($log_format_parse, $start+$len, 1)==" ") {
                $len++;
            }
            if ($param_name=="X" || !isset($param_array[$param_name])) {
                $param_array[$param_name] = array($start,$len+1);
            } else {
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
        $out.=
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
            ."Click <a href='javascript:history.back()'><b><u>here</u></b></a>"
            ." to check your log format and try again.</p>";
        return $out;
    }

    //   foreach($param_array as $key=>$value){ print("<li>$key = ".$value[0].", ".$value[1]."</li>\n"); }


    $log_shows_YYYY =    false;
    $log_shows_MM =    false;
    $log_shows_DD =    false;

    if (
        isset($param_array["DDMMYY"]) ||    isset($param_array["DD.MM.YY"]) ||
        isset($param_array["DDYYMM"]) ||    isset($param_array["DD.YY.MM"]) ||
        isset($param_array["MMDDYY"]) ||    isset($param_array["MM.DD.YY"]) ||
        isset($param_array["MMYYDD"]) ||    isset($param_array["MM.YY.DD"]) ||
        isset($param_array["YYDDMM"]) ||    isset($param_array["YY.DD.MM"]) ||
        isset($param_array["YYMMDD"]) ||    isset($param_array["YY.MM.DD"]) ||

        isset($param_array["DDMMMYY"]) ||   isset($param_array["DD.MMM.YY"]) ||
        isset($param_array["DDYYMMM"]) ||   isset($param_array["DD.YY.MMM"]) ||
        isset($param_array["MMMDDYY"]) ||   isset($param_array["MMM.DD.YY"]) ||
        isset($param_array["MMMYYDD"]) ||   isset($param_array["MMM.YY.DD"]) ||
        isset($param_array["YYDDMMM"]) ||   isset($param_array["YY.DD.MMM"]) ||
        isset($param_array["YYMMMDD"]) ||   isset($param_array["YY.MMM.DD"]) ||

        isset($param_array["DDMMYYYY"]) ||  isset($param_array["DD.MM.YYYY"]) ||
        isset($param_array["DDYYYYMM"]) ||  isset($param_array["DD.YYYY.MM"]) ||
        isset($param_array["MMDDYYYY"]) ||  isset($param_array["MM.DD.YYYY"]) ||
        isset($param_array["MMYYYYDD"]) ||  isset($param_array["MM.YYYY.DD"]) ||
        isset($param_array["YYYYDDMM"]) ||  isset($param_array["YYYY.DD.MM"]) ||
        isset($param_array["YYYYMMDD"]) ||  isset($param_array["YYYY.MM.DD"]) ||

        isset($param_array["DDMMMYYYY"]) || isset($param_array["DD.MMM.YYYY"]) ||
        isset($param_array["DDYYYYMMM"]) || isset($param_array["DD.YYYY.MMM"]) ||
        isset($param_array["MMMDDYYYY"]) || isset($param_array["MMM.DD.YYYY"]) ||
        isset($param_array["MMMYYYYDD"]) || isset($param_array["MMM.YYYY.DD"]) ||
        isset($param_array["YYYYDDMMM"]) || isset($param_array["YYYY.DD.MMM"]) ||
        isset($param_array["YYYYMMMDD"]) || isset($param_array["YYYY.MMM.DD"])
    ) {
        $log_shows_YYYY =    true;
        $log_shows_MM =    true;
        $log_shows_DD =    true;
    }
    if (
        isset($param_array["DM"]) ||        isset($param_array["D.M"]) ||
        isset($param_array["DDM"]) ||       isset($param_array["DD.M"]) ||
        isset($param_array["DMM"]) ||       isset($param_array["D.MM"]) ||
        isset($param_array["DDMM"]) ||      isset($param_array["DD.MM"]) ||
        isset($param_array["DMMM"]) ||      isset($param_array["D.MMM"]) ||
        isset($param_array["DDMMM"]) ||     isset($param_array["DD.MMM"]) ||

        isset($param_array["MD"]) ||        isset($param_array["M.D"]) ||
        isset($param_array["MDD"]) ||       isset($param_array["M.DD"]) ||
        isset($param_array["MMD"]) ||       isset($param_array["MM.D"]) ||
        isset($param_array["MMDD"]) ||      isset($param_array["MM.DD"]) ||
        isset($param_array["MMMD"]) ||      isset($param_array["MMM.D"]) ||
        isset($param_array["MMMDD"]) ||     isset($param_array["MMM.DD"])
    ) {
        $log_shows_MM =    true;
        $log_shows_DD =    true;
    }
    if (isset($param_array["MM"])) {
        $log_shows_MM =    true;
    }
    if (isset($param_array["M"])) {
        $log_shows_MM =    true;
    }
    if (isset($param_array["D"])) {
        $log_shows_DD =    true;
    }
    if (isset($param_array["DD"])) {
        $log_shows_DD =    true;
    }
    if (isset($param_array["YY"])) {
        $log_shows_YYYY =    true;
    }
    if (isset($param_array["YYYY"])) {
        $log_shows_YYYY =    true;
    }



    switch ($submode) {
        case "parse_log":
            if (!isset($param_array["ID"])) {
                $out.=
                     "<br><h1>Error</h1><p>Your log format must include the ID field.<br>"
                    ."Click <a href='javascript:history.back()'><b><u>here</u></b></a>"
                    ." to check your log format and try again.</p>";
            } else {
                $out.=
                     "<br><span class='p'><b>Parser Results</b><small> - see"
                    ." <a href='#next'><b>below</b></a> for suggested <b>Next Steps</b></small></span>\n"
                    ."<table border='0' cellpadding='2' cellspacing='1' bgcolor='#c0c0c0'>\n"
                    ."  <tr class='downloadTableHeadings_nosort'>\n"
                    ."    <th>KHz</th>\n"
                    ."    <th>ID</th>\n"
                    ."    <th>QTH</th>\n"
                    ."    <th>SP</th>\n"
                    ."    <th>ITU</th>\n"
                    ."    <th>GSQ</th>\n"
                    ."    <th>Heard In</th>\n"
                    ."    <th>YYYYMMDD</th>\n"
                    ."    <th>HHMM</th>\n"
                    ."    <th>LSB</th>\n"
                    ."    <th>USB</th>\n"
                    ."    <th>Sec</th>\n"
                    ."    <th>Format</th>\n"
                    ."    <th>New?</th>\n"
                    ."  </tr>\n";
                $lines =        explode("\r", " ".stripslashes($log_entries));
                $unresolved_signals =    array();


                $total_loggings =    0;
                $date_fail =        false;
                if (isset($param_array["DM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DM\"][0])); return (\$Y.M_to_MM(substr(\$t,1,1)).D_to_DD(substr(\$t,0,1))); }" );
                } elseif (isset($param_array["D.M"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.M\"][0])); return (\$Y.M_to_MM(substr(\$t,2,1)).D_to_DD(substr(\$t,0,1))); }" );
                } elseif (isset($param_array["DDM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDM\"][0])); return (\$Y.M_to_MM(substr(\$t,2,1)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.M"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.M\"][0])); return (\$Y.M_to_MM(substr(\$t,3,1)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DMM\"][0])); return (\$Y.substr(\$t,1,2).D_to_DD(substr(\$t,0,1))); }" );
                } elseif (isset($param_array["D.MM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.MM\"][0])); return (\$Y.substr(\$t,2,2).D_to_DD(substr(\$t,0,1))); }" );
                } elseif (isset($param_array["DDMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMM\"][0])); return (\$Y.substr(\$t,2,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.MM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM\"][0])); return (\$Y.substr(\$t,3,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DMMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DMMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,1,3)).D_to_DD(substr(\$t,0,1))); }" );
                } elseif (isset($param_array["D.MMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.MMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,2,3)).D_to_DD(substr(\$t,0,1))); }" );
                } elseif (isset($param_array["DDMMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.MMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }" );
                }

                if (isset($param_array["MD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).D_to_DD(substr(\$t,1,1))); }" );
                } elseif (isset($param_array["M.D"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"M.D\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).D_to_DD(substr(\$t,2,1))); }" );
                } elseif (isset($param_array["MDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MDD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).substr(\$t,1,2)); }" );
                } elseif (isset($param_array["M.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"M.DD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).substr(\$t,2,2)); }" );
                } elseif (isset($param_array["MMD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMD\"][0])); return (\$Y.substr(\$t,0,2).D_to_DD(substr(\$t,2,1))); }" );
                } elseif (isset($param_array["MM.D"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.D\"][0])); return (\$Y.substr(\$t,0,2).D_to_DD(substr(\$t,3,1))); }" );
                } elseif (isset($param_array["MMDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDD\"][0])); return (\$Y.substr(\$t,0,2).substr(\$t,2,2)); }" );
                } elseif (isset($param_array["MM.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD\"][0])); return (\$Y.substr(\$t,0,2).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["MMMD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).D_to_DD(substr(\$t,3,1))); }" );
                } elseif (isset($param_array["MMM.D"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.D\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).D_to_DD(substr(\$t,4,1))); }" );
                } elseif (isset($param_array["MMMDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["MMM.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }" );
                } elseif (isset($param_array["DDMMYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMYY\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).substr(\$t,2,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.MM.YY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM.YY\"][0])); return (YY_to_YYYY(substr(\$t,6,2)).substr(\$t,3,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DDYYMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYMM\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).substr(\$t,4,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.YY.MM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YY.MM\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).substr(\$t,6,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["MMDDYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDDYY\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).substr(\$t,0,2).substr(\$t,2,2)); }" );
                } elseif (isset($param_array["MM.DD.YY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD.YY\"][0])); return (YY_to_YYYY(substr(\$t,6,2)).substr(\$t,0,2).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["MMYYDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMYYDD\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).substr(\$t,0,2).substr(\$t,4,2)); }" );
                } elseif (isset($param_array["MM.YY.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.YY.DD\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).substr(\$t,0,2).substr(\$t,6,2)); }" );
                } elseif (isset($param_array["YYDDMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYDDMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,4,2).substr(\$t,2,2)); }" );
                } elseif (isset($param_array["YY.DD.MM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.DD.MM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,6,2).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["YYMMDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYMMDD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,2,2).substr(\$t,4,2)); }" );
                } elseif (isset($param_array["YY.MM.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.MM.DD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,3,2).substr(\$t,6,2)); }" );
                } elseif (isset($param_array["DDMMMYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMMYY\"][0])); return (YY_to_YYYY(substr(\$t,5,2)).MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.MMM.YY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM.YY\"][0])); return (YY_to_YYYY(substr(\$t,7,2)).MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DDYYMMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYMMM\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).MMM_to_MM(substr(\$t,4,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.YY.MMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YY.MMM\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).MMM_to_MM(substr(\$t,6,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["MMMDDYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDDYY\"][0])); return (YY_to_YYYY(substr(\$t,5,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["MMM.DD.YY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD.YY\"][0])); return (YY_to_YYYY(substr(\$t,7,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }" );
                } elseif (isset($param_array["MMMYYDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMYYDD\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,5,2)); }" );
                } elseif (isset($param_array["MMM.YY.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.YY.DD\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,7,2)); }" );
                } elseif (isset($param_array["YYDDMMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYDDMMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,4,3)).substr(\$t,2,2)); }" );
                } elseif (isset($param_array["YY.DD.MMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.DD.MMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,6,3)).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["YYMMMDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYMMMDD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,2,3)).substr(\$t,5,2)); }" );
                } elseif (isset($param_array["YY.MMM.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.MMM.DD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,3,3)).substr(\$t,7,2)); }" );
                } elseif (isset($param_array["DDMMYYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMYYYY\"][0])); return (substr(\$t,4,4).substr(\$t,2,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.MM.YYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM.YYYY\"][0])); return (substr(\$t,6,4).substr(\$t,3,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DDYYYYMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYYYMM\"][0])); return (substr(\$t,2,4).substr(\$t,6,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.YYYY.MM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YYYY.MM\"][0])); return (substr(\$t,3,4).substr(\$t,8,2).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["MMDDYYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDDYYYY\"][0])); return (substr(\$t,4,4).substr(\$t,0,2).substr(\$t,2,2)); }" );
                } elseif (isset($param_array["MM.DD.YYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD.YYYY\"][0])); return (substr(\$t,6,4).substr(\$t,0,2).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["MMYYYYDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMYYYYDD\"][0])); return (substr(\$t,2,4).substr(\$t,0,2).substr(\$t,6,2)); }" );
                } elseif (isset($param_array["MM.YYYY.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.YYYY.DD\"][0])); return (substr(\$t,3,4).substr(\$t,0,2).substr(\$t,8,2)); }" );
                } elseif (isset($param_array["YYYYDDMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYDDMM\"][0])); return (substr(\$t,0,4).substr(\$t,6,2).substr(\$t,4,2)); }" );
                } elseif (isset($param_array["YYYY.DD.MM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.DD.MM\"][0])); return (substr(\$t,0,4).substr(\$t,8,2).substr(\$t,5,2)); }" );
                } elseif (isset($param_array["YYYYMMDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYMMDD\"][0])); return (substr(\$t,0,4).substr(\$t,4,2).substr(\$t,6,2)); }" );
                } elseif (isset($param_array["YYYY.MM.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.MM.DD\"][0])); return (substr(\$t,0,4).substr(\$t,5,2).substr(\$t,8,2)); }" );
                } elseif (isset($param_array["DDMMMYYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMMYYYY\"][0])); return (substr(\$t,5,4).MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.MMM.YYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM.YYYY\"][0])); return (substr(\$t,7,4).MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DDYYYYMMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYYYMMM\"][0])); return (substr(\$t,2,4).MMM_to_MM(substr(\$t,6,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["DD.YYYY.MMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YYYY.MMM\"][0])); return (substr(\$t,3,4).MMM_to_MM(substr(\$t,8,3)).substr(\$t,0,2)); }" );
                } elseif (isset($param_array["MMMDDYYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDDYYYY\"][0])); return (substr(\$t,5,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }" );
                } elseif (isset($param_array["MMM.DD.YYYY"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD.YYYY\"][0])); return (substr(\$t,7,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }" );
                } elseif (isset($param_array["MMMYYYYDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMYYYYDD\"][0])); return (substr(\$t,3,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,7,2)); }" );
                } elseif (isset($param_array["MMM.YYYY.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.YYYY.DD\"][0])); return (substr(\$t,4,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,9,2)); }" );
                } elseif (isset($param_array["YYYYDDMMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYDDMMM\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,6,3)).substr(\$t,4,2)); }" );
                } elseif (isset($param_array["YYYY.DD.MMM"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.DD.MMM\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,8,3)).substr(\$t,5,2)); }" );
                } elseif (isset($param_array["YYYYMMMDD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYMMMDD\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,4,3)).substr(\$t,7,2)); }" );
                } elseif (isset($param_array["YYYY.MMM.DD"])) {
                    eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.MMM.DD\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,5,3)).substr(\$t,9,2)); }" );
                }

                for ($i=0; $i<count($lines); $i++) {
                    //          print "<pre>".$lines[$i]."</pre>";
                    $YYYY =    YY_to_YYYY($log_yyyy);
                    $MM =        M_to_MM($log_mm);
                    $DD =        D_to_DD($log_dd);

                    if (function_exists("parse")) {
                        $YYYYMMDD = parse($param_array, $lines[$i], $YYYY, $MM, $DD);
                        $YYYY =     substr($YYYYMMDD, 0, 4);
                        $MM =       substr($YYYYMMDD, 4, 2);
                        $DD =       substr($YYYYMMDD, 6, 2);
                    } elseif (
                        isset($param_array["D"]) ||
                        isset($param_array["DD"]) ||
                        isset($param_array["M"]) ||
                        isset($param_array["MM"])
                    ) {
                        if (isset($param_array["D"])) {
                            $DD =
                                D_to_DD(trim(substr($lines[$i], $param_array["D"][0], 2)));
                        }
                        if (isset($param_array["DD"])) {
                            $DD =
                                trim(substr($lines[$i], $param_array["DD"][0], $param_array["DD"][1]));
                        }
                        if (isset($param_array["M"])) {
                            // DD shown in log
                            $MM =
                                M_to_MM(trim(substr($lines[$i], $param_array["M"][0], $param_array["M"][1])));
                        }
                        if (isset($param_array["MM"])) {
                            // DD shown in log
                            $MM =
                                trim(substr($lines[$i], $param_array["MM"][0], $param_array["MM"][1]));
                        }
                        if (isset($param_array["YY"])) {
                            // DD shown in log
                            $YYYY =
                                YY_to_YYYY(trim(substr($lines[$i], $param_array["YY"][0], $param_array["YY"][1])));
                        }
                        if (isset($param_array["YYYY"])) {
                            // DD shown in log
                            $YYYY =
                                trim(substr($lines[$i], $param_array["YYYY"][0], $param_array["YYYY"][1]));
                        }
                    }

                    $YYYYMMDD =    $YYYY.$MM.$DD;

                    // Parse Time: Options are hh:mm and hhmm
                    $hhmm =        "";
                    if (isset($param_array["hh:mm"])) {
                        // hh:mm shown in log
                        $hhmm_arr = explode(":", trim(substr($lines[$i], $param_array["hh:mm"][0], 6)));
                        if (isset($hhmm_arr[1])) {
                            $hhmm =
                                (strlen($hhmm_arr[0])==1 ? "0" : "").$hhmm_arr[0].$hhmm_arr[1];
                        }
                    }
                    if (isset($param_array["hhmm"])) {
                        // hhmm shown in log
                        $hhmm =
                            substr(trim(substr($lines[$i], $param_array["hhmm"][0], $param_array["hhmm"][1])), 0, 4);
                    }
                    if (!is_numeric($hhmm)) {
                        $hhmm =    "";
                    }
                    $KHZ =    (float)(isset($param_array["KHZ"]) ?
                        str_replace(",", ".", trim(substr($lines[$i], $param_array["KHZ"][0], $param_array["KHZ"][1])))
                     :
                        ""
                    );
                    $ID =     strtoUpper(trim(substr($lines[$i], $param_array["ID"][0], $param_array["ID"][1])));

                    $sec =    (isset($param_array["sec"]) ?
                        htmlentities(trim(substr($lines[$i], $param_array["sec"][0], $param_array["sec"][1])))
                     :
                        ""
                    );
                    $fmt =    (isset($param_array["fmt"]) ?
                        htmlentities(trim(substr($lines[$i], $param_array["fmt"][0], $param_array["fmt"][1])))
                     :
                        ""
                    );
                    $LSB =    "";
                    $USB =    "";
                    $LSB_approx =    "";
                    $USB_approx =    "";
                    if (isset($param_array["LSB"])) {
                        $LSB =        trim(substr($lines[$i], $param_array["LSB"][0], $param_array["LSB"][1]));
                        if (substr($LSB, 0, 1)=="~") {
                            $LSB =    substr($LSB, 1);
                            $LSB_approx =    "~";
                        }
                        if ($LSB=="---") {
                            // Andy Robins logs use --- as blank
                            $LSB = "";
                        }
                    }
                    if (isset($param_array["USB"])) {
                        $USB =        trim(substr($lines[$i], $param_array["USB"][0], $param_array["USB"][1]));
                        if (substr($USB, 0, 1)=="~") {
                            $USB =    substr($USB, 1);
                            $USB_approx =    "~";
                        }
                        if ($USB=="---") {
                            $USB = "";
                        }
                    }
                    if (isset($param_array["~LSB"])) {
                        $LSB =        trim(substr($lines[$i], $param_array["~LSB"][0], $param_array["~LSB"][1]));
                        $LSB_approx =    "~";
                    }
                    if (isset($param_array["~USB"])) {
                        $USB =        trim(substr($lines[$i], $param_array["~USB"][0], $param_array["~USB"][1]));
                        $USB_approx =    "~";
                    }

                    // The following parameters are only used for simplifying adding of new signals"
                    // if the input format happens to include them:
                    $GSQ =    (isset($param_array["GSQ"]) ?
                        trim(substr($lines[$i], $param_array["GSQ"][0], $param_array["GSQ"][1]))
                     :
                        ""
                    );
                    $QTH =    (isset($param_array["QTH"]) ?
                        trim(substr($lines[$i], $param_array["QTH"][0], $param_array["QTH"][1]))
                     :
                        ""
                    );
                    $ITU =    (isset($param_array["ITU"]) ?
                        trim(substr($lines[$i], $param_array["ITU"][0], $param_array["ITU"][1]))
                     :
                        ""
                    );
                    $SP =     (isset($param_array["SP"]) ?
                        trim(substr($lines[$i], $param_array["SP"][0], $param_array["SP"][1]))
                     :
                        ""
                    );
                    $PWR =    (isset($param_array["PWR"]) ?
                        trim(substr($lines[$i], $param_array["PWR"][0], $param_array["PWR"][1]))
                     :
                        ""
                    );


                    if (isset($param_array["+SB-"])) {
                        $sb =    str_replace(
                            "–",
                            "-",
                            trim(substr($lines[$i], $param_array["+SB-"][0], $param_array["+SB-"][1]))
                        );
                        // Convert hyphen symbol to - (For Steve R's Offsets)
                        $sb_arr =    explode(" ", $sb);
                        for ($j=0; $j<count($sb_arr); $j++) {
                            $sb =    trim($sb_arr[$j]);
                            if ($sb=="X" || $sb=="X-") {
                                // Format used by Jim Smith to indicate sb not present
                                $sb="";
                            }
                            if (
                                $sb=="DAID" ||
                                $sb=="DA2ID" ||
                                $sb=="DA3ID" ||
                                $sb=="DBID" ||
                                $sb=="DB2ID" ||
                                $sb=="DB3ID") {
                                $fmt = $sb;
                            }
                            if (
                                (substr($sb, 0, 1)=="+" && substr($sb, strlen($sb)-1, 1)=="-") ||
                                (substr($sb, 0, 1)=="-" && substr($sb, strlen($sb)-1, 1)=="+")
                            ) {
                                $USB = abs($sb);
                                $LSB = $USB;
                            } elseif (substr($sb, 0, 1)=="±") {
                                $USB = abs(substr($sb, 1));
                                $LSB = $USB;
                            } elseif (substr($sb, 0, 3)=="+/-" || substr($sb, 0, 3)=="-/+") {
                                $USB = abs(substr($sb, 3));
                                $LSB = $USB;
                            } elseif (substr($sb, 0, 2)=="+-" || substr($sb, 0, 2)=="-+") {
                                $USB = abs(substr($sb, 2));
                                $LSB = $USB;
                            } else {
                                $approx =    "";
                                if (substr($sb, 0, 1)=="~") {
                                    $approx = "~";
                                    $sb = substr($sb, 1);
                                }

                                if (substr($sb, 0, 1)=="+" || substr($sb, strlen($sb)-1, 1)=="+") {
                                    // + at start or end
                                    $USB = abs($sb);
                                    $USB_approx =    $approx;
                                } elseif (substr($sb, 0, 1)=="-" || substr($sb, strlen($sb)-1, 1)=="-") {
                                    // - at start or end
                                    $LSB = abs($sb);
                                    $LSB_approx =    $approx;
                                } elseif (substr($sb, 0, 1)=="±") {
                                    $USB = abs(substr($sb, 1));
                                    $LSB = $USB;
                                } elseif (is_numeric($sb)) {
                                    $USB = $sb;
                                    // neither + nor -, therefore USB
                                    $USB_approx =    $approx;
                                }
                            }
                        }
                    }

                    if (isset($param_array["+~SB-"])) {
                        $sb =    str_replace(
                            "–",
                            "-",
                            trim(substr($lines[$i], $param_array["+~SB-"][0], $param_array["+~SB-"][1]))
                        );
                        // Convert hyphen symbol to - (For Steve R's Offsets)
                        $sb =    str_replace("~", "", $sb); // Remove ~ symbol now we know it's approx
                        $sb_arr =    explode(" ", $sb);
                        for ($j=0; $j<count($sb_arr); $j++) {
                            $sb =    trim($sb_arr[$j]);
                            if (
                                $sb=="DAID" ||
                                $sb=="DA2ID" ||
                                $sb=="DA3ID" ||
                                $sb=="DBID" ||
                                $sb=="DB2ID" ||
                                $sb=="DB3ID"
                            ) {
                                $fmt = $sb;
                            } elseif (
                                (substr($sb, 0, 1)=="+" && substr($sb, strlen($sb)-1, 1)=="-") ||
                                (substr($sb, 0, 1)=="-" && substr($sb, strlen($sb)-1, 1)=="+")
                            ) {
                                $USB_approx =    "~";
                                $LSB_approx =    "~";
                                $USB = abs($sb);
                                $LSB = $USB;
                            } elseif (substr($sb, 0, 1)=="±") {
                                $USB_approx =    "~";
                                $LSB_approx =    "~";
                                $USB = abs(substr($sb, 1));
                                $LSB = $USB;
                            } elseif (substr($sb, 0, 3)=="+/-" || substr($sb, 0, 3)=="-/+") {
                                $USB_approx =    "~";
                                $LSB_approx =    "~";
                                $USB = abs(substr($sb, 3));
                                $LSB = $USB;
                            } elseif (substr($sb, 0, 2)=="+-" || substr($sb, 0, 2)=="-+") {
                                $USB_approx =    "~";
                                $LSB_approx =    "~";
                                $USB = abs(substr($sb, 2));
                                $LSB = $USB;
                            } else {
                                if (substr($sb, 0, 1)=="+" || substr($sb, strlen($sb)-1, 1)=="+") {
                                    // + at start or end
                                    $USB_approx =    "~";
                                    $USB = abs($sb);
                                } elseif (substr($sb, 0, 1)=="-" || substr($sb, strlen($sb)-1, 1)=="-") {
                                    // - at start or end
                                    $LSB_approx =    "~";
                                    $LSB =     abs($sb);
                                } else {
                                    if (is_numeric($sb)) {
                                        $USB_approx =    "~";
                                        $USB =         $sb;
                                        // neither + nor -, therefore USB
                                    }
                                }
                            }
                        }
                    }

                    // Cope with Brian Keyte's +0.4 1- offsets
                    if (isset($param_array["+K-"])) {
                        $sb = trim(
                            str_replace(
                                "–",
                                "-",
                                trim(substr($lines[$i], $param_array["+K-"][0], $param_array["+K-"][1]))
                            )
                        ); // Convert hyphen symbol to -
                        if ($sb ===    "0.4") {
                            $USB_approx =    "~";
                            $LSB_approx =    "~";
                            $USB = "400";
                            $LSB = "400";
                        } elseif ($sb ===    "+0.4") {
                            $USB_approx =    "~";
                            $USB = "400";
                        } elseif ($sb ===    "-0.4") {
                            $LSB_approx =    "~";
                            $LSB = "400";
                        } elseif ($sb ===    "1") {
                            $USB_approx =    "~";
                            $LSB_approx =    "~";
                            $USB = "1020";
                            $LSB = "1020";
                        } elseif ($sb ===    "+1") {
                            $USB_approx =    "~";
                            $USB = "1020";
                        } elseif ($sb ===    "-1") {
                            $LSB_approx =    "~";
                            $LSB = "1020";
                        }
                    }


                    if (isset($param_array["ABS"])) {
                        $ABS =    trim(substr($lines[$i], $param_array["ABS"][0], $param_array["ABS"][1]));
                        $ABS_arr =    explode(" ", $ABS);
                        for ($j=0; $j<count($ABS_arr); $j++) {
                            //              print "ABS=$ABS, KHZ=$KHZ";
                            $ABS = (double)trim($ABS_arr[$j]);
                            if ($ABS) {
                                if ($ABS>(float)$KHZ) {
                                    $USB = round((1000*($ABS-$KHZ)));
                                } else {
                                    $LSB = round((1000*($KHZ-$ABS)));
                                }
                            }
                        }
                    }

                    if (isset($param_array["~ABS"])) {
                        $ABS =    trim(substr($lines[$i], $param_array["~ABS"][0], $param_array["~ABS"][1]));
                        $ABS_arr =    explode(" ", $ABS);
                        for ($j=0; $j<count($ABS_arr); $j++) {
                            $ABS = (double)trim($ABS_arr[$j]);
                            if ($ABS) {
                                if ($ABS>(float)$KHZ) {
                                    $USB = round((1000*($ABS-$KHZ)));
                                    $USB_approx = "~";
                                } else {
                                    $LSB = round((1000*($KHZ-$ABS)));
                                    $LSB_approx = "~";
                                }
                            }
                        }
                    }

                    $YYYYMMDD =    abs($YYYYMMDD);


                    if ($ID && $YYYYMMDD) {
                        $sta_sel =    "";
                        if ($ID && $YYYYMMDD) {
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
                                $out.= "Problem looking up station - frequency was $KHZ";
                            }
                            if ($result && mysql_num_rows($result)) {
                                $total_loggings++;
                                if (mysql_num_rows($result) == 1) {
                                    $out.=    "<tr class='rownormal'>\n";
                                    $row =    mysql_fetch_array($result, MYSQL_ASSOC);
                                    $bgcolor =    "";
                                    if (!$row["active"]) {
                                        $bgcolor =
                                            " bgcolor='#d0d0d0' title='(Reportedly off air or decommissioned)'";
                                    } else {
                                        switch ($row["type"]) {
                                            case NDB:
                                                $bgcolor = "";
                                                break;
                                            case DGPS:
                                                $bgcolor = " bgcolor='#00D8ff' title='DGPS Station'";
                                                break;
                                            case TIME:
                                                $bgcolor = " bgcolor='#FFE0B0' title='Time Signal Station'";
                                                break;
                                            case NAVTEX:
                                                $bgcolor = " bgcolor='#FFB8D8' title='NAVTEX Station'";
                                                break;
                                            case HAMBCN:
                                                $bgcolor = " bgcolor='#D8FFE0' title='Amateur signal'";
                                                break;
                                            case OTHER:
                                                $bgcolor = " bgcolor='#B8F8FF' title='Other Utility Station'";
                                                break;
                                        }
                                    }
                                    $out.=
                                         "  <td>"
                                        ."<input type='hidden' name='ID[]' value='".$row["ID"]."'>"
                                        .(((float)$row['khz']<198 || (float)$row['khz'] > 530) ?
                                            "<font color='#FF8C00'><b>".(float)$row['khz']."</b></font>"
                                         :
                                            (float)$row['khz']
                                        )
                                        ."</td>\n"
                                        ."  <td$bgcolor>"
                                        ."<a href='javascript:signal_info(\"".$row["ID"]."\")'>$ID</a>"
                                        ."</td>\n"
                                        ."  <td".($row['QTH']?"":" bgcolor='#FFE7B9'"
                                        ." title='Please provide a value for QTH if you have one'").">"
                                        ."<font color='#606060'>".$row['QTH']."</font>"
                                        ."</td>\n"
                                        ."  <td>"
                                        .($row['SP'] ? "<font color='#606060'>".$row['SP']."</font>" : "&nbsp;")
                                        ."</td>\n"
                                        ."  <td><font color='#606060'>".$row['ITU']."</font></td>\n"
                                        ."  <td><font color='#606060'>"
                                        .($row["GSQ"] ?
                                             "<a href='javascript:popup_map("
                                            ."\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\""
                                            .")' title='Show map (accuracy limited to nearest Grid Square)'>"
                                            .$row["GSQ"]."</a>"
                                         :
                                            "&nbsp;"
                                         )
                                         ."</font></td>\n"
                                         ."  <td><font color='#606060'>"
                                         .(strpos($row['heard_in'], $listener_in)===false ?
                                            "<font color='#008000'><b>".$row['heard_in']."</b></font>"
                                          :
                                            highlight($row['heard_in'], $listener_in)
                                         )
                                         ."</font></td>\n";
                                } else {
                                    $out.=
                                         "<tr bgcolor='#ffe0a0'>\n"
                                        ."  <td colspan='7'>"
                                        ."<select name='ID[]' class='formfixed'>\n";
                                    $defaultChosen =    false;
                                    $selected =         false;
                                    for ($j=0; $j<mysql_num_rows($result); $j++) {
                                        $row =    mysql_fetch_array($result, MYSQL_ASSOC);
                                        if (!$defaultChosen && $row["active"]=="1") {
                                            $selected = true;
                                            $defaultChosen =  true;
                                        }
                                        $out.=
                                             "<option"
                                            .($row["active"]=="0" ? " style='background-color: #d0d0d0'" : "")
                                            ." value='".$row["ID"]."'"
                                            .($selected ? " selected='selected'" : "")
                                            .">"
                                            .(float)$row["khz"]." "
                                            .$ID." "
                                            .$row["QTH"]." "
                                            .pad_nbsp($row["SP"], 3)." "
                                            .$row["ITU"]." "
                                            .$row["GSQ"]." "
                                            .$row["heard_in"]
                                            ."</option>\n";
                                        $selected = false;
                                    }
                                    $out.=
                                        "</select></td>\n";
                                }
                                $out.=
                                    "  <td align='center'><input type='hidden' name='YYYYMMDD[]' value='$YYYYMMDD'>";

                                if (strlen($YYYYMMDD)!=8) {
                                    $date_fail = true;
                                    $out.=
                                        "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                } elseif ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                                    $date_fail = true;
                                    $out.=
                                        "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                } else {
                                    $out.=    ($YYYY<2005 ? "<font color='#FF8C00'><b>$YYYY</b></font>" : "$YYYY");
                                    if (!checkdate($MM, $DD, $YYYY)) {
                                        $date_fail = true;
                                        $out.=
                                            "<font color='red'><b><strike>$MM</strike></b></font>";
                                    } else {
                                        $out.=    $MM;
                                    }
                                    if (!checkdate($MM, $DD, $YYYY)) {
                                        $date_fail = true;
                                        $out.=
                                            "<font color='red'><b><strike>$DD</strike></b></font>";
                                    } else {
                                        $out.=    $DD;
                                    }
                                }
                                $out.=
                                     "</td>\n"
                                    ."  <td align='center'><input type='hidden' name='hhmm[]' value='$hhmm'>";
                                if (
                                    (strlen($hhmm)!=0 && strlen($hhmm)!=4) ||
                                    substr($hhmm, 0, 2)>23 || substr($hhmm, 2, 2)>59
                                ) {
                                    $date_fail = true;
                                    $out.=    "<font color='red'><b><strike>$hhmm</strike></b></font>";
                                } else {
                                    $out.=    $hhmm;
                                }
                                $out.=
                                     "</td>\n"
                                    ."  <td>"
                                    ."<input type='hidden' name='LSB_approx[]' value='$LSB_approx'>"
                                    ."<input type='hidden' name='LSB[]' value='$LSB'>"
                                    .((($LSB>0 && $LSB<350) || ($LSB>450 && $LSB<960) || ($LSB>1080)) ?
                                        "<font color='#FF8C00'><b>$LSB_approx$LSB</b></font>"
                                    :
                                        "$LSB_approx$LSB"
                                    )
                                    ."</td>\n"
                                    ."  <td>"
                                    ."<input type='hidden' name='USB_approx[]' value='$USB_approx'>"
                                    ."<input type='hidden' name='USB[]' value='$USB'>"
                                    .((($USB>0 && $USB<350) || ($USB>450 && $USB<960) || ($USB>1080)) ?
                                        "<font color='#FF8C00'><b>$USB_approx$USB</b></font>"
                                     :
                                        "$USB_approx$USB"
                                    )
                                    ."</td>\n"
                                    ."  <td><input type='hidden' name='sec[]' value=\"$sec\">$sec</td>\n"
                                    ."  <td><input type='hidden' name='fmt[]' value=\"$fmt\">$fmt</td>\n"
                                    ."  <td>&nbsp;</td>\n"
                                    ."</tr>\n";
                            } else {
                                $out.=
                                     "<tr bgcolor='#ffd0d0' title='signal not listed in database'>\n"
                                    ."  <td>"
                                    .(((float)$KHZ<198 || (float)$KHZ > 530) ?
                                        "<font color='#FF8C00'><b>".(float)$KHZ."</b></font>"
                                     :
                                        (float)$KHZ
                                    )
                                    ."</td>\n"
                                    ."  <td>$ID</td>\n"
                                    ."  <td>$QTH</td>\n"
                                    ."  <td>$SP</td>\n"
                                    ."  <td>$ITU</td>\n"
                                    ."  <td>$GSQ</td>\n"
                                    ."  <td>&nbsp;</td>\n"
                                    ."  <td align='center'>";
                                if (strlen($YYYYMMDD)!=8) {
                                    $date_fail = true;
                                    $out.=    "<font color='red'><b><strike>$YYYYMMDD</strike></i></b></font>";
                                } elseif ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                                    $date_fail = true;
                                    $out.=    "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                } else {
                                    $out.=    ($YYYY<2003 ? "<font color='#FF8C00'><b>$YYYY</b></font>" : "$YYYY");
                                    if (!checkdate($MM, $DD, $YYYY)) {
                                        $date_fail = true;
                                        $out.=    "<font color='red'><b><strike>$MM</strike></b></font>";
                                    } else {
                                        $out.=    $MM;
                                    }
                                    if (!checkdate($MM, $DD, $YYYY)) {
                                        $date_fail = true;
                                        $out.=    "<font color='red'><b><strike>$DD</strike></b></font>";
                                    } else {
                                        $out.=    $DD;
                                    }
                                }
                                $out.=
                                     "</td>\n"
                                    ."  <td align='center'>$hhmm</td>\n"
                                    ."  <td>$LSB_approx$LSB</td>\n"
                                    ."  <td>$USB_approx$USB</td>\n"
                                    ."  <td><input type='hidden' name='sec[]' value=\"$sec\">$sec</td>\n"
                                    ."  <td><input type='hidden' name='fmt[]' value=\"$fmt\">$fmt</td>\n"
                                    ."  <td><a href='javascript:signal_add("
                                    ."\"$ID\",\"$KHZ\",\"$GSQ\",\"$QTH\",\"$SP\",\"$ITU\",\"$PWR\""
                                    .")'><b>Add...</b></a></td>\n"
                                    ."</tr>";
                                $unresolved_signals[] =    $lines[$i];
                            }
                        }
                    }
                }
                if (!count($unresolved_signals) && !$date_fail) {
                    $out.=
                         "  <tr class='downloadTableHeadings_nosort'>\n"
                        ."    <th colspan='14'>"
                        ."<input type='button' value='Submit Log' class='formbutton' name='go'"
                        ." onclick='this.value=\"Please wait..\";this.disabled=true;submit_log()'>"
                        ."<script type='text/javascript'>document.form.go.focus()</script>\n"
                        ."</th>\n"
                        ."  </tr>\n";

                } else {
                    $out.=
                         "  <tr class='downloadTableHeadings_nosort'>\n"
                        ."    <th colspan='14'>"
                        ."<input type='button' value='Serious errors found - Go Back...' class='formbutton' name='go'"
                        ." onclick='history.back()'>"
                        ."<script type='text/javascript'>document.form.go.focus()</script>\n"
                        ."</th>\n"
                        ."  </tr>\n";
                }

                $out.=  "</table>\n";

                if (count($unresolved_signals)) {
                    $out.=
                         "<p><b>Issues:</b><br>\n"
                        ."<small>There "
                        .(count($unresolved_signals)!=1 ?
                            "are <b><font color='red'>".count($unresolved_signals)." unresolved signals</font></b>"
                         :
                            "<b><font color='red'>is one</font></b> unresolved signal"
                        )
                        ." contained in the log</b>.</small><br>"
                        ."<textarea rows='10' cols='90'>"
                        ."Unresolved records\n"
                        ."---------------------------------\n"
                        .implode("", $unresolved_signals)
                        ."</textarea>";
                } else {
                    $out.=
                        "<span class='p'><small><b>Total Loggings in this report: $total_loggings</b></small></span>";
                }

                $out.=
                     "<p><a name='next'></a><b>Next Steps...</b></p>\n"
                    ."<ul>\n"
                    ."<li>Please review the results shown above, especially warnings"
                    ." (<font color='#FF8C00'><b>orange</b></font>) and serious errors"
                    ." (<font color='red'><b>red</b></font>).</li>\n"
                    ."<li>Serious errors (invalid dates and unrecognised signals) prevent the"
                    ." <b>Submit Log</b> button from appearing.</li>"
                    ."<li>If LSB or USB appear to be too high or low, click on the link shown for the signal ID"
                    ." to see the offset history.</li>\n"
                    ."<li>If data seems to have been misread, click"
                    ." <a href='javascript:history.back()'><b><u>here</u></b></a>"
                    ." to check your formatting and try again.<br>\n"
                    ."(See the <b>Help</b> page on the main menu for details on acceptable formats).</li>\n"
                    ."<li>If a signal is not in the system, a link is provided to add the signal -"
                    ." check the details carefully before you add a new signal.</li>\n"
                    ."<li>If you have just added a signal for this log and you are asked by your browser whether"
                    ." you wish to 'Repost Data', say 'Yes'.</li>\n";
                if (!count($unresolved_signals) && !$date_fail) {
                    $out.=
                         "<li>If you are happy with the results shown above, press"
                        ." <b>Submit Log</b> to process the data.</li>\n";
                }
                $out.=    "</ul>\n";
            }
            break;

        case "submit_log":
            $out.=
                 "<br><input type='submit' value='&nbsp;Done&nbsp;' name='go'"
                ." onclick='window.close();' class='formbutton'>"
                ."<script type='text/javascript'>document.form.go.focus();</script>\n";
            break;
    }

    if ($listenerID!="" && $submode=="") {
        $out.=
             "&nbsp;"
            ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'>Log to Parse</th>\n"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td><input name='log_format' class='fixed_heading' size='105' value='$log_format'>"
            ."<input class='formbutton' name='save' type='button' value='Save' onclick='"
            ."this.disabled=true;document.form.go.disabled=true;document.form.conv.disabled=true;"
            ."document.form.submode.value=\"save_format\";document.form.submit()'>"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td><textarea rows='30' cols='110' class='fixed' name='log_entries'"
            ." onKeyUp='check_for_tabs(document.form);'"
            ." onchange='check_for_tabs(document.form);'>"
            .stripslashes($log_entries)
            ."</textarea>\n"
            ."  </tr>\n";
        if ((!$log_shows_YYYY || !$log_shows_MM || !$log_shows_DD)) {
            $out.=
                 "  <tr class='rownormal'>\n"
                ."    <td>The following details are also required: &nbsp; \n";
            if (!$log_shows_DD) {
                $out.=
                     "Day "
                    ."<input type='text' name='log_dd' size='2' maxlength='2' class='formfield' value='$log_dd'>\n";
            }
            if (!$log_shows_MM) {
                $out.=
                     "Month "
                    ."<input type='text' name='log_mm' size='2' maxlength='2' class='formfield' value='$log_mm'>\n";
            }
            if (!$log_shows_YYYY) {
                $out.=
                     "Year "
                    ."<input type='text' name='log_yyyy' size='4' maxlength='4' class='formfield' value='$log_yyyy'>\n";
            }
            $now =        mktime();
            $now_DD =        gmdate("d", $now);
            $now_MM =        gmdate("m", $now);
            $now_YYYY =    gmdate("Y", $now);

            $out.=
                 "<input type='button' value='&lt;-- Current' class='formButton' onclick=\""
                .(!$log_shows_DD ?
                    "if (document.form.log_dd.value=='')   { document.form.log_dd.value='$now_DD'; };"
                 :
                    ""
                )
                .(!$log_shows_MM ?
                    "if (document.form.log_mm.value=='')   { document.form.log_mm.value='$now_MM'; };"
                 :
                    ""
                )
                .(!$log_shows_YYYY ?
                    "if (document.form.log_yyyy.value=='') { document.form.log_yyyy.value='$now_YYYY'; };"
                 :
                    ""
                )
                ."\"></td>\n"
                ."  </tr>\n";
        }

        $out.=
             "  <tr class='rownormal'>\n"
            ."    <th>"
            ."<input type='button' value='Tabs > Spaces' class='formbutton' name='conv'"
            ." onclick='tabs_to_spaces(document.form)'"
            .(!preg_match("/	/", $log_entries) ? " disabled='disabled'" : "").">\n"
            ."<input type='button' value='Line Up' class='formbutton' name='lineup'"
            ." onclick='line_up(document.form)'>\n"
            ."<input type='button' name='go' value='Parse Log' class='formbutton'"
            ." onclick='if (parse_log(document.form)) { document.form.go.value=\"Please wait..\";"
            ."document.form.go.disabled=true;document.form.conv.disabled=true;"
            ."document.form.save.disabled=true;document.form.submode.value=\"parse_log\";"
            ."document.form.submit();}'> "
            ."<script type='text/javascript'>document.form.go.focus();</script>"
            ."</th>\n"
            ."  </tr>\n"
            ."</table>\n";
    }
    $out.=    "</form>";
    return $out;
}
