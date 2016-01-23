<?php

class SignalList
{
    public $html = '';
    public $head = '';
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
    protected $show;

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
            ."<div style='float:left;width:240px'>"
            .$this->drawStatsSignals()
            ."<br>\n"
            .$this->drawStatsListeners()
            ."<br>\n"
            .$this->drawVisitorPoll()
            ."</div>"
            ."<br style='clear:both' /><br />";
        if ($this->rows) {
            if ($this->show!='map') {
                $this->drawListing();
            } else {
                $this->drawMap();
            }
        } else {
            $this->html.=
                "<h2>Results</h2><br><br><h3>No results for search criteria</h3><br><br><br>\n";
        }
        $this->html.= $this->drawButtons();
    }

    protected function drawButtons()
    {
        $this->html .=
             "<p class='noprint buttons'>\n"
            ."<input type='button' value='Print...' onclick='"
            .(($this->limit!=-1 && $this->limit<$this->total) ?
                 "if (confirm("
                ."\"Information\\n\\nThis printout works best in Landscape.\\n\\n"
                ."You are not presently displaying all ".$this->total." available records.\\n"
                ."Continue anyway?\""
                .")) { window.print(); }": "window.print()"
            )."'/> "
            .(isAdmin() ?
             "<input type='button' value='Add signal...'"
                ." onclick=\"signal_add("
                ."\$('#filter_id').val(),\$('#filter_khz_1').val(), '', '', '', '', '',get_type(document.form)"
                .")\" /> "
            :
                ""
            )
            ."<input type='button' value='All ".system." > Excel'"
            ." title='Get the whole database as Excel' onclick='export_signallist_excel();' /> "
            ."<input type='button' value='All ".system." > PDF'"
            ." title='get the whole database as PDF' onclick='export_signallist_pdf();' /> "
            ."<input type='button' value='All ".system." > ILG'"
            ." title='get the whole database as ILGRadio format for Ham Radio Deluxe'"
            ." onclick='export_signallist_ilg()' /> "
            ."</p>\n";

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

    protected function drawControlColumnSortby()
    {
        $columns = array(
            'khz|KHz - Nominal carrier',
            'call|ID &nbsp;- Callsign or ID',
            'LSB|LSB - Offset value in Hz',
            'USB|USB - Offset value in Hz',
            'sec|Sec - Cycle time in sec',
            'format|Fmt - Signal Format',
            'QTH|QTH - \'Name\' and location',
            'sp|S/P - State or Province',
            'itu|ITU - Country code',
            'gsq|GSQ - Grid Square',
            'pwr|PWR - TX power in watts',
            'notes|Notes column',
            'heard_in|Heard In column',
            'logs|Logs - Number of loggings',
            'last_heard|Date last heard',
            'CLE64|CLE64 - First letter / DX from GSQ|#ff0000'
        );
        $html =
             "<select name='sort_by_column' id='sort_by_column' class='fixed'"
            ." onchange='signalsListChangeSortControl()'>\n";
        foreach ($columns as $column) {
            $col = explode('|', $column);
            $html.=
                 "  <option value='".$col[0]."'"
                .($this->sort_by==$col[0] || $this->sort_by==$col[0]."_d" ? " selected='selected'" : "")
                .(isset($col[2]) ? " style='color:".$col[2]."'" : "")
                .">".$col[1]."</option>\n";
        }
        $html.=
            "</select>";
        return $html;
    }

    protected function drawControlContinents()
    {
        return
             "<label style='display:inline-block; width:70px; margin:0.25em 0;' for='filter_continent'>"
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

    protected function drawControlDXMax()
    {
        return
             "<input title='Enter a value to show only signals up to this distance' type='text'"
            ." name='filter_dx_max' id='filter_dx_max' size='5' maxlength='5' value='".$this->filter_dx_max."'"
            ." onKeyUp='set_range(form)' onchange='set_range(form)'"
            .($this->filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")
            .">";
    }

    protected function drawControlDXMin()
    {
        return
             "<input title='Enter a value to show only signals equal or greater to this distance' type='text'"
            ." name='filter_dx_min' id='filter_dx_min' size='5' maxlength='5' value='".$this->filter_dx_min."'"
            ." onKeyUp='set_range(form)' onchange='set_range(form)'"
            .($this->filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")
            .">";
    }

    protected function drawControlDXUnits()
    {
        return
             "<input type='radio' id='filter_dx_units_km' name='filter_dx_units' value='km'"
            .($this->filter_dx_units=="km" ? " checked" : "")
            .($this->filter_dx_gsq && ($this->filter_dx_min || $this->filter_dx_max) ? "" : " disabled").">"
            ."<label for='filter_dx_units_km'>km</label> "
            ."<input type='radio' id='filter_dx_units_miles' name='filter_dx_units' value='miles'"
            .($this->filter_dx_units=="miles" ? " checked" : "")
            .($this->filter_dx_gsq && ($this->filter_dx_min || $this->filter_dx_max) ? "" : " disabled").">"
            ."<label for='filter_dx_units_miles'>miles</label>";
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

    protected function drawControlGSQ()
    {
        return
             "<input title='Enter a grid square to show only signals physically located between the distances chosen'"
            ." type='text' class='formfield' name='filter_dx_gsq' id='filter_dx_gsq' size='6' maxlength='6'"
            ." value='".$this->filter_dx_gsq."' onkeyup='set_range(form)' onchange='set_range(form)' />";
    }

    protected function drawControlHeardBy()
    {
        return
             "<select name='filter_listener[]' id='filter_listener' multiple class='formfield'"
            ." onchange='set_listener_and_heard_in(document.form)'"
            ." style='font-family: monospace; width:100%; height: 100px;'>\n"
            .get_listener_options_list(
                $this->listeners_list_filter,
                $this->filter_listener,
                "Anyone (or enter values in \"Heard here\" box)"
            )
            ."</select>";

    }

    protected function drawControlHeardIn()
    {
        return
             "<input type='text' name='filter_heard_in' id='filter_heard_in' size='41'"
            ." value='".($this->filter_heard_in ? strToUpper($this->filter_heard_in) : "(All States and Countries)")."'"
            .($this->filter_heard_in=="" ? "style='color: #0000ff' ":"")
            ."onclick=\"if(this.value=='(All States and Countries)') { this.value=''; this.style.color='#000000'}\"\n"
            ."onblur=\"if(this.value=='') { this.value='(All States and Countries)'; this.style.color='#0000ff';}\"\n"
            ."onchange='set_listener_and_heard_in(form)' onKeyUp='set_listener_and_heard_in(form)' "
            .($this->filter_listener ? "class='formfield_disabled' disabled" : "class='formfield'")
            .">";
    }

    protected function drawControlHeardInContinent()
    {
        return
             "<select name='region' id='region' onchange='document.form.go.disabled=1;document.form.submit()'"
            ." class='formField' style='width: 100%;'>\n"
            .get_region_options_list($this->region, "(All Continents)")
            ."</select>";

    }

    protected function drawControlHeardInMatch()
    {
        return
             "<input id='radio_filter_heard_in_mod_any' type='radio' value='any' name='filter_heard_in_mod'"
            .($this->filter_heard_in_mod!="all" ? " checked" : "")
            .($this->filter_listener || !$this->filter_heard_in ? " disabled" : "")
            .">"
            ."<label for='radio_filter_heard_in_mod_any' title='Show where any terms match'>Any</label>\n"
            ."<input id='radio_filter_heard_in_mod_all' type='radio' value='all' name='filter_heard_in_mod'"
            .($this->filter_heard_in_mod=="all" ? " checked" : "")
            .($this->filter_listener || !$this->filter_heard_in ? " disabled" : "")
            .">"
            ."<label for='radio_filter_heard_in_mod_all' title='Show where all terms match'>All</label>";
    }

    protected function drawControlId()
    {
        return
             "<input type='text' name='filter_id' id='filter_id' size='6' maxlength='12' value='".$this->filter_id."'"
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
                 "<label style='width:".$type[3]."%;' class='".strToLower($type[1])."'>"
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
            ."<input type='hidden' name='sort_by' id = 'sort_by' value='".$this->sort_by."' />\n"
            ."<div class='form_box shadow'>\n"
            ."  <div class='header'>Customise ".system." Report</div>\n"
            ."  <div class='body rowForm'>\n"
            ."    <table cellpadding='2' cellspacing='0' border='10' class='tableForm' style='width:536px'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Show</th>\n"
            ."        <td nowrap>"
            .show_page_bar($this->total, $this->limit, $this->offset, 1, 1, 1)
            ."<div style='float:right'>"
            ."<input type='radio' id='show_list' name='show' value='list'"
            .($this->show=='map' ? '' : ' checked="checked"').">"
            ."<label for='show_list'>List</label>&nbsp;"
            ."<input type='radio' id='show_map' name='show' value='map'"
            .($this->show=='map' ? ' checked="checked"' : '').">"
            ."<label for='show_map'>Map</label></div>"
            ."</td>\n"
            ."      </tr>"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Types&nbsp;</th>\n"
            ."        <td nowrap class='signalType'>\n"
            .$this->drawControlType()
            ."</td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'><label for='filter_id' title='Callsign or DGPS ID"
            ." (Exact matches are shown at the top of the report, partial matches are shown later)'>"
            ."<b>Call / ID</b></label></th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td>"
            .$this->drawControlId()
            ."</td>\n"
            ."            <td align='center'><label for='filter_khz_1' title='Frequencies'><b>Freq.&nbsp;</b></label>"
            .$this->drawControlFrequencyRange()
            ."</td>\n"
            ."            <td align='right'><label for='filter_channels'><b>Channels</b>&nbsp;</label>"
            .$this->drawControlChannels()
            ."</td>"
            ."          </tr>\n"
            ."        </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'><label for='filter_sp'>Locations</label></th>\n"
            ."        <td nowrap>\n"
            .$this->drawControlStates()
            ."<br />\n"
            .$this->drawControlCountries()
            ."<br />\n"
            .$this->drawControlContinents()
            ."</td>"
            ."     </tr>\n"
            ."     <tr class='rowForm'>\n"
            ."       <th align='left'><label for='filter_dx_gsq'>Distance</label></th>\n"
            ."       <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."         <tr>\n"
            ."           <td><label for='filter_dx_gsq'><b>From GSQ</b></label> "
            .$this->drawControlGSQ()
            ."</td>"
            ."           <td><label for='filter_dx_min' title='Distance'><b>DX</b></label> "
            .$this->drawControlDXMin()
            ." - "
            .$this->drawControlDXMax()
            ."</td>"
            ."           <td>"
            .$this->drawControlDXUnits()
            ."</td>"
            ."         </tr>\n"
            ."	     </table></td>"
            ."     </tr>\n";
        if (system=="RWW") {
            $html.=
                 "    <tr class='rowForm'>\n"
                ."       <th align='left'>Heard in</th>\n"
                ."       <td>\n"
                .$this->drawControlHeardInContinent()
                ."</td>"
                ."      </tr>\n";
        }
        $html.=
             "     <tr class='rowForm'>\n"
            ."       <th align='left'><label title='Only signals heard by the selected listener'>Heard by<br /><br />"
            ."<span style='font-weight: normal;'>Use SHIFT or <br />CONTROL to<br />select multiple<br />values</span>"
            ."</label></th>"
            ."       <td>"
            .$this->drawControlHeardBy()
            ."</td>\n"
            ."    </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>"
            ."<span title='Only signals heard in these states and countries'>Heard here</span></th>\n"
            ."        <td><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td title='Separate multiple options using spaces'>\n"
            .$this->drawControlHeardIn()
            ."</td>"
            ."            <td>"
            .$this->drawControlHeardInMatch()
            ."</td>\n"
            ."          </tr>\n"
            ."	 </table></td>"
            ."      </tr>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Last Heard</th>\n"
            ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td>"
            ."<div style='float:left'><input title='Enter a start date to show only signals last heard after this date (YYYY-MM-DD format)'"
            ." type='text' name='filter_date_1' id='filter_date_1' size='12' maxlength='10'"
            ." value='".($this->filter_date_1 != "1900-01-01" ? $this->filter_date_1 : "")."' class='formfield' /></div>\n"
            ."<div style='float:left;padding:0 1em'>-</div>\n"
            ."<div style='float:left'><input title='Enter an end date to show only signals last heard before this date (YYYY-MM-DD format)'"
            ." type='text' name='filter_date_2' id='filter_date_2' size='12' maxlength='10'"
            ." value='".($this->filter_date_2 != "2020-01-01" ? $this->filter_date_2 : "")."' class='formfield' /></div>"
            ."</td>"
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
            ."            <td>"
            .$this->drawControlColumnSortby()
            ."</td>"
            ."            <td width='45'><label for='sort_by_d'>"
            ."<input type='checkbox' name='sort_by_d' id='sort_by_d' onclick='signalsListChangeSortControl()'"
            ." value='_d'".(substr($this->sort_by, strlen($this->sort_by)-2, 2)=="_d" ? " checked" : "").">"
            ."Z-A</label></td>"
            ."            <td align='right'><label for='chk_filter_active'>"
            ."<input type='checkbox' name='filter_active' id='chk_filter_active' value='1'"
            .($this->filter_active ? " checked" : "").">Only active&nbsp;</label></td>"
            ."          </tr>\n"
            ."	 </table></td>"
            ."      </tr>\n"
            .(isAdmin() ?
            "      <tr class='rowForm'>\n"
            ."        <th align='left'>Admin:</th>\n"
            ."        <td nowrap><select name='filter_system' class='formField'>\n"
            ."<option value='1'"
            .($this->filter_system=='1' ? " selected" : "")
            .">RNA</option>\n"
            ."<option value='2'"
            .($this->filter_system=='2' ? " selected" : "")
            .">REU</option>\n"
            ."<option value='3'"
            .($this->filter_system=='3' ? " selected" : "")
            .">RWW</option>\n"
            ."<option value='not_logged'"
            .($this->filter_system=='not_logged' ? " selected" : "")
            .">Unlogged signals</option>\n"
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
            ."        <th colspan='2'>"
            ."<input type='submit' onclick='return send_form(form)' name='go' value='Go'"
            ." style='width: 100px;' class='formButton' title='Execute search'>\n"
            ."<input name='clear' type='button' class='formButton' value='Clear'"
            ." style='width: 100px;' onclick='clear_signal_list(document.form)'></th>"
            ."      </tr>\n"
            ."    </table></div></div></form>"
            ."<script type='text/javascript'>\n"
            ."//<!--\n"
            ."\$(function() {\n"
            ."  var minDate = new Date('".$this->stats['first_log_iso']."');\n"
            ."  var maxDate = new Date('".$this->stats['last_log_iso']."');\n"
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
            ."  \$('#filter_date_1').datepicker(config);\n"
            ."  \$('#filter_date_2').datepicker(config);\n"
            ."  \$('#filter_id').focus();\n"
            ."  \$('#filter_id').select();\n"
            ."})\n"
            ."//-->\n"
            ."</script>\n";
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
                ."<a href='http://www.navcen.uscg.gov/?pageName=dgpsSiteInfo&amp;All' target='_blank'>"
                ."<b>USCG DGPS Site List</b></a>"
                ." ]</li>\n"
                ."</ul>\n";
        }
        return $html;
    }

    protected function drawListing()
    {
        $this->html.=
             $this->drawResultsInfo()
            ."<table cellpadding='2' cellspacing='0' border='1' class='listTable'>\n"
            .$this->drawResultsHeadings()
            .$this->drawResultsData()
            ."</table>\n"
            ."<br>\n"
            ."<span class='noscreen'>\n"
            ."<b><i>(End of printout)</i></b>\n"
            ."</span>\n";
    }

    protected function drawMap()
    {
        $this->head.=
             "<script type=\"text/javascript\" src=\"//maps.googleapis.com/maps/api/js\"></script>\n"
            ."<script type=\"text/javascript\" src=\"".BASE_PATH."assets/markerclusterer.js\"></script>\n"
            ."<script type=\"text/javascript\">"
            ."google.maps.event.addDomListener(window, 'load', signal.init);"
            ."</script>\n"
            ."<script type=\"text/javascript\">\n"
            ."//<!--\n"
            ."var signals = [\n";
        foreach ($this->rows as $row) {
            if (isset($filter_by_dx) && $filter_by_dx) {
                $dx =        get_dx($filter_by_lat, $filter_by_lon, $row["lat"], $row["lon"]);
            }
            if (!$row["active"]) {
                $class='inactive';
                $type = '(Reportedly off air or decommissioned)';
            } else {
                switch ($row["type"]) {
                    case NDB:
                        $class =    'ndb';
                        $type =     'NDB';
                        break;
                    case DGPS:
                        $class =    'dgps';
                        $type =     'DGPS Station';
                        break;
                    case DSC:
                        $class =    'dsc';
                        $type =     'DSC Station';
                        break;
                    case TIME:
                        $class =    'time';
                        $type =     'Time Signal Station';
                        break;
                    case NAVTEX:
                        $class =    'navtex';
                        $type =     'NAVTEX Station';
                        break;
                    case HAMBCN:
                        $class =    'hambcn';
                        $type =     'Amateur signal';
                        break;
                    case OTHER:
                        $class =    'other';
                        $type =     'Other Utility Station';
                        break;
                }
            }
            $call =     ($this->filter_id ?
                highlight($row["call"], $this->filter_id)
             :
                $row["call"]
            );
            $heard_in = ($this->filter_heard_in ?
                highlight($row["heard_in_html"], str_replace(" ", "|", $this->filter_heard_in))
             :
                $row["heard_in_html"]
            );
            $SP =       ($this->filter_sp ?
                highlight($row["SP"], str_replace(" ", "|", $this->filter_sp))
             :
                $row["SP"]
            );
            $ITU =      ($this->filter_itu ?
                highlight($row["ITU"], str_replace(" ", "|", $this->filter_itu))
             :
                $row["ITU"]
            );
            if ($row['lat'] || $row['lon']) {
                $this->head.=
                     "  {\"id\":".$row["ID"].","
                    ."\"khz\":".$row["khz"].","
                    ."\"call\":\"".$call."\","
                    ."\"className\":\"".$class."\","
                    ."\"type\":\"".$type."\","
                    ."\"pwr\":\"".number_format((float)$row["pwr"])."\","
                    ."\"qth\":\"".htmlEntities($row["QTH"])."\","
                    ."\"itu\":\"".$row["ITU"]."\","
                    ."\"sp\":\"".$SP."\","
                    ."\"lat\":".($row['lat'] ? $row['lat'] : "0").","
                    ."\"lon\":".($row['lon'] ? $row['lon'] : "0").","
                    ."\"gsq\":'".$row["GSQ"]."',"
                    ."\"lsb\":'"
                    .$row["LSB_approx"]
                    .($row["LSB"]<>"" ?
                        ($this->offsets=="" ?
                            $row["LSB"]
                         :
                            number_format((float) ($row["khz"]-($row["LSB"]/1000)), 3, '.', '')
                        )
                        :
                        ""
                     )
                    ."',"
                    ."\"usb\":'"
                    .$row["USB_approx"]
                    .($row["USB"]<>"" ?
                        ($this->offsets=="" ?
                            $row["USB"]
                          :
                            number_format((float) ($row["khz"]+($row["USB"]/1000)), 3, '.', '')
                         )
                         :
                         ""
                     )
                     ."',"
                    ."\"sec\":'".$row['sec']."',"
                    ."\"fmt\":'".($row["format"] ? stripslashes($row["format"]) : "")."',"
                    ."\"heard\":'".($row["last_heard"]!="0000-00-00" ? $row["last_heard"] : "")."',"
                    ."\"heard_in\":\"".str_replace(array('</a>','</b>'), array('<\/a>','<\/b>'), $heard_in)."\""
                    ."},\n";
            }
        }
        $this->head.=
            "];\n"
            ."//-->\n"
            ."</script>\n";

        $this->html.=
             "<div id=\"panel\">\n"
            ."  <h2>Signals Map</h2>\n"
            ."  <div style='float:right;margin: 0 2em;'>\n"
            ."    <input type=\"checkbox\" checked=\"checked\" id=\"usegmm\"/>\n"
            ."    <label for='usegmm'>Clustering</label>\n"
            ."  </div>\n"
            ."  <div id=\"markerlist\">\n"
            ."  </div>\n"
            ."</div>\n"
            ."<div id=\"map-container\">\n"
            ."  <div id=\"map\"></div>\n"
            ."</div>";
    }

    protected function drawResultsData()
    {
        $html =
            "  <tbody>";
        foreach ($this->rows as $row) {
            if (isset($filter_by_dx) && $filter_by_dx) {
                $dx =        get_dx($filter_by_lat, $filter_by_lon, $row["lat"], $row["lon"]);
            }
            if (!$row["active"]) {
                $class='inactive';
                $title = '(Reportedly off air or decommissioned)';
            } else {
                switch ($row["type"]) {
                    case NDB:
                        $class =    'ndb';
                        $title =    'NDB';
                        break;
                    case DGPS:
                        $class =    'dgps';
                        $title =    'DGPS Station';
                        break;
                    case DSC:
                        $class =    'dsc';
                        $title =    'DSC Station';
                        break;
                    case TIME:
                        $class =    'time';
                        $title =    'Time Signal Station';
                        break;
                    case NAVTEX:
                        $class =    'navtex';
                        $title =    'NAVTEX Station';
                        break;
                    case HAMBCN:
                        $class =    'hambcn';
                        $title =    'Amateur signal';
                        break;
                    case OTHER:
                        $class =    'other';
                        $title =    'Other Utility Station';
                        break;
                }
            }
            $call =     ($this->filter_id ?
                highlight($row["call"], $this->filter_id)
             :
                $row["call"]
            );
            $heard_in = ($this->filter_heard_in ?
                highlight($row["heard_in_html"], str_replace(" ", "|", $this->filter_heard_in))
             :
                $row["heard_in_html"]
            );
            $SP =       ($this->filter_sp ?
                highlight($row["SP"], str_replace(" ", "|", $this->filter_sp))
             :
                $row["SP"]
            );
            $ITU =      ($this->filter_itu ?
                highlight($row["ITU"], str_replace(" ", "|", $this->filter_itu))
             :
                $row["ITU"]
            );
            $html.=
                 "<tr class='rownormal ".$class."' title='".$title."'>"
                ."<td><a href='".system_URL."/signal_list?filter_khz_1="
                .(float)$row["khz"]."&amp;filter_khz_2="
                .(float)$row["khz"]."&amp;limit=-1' title='Filter on this value'>".(float)$row["khz"]."</a></td>\n"
                ."<td><a href=\"".system_URL."/".$row["ID"]."\" onclick=\"signal_info('".$row["ID"]."');return false\">"
                ."<b>$call</b></a></td>\n";
            if ($this->type_NDB) {
                $html.=
                     "<td align='right'>"
                     .$row["LSB_approx"]
                     .($row["LSB"]<>"" ?
                        ($this->offsets=="" ?
                            $row["LSB"]
                          :
                            number_format((float) ($row["khz"]-($row["LSB"]/1000)), 3, '.', '')
                         )
                         :
                            "&nbsp;"
                     )
                    ."</td>\n"
                    ."<td align='right'>"
                    .$row["USB_approx"]
                    .($row["USB"]<>"" ?
                        ($this->offsets=="" ?
                            $row["USB"]
                         :
                            number_format((float) ($row["khz"]+($row["USB"]/1000)), 3, '.', '')
                         )
                         :
                            "&nbsp;"
                     )
                    ."</td>\n"
                    ."<td>".($row["sec"] ? $row["sec"] : "&nbsp;")."</td>\n"
                    ."<td>".($row["format"] ? stripslashes($row["format"]) : "&nbsp;")."</td>\n";
            }
            $html.=
                 "<td>"
                .($row["QTH"] ?
                    get_sp_maplinks($row['SP'], $row['ID'], $row["QTH"])
                  :
                    "&nbsp;"
                 )
                ."</td>\n"
                ."<td>"
                .($SP ?
                     "<a href='".system_URL."/signal_list?filter_sp=".$row["SP"]."'"
                    ." title='Filter on this value'>".$SP."</a>"
                  :
                    "&nbsp;"
                 )
                ."</td>\n"
                ."<td>"
                .($ITU ?
                     "<a href='".system_URL."/signal_list?filter_itu=".$row["ITU"]."'"
                    ." title='Filter on this value'>".$ITU."</a>"
                 :
                    "&nbsp;"
                 )
                ."</td>\n"
                ."<td>"
                .($row["GSQ"] ?
                     "<a href='.'"
                    ." onclick='popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\");return false;'"
                    ." title='Show map (accuracy limited to nearest Grid Square)'>"
                    ."<span class='fixed'>".$row["GSQ"]."</span></a>"
                  :
                    "&nbsp;"
                 )
                ."</td>\n"
                ."<td>"
                .($row["pwr"] ? $row["pwr"] : "&nbsp;")
                ."</td>\n"
                ."<td>"
                .($row["notes"] ? stripslashes($row["notes"]) : "&nbsp;")
                ."</td>\n"
                ."<td>"
                .($heard_in ? $heard_in : "&nbsp;")
                ."</td>\n"
                ."<td align='right'>"
                .($row["logs"] ?
                     "<a href=\"".system_URL."/signal_log/".$row["ID"]."\""
                    ." onclick='signal_log(\"".$row["ID"]."\");return false;'><b>".$row["logs"]."</b></a>"
                  :
                    "&nbsp;"
                 )
                ."</td>\n"
                ."<td>".($row["last_heard"]!="0000-00-00" ? $row["last_heard"] : "&nbsp;")."</td>\n";

            if ($this->filter_listener) {
                $html.=
                 "<td align='right'>".($row["dx_km"]!=='' ? $row["dx_km"] : "&nbsp;")."</td>\n"
                ."<td align='right'>".($row["dx_miles"]!=='' ? $row["dx_miles"] : "&nbsp;")."</td>\n";
            }

            if ($this->filter_dx_gsq) {
                $html.=
                     "<td align='right'>"
                    .($row["range_dx_km"]!=='' ? round($row["range_dx_km"]) : "&nbsp;")
                    ."</td>\n"
                    ."<td align='right'>"
                    .($row["range_dx_miles"]!=='' ? round($row["range_dx_miles"]) : "&nbsp;")
                    ."</td>\n"
                    ."<td align='right'>"
                    .($row["range_dx_deg"]!=='' ? round($row["range_dx_deg"]) : "&nbsp;")
                    ."</td>\n";
            }

            if (isAdmin()) {
                $html.=
                     "<td nowrap>"
                    ."<a href='#' onclick='if ("
                    ."confirm(\"CONFIRM\\n\\nAre you sure you wish to delete this signal and\\nall associated logs?\")"
                    .") { document.form.submode.value=\"delete\"; document.form.targetID.value=\"".$row["ID"]."\";"
                    ." document.form.submit();};return false'>Del</a>\n"
                    ."<a href='#' onclick='signal_merge(".$row["ID"].")'>Merge</a></td>\n";
            }
        }
        $html.=
             "  </tr>"
            ."</tbody>";
        return $html;
    }

    protected function drawResultsHeadings()
    {
        $columns = array(
            'khz|0|Sort by Frequency|KHz',
            'call|0|Sort by Callign or DGPS Station ID|ID',
            'LSB|0|Sort by LSB (-ve Offset)|LSB|'.$this->type_NDB,
            'USB|0|Sort by USB (+ve Offset)|USB|'.$this->type_NDB,
            'sec|0|Sort by cycle duration|Sec|'.$this->type_NDB,
            'format|0|Sort by cycle format|Fmt|'.$this->type_NDB,
            'QTH|0|Sort by \'Name\' and Location|\'Name\' and Location',
            'sp|0|Sort by State / Province|S/P',
            'itu|0|Sort by NDB List Country Code|ITU',
            'gsq|0|Sort by GSQ Grid Locator Square|GSQ',
            'pwr|1|Sort by Transmitter Power|PWR',
            'notes|0|Sort by Notes column|Notes',
            'heard_in|0|Sort by \'Heard In\' column|Heard In',
            'logs|0|Sort by number of times logged|Logs',
            'last_heard|1|Sort by date last logged (YYYY-MM-DD)|Last Heard'
        );
        $html =
             "  <thead>\n"
            ."  <tr>\n";
        foreach ($columns as $column) {
            $col = explode('|', $column);
            if (!isset($col[4]) || $col[4]) {
                $html.=
                     "    <th rowspan='2' onclick=\"colSort("
                    ."'".$col[0]."',"
                    ."'".$this->sort_by."'"
                    .($col[1] ? ",1" : "")
                    .")\""
                    ." title=\"".$col[2]."\">"
                    .$col[3]." "
                    .($this->sort_by==$col[0] ?
                        "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>"
                     :
                        ""
                    )
                    .($this->sort_by==$col[0].'_d' ?
                        "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>"
                     :
                        ""
                    )
                    ."</th>\n";
            }
        }
        if ($this->filter_listener) {
            $html.=    "    <th class='nosort txt_c' colspan='2'>Range from<br />Listener</th>\n";
        }
        if ($this->filter_dx_gsq) {
            $html.=    "    <th class='nosort txt_c' colspan='3'>Range from<br />GSQ</th>\n";
        }
        if (isAdmin()) {
            $html.=    "    <th class='nosort' rowspan='2'>&nbsp;</th>\n";
        }
        if ($this->filter_listener || $this->filter_dx_gsq) {
            $html.=    "  <tr>\n";
            if ($this->filter_listener) {
                $html.=
                     "    <th onclick=\"colSort('dx','".$this->sort_by."',1)\""
                    ." title=\"Sort by 'KM' column\"  nowrap>"
                    ."KM "
                    .($this->sort_by=='dx' ?
                        "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>"
                     :
                        ""
                    )
                    .($this->sort_by=='dx_d' ?
                        "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>"
                     :
                        ""
                    )
                    ."</th>\n"
                    ."    <th onclick=\"colSort('dx','".$this->sort_by."',1)\""
                    ." title=\"Sort by 'Miles' column\" nowrap>"
                    ."Miles "
                    .($this->sort_by=='dx' ?
                        "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>"
                     :
                        ""
                    )
                    .($this->sort_by=='dx_d' ?
                        "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>"
                     :
                        ""
                    )
                    ."</th>\n"
                ;
            }
            if ($this->filter_dx_gsq) {
                $html.=
                     "    <th onclick=\"colSort('range_dx_km','".$this->sort_by."',1)\""
                    ." title=\"Sort by 'Range KM' column\">"
                    ."KM "
                    .($this->sort_by=='range_dx_km' ?
                        "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>"
                     :
                        ""
                    )
                    .($this->sort_by=='range_dx_km_d' ?
                        "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>"
                     :
                        ""
                    )
                    ."</th>\n"
                    ."    <th onclick=\"colSort('range_dx_km','".$this->sort_by."',1)\""
                    ." title=\"Sort by 'Range Miles' column\">"
                    ."Miles "
                    .($this->sort_by=='range_dx_km' ?
                        "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>"
                     :
                        ""
                    )
                    .($this->sort_by=='range_dx_km_d' ?
                        "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>"
                     :
                        ""
                    )
                    ."</th>\n"
                    ."    <th onclick=\"colSort('range_dx_deg','".$this->sort_by."')\""
                    ." title=\"Sort by 'Degrees' column\">"
                    ."Deg "
                    .($this->sort_by=='range_dx_deg' ?
                        "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>"
                     :
                        ""
                    )
                    .($this->sort_by=='range_dx_deg_d' ?
                        "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>"
                     :
                        ""
                    )
                    ."</th>\n";
            }
            $html.=
                "  </tr>\n";
        }
        $html.=
             "  </tr>\n"
            ."  </thead>\n";
        return $html;
    }

    protected function drawResultsInfo()
    {
        $html = "";
        switch($this->sort_by) {
            case 'CLE64':
            case 'CLE64_d':
                $html.=
                     "<div><span style='color:#ff0000'>CLE64 Custom sort order applied:</span></b></div>\n"
                    ."<ul>\n"
                    ."  <li>Show <b>active</b> beacons first</li>\n"
                    ."  <li>Sort by <b>first letter</b> of callsign: <b>("
                    .($this->sort_by=='CLE64' ? 'A-Z' : 'Z-A')
                    .")</b></li>\n"
                    ."  <li>Sort by <b>DX</b> from Grid Square <b>".$this->filter_dx_gsq."</b>.</li>\n"
                    ."</ul>\n"
                    ."<p><b>Tip:</b> You can further <b>refine this search</b> by entering additional"
                    ." search criteria such as range limits.</p>\n";
                break;
        }
        if ($this->filter_id) {
            $html.=
                 "<p><b>Note:</b> Any exact matches for <b>".$this->filter_id."</b>"
                ." will shown at the top of this list, regardless of the station's current status.</p>\n";
        }
        return $html;
    }

    protected function drawStatsListeners()
    {
        return
             "<div class='form_box shadow'>\n"
            ."  <div class='header'>".system." Listeners</div>\n"
            ."  <div class='body rowForm'>\n"
            ."    <table cellpadding='2' cellspacing='0' border='1' class='tableForm' style='width:180px'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left' width='50%'>Locations</th>\n"
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

    protected function drawStatsSignals()
    {
        return
             "<div class='form_box shadow'>\n"
            ."  <div class='header'>Signals</div>\n"
            ."  <div class='body rowForm'>\n"
            ."    <table cellpadding='2' cellspacing='0' border='1' class='tableForm' style='width:180px'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left' width='50%'>RNA only</th>\n"
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
            .(isAdmin() ?
                 "      <tr class='rowForm'>\n"
                ."        <th align='left'>Unlogged</th>\n"
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
        $this->stats = array_merge($this->stats, Log::getLogDateRange($this->filter_system, $this->region));
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
        $this->setupLoadVars();
        $this->setupDoSubmode();
        $this->setupTweakVars();
        $this->setupLoadStats();
        $this->setupInitListenersListFilter();
        $this->setupInitSql();
        $this->getCountMatched();
        $this->getResultsMatched();
    }

    protected function setupDoSubmode()
    {
        if (!isAdmin()) {
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
                     "`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL,"
                    ." CAST(`signals`.`sec` AS UNSIGNED) ASC";
                break;
            case "sec_d":
                $this->sql_sort_by =
                     "`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL,"
                    ." CAST(`signals`.`sec` AS UNSIGNED) DESC";
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
                         "`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)>='A' DESC,"
                        ." LEFT(`signals`.`call`,1) ASC, `range_dx_km` DESC";
                }
                break;
            case "CLE64_d":
                if ($this->filter_dx_gsq) {
                    $this->sql_sort_by =
                         "`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)<='Z' ASC,"
                        ." LEFT(`signals`.`call`,1) DESC, `range_dx_km` DESC";
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
        global $mode, $sort_by;
        $this->mode =                   $mode;
        $this->submode =                get_var('submode');
        $this->show =                   get_var('show');
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
