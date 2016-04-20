<?php
namespace Rxx\Managers;

class LogUploader
{
    protected $debug = false;
    protected $html = '';
    protected $listener;
    protected $tokens = array();
    protected $stats =  array(
        'first_for_listener' =>     0,
        'first_for_state_or_itu' => 0,
        'repeat_for_listener' =>    0,
        'exact_duplicate' =>        0,
        'latest_for_signal' =>      0
    );

    protected $logHas = array(
        'YYYY' =>   false,
        'MM' =>     false,
        'DD' =>     false
    );

    public static $YYYYMMDDTokens = array(
        'DDMMYY',    'DD.MM.YY',    'DDYYMM',    'DD.YY.MM',
        'DDMMMYY',   'DD.MMM.YY',   'DDYYMMM',   'DD.YY.MMM',
        'DDMMYYYY',  'DD.MM.YYYY',  'DDYYYYMM',  'DD.YYYY.MM',
        'DDMMMYYYY', 'DD.MMM.YYYY', 'DDYYYYMMM', 'DD.YYYY.MMM',

        'MMDDYY',    'MM.DD.YY',    'MMYYDD',    'MM.YY.DD',
        'MMMDDYY',   'MMM.DD.YY',   'MMMYYDD',   'MMM.YY.DD',
        'MMDDYYYY',  'MM.DD.YYYY',  'MMYYYYDD',  'MM.YYYY.DD',
        'MMMDDYYYY', 'MMM.DD.YYYY', 'MMMYYYYDD', 'MMM.YYYY.DD',

        'YYDDMM',    'YY.DD.MM',    'YYMMDD',    'YY.MM.DD',
        'YYDDMMM',   'YY.DD.MMM',   'YYMMMDD',   'YY.MMM.DD',
        'YYYYDDMM',  'YYYY.DD.MM',  'YYYYMMDD',  'YYYY.MM.DD',
        'YYYYDDMMM', 'YYYY.DD.MMM', 'YYYYMMMDD', 'YYYY.MMM.DD'
    );

    public static $MMDDTokens = array(
        'DM',        'D.M',         'DDM',       'DD.M',
        'DMM',       'D.MM',        'DDMM',      'DD.MM',
        'DMMM',      'D.MMM',       'DDMMM',     'DD.MMM',
        'MD',        'M.D',         'MDD',       'M.DD',
        'MMD',       'MM.D',        'MMDD',      'MM.DD',
        'MMMD',      'MMM.D',       'MMMDD',     'MMM.DD'
    );

    public static $singleTokens = array(
        'KHZ', 'ID',  'GSQ',  'PWR',  'QTH',   'SP',    'ITU',
        'LSB', 'USB', '~LSB', '~USB', '+SB-',  '+~SB-', '+K-', 'ABS', '~ABS',
        'sec', 'fmt', 'x',    'X',    'hh:mm', 'hhmm',
        'D',   'DD',  'M',    'MM',   'MMM',   'YY',    'YYYY'
    );


    public function draw()
    {
        global $mode, $submode, $log_format, $log_entries, $log_dd, $log_mm, $log_yyyy;
        global $fmt, $sec, $ID, $KHZ, $LSB, $LSB_approx, $USB, $USB_approx, $YYYYMMDD, $hhmm, $daytime;

        $this->listener = new \Rxx\Listener(\Rxx\Rxx::get_var('listenerID'));
        if ($this->listener->getID()) {
            $this->listener->load();
        }

        $this->html.=
             "<form name='form' action='".system_URL."/".$mode."' method='POST'>"
            ."<input type='hidden' name='submode' value=''>";
        switch ($submode) {
            case "save_format":
                $this->updateLogFormat();
                $submode = '';
                break;
            case "submit_log":
                set_time_limit(600);    // Extend maximum execution time to 10 mins
                if ($this->debug) {
                    $this->html.=
                         "1: Logged at least once from this state<br>"
                        ."2: No listener yet listed in this state<br>"
                        ."3: Listener listed, but this is not a duplicate logging so add a new one<br>"
                        ."4: signal never logged in this state<br>";
                }
                for ($i=0; $i<count($ID); $i++) {
                    if ($this->debug) {
                        $this->html.=    "<li>ID=".$ID[$i]." ";
                    }
                    $update_signal =            false;
                    $update_signal_heard_in =   true;
                    $signal =   new \Rxx\Signal($ID[$i]);
                    $dx =       $signal->getDx($this->listener->record["lat"], $this->listener->record["lon"]);
                    $dx_miles = $dx[0];
                    $dx_km =    $dx[1];
                    $daytime =  ($this->listener->isDaytime($hhmm[$i]) ? 1 : 0);
                    $heardIn =  ($this->listener->record['SP'] ? $this->listener->record['SP'] : $this->listener->record['ITU']);
                    $data = array(
                        'signalID' =>   $ID[$i],
                        'date' =>       $YYYYMMDD[$i],
                        'daytime' =>    $daytime,
                        'heard_in' =>   $heardIn,
                        'listenerID' => $this->listener->getID(),
                        'region' =>     $this->listener->record["region"]
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
                    if ($row = \Rxx\Log::checkIfHeardAtPlace($ID[$i], $heardIn)) {
                        if ($this->debug) {
                            $this->html.=    "1 ";
                        }
                        if ($row = \Rxx\Log::checkIfDuplicate(
                            $ID[$i],
                            $this->listener->getID(),
                            $YYYYMMDD[$i],
                            $hhmm[$i]
                        )) {
                            $this->stats['exact_duplicate']++;
                        } else {
                            $update_signal = true;
                            if ($this->debug) {
                                $this->html.= "3 ";
                            }
                            $row = \Rxx\Log::countTimesHeardByListener($ID[$i], $this->listener->getID());
                            if ($row["count"]) {
                                $this->stats['repeat_for_listener']++;
                            } else {
                                $this->stats['first_for_listener']++;
                            }
                            $log = new \Rxx\Log;
                            $log->insert($data);
                            $update_signal = true;          // Update signal record (IF this data is the most recent...)
                        }
                    } else {
                        if ($this->debug) {
                            $this->html.=    "4 ";
                        }
                        $log = new \Rxx\Log;
                        $log->insert($data);

                        $update_signal = true;              // Update signal record (IF this data is the most recent...)
                        $update_signal_heard_in =    true;  // Update signal heard in record
                        $this->stats['first_for_state_or_itu']++;
                        $this->stats['first_for_listener']++;
                    }
                    if ($this->debug) {
                        $this->html.=
                             "<li>update_signal = "
                            .($update_signal ? 'Y' : 'N')
                            .", update_signal_heard_in = "
                            .($update_signal_heard_in ? 'Y' : 'N')
                            ."</li>\n";
                    }

                    if ($update_signal_heard_in) {
                        $signal->updateHeardInList();
                    }

                    if ($update_signal) {
                    // See if the data is more recent than MLR:
                        $sql =
                             "SELECT\n"
                            ."  DATE_FORMAT(`last_heard`,'%Y%m%d') AS `last_heard`\n"
                            ."FROM\n"
                            ."  `signals`\n"
                            ."WHERE\n"
                            ."  `ID` = \"".$ID[$i]."\"";
                        $result =    \Rxx\Database::query($sql);
                        $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                        if ($row["last_heard"] >= $YYYYMMDD[$i]) {
                            $update_signal = false;
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
                    $result =   \Rxx\Database::query($sql);
                    $row =      \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                    $logs =     $row["logs"];

                    if ($update_signal) {
                        $this_YYYY =  substr($YYYYMMDD[$i], 0, 4);
                        $this_MM =    substr($YYYYMMDD[$i], 4, 2);
                        $this_DD =    substr($YYYYMMDD[$i], 6, 2);
                        $this->stats['latest_for_signal']++;
                        $last_heard = $this_YYYY."-".$this_MM."-".$this_DD;
                        \Rxx\Tools\Signal::signal_update_full(
                            $ID[$i],
                            $LSB[$i],
                            $LSB_approx[$i],
                            $USB[$i],
                            $USB_approx[$i],
                            $sec[$i],
                            htmlentities($fmt[$i]),
                            $logs,
                            $last_heard,
                            $this->listener->record["region"]
                        );
                    } else {
                        $sql =
                             "UPDATE\n"
                            ."  `signals`\n"
                            ."SET\n"
                            ."  `logs` = $logs,\n"
                            ."  `heard_in_".$this->listener->record["region"]."`=1\n"
                            ."WHERE\n"
                            ."  `ID` = ".$ID[$i];
                        \Rxx\Database::query($sql);
                        if ($this->debug) {
                            $this->html.=    "<pre>$sql</pre>";
                        }
                    }
                }
                \Rxx\Rxx::update_listener_log_count($this->listener->getID());
                break;
        }

        switch ($submode) {
            case "":
                if ($this->listener->getID()) {
                    $log_format =    $this->listener->record["log_format"];
                }
                $this->html.=
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
                    ." style='font-family: monospace;'>\n"
                    .\Rxx\Rxx::get_listener_options_list("1", $this->listener->getID(), "Select Listener")
                    ."</select>\n"
                    ."</td>\n"
                    ."  </tr>\n"
                    ."</table>\n";
                break;

            case "parse_log":
                $this->listener->updateLogFormat($log_format);
                $this->listener->load();
                $this->html.=
                     "<h1>Add Log > Confirm Data</h1><br>"
                    ."<img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>"
                    ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
                    ."  <tr>\n"
                    ."    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n"
                    ."  </tr>\n"
                    ."  <tr class='rownormal'>\n"
                    ."    <th align='left'>Listener</th>\n"
                    ."    <td colspan='3'>\n"
                    ."    <input type='hidden' name='listenerID' value='".$this->listener->getID()."'>\n"
                    .$this->listener->record["name"]
                    .($this->listener->record["callsign"] ? " <b>".$this->listener->record["callsign"]."</b>" : "")." "
                    .$this->listener->record["QTH"].", "
                    .($this->listener->record["SP"] ? $this->listener->record["SP"].", " : "")
                    .$this->listener->record["ITU"]
                    .($this->listener->record["notes"] ? " (".stripslashes($this->listener->record["notes"]).")" : "")
                    ."</td>\n"
                    ."  </tr>\n"
                    ."</table>\n";
                break;

            case "submit_log":
                $this->drawStats();
                break;
        }

        $this->tokens =    array();
        $start =    0;
        $len =      0;

        $log_format_parse =    $log_format." ";
        $log_format_errors = "";
        $valid = array_merge(
            static::$singleTokens,
            static::$MMDDTokens,
            static::$YYYYMMDDTokens
        );
        $tokens =       array();
        $fieldFlags =   array();
        $flags =        array();
        while (substr($log_format_parse, $start, 1)==" ") {
            $start++;
        }
        while ($start<strlen($log_format_parse)) {
            $len =        strpos(substr($log_format_parse, $start), " ");
            $param_name =    substr($log_format_parse, $start, $len);
            if ($len) {
                while (substr($log_format_parse, $start+$len, 1)==" ") {
                    $len++;
                }
                if ($param_name=="X" || !isset($this->tokens[$param_name])) {
                    $this->tokens[$param_name] = array($start,$len+1);
                    if (!in_array($param_name, $valid)) {
                        $tokens[] = $param_name;
                        $flags[] = "<span style='color:#ff0000;font-weight:bold;cursor:pointer' title='Token not recognised'>".$param_name."</span>";
                        $log_format_errors.=
                             "<tr class='rownormal'>\n"
                            ."  <th align='left'>".$param_name."</th>\n"
                            ."  <td><span style='color:#ff0000;'>Token not recognised</span></td>\n"
                            ."</tr>\n";

                    }
                } else {
                    $tokens[] = $param_name;
                    $flags[] = "<span style='color:#ff00ff;font-weight:bold;cursor:pointer' title='Token occurs more than once'>".$param_name."</span>";
                    $log_format_errors.=
                         "<tr class='rownormal'>\n"
                        ."  <th align='left'>".$param_name."</th>\n"
                        ."  <td><span style='color:#ff00ff;'>Token occurs more than once</span></td>\n"
                        ."</tr>\n";
                }
            }
            $start = $start+$len;
        }
        if (($submode=="" || $submode=="parse_log") && $log_format_errors!="") {
            $this->html.=
                 "<br><span class='p'><b>Log Format Errors</b></span>\n"
                ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0' width='100%'>\n"
                ."  <tr class='downloadTableHeadings'>\n"
                ."    <th colspan='2'>Problems Seen</th>\n"
                ."  </tr>\n"
                ."  <tr class='rownormal'>\n"
                ."    <th align='left'>Input</th>\n"
                ."    <td><pre style='margin:0;'>".str_replace($tokens, $flags, $log_format)."</pre></td>\n"
                ."  </tr>\n"
                .$log_format_errors
                ."</table>\n\n"
                ."<ul>\n"
                ."  <li>Click <a href='javascript:history.back()'><b><u>here</u></b></a> to check your log format and try again.</li>\n"
                ."  <li>Click <a href='".system_URL."/admin_help' target='_blank'><b><u>here</u></b></a> for the full list of tokens that can be used.</li>\n"
                ."</ul>\n";
        }

        $this->checkLogDateTokens();

        switch ($submode) {
            case "parse_log":
                if (!isset($this->tokens["ID"])) {
                    $this->html.=
                         "<br><h1>Error</h1><p>Your log format must include the ID field.<br>"
                        ."Click <a href='javascript:history.back()'><b><u>here</u></b></a>"
                        ." to check your log format and try again.</p>";
                } else {
                    $this->html.=
                         "<br><span class='p'><b>Parser Results</b><small> - see"
                        ." <a href='#next'><b>below</b></a> for suggested <b>Next Steps</b></small></span>\n"
                        ."<table border='0' cellpadding='2' cellspacing='1' bgcolor='#c0c0c0' class='uploadParse'>\n"
                        ."  <tr class='downloadTableHeadings_nosort'>\n"
                        ."    <th class='KHz'>KHz</th>\n"
                        ."    <th class='ID'>ID</th>\n"
                        ."    <th class='ITU'>ITU</th>\n"
                        ."    <th class='SP'>SP</th>\n"
                        ."    <th class='GSQ'>GSQ</th>\n"
                        ."    <th class='QTH'>QTH</th>\n"
                        ."    <th class='DT' title='Daytime Logging - 10am to 2pm local time for listener'>DT<br /><sup>(*)</sup></th>\n"
                        ."    <th class='Km'>DX<br />Km</th>\n"
                        ."    <th class='Mi'>DX<br />Mi</th>\n"
                        ."    <th class='HeardIn'>Heard In</th>\n"
                        ."    <th class='YYYYMMDD'>YYYYMMDD</th>\n"
                        ."    <th class='hhmm'>hhmm</th>\n"
                        ."    <th class='LSB'>LSB</th>\n"
                        ."    <th class='USB'>USB</th>\n"
                        ."    <th class='Sec'>Sec</th>\n"
                        ."    <th class='Fmt'>Fmt</th>\n"
                        ."    <th class='New'>New?</th>\n"
                        ."  </tr>\n";
                    $lines =        explode("\r", " ".stripslashes($log_entries));
                    $unresolved_signals =    array();

                    $total_loggings =    0;
                    $date_fail =        false;
                    if (isset($this->tokens["DM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DM\"][0])); return (\$Y.M_to_MM(substr(\$t,1,1)).D_to_DD(substr(\$t,0,1))); }");
                    } elseif (isset($this->tokens["D.M"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.M\"][0])); return (\$Y.M_to_MM(substr(\$t,2,1)).D_to_DD(substr(\$t,0,1))); }");
                    } elseif (isset($this->tokens["DDM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDM\"][0])); return (\$Y.M_to_MM(substr(\$t,2,1)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.M"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.M\"][0])); return (\$Y.M_to_MM(substr(\$t,3,1)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DMM\"][0])); return (\$Y.substr(\$t,1,2).D_to_DD(substr(\$t,0,1))); }");
                    } elseif (isset($this->tokens["D.MM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.MM\"][0])); return (\$Y.substr(\$t,2,2).D_to_DD(substr(\$t,0,1))); }");
                    } elseif (isset($this->tokens["DDMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMM\"][0])); return (\$Y.substr(\$t,2,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.MM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM\"][0])); return (\$Y.substr(\$t,3,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DMMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DMMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,1,3)).D_to_DD(substr(\$t,0,1))); }");
                    } elseif (isset($this->tokens["D.MMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"D.MMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,2,3)).D_to_DD(substr(\$t,0,1))); }");
                    } elseif (isset($this->tokens["DDMMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.MMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM\"][0])); return (\$Y.MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }");
                    }

                    if (isset($this->tokens["MD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).D_to_DD(substr(\$t,1,1))); }");
                    } elseif (isset($this->tokens["M.D"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"M.D\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).D_to_DD(substr(\$t,2,1))); }");
                    } elseif (isset($this->tokens["MDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MDD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).substr(\$t,1,2)); }");
                    } elseif (isset($this->tokens["M.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"M.DD\"][0])); return (\$Y.M_to_MM(substr(\$t,0,1)).substr(\$t,2,2)); }");
                    } elseif (isset($this->tokens["MMD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMD\"][0])); return (\$Y.substr(\$t,0,2).D_to_DD(substr(\$t,2,1))); }");
                    } elseif (isset($this->tokens["MM.D"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.D\"][0])); return (\$Y.substr(\$t,0,2).D_to_DD(substr(\$t,3,1))); }");
                    } elseif (isset($this->tokens["MMDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDD\"][0])); return (\$Y.substr(\$t,0,2).substr(\$t,2,2)); }");
                    } elseif (isset($this->tokens["MM.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD\"][0])); return (\$Y.substr(\$t,0,2).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["MMMD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).D_to_DD(substr(\$t,3,1))); }");
                    } elseif (isset($this->tokens["MMM.D"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.D\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).D_to_DD(substr(\$t,4,1))); }");
                    } elseif (isset($this->tokens["MMMDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["MMM.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD\"][0])); return (\$Y.MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }");
                    } elseif (isset($this->tokens["DDMMYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMYY\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).substr(\$t,2,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.MM.YY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM.YY\"][0])); return (YY_to_YYYY(substr(\$t,6,2)).substr(\$t,3,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DDYYMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYMM\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).substr(\$t,4,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.YY.MM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YY.MM\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).substr(\$t,6,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["MMDDYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDDYY\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).substr(\$t,0,2).substr(\$t,2,2)); }");
                    } elseif (isset($this->tokens["MM.DD.YY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD.YY\"][0])); return (YY_to_YYYY(substr(\$t,6,2)).substr(\$t,0,2).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["MMYYDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMYYDD\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).substr(\$t,0,2).substr(\$t,4,2)); }");
                    } elseif (isset($this->tokens["MM.YY.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.YY.DD\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).substr(\$t,0,2).substr(\$t,6,2)); }");
                    } elseif (isset($this->tokens["YYDDMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYDDMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,4,2).substr(\$t,2,2)); }");
                    } elseif (isset($this->tokens["YY.DD.MM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.DD.MM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,6,2).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["YYMMDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYMMDD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,2,2).substr(\$t,4,2)); }");
                    } elseif (isset($this->tokens["YY.MM.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.MM.DD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).substr(\$t,3,2).substr(\$t,6,2)); }");
                    } elseif (isset($this->tokens["DDMMMYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMMYY\"][0])); return (YY_to_YYYY(substr(\$t,5,2)).MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.MMM.YY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM.YY\"][0])); return (YY_to_YYYY(substr(\$t,7,2)).MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DDYYMMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYMMM\"][0])); return (YY_to_YYYY(substr(\$t,2,2)).MMM_to_MM(substr(\$t,4,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.YY.MMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YY.MMM\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).MMM_to_MM(substr(\$t,6,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["MMMDDYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDDYY\"][0])); return (YY_to_YYYY(substr(\$t,5,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["MMM.DD.YY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD.YY\"][0])); return (YY_to_YYYY(substr(\$t,7,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }");
                    } elseif (isset($this->tokens["MMMYYDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMYYDD\"][0])); return (YY_to_YYYY(substr(\$t,3,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,5,2)); }");
                    } elseif (isset($this->tokens["MMM.YY.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.YY.DD\"][0])); return (YY_to_YYYY(substr(\$t,4,2)).MMM_to_MM(substr(\$t,0,3)).substr(\$t,7,2)); }");
                    } elseif (isset($this->tokens["YYDDMMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYDDMMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,4,3)).substr(\$t,2,2)); }");
                    } elseif (isset($this->tokens["YY.DD.MMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.DD.MMM\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,6,3)).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["YYMMMDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYMMMDD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,2,3)).substr(\$t,5,2)); }");
                    } elseif (isset($this->tokens["YY.MMM.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YY.MMM.DD\"][0])); return (YY_to_YYYY(substr(\$t,0,2)).MMM_to_MM(substr(\$t,3,3)).substr(\$t,7,2)); }");
                    } elseif (isset($this->tokens["DDMMYYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMYYYY\"][0])); return (substr(\$t,4,4).substr(\$t,2,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.MM.YYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MM.YYYY\"][0])); return (substr(\$t,6,4).substr(\$t,3,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DDYYYYMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYYYMM\"][0])); return (substr(\$t,2,4).substr(\$t,6,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.YYYY.MM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YYYY.MM\"][0])); return (substr(\$t,3,4).substr(\$t,8,2).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["MMDDYYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMDDYYYY\"][0])); return (substr(\$t,4,4).substr(\$t,0,2).substr(\$t,2,2)); }");
                    } elseif (isset($this->tokens["MM.DD.YYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.DD.YYYY\"][0])); return (substr(\$t,6,4).substr(\$t,0,2).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["MMYYYYDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMYYYYDD\"][0])); return (substr(\$t,2,4).substr(\$t,0,2).substr(\$t,6,2)); }");
                    } elseif (isset($this->tokens["MM.YYYY.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MM.YYYY.DD\"][0])); return (substr(\$t,3,4).substr(\$t,0,2).substr(\$t,8,2)); }");
                    } elseif (isset($this->tokens["YYYYDDMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYDDMM\"][0])); return (substr(\$t,0,4).substr(\$t,6,2).substr(\$t,4,2)); }");
                    } elseif (isset($this->tokens["YYYY.DD.MM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.DD.MM\"][0])); return (substr(\$t,0,4).substr(\$t,8,2).substr(\$t,5,2)); }");
                    } elseif (isset($this->tokens["YYYYMMDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYMMDD\"][0])); return (substr(\$t,0,4).substr(\$t,4,2).substr(\$t,6,2)); }");
                    } elseif (isset($this->tokens["YYYY.MM.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.MM.DD\"][0])); return (substr(\$t,0,4).substr(\$t,5,2).substr(\$t,8,2)); }");
                    } elseif (isset($this->tokens["DDMMMYYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDMMMYYYY\"][0])); return (substr(\$t,5,4).MMM_to_MM(substr(\$t,2,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.MMM.YYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.MMM.YYYY\"][0])); return (substr(\$t,7,4).MMM_to_MM(substr(\$t,3,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DDYYYYMMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DDYYYYMMM\"][0])); return (substr(\$t,2,4).MMM_to_MM(substr(\$t,6,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["DD.YYYY.MMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"DD.YYYY.MMM\"][0])); return (substr(\$t,3,4).MMM_to_MM(substr(\$t,8,3)).substr(\$t,0,2)); }");
                    } elseif (isset($this->tokens["MMMDDYYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMDDYYYY\"][0])); return (substr(\$t,5,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,3,2)); }");
                    } elseif (isset($this->tokens["MMM.DD.YYYY"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.DD.YYYY\"][0])); return (substr(\$t,7,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,4,2)); }");
                    } elseif (isset($this->tokens["MMMYYYYDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMMYYYYDD\"][0])); return (substr(\$t,3,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,7,2)); }");
                    } elseif (isset($this->tokens["MMM.YYYY.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"MMM.YYYY.DD\"][0])); return (substr(\$t,4,4).MMM_to_MM(substr(\$t,0,3)).substr(\$t,9,2)); }");
                    } elseif (isset($this->tokens["YYYYDDMMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYDDMMM\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,6,3)).substr(\$t,4,2)); }");
                    } elseif (isset($this->tokens["YYYY.DD.MMM"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.DD.MMM\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,8,3)).substr(\$t,5,2)); }");
                    } elseif (isset($this->tokens["YYYYMMMDD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYYMMMDD\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,4,3)).substr(\$t,7,2)); }");
                    } elseif (isset($this->tokens["YYYY.MMM.DD"])) {
                        eval("function parse(\$a,\$b,\$Y,\$M,\$D){\$t = trim(substr(\$b,\$a[\"YYYY.MMM.DD\"][0])); return (substr(\$t,0,4).MMM_to_MM(substr(\$t,5,3)).substr(\$t,9,2)); }");
                    }

                    for ($i=0; $i<count($lines); $i++) {
                        //          print "<pre>".$lines[$i]."</pre>";
                        $YYYY =    \Rxx\Rxx::YY_to_YYYY($log_yyyy);
                        $MM =      \Rxx\Rxx::M_to_MM($log_mm);
                        $DD =      \Rxx\Rxx::D_to_DD($log_dd);

                        if (function_exists("parse")) {
                            $YYYYMMDD = parse($this->tokens, $lines[$i], $YYYY, $MM, $DD);
                            $YYYY =     substr($YYYYMMDD, 0, 4);
                            $MM =       substr($YYYYMMDD, 4, 2);
                            $DD =       substr($YYYYMMDD, 6, 2);
                        } elseif (isset($this->tokens["D"]) ||
                            isset($this->tokens["DD"]) ||
                            isset($this->tokens["M"]) ||
                            isset($this->tokens["MM"]) ||
                            isset($this->tokens["MMM"]) ||
                            isset($this->tokens["YY"]) ||
                            isset($this->tokens["YYYY"])
                        ) {
                            if (isset($this->tokens["D"])) {
                                $DD =
                                    \Rxx\Rxx::D_to_DD(trim(substr($lines[$i], $this->tokens["D"][0], 2)));
                            }
                            if (isset($this->tokens["DD"])) {
                                $DD =
                                    trim(substr($lines[$i], $this->tokens["DD"][0], $this->tokens["DD"][1]));
                            }
                            if (isset($this->tokens["M"])) {
                                // DD shown in log
                                $MM =
                                    \Rxx\Rxx::M_to_MM(trim(substr($lines[$i], $this->tokens["M"][0], $this->tokens["M"][1])));
                            }
                            if (isset($this->tokens["MM"])) {
                                // DD shown in log
                                $MM =
                                    trim(substr($lines[$i], $this->tokens["MM"][0], $this->tokens["MM"][1]));
                            }
                            if (isset($this->tokens["MMM"])) {
                                // DD shown in log
                                $MM =
                                    \Rxx\Rxx::MMM_to_MM(trim(substr($lines[$i], $this->tokens["MMM"][0], $this->tokens["MMM"][1])));
                            }
                            if (isset($this->tokens["YY"])) {
                                // DD shown in log
                                $YYYY =
                                    \Rxx\Rxx::YY_to_YYYY(trim(substr($lines[$i], $this->tokens["YY"][0], $this->tokens["YY"][1])));
                                    print 'yes';
                            }
                            if (isset($this->tokens["YYYY"])) {
                                // DD shown in log
                                $YYYY =
                                    trim(substr($lines[$i], $this->tokens["YYYY"][0], $this->tokens["YYYY"][1]));
                            }
                        }

                        $YYYYMMDD =    $YYYY.$MM.$DD;

                        // Parse Time: Options are hh:mm and hhmm
                        $hhmm =        "";
                        if (isset($this->tokens["hh:mm"])) {
                            // hh:mm shown in log
                            $hhmm_arr = explode(":", trim(substr($lines[$i], $this->tokens["hh:mm"][0], 6)));
                            if (isset($hhmm_arr[1])) {
                                $hhmm =
                                    (strlen($hhmm_arr[0])==1 ? "0" : "").$hhmm_arr[0].$hhmm_arr[1];
                            }
                        }
                        if (isset($this->tokens["hhmm"])) {
                            // hhmm shown in log
                            $hhmm =
                                substr(trim(substr($lines[$i], $this->tokens["hhmm"][0], $this->tokens["hhmm"][1])), 0, 4);
                        }
                        if (!is_numeric($hhmm)) {
                            $hhmm =    "";
                        }
                        $KHZ =    (float)(isset($this->tokens["KHZ"]) ?
                            str_replace(",", ".", trim(substr($lines[$i], $this->tokens["KHZ"][0], $this->tokens["KHZ"][1])))
                         :
                            ""
                        );
                        $ID =     strtoUpper(trim(substr($lines[$i], $this->tokens["ID"][0], $this->tokens["ID"][1])));

                        $sec =    (isset($this->tokens["sec"]) ?
                            htmlentities(trim(substr($lines[$i], $this->tokens["sec"][0], $this->tokens["sec"][1])))
                         :
                            ""
                        );
                        $fmt =    (isset($this->tokens["fmt"]) ?
                            htmlentities(trim(substr($lines[$i], $this->tokens["fmt"][0], $this->tokens["fmt"][1])))
                         :
                            ""
                        );
                        $LSB =    "";
                        $USB =    "";
                        $LSB_approx =    "";
                        $USB_approx =    "";
                        if (isset($this->tokens["LSB"])) {
                            $LSB =        trim(substr($lines[$i], $this->tokens["LSB"][0], $this->tokens["LSB"][1]));
                            if (substr($LSB, 0, 1)=="~") {
                                $LSB =    substr($LSB, 1);
                                $LSB_approx =    "~";
                            }
                            if ($LSB=="---") {
                                // Andy Robins logs use --- as blank
                                $LSB = "";
                            }
                        }
                        if (isset($this->tokens["USB"])) {
                            $USB =        trim(substr($lines[$i], $this->tokens["USB"][0], $this->tokens["USB"][1]));
                            if (substr($USB, 0, 1)=="~") {
                                $USB =    substr($USB, 1);
                                $USB_approx =    "~";
                            }
                            if ($USB=="---") {
                                $USB = "";
                            }
                        }
                        if (isset($this->tokens["~LSB"])) {
                            $LSB =        trim(substr($lines[$i], $this->tokens["~LSB"][0], $this->tokens["~LSB"][1]));
                            $LSB_approx =    "~";
                        }
                        if (isset($this->tokens["~USB"])) {
                            $USB =        trim(substr($lines[$i], $this->tokens["~USB"][0], $this->tokens["~USB"][1]));
                            $USB_approx =    "~";
                        }

                        // The following parameters are only used for simplifying adding of new signals"
                        // if the input format happens to include them:
                        $GSQ =    (isset($this->tokens["GSQ"]) ?
                            trim(substr($lines[$i], $this->tokens["GSQ"][0], $this->tokens["GSQ"][1]))
                         :
                            ""
                        );
                        $QTH =    (isset($this->tokens["QTH"]) ?
                            trim(substr($lines[$i], $this->tokens["QTH"][0], $this->tokens["QTH"][1]))
                         :
                            ""
                        );
                        $ITU =    (isset($this->tokens["ITU"]) ?
                            trim(substr($lines[$i], $this->tokens["ITU"][0], $this->tokens["ITU"][1]))
                         :
                            ""
                        );
                        $SP =     (isset($this->tokens["SP"]) ?
                            trim(substr($lines[$i], $this->tokens["SP"][0], $this->tokens["SP"][1]))
                         :
                            ""
                        );
                        $PWR =    (isset($this->tokens["PWR"]) ?
                            trim(substr($lines[$i], $this->tokens["PWR"][0], $this->tokens["PWR"][1]))
                         :
                            ""
                        );


                        if (isset($this->tokens["+SB-"])) {
                            $sb =    str_replace(
                                "–",
                                "-",
                                trim(substr($lines[$i], $this->tokens["+SB-"][0], $this->tokens["+SB-"][1]))
                            );
                            // Convert hyphen symbol to - (For Steve R's Offsets)
                            $sb_arr =    explode(" ", $sb);
                            for ($j=0; $j<count($sb_arr); $j++) {
                                $sb =    trim($sb_arr[$j]);
                                if ($sb=="X" || $sb=="X-") {
                                    // Format used by Jim Smith to indicate sb not present
                                    $sb="";
                                }
                                if ($sb=="DAID" ||
                                    $sb=="DA2ID" ||
                                    $sb=="DA3ID" ||
                                    $sb=="DBID" ||
                                    $sb=="DB2ID" ||
                                    $sb=="DB3ID") {
                                    $fmt = $sb;
                                }
                                if ((substr($sb, 0, 1)=="+" && substr($sb, strlen($sb)-1, 1)=="-") ||
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

                        if (isset($this->tokens["+~SB-"])) {
                            $sb =    str_replace(
                                "–",
                                "-",
                                trim(substr($lines[$i], $this->tokens["+~SB-"][0], $this->tokens["+~SB-"][1]))
                            );
                            // Convert hyphen symbol to - (For Steve R's Offsets)
                            $sb =    str_replace("~", "", $sb); // Remove ~ symbol now we know it's approx
                            $sb_arr =    explode(" ", $sb);
                            for ($j=0; $j<count($sb_arr); $j++) {
                                $sb =    trim($sb_arr[$j]);
                                if ($sb=="DAID" ||
                                    $sb=="DA2ID" ||
                                    $sb=="DA3ID" ||
                                    $sb=="DBID" ||
                                    $sb=="DB2ID" ||
                                    $sb=="DB3ID"
                                ) {
                                    $fmt = $sb;
                                } elseif ((substr($sb, 0, 1)=="+" && substr($sb, strlen($sb)-1, 1)=="-") ||
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
                        if (isset($this->tokens["+K-"])) {
                            $sb = trim(
                                str_replace(
                                    "–",
                                    "-",
                                    trim(substr($lines[$i], $this->tokens["+K-"][0], $this->tokens["+K-"][1]))
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


                        if (isset($this->tokens["ABS"])) {
                            $ABS =    trim(substr($lines[$i], $this->tokens["ABS"][0], $this->tokens["ABS"][1]));
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

                        if (isset($this->tokens["~ABS"])) {
                            $ABS =    trim(substr($lines[$i], $this->tokens["~ABS"][0], $this->tokens["~ABS"][1]));
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

                                $result = @\Rxx\Database::query($sql);
                                if (\Rxx\Database::getError()) {
                                    $this->html.= "Problem looking up station - frequency was $KHZ";
                                }
                                if ($result && \Rxx\Database::numRows($result)) {
                                    $total_loggings++;
                                    if (\Rxx\Database::numRows($result) == 1) {
                                        $this->html.=    "<tr class='rownormal'>\n";
                                        $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
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
                                        $dx = \Rxx\Rxx::get_dx($this->listener->record['lat'], $this->listener->record['lon'], $row["lat"], $row["lon"]);
                                        $this->html.=
                                             "  <td class='KHz'>"
                                            ."<input type='hidden' name='ID[]' value='".$row["ID"]."'>"
                                            .(((float)$row['khz']<198 || (float)$row['khz'] > 530) ?
                                                "<font color='#FF8C00'><b>".(float)$row['khz']."</b></font>"
                                             :
                                                (float)$row['khz']
                                            )
                                            ."</td>\n"
                                            ."  <td$bgcolor class='ID'>"
                                            ."<a style='font-family:monospace' href='javascript:signal_info(\"".$row["ID"]."\")'>$ID</a>"
                                            ."</td>\n"
                                            ."  <td class='ITU'>".$row['ITU']."</td>\n"
                                            ."  <td class='SP'>".($row['SP'] ? $row['SP'] : "&nbsp;")."</td>\n"
                                            ."  <td class='GSQ'>"
                                            .($row["GSQ"] ?
                                                 "<a href='javascript:popup_map("
                                                ."\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\""
                                                .")' title='Show map (accuracy limited to nearest Grid Square)'>"
                                                .$row["GSQ"]."</a>"
                                             :
                                                "&nbsp;"
                                             )
                                            ."</td>\n"
                                            ."  <td class='QTH'"
                                            .($row['QTH'] ?
                                                ""
                                             :
                                                " bgcolor='#FFE7B9' title='Please provide a value for QTH if you have one'"
                                            )
                                            .">"
                                            .$row['QTH']
                                            ."</td>\n"
                                            ."  <td class='DT center'>".($this->listener->isDaytime($hhmm) ? 'Y' : '')."</td>\n"
                                            ."  <td class='Km num'>".($dx[1]!=='' ? number_format($dx[1]) : '')."</th>\n"
                                            ."  <td class='Mi num'>".($dx[0]!=='' ? number_format($dx[0]) : '')."</th>\n"
                                            ."  <td class='HeardIn'>"
                                            .(strpos($row['heard_in'], ($this->listener->record['SP'] ? $this->listener->record['SP'] : $this->listener->record['ITU']))===false ?
                                               "<font color='#008000'><b>".$row['heard_in']."</b></font>"
                                              :
                                                \Rxx\Rxx::highlight($row['heard_in'], ($this->listener->record['SP'] ? $this->listener->record['SP'] : $this->listener->record['ITU']))
                                            )
                                            ."</td>\n";

                                    } else {
                                        $this->html.=
                                             "<tr bgcolor='#ffe0a0'>\n"
                                            ."  <td colspan='10' class='Combined'>"
                                            ."<select name='ID[]' class='formfixed' style='width:844px;overflow:hidden;text-overflow:ellipsis'>\n";
                                        $defaultChosen =    false;
                                        $selected =         false;
                                        for ($j=0; $j<\Rxx\Database::numRows($result); $j++) {
                                            $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                                            $dx = get_dx($this->listener->record['lat'], $this->listener->record['lat'], $row["lat"], $row["lon"]);
                                            if (!$defaultChosen && $row["active"]=="1") {
                                                $selected = true;
                                                $defaultChosen =  true;
                                            }
                                            $title =
                                                 (float)$row["khz"]." | "
                                                .$ID." | "
                                                .$row["ITU"]." | "
                                                .($row["SP"] ? $row["SP"] : '  ')." | "
                                                .$row["GSQ"]." | "
                                                .$row["QTH"]." | "
                                                .($this->listener->isDaytime($hhmm) ? 'Y' : ' ')." | "
                                                .($dx[1]!=='' ? number_format($dx[1])."km" : "  ")." | "
                                                .($dx[0]!=='' ? number_format($dx[0])."mi" : "  ")." | "
                                                .$row["heard_in"];
                                            $label =
                                                 pad_nbsp((float)$row["khz"], 5)."|"
                                                .pad_nbsp($ID, 5)."|"
                                                .$row["ITU"]." |"
                                                .($row["SP"] ? $row["SP"] : "&nbsp;&nbsp;")."&nbsp;|"
                                                .pad_nbsp($row["GSQ"], 6)."|"
                                                .(strlen($row["QTH"])<25 ? pad_nbsp($row["QTH"], 25) : pad_nbsp(substr($row["QTH"], 0, 22).'...', 25))."|"
                                                .($this->listener->isDaytime($hhmm) ? ' Y ' : '&nbsp;&nbsp;&nbsp;')."|"

                                                .lead_nbsp($dx[1]!=='' ? number_format($dx[1]) : "", 6)."|"
                                                .lead_nbsp($dx[0]!=='' ? number_format($dx[0]) : "", 6)."|"
                                                .$row["heard_in"];

                                            $this->html.=
                                                 "<option title='".$title."'"
                                                .($row["active"]=="0" ? " style='background-color: #d0d0d0'" : "")
                                                ." value='".$row["ID"]."'"
                                                .($selected ? " selected='selected'" : "")
                                                .">"
                                                .$label
                                                ."</option>\n";
                                            $selected = false;
                                        }
                                        $this->html.=
                                            "</select></td>\n";
                                    }
                                    $this->html.=
                                        "  <td align='center'><input type='hidden' name='YYYYMMDD[]' value='$YYYYMMDD'>";

                                    if (strlen($YYYYMMDD)!=8) {
                                        $date_fail = true;
                                        $this->html.=
                                            "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                    } elseif ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                                        $date_fail = true;
                                        $this->html.=
                                            "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                    } else {
                                        $this->html.=    ($YYYY<2005 ? "<font color='#FF8C00'><b>$YYYY</b></font>" : "$YYYY");
                                        if (!checkdate($MM, $DD, $YYYY)) {
                                            $date_fail = true;
                                            $this->html.=
                                                "<font color='red'><b><strike>$MM</strike></b></font>";
                                        } else {
                                            $this->html.=    $MM;
                                        }
                                        if (!checkdate($MM, $DD, $YYYY)) {
                                            $date_fail = true;
                                            $this->html.=
                                                "<font color='red'><b><strike>$DD</strike></b></font>";
                                        } else {
                                            $this->html.=    $DD;
                                        }
                                    }
                                    $this->html.=
                                         "</td>\n"
                                        ."  <td align='center'><input type='hidden' name='hhmm[]' value='$hhmm'>";
                                    if ((strlen($hhmm)!=0 && strlen($hhmm)!=4) ||
                                        substr($hhmm, 0, 2)>23 || substr($hhmm, 2, 2)>59
                                    ) {
                                        $date_fail = true;
                                        $this->html.=    "<font color='red'><b><strike>$hhmm</strike></b></font>";
                                    } else {
                                        $this->html.=    $hhmm;
                                    }
                                    $this->html.=
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
                                        ."  <td align='center'></td>\n"
                                        ."</tr>\n";
                                } else {
                                    $dx = array('0','0');
                                    if ($GSQ) {
                                        $ll = GSQ_deg($GSQ);
                                        $dx = get_dx($this->listener->record['lat'], $this->listener->record['lat'], $ll["lat"], $ll["lon"]);
                                    }
                                    $this->html.=
                                         "<tr bgcolor='#ffd0d0' title='signal not listed in database'>\n"
                                        ."  <td>"
                                        .(((float)$KHZ<198 || (float)$KHZ > 530) ?
                                            "<font color='#FF8C00'><b>".(float)$KHZ."</b></font>"
                                         :
                                            (float)$KHZ
                                        )
                                        ."</td>\n"
                                        ."  <td class='ID'>$ID</td>\n"
                                        ."  <td class='ITU'>$ITU</td>\n"
                                        ."  <td class='SP'>$SP</td>\n"
                                        ."  <td class='GSQ'>$GSQ</td>\n"
                                        ."  <td class='QTH'>$QTH</td>\n"
                                        ."  <td class='DT center'>".($this->listener->isDaytime($hhmm) ? 'Y' : '')."</td>\n"
                                        ."  <td class='Km num'>".($dx[1] ? number_format($dx[1]) : '')."</th>\n"
                                        ."  <td class='Mi num'>".($dx[0] ? number_format($dx[0]) : '')."</th>\n"
                                        ."  <td>&nbsp;</td>\n"
                                        ."  <td align='center'>";
                                    if (strlen($YYYYMMDD)!=8) {
                                        $date_fail = true;
                                        $this->html.=    "<font color='red'><b><strike>$YYYYMMDD</strike></i></b></font>";
                                    } elseif ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                                        $date_fail = true;
                                        $this->html.=    "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                    } else {
                                        $this->html.=    ($YYYY<2003 ? "<font color='#FF8C00'><b>$YYYY</b></font>" : "$YYYY");
                                        if (!checkdate($MM, $DD, $YYYY)) {
                                            $date_fail = true;
                                            $this->html.=    "<font color='red'><b><strike>$MM</strike></b></font>";
                                        } else {
                                            $this->html.=    $MM;
                                        }
                                        if (!checkdate($MM, $DD, $YYYY)) {
                                            $date_fail = true;
                                            $this->html.=    "<font color='red'><b><strike>$DD</strike></b></font>";
                                        } else {
                                            $this->html.=    $DD;
                                        }
                                    }
                                    $this->html.=
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
                        $this->html.=
                             "  <tr class='downloadTableHeadings_nosort'>\n"
                            ."    <th colspan='17'>"
                            ."<input type='button' value='Submit Log' class='formbutton' id='btn_go' name='go'"
                            ." onclick='submit_log()'>"
                            ."<script type='text/javascript'>document.form.go.focus()</script>\n"
                            ."</th>\n"
                            ."  </tr>\n";

                    } else {
                        $this->html.=
                             "  <tr class='downloadTableHeadings_nosort'>\n"
                            ."    <th colspan='17'>"
                            ."<input type='button' value='Serious errors found - Go Back...' id='btn_go' class='formbutton' name='go'"
                            ." onclick='history.back()'>"
                            ."<script type='text/javascript'>document.form.go.focus()</script>\n"
                            ."</th>\n"
                            ."  </tr>\n";
                    }

                    $this->html.=  "</table>\n";

                    if (count($unresolved_signals)) {
                        $this->html.=
                             "<p><b>Issues:</b><br>\n"
                            ."<small>There "
                            .(count($unresolved_signals)!=1 ?
                                "are <b><font color='red'>".count($unresolved_signals)." unresolved signals</font></b>"
                             :
                                "<b><font color='red'>is one</font></b> unresolved signal"
                            )
                            ." contained in the log</b>.</small><br>"
                            ."<textarea rows='10' cols='90' style='width:1040px'>"
                            .str_repeat('-', 1+strlen($log_format))."\n"
                            .$log_format."\n"
                            .str_repeat('-', 1+strlen($log_format))
                            .implode("", $unresolved_signals)
                            ."</textarea>";
                    } else {
                        $this->html.=
                            "<span class='p'><small><b>Total Loggings in this report: $total_loggings</b></small></span>";
                    }

                    $this->html.=
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
                        $this->html.=
                             "<li>If you are happy with the results shown above, press"
                            ." <b>Submit Log</b> to process the data.</li>\n";
                    }
                    $this->html.=    "</ul>\n";
                }
                break;

            case "submit_log":
                $this->html.=
                     "<br><input type='submit' value='&nbsp;Done&nbsp;' name='go'"
                    ." onclick='window.close();' class='formbutton'>"
                    ."<script type='text/javascript'>document.form.go.focus();</script>\n";
                break;
        }

        if ($this->listener->getID()!="" && $submode=="") {
            $this->html.=
                 "&nbsp;"
                ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
                ."  <tr>\n"
                ."    <th class='downloadTableHeadings_nosort'>Log to Parse</th>\n"
                ."  </tr>\n"
                ."  <tr class='rownormal'>\n"
                ."    <td><input name='log_format' class='fixed_heading' size='105' style='width:1040px".($log_format_errors ? ';background:#804040' : '')."' value=\"$log_format\">\n"
                ."<input class='formbutton' name='save' type='button' value='Save' onclick='"
                ."this.disabled=true;document.form.go.disabled=true;document.form.conv.disabled=true;"
                ."document.form.submode.value=\"save_format\";document.form.submit()'>"
                ."  </tr>\n"
                ."  <tr class='rownormal'>\n"
                ."    <td><textarea rows='".($log_format_errors ? 19 : 30)."' cols='110' class='fixed' style='width:1100px' name='log_entries'"
                ." onKeyUp='check_for_tabs(document.form);'"
                ." onchange='check_for_tabs(document.form);'>"
                .stripslashes($log_entries)
                ."</textarea>\n"
                ."  </tr>\n";
            if ((!$this->logHas['YYYY'] || !$this->logHas['MM'] || !$this->logHas['DD'])) {
                $this->html.=
                     "  <tr class='rownormal'>\n"
                    ."    <td>The following details are also required: &nbsp; \n";
                if (!$this->logHas['DD']) {
                    $this->html.=
                         "Day "
                        ."<input type='text' name='log_dd' size='2' maxlength='2' class='formfield' value='$log_dd'>\n";
                }
                if (!$this->logHas['MM']) {
                    $this->html.=
                         "Month "
                        ."<input type='text' name='log_mm' size='2' maxlength='2' class='formfield' value='$log_mm'>\n";
                }
                if (!$this->logHas['YYYY']) {
                    $this->html.=
                         "Year "
                        ."<input type='text' name='log_yyyy' size='4' maxlength='4' class='formfield' value='$log_yyyy'>\n";
                }
                $now =        mktime();
                $now_DD =        gmdate("d", $now);
                $now_MM =        gmdate("m", $now);
                $now_YYYY =    gmdate("Y", $now);

                $this->html.=
                     "<input type='button' value='&lt;-- Current' class='formButton' onclick=\""
                    .(!$this->logHas['DD'] ?
                        "if (document.form.log_dd.value=='')   { document.form.log_dd.value='$now_DD'; };"
                     :
                        ""
                    )
                    .(!$this->logHas['MM'] ?
                        "if (document.form.log_mm.value=='')   { document.form.log_mm.value='$now_MM'; };"
                     :
                        ""
                    )
                    .(!$this->logHas['YYYY'] ?
                        "if (document.form.log_yyyy.value=='') { document.form.log_yyyy.value='$now_YYYY'; };"
                     :
                        ""
                    )
                    ."\"></td>\n"
                    ."  </tr>\n";
            }

            $this->html.=
                 "  <tr class='rownormal'>\n"
                ."    <th>"
                ."<input type='button' value='Tabs > Spaces' class='formbutton' name='conv'"
                ." onclick='tabs_to_spaces(document.form)'"
                .(!preg_match("/	/", $log_entries) ? " disabled='disabled'" : "").">\n"
                ."<input type='button' value='Line Up' class='formbutton' name='lineup'"
                ." onclick='line_up(document.form)'>\n"
                ."<input type='button' name='go' value='Parse Log' class='formbutton'"
                ." onclick='if (parse_log(document.form)) { document.form.submode.value=\"parse_log\";"
                ."document.form.submit();}'> "
                ."<script type='text/javascript'>document.form.log_entries.focus();</script>"
                ."</th>\n"
                ."  </tr>\n"
                ."</table>\n";
        }
        $this->html.=    "</form>";
        return $this->html;
    }

    protected function drawStats()
    {
        global $ID;
        $this->html.=
             "<h1>Add Log > Results</h1><br>"
            ."<img src='".BASE_PATH."assets/spacer.gif' height='4' width='1' alt=''>"
            ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
            ."  <tr>\n"
            ."    <th colspan='4' class='downloadTableHeadings_nosort'>Listener Details</th>\n"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <th align='left'>Listener</th>"
            ."    <td colspan='3'><input type='hidden' name='listenerID' value='".$this->listener->getID()."'>"
            .$this->listener->record["name"]
            .($this->listener->record["callsign"] ? " <b>".$this->listener->record["callsign"]."</b>" : "")." "
            .$this->listener->record["QTH"].", "
            .($this->listener->record["SP"] ? $this->listener->record["SP"].", " : "")
            .$this->listener->record["ITU"]
            .($this->listener->record["notes"] ? " (".stripslashes($this->listener->record["notes"]).")" : "")
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
            ."    <td>".$this->stats['exact_duplicate']."</td>\n"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td>First for State or ITU</td>\n"
            ."    <td>".$this->stats['first_for_state_or_itu']."</td>\n"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td>First for Listener</td>\n"
            ."    <td>".$this->stats['first_for_listener']."</td>\n"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td>Repeat logging for Listener</td>\n"
            ."    <td>".$this->stats['repeat_for_listener']."</td>\n"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td>Latest signal logging</td>\n"
            ."    <td>".$this->stats['latest_for_signal']."</td>\n"
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
            ."    <td>".$this->listener->record["count_signals"]."</td>\n"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td>Logs in database</td>\n"
            ."    <td>".$this->listener->record["count_logs"]."</td>\n"
            ."  </tr>\n"
            ."</table>\n";
    }

    protected function checkLogDateTokens()
    {
        foreach (static::$YYYYMMDDTokens as $token) {
            if (isset($this->tokens[$token])) {
                $this->logHas['YYYY'] = true;
                $this->logHas['MM'] =   true;
                $this->logHas['DD'] =   true;
                return;
            }
        }
        foreach (static::$MMDDTokens as $token) {
            if (isset($this->tokens[$token])) {
                $this->logHas['MM'] =   true;
                $this->logHas['DD'] =   true;
                break;
            }
        }
        if (isset($this->tokens["YYYY"]) || isset($this->tokens["YY"])) {
            $this->logHas['YYYY'] = true;
        }
        if (isset($this->tokens["MMM"]) || isset($this->tokens["MM"]) || isset($this->tokens["M"])) {
            $this->logHas['MM'] =   true;
        }
        if (isset($this->tokens["DD"]) || isset($this->tokens["D"])) {
            $this->logHas['DD'] =   true;
        }
    }

    protected function updateLogFormat()
    {
        $this->listener->updateLogFormat(get_var('log_format'));
        $this->listener->load();
    }
}
