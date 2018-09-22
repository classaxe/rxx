<?php
namespace Rxx;

class SignalSeekList
{
    public $html = '';
    public $head = '';

    protected $type_NDB;
    protected $type_TIME;
    protected $type_DGPS;
    protected $type_DSC;
    protected $type_NAVTEX;
    protected $type_HAMBCN;
    protected $type_OTHER;
    protected $type_ALL;

    private $filter_locator;
    private $filter_continent;

    public function draw()
    {
        global $mode, $paper, $createFor, $region, $filter_active, $filter_last_date_1, $filter_last_date_2;
        global $filter_continent, $filter_dx_gsq, $filter_dx_max, $filter_dx_min, $filter_dx_units;
        global $filter_heard_in, $filter_id, $filter_system, $filter_khz_1, $filter_khz_2, $filter_channels;
        global $filter_sp, $filter_itu, $filter_listener, $sortBy, $filter_heard_in_mod, $limit, $offset;
        global $type_NDB, $type_TIME, $type_DSC, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
        global $filter_sp_itu_clause, $filter_locator;
        
        $this->setup();

// No array used as page size cause memory fault on linux box.   Just print.

//  $filter_sp = "ON";
//print "active=$filter_active";

        // If there's no valid listener data, blank it all out
        if (!($filter_listener && is_array($filter_listener) && count($filter_listener) && $filter_listener[0])) {
            $filter_listener =    false;
        }

        if ($filter_heard_in=="(All States and Countries)") {
            $filter_heard_in="";
        }


        if ($filter_system=="") {
            switch (system) {
                case "RNA":    $filter_system=1;
                    break;
                case "REU":    $filter_system=2;
                    break;
                case "RWW":    $filter_system=3;
                    break;
            }
        }

        switch ($filter_system) {
            case "1":
                $filter_system_SQL =        "(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
                $filter_log_SQL =           "(`region` = 'na' OR `region` = 'ca' OR `heard_in` = 'hi')";
                $filter_listener_SQL =      "(`region` = 'na' OR `region` = 'ca' OR `SP` = 'hi')";
                break;
            case "2":
                $filter_system_SQL =        "(`heard_in_eu` = 1)";
                $filter_log_SQL =           "(`region` = 'eu')";
                $filter_listener_SQL =      "(`region` = 'eu')";
                break;
            case "3":
                if ($region!="") {
                    $filter_system_SQL =    "(`heard_in_$region`=1)";
                    $filter_listener_SQL =  "(`region` = '$region')";
                    $filter_log_SQL =       "(`region` = '$region')";
                } else {
                    $filter_system_SQL =    "(1)";
                    $filter_listener_SQL =  "(1)";
                    $filter_log_SQL =       "(1)";
                }
                break;
        }
        $sql =    "SELECT\n"
            ."  DATE_FORMAT(MAX(`date`),'%e %b %Y') AS `last`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  $filter_log_SQL";

//  print("<pre>$sql</pre>");
        $result =     @\Rxx\Database::query($sql);
        $row =    \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
        $last =    $row["last"];


        $filter_size =    300;
        $line_height =    14;
        $heading_height =    20;
        $heading_gap =    11;

        if (!$paper) {
            $paper =    "ltr";
        }
        switch ($paper) {
            case "ltr":
                $page_len =    710; // px length
                $page_cols =    5;
                break;
            case "lgl":
                $page_len =    906; // px length
                $page_cols =    5;
                break;
            case "a4":
                $page_len =    755; // px length
                $page_cols =    4;
                break;
            case "ltr_l":
                $page_len =    490; // px length
                $page_cols =    6;
                break;
            case "lgl_l":
                $page_len =    490; // px length
                $page_cols =    9;
                break;
            case "a4_l":
                $page_len =    470; // px length
                $page_cols =    7;
                break;
        }

        $filter_type =    array();
        if (!($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
            switch (system) {
                case "RNA":    $type_NDB =    1;
                    break;
                case "REU":    $type_NDB =    1;
                    break;
                case "RWW":    $type_NDB =    1;
                    break;
            }

        }

        if ($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
            if ($type_NDB) {
                $filter_type[] =     "`type` = ".NDB;
            }
            if ($type_DGPS) {
                $filter_type[] =     "`type` = ".DGPS;
            }
            if ($type_DSC) {
                $filter_type[] =     "`type` = ".DSC;
            }
            if ($type_TIME) {
                $filter_type[] =     "`type` = ".TIME;
            }
            if ($type_HAMBCN) {
                $filter_type[] =     "`type` = ".HAMBCN;
            }
            if ($type_NAVTEX) {
                $filter_type[] =     "`type` = ".NAVTEX;
            }
            if ($type_OTHER) {
                $filter_type[] =     "`type` = ".OTHER;
            }
        }
        $filter_type =    (count($filter_type) ? "(".implode($filter_type, " OR ").")" : "");

        if ($filter_heard_in) {
            $tmp =        explode(" ", strToUpper($filter_heard_in));
            sort($tmp);
            $filter_heard_in =    implode(" ", $tmp);
        }

        // Filter on 'Heard in':
        $filter_heard_in_SQL =    explode(" ", strToUpper($filter_heard_in));
        if ($filter_heard_in_mod=="all") {
            $filter_heard_in_SQL =    "`signals`.`heard_in` LIKE '%".implode($filter_heard_in_SQL, "%' AND `signals`.`heard_in` LIKE '%")."%'";
        } else {
            $filter_heard_in_SQL =    "`logs`.`heard_in` = '".implode($filter_heard_in_SQL, "' OR `logs`.`heard_in` = '")."'";
        }
        // Filter on Frequencies:
        if ($filter_khz_1 || $filter_khz_2) {
            if ($filter_khz_1 == "") {
                $filter_khz_1 = 0;
            }
            if ($filter_khz_2 == "") {
                $filter_khz_2 = 100000;
            }
            $filter_khz_1 =    (float)$filter_khz_1;
            $filter_khz_2 =    (float)$filter_khz_2;
        }
        $filter_sp =        strToUpper($filter_sp);
        $filter_itu =        strToUpper($filter_itu);

        if ($filter_sp) {
            $filter_sp_SQL =    explode(" ", str_replace('*', '%',$filter_sp));
            $filter_sp_SQL =    "`signals`.`SP` LIKE '".implode($filter_sp_SQL, "' OR `signals`.`SP` LIKE '")."'";
        }

        if ($filter_itu) {
            $filter_itu_SQL =    explode(" ", str_replace('*', '%',$filter_itu));
            $filter_itu_SQL =    "`signals`.`ITU` LIKE '".implode($filter_itu_SQL, "' OR `signals`.`ITU` LIKE '")."'";
        }

        if ($filter_locator) {
            $filter_locator_SQL = explode(" ", str_replace('*', '%', $filter_locator));
            $filter_locator_SQL =
                "    (`signals`.`gsq` LIKE '"
                .implode($filter_locator_SQL, "%' OR `signals`.`gsq` LIKE '")
                ."%')";
        }
        // Filter on Date Last Logged:
        if ($filter_last_date_1 || $filter_last_date_2) {
            if ($filter_last_date_1 == "") {
                $filter_last_date_1 = "1900-01-01";
            }
            if ($filter_last_date_2 == "") {
                $filter_last_date_2 = "2030-01-01";
            }
        }

        if (!isset($filter_dx_units)) {
            $filter_dx_units = "km";
        }

        $filter_by_range = false;
        if ($filter_dx_gsq && ($filter_dx_min || $filter_dx_max)) {
            $filter_dx_gsq = strtoUpper(substr($filter_dx_gsq, 0, 4)).strtoLower(substr($filter_dx_gsq, 4, 2));
            $filter_by_range =    true;
            $a =         \Rxx\Rxx::GSQ_deg($filter_dx_gsq);
            $filter_dx_lat =    $a["lat"];
            $filter_dx_lon =    $a["lon"];
        }

        if (!$filter_by_range && ($sortBy == "range" || $sortBy == "range_d")) {
            $sortBy =    "khz";
        }


        $sql =
            "SELECT DISTINCT\n"
            ."  `signals`.`ID`,\n"
            ."  `signals`.`active`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`type`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`\n"
            .($filter_heard_in || $filter_listener ?
                "FROM\n"
                ."  `signals`,\n"
                ."  `logs`\n"
                ."WHERE\n"
                ."  (`signals`.`ID` = `logs`.`signalID`) AND\n"
                .($filter_heard_in ?   "  (".$filter_heard_in_SQL.") AND\n" : "")
                .($filter_listener ?     "  (`logs`.`listenerID`=".implode($filter_listener, " OR `logs`.`listenerID`=").") AND\n" : "")
                :
                "FROM\n"
                ."  `signals`\n"
                ."WHERE\n"
            )
            ."  ".$filter_system_SQL." AND\n"
            .($filter_active ?                      "  (`active` = 1) AND\n" : "")
            .($filter_by_range && $filter_dx_min ?  "  round(degrees(acos(sin(radians(".$filter_dx_lat.")) * sin(radians(signals.lat)) + cos(radians(".$filter_dx_lat.")) * cos(radians(signals.lat)) * cos(radians(".$filter_dx_lon." - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) > $filter_dx_min AND\n" : "")
            .($filter_by_range && $filter_dx_max ?  "  round(degrees(acos(sin(radians(".$filter_dx_lat.")) * sin(radians(signals.lat)) + cos(radians(".$filter_dx_lat.")) * cos(radians(signals.lat)) * cos(radians(".$filter_dx_lon." - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) < $filter_dx_max AND\n" : "")
            .($filter_last_date_2 ?                 "  (`last_heard` >= \"".$filter_last_date_1."\" AND `last_heard` <= \"".$filter_last_date_2."\") AND\n" : "")
            .($filter_id ?                          "  (`signals`.`call` LIKE \"%".$filter_id."%\") AND\n" : "")
            .($filter_khz_2 ?                       "  (`khz` >= ".$filter_khz_1." AND `khz` <= ".$filter_khz_2.") AND\n" : "")
            .($filter_channels==1 ?                 "  MOD((`khz`* 1000),1000) = 0 AND\n" : "")
            .($filter_channels==2 ?                 "  MOD((`khz`* 1000),1000) != 0 AND\n" : "")
            .($filter_type ?                        "  ".$filter_type." AND\n" : "")
            .($filter_locator ?                     "  (".$filter_locator_SQL.") AND\n" : "")

            .($filter_sp || $filter_itu ? " (\n" : "")
            .($filter_sp ?           "  (".$filter_sp_SQL.")" : "")
            .($filter_sp && $filter_itu ?
                ($filter_sp_itu_clause ? $filter_sp_itu_clause : " AND ")
             :
                ""
            )
            .($filter_itu ?           "  (".$filter_itu_SQL.")" : "")
            .($filter_sp || $filter_itu ? ") AND\n" : "")
            .($filter_continent ?
                 "    (`signals`.`ITU` IN(\n"
                ."        SELECT `ITU` FROM `itu` WHERE `region` = '".$this->filter_continent."')\n"
                ."    ) AND\n"
             :
                ""
            )
            ."  (1)\n"
            ."ORDER BY\n"
            ."  `signals`.`ITU`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`call`";
//    \Rxx\Rxx::z($sql);die;
        $result =     @\Rxx\Database::query($sql);
        $total =    \Rxx\Database::numRows($result);
        $heard =    0;

        //print("<pre>$sql</pre>");


        $signals =    array();
        $itu_sp =    array();
        for ($i=0; $i<$total; $i++) {
            $row = \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
            $signals[$row['ID']] = array('active'=>$row['active'],'khz'=>(float)$row['khz'],'call'=>$row['call'],'type'=>$row['type'],'SP'=>$row['SP'],'ITU'=>$row['ITU'],'heard'=>0);
            $itu_sp_index = $row['ITU']."_".$row['SP'];
            if (!isset($itu_sp[$itu_sp_index])) {
                $itu_sp[$itu_sp_index] = array('total'=>1,'heard'=>0);
            } else {
                $itu_sp[$itu_sp_index]['total']++;
            }
        }
        \Rxx\Database::freeResult($result);

        if ($createFor) {
            $sql =    "select DISTINCT `signals`.`ID` FROM `signals`,`logs` WHERE `signals`.`ID` = `logs`.`signalID` AND `listenerID` = $createFor";
            $result =     \Rxx\Database::query($sql);
            for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                $row = \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
                $ID = $row['ID'];
                if (isset($signals[$ID])) {
                    $signals[$ID]['heard'] = 1;
                    $itu_sp[$signals[$ID]['ITU']."_".$signals[$ID]['SP']]['heard']++;
                    $heard++;
                }
            }
            \Rxx\Database::freeResult($result);
        }

        $this->html =
            "<form name='form' action='".system_URL."/".$mode."' method='POST'>\n"
            ."<input type='hidden' name='mode' value='$mode'>\n"
            ."<input type='hidden' name='submode' value=''>\n"
            ."<input type='hidden' name='targetID' value=''>\n"
            ."<h2>Signal Seeklist</h2>\n"
            ."<table cellpadding='2' border='0' cellspacing='1'>\n"
            ."  <tr>\n"
            ."    <td align='center' valign='top' colspan='2'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
            ."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Customise Report</td>\n"
            ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
            ."      </tr>\n"
            ."    </table>\n"
            ."    <table cellpadding='2' cellspacing='0' border='1' class='tableForm'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Page Size</th>\n"
            ."        <td nowrap>\n"
            .$this->drawControlPaperSize()
            ."          <span class='noprint'>Click <a href='#' onclick='alert(\"Tips:\\n\\nYou should make sure that the size chosen matches the\\npaper size selected in your browser.\\n\\nUse \\\"Print Preview\\\" if available to check that report will fit.\\n\\nYou do not need to print the last page - this just contains\\nsoftware copyright info - save trees!\\n\")'><b>here</b></a> for tips...</span>"
            ."        </td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Types&nbsp;</th>\n"
            ."        <td nowrap class='signalType'>\n"
            .$this->drawControlType()
            ."</td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left' title='Select listener to generate Seeklist for'>Create for</th>\n"
            ."        <td><select name='createFor' class='formfield' style='font-family: monospace' onchange='set_listener_and_heard_in(document.form)'>\n"
            .\Rxx\Rxx::get_listener_options_list($filter_listener_SQL, $createFor, "Don't customise list for any listener")
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
            ."            <td align='right'><span title='Callsign or DGPS ID (Exact matches are shown at the top of the report, partial matches are shown later)'><b>Call / ID</b></span> <input type='text' name='filter_id' size='6' maxlength='12' value='$filter_id' class='formfield' title='Limit results to signals with this ID or partial ID -\nuse _ to indicate a wildcard character'></td>"
            ."          </tr>\n"
            ."        </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'><label for='filter_sp'>Signal<br />Locations</label></th>\n"
            ."        <td nowrap>\n"
            .$this->drawControlStates()
            ."<br>\n"
            .$this->drawControlSpItuClause()
            ."<br>\n"
            .$this->drawControlCountries()
            ."<br>\n"
            .$this->drawControlContinents()
            ."<br>\n"
            .$this->drawControlLocator()
            ."</td>"
            ."     </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Distance</th>\n"
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
            ."        <th align='left' valign='top'><span title='Only signals heard by the selected listener'>Heard by<br><br><span style='font-weight: normal;'>Use SHIFT or <br>CONTROL to<br>select multiple<br>values</span></span></th>"
            ."        <td><select name='filter_listener[]' multiple class='formfield' onchange='set_listener_and_heard_in(document.form)' style='font-family: monospace; width: 425px; height: 90px;' >\n"
            .\Rxx\Rxx::get_listener_options_list($filter_listener_SQL, $filter_listener, "Anyone (or enter values in \"Heard In\" box)")
            ."</select></td>\n"
            ."      </tr>\n";
        if (system=="RWW") {
            $this->html.=
                "     <tr class='rowForm'>\n"
                ."       <th align='left'>Heard in&nbsp;</th>\n"
                ."       <th align='left'>\n"
                ."<select name='region' onchange='document.form.go.disabled=1;document.form.submit()' class='formField' style='width: 100%;'>\n"
                .\Rxx\Rxx::get_region_options_list($region, "(All Continents)")
                ."</select>"
                ."</th>"
                ."      </tr>\n";
        }
        $this->html.=
            "      <tr class='rowForm'>\n"
            ."        <th align='left'><span title='Only signals heard in these states and countries'>Heard here</span></th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td title='Separate multiple options using spaces' nowrap>\n"
            ."<input type='text' name='filter_heard_in' size='41' value='".($filter_heard_in ? strToUpper($filter_heard_in) : "(All States and Countries)")."'\n"
            .($filter_heard_in=="" ? "style='color: #0000ff' ":"")
            ."onclick=\"if(this.value=='(All States and Countries)') { this.value=''; this.style.color='#000000'}\"\n"
            ."onblur=\"if(this.value=='') { this.value='(All States and Countries)'; this.style.color='#0000ff';}\"\n"
            ."onchange='set_listener_and_heard_in(form)' onKeyUp='set_listener_and_heard_in(form)' ".($filter_listener ? "class='formfield_disabled' disabled" : "class='formfield'").">"
            ."            <td width='45'><label for='radio_filter_heard_in_mod_any' title='Show where any terms match'><input id='radio_filter_heard_in_mod_any' type='radio' value='any' name='filter_heard_in_mod'".($filter_heard_in_mod!="all" ? " checked" : "").($filter_listener || !$filter_heard_in ? " disabled" : "").">Any</label></td>\n"
            ."            <td width='55'><label for='radio_filter_heard_in_mod_all' title='Show where all terms match'><input id='radio_filter_heard_in_mod_all' type='radio' value='all' name='filter_heard_in_mod'".($filter_heard_in_mod=="all" ? " checked" : "").($filter_listener || !$filter_heard_in ? " disabled" : "").">All</label></td>\n"
            ."          </tr>\n"
            ."	 </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Last Logged</th>\n"
            ."        <td>"
            ."<div style='float:left'><input title='Enter a start date to show only signals last logged after this date (YYYY-MM-DD format)'"
            ." type='text' name='filter_last_date_1' id='filter_last_date_1' size='12' maxlength='10'"
            ." value='".($filter_last_date_1 != "1900-01-01" ? $filter_last_date_1 : "")."' class='formfield' /></div>\n"
            ."<div style='float:left;padding:0 1em'>-</div>\n"
            ."<div style='float:left'><input title='Enter an end date to show only signals last logged before this date (YYYY-MM-DD format)'"
            ." type='text' name='filter_last_date_2' id='filter_last_date_2' size='12' maxlength='10'"
            ." value='".($filter_last_date_2 != "2030-01-01" ? $filter_last_date_2 : "")."' class='formfield' /></div>"
            ."</td>"
            ."      </tr>\n"
            ."      <tr class='rowForm noprint'>\n"
            ."        <th colspan='2'><input type='submit' onclick='return send_form(form)' name='go' value='Go' style='width: 100px;' class='formButton' title='Execute search'>\n"
            ."<input name='clear' type='button' class='formButton' value='Clear' style='width: 100px;' onclick='clear_signal_list(document.form)'></th>"
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>\n"
            ."</table><br>\n";

        $page =    1;


        $SP =        "";
        $ITU =    "";
        $xpos =    0;
        $col =    0;
        $row =    $page_len-$filter_size;
        $listener =    \Rxx\Rxx::get_listener_name($createFor);
        $this->html.=
            "<table cellpadding='2' cellspacing='1' border='1' style='page-break-after: always;' class='downloadtable'>\n"
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
            .($filter_heard_in ? " heard in [<b>$filter_heard_in</b>]" : "")
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
        $this->html.=
            "  <tr>\n"
            ."    <td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'>\n";
        foreach ($signals as $key => $value) {
            if ($value['SP'] != $SP || $value['ITU'] != $ITU) {
                if ($SP.$ITU!="" && $xpos) {
                    $this->html.=    "<br>";
                    $xpos+=    $heading_gap;
                }
                $this->html.=
                     "<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
                    ."  <tr>\n"
                    ."    <td><b>".$value['ITU']." ".$value['SP']."</b></td>\n"
                    ."    <td align='right'>("
                    .($createFor!="" ? $itu_sp[$value['ITU']."_".$value['SP']]['heard']." of " : "")
                    .$itu_sp[$value['ITU']."_".$value['SP']]['total']
                    .")</td>\n"
                    ."  </tr>\n"
                    ."</table>\n";
                $xpos+=    $heading_height;
                $SP =    $value['SP'];
                $ITU =    $value['ITU'];
            }
            if ($value["active"]!="1") {
                $bgcolor =    " style='border: 1px dashed #000000; background-color: #d0d0d0;' title='(Reportedly Out Of Service or Decomissioned)'";
            } else {
                $style = " style='border:1px solid #fff; background-color: ".\Rxx\Signal::$colors[$value['type']]."'";
                switch ($value['type']) {
                    case NDB:
                        $bgcolor = $style." title='NDB'";
                        break;
                    case DGPS:
                        $bgcolor = $style." title='DGPS Station'";
                        break;
                    case DSC:
                        $bgcolor = $style." title='DSC Station'";
                        break;
                    case TIME:
                        $bgcolor = $style." title='Time Signal Station'";
                        break;
                    case NAVTEX:
                        $bgcolor = $style." title='NAVTEX Station'";
                        break;
                    case HAMBCN:
                        $bgcolor = $style." title='Amateur signal'";
                        break;
                    case OTHER:
                        $bgcolor = $style." title='Other form of transmission'";
                        break;
                }
            }
            $this->html.=
                 "<table cellpadding='0' cellspacing='0' border='0'><tr><td>"
                ."<span class='fixed'$bgcolor>"
                .\Rxx\Rxx::pad_dot($value["khz"], 8)
                .\Rxx\Rxx::pad_dot($value['call'], 8)
                ."</td><td>"
                .($value['heard'] ?
                    "<img src='".BASE_PATH."assets/icon-tick-on.gif' alt='Y'>"
                 :
                    "<img src='".BASE_PATH."assets/icon-tick-off.gif' alt='N'>"
                )
                ."</span></td></tr></table>\n";
            $xpos+=    $line_height;
            if ($xpos>$row) {
                $xpos=0;
                $col++;
                if ($col<$page_cols) {
                    $this->html.=        "<td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'>";
                } else {
                    $this->html.=         "</td>\n"
                        ."</tr>\n"
                        ."</table>\n"
                        ."<br>\n"
                        ."<br>\n"
                        ."<table cellpadding='2' cellspacing='1' border='1' style='page-break-after: always;' class='downloadtable'>\n"
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

                    $this->html.=        "  <tr>\n"
                        ."    <td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'>\n"
                        ."    <table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><b>...".$value['ITU']." ".$value['SP']."</b></td><td align='right'>(".($createFor!="" ? $itu_sp[$value['ITU']."_".$value['SP']]['heard']." of " : "").$itu_sp[$value['ITU']."_".$value['SP']]['total'].")</td></tr></table>\n";
                    $xpos+=    $heading_height;
                    $col = 0;
                    $row = $page_len;
                }
            }

        }

        if ($col<$page_cols) {
            $this->html.=    "&nbsp;</td>\n";
            for ($col; $col<$page_cols-1; $col++) {
                $this->html.=    "<td valign='top' nowrap width='".(100/$page_cols)."%' class='downloadTableContent'><span class='fixed'>&nbsp;</span></td>\n";
            }
        }
        $row = \Rxx\Log::getLogDateRange($filter_system, $region);
        $this->html.=
            "</tr>\n"
            ."</table>\n"
            ."</form>\n"
            ."<script type='text/javascript'>"
            ."//<!--\n"
            ."\$(function() {\n"
            ."  var minDate = new Date('".$row['first_log']."');\n"
            ."  var maxDate = new Date('".$row['last_log']."');\n"
            ."  minDate.setDate(minDate.getDate()+1);\n"
            ."  maxDate.setDate(maxDate.getDate()+1);\n"
            ."  var config = {\n"
            ."    changeMonth:true,\n"
            ."    changeYear:true,\n"
            ."    dateFormat:'yy-mm-dd',\n"
            ."    minDate:minDate,\n"
            ."    maxDate:maxDate,\n"
            ."    showOn:'button',\n"
            ."    buttonImage:'".BASE_PATH."assets/datepicker_".strToLower(system).".gif',\n"
            ."    buttonImageOnly: true,\n"
            ."    buttonText: 'Select date'\n"
            ."  };\n"
            ."  \$('#filter_last_date_1').datepicker(config);\n"
            ."  \$('#filter_last_date_2').datepicker(config);\n"
            ."  \$('#filter_id').focus();\n"
            ."  \$('#filter_id').select();\n"
            ."})\n"
            ."//-->\n"
            ."</script>\n";
    }

    private function drawControlStates()
    {
        return
             "<label title='List of States or Provinces' style='display:inline-block; width:70px; margin:0.25em 0;'>"
            ."<a href='".system_URL."/show_sp' onclick='show_sp();return false'"
            ." title='NDBList State and Province codes'><b>States</b></a></label> "
            ."<input title='Enter one or more states or provinces (e.g. MI or NB) to show only signals physically"
            ." located there' type='text' name='filter_sp' id='filter_sp' size='20' value='"
            .$this->filter_sp
            ."' class='formfield' style='width:360px'>";
    }

    private function drawControlContinents()
    {
        return
             "<label style='display:inline-block; width:70px; margin:0.25em 0;' for='filter_continent'>"
            ."<b>Continent</b></label> "
            ."<select title='Choose a continent to show only signals physically located there'"
            ." name='filter_continent' id='filter_continent' class='formfield' style='width:360px'>"
            .Rxx::get_region_options_list($this->filter_continent, '(All)')
            ."</select>\n";
    }
    

    private function drawControlCountries()
    {
        return
             "<label title='List of Countries' style='display:inline-block; width:70px; margin:0.25em 0;'>"
            ."<a href='".system_URL."/show_itu' onclick='show_itu();return false' title='NDBList Country codes'>"
            ."<b>Countries</b></a></label> "
            ."<input title='Enter one or more NDBList approved 3-letter country codes (e.g. CAN or BRA) to show only"
            ." signals physically located there' type='text' name='filter_itu' id='filter_itu' size='20' value='"
            .$this->filter_itu
            ."' class='formfield' style='width:360px'>";
    }

    private function drawControlLocator()
    {
        return
            "<label title='Maidenhead Locator Grid Squares' style='display:inline-block; width:70px; margin:0.25em 0;'>"
            ."<b>GSQs</b></label> "
            ."<input title='Enter one or more partial or complete GSQ locator values to restrict results to signals in those squares'"
            ." type='text' name='filter_locator' id='filter_locator' size='20' value='"
            .$this->filter_locator
            ."' class='formfield' style='width:360px'>";
    }

    private function drawControlPaperSize()
    {
        $paper = RXX::get_var('paper');
        return
             "          <select name='paper' class='formField' onchange='document.form.go.value=\"Please wait...\";document.form.go.disabled=1;document.form.submit()'>\n"
            ."            <option value='ltr'".($paper=='ltr' ? " selected" : "").">Letter (Portrait) - 8.5&quot; x 11&quot;</option>\n"
            ."            <option value='lgl'".($paper=='lgl' ? " selected" : "").">Legal (Portrait) - 8.5&quot; x 14&quot;</option>\n"
            ."            <option value='a4'".($paper=='a4' ? " selected" : "").">A4 (Portrait) - 21.6cm x 27.9cm</option>\n"
            ."            <option value='ltr_l'".($paper=='ltr_l' ? " selected" : "").">Letter (Landscape) - 11&quot; x 8.5&quot;</option>\n"
            ."            <option value='lgl_l'".($paper=='lgl_l' ? " selected" : "").">Legal (Landscape) - 14&quot; x 8.5&quot;</option>\n"
            ."            <option value='a4_l'".($paper=='a4_l' ? " selected" : "").">A4 (Landscape) - 27.9cm x 21.6cm</option>\n"
            ."          </select>\n";
    }

    private function drawControlSpItuClause()
    {
        return
             "<label style='display:inline-block; width:70px; margin:0.25em 0;' for='filter_sp_itu_clause'>"
            ."&nbsp;</label> "
            ."<select class='formfield' title='Only used when both States and Countries are given'"
            ." name='filter_sp_itu_clause' id='filter_sp_itu_clause'>"
            ."<option value='AND'".($this->filter_sp_itu_clause=='AND' ? " selected='selected'" : "").">AND</option>"
            ."<option value='OR'".($this->filter_sp_itu_clause=='OR' ? " selected='selected'" : "").">OR</option>"
            ."</select> (only when both State AND Country are given)";
    }

    private function drawControlType()
    {
        $types = array(
            array(DGPS,     'type_DGPS',    'DGPS',     13),
            array(DSC,      'type_DSC',     'DSC',      11),
            array(HAMBCN,   'type_HAMBCN',  'Ham',      12),
            array(NAVTEX,   'type_NAVTEX',  'Navtex',   15),
            array(NDB,      'type_NDB',     'NDB',      11),
            array(TIME,     'type_TIME',    'Time',     13),
            array(OTHER,    'type_OTHER',   'Other',    13),
            array(ALL,      'type_ALL',     '(All)',    12)
        );
        $html = '';
        foreach ($types as $type) {
            $html.=
                "<label style='width:".$type[3]."%;' class='".strToLower($type[1])."'>"
                ."<input type='checkbox' style='vertical-align: middle' name='".$type[1]."' value='1'"
                .($this->{$type[1]} ? " checked='checked'" : "")
                .('type_ALL' == $type[1] ? " onchange=\"set_signal_list_types(document.form, this.checked)\"" : "")
                .">"
                .$type[2]
                ."</label>";
        }
        return $html;
    }

    private function setup()
    {
        global $filter_continent, $filter_itu, $filter_sp_itu_clause, $filter_locator, $filter_sp;
        $this->filter_continent =       $filter_continent;
        $this->filter_itu =             $filter_itu;
        $this->filter_locator =         $filter_locator;
        $this->filter_sp =              $filter_sp;
        $this->filter_sp_itu_clause =   $filter_sp_itu_clause;

        $this->type_NDB =               Rxx::get_var('type_NDB');
        $this->type_TIME =              Rxx::get_var('type_TIME');
        $this->type_DGPS =              Rxx::get_var('type_DGPS');
        $this->type_DSC =               Rxx::get_var('type_DSC');
        $this->type_NAVTEX =            Rxx::get_var('type_NAVTEX');
        $this->type_HAMBCN =            Rxx::get_var('type_HAMBCN');
        $this->type_OTHER =             Rxx::get_var('type_OTHER');
        $this->type_ALL =
            ($this->type_NDB && $this->type_TIME && $this->type_DGPS && $this->type_DSC && $this->type_NAVTEX && $this->type_HAMBCN && $this->type_OTHER);
        if (!(
            $this->type_NDB ||
            $this->type_DGPS ||
            $this->type_DSC ||
            $this->type_TIME ||
            $this->type_HAMBCN ||
            $this->type_NAVTEX ||
            $this->type_OTHER
        )) {
            $this->type_NDB = 1;
        }

    }
}
