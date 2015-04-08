<?php

class SignalList
{
    protected $html = '';
    protected $ObjSignal;

    protected $total;
    protected $rows =   array();
    protected $stats =  array();
    protected $region;

    protected $filter_active;
    protected $filter_by_range;
    protected $filter_channels;
    protected $filter_continent;
    protected $filter_custom;
    protected $filter_date_1;
    protected $filter_date_2;
    protected $filter_dx_gsq;
    protected $filter_dx_lat;
    protected $filter_dx_lon;
    protected $filter_dx_max;
    protected $filter_dx_min;
    protected $filter_dx_units;
    protected $filter_heard_in;
    protected $filter_id;
    protected $filter_itu;
    protected $filter_khz_1;
    protected $filter_khz_2;
    protected $filter_listener;
    protected $filter_sp;
    protected $filter_system;

    protected $type_NDB;
    protected $type_TIME;
    protected $type_DGPS;
    protected $type_DSC;
    protected $type_NAVTEX;
    protected $type_HAMBCN;
    protected $type_OTHER;

    protected $listeners_list_filter;
    protected $offsets;

    protected $limit;
    protected $offset;
    protected $sort_by;

    protected $mode;
    protected $submode;

    protected $sql_filter_active =          false;
    protected $sql_filter_channels =        false;
    protected $sql_filter_continent =       false;
    protected $sql_filter_custom =          false;
    protected $sql_filter_frequency =       false;
    protected $sql_filter_heard_in =        false;
    protected $sql_filter_heard_in_mod =    false;
    protected $sql_filter_id =              false;
    protected $sql_filter_itu =             false;
    protected $sql_filter_last_heard =      false;
    protected $sql_filter_listener =        false;
    protected $sql_filter_range_max =       false;
    protected $sql_filter_range_min =       false;
    protected $sql_filter_sp =              false;
    protected $sql_filter_system =          false;
    protected $sql_filter_type =            false;
    protected $sql_limit =                  false;
    protected $sql_offset =                 false;
    protected $sql_sort_by =                false;


    public function draw()
    {
        $this->setup();

        $this->html.=
             "<h2>Signal List</h2>\n"
            .$this->drawHelp()
            ."<div style='float:left;width:540px; margin: 0 20px 10px 0;'>"
            .$this->drawForm()
            ."</div>"
            ."<div style='float:left;width:160px;'>"
            .$this->drawSignalStats()
            ."<br>\n"
            .$this->drawListenerStats()
            ."<br>\n"
            .$this->drawVisitorPoll()
            ."</div>"
            ."<br style='clear:both' />";

        if ($this->rows) {
            if ($this->sort_by=='CLE64') {
                $this->html.=    "<table cellpadding='0' cellspacing='0' border='0'><tr><td><ul><li><b><font color='#ff0000'>CLE64 Custom sort order applied:</font></b><br> - Show <b>active</b> beacons first<br> - Sort by <b>first letter</b> of callsign: <b>A-Z</b><br> - Sort by <b>DX</b> from Grid Square <b>".$this->filter_dx_gsq."</b>.<br><b>Tip:</b> You can further <b>refine this search</b> by entering values in 'Heard here', 'Heard by' or adding other criteria such as range limits.</li></ul></td></tr></table>\n";
            }
            if ($this->sort_by=='CLE64_d') {
                $this->html.=    "<table cellpadding='0' cellspacing='0' border='0'><tr><td><ul><li><b><font color='#ff0000'>CLE64 Custom sort order applied:</font></b><br> - Show <b>active</b> beacons first<br> - Sort by <b>first letter</b> of callsign: <b>Z-A</b><br> - Sort by <b>DX</b> from Grid Square <b>".$this->filter_dx_gsq."</b>.<br><b>Tip:</b> You can further <b>refine this search</b> by entering values in 'Heard here', 'Heard by' or adding other criteria such as range limits.</li></ul></td></tr></table>\n";
            }
            if ($this->filter_id) {
                $this->html.=    "<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>Note:</b> Any exact matches for <b>".$this->filter_id."</b> will shown at the top of this list, regardless of the station's current status.</td></tr></table>\n";
            }
            $this->html.=
            "<table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' bgcolor='#ffffff' class='downloadtable'>\n"
            ."  <thead>\n"
            ."  <tr id=\"header\">\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='khz".($this->sort_by=="khz" ? "_d" : "")."';document.form.submit()\" title=\"Sort by Frequency\">KHz ".($this->sort_by=='khz' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='khz_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='call".($this->sort_by=="call" ? "_d" : "")."';document.form.submit()\" title=\"Sort by Callign or DGPS Station ID\">ID ".($this->sort_by=='call' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='call_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            .($this->type_NDB ?
            "    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='LSB".($this->sort_by=="LSB" ? "_d" : "")."';document.form.submit()\" title=\"Sort by LSB (-ve Offset)\">LSB ".($this->sort_by=='LSB' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='LSB_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='USB".($this->sort_by=="USB" ? "_d" : "")."';document.form.submit()\" title=\"Sort by USB (+ve Offset)\">USB ".($this->sort_by=='USB' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='USB_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='sec".($this->sort_by=="sec" ? "_d" : "")."';document.form.submit()\" title=\"Sort by cycle duration\">Sec ".($this->sort_by=='sec' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='sec_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='format".($this->sort_by=="format" ? "_d" : "")."';document.form.submit()\" title=\"Sort by cycle format\">Fmt ".($this->sort_by=='format' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='format_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            : ""
            )
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='QTH".($this->sort_by=="QTH" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Name' and Location\">'Name' and Location ".($this->sort_by=='QTH' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='QTH_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='sp".($this->sort_by=="sp" ? "_d" : "")."';document.form.submit()\" title=\"Sort by State / Province\">S/P ".($this->sort_by=='sp' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='sp_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='itu".($this->sort_by=="itu" ? "_d" : "")."';document.form.submit()\" title=\"Sort by NDB List Country Code\">ITU ".($this->sort_by=='itu' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='itu_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='gsq".($this->sort_by=="gsq" ? "_d" : "")."';document.form.submit()\" title=\"Sort by GSQ Grid Locator Square\">GSQ ".($this->sort_by=='gsq' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='gsq_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='".($this->sort_by=="pwr_d" ? "pwr" : "pwr_d")."';document.form.submit()\" title=\"Sort by Transmitter Power\">PWR ".($this->sort_by=='pwr' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='pwr_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='notes".($this->sort_by=="notes" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Notes' column\">Notes ".($this->sort_by=='notes' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='notes_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='heard_in".($this->sort_by=="heard_in" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Heard In' column\">Heard In <span style='font-weight: normal'>(Click for Map - <b>bold</b> = daytime logging)</span>".($this->sort_by=='heard_in' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='heard_in_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='logs".($this->sort_by=="logs" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Logs' column\" nowrap>Logs ".($this->sort_by=='logs' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='logs_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='last_heard".($this->sort_by=="last_heard" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Last Heard' column (YYYY-MM-DD)\" nowrap>Last Heard ".($this->sort_by=='last_heard' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='last_heard_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";

            if ($this->filter_listener) {
                $this->html.=    "    <th class='downloadTableHeadings_nosort' colspan='2'>Range from<br>Listener</th>\n";
            }
            if ($this->filter_dx_gsq) {
                $this->html.=    "    <th class='downloadTableHeadings_nosort' colspan='3'>Range from<br>GSQ</th>\n";
            }
            if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
                $this->html.=    "    <th class='downloadTableHeadings_nosort' rowspan='2' valign='bottom'>&nbsp;</th>\n";
            }
            if ($this->filter_listener || $this->filter_dx_gsq) {
                $this->html.=    "  <tr>\n";
                if ($this->filter_listener) {
                    $this->html.=
                    "    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='".($this->sort_by=="dx_d" ? "dx" : "dx_d")."';document.form.submit()\" title=\"Sort by 'KM' column\"  nowrap>KM ".($this->sort_by=='dx' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='dx_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
                    ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='".($this->sort_by=="dx_d" ? "dx" : "dx_d")."';document.form.submit()\" title=\"Sort by 'Miles' column\" nowrap>Miles ".($this->sort_by=='dx' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='dx_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
              //		 ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='".($this->sort_by=="dx_deg" ? "dx_deg_d" : "dx_deg")."';document.form.submit()\" title=\"Sort by 'Degrees' column\" nowrap>Deg ".($this->sort_by=='dx_deg' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='dx_deg_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>": "")."</th>\n"
                    ;
                }
                if ($this->filter_dx_gsq) {
                    $this->html.=
                    "    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='".($this->sort_by=="range_dx_km_d" ? "range_dx_km" : "range_dx_km_d")."';document.form.submit()\" title=\"Sort by 'Range KM' column\" nowrap>KM ".($this->sort_by=='range_dx_km' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='range_dx_km_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
                    ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='".($this->sort_by=="range_dx_km_d" ? "range_dx_km" : "range_dx_km_d")."';document.form.submit()\" title=\"Sort by 'Range Miles' column\" nowrap>Miles ".($this->sort_by=='range_dx_km' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='range_dx_km_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
                    ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sort_by.value='".($this->sort_by=="range_dx_deg" ? "range_dx_deg_d" : "range_dx_deg")."';document.form.submit()\" title=\"Sort by 'Degrees' column\" nowrap>Deg ".($this->sort_by=='range_dx_deg' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($this->sort_by=='range_dx_deg_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
                }
                $this->html.=        "  </tr>\n";
            }
            $this->html.=
            "  </tr>\n"
            ."  </thead>\n"
            ."  <tbody>";
            foreach ($this->rows as $row) {
                if (isset($filter_by_dx) && $filter_by_dx) {
                    $dx =        get_dx($filter_by_lat, $filter_by_lon, $row["lat"], $row["lon"]);
                }
                if (!$row["active"]) {
                    $class='inactive';
                    $title = '(Reportedly off air or decommissioned)';
                } else {
                    switch ($row["type"]) {
                        case NDB:        $class='ndb';
                            $title = 'NDB';
                            break;
                        case DGPS:    $class='dgps';
                            $title = 'DGPS Station';
                            break;
                        case DSC:        $class='dsc';
                            $title = 'DSC Station';
                            break;
                        case TIME:    $class='time';
                            $title = 'Time Signal Station';
                            break;
                        case NAVTEX:    $class='navtex';
                            $title = 'NAVTEX Station';
                            break;
                        case HAMBCN:    $class='hambcn';
                            $title = 'Amateur signal';
                            break;
                        case OTHER:    $class='other';
                            $title = 'Other Utility Station';
                            break;
                    }
                }
                $call =    ($this->filter_id ? highlight($row["call"], $this->filter_id) : $row["call"]);
                $heard_in = ($this->filter_heard_in ? highlight($row["heard_in_html"], str_replace(" ", "|", $this->filter_heard_in)) : $row["heard_in_html"]);
                $SP =     ($this->filter_sp ? highlight($row["SP"], str_replace(" ", "|", $this->filter_sp)) : $row["SP"]);
                $ITU =    ($this->filter_itu ? highlight($row["ITU"], str_replace(" ", "|", $this->filter_itu)) : $row["ITU"]);
                $this->html.=
                "<tr class='rownormal ".$class."' title='".$title."'>"
                ."<td><a href='".system_URL."/signal_list?filter_khz_1=".(float)$row["khz"]."&amp;filter_khz_2=".(float)$row["khz"]."&amp;limit=-1' title='Filter on this value'>".(float)$row["khz"]."</a></td>\n"
                ."<td><a onmouseover='window.status=\"View profile for ".(float)$row["khz"]."-".$row["call"]."\";return true;' onmouseout='window.status=\"\";return true;' href=\"".system_URL."/".$row["ID"]."\" onclick=\"signal_info('".$row["ID"]."');return false\"><b>$call</b></a></td>\n";
                if ($this->type_NDB) {
                    $this->html.=
                    "<td align='right'>".$row["LSB_approx"].($row["LSB"]<>"" ? ($this->offsets=="" ? $row["LSB"] : number_format((float) ($row["khz"]-($row["LSB"]/1000)), 3, '.', '')): "&nbsp;")."</td>\n"
                    ."<td align='right'>".$row["USB_approx"].($row["USB"]<>"" ? ($this->offsets=="" ? $row["USB"] : number_format((float) ($row["khz"]+($row["USB"]/1000)), 3, '.', '')): "&nbsp;")."</td>\n"
                    ."<td>".($row["sec"] ? $row["sec"] : "&nbsp;")."</td>\n"
                    ."<td>".($row["format"] ? stripslashes($row["format"]) : "&nbsp;")."</td>\n";
                }
                $this->html.=
                "<td>".($row["QTH"] ? get_sp_maplinks($row['SP'], $row['ID'], $row["QTH"]) : "&nbsp;")."</td>\n"
                ."<td>".($SP ? "<a href='".system_URL."/signal_list?filter_sp=".$row["SP"]."' title='Filter on this value'>".$SP."</a>" : "&nbsp;")."</td>\n"
                ."<td>".($ITU ? "<a href='".system_URL."/signal_list?filter_itu=".$row["ITU"]."' title='Filter on this value'>".$ITU."</a>" : "&nbsp;")."</td>\n"
                ."<td>".($row["GSQ"] ? "<a href='.' onclick='popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\");return false;' title='Show map (accuracy limited to nearest Grid Square)'><span class='fixed'>".$row["GSQ"]."</span></a>" : "&nbsp;")."</td>\n"
                ."<td>".($row["pwr"] ? $row["pwr"] : "&nbsp;")."</td>\n"
                ."<td>".($row["notes"] ? stripslashes($row["notes"]) : "&nbsp;")."</td>\n"
                ."<td>".($heard_in ? $heard_in : "&nbsp;")."</td>\n"
                ."<td align='right'>"
                .($row["logs"] ? "<a href=\"".system_URL."/signal_log/".$row["ID"]."\" onclick='signal_log(\"".$row["ID"]."\");return false;'><b>".$row["logs"]."</b></a>" : "&nbsp;")."</td>\n"
                ."<td>".($row["last_heard"]!="0000-00-00" ? $row["last_heard"] : "&nbsp;")."</td>\n";

                if ($this->filter_listener) {
                    $this->html.=
                    "<td align='right'>".($row["dx_km"]!=='' ? $row["dx_km"] : "&nbsp;")."</td>\n"
                    ."<td align='right'>".($row["dx_miles"]!=='' ? $row["dx_miles"] : "&nbsp;")."</td>\n"
              //	  ."<td align='right'>".($row["dx_deg"] ? $row["dx_deg"] : "&nbsp;")."</td>\n"
                    ;
                }

                if ($this->filter_dx_gsq) {
                    $this->html.=
                    "<td align='right'>".($row["range_dx_km"]!=='' ? round($row["range_dx_km"]) : "&nbsp;")."</td>\n"
                    ."<td align='right'>".($row["range_dx_miles"]!=='' ? round($row["range_dx_miles"]) : "&nbsp;")."</td>\n"
                    ."<td align='right'>".($row["range_dx_deg"]!=='' ? round($row["range_dx_deg"]) : "&nbsp;")."</td>\n";
                }

                if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
                    $this->html.=
                    "<td nowrap><a href='javascript: if (confirm(\"CONFIRM\\n\\nAre you sure you wish to delete this signal and\\nall associated logs?\")) { document.form.submode.value=\"delete\"; document.form.targetID.value=\"".$row["ID"]."\"; document.form.submit();}'>Del</a>\n"
                    ."<a href='javascript:signal_merge(".$row["ID"].")'>Merge</a></td>\n";
                }
            }
            $this->html.=     "  </tr>"
            ."</tbody>"
            ."</table>\n"
            ."<br>\n"
            ."<span class='noscreen'>\n"
            ."<b><i>(End of printout)</i></b>\n"
            ."</span>\n";
        } else {
            $this->html.=    "<h2>Results</h2><br><br><h3>No results for search criteria</h3><br><br><br>\n";
        }

            $this->html.=
            "<span class='noprint'>\n"
            ."<input type='button' value='Print...' onclick='".(($this->limit!=-1 && $this->limit<$this->total) ? "if (confirm(\"Information\\n\\nThis printout works best in Landscape.\\n\\nYou are not presently displaying all ".$this->total." available records.\\nContinue anyway?\")) { window.print(); }": "window.print()")."' class='formbutton' style='width: 150px;'> ";
        if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
            $this->html.=    "<input type='button' class='formbutton' value='Add signal...' style='width: 150px' onclick='signal_add(document.form.filter_id.value,document.form.filter_khz_1.value,\"\",\"\",\"\",\"\",\"\",get_type(document.form))'> ";
        }
            $this->html.=
            "<input type='button' class='formbutton' value='All ".system." > Excel' style='width: 150px' title='Get the whole database as Excel' onclick='export_signallist_excel();'> "
            ."<input type='button' class='formbutton' value='All ".system." > PDF'   style='width: 150px' title='get the whole database as PDF' onclick='export_signallist_pdf();'> "
            ."<input type='button' class='formbutton' value='All ".system." > ILG'   style='width: 150px' title='get the whole database as ILGRadio format for Ham Radio Deluxe' onclick='if (confirm(\"EXPORT ENTIRE ".system." DATABASE TO IRGRadio Database format?\\n\\nThis can be a time consuming process - typically 5 minutes or more.\")) { show_ILG(); }'> "
            ."</span>\n"
            ."<script type='text/javascript'>document.form.filter_id.focus();document.form.filter_id.select();</script>\n";
            return $this->html;
    }

    protected function drawControlChannels()
    {
        return
             "<select name='filter_channels' id='filter_channels' class='formField'>\n"
            ."  <option value=''" .($this->filter_channels=='' ?  " selected='selected'" : '').">All</option>\n"
            ."  <option value='1'".($this->filter_channels=='1' ? " selected='selected'" : '').">Only 1 KHz</option>\n"
            ."  <option value='2'".($this->filter_channels=='2' ? " selected='selected'" : '').">Not 1 KHz</option>\n"
            ."</select>";
    }

    protected function drawControlContinents()
    {
        return
             "<label style='display:inline-block; width:70px; margin:0.25em 0;'>"
            ."<b>Continent</b></label> "
            ."<select title='Choose a continent to show only signals physically located there'"
            ." name='filter_continent' id='filter_continent' class='formfield' style='width:360px'>"
            .get_region_options_list($this->filter_continent, '(All)')
            ."</select>\n";
    }

    protected function drawControlCountries()
    {
        return
             "<label title='List of Countries' style='display:inline-block; width:70px; margin:0.25em 0;'>"
            ."<a href='".system_URL."/show_itu' onclick='show_itu();return false' title='NDBList Country codes'>"
            ."<b>Countries</b></a></label> "
            ."<input title='Enter one or more NDBList approved 3-letter country codes (e.g. CAN or BRA) to show only"
            ." signals physically located there' type='text' name='filter_itu' id='filter_itu' size='20' value='"
            .$this->filter_itu
            ."' class='formfield' style='width:360px'/>";
    }

    protected function drawControlFrequencyRange()
    {
        return
             "<input title='Lowest frequency (or leave blank)' type='text' name='filter_khz_1' id='filter_khz_1'"
            ." size='6' maxlength='9' value='"
            .($this->filter_khz_1 !="0" ? $this->filter_khz_1 : "")
            ."' class='formfield' />"
            ." - "
            ."<input title='Highest frequency (or leave bank)' type='text' name='filter_khz_2' id='filter_khz_2'"
            ." size='6' maxlength='9' value='"
            .($this->filter_khz_2 != 1000000 ? $this->filter_khz_2 : "")
            ."' class='formfield' /> KHz";

    }

    protected function drawControlId()
    {
        return
             "<label for='filter_id' title='Callsign or DGPS ID"
            ." (Exact matches are shown at the top of the report, partial matches are shown later)'>"
            ."<b>Call / ID</b></label> "
            ."<input type='text' name='filter_id' id='filter_id' size='6' maxlength='12' value='".$this->filter_id."'"
            ." class='formfield' title='Limit results to signals with this ID or partial ID -\n"
            ."use _ to indicate a wildcard character' />";
    }

    protected function drawControlStates()
    {
        return
             "<label title='List of States or Provinces' style='display:inline-block; width:70px; margin:0.25em 0;'>"
            ."<a href='".system_URL."/show_sp' onclick='show_sp();return false'"
            ." title='NDBList State and Province codes'><b>States</b></a></label> "
            ."<input title='Enter one or more states or provinces (e.g. MI or NB) to show only signals physically"
            ." located there' type='text' name='filter_sp' id='filter_sp' size='20' value='"
            .$this->filter_sp
            ."' class='formfield' style='width:360px'/>";
    }

    protected function drawControlType()
    {
        $types = array(
            array(DGPS,     'type_DGPS',    'DGPS',     14),
            array(DSC,      'type_DSC',     'DSC',      13),
            array(HAMBCN,   'type_HAMBCN',  'Ham',      13),
            array(NAVTEX,   'type_NAVTEX',  'NAVTEX',   19),
            array(NDB,      'type_NDB',     'NDB',      13),
            array(TIME,     'type_TIME',    'Time',     14),
            array(OTHER,    'type_OTHER',   'Other',    14)

        );
        $html = '';
        foreach ($types as $type) {
            $html.=
                 "<label style='width:".$type[3]."%;background:".Signal::$colors[$type[0]]."'>"
                ."<input type='checkbox' name='".$type[1]."' value='1'"
                .($this->$type[1] ? " checked='checked'" : "")
                .">"
                .$type[2]
                ."</label>";
        }
        return $html;
    }

    protected function drawForm()
    {
        $html =
             "<form name='form' action='".system_URL."/".$this->mode."' method='POST'>\n"
            ."<input type='hidden' name='mode' value='".$this->mode."' />\n"
            ."<input type='hidden' name='submode' value='' />\n"
            ."<input type='hidden' name='targetID' value='' />\n"
            ."<input type='hidden' name='sort_by' value='".$this->sort_by."' />\n"
            ."<div class='form_box shadow'>\n"
            ."  <div class='header'>Customise ".system." Report</div>\n"
            ."  <div class='body rowForm'>\n"
            ."    <table cellpadding='2' cellspacing='0' border='10' class='tableForm' style='width:540px'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Show</th>\n"
            ."        <td nowrap>"
            .show_page_bar($this->total, $this->limit, $this->offset, 1, 1, 1)
            ."</td>\n"
            ."      </tr>"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Types&nbsp;</th>\n"
            ."        <td nowrap class='signalType'>\n"
            .$this->drawControlType()
            ."</td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'><label for='filter_khz_1'>Frequencies</label></th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td>"
            .$this->drawControlFrequencyRange()
            ."</td>\n"
            ."            <td><label for='filter_channels'><b>Channels</b></label></td>\n"
            ."            <td>"
            .$this->drawControlChannels()
            ."</td>\n"
            ."            <td align='right'>"
            .$this->drawControlId()
            ."</td>"
            ."          </tr>\n"
            ."        </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Locations</th>\n"
            ."        <td nowrap>\n"
            .$this->drawControlStates()
            ."<br />\n"
            .$this->drawControlCountries()
            ."<br />\n"
            .$this->drawControlContinents()
            ."</td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Range</th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td>&nbsp;<b>From GSQ</b> <input title='Enter a grid square to show only signals physically located between the distances indicated' type='text' name='filter_dx_gsq' size='6' maxlength='6' value='".$this->filter_dx_gsq."' class='formfield' onKeyUp='set_range(form)' onchange='set_range(form)'></td>"
            ."            <td><b><span title='Distance'>DX</span></b> <input title='Enter a value to show only signals equal or greater to this distance' type='text' name='filter_dx_min' size='5' maxlength='5' value='".$this->filter_dx_min."' onKeyUp='set_range(form)' onchange='set_range(form)'".($this->filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")."> - "
            ."<input title='Enter a value to show only signals up to this distance' type='text' name='filter_dx_max' size='5' maxlength='5' value='".$this->filter_dx_max."' onKeyUp='set_range(form)' onchange='set_range(form)'".($this->filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")."></td>"
            ."            <td width='45'><label for='filter_dx_units_km'><input type='radio' id='filter_dx_units_km' name='filter_dx_units' value='km'".($this->filter_dx_units=="km" ? " checked" : "").($this->filter_dx_gsq && ($this->filter_dx_min || $this->filter_dx_max) ? "" : " disabled").">km</label></td>"
            ."            <td width='55'><label for='filter_dx_units_miles'><input type='radio' id='filter_dx_units_miles' name='filter_dx_units' value='miles'".($this->filter_dx_units=="miles" ? " checked" : "").($this->filter_dx_gsq && ($this->filter_dx_min || $this->filter_dx_max) ? "" : " disabled").">miles&nbsp;</label></td>"
            ."          </tr>\n"
            ."	 </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left' valign='top'><span title='Only signals heard by the selected listener'>Heard by<br><br><span style='font-weight: normal;'>Use SHIFT or <br>CONTROL to<br>select multiple<br>values</span></span></th>"
            ."        <td><select name='filter_listener[]' multiple class='formfield' onchange='set_listener_and_heard_in(document.form)' style='font-family: monospace; width: 425; height: 90px;' >\n"
            .get_listener_options_list($this->listeners_list_filter, $this->filter_listener, "Anyone (or enter values in \"Heard here\" box)")
            ."</select></td>\n"
            ."      </tr>\n";
        if (system=="RWW") {
            $html.=
                 "     <tr class='rowForm'>\n"
                ."       <th align='left'>Heard in&nbsp;</th>\n"
                ."       <td>\n"
                ."<select name='region' onchange='document.form.go.disabled=1;document.form.submit()' class='formField' style='width: 100%;'>\n"
                .get_region_options_list($this->region, "(All Continents)")
                ."</select>"
                ."</td>"
                ."      </tr>\n";
        }
        $html.=
             "      <tr class='rowForm'>\n"
            ."        <th align='left'><span title='Only signals heard in these states and countries'>Heard here</span></th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td title='Separate multiple options using spaces' nowrap>\n"
            ."<input type='text' name='filter_heard_in' size='41' value='".($this->filter_heard_in ? strToUpper($this->filter_heard_in) : "(All States and Countries)")."'\n"
            .($this->filter_heard_in=="" ? "style='color: #0000ff' ":"")
            ."onclick=\"if(this.value=='(All States and Countries)') { this.value=''; this.style.color='#000000'}\"\n"
            ."onblur=\"if(this.value=='') { this.value='(All States and Countries)'; this.style.color='#0000ff';}\"\n"
            ."onchange='set_listener_and_heard_in(form)' onKeyUp='set_listener_and_heard_in(form)' ".($this->filter_listener ? "class='formfield_disabled' disabled" : "class='formfield'").">"
            ."            <td width='45'><label for='radio_filter_heard_in_mod_any' title='Show where any terms match'><input id='radio_filter_heard_in_mod_any' type='radio' value='any' name='filter_heard_in_mod'".($this->filter_heard_in_mod!="all" ? " checked" : "").($this->filter_listener || !$this->filter_heard_in ? " disabled" : "").">Any</label></td>\n"
            ."            <td width='55'><label for='radio_filter_heard_in_mod_all' title='Show where all terms match'><input id='radio_filter_heard_in_mod_all' type='radio' value='all' name='filter_heard_in_mod'".($this->filter_heard_in_mod=="all" ? " checked" : "").($this->filter_listener || !$this->filter_heard_in ? " disabled" : "").">All</label></td>\n"
            ."          </tr>\n"
            ."	 </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Last Heard</th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td><input title='Enter a start date to show only signals last heard after this date (YYYY-MM-DD format)' type='text' name='filter_date_1' size='10' maxlength='10' value='".($this->filter_date_1 != "1900-01-01" ? $this->filter_date_1 : "")."' class='formfield'> -\n"
            ."<input title='Enter an end date to show only signals last heard before this date (YYYY-MM-DD format)' type='text' name='filter_date_2' size='10' maxlength='10' value='".($this->filter_date_2 != "2020-01-01" ? $this->filter_date_2 : "")."' class='formfield'></td>"
            ."            <td align='right'><b>Offsets</b> <select name='offsets' class='formField'>\n"
            ."<option value=''".($this->offsets=="" ? " selected" : "") .">Relative</option>\n"
            ."<option value='abs'".($this->offsets=="" ? "" : " selected") .">Absolute</option>\n"
            ."</select></td>"
            ."          </tr>\n"
            ."	 </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Sort By</th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td><select name='sort_by_column' class='fixed'>\n"
            ."<option value='khz'".($this->sort_by=="khz" || $this->sort_by=="khz_d" ? " selected" : "") .">KHz - Nominal carrier</option>\n"
            ."<option value='call'".($this->sort_by=="call" || $this->sort_by=="call_d" ? " selected" : "") .">ID &nbsp;- Callsign or ID</option>\n"
            ."<option value='LSB'".($this->sort_by=="LSB" || $this->sort_by=="LSB_d" ? " selected" : "") .">LSB - Offset value in Hz</option>\n"
            ."<option value='USB'".($this->sort_by=="USB" || $this->sort_by=="USB_d" ? " selected" : "") .">USB - Offset value in Hz</option>\n"
            ."<option value='sec'".($this->sort_by=="sec" || $this->sort_by=="sec_d" ? " selected" : "") .">Sec - Cycle time in sec</option>\n"
            ."<option value='format'".($this->sort_by=="format" || $this->sort_by=="format_d" ? " selected" : "") .">Fmt - Signal Format</option>\n"
            ."<option value='QTH'".($this->sort_by=="QTH" || $this->sort_by=="QTH_d" ? " selected" : "") .">QTH - 'Name' and location</option>\n"
            ."<option value='sp'".($this->sort_by=="sp" || $this->sort_by=="sp_d" ? " selected" : "") .">S/P - State or Province</option>\n"
            ."<option value='itu'".($this->sort_by=="itu" || $this->sort_by=="itu_d" ? " selected" : "") .">ITU - Country code</option>\n"
            ."<option value='gsq'".($this->sort_by=="gsq" || $this->sort_by=="gsq_d" ? " selected" : "") .">GSQ - Grid Square</option>\n"
            ."<option value='pwr'".($this->sort_by=="pwr" || $this->sort_by=="pwr_d" ? " selected" : "") .">PWR - TX power in watts</option>\n"
            ."<option value='notes'".($this->sort_by=="notes" || $this->sort_by=="notes_d" ? " selected" : "") .">Notes column</option>\n"
            ."<option value='heard_in'".($this->sort_by=="heard_in" || $this->sort_by=="heard_in_d" ? " selected" : "") .">Heard In column</option>\n"
            ."<option value='logs'".($this->sort_by=="logs" || $this->sort_by=="logs_d" ? " selected" : "") .">Logs - Number of loggings</option>\n"
            ."<option value='last_heard'".($this->sort_by=="last_heard" || $this->sort_by=="last_heard_d" ? " selected" : "") .">Date last heard</option>\n"
            ."<option value='CLE64'".($this->sort_by=="CLE64" || $this->sort_by=="CLE64_d" ? " selected" : "") ." style='color: #ff0000;'>CLE64 - First letter / DX</option>\n"
            ."</select></td>"
            ."            <td width='45'><label for='sort_by_d'><input type='checkbox' id='sort_by_d' name='sort_by_d' value='_d'".(substr($this->sort_by, strlen($this->sort_by)-2, 2)=="_d" ? " checked" : "").">Z-A</label></td>"
            ."            <td align='right'><label for='chk_filter_active'><input id='chk_filter_active' type='checkbox' name='filter_active' value='1'".($this->filter_active ? " checked" : "").">Only active&nbsp;</label></td>"
            ."          </tr>\n"
            ."	 </table></td>"
            ."      </tr>\n"
            .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
            "      <tr class='rowForm'>\n"
            ."        <th align='left'>Admin:</th>\n"
            ."        <td nowrap><select name='filter_system' class='formField'>\n"
            ."<option value='1'".($this->filter_system=='1' ? " selected" : "").">RNA</option>\n"
            ."<option value='2'".($this->filter_system=='2' ? " selected" : "").">REU</option>\n"
            ."<option value='3'".($this->filter_system=='3' ? " selected" : "").">RWW</option>\n"
            ."<option value='not_logged'".($this->filter_system=='not_logged' ? " selected" : "").">Unlogged signals</option>\n"
            ."<option value='all'".($this->filter_system=='all' ? " selected" : "").">Show everything</option>\n"
            ."</select> Select system</td>\n"
            ."      </tr>"
            : ""
            )
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Custom Filter:</th>\n"
            ."        <td nowrap><select name='filter_custom' class='formField'>\n"
            ."<option value=''".($this->filter_custom=='' ? " selected" : "").">(None)</option>\n"
            ."<option value='cle160'".($this->filter_custom=='cle160' ? " selected" : "").">CLE160</option>\n"
            ."</select></td>\n"
            ."      </tr>"
            ."      <tr class='rowForm noprint'>\n"
            ."        <th colspan='2'><input type='submit' onclick='return send_form(form)' name='go' value='Go' style='width: 100px;' class='formButton' title='Execute search'>\n"
            ."<input name='clear' type='button' class='formButton' value='Clear' style='width: 100px;' onclick='clear_signal_list(document.form)'></th>"
            ."      </tr>\n"
            ."    </table></div></div></form>";
        return $html;
    }

    protected function drawHelp()
    {
        $html =
             "<ul>\n"
            ."  <li>Click on any station <b>ID</b> for details, <b>GSQ</b> for location map, <b>Heard In</b> list"
            ." for reception map and <b>Logs</b> value to see all logs for the station.</li>\n"
            ."  <li>To list different types of signals, check the boxes shown for 'Types' below."
            ." Inactive stations are normally shown at the end of the report.</li>\n"
            ."  <li>This report prints best in Landscape.</li>\n"
            ."</ul>\n";
        if ($this->type_NDB) {
            $html.=
                 "<h2>Reporting NDBs</h2>\n"
                ."<ul><li>Please use the following list as an additional data source -"
                ." the ship listings from around 404KHz may prove particularly useful:<br>\n"
                ."[ "
                ."<a href='http://www.dxinfocentre.com/ndb.htm' target='_blank'><b>William Hepburn's LF List</b></a>"
                ." ]</li>\n"
                ."</ul>\n";
        }
        if ($this->type_NAVTEX) {
            $html.=
                 "<h2>Reporting Navtex Stations</h2>\n"
                ."<ul>\n"
                ."  <li>Please use the following lists as your primary reference source -"
                ." these lists are very current and should be considered authorative:<br>\n"
                ."[ "
                ."<a href='http://www.dxinfocentre.com/navtex.htm' target='_blank'><b>William Hepburn's LF List</b></a>"
                ." | "
                ."<a href='http://www.dxinfocentre.com/maritimesafetyinfo.htm' target='_blank'>"
                ."<b>William Hepburn's HF List</b></a> ]</li>\n"
                ."</ul>\n";
        }
        if ($this->type_HAMBCN) {
            $html.=
                 "<h2>Reporting Ham Beacons</h2>\n"
                ."<ul>\n"
                ."  <li>Please use the following lists as your primary reference source -"
                ." these lists are very current and should be considered authorative:<br>\n"
                ."[ "
                ."<a href='http://www.lwca.org/sitepage/part15' target='_blank'><b>LOWFERS</b></a>"
                ." | "
                ."<a href='http://www.keele.ac.uk/depts/por/28.htm' target='_blank'><b>HF</b></a>"
                ." | "
                ."<a href='http://www.keele.ac.uk/depts/por/50.htm' target='_blank'><b>50MHz</b></a>"
                ." ]</li>\n"
                ."</ul>\n";
        }
        if ($this->type_DGPS) {
            $html.=
                 "<h2>Reporting DGPS Stations</h2>\n"
                ."<ul>\n"
                ."  <li>Please use the following lists as your primary reference source -"
                ." these lists are very current and should be considered authorative:<br>\n"
                ."[ "
                ."<a href='http://www.ndblist.info/dgnavinfo/datamodes/worldDGPSdatabase.pdf' target='_blank'>"
                ."<b>NDB List PDF (by Frequency)</b></a>"
                ." | "
                ."<a href='http://www.navcen.uscg.gov/?pageName=dgpsSiteInfo&All' target='_blank'>"
                ."<b>USCG DGPS Site List</b></a>"
                ." ]</li>\n"
                ."</ul>\n";
        }
        return $html;
    }

    protected function drawListenerStats()
    {
        return
             "<div class='form_box shadow'>\n"
            ."  <div class='header'>".system." Listeners</div>\n"
            ."  <div class='body rowForm'>\n"
            ."    <table cellpadding='2' cellspacing='0' border='10' class='tableForm' style='width:180px'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Locations</th>\n"
            ."        <td align='right'>".$this->stats['locations']."</td>\n"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Loggings</th>\n"
            ."        <td align='right'>".$this->stats['logs']."</td>\n"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>First log</th>\n"
            ."        <td align='right'>".$this->stats['first_log']."</td>\n"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Last log</th>\n"
            ."        <td align='right'>".$this->stats['last_log']."</td>\n"
            ."      </tr>\n"
            ."    </table>\n"
            ."  </div>\n"
            ."</div>";
    }

    protected function drawSignalStats()
    {
        return
             "<div class='form_box shadow'>\n"
            ."  <div class='header'>Signals</div>\n"
            ."  <div class='body rowForm'>\n"
            ."    <table cellpadding='2' cellspacing='0' border='10' class='tableForm' style='width:180px'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>RNA only</th>\n"
            ."        <td align='right'>".$this->stats['RNA_only']."</td>\n"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>REU only</th>\n"
            ."        <td align='right'>".$this->stats['REU_only']."</td>\n"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>RNA + REU</th>\n"
            ."        <td align='right'>".$this->stats['RNA_and_REU']."</td>\n"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>RWW</th>\n"
            ."        <td align='right'>".$this->stats['RWW']."</td>\n"
            ."      </tr>\n"
            .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
                 "      <tr class='rowForm'>\n"
                ."        <th align='left'>Unassigned signals</th>\n"
                ."        <td align='right'>".$this->stats['Unassigned']."</td>\n"
                ."      </tr>\n"
             :
                ""
            )
            ."    </table>\n"
            ."  </div>\n"
            ."</div>";
    }

    protected function drawVisitorPoll()
    {
        $Obj = new Poll;
        return
             "<div class='form_box shadow'>\n"
            ."  <div class='header'>Visitor's Poll</div>\n"
            ."  <div class='body rowForm'>"
            ."    <table cellpadding='2' cellspacing='0' border='1' class='tableForm' style='width:180px'>\n"
            ."      <tr>\n"
            ."        <td>\n"
            .$Obj->draw()
            ."        </td>\n"
            ."      </tr>\n"
            ."    </table>\n"
            ."  </div>\n"
            ."</div>";
    }

    protected function getCountLocations()
    {
        $this->stats['locations'] =    listener_get_count($this->region);
    }

    protected function getCountLogs()
    {
        switch ($this->filter_system) {
            case "1":
                $filter =   "`region` = 'na'";
                break;
            case "2":
                $filter =   "`region` = 'eu'";
                break;
            case "3":
                $filter =   ($this->region!="" && $this->region!="na" ? "`region` = '".$this->region."'" : "1");
                break;
            case "all":
                $filter = "1";
                break;
            case "not_logged":
                $filter = "0";
                break;
        }
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    ".$filter;
        $this->stats['logs'] =    $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountMatched()
    {
        $sql = ($this->filter_heard_in || $this->filter_listener ?
                "SELECT\n"
               ."    COUNT(distinct `signals`.`ID`) AS `count`\n"
               ."FROM\n"
               ."    `signals`\n"
               ."INNER JOIN `logs` ON\n"
               ."    `signals`.`ID` = `logs`.`signalID`\n"
               ."WHERE\n"
               .$this->sql_filter_system
               .($this->sql_filter_heard_in ?   " AND\n".$this->sql_filter_heard_in     : "")
               .($this->sql_filter_listener ?   " AND\n".$this->sql_filter_listener     : "")
            :
                "SELECT\n"
               ."    COUNT(*) AS `count`\n"
               ."FROM\n"
               ."    `signals`\n"
               ."WHERE\n"
               .$this->sql_filter_system
            )
            .($this->sql_filter_active ?        " AND\n".$this->sql_filter_active       : "")
            .($this->sql_filter_channels ?      " AND\n".$this->sql_filter_channels     : "")
            .($this->sql_filter_continent ?     " AND\n".$this->sql_filter_continent    : "")
            .($this->sql_filter_custom ?        " AND\n".$this->sql_filter_custom       : "")
            .($this->sql_filter_frequency ?     " AND\n".$this->sql_filter_frequency    : "")
            .($this->sql_filter_id ?            " AND\n".$this->sql_filter_id           : "")
            .($this->sql_filter_itu ?           " AND\n".$this->sql_filter_itu          : "")
            .($this->sql_filter_last_heard ?    " AND\n".$this->sql_filter_last_heard   : "")
            .($this->sql_filter_range_min ?     " AND\n".$this->sql_filter_range_min    : "")
            .($this->sql_filter_range_max ?     " AND\n".$this->sql_filter_range_max    : "")
            .($this->sql_filter_sp ?            " AND\n".$this->sql_filter_sp           : "")
            .($this->sql_filter_type ?          " AND\n".$this->sql_filter_type         : "")
            ;
//        z($sql);die;
        $this->total = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsREUOnly()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=1 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=0 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $this->stats['REU_only'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsRNAOnly()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=0 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=1 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $this->stats['RNA_only'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsRNAAndREU()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_eu`=1 AND\n"
            ."    `heard_in_na`=1\n"
            .")";
        $this->stats['RNA_and_REU'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsRWW()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE\n"
            ."    `logs` > 0";
        $this->stats['RWW'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsUnassigned()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=0 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=0 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $this->stats['Unassigned'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getDateFirstAndLastLogs()
    {
        $filter = "1";
        switch ($this->filter_system) {
            case "1":
                $filter =
                    "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `heard_in` = 'hi'))";
                break;
            case "2":
                $filter =
                    "(`region` = 'eu')";
                break;
            case "3":
                if ($this->region!="") {
                    $filter =
                        "(`region` = '".$this->region."')";
                }
                break;
        }
        $sql =
             "SELECT\n"
            ."    DATE_FORMAT(MIN(`date`),'%e %b %Y') AS `first_log`,\n"
            ."    DATE_FORMAT(MAX(`date`),'%e %b %Y') AS `last_log`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    ".$filter." AND\n"
            ."    `date` !=\"\" AND\n"
            ."    `date` !=\"0000-00-00\"";
        $row = $this->ObjSignal->getRecordForSql($sql);
        $this->stats = array_merge($this->stats, $row);
    }

    protected function getResultsMatched()
    {
        $sql =
             "SELECT\n"
            .($this->filter_heard_in || $this->filter_listener ?
                ($this->filter_listener ?
                     "    DISTINCT `signals`.*,\n"
                    ."   `logs`.`dx_km`,\n"
                    ."   `logs`.`dx_miles`\n"
                 :
                     "    DISTINCT `signals`.*"
                )
                .($this->filter_dx_gsq ? ",\n".$this->getSqlDxColumns() : "\n")
                ."FROM\n"
                ."    `signals`\n"
                ."INNER JOIN `logs` ON\n"
                ."    `signals`.`ID` = `logs`.`signalID`\n"
                ."WHERE\n"
                .$this->sql_filter_system
                .($this->filter_heard_in ?  " AND\n".$this->sql_filter_heard_in : "")
                .($this->filter_listener ?  " AND\n".$this->sql_filter_listener : "")
               :
                 "    `signals`.*"
                .($this->filter_dx_gsq ? ",\n".$this->getSqlDxColumns() : "\n")
                ."FROM\n"
                ."  `signals`\n"
                ."WHERE\n"
                ."  ".$this->sql_filter_system
            )
            .($this->sql_filter_active ?        " AND\n".$this->sql_filter_active       : "")
            .($this->sql_filter_channels ?      " AND\n".$this->sql_filter_channels     : "")
            .($this->sql_filter_continent ?     " AND\n".$this->sql_filter_continent    : "")
            .($this->sql_filter_custom ?        " AND\n".$this->sql_filter_custom       : "")
            .($this->sql_filter_frequency ?     " AND\n".$this->sql_filter_frequency    : "")
            .($this->sql_filter_id ?            " AND\n".$this->sql_filter_id           : "")
            .($this->sql_filter_itu ?           " AND\n".$this->sql_filter_itu          : "")
            .($this->sql_filter_last_heard ?    " AND\n".$this->sql_filter_last_heard   : "")
            .($this->sql_filter_range_min ?     " AND\n".$this->sql_filter_range_min    : "")
            .($this->sql_filter_range_max ?     " AND\n".$this->sql_filter_range_max    : "")
            .($this->sql_filter_sp ?            " AND\n".$this->sql_filter_sp           : "")
            .($this->sql_filter_type ?          " AND\n".$this->sql_filter_type         : "")
            .($this->sql_sort_by ?              "\nORDER BY\n  ".$this->sql_sort_by : "")
            .($this->limit!=-1 ?                "\nLIMIT\n  ".$this->offset.", ".$this->limit : "");
//        z($sql);
        $result =   mysql_query($sql);
        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $this->rows[] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
    }

    protected function getSqlDxColumns()
    {
        return
           "    CAST(\n"
          ."        COALESCE(\n"
          ."            ROUND(\n"
          ."                DEGREES(\n"
          ."                    ACOS(\n"
          ."                        (\n"
          ."                            SIN(RADIANS(".$this->filter_dx_lat.")) *\n"
          ."                            SIN(RADIANS(signals.lat))\n"
          ."                        ) + (\n"
          ."                            COS(RADIANS(".$this->filter_dx_lat.")) *\n"
          ."                            COS(RADIANS(signals.lat)) *\n"
          ."                            COS(RADIANS(".$this->filter_dx_lon." - signals.lon))\n"
          ."                        )\n"
          ."                    )\n"
          ."                ) * 69,\n"
          ."                2\n"
          ."            ),\n"
          ."            ''\n"
          ."        ) AS UNSIGNED\n"
          ."    ) AS `range_dx_miles`,\n"
          ."    CAST(\n"
          ."        COALESCE(\n"
          ."            ROUND(\n"
          ."                DEGREES(\n"
          ."                    ACOS(\n"
          ."                        (\n"
          ."                            SIN(RADIANS(".$this->filter_dx_lat.")) * \n"
          ."                            SIN(RADIANS(signals.lat))\n"
          ."                        ) + (\n"
          ."                            COS(RADIANS(".$this->filter_dx_lat.")) * \n"
          ."                            COS(RADIANS(signals.lat)) * \n"
          ."                            COS(RADIANS(".$this->filter_dx_lon." - signals.lon))\n"
          ."                        )\n"
          ."                    )\n"
          ."                ) * 111.05,\n"
          ."                2\n"
          ."            ),\n"
          ."            ''\n"
          ."        ) AS UNSIGNED\n"
          ."    ) AS `range_dx_km`,\n"
          ."    CAST(\n"
          ."        COALESCE(\n"
          ."            ROUND(\n"
          ."                (\n"
          ."                    DEGREES(\n"
          ."                        ATAN2(\n"
          ."                            (\n"
          ."                                SIN(\n"
          ."                                    RADIANS(signals.lon) -\n"
          ."                                    RADIANS(".$this->filter_dx_lon.")\n"
          ."                                ) *\n"
          ."                                COS(RADIANS(signals.lat))\n"
          ."                            ),\n"
          ."                            (\n"
          ."                                (\n"
          ."                                    COS(RADIANS(".$this->filter_dx_lat.")) *\n"
          ."                                    SIN(RADIANS(signals.lat))\n"
          ."                                ) - SIN(RADIANS(".$this->filter_dx_lat.")) *\n"
          ."                                COS(RADIANS(signals.lat)) *\n"
          ."                                COS(\n"
          ."                                    RADIANS(signals.lon) -\n"
          ."                                    RADIANS(".$this->filter_dx_lon.")\n"
          ."                                )\n"
          ."                            )\n"
          ."                        )\n"
          ."                    ) +\n"
          ."                    360\n"
          ."                )\n"
          ."                MOD 360\n"
          ."            ),\n"
          ."            ''\n"
          ."        ) AS UNSIGNED\n"
          ."    ) AS `range_dx_deg`\n";
    }

    protected function setup()
    {
        $this->ObjSignal = new Signal;
        $this->setupDoSubmode();
        $this->setupLoadVars();
        $this->setupTweakVars();
        $this->setupLoadStats();
        $this->setupInitListenersListFilter();
        $this->setupInitSql();
        $this->getCountMatched();
        $this->getResultsMatched();
    }

    protected function setupDoSubmode()
    {
        if (!isset($_COOKIE['cookie_admin'])) {
            return;
        }
        if ($_COOKIE['cookie_admin']!=admin_session) {
            return;
        }
        switch ($this->submode){
            case "delete":
                $sql =      "DELETE FROM `logs` WHERE `signalID` = ".$this->targetID;
                mysql_query($sql);
                $sql =  "   DELETE FROM `signals` WHERE `ID` = ".$this->targetID;
                mysql_query($sql);
                break;
        }
    }

    protected function setupInitListenersListFilter()
    {
        switch ($this->filter_system) {
            case "1":
                $this->listeners_list_filter =
                    "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `SP` = 'hi'))";
                break;
            case "2":
                $this->listeners_list_filter =
                    "(`region` = 'eu')";
                break;
            case "3":
                if ($this->region!="") {
                    $this->listeners_list_filter =
                        "(`region` = '".$this->region."')";
                } else {
                    $this->listeners_list_filter =
                        "1";
                }
                break;
            case "not_logged":
                $this->listeners_list_filter =
                    "0";
                break;
            case "all":
                $this->listeners_list_filter =
                    "1";
                break;
        }
    }

    protected function setupInitSql()
    {
        $this->setupInitSqlFilterActive();
        $this->setupInitSqlFilterChannels();
        $this->setupInitSqlFilterContinent();
        $this->setupInitSqlFilterCustom();
        $this->setupInitSqlFilterFrequency();
        $this->setupInitSqlFilterHeardIn();
        $this->setupInitSqlFilterId();
        $this->setupInitSqlFilterITU();
        $this->setupInitSqlFilterLastHeard();
        $this->setupInitSqlFilterListener();
        $this->setupInitSqlFilterRangeMax();
        $this->setupInitSqlFilterRangeMin();
        $this->setupInitSqlFilterSP();
        $this->setupInitSqlFilterSystem();
        $this->setupInitSqlFilterType();
        $this->setupInitSqlSortBy();

    }

    protected function setupInitSqlFilterActive()
    {
        if ($this->filter_active) {
            $this->sql_filter_active = "    (`active` = 1)";
        }
    }

    protected function setupInitSqlFilterChannels()
    {
        switch ($this->filter_channels) {
            case 1:
                $this->sql_filter_channels =    "    (MOD(`khz`* 1000, 1000) = 0)";
                break;
            case 2:
                $this->sql_filter_channels =    "    (MOD(`khz`* 1000, 1000) != 0)";
                break;
        }
    }

    protected function setupInitSqlFilterCustom()
    {
        switch ($this->filter_custom){
            case 'cle160':
                $itu_codes =
                     'ABW AFG AFS AGL AIA ALB ALG ALS AND ANI AOE ARG ARM ARS ASC ATA ATG ATN AUI AUS AUT AZE AZR '
                    .'BAH BAL BAR BDI BEL BEN BER BFA BGD BHR BIH BLR BLZ BOL BOT BRA BRB BRI BRM BRU BTN BUL '
                    .'CAB CAF CBG CEU CHL CHN CHR CKH CKS CLI CLM CLN CME CNR COD COG COM COR CPV CTI CTR CUB '
                    .'CVA CYM CYP CZE';
                $sp_codes =
                    'AB AK AL AR AT AZ BC CA CO CT';
                $this->sql_filter_custom =
                     "    (`signals`.`ITU` IN('"
                    .implode("','", explode(' ', $itu_codes))
                    ."') OR `signals`.`SP` IN('"
                    .implode("','", explode(' ', $sp_codes))
                    ."'))";
                break;
        }
    }

    protected function setupInitSqlFilterFrequency()
    {
        if (!$this->filter_khz_2) {
            return;
        }
        $this->sql_filter_frequency =
            "    (`khz` >= ".$this->filter_khz_1." AND `khz` <= ".$this->filter_khz_2.")";
    }

    protected function setupInitSqlFilterHeardIn()
    {
        if ($this->filter_heard_in=='') {
            return;
        }
        $this->sql_filter_heard_in =    explode(" ", strToUpper($this->filter_heard_in));
        if ($this->filter_heard_in_mod=="all") {
            $this->sql_filter_heard_in =
                "    (`signals`.`heard_in` LIKE '%"
                .implode($this->sql_filter_heard_in, "%' AND `signals`.`heard_in` LIKE '%")
                ."%')";
            return;
        }
        $this->sql_filter_heard_in =
            "    (`logs`.`heard_in` IN('"
            .implode($this->sql_filter_heard_in, "','")
            ."'))";
    }

    protected function setupInitSqlFilterId()
    {
        if ($this->filter_id===false) {
            return;
        }
        $this->sql_filter_id = "    (`signals`.`call` LIKE \"%".$this->filter_id."%\")";
    }

    protected function setupInitSqlFilterItu()
    {
        if (!$this->filter_itu) {
            return;
        }
        $tmp =        explode(" ", $this->filter_itu);
        sort($tmp);
        $this->filter_itu =     implode(" ", $tmp);
        $this->sql_filter_itu = explode(" ", $this->filter_itu);
        $this->sql_filter_itu = "    (`signals`.`ITU` IN('".implode($this->sql_filter_itu, "', '")."'))";
    }

    protected function setupInitSqlFilterLastHeard()
    {
        if (!$this->filter_date_1) {
            return;
        }
        $this->sql_filter_last_heard =
            "    (`last_heard` >= \"".$this->filter_date_1."\" AND `last_heard` <= \"".$this->filter_date_2."\")";
    }

    protected function setupInitSqlFilterListener()
    {
        if (!$this->filter_listener) {
            return;
        }
        $this->sql_filter_listener =
            "    (`logs`.`listenerID` IN(".implode(',', $this->filter_listener)."))";
    }

    protected function setupInitSqlFilterRangeMax()
    {
        if (!$this->filter_by_range || !$this->filter_dx_max) {
            return;
        }
        $this->sql_filter_range_max =
             "    (ROUND(\n"
            ."        DEGREES(\n"
            ."            ACOS(\n"
            ."                (\n"
            ."                    SIN(RADIANS(".$this->filter_dx_lat.")) *\n"
            ."                    SIN(RADIANS(signals.lat))\n"
            ."                ) + (\n"
            ."                    COS(RADIANS(".$this->filter_dx_lat.")) *\n"
            ."                    COS(RADIANS(signals.lat)) *\n"
            ."                    COS(RADIANS(".$this->filter_dx_lon." - signals.lon))\n"
            ."                )\n"
            ."            )\n"
            ."        ) * "
            .($this->filter_dx_units=="km" ? "111.05" : "69")
            .",\n"
            ."        2\n"
            ."    ) < ".$this->filter_dx_max.")";
    }

    protected function setupInitSqlFilterRangeMin()
    {
        if (!$this->filter_by_range || !$this->filter_dx_min) {
            return;
        }
        $this->sql_filter_range_min =
             "    (ROUND(\n"
            ."        DEGREES(\n"
            ."            ACOS(\n"
            ."                (\n"
            ."                    SIN(RADIANS(".$this->filter_dx_lat.")) *\n"
            ."                    SIN(RADIANS(signals.lat))\n"
            ."                ) + (\n"
            ."                    COS(RADIANS(".$this->filter_dx_lat.")) *\n"
            ."                    COS(RADIANS(signals.lat)) *\n"
            ."                    COS(RADIANS(".$this->filter_dx_lon." - signals.lon))\n"
            ."                )\n"
            ."            )\n"
            ."        ) * "
            .($this->filter_dx_units=="km" ? "111.05" : "69")
            .",\n"
            ."        2\n"
            ."    ) > ".$this->filter_dx_min.")";

    }
    protected function setupInitSqlFilterContinent()
    {
        if (!$this->filter_continent) {
            return;
        }
        $this->sql_filter_continent =
             "    (`signals`.`ITU` IN(\n"
            ."        SELECT `ITU` FROM `itu` WHERE `region` = '".$this->filter_continent."')\n"
            ."    )";
    }

    protected function setupInitSqlFilterSP()
    {
        if (!$this->filter_sp) {
            return;
        }
        $tmp =        explode(" ", $this->filter_sp);
        sort($tmp);
        $this->filter_sp =  implode(" ", $tmp);
        $this->sql_filter_sp =    explode(" ", $this->filter_sp);
        $this->sql_filter_sp =    "    (`signals`.`SP` IN('".implode($this->sql_filter_sp, "', '")."'))";
    }

    protected function setupInitSqlFilterSystem()
    {
        switch ($this->filter_system) {
            case "1":
                $this->sql_filter_system =
                    "    (`heard_in_na` = 1 OR `heard_in_ca` = 1)";
                break;
            case "2":
                $this->sql_filter_system =
                    "    (`heard_in_eu` = 1)";
                break;
            case "3":
                if ($this->region!="") {
                    $this->sql_filter_system =
                        "    (`heard_in_".$this->region."` = 1)";
                } else {
                    $this->sql_filter_system =
                        "    (1)";
                }
                break;
            case "not_logged":
                $this->sql_filter_system =
                     "    (`heard_in_af` = 0 AND `heard_in_as` = 0 AND `heard_in_ca` = 0 AND `heard_in_eu` = 0 AND\n"
                    ."`heard_in_iw` = 0 AND `heard_in_na` = 0 AND `heard_in_oc` = 0 AND `heard_in_sa` = 0)";
                break;
            case "all":
                $this->sql_filter_system =
                    "    (1)";
                break;
        }

    }

    protected function setupInitSqlFilterType()
    {
        $filter_type =    array();
        if ($this->type_NDB) {
            $filter_type[] =     NDB;
        }
        if ($this->type_DGPS) {
            $filter_type[] =     DGPS;
        }
        if ($this->type_TIME) {
            $filter_type[] =     TIME;
        }
        if ($this->type_NAVTEX) {
            $filter_type[] =     NAVTEX;
        }
        if ($this->type_HAMBCN) {
            $filter_type[] =     HAMBCN;
        }
        if ($this->type_OTHER) {
            $filter_type[] =     OTHER;
        }
        if ($this->type_DSC) {
            $filter_type[] =     DSC;
        }
        $this->sql_filter_type =
            "    (`type` IN("
           .implode($filter_type, ",")
           ."))";
    }

    protected function setupInitSqlSortBy()
    {
        switch ($this->sort_by) {
            case "call":
                $this->sql_sort_by =
                    "`active` DESC, `call` ASC, `khz` ASC";
                break;
            case "call_d":
                $this->sql_sort_by =
                    "`active` DESC, `call` DESC, `khz` ASC";
                break;
            case "dx":
                $this->sql_sort_by =
                    "`active` DESC, `dx_km` ASC";
                break;
            case "dx_d":
                $this->sql_sort_by =
                    "`active` DESC, `dx_km` DESC";
                break;
            case "dx_deg":
                $this->sql_sort_by =
                    "`active` DESC, CAST(`dx_range` AS UNSIGNED) ASC";
                break;
            case "dx_deg_d":
                $this->sql_sort_by =
                    "`active` DESC, CAST(`dx_range` AS UNSIGNED) DESC";
                break;
            case "format":
                $this->sql_sort_by =
                    "`active` DESC, `signals`.`format`='' OR `signals`.`format` IS NULL, `signals`.`format` ASC";
                break;
            case "format_d":
                $this->sql_sort_by =
                    "`active` DESC, `format`='' OR `format` IS NULL, `format` DESC";
                break;
            case "gsq":
                $this->sql_sort_by =
                    "`active` DESC, `GSQ` ASC";
                break;
            case "gsq_d":
                $this->sql_sort_by =
                    "`active` DESC, `GSQ` DESC";
                break;
            case "heard_in":
                $this->sql_sort_by =
                    "`active` DESC, `heard_in` ASC, `khz` ASC, `call` ASC";
                break;
            case "heard_in_d":
                $this->sql_sort_by =
                    "`active` DESC, `heard_in` DESC, `khz` ASC, `call` ASC";
                break;
            case "itu":
                $this->sql_sort_by =
                    "`active` DESC, `ITU` ASC, `SP` ASC, `khz` ASC, `call` ASC";
                break;
            case "itu_d":
                $this->sql_sort_by =
                    "`active` DESC, `ITU` DESC, `SP` ASC, `khz` ASC, `call` ASC";
                break;
            case "khz":
                $this->sql_sort_by =
                    "`active` DESC, `khz` ASC, `call` ASC";
                break;
            case "khz_d":
                $this->sql_sort_by =
                    "`active` DESC, `khz` DESC, `call` ASC";
                break;
            case "last_heard":
                $this->sql_sort_by =
                    "`active` DESC, `last_heard` IS NULL, `last_heard` ASC";
                break;
            case "last_heard_d":$this->sql_sort_by =    "`active` DESC, `last_heard` IS NULL, `last_heard` DESC";
                break;
            case "logs":        $this->sql_sort_by =    "`active` DESC, `logs` IS NULL, `logs` ASC";
                break;
            case "logs_d":        $this->sql_sort_by =    "`active` DESC, `logs` IS NULL, `logs` DESC";
                break;
            case "LSB":
                if ($this->offsets=='') {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`LSB` ASC";
                } else {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`khz`-(`signals`.`LSB`/1000) ASC";
                }
                break;
            case "LSB_d":
                if ($this->offsets=='') {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`LSB` DESC";
                } else {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`khz`-(`signals`.`LSB`/1000) DESC";
                }
                break;
            case "notes":
                $this->sql_sort_by =
                    "`active` DESC, `notes`='' OR `notes` IS NULL, `notes` ASC";
                break;
            case "notes_d":
                $this->sql_sort_by =
                    "`active` DESC, `notes`='' OR `notes` IS NULL, `notes` DESC";
                break;
            case "QTH":
                $this->sql_sort_by =
                    "`active` DESC, `QTH`='' OR `QTH` IS NULL, `QTH` ASC";
                break;
            case "QTH_d":
                $this->sql_sort_by =
                    "`active` DESC, `QTH`='' OR `QTH` IS NULL, `QTH` DESC";
                break;
            case "pwr":
                $this->sql_sort_by =
                    "`active` DESC, `pwr`=0, `pwr` ASC";
                break;
            case "pwr_d":
                $this->sql_sort_by =
                    "`active` DESC, `pwr`=0, `pwr` DESC";
                break;
            case "range_dx_km":
                $this->sql_sort_by =
                    "`active` DESC, `lat` IS NULL, `range_dx_km` ASC";
                break;
            case "range_dx_km_d":
                $this->sql_sort_by =
                    "`active` DESC, `lat` IS NULL, `range_dx_km` DESC";
                break;
            case "range_dx_deg":
                $this->sql_sort_by =
                    "`active` DESC, `lat` IS NULL, CAST(`range_dx_deg` AS UNSIGNED) ASC";
                break;
            case "range_dx_deg_d":
                $this->sql_sort_by =
                    "`active` DESC, `lat` IS NULL, CAST(`range_dx_deg` AS UNSIGNED) DESC";
                break;
            case "sec":
                $this->sql_sort_by =
                    "`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL, CAST(`signals`.`sec` AS UNSIGNED) ASC";
                break;
            case "sec_d":
                $this->sql_sort_by =
                    "`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL, CAST(`signals`.`sec` AS UNSIGNED) DESC";
                break;
            case "sp":
                $this->sql_sort_by =
                    "`active` DESC, `SP`='' or `SP` IS NULL,`SP` ASC,`ITU` ASC, `khz` ASC, `call` ASC";
                break;
            case "sp_d":
                $this->sql_sort_by =
                    "`active` DESC, `SP`='' or `SP` IS NULL,`SP` DESC,`ITU` ASC, `khz` ASC, `call` ASC";
                break;
            case "USB":
                if ($this->offsets=='') {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`USB` ASC";
                } else {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`khz`+(`signals`.`USB`/1000) ASC";
                }
                break;
            case "USB_d":
                if ($this->offsets=='') {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`USB` DESC";
                } else {
                    $this->sql_sort_by =
                        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`khz`+(`signals`.`USB`/1000) DESC";
                }
                break;
            case "CLE64":
                if ($this->filter_dx_gsq) {
                    $this->sql_sort_by =
                        "`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)>='A' DESC, LEFT(`signals`.`call`,1) ASC, `range_dx_km` DESC";
                }
                break;
            case "CLE64_d":
                if ($this->filter_dx_gsq) {
                    $this->sql_sort_by =
                        "`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)<='Z' ASC, LEFT(`signals`.`call`,1) DESC, `range_dx_km` DESC";
                }
                break;
        }
        if ($this->filter_id) {
            $this->sql_sort_by =    "`call` = '".$this->filter_id."' DESC, ".$this->sql_sort_by;
        }
    }

    protected function setupLoadStats()
    {
        $this->getCountSignalsRNAOnly();
        $this->getCountSignalsREUOnly();
        $this->getCountSignalsRNAAndREU();
        $this->getCountSignalsRWW();
        $this->getCountSignalsUnassigned();
        $this->getCountLocations();
        $this->getCountLogs();
        $this->getDateFirstAndLastLogs();
    }


    protected function setupLoadVars()
    {
        global $mode;
        $this->mode =                   $mode;
        $this->submode =                get_var('submode');
        $this->targetID =               (int)get_var('targetID');
        $this->filter_active =          get_var('filter_active');
        $this->filter_channels =        get_var('filter_channels');
        $this->filter_continent =       get_var('filter_continent');
        $this->filter_custom =          get_var('filter_custom');
        $this->filter_date_1 =          get_var('filter_date_1');
        $this->filter_date_2 =          get_var('filter_date_2');
        $this->filter_dx_gsq =          get_var('filter_dx_gsq');
        $this->filter_dx_max =          get_var('filter_dx_max');
        $this->filter_dx_min =          get_var('filter_dx_min');
        $this->filter_dx_units =        get_var('filter_dx_units', 'km');
        $this->filter_heard_in =        get_var('filter_heard_in');
        $this->filter_heard_in_mod =    get_var('filter_heard_in_mod');
        $this->filter_id =              strToUpper(get_var('filter_id'));
        $this->filter_itu =             strToUpper(get_var('filter_itu'));
        $this->filter_khz_1 =           get_var('filter_khz_1');
        $this->filter_khz_2 =           get_var('filter_khz_2');
        $this->filter_listener =        get_var('filter_listener');
        $this->filter_system =          get_var('filter_system');
        $this->filter_sp =              strToUpper(get_var('filter_sp'));
        $this->offsets =                get_var('offsets');
        $this->region =                 get_var('region');

        $this->limit =                  (int)get_var('limit', 50);
        $this->offset =                 (int)get_var('offset', 0);
        $this->sort_by =                get_var('sort_by');

        $this->type_NDB =               get_var('type_NDB');
        $this->type_TIME =              get_var('type_TIME');
        $this->type_DGPS =              get_var('type_DGPS');
        $this->type_DSC =               get_var('type_DSC');
        $this->type_NAVTEX =            get_var('type_NAVTEX');
        $this->type_HAMBCN =            get_var('type_HAMBCN');
        $this->type_OTHER =             get_var('type_OTHER');
    }

    protected function setupTweakVars()
    {
        if ($this->filter_date_1 || $this->filter_date_2) {
            if ($this->filter_date_1 == "") {
                $this->filter_date_1 = "1900-01-01";
            }
            if ($this->filter_date_2 == "") {
                $this->filter_date_2 = "2020-01-01";
            }
        }

        $this->filter_dx_gsq =
             strtoUpper(substr($this->filter_dx_gsq, 0, 4))
            .strtoLower(substr($this->filter_dx_gsq, 4, 2));

        if ($this->filter_dx_gsq) {
            $a =    GSQ_deg($this->filter_dx_gsq);
            $this->filter_dx_lat =    $a["lat"];
            $this->filter_dx_lon =    $a["lon"];
        }

        $this->filter_by_range = ($this->filter_dx_gsq && ($this->filter_dx_min || $this->filter_dx_max));

        if ($this->filter_khz_1 || $this->filter_khz_2) {
            if ($this->filter_khz_1 == "") {
                $this->filter_khz_1 = 0;
            }
            if ($this->filter_khz_2 == "") {
                $this->filter_khz_2 = 1000000;
            }
            $this->filter_khz_1 =    (float)$this->filter_khz_1;
            $this->filter_khz_2 =    (float)$this->filter_khz_2;
        }

        if ($this->filter_heard_in=="(All States and Countries)") {
            $this->filter_heard_in="";
        }

        if ($this->filter_heard_in) {
            $tmp =        explode(" ", strToUpper($this->filter_heard_in));
            sort($tmp);
            $this->filter_heard_in =    implode(" ", array_unique($tmp));
        }

        if ($this->filter_id && substr($this->filter_id, 0, 1)=="#") {
            $this->type_DGPS=1;
        }

        if ($this->filter_id && substr($this->filter_id, 0, 1)=="$") {
            $this->type_NAVTEX=1;
        }

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

        if (
            !(
                $this->filter_listener &&
                is_array($this->filter_listener)
                && count($this->filter_listener)
                && $this->filter_listener[0]
            )
        ) {
            $this->filter_listener =    false;
        }
        if ($this->region=="") {
            switch (system) {
                case "REU":
                    $this->region = "eu";
                    break;
                case "RNA":
                    $this->region = "na";
                    break;
            }
        }
        if ($this->filter_system=="") {
            switch(system) {
                case "RNA":    $this->filter_system = 1;
                    break;
                case "REU":    $this->filter_system = 2;
                    break;
                case "RWW":    $this->filter_system = 3;
                    break;
            }
        }
        if (!$this->filter_by_range && ($this->sort_by == "range" || $this->sort_by == "range_d")) {
            $this->sort_by =    "khz";
        }
        if ($this->sort_by =='CLE64' and $this->filter_dx_gsq=='') {
            $this->sort_by = '';
        }
        if ($this->sort_by=="") {
            $this->sort_by = "khz";
        }

    }
}
