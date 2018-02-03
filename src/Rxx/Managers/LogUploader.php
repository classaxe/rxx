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
        global $mode, $submode, $log_format, $log_entries;
        global $fmt, $sec, $ID, $LSB, $LSB_approx, $USB, $USB_approx, $YYYYMMDD, $hhmm, $daytime;
        
        $this->setup();
        $this->html.=
             "<form name='form' action='".system_URL."/".$mode."' method='POST'>"
            ."<input type='hidden' name='submode' value=''>";
        switch ($submode) {
            case "":
                $this->drawInputScreen();
                break;
            case "parse_log":
                $this->formatUpdate();
                $this->html.=
                     "<h1>Add Log > Confirm Data</h1><br>";
                $this->drawListenerDetails();
                break;
            case "save_format":
                $this->formatUpdate();
                $submode = '';
                $this->drawInputScreen();
                break;
            case "submit_log":
                $this->logSubmit();
                \Rxx\Rxx::update_listener_log_count($this->listener->getID());
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
                        $flags[] =
                            "<span style='color:#ff0000;font-weight:bold;cursor:pointer'"
                           ." title='Token not recognised'>"
                           .$param_name
                            ."</span>";
                        $log_format_errors.=
                             "<tr class='rownormal'>\n"
                            ."  <th align='left'>".$param_name."</th>\n"
                            ."  <td><span style='color:#ff0000;'>Token not recognised</span></td>\n"
                            ."</tr>\n";

                    }
                } else {
                    $tokens[] = $param_name;
                    $flags[] =
                         "<span style='color:#ff00ff;font-weight:bold;cursor:pointer'"
                        ." title='Token occurs more than once'>".$param_name."</span>";
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
                ."  <li>Click <a href='javascript:history.back()'><b><u>here</u></b></a>"
                ." to check your log format and try again.</li>\n"
                ."  <li>Click <a href='".system_URL."/admin_help' target='_blank'><b><u>here</u></b></a>"
                ." for the full list of tokens that can be used.</li>\n"
                ."</ul>\n";
        }
        $this->checkLogDateTokens();
        switch ($submode) {
            case "submit_log":
                $this->html.=
                    "<br><input type='submit' value='&nbsp;Done&nbsp;' name='go'"
                    ." onclick='window.close();' class='formbutton'>"
                    ."<script type='text/javascript'>document.form.go.focus();</script>\n";
                break;

            case "parse_log":
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
                    ."    <th class='DT' title='Daytime Logging - 10am to 2pm local time for listener'>DT<br />"
                    ."<sup>(*)</sup></th>\n"
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

                foreach ($lines as $this->line) {
                    $line= $this->line;
                    $YYYYMMDD = $this->extractDate();
                    $this->extractTime();
                    $this->extractKhz();
                    $ID =       $this->extractID();
                    $sec =      $this->extractSec();
                    $fmt =      $this->extractFmt();
                    // The following parameters are only used for simplifying adding of new signals"
                    // if the input format happens to include them:
                    $GSQ =      $this->extractGSQ();
                    $QTH =      $this->extractQTH();
                    $ITU =      $this->extractITU();
                    $SP =       $this->extractSP();
                    $PWR =      $this->extractPWR();

                    $YYYY =     substr($YYYYMMDD, 0, 4);
                    $MM =       substr($YYYYMMDD, 4, 2);
                    $DD =       substr($YYYYMMDD, 6, 2);

                    $LSB =    "";
                    $USB =    "";
                    $LSB_approx =    "";
                    $USB_approx =    "";
                    if (isset($this->tokens["LSB"])) {
                        $LSB =        trim(substr($line, $this->tokens["LSB"][0], $this->tokens["LSB"][1]));
                        if (substr($LSB, 0, 1)=="~") {
                            $LSB =    substr($LSB, 1);
                            $LSB_approx =    "~";
                        }
                        if ($LSB=="---") {
                            // Andy Robins logs use --- as blank
                            $LSB = "";
                        }
                    }
                    if (isset($this->tokens["~LSB"])) {
                        $LSB =        trim(substr($line, $this->tokens["~LSB"][0], $this->tokens["~LSB"][1]));
                        $LSB_approx =    "~";
                    }

                    if (isset($this->tokens["USB"])) {
                        $USB =        trim(substr($line, $this->tokens["USB"][0], $this->tokens["USB"][1]));
                        if (substr($USB, 0, 1)=="~") {
                            $USB =    substr($USB, 1);
                            $USB_approx =    "~";
                        }
                        if ($USB=="---") {
                            $USB = "";
                        }
                    }
                    if (isset($this->tokens["~USB"])) {
                        $USB =        trim(substr($line, $this->tokens["~USB"][0], $this->tokens["~USB"][1]));
                        $USB_approx =    "~";
                    }

                    if (isset($this->tokens["+SB-"])) {
                        $sb =    str_replace(
                            "–",
                            "-",
                            trim(substr($line, $this->tokens["+SB-"][0], $this->tokens["+SB-"][1]))
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
                            trim(substr($line, $this->tokens["+~SB-"][0], $this->tokens["+~SB-"][1]))
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
                                trim(substr($line, $this->tokens["+K-"][0], $this->tokens["+K-"][1]))
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
                        $ABS =    trim(substr($line, $this->tokens["ABS"][0], $this->tokens["ABS"][1]));
                        $ABS_arr =    explode(" ", $ABS);
                        for ($j=0; $j<count($ABS_arr); $j++) {
                            //              print "ABS=$ABS, KHZ=$this->KHZ";
                            $ABS = (double)trim($ABS_arr[$j]);
                            if ($ABS) {
                                if ($ABS>(float)$this->KHZ) {
                                    $USB = round((1000*($ABS-$this->KHZ)));
                                } else {
                                    $LSB = round((1000*($this->KHZ-$ABS)));
                                }
                            }
                        }
                    }

                    if (isset($this->tokens["~ABS"])) {
                        $ABS =    trim(substr($line, $this->tokens["~ABS"][0], $this->tokens["~ABS"][1]));
                        $ABS_arr =    explode(" ", $ABS);
                        for ($j=0; $j<count($ABS_arr); $j++) {
                            $ABS = (double)trim($ABS_arr[$j]);
                            if ($ABS) {
                                if ($ABS>(float)$this->KHZ) {
                                    $USB = round((1000*($ABS-$this->KHZ)));
                                    $USB_approx = "~";
                                } else {
                                    $LSB = round((1000*($this->KHZ-$ABS)));
                                    $LSB_approx = "~";
                                }
                            }
                        }
                    }

                    if ($ID && $YYYYMMDD) {
                        $sta_sel =    "";
                        if ($ID && $YYYYMMDD) {
                            $swing = ($this->KHZ>1740 ? swing_LF : swing_HF);
                            $sql =
                                 "SELECT\n"
                                ."  *\n"
                                ."FROM\n"
                                ."  `signals`\n"
                                ."WHERE\n"
                                .($this->KHZ ?
                                    "  `khz` >= $this->KHZ-".$swing." AND `khz` <= $this->KHZ+".$swing." AND\n"
                                 :
                                    ""
                                 )
                                ." `call` = \"$ID\"";
                            $result = @\Rxx\Database::query($sql);
                            if (\Rxx\Database::getError()) {
                                $this->html.= "Problem looking up station - frequency was $this->KHZ";
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
                                    $dx = \Rxx\Rxx::get_dx(
                                        $this->listener->record['lat'],
                                        $this->listener->record['lon'],
                                        $row["lat"],
                                        $row["lon"]
                                    );
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
                                        ."<a style='font-family:monospace'"
                                        ." href='javascript:signal_info(\"".$row["ID"]."\")'>$ID</a>"
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
                                             " bgcolor='#FFE7B9'"
                                            ." title='Please provide a value for QTH if you have one'"
                                        )
                                        .">"
                                        .$row['QTH']
                                        ."</td>\n"
                                        ."  <td class='DT center'>"
                                        .($this->listener->isDaytime($this->hhmm) ? 'Y' : '')
                                        ."</td>\n"
                                        ."  <td class='Km num'>"
                                        .($dx[1]!=='' ? number_format($dx[1]) : '')
                                        ."</th>\n"
                                        ."  <td class='Mi num'>"
                                        .($dx[0]!=='' ? number_format($dx[0]) : '')
                                        ."</th>\n"
                                        ."  <td class='HeardIn'>"
                                        .(strpos(
                                            $row['heard_in'],
                                            ($this->listener->record['SP'] ?
                                                $this->listener->record['SP']
                                             :
                                                $this->listener->record['ITU']
                                            )
                                        )===false ?
                                           "<font color='#008000'><b>".$row['heard_in']."</b></font>"
                                          :
                                            \Rxx\Rxx::highlight(
                                                $row['heard_in'],
                                                ($this->listener->record['SP'] ?
                                                    $this->listener->record['SP']
                                                 :
                                                    $this->listener->record['ITU']
                                                )
                                            )
                                        )
                                        ."</td>\n";
                                } else {
                                    $this->html.=
                                         "<tr bgcolor='#ffe0a0'>\n"
                                        ."  <td colspan='10' class='Combined'>"
                                        ."<select name='ID[]' class='formfixed'"
                                        ." style='width:844px;overflow:hidden;text-overflow:ellipsis'>\n";
                                    $defaultChosen =    false;
                                    $selected =         false;
                                    for ($j=0; $j<\Rxx\Database::numRows($result); $j++) {
                                        $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                                        $dx =   \Rxx\Rxx::get_dx(
                                            $this->listener->record['lat'],
                                            $this->listener->record['lat'],
                                            $row["lat"],
                                            $row["lon"]
                                        );
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
                                            .($this->listener->isDaytime($this->hhmm) ? 'Y' : ' ')." | "
                                            .($dx[1]!=='' ? number_format($dx[1])."km" : "  ")." | "
                                            .($dx[0]!=='' ? number_format($dx[0])."mi" : "  ")." | "
                                            .$row["heard_in"];
                                        $label =
                                             \Rxx\Rxx::pad_nbsp((float)$row["khz"], 5)."|"
                                            .\Rxx\Rxx::pad_nbsp($ID, 5)."|"
                                            .$row["ITU"]." |"
                                            .($row["SP"] ? $row["SP"] : "&nbsp;&nbsp;")."&nbsp;|"
                                            .\Rxx\Rxx::pad_nbsp($row["GSQ"], 6)."|"
                                            .(strlen($row["QTH"])<25 ?
                                                \Rxx\Rxx::pad_nbsp($row["QTH"], 25)
                                             :
                                                \Rxx\Rxx::pad_nbsp(substr($row["QTH"], 0, 22).'...', 25)
                                            )
                                            ."|"
                                            .($this->listener->isDaytime($this->hhmm) ? ' Y ' : '&nbsp;&nbsp;&nbsp;')."|"

                                            .\Rxx\Rxx::lead_nbsp($dx[1]!=='' ? number_format($dx[1]) : "", 6)."|"
                                            .\Rxx\Rxx::lead_nbsp($dx[0]!=='' ? number_format($dx[0]) : "", 6)."|"
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
                                     "  <td align='center'>"
                                    ."<input type='hidden' name='YYYYMMDD[]' value='$YYYYMMDD'>";

                                if (strlen($YYYYMMDD)!=8) {
                                    $date_fail = true;
                                    $this->html.=
                                        "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                } elseif ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                                    $date_fail = true;
                                    $this->html.=
                                        "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                } else {
                                    $this->html.=
                                        ($YYYY<2005 ? "<font color='#FF8C00'><b>$YYYY</b></font>" : "$YYYY");
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
                                    ."  <td align='center'><input type='hidden' name='hhmm[]' value='$this->hhmm'>";
                                if ((strlen($this->hhmm)!=0 && strlen($this->hhmm)!=4) ||
                                    substr($this->hhmm, 0, 2)>23 || substr($this->hhmm, 2, 2)>59
                                ) {
                                    $date_fail = true;
                                    $this->html.=    "<font color='red'><b><strike>$this->hhmm</strike></b></font>";
                                } else {
                                    $this->html.=    $this->hhmm;
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
                                    $ll = \Rxx\Rxx::GSQ_deg($GSQ);
                                    $dx = \Rxx\Rxx::get_dx(
                                        $this->listener->record['lat'],
                                        $this->listener->record['lat'],
                                        $ll["lat"],
                                        $ll["lon"]
                                    );
                                }
                                $this->html.=
                                     "<tr bgcolor='#ffd0d0' title='signal not listed in database'>\n"
                                    ."  <td>"
                                    .(((float)$this->KHZ<198 || (float)$this->KHZ > 530) ?
                                        "<font color='#FF8C00'><b>".(float)$this->KHZ."</b></font>"
                                     :
                                        (float)$this->KHZ
                                    )
                                    ."</td>\n"
                                    ."  <td class='ID'>$ID</td>\n"
                                    ."  <td class='ITU'>$ITU</td>\n"
                                    ."  <td class='SP'>$SP</td>\n"
                                    ."  <td class='GSQ'>$GSQ</td>\n"
                                    ."  <td class='QTH'>$QTH</td>\n"
                                    ."  <td class='DT center'>"
                                    .($this->listener->isDaytime($this->hhmm) ? 'Y' : '')
                                    ."</td>\n"
                                    ."  <td class='Km num'>".($dx[1] ? number_format($dx[1]) : '')."</th>\n"
                                    ."  <td class='Mi num'>".($dx[0] ? number_format($dx[0]) : '')."</th>\n"
                                    ."  <td>&nbsp;</td>\n"
                                    ."  <td align='center'>";
                                if (strlen($YYYYMMDD)!=8) {
                                    $date_fail = true;
                                    $this->html.=
                                        "<font color='red'><b><strike>$YYYYMMDD</strike></i></b></font>";
                                } elseif ((int)$YYYYMMDD > (int)gmdate("Ymd")) {
                                    $date_fail = true;
                                    $this->html.=
                                        "<font color='red'><b><strike>$YYYYMMDD</strike></b></font>";
                                } else {
                                    $this->html.=
                                        ($YYYY<2003 ? "<font color='#FF8C00'><b>$YYYY</b></font>" : "$YYYY");
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
                                    ."  <td align='center'>$this->hhmm</td>\n"
                                    ."  <td>$LSB_approx$LSB</td>\n"
                                    ."  <td>$USB_approx$USB</td>\n"
                                    ."  <td><input type='hidden' name='sec[]' value=\"$sec\">$sec</td>\n"
                                    ."  <td><input type='hidden' name='fmt[]' value=\"$fmt\">$fmt</td>\n"
                                    ."  <td><a href='javascript:signal_add("
                                    ."\"$ID\",\"$this->KHZ\",\"$GSQ\",\"$QTH\",\"$SP\",\"$ITU\",\"$PWR\""
                                    .")'><b>Add...</b></a></td>\n"
                                    ."</tr>";
                                $unresolved_signals[] =    trim($line);
                            }
                        }
                    }
                }
                if (!count($unresolved_signals) && !$date_fail  && $total_loggings>0) {
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
                        ."<input type='button' value='Serious errors found - Go Back...' id='btn_go'"
                        ." class='formbutton' name='go'"
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
                        .str_repeat('-', 2+strlen($log_format))."\n"
                        ." ".$log_format."\n"
                        .str_repeat('-', 2+strlen($log_format))."\n"
                        ." ".implode("\n ", $unresolved_signals)
                        ."</textarea>";
                } else {
                    $this->html.=
                         "<span class='p'><small><b>"
                        ."Total Loggings in this report: $total_loggings"
                        ."</b></small></span>";
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
                ."    <td>"
                ."<input name='log_format' class='fixed_heading' size='105' style='width:1040px"
                .($log_format_errors ? ';background:#804040' : '')
                ."' value=\"$log_format\">\n"
                ."<input class='formbutton' name='save' type='button' value='Save' onclick='"
                ."this.disabled=true;document.form.go.disabled=true;document.form.conv.disabled=true;"
                ."document.form.submode.value=\"save_format\";document.form.submit()'>"
                ."  </tr>\n"
                ."  <tr class='rownormal'>\n"
                ."    <td><textarea rows='"
                .($log_format_errors ? 19 : 30)
                ."' cols='110' class='fixed' style='width:1100px' name='log_entries'"
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
                        ."<input type='text' name='log_dd' size='2' maxlength='2' class='formfield'"
                        ." value='".\Rxx\Rxx::get_var('log_dd')."' />\n";
                }
                if (!$this->logHas['MM']) {
                    $this->html.=
                         "Month "
                        ."<input type='text' name='log_mm' size='2' maxlength='2' class='formfield'"
                        ." value='".\Rxx\Rxx::get_var('log_mm')."' />\n";
                }
                if (!$this->logHas['YYYY']) {
                    $this->html.=
                         "Year "
                        ."<input type='text' name='log_yyyy' size='4' maxlength='4' class='formfield'"
                        ." value='".\Rxx\Rxx::get_var('log_yyyy')."' />\n";
                }
                $now =        time();
                $now_DD =     gmdate("d", $now);
                $now_MM =     gmdate("m", $now);
                $now_YYYY =   gmdate("Y", $now);

                $this->html.=
                     "<input type='button' value='&lt;-- Current' class='formButton' onclick=\""
                    .(!$this->logHas['DD'] ?
                        "document.form.log_dd.value='$now_DD';"
                     :
                        ""
                    )
                    .(!$this->logHas['MM'] ?
                        "document.form.log_mm.value='$now_MM';"
                     :
                        ""
                    )
                    .(!$this->logHas['YYYY'] ?
                        "document.form.log_yyyy.value='$now_YYYY';"
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

    private function drawListenerDetails()
    {
        $this->html.=
             "<table cellpadding='2' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
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
    }

    private function drawStats()
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

    private function extractDate()
    {
        static $initialised = false;
        $specified_YYYY =    \Rxx\Rxx::YY_to_YYYY(\Rxx\Rxx::get_var('log_yyyy'));
        $specified_MM =      \Rxx\Rxx::M_to_MM(\Rxx\Rxx::get_var('log_mm'));
        $specified_DD =      \Rxx\Rxx::D_to_DD(\Rxx\Rxx::get_var('log_dd'));
        if (!$initialised) {
            $this->initialiseDateParser();
            $initialised = true;
        }
        if (function_exists("parseDate")) {
            $YYYYMMDD = parseDate($this->tokens, $this->line, $specified_YYYY, $specified_MM, $specified_DD);
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
                    \Rxx\Rxx::D_to_DD(trim(substr($this->line, $this->tokens["D"][0], 2)));
            }
            if (isset($this->tokens["DD"])) {
                $DD =
                    trim(substr($this->line, $this->tokens["DD"][0], $this->tokens["DD"][1]));
            }
            if (isset($this->tokens["M"])) {
                // DD shown in log
                $MM =
                    \Rxx\Rxx::M_to_MM(trim(substr($this->line, $this->tokens["M"][0], $this->tokens["M"][1])));
            }
            if (isset($this->tokens["MM"])) {
                // DD shown in log
                $MM =
                    trim(substr($this->line, $this->tokens["MM"][0], $this->tokens["MM"][1]));
            }
            if (isset($this->tokens["MMM"])) {
                // DD shown in log
                $MM =
                    \Rxx\Rxx::MMM_to_MM(trim(substr($this->line, $this->tokens["MMM"][0], $this->tokens["MMM"][1])));
            }
            if (isset($this->tokens["YY"])) {
                // YY shown in log
                $YYYY =
                    \Rxx\Rxx::YY_to_YYYY(trim(substr($this->line, $this->tokens["YY"][0], $this->tokens["YY"][1])));
            }
            if (isset($this->tokens["YYYY"])) {
                // DD shown in log
                $YYYY =
                    trim(substr($this->line, $this->tokens["YYYY"][0], $this->tokens["YYYY"][1]));
            }
        }
        if (!isset($YYYY)) {
            $YYYY = $specified_YYYY;
        }
        if (!isset($MM)) {
            $MM = $specified_MM;
        }
        if (!isset($DD)) {
            $DD = $specified_DD;
        }
        return $YYYY.$MM.$DD;
    }

    private function extractID()
    {
        return strtoUpper(trim(substr($this->line, $this->tokens["ID"][0], $this->tokens["ID"][1])));
    }

    private function extractKhz()
    {
        if ((float)isset($this->tokens["KHZ"])) {
            $this->KHZ =
                str_replace(",", ".", trim(substr($this->line, $this->tokens["KHZ"][0], $this->tokens["KHZ"][1])));
        }
    }

    private function extractSec()
    {
        return (isset($this->tokens["sec"]) ?
            htmlentities(trim(substr($this->line, $this->tokens["sec"][0], $this->tokens["sec"][1])))
         :
            ""
        );
    }

    private function extractFmt()
    {
        return (isset($this->tokens["fmt"]) ?
            htmlentities(trim(substr($this->line, $this->tokens["fmt"][0], $this->tokens["fmt"][1])))
         :
            ""
        );
    }

    private function extractGSQ()
    {
        return (isset($this->tokens["GSQ"]) ?
            trim(substr($this->line, $this->tokens["GSQ"][0], $this->tokens["GSQ"][1]))
         :
            ""
        );
    }

    private function extractITU()
    {
        return (isset($this->tokens["ITU"]) ?
            trim(substr($this->line, $this->tokens["ITU"][0], $this->tokens["ITU"][1]))
         :
            ""
        );
    }

    private function extractSP()
    {
        return (isset($this->tokens["SP"]) ?
            trim(substr($this->line, $this->tokens["SP"][0], $this->tokens["SP"][1]))
         :
            ""
        );
    }

    private function extractPWR()
    {
        return (isset($this->tokens["PWR"]) ?
            trim(substr($this->line, $this->tokens["PWR"][0], $this->tokens["PWR"][1]))
         :
            ""
        );
    }

    private function extractQTH()
    {
        return (isset($this->tokens["QTH"]) ?
            trim(substr($this->line, $this->tokens["QTH"][0], $this->tokens["QTH"][1]))
         :
            ""
        );
    }

    private function extractTime()
    {
        $this->hhmm =        "";
        if (isset($this->tokens["hh:mm"])) {
            $hhmm_arr = explode(":", trim(substr($this->line, $this->tokens["hh:mm"][0], 6)));
            if (isset($hhmm_arr[1])) {
                $this->hhmm =
                    (strlen($hhmm_arr[0])==1 ? "0" : "").$hhmm_arr[0].$hhmm_arr[1];
            }
        }
        if (isset($this->tokens["hhmm"])) {
            $this->hhmm =
                substr(trim(substr($this->line, $this->tokens["hhmm"][0], $this->tokens["hhmm"][1])), 0, 4);
        }
        if (!is_numeric($this->hhmm)) {
            $this->hhmm =    "";
        }
    }

    private function formatUpdate()
    {
        global $log_format;
        $log_format = \Rxx\Rxx::get_var('log_format');
        $this->listener->updateLogFormat($log_format);
        $this->listener->load();
    }

    private function drawInputScreen()
    {
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
    }

    private function logSubmit()
    {
        global $log_format, $log_entries;
        global $fmt, $sec, $ID, $LSB, $LSB_approx, $USB, $USB_approx, $YYYYMMDD, $hhmm, $daytime;

        set_time_limit(600);    // Extend maximum execution time to 10 mins
        if ($this->debug) {
            $this->html.=
                 "1: Logged at least once from this state<br>"
                ."2: No listener yet listed in this state<br>"
                ."3: Listener listed, but this is not a duplicate logging so add a new one<br>"
                ."4: signal never logged in this state<br>";
        }
        for ($i=0; $i<count($ID); $i++) {
            $ObjSignal =   new \Rxx\Signal($ID[$i]);
            if ($this->debug) {
                $this->html.=    "<li>ID=".$ID[$i]." ";
            }
            if ($row = \Rxx\Log::checkIfDuplicate(
                $ID[$i],
                $this->listener->getID(),
                $YYYYMMDD[$i],
                $hhmm[$i]
            )) {
                $this->stats['exact_duplicate']++;
                continue;
            }
            $daytime =
                ($this->listener->isDaytime($hhmm[$i]) ? 1 : 0);
            $heardIn =
                ($this->listener->record['SP'] ? $this->listener->record['SP'] : $this->listener->record['ITU']);
            $data = array(
                'signalID' =>   $ID[$i],
                'date' =>       $YYYYMMDD[$i],
                'daytime' =>    $daytime,
                'heard_in' =>   $heardIn,
                'listenerID' => $this->listener->getID(),
                'region' =>     $this->listener->record["region"]
            );
            $dx =       $ObjSignal->getDx($this->listener->record["lat"], $this->listener->record["lon"]);
            $dx_miles = $dx[0];
            $dx_km =    $dx[1];
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
            } else {
                if ($this->debug) {
                    $this->html.=    "4 ";
                }
                $log = new \Rxx\Log;
                $log->insert($data);
                $ObjSignal->setAsHeardInRegion($this->listener->record["region"]);
                $this->stats['first_for_state_or_itu']++;
                $this->stats['first_for_listener']++;
            }
            $ObjSignal->updateHeardInList();
            $logStats =     $ObjSignal->getLogsAndLastHeardDate();
            $logs =         $logStats['logs'];
            $first_heard =  $logStats['first_heard'];
            $last_heard =   $logStats['last_heard'];


            $this->stats['latest_for_signal']++;
            \Rxx\Tools\Signal::signal_update_full(
                $ID[$i],
                $LSB[$i],
                $LSB_approx[$i],
                $USB[$i],
                $USB_approx[$i],
                $sec[$i],
                htmlentities($fmt[$i]),
                $logs,
                $first_heard,
                $last_heard,
                $this->listener->record["region"]
            );
        }
    }

    private function setup()
    {
        $this->loadListener();
    }

    private function initialiseDateParser()
    {
        static $dateParsers = array(
            'DM' =>          '$Y.\Rxx\Rxx::M_to_MM(substr($t,1,1)).\Rxx\Rxx::D_to_DD(substr($t,0,1))',
            'D.M' =>         '$Y.\Rxx\Rxx::M_to_MM(substr($t,2,1)).\Rxx\Rxx::D_to_DD(substr($t,0,1))',
            'DDM' =>         '$Y.\Rxx\Rxx::M_to_MM(substr($t,2,1)).substr($t,0,2)',
            'DD.M' =>        '$Y.\Rxx\Rxx::M_to_MM(substr($t,3,1)).substr($t,0,2)',
            'DMM' =>         '$Y.substr($t,1,2).\Rxx\Rxx::D_to_DD(substr($t,0,1))',
            'D.MM' =>        '$Y.substr($t,2,2).\Rxx\Rxx::D_to_DD(substr($t,0,1))',
            'DDMM' =>        '$Y.substr($t,2,2).substr($t,0,2)',
            'DD.MM' =>       '$Y.substr($t,3,2).substr($t,0,2)',
            'DMMM' =>        '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,1,3)).\Rxx\Rxx::D_to_DD(substr($t,0,1))',
            'D.MMM' =>       '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,2,3)).\Rxx\Rxx::D_to_DD(substr($t,0,1))',
            'DDMMM' =>       '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,2,3)).substr($t,0,2)',
            'DD.MMM' =>      '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,3,3)).substr($t,0,2)',
            'MD' =>          '$Y.\Rxx\Rxx::M_to_MM(substr($t,0,1)).\Rxx\Rxx::D_to_DD(substr($t,1,1))',
            'M.D' =>         '$Y.\Rxx\Rxx::M_to_MM(substr($t,0,1)).\Rxx\Rxx::D_to_DD(substr($t,2,1))',
            'MDD' =>         '$Y.\Rxx\Rxx::M_to_MM(substr($t,0,1)).substr($t,1,2)',
            'M.DD' =>        '$Y.\Rxx\Rxx::M_to_MM(substr($t,0,1)).substr($t,2,2)',
            'MMD' =>         '$Y.substr($t,0,2).\Rxx\Rxx::D_to_DD(substr($t,2,1))',
            'MM.D' =>        '$Y.substr($t,0,2).\Rxx\Rxx::D_to_DD(substr($t,3,1))',
            'MMDD' =>        '$Y.substr($t,0,2).substr($t,2,2)',
            'MM.DD' =>       '$Y.substr($t,0,2).substr($t,3,2)',
            'MMMD' =>        '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).\Rxx\Rxx::D_to_DD(substr($t,3,1))',
            'MMM.D' =>       '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).\Rxx\Rxx::D_to_DD(substr($t,4,1))',
            'MMMDD' =>       '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,3,2)',
            'MMM.DD' =>      '$Y.\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,4,2)',
            'DDMMYY' =>      '\Rxx\Rxx::YY_to_YYYY(substr($t,4,2)).substr($t,2,2).substr($t,0,2)',
            'DD.MM.YY' =>    '\Rxx\Rxx::YY_to_YYYY(substr($t,6,2)).substr($t,3,2).substr($t,0,2)',
            'DDYYMM' =>      '\Rxx\Rxx::YY_to_YYYY(substr($t,2,2)).substr($t,4,2).substr($t,0,2)',
            'DD.YY.MM' =>    '\Rxx\Rxx::YY_to_YYYY(substr($t,3,2)).substr($t,6,2).substr($t,0,2)',
            'MMDDYY' =>      '\Rxx\Rxx::YY_to_YYYY(substr($t,4,2)).substr($t,0,2).substr($t,2,2)',
            'MM.DD.YY' =>    '\Rxx\Rxx::YY_to_YYYY(substr($t,6,2)).substr($t,0,2).substr($t,3,2)',
            'MMYYDD' =>      '\Rxx\Rxx::YY_to_YYYY(substr($t,2,2)).substr($t,0,2).substr($t,4,2)',
            'MM.YY.DD' =>    '\Rxx\Rxx::YY_to_YYYY(substr($t,3,2)).substr($t,0,2).substr($t,6,2)',
            'YYDDMM' =>      '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).substr($t,4,2).substr($t,2,2)',
            'YY.DD.MM' =>    '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).substr($t,6,2).substr($t,3,2)',
            'YYMMDD' =>      '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).substr($t,2,2).substr($t,4,2)',
            'YY.MM.DD' =>    '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).substr($t,3,2).substr($t,6,2)',
            'DDMMMYY' =>     '\Rxx\Rxx::YY_to_YYYY(substr($t,5,2)).\Rxx\Rxx::MMM_to_MM(substr($t,2,3)).substr($t,0,2)',
            'DD.MMM.YY' =>   '\Rxx\Rxx::YY_to_YYYY(substr($t,7,2)).\Rxx\Rxx::MMM_to_MM(substr($t,3,3)).substr($t,0,2)',
            'DDYYMMM' =>     '\Rxx\Rxx::YY_to_YYYY(substr($t,2,2)).\Rxx\Rxx::MMM_to_MM(substr($t,4,3)).substr($t,0,2)',
            'DD.YY.MMM' =>   '\Rxx\Rxx::YY_to_YYYY(substr($t,3,2)).\Rxx\Rxx::MMM_to_MM(substr($t,6,3)).substr($t,0,2)',
            'MMMDDYY' =>     '\Rxx\Rxx::YY_to_YYYY(substr($t,5,2)).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,3,2)',
            'MMM.DD.YY' =>   '\Rxx\Rxx::YY_to_YYYY(substr($t,7,2)).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,4,2)',
            'MMMYYDD' =>     '\Rxx\Rxx::YY_to_YYYY(substr($t,3,2)).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,5,2)',
            'MMM.YY.DD' =>   '\Rxx\Rxx::YY_to_YYYY(substr($t,4,2)).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,7,2)',
            'YYDDMMM' =>     '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).\Rxx\Rxx::MMM_to_MM(substr($t,4,3)).substr($t,2,2)',
            'YY.DD.MMM' =>   '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).\Rxx\Rxx::MMM_to_MM(substr($t,6,3)).substr($t,3,2)',
            'YYMMMDD' =>     '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).\Rxx\Rxx::MMM_to_MM(substr($t,2,3)).substr($t,5,2)',
            'YY.MMM.DD' =>   '\Rxx\Rxx::YY_to_YYYY(substr($t,0,2)).\Rxx\Rxx::MMM_to_MM(substr($t,3,3)).substr($t,7,2)',
            'DDMMYYYY' =>    'substr($t,4,4).substr($t,2,2).substr($t,0,2)',
            'DD.MM.YYYY' =>  'substr($t,6,4).substr($t,3,2).substr($t,0,2)',
            'DDYYYYMM' =>    'substr($t,2,4).substr($t,6,2).substr($t,0,2)',
            'DD.YYYY.MM' =>  'substr($t,3,4).substr($t,8,2).substr($t,0,2)',
            'MMDDYYYY' =>    'substr($t,4,4).substr($t,0,2).substr($t,2,2)',
            'MM.DD.YYYY' =>  'substr($t,6,4).substr($t,0,2).substr($t,3,2)',
            'MMYYYYDD' =>    'substr($t,2,4).substr($t,0,2).substr($t,6,2)',
            'MM.YYYY.DD' =>  'substr($t,3,4).substr($t,0,2).substr($t,8,2)',
            'YYYYDDMM' =>    'substr($t,0,4).substr($t,6,2).substr($t,4,2)',
            'YYYY.DD.MM' =>  'substr($t,0,4).substr($t,8,2).substr($t,5,2)',
            'YYYYMMDD' =>    'substr($t,0,4).substr($t,4,2).substr($t,6,2)',
            'YYYY.MM.DD' =>  'substr($t,0,4).substr($t,5,2).substr($t,8,2)',
            'DDMMMYYYY' =>   'substr($t,5,4).\Rxx\Rxx::MMM_to_MM(substr($t,2,3)).substr($t,0,2)',
            'DD.MMM.YYYY' => 'substr($t,7,4).\Rxx\Rxx::MMM_to_MM(substr($t,3,3)).substr($t,0,2)',
            'DDYYYYMMM' =>   'substr($t,2,4).\Rxx\Rxx::MMM_to_MM(substr($t,6,3)).substr($t,0,2)',
            'DD.YYYY.MMM' => 'substr($t,3,4).\Rxx\Rxx::MMM_to_MM(substr($t,8,3)).substr($t,0,2)',
            'MMMDDYYYY' =>   'substr($t,5,4).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,3,2)',
            'MMM.DD.YYYY' => 'substr($t,7,4).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,4,2)',
            'MMMYYYYDD' =>   'substr($t,3,4).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,7,2)',
            'MMM.YYYY.DD' => 'substr($t,4,4).\Rxx\Rxx::MMM_to_MM(substr($t,0,3)).substr($t,9,2)',
            'YYYYDDMMM' =>   'substr($t,0,4).\Rxx\Rxx::MMM_to_MM(substr($t,6,3)).substr($t,4,2)',
            'YYYY.DD.MMM' => 'substr($t,0,4).\Rxx\Rxx::MMM_to_MM(substr($t,8,3)).substr($t,5,2)',
            'YYYYMMMDD' =>   'substr($t,0,4).\Rxx\Rxx::MMM_to_MM(substr($t,4,3)).substr($t,7,2)',
            'YYYY.MMM.DD' => 'substr($t,0,4).\Rxx\Rxx::MMM_to_MM(substr($t,5,3)).substr($t,9,2)'
        );
        foreach ($dateParsers as $key => $code) {
            if (isset($this->tokens[$key])) {
                eval(
                    "function parseDate(\$a, \$b, \$Y, \$M, \$D){\n"
                    ."    \$t = trim(substr(\$b, \$a['$key'][0]));\n"
                    ."     return $code;\n"
                    ."}\n"
                );
            }
        }
    }

    private function loadListener()
    {
        global $log_format;
        $this->listener = new \Rxx\Listener(\Rxx\Rxx::get_var('listenerID'));
        if ($this->listener->getID()) {
            $this->listener->load();
            $log_format =    $this->listener->record["log_format"];
        }
    }
}
