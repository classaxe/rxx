<?php
namespace Rxx\Managers;

use Rxx\Tools\Tools;

class Admin
{
    public function draw()
    {
        global $mode, $submode;
        $url = system_URL."/".$mode;
        $out =
             "<form name='form' method='post' enctype='multipart/form-data' action='".system_URL."'>\n"
            ."<input type='hidden' name='mode' value='$mode'>\n"
            ."<input type='hidden' name='submode' value=''>\n"
            ."<input type='hidden' name='MAX_FILE_SIZE' value='800000'>\n"
            ."<h2>Administrator Management Tools</h2><br>\n<ol class='p'>\n"

            ."<li><input type='button' value='Go' onclick='this.disabled=1;document.location=\""
            .$url."?submode=admin_signalUpdateFromGSQ\"'>"
            ." <b>Signals: Update Lat and Lon values from GSQ</b>"
            ." (use after importing new signals using phpMyAdmin)</li>\n"

            ."<li><input type='button' value='Go' onclick='this.disabled=1;document.location=\""
            .$url."?submode=admin_signalUpdateFromLogs\"'>"
            ." <b>Signals: Update listeners, logs and Heard In from latest log data</b>"
            ." (run if problems are seen)<br><br></li>\n"

            ."<li><input type='button' value='Go' onclick='this.disabled=1;document.location=\""
            .$url."?submode=admin_logsUpdateDX\"'>"
            ." <b>Logs: Recalculate all distances</b>"
            ." (use after adding GSQs for existing logging or function above)</li>\n"
            ."<li><input type='button' value='Go' onclick='this.disabled=1;document.location=\""
            .$url."?submode=admin_setDaytimeLogs\"'>"
            ." <b>Logs: Mark daytime loggings</b> (run periodically)<br><br></li>\n"
            ."<li><input type='button' value='Go' onclick='this.disabled=1;document.location=\""
            .$url."?submode=admin_listenersUpdateLogCount\"'>"
            ." <b>Listeners: Update log counts</b> (run periodically)<br><br></li>\n"
            ."<li><input type='button' value='Go' onclick='this.disabled=1;document.location=\""
            .$url."?submode=admin_importICAO\"'>"
            ." <b>ICAO: Get latest data</b> (run once a month)<br><br></li>\n"
            ."<li><input type='button' value='Go' onclick='document.location=\""
            .system_URL."/db_export\"'>"
            ." <b>System: Export Database</b> (run periodically)</li>\n"
            ."<li><input type='button' value='Go' onclick='this.disabled=1;document.location=\""
            .$url."?submode=admin_systemSendTestEmail&amp;"
            ."sendToEmail=\"+document.getElementById(\"sendToEmail\").value'>"
            ." <b>System: Send Test Email to </b>"
            ."<input type='text' class='formField' id='sendToEmail' name='sendToEmail' value=''><br><br></li>\n"
            ."</ol><br></form>\n";
        switch ($submode) {
            case "admin_logsUpdateDX":
                $out.= $this->logsUpdateDX();
                break;
            case "admin_importICAO":
                $out.= $this->importICAO();
                break;
            case "admin_setDaytimeLogs":
                $out.= $this->setDaytimeLogs();
                break;
            case "admin_signalUpdateFromLogs":
                $out.= $this->signalUpdateFromLogs();
                break;
            case "admin_signalUpdateFromGSQ":
                $out.= $this->signalUpdateFromGSQ();
                break;
            case "admin_listenersUpdateLogCount":
                $out.= $this->listenersUpdateLogCount();
                break;
            case "admin_systemSendTestEmail":
                $out.= $this->systemSendTestEmail();
                break;
        }
        return $out;
    }

    protected function logsUpdateDX()
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $sql =
             "SELECT\n"
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
        //  print("<pre>$sql</pre>"); die;
        $result =    @\Rxx\Database::query($sql);
        $updated =    0;

        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =    \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            if ($row["qth_lat"] && $row["dx_lat"]) {
                $a =    \Rxx\Rxx::get_dx($row["qth_lat"], $row["qth_lon"], $row["dx_lat"], $row["dx_lon"]);
                $sql =
                     "UPDATE\n"
                    ."  `logs`\n"
                    ."SET\n"
                    ."  `dx_miles` = ".$a[0].",\n"
                    ."  `dx_km` = ".$a[1]."\n"
                    ."WHERE\n"
                    ."  `ID` = ".$row["ID"];
                \Rxx\Database::query($sql);
                if (\Rxx\Database::affectedRows()) {
                    $updated++;
                }
            }
        }
        return "<h2>Updating DX values</h2><br><br><p>Done. $updated logs updated</p>";
    }



    protected function signalUpdateFromLogs()
    {
        // 100MB used with specs, 4MB without
        $update_specs = false; // RXX live runs out of memory otherwise
        $signal = new \Rxx\Signal;

        if ($update_specs) {
            $affected = $signal->updateFromLogs(false, true);
        } else {
            $sql = "select ID from signals;";
            $results =    @\Rxx\Database::query($sql);
            $affected = 0;
            foreach ($results as $r) {
                $affected += $signal->updateFromLogs($r['ID'], false);
            }
        }
        $mem = number_format(memory_get_peak_usage());
        return "<h2>Updating Signal Data from latest Logs...</h2><p>Done. $affected signals updated - $mem bytes of memory used.</p>";
    }


    protected function setDaytimeLogs()
    {
        $sql = <<< EOD
            UPDATE
                logs
            INNER JOIN listeners l ON
                logs.listenerID = l.ID
            SET
                daytime = IF(
                    (logs.time + 2400 >= 3400 + (l.timezone * -100)) AND
                    (logs.time + 2400 < 3800 + (l.timezone * -100)),
                    1, 0
                )
EOD;
        \Rxx\Database::query($sql);
        $affected = \Rxx\Database::affectedRows();
        return "Updated ".$affected." logs";
    }


    protected function importICAO()
    {
        global $mode, $submode, $subsubmode, $data;
        $url =    "http://www.rap.ucar.edu/weather/surface/stations.txt";
        $url =    "https://www.aviationweather.gov/docs/metar/stations.txt";
        $out =
             "<form name='form' action='".system_URL."' method='POST'>\n"
            ."<input type='hidden' name='mode' id='mode' value='$mode'>\n"
            ."<input type='hidden' name='submode' id='submode' value='$submode'>\n"
            ."<input type='hidden' name='subsubmode' id='subsubmode' value=''>\n"
            ."<h2>Import ICAO</h2><br><br>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'>"
            ."<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'>"
            ."<a name='dx'></a>ICAO Data"
            ."</th>\n"
            ."        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<small>[ <a href='#top' class='yellow'><b>Top</b></a> ]</small>"
            ."</th>\n"
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n";
        if (\Rxx\Rxx::get_var('subsubmode')==='Update') {
            $data =    explode(chr(13).chr(10), stripslashes($data));
            if ($data[3] == "\n---CONNECTION ERROR---") {
                $num =    0;
            } else {
                $num =    count($data)-7;
                $sql =    "DELETE FROM `icao`";
                \Rxx\Database::query($sql);
                for ($i=5; $i<$num+5; $i++) {
                    $icao_name =    trim(substr($data[$i], 0, 16));
                    $icao_cnt =     trim(substr($data[$i], 20, 2));
                    $icao_sp =      trim(substr($data[$i], 17, 2));
                    $icao_ele =     trim(substr($data[$i], 45, 4));
                    $lat =          (int) trim(substr($data[$i], 29, 2)) + (int) substr($data[$i], 32, 2) /60 ;
                    if (substr($data[$i], 35, 1)=="S") {
                        $lat *=-1;
                    }
                    $lon =          (int) trim(substr($data[$i], 37, 3)) + (int) substr($data[$i], 41, 2) /60 ;
                    if (substr($data[$i], 43, 1)=="W") {
                        $lon *=-1;
                    }
                    $icao_gsq =    \Rxx\Rxx::deg_GSQ($lat, $lon);
                    $sql =
                         "INSERT INTO `icao` SET \n"
                        ."  `name` = \"$icao_name\",\n"
                        ."  `CNT` = \"$icao_cnt\",\n"
                        ."  `elevation` = $icao_ele,\n"
                        ."  `GSQ` = \"$icao_gsq\",\n"
                        ."  `ICAO` = \"".(substr($data[$i], 23, 4))."\",\n"
                        ."  `lat` = $lat,\n"
                        ."  `lon` = $lon,\n"
                        ."  `SP` = \"$icao_sp\"";
                    \Rxx\Database::query($sql);
                    if (\Rxx\Database::getError()) {
                        $out.= "<pre>$data[$i]<br>$sql</pre>";
                    }
                }
                $out.=
                     "  <tr>\n"
                    ."    <td class='downloadTableContent'>Success: Database updated, $num records processed</td>"
                    ."  </tr>\n";
            }
        } else {
            $out.=
                 "  <tr>\n"
                ."    <td class='downloadTableContent'><textarea class='formFixed' cols='55' rows='30' name='data'>"
                ."Data extracted from\n".$url."\n\n";

            if ($my_file = @file($url)) {
                $out.=
                     "STATION          SP CN ICAO  LAT     LONG    ELEV\n"
                    ."--------------------------------------------------\n";
                $num =    0;
                for ($i=0; $i<count($my_file); $i++) {
                    if (substr($my_file[$i], 62, 1)=="X") {
                        $num++;
                        $out.=
                             substr($my_file[$i], 3, 17)
                            .substr($my_file[$i], 0, 3)
                            .substr($my_file[$i], 81, 2)." "
                            .substr($my_file[$i], 20, 5)." "
                            .substr($my_file[$i], 39, 20)
                            ."\n";
                    }
                }
                $out.=
                     "--------------------------------------------------\n"
                    ."Total entries: ".$num
                    ."</textarea></td>\n"
                    ."  </tr>\n"
                    ."  <tr>\n"
                    ."    <td class='downloadTableContent' align='center'>"
                    ."<input type='submit' name='go' value='Update' class='formButton' onclick='"
                    ."document.getElementById(\"subsubmode\").value=\"Update\"'"
                    ."></td>"
                    ."  </tr>\n";
            } else {
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


    protected function signalUpdateFromGSQ()
    {
        $html = "<h2>Updating Signal Locations...</h2><br><br>";
        $updated =    0;
        $sql =
             "SELECT\n"
            ."  `ID`,"
            ."  `call`,"
            ."  `GSQ`,"
            ."  `khz`"
            ."FROM\n"
            ."  `signals`";
        $result =    @\Rxx\Database::query($sql);

        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =      \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $ID =       $row["ID"];
            $call =     $row['call'];
            $GSQ =      $row["GSQ"];
            $khz =      $row['khz'];
            if ($GSQ) {
                $a =     \Rxx\Rxx::GSQ_deg($GSQ);
                if ($a===false) {
                    $html.= "<b>Error:</b> $call on ".(0+$khz)." has invalid GSQ $GSQ.<br />";
                } else {
                    $lat =    $a["lat"];
                    $lon =    $a["lon"];
                    $sql =
                        "UPDATE\n"
                        ."  `signals`\n"
                        ."SET\n"
                        ."  `lat` = $lat,\n"
                        ."  `lon` = $lon\n"
                        ."WHERE\n"
                        ."  `ID` = $ID";
                    \Rxx\Database::query($sql);
                    if (\Rxx\Database::affectedRows()) {
                        $updated++;
                    }
                }
            }
        }
        $html.= "<p>Done. $updated signals updated.</p>";
        return $html;
    }


    protected function listenersUpdateLogCount()
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins

        $updated =    0;

        $sql =        "SELECT `ID` FROM `listeners`";
        $result2=          \Rxx\Database::query($sql);
        for ($i=0; $i<\Rxx\Database::numRows($result2); $i++) {
            $row2 =        \Rxx\Database::fetchArray($result2);
            $listenerID =    $row2["ID"];
            if (\Rxx\Rxx::update_listener_log_count($listenerID)) {
                $updated++;
            }
        }
        return "<h2>Updating Log Counts...</h2><br><br><p>Done. $updated Listeners updated.</p>";
    }



    protected function systemSendTestEmail()
    {
        global $sendToEmail;
        $mail = new \Rxx\PHPMailer();
        $mail->PluginDir =      "../";
        $mail->IsHtml(true);
        $mail->Mailer =         "smtp";

        $mail->From =       "martin@classaxe.com";
        $mail->FromName =   "RNA / REU / RWW system";
        $mail->Host =       SMTP_HOST;
        $mail->Mailer =     "smtp";

        $mail->AddAddress($sendToEmail, "Test Email");
        $mail->Subject = "RNA / REU / RWW System";
        $mail->Body    = "Test Message to ".$sendToEmail." via ".SMTP_HOST." from ".getenv("SERVER_NAME");
        if ($mail->Send()) {
            return "<h2>Sent test email to ".$sendToEmail." via ".SMTP_HOST."</h2>";
        }
        return
            "<h2>Test email to ".$sendToEmail." via ".SMTP_HOST." failed:</h2>"
           ."<p>".$mail->ErrorInfo;
        
    }
}
