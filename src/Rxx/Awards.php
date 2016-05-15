<?php
namespace Rxx;

/**
 * Class Awards
 * @package Rxx
 */
class Awards
{
    protected $listenerFilterSQL;
    protected $html;

    /**
     * @return string
     */
    public function draw()
    {
        global $mode, $submode, $listenerID, $region;
        global $awards_requested, $awards_email, $awards_name;
        global $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER;
        if (!$listenerID) {
            $path_arr = (explode('?', $_SERVER["REQUEST_URI"]));
            $path_arr = explode('/', $path_arr[0]);
            if ($path_arr[count($path_arr)-2]==$mode) {
                $listenerID = array_pop($path_arr);
            }
        }
        $this->getListenerFilterSQL();
        $this->_listenerID = $listenerID;
        if ($submode=="send") {
            return $this->send($awards_requested, $awards_email, $awards_name);
        }
        $this->loadListenerDetails();
        $this->html =
             "<form name='form' id='form' action='".system_URL."/".$mode."' method='POST'>\n"
            ."<input type='hidden' id='submode' name='submode' value=''>\n"
            ."<h2>Awards</h2>\n"
            ."<p>This report shows awards for which <b>only</b> registered NDB List members may apply.</p>\n"
            ."<div class='form_box shadow'>\n"
            ."  <div class='header'>Customise ".system." Report</div>\n"
            ."  <div class='body rowForm'>\n"
            ."    <table cellpadding='0' cellspacing='0' class='tableForm' border='1' bordercolor='#c0c0c0'>\n"
            ."      <tr class='rowForm'>\n"
            ."        <th align='left'>Show for &nbsp;</th>\n"
            ."        <td nowrap>"
            ."<select name='listenerID' class='formfield' onchange='document.form.submit()'"
            ." style='font-family: monospace;' >\n"
            .Rxx::get_listener_options_list(
                $this->listenerFilterSQL,
                $this->_listenerID,
                "(Select a Listener to view Awards)"
            )
            ."</select></td>\n"
            ."      </tr>\n"
            .(!$this->_listenerID ?
                 "      <tr class='rowForm'>\n"
                ."        <td align='center' colspan='2' height='22'>"
                ."Select your local system (<a href='".system_URL."/awards?sys=system_RNA'><b>RNA</b></a>,"
                ." <a href='".system_URL."/awards?sys=system_REU'><b>REU</b></a> or"
                ." <a href='".system_URL."/awards?sys=system_RWW'><b>RWW</b></a>),"
                ." then find your name in the list.&nbsp;</td>\n"
                ."      </tr>\n"
             :
                ""
            )
            ."    </table>"
            ."  </div>\n"
            ."</div><br>"
            ."<table cellpadding='5' border='1' cellspacing='0' class='downloadtable' bordercolor='#000000'>\n"
            ."  <tr>\n"
            ."    <th align='left' class='downloadTableHeadings_nosort'>NDB List Awards"
            ."<span style='font-weight: normal'>  Devised by Andy Robins, <b>Administered</b> by "
            .awardsAdminName
            ."</span></th>\n"
            ."  </tr>\n"
            ."  <tr class='rowForm'>\n"
            ."    <td bgcolor='#ffffff' width='800'>"
            ."<p align='justify'><b>Introducton</b><br>\n"
            ."The NDB List Awards Program recognizes the achievements of radio hobbyists\n"
            ."who DX non-directional radio beacons in the LF/MF frequency range (190 to\n"
            ."1800 kHz). These certificates are <b>not</b> intended as, nor should they be considered\n"
            ."prizes in a 'contest'. They are tangible symbols of the hard work, dedication,\n"
            ."technical skill and fun that goes along with our hobby.</p>\n"
            ."<p align='justify'>These certificates are\n"
            ."available at no cost to List members who send the appropriate information to the\n"
            ."Awards Coordinator <b>".awardsAdminName."</b> either directly or by means or this ordering system."
            ."Whenever possible, certificates will be sent electronically in Adobe Acrobat (.pdf) format.\n"
            ."Those listeners unable to receive them this way should inform the Coordinator\n"
            ."when requesting their certificates.</p>"
            ."<p align='justify'>The awards certificate program has been designed so that virtually all listeners\n"
            ."who desire them can get at least one in each category. The 'difficulty factor' in\n"
            ."each ranges from modest to a level that even experienced listeners will find\n"
            ."challenging. In setting these requirements, we have kept in mind the fact that the\n"
            ."vast majority of List members are in either Europe or North America.</p>\n"
            ."<p align='justify'><b>Requesting Awards</b><br>"
            ."To use this system to request awards, select your name and listening location in the list \n"
            ."above and choose awards by clicking on the shopping cart icon in the 'Order' columns shown for"
            ." qualifying awards shown below.<br><br>";

        if ($this->_listenerID) {
            $this->html.=
                 "Please ensure that you have not previously been issued these awards before requesting them.</p>"
                ."<p align='justify'>Since requested certificates will be sent electronically to the email address"
                ." given for the listener for whom they are to be created, please verify that your email address is"
                ." shown in the <a href='#checkout'><b>checkout form</b></a>.<br><br><hr><br>"
                ."<h2>Awards available to ".$this->_listener_details.":</h2><br><br>"
                .$this->drawDaytime()
                .$this->drawLongranger()
                .$this->drawContinental()
                .$this->drawCountry()
                .$this->drawNorth60()
                .$this->drawLt()
                .$this->drawTransatlantic()
                .$this->drawTranspacific()
                .$this->drawCanadianTranscontinental()
                .$this->drawUsTranscontinental()
                .$this->drawCheckout();
        }
        $this->html.=
             "    </td>\n"
            ."  </tr>\n"
            ."</table>\n"
            ."</form>";
        return $this->html;
    }

    /**
     * @return string
     */
    protected function drawCanadianTranscontinental()
    {
        $can_transcont_awards = array(1,2,4,6);
        $sql =
             "SELECT DISTINCT\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`type` = 0 AND\n"
            ."  `signals`.`ITU` = 'CAN' AND\n"
            ."  (`signals`.`SP` = 'PE' OR `signals`.`SP` = 'NL' OR `signals`.`SP` = 'NB') AND\n"
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
            ."ORDER BY `khz`,`call`";
        $result_atl =    \Rxx\Database::query($sql);
        $sql =
             "SELECT DISTINCT\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`type` = 0 AND\n"
            ."  `signals`.`ITU` = 'CAN' AND\n"
            ."  (`signals`.`SP` = 'BC') AND\n"
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
            ."ORDER BY `khz`,`call`";
        $result_pac =    \Rxx\Database::query($sql);

        if (!(\Rxx\Database::numRows($result_atl) && \Rxx\Database::numRows($result_pac))) {
            return "";
        }
        $out =
             "<b>Canadian Transcontinental NDB DX Award</b> ("
             .(\Rxx\Database::numRows($result_atl)>=\Rxx\Database::numRows($result_pac) ?
                 \Rxx\Database::numRows($result_pac)
              :
                 \Rxx\Database::numRows($result_atl)
             )
            ." qualifying pairs of stations)<br>"
            ."This cerificate recognises reception of NDBs on Pacific and Atlantic coasts of Canada"
            ." (i.e. British Columbia and any of the Maritime Provinces). "
            ."Qualifying beacons must be in these provinces but do not have to literally be on the shoreline."
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0'"
            ." style='border:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='132' valign='top'><b>Stations</b></td>\n"
            ."    <td width='25' valign='top' rowspan='".(Count($can_transcont_awards)+1)."' bgcolor='#ffffff'"
            ." style='border:none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>"
            ."    <td width='591' valign='top'><b>Station</b></td>"
            ."    <td width='20' valign='top'><b>Order</b></td>"
            ."  </tr>\n";
        $old_level = 0;
        for ($i=0; $i<count($can_transcont_awards); $i++) {
            $level =    $can_transcont_awards[$i];
            $out.=
                 "          <tr>\n"
                ."            <td bgcolor='#f0f0f0' valign='top' nowrap>".$level." on each coast</td>\n";
            if (\Rxx\Database::numRows($result_atl)>$old_level && \Rxx\Database::numRows($result_pac)>$old_level) {
                $Eligible_atl = (\Rxx\Database::numRows($result_atl) >= $level);
                $Eligible_pac = (\Rxx\Database::numRows($result_pac) >= $level);
                $out.=
                     "    <td bgcolor='#f0f0f0' valign='top' style='font-family: courier;'>"
                    .($Eligible_atl ? "" : "<font color='#808080'>")."Atlantic Coast:<br>\n";
                for ($j = $old_level; $j<$level; $j++) {
                    if ($j<\Rxx\Database::numRows($result_atl)) {
                        $row_atl =    \Rxx\Database::fetchArray($result_atl, MYSQL_ASSOC);
                        $out.= (float)$row_atl['khz']."-".Rxx::pad_nbsp($row_atl['call'], 3)." (".$row_atl['SP'].") ";
                    }
                }
                $out.=
                     ($Eligible_atl ? "" : "</font>")
                    ."<br>\n"
                    .($Eligible_pac ? "" : "<font color='#808080'>")
                    ."Pacific Coast:<br>\n";
                for ($j = $old_level; $j<$level; $j++) {
                    if ($j<\Rxx\Database::numRows($result_pac)) {
                        $row_pac =    \Rxx\Database::fetchArray($result_pac, MYSQL_ASSOC);
                        $out.= (float)$row_pac['khz']."-".Rxx::pad_nbsp($row_pac['call'], 3)." (".$row_pac['SP'].") ";
                    }
                }
                $out.=
                     ($Eligible_pac ? "" : "</font>")
                    ."</td>"
                    ."<td bgcolor='#f0f0f0' align='center'>";
                if ($Eligible_atl && $Eligible_pac) {
                    $out.=  $this->drawCartButton("Canadian Transcontinental ($level stations on each coast)");
                } else {
                    $out.=    "&nbsp;";
                }
                $out.=    "</td>";
                $old_level =    $level;
            } else {
                $out.=
                     "    <td bgcolor='#f0f0f0'>&nbsp;</td>\n"
                    ."    <td valign='top' bgcolor='#f0f0f0' align='center'>&nbsp;</td>";
            }
            $out.=    "  </tr>\n";
        }
        $out.=
             "</table>\n"
            ."<br><br>\n";
        return $out;
    }

    /**
     * @return string
     */
    protected function drawContinental()
    {
        $continent_awards = array(
            "eu^European NDB DX Awards^This certificate recognises reception of NDBs located in Europe at the levels shown below.^10^20^30^40",
            "na^North American NDB DX Awards^This certificate recognises reception of NDBs located in North America.<br>When considering the requirement in each division, keep in mind that each US state and Canadian province is considered a separate \"country\".^10^30^45^60",
            "ca^Caribbean / Central American NDB DX Awards^Available in the following categories:^3^10^15^20",
            "sa^South American NDB DX Awards^Available in the following categories:^1^3^5^10",
            "af^African NDB DX Awards^Available in the following categories:^1^5^10^15",
            "as^Asian NDB DX Awards^Available in:^1^3^5^10",
            "oc^Australian and Pacific Islands NDB DX Awards^Get it in these \"denominations\":^1^3^10^20",
            "an^Antarctic NDB DX Awards^If anyone hears a NDB from this frozen place, they will definately receive an award!^1"
        );
        $out = '';
        for ($continent=0; $continent<count($continent_awards); $continent++) {
            $this_continent =    explode("^", $continent_awards[$continent]);
            $sql =
                 "SELECT\n"
                ."  DISTINCT `signals`.`SP`,\n"
                ."  `signals`.`ITU`\n"
                ."FROM\n"
                ."  `signals`,\n"
                ."  `logs`,\n"
                ."  `itu`\n"
                ."WHERE\n"
                ."  `signals`.`ID` = `logs`.`signalID` AND\n"
                ."  `signals`.`type` = 0 AND\n"
                ."  `signals`.`ITU` = `itu`.`ITU` AND\n"
                ."  `logs`.`listenerID` = ".$this->_listenerID." AND\n"
                ."  `itu`.`region` = '".$this_continent[0]."'\n"
                ."ORDER BY `SP`,`ITU`";
            $result =    \Rxx\Database::query($sql);

            if (\Rxx\Database::numRows($result)) {
                $out.=
                     "<li><b>".$this_continent[1]."</b> (".\Rxx\Database::numRows($result)." qualifying countries)</li>\n"
                    .$this_continent[2]
                    ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='border:none; border-collapse:collapse;'>\n"
                    ."  <tr class='downloadTableHeadings_nosort'>\n"
                    ."    <td width='132' valign='top' nowrap><b>Countries</b></td>\n"
                    ."    <td width='25'  valign='top' rowspan='".(count($this_continent)-2)."' bgcolor='#ffffff' style='border: none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>\n"
                    ."    <td valign='top'><b>NDB List Country codes</b></td>\n"
                    ."    <td width='20'  valign='top'><b>Order</b></td>\n"
                    ."  </tr>\n";
                $old_level = 0;
                for ($i=3; $i<count($this_continent); $i++) {
                    $level =    $this_continent[$i];
                    $out.=
                         "  <tr>\n"
                        ."    <td bgcolor='#f0f0f0' valign='top' nowrap>".$level."</td>\n";
                    if (\Rxx\Database::numRows($result)>$old_level) {
                        $Eligible = (\Rxx\Database::numRows($result) >= $level);
                        $out.=
                            "    <td bgcolor='#f0f0f0' valign='top' style='font-family: courier;'>"
                           .($Eligible ? "" : "<font color='#808080'>");
                        $this_level =    0;
                        for ($j = $old_level; $j < $level; $j++) {
                            $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                            $out.= ($row['SP'] && $row['SP']!="AK" && $row['SP']!="PR"  ?
                                $row['SP']
                              :
                                $row['ITU']
                             )
                            ." ";
                            $this_level++;
                        }
                        $out.=
                             ($Eligible ? "" : "</font>")
                            ."</td>"
                            ."    <td bgcolor='#f0f0f0' align='center'>";
                        if ($Eligible) {
                            $out.=  $this->drawCartButton($this_continent[1]."_".$level."_countries");
                        } else {
                            $out.=    "&nbsp;";
                        }
                        $out.=    "</td>";
                        $old_level =    $level;
                    } else {
                        $out.=
                             "    <td bgcolor='#f0f0f0'>&nbsp;</td>\n"
                            ."    <td valign='top' bgcolor='#f0f0f0' align='center'>&nbsp;</td>";
                    }
                    $out.=    "  </tr>\n";
                }
                $out.=
                     "</table>\n"
                    ."<br><br>\n";
            }
        }
        return $out;
    }

    /**
     * @return string
     */
    protected function drawCountry()
    {
        // Search locations^number^must include all^Title^blub^Level1^Level2 (etc)
        $country_awards = array(
            "`ITU` = 'BEL' OR `ITU` = 'HOL' OR `ITU` = 'LUX'^3^1^Benelux NDB Award^This certificate recognises reception of NDBs in the \"Benelux\"countries (Belgium, the Netherlands and Luxembourg).<br><b>Note that, for all certificates, at least one beacon in each of the three countries must have been received</b>.^5^15^25^35",
            "`ITU` = 'FRA' OR `ITU` = 'COR'^2^0^French NDB Award^This certificate recognises reception of NDBs located in France at the following levels:^20^40^80^120",
            "`ITU` = 'DEU'^1^0^German NDB Award^This certificate recognises reception of NDBs located in Germany at the following levels:^20^30^60^100",
            "`ITU` = 'SWE' OR `ITU` = 'NOR' OR `ITU` = 'FIN' OR `ITU` = 'DNK'^4^0^Scandanavian NDB Award^This certificate recognises reception of NDBs located in Sweden, Norway, Finland or Denmark (but excluding Iceland and island dependencies in the North Atlantic) at the following levels:^25^100^200^300",
            "`ITU` = 'ENG' OR `ITU` = 'WLS' OR `ITU` = 'IOM' OR `ITU` = 'GSY' OR `ITU` = 'JSY' OR `ITU` = 'SCT' OR `ITU` = 'ORK' OR `ITU` = 'SHE' OR `ITU` = 'NIR' OR `ITU` = 'IRL'^10^0^UK and Irish NDB Award^This certificate recognises reception of NDBs located in the United Kingdom (including Northern Ireland, the Channel Island, the Isle of Man and the Shetland Islands) and the republic of Ireland at the following levels:^20^40^80^120",
            "`ITU` = 'ITA' OR `ITU` = 'SAR' OR `ITU` = 'SCY'^3^0^Italian NDB Award^This certificate recognises reception of NDBs located in Italy (including Sardinia and Sicily) at the following levels:^20^40^60^90",
            "`ITU` = 'ESP' OR `ITU` = 'POR' OR `ITU` = 'BAL'^3^0^Iberian NDB Award^This certificate recognises reception of NDBs located in Spain and Portugal (including the Baleric Islands but excluding the Azores, the Canary Islands and spanish enclaves in Morocco) at the following levels:^20^40^80^120"
        );
        $out = '';
        for ($country=0; $country<count($country_awards); $country++) {
            $this_country =    explode("^", $country_awards[$country]);
            if ($this_country[2]) {
                $sql =
                 "SELECT\n"
                ."  `signals`.`ID`\n"
                ."FROM\n"
                ."  `signals`\n"
                ."WHERE\n"
                ."  (".$this_country[0].")\n"
                ."GROUP BY\n"
                ."  `signals`.`ITU`";
                $result =    @\Rxx\Database::query($sql);
                $first =    array();
                for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                    $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                    $first[] =    $row['ID'];
                }
                $sql =
                 "SELECT DISTINCT\n"
                ."  `signals`.`ID`,\n"
                ."  `signals`.`call`,\n"
                ."  `signals`.`khz`,\n"
                ."  `signals`.`ITU`\n"
                ."FROM\n"
                ."  `signals`,\n"
                ."  `logs`\n"
                ."WHERE\n"
                ."  `signals`.`ID` = `logs`.`signalID` AND\n"
                ."  `signals`.`type` = 0 AND\n"
                ."  (".$this_country[0].") AND\n"
                ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
                ."ORDER BY (`signals`.`ID`=".implode($first, "  OR `signals`.`ID`=").") DESC";
            } else {
                $sql =
                 "SELECT DISTINCT\n"
                ."  `signals`.`ID`,\n"
                ."  `signals`.`call`,\n"
                ."  `signals`.`khz`,\n"
                ."  `signals`.`ITU`\n"
                ."FROM\n"
                ."  `signals`,\n"
                ."  `logs`\n"
                ."WHERE\n"
                ."  `signals`.`ID` = `logs`.`signalID` AND\n"
                ."  `signals`.`type` = 0 AND\n"
                ."  (".$this_country[0].") AND\n"
                ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
                ."ORDER BY `khz`,`call`";
            }
            $result =        \Rxx\Database::query($sql);
            if (\Rxx\Database::numRows($result)) {
                $out.=
                 "<b>".$this_country[3]."</b> (".\Rxx\Database::numRows($result)." qualifying stations)<br>\n"
                .$this_country[4]
                ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='border:none; border-collapse:collapse;'>\n"
                ."  <tr class='downloadTableHeadings_nosort'>\n"
                ."    <td width='132' valign='top' nowrap><b>NDBs</b></td>\n"
                ."    <td width='25'  valign='top' rowspan='".(count($this_country)-3)."' bgcolor='#ffffff' style='border:none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>\n"
                ."    <td width='480' valign='top'><b>NDB Details</b></td>\n"
                ."    <td width='20'  valign='top'><b>Order</b></td>\n"
                ."  </tr>\n";
                $old_level = 0;
                for ($i=5; $i<count($this_country); $i++) {
                    $level =    $this_country[$i];
                    $out.=
                     "  <tr>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top' nowrap>".$level."</td>\n";
                    if (\Rxx\Database::numRows($result)>$old_level) {
                        $Eligible = (\Rxx\Database::numRows($result) >= $level);
                        $out.=
                         "    <td bgcolor='#f0f0f0' valign='top' style='font-family: courier;'>"
                        .($Eligible ? "" : "<font color='#808080'>");
                        $this_level =    0;
                        for ($j = $old_level; $j < $level; $j++) {
                            $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                            if ($row['call']) {
                                $out.=    "<nobr>".Rxx::pad_dot((float)$row['khz'], 6).($this_country[1]>1 ? Rxx::pad_dot($row['call'], 4)."(".$row['ITU'].")" : Rxx::pad_nbsp($row['call'], 4))."</nobr>&nbsp;&nbsp;";
                            }
                            $this_level++;
                        }
                        $out.=
                         ($Eligible ? "" : "</font>")
                        ."</td>"
                        ."    <td bgcolor='#f0f0f0' align='center' valign='top'>";
                        if ($Eligible) {
                            $out.=  $this->drawCartButton($this_country[3]." (".$level." stations)");
                        } else {
                            $out.=    "&nbsp;";
                        }
                        $out.=    "</td>";
                        $old_level =    $level;
                    } else {
                        $out.=
                         "    <td bgcolor='#f0f0f0'>&nbsp;</td>\n"
                        ."    <td valign='top' bgcolor='#f0f0f0' align='center'>&nbsp;</td>";
                    }
                    $out.=    "  </tr>\n";
                }
                $out.=
                 "</table>\n"
                ."<br><br>\n";
            }
        }
        return $out;
    }

    /**
     * @return string
     */
    protected function drawDaytime()
    {
        $ident = "Daytime_DX_";
        $daytime_dx = array(
            "250^499^402^804",
            "500^749^805^1205",
            "750^999^1206^1607",
            "1000^1249^1608^2010",
            "1250^0^1608^0"
        );
        $level =    explode("^", $daytime_dx[0]);
        if (!Rxx::get_bestDX($this->_listenerID, 1, $level[0], 0)) {
            return "";
        }
        $out=
             "<b>Daytime DX Awards (Daytime hours are "
            .Rxx::lead_zero(1000 + $this->_listener_record['timezone']*100 % 2400, 4)
            ." - "
            .Rxx::lead_zero(1400 + $this->_listener_record['timezone']*100 % 2400, 4)
            ." hrs UTC)</b><br>"
            ."This cerificate recognises long distance reception of NDBs between the hours of 1000 and 1400 local"
            ." standard time -<br>\n"
            ."that is, two hours before and afer noon in the listener's timezone.<br>\n"
            ."There are five levels:"
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0'"
            ." style='border:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='65' valign='top' nowrap><b>Miles</b></td>\n"
            ."    <td width='65' valign='top'><b>KM</b></td>\n"
            ."    <td width='25' valign='top' rowspan='"
            .(count($daytime_dx)+1)
            ."' bgcolor='#ffffff' style='border:none'>&nbsp;</td>"
            ."    <td width='25' valign='top'><b>KHz</b></td>"
            ."    <td width='25' valign='top'><b>ID</b></td>"
            ."    <td width='250' valign='top'><b>Location</b></td>"
            ."    <td width='20' valign='top'><b>SP</b></td>"
            ."    <td width='30' valign='top'><b>ITU</b></td>"
            ."    <td width='30' valign='top'><b>Pwr</b></td>"
            ."    <td width='35' valign='top' align='right'><b>Miles</b></td>"
            ."    <td width='35' valign='top' align='right'><b>KM</b></td>"
            ."    <td width='70' valign='top' align='center'><b>Date</b></td>"
            ."    <td width='35' align='center'><b>UTC</b></td>"
            ."    <td><b>Order</b></td>"
            ."  </tr>\n";
        for ($i=0; $i<count($daytime_dx); $i++) {
            $level =    explode("^", $daytime_dx[$i]);
            $result = Rxx::get_bestDX($this->_listenerID, 1, $level[0], $level[1]);
            $out.=
                 "  <tr>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>"
                .$level[0].($level[1] ? "-".$level[1] : "+")
                ."</td>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>"
                .$level[2].($level[3] ? "-".$level[3] : "+")
                ."</td>\n";
            if ($result) {
                $out.=
                     "    <td bgcolor='#f0f0f0' valign='top'>"
                    .(float)$result['khz']
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top'>"
                    ."<a href='signal_info/".$result["ID"]."'"
                    ." onclick='signal_info(\"".$result["ID"]."\");return false'>"
                    ."<b>".$result['call']."</b>"
                    ."</a>"
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top'>"
                    .$result['QTH']
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top'>"
                    .($result['SP'] ? $result['SP'] : "&nbsp;")
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top'>"
                    .$result['ITU']
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right'>"
                    .($result['pwr'] ? $result['pwr'] : "&nbsp;")
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right'>"
                    .$result['dx_miles']
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right'>"
                    .$result['dx_km']
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right' nowrap>"
                    .$result['date']
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='center'>"
                    .$result['time']
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='center'>"
                    .$this->drawCartButton($ident.$level[0])
                    ."</td>";
            } else {
                $out.=    "    <td colspan='10' style='border-style: none'>&nbsp;</td>";
            }
            $out.=      "  </tr>\n";
        }
        $out.=
             "</table>\n"
            ."<br><br>\n";
        return $out;
    }

    /**
     * @return string
     */
    protected function drawLongranger()
    {
        $ident = "Long_Ranger_DX_";
        $longranger_dx = array(
            "500^999^805^1607",
            "1000^1499^1608^2413",
            "1500^1999^2414^3217",
            "2000^2499^3218^4022",
            "2500^2999^4023^4826",
            "3000^4999^4827^8044",
            "5000^0^8045^0"
        );
        $level =    explode("^", $longranger_dx[0]);
        if (!Rxx::get_bestDX($this->_listenerID, 0, $level[0], 0)) {
            return "";
        }
        $out =
             "<b>Long Ranger DX Awards</b><br>"
            ."This cerificate recognises <i>really</i> long distance reception of NDBs particularly at night"
            ." when skywave propagation is possible<br>"
            ."that is, two hours before and afer noon in the listener's timezone."
            ." This award is available at seven levels:"
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0'"
            ." style='border:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='65' valign='top' nowrap><b>Miles</b></td>\n"
            ."    <td width='65' valign='top'><b>KM</b></td>\n"
            ."    <td width='25' valign='top' rowspan='"
            .(count($longranger_dx)+1)
            ."' bgcolor='#ffffff' style='border:none;'>&nbsp;</td>"
            ."    <td width='25' valign='top'><b>KHz</b></td>"
            ."    <td width='25' valign='top'><b>ID</b></td>"
            ."    <td width='250' valign='top'><b>Location</b></td>"
            ."    <td width='20' valign='top'><b>SP</b></td>"
            ."    <td width='30' valign='top'><b>ITU</b></td>"
            ."    <td width='30' valign='top'><b>Pwr</b></td>"
            ."    <td width='35' valign='top' align='right'><b>Miles</b></td>"
            ."    <td width='35' valign='top' align='right'><b>KM</b></td>"
            ."    <td width='70' valign='top' align='center'><b>Date</b></td>"
            ."    <td width='35' align='center'><b>UTC</b></td>"
            ."    <td><b>Order</b></td>"
            ."  </tr>\n";
        for ($i=0; $i<count($longranger_dx); $i++) {
            $level =    explode("^", $longranger_dx[$i]);
            $result = Rxx::get_bestDX($this->_listenerID, 0, $level[0], $level[1]);
            $out.=
                 "  <tr>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>"
                .$level[0].($level[1] ? "-".$level[1] : "+")
                ."</td>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>"
                .$level[2].($level[3] ? "-".$level[3] : "+")
                ."</td>\n";
            if ($result) {
                $out.=
                     "    <td bgcolor='#f0f0f0' valign='top'>\n"
                    .(float)$result['khz']
                    ."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top'>"
                    ."<a href='signal_info/".$result["ID"]."'"
                    ." onclick='signal_info(\"".$result["ID"]."\");return false'><b>".$result['call']."</b></a>"
                    ."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top'>".$result['QTH']."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top'>".($result['SP'] ? $result['SP'] : "&nbsp;")."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top'>".$result['ITU']."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right'>".($result['pwr'] ? $result['pwr'] : "&nbsp;")."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right'>".$result['dx_miles']."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right'>".$result['dx_km']."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='right' nowrap>".$result['date']."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='center'>".($result['time'] ? $result['time'] : "&nbsp;")."</td>\n"
                    ."    <td bgcolor='#f0f0f0' valign='top' align='center'>"
                    .$this->drawCartButton($ident.$level[0])
                    ."</td>\n";
            } else {
                $out.= "    <td colspan='10' style='border-style: none'>&nbsp;</td>\n";
            }
            $out.= "  </tr>\n";
        }
        $out.=
             "</table>\n"
            ."<br><br>\n";
        return $out;
    }

    /**
     * @return string
     */
    protected function drawLt()
    {
        $sql =
             "SELECT DISTINCT\n"
            ."  `logs`.`date`,\n"
            ."  `logs`.`time`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `logs`.`signalID` = 2737 AND\n"
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n";
        $result =        \Rxx\Database::query($sql);

        if (!\Rxx\Database::numRows($result)) {
            return "";
        }
        $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
        $out =
             "<b>LT Alert Awards</b><br>"
            ."This cerificate recognises reception of a single NDBs: LT on 305 KHz, at Alert, Nunavut, in Canada. "
            ."This is the highest latitude NDB known in the Northern Hemisphere."
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='border:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='132'><b>Station</b></td>\n"
            ."    <td width='25' valign='top' rowspan='2' bgcolor='#ffffff' style='border:none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>"
            ."    <td width='208'><b>Location</b></td>"
            ."    <td width='65'><b>Lon</b></td>"
            ."    <td width='65'><b>Lat</b></td>"
            ."    <td width='65'><b>Miles</b></td>"
            ."    <td width='65'><b>KM</b></td>"
            ."    <td width='70'><b>Date</b></td>"
            ."    <td width='35'><b>UTC</b></td>"
            ."    <td><b>Order</b></td>"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td bgcolor='#f0f0f0'><input type='hidden' name='Award_LT Alert' value='0'>LT-305</td>\n"
            ."    <td bgcolor='#f0f0f0'>Alert, NU, Canada</td>\n"
            ."    <td bgcolor='#f0f0f0'>62.2083W</td>\n"
            ."    <td bgcolor='#f0f0f0'>82.5208N</td>\n"
            ."    <td bgcolor='#f0f0f0'>".$row['dx_miles']."</td>\n"
            ."    <td bgcolor='#f0f0f0'>".$row['dx_km']."</td>\n"
            ."    <td bgcolor='#f0f0f0'>".$row['date']."</td>\n"
            ."    <td bgcolor='#f0f0f0'>".$row['time']."</td>\n"
            ."    <td bgcolor='#f0f0f0' valign='top' align='center'>"
            .$this->drawCartButton('LT Alert')
            ."</td>"
            ."  </tr>\n"
            ."</table>\n"
            ."<br><br>\n";
        return $out;
    }

    /**
     * @return string
     */
    protected function drawNorth60()
    {
        $n60_awards = array(
            5,10,20,30
        );

        $sql =
             "SELECT DISTINCT\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`type` = 0 AND\n"
            ."  `signals`.`lat` >= 60 AND\n"
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
            ."ORDER BY `khz`,`call`";
        $result =        \Rxx\Database::query($sql);
        if (!\Rxx\Database::numRows($result)) {
              return "";
        }
        $out =
             "<b>North of 60 Awards</b> (".\Rxx\Database::numRows($result)." qualifying stations)<br>"
            ."This cerificate recognises reception of NDBs located at least 60 Degrees of latitude north of the Equator. "
            ."This area includes Iceland, Greenland and Alaska as well as most of Scandanavia and parts of Russia and Canada."
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='border:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='132' valign='top'><b>Stations</b></td>\n"
            ."    <td width='25' valign='top' rowspan='".(Count($n60_awards)+1)."' bgcolor='#ffffff' style='border: none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>"
            ."    <td width='591' valign='top'><b>Station</b></td>"
            ."    <td width='20' valign='top'><b>Order</b></td>"
            ."  </tr>\n";
        $old_level = 0;
        for ($i=0; $i<count($n60_awards); $i++) {
            $level =    $n60_awards[$i];
            $out.=
                 "  <tr>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>".$level."</td>\n";
            if (\Rxx\Database::numRows($result)>$old_level) {
                $Eligible = (\Rxx\Database::numRows($result) >= $level);
                $out.=
                     "    <td bgcolor='#f0f0f0' valign='top' style='font-family: courier;'>"
                    .($Eligible ? "" : "<font color='#808080'>");
                for ($j = $old_level; $j<$level; $j++) {
                    if ($j<\Rxx\Database::numRows($result)) {
                        $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                        $out.=    (float)$row['khz']."-".Rxx::pad_nbsp($row['call'], 3)." ";
                    }
                }
                $out.=
                     ($Eligible ? "" : "</font>")
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' align='center'>";
                if ($Eligible) {
                    $out.= $this->drawCartButton("North of Sixty ($level stations)");
                } else {
                    $out.=    "&nbsp;";
                }
                $out.=    "</td>";
                $old_level =    $level;
            } else {
                $out.=
                     "    <td bgcolor='#f0f0f0'>&nbsp;</td>\n"
                    ."    <td valign='top' bgcolor='#f0f0f0' align='center'>&nbsp;</td>";
            }
            $out.=    "  </tr>\n";
        }
        $out.=
             "</table>\n"
            ."<br><br>\n";
        return $out;
    }

    /**
     * @return string
     */
    protected function drawTransatlantic()
    {
        $transatlantic_awards_eu = array(1,10);
        $transatlantic_awards_na = array(1,3);
        if ($this->_listener_region!="na" && $this->_listener_region!="eu") {
            return "";
        }
        if ($this->_listener_region=="na") {
            $transatlantic_awards = $transatlantic_awards_na;
        } else {
            $transatlantic_awards = $transatlantic_awards_eu;
        }
      // can qualify for Transatlantic
        $sql =
             "SELECT DISTINCT\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`itu`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`,\n"
            ."  `itu`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`type` = 0 AND\n"
            ."  `signals`.`itu` = `itu`.`ITU` AND\n"
            .($this->_listener_region=="na" ?
                "  (`itu`.`region` = 'eu' or `itu`.`region` = 'af') AND\n"
             :
                "  (`itu`.`region` = 'na' or `itu`.`region` = 'ca' or `itu`.`region` = 'sa') AND\n"
            )
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
            ."ORDER BY `khz`,`call`";
        $result =    \Rxx\Database::query($sql);
        if (!\Rxx\Database::numRows($result)) {
            return "";
        }
        $out =
             "<b>Transatlantic DX Awards</b> (".\Rxx\Database::numRows($result)." qualifying stations)<br>"
            ."This is probably the most challenging award of the program, even at its first level. However, there is a lot of interest on "
            ."both sides of the Atlantic in transoceanic NDB DX. To qualify, listeners must hear NDBs on the other side. "
            ."North American listeners may count NDBs in the Azores, the Canary Islands and Cape Verde as well as those on the "
            ."European and African continents proper. European listeners may count NDBs in Greenland, Canada, the United States "
            ."and the islands of the caribbean as well as Central and South America."
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='borde:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='132' valign='top'><b>Stations</b></td>\n"
            ."    <td width='25' valign='top' rowspan='".(Count($transatlantic_awards)+1)."' bgcolor='#ffffff' style='border:none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>"
            ."    <td width='591' valign='top'><b>Station</b></td>"
            ."    <td width='20' valign='top'><b>Order</b></td>"
            ."  </tr>\n";
        $old_level = 0;

        for ($i=0; $i<count($transatlantic_awards); $i++) {
            $level =    $transatlantic_awards[$i];
            $out.=
                 "  <tr>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>".$level."</td>\n";

            if (\Rxx\Database::numRows($result)>$old_level) {
                $Eligible = (\Rxx\Database::numRows($result) >= $level);
                $out.=
                     "    <td bgcolor='#f0f0f0' valign='top' style='font-family: courier;'>"
                    .($Eligible ? "" : "<font color='#808080'>");
                for ($j = $old_level; $j<$level; $j++) {
                    if ($j<\Rxx\Database::numRows($result)) {
                        $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                        $out.=    (float)$row['khz']."-".Rxx::pad_nbsp($row['call'], 3)." ";
                    }
                }
                $out.=
                     ($Eligible ? "" : "</font>")
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' align='center'>";
                if ($Eligible) {
                    $out.= $this->drawCartButton("Transatlantic NDB DX ($level stations)");
                } else {
                    $out.=    "&nbsp;";
                }
                $out.=    "</td>";
                $old_level =    $level;
            } else {
                $out.=
                     "    <td bgcolor='#f0f0f0'>&nbsp;</td>\n"
                    ."    <td valign='top' bgcolor='#f0f0f0' align='center'>&nbsp;</td>";
            }
            $out.=    "  </tr>\n";
        }
        $out.=
             "</table>\n"
            ."<br><br>\n";
        return $out;
    }

    /**
     * @return string
     */
    protected function drawTranspacific()
    {
        $transpacific_awards = array(1,2,3,4);
        if ($this->_listener_region!="na" &&
            $this->_listener_region!="ca" &&
            $this->_listener_region!="sa" &&
            $this->_listener_region!="as" &&
            $this->_listener_ITU!="AUS" &&
            $this->_listener_ITU!="NZL" &&
            $this->_listener_ITU!="PNG"
        ) {
            return "";
        }
      // can qualify for Transpacific
        $sql =
             "SELECT DISTINCT\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`itu`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`,\n"
            ."  `itu`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`type` = 0 AND\n"
            ."  `signals`.`ITU` = `itu`.`ITU` AND\n"
            .(
                ($this->_listener_region=="na" || $this->_listener_region=="ca" || $this->_listener_region=="sa" ?
                    "  (`itu`.`region` = 'as' or `signals`.`ITU` = 'AUS' or `signals`.`ITU` = 'PNG' or `signals`.`ITU` = 'NZL') AND\n"
                 :
                    "  (`itu`.`region` = 'na' or `itu`.`region` = 'ca' or `itu`.`region` = 'sa') AND\n"
                 )
            )
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
            ."ORDER BY `khz`,`call`";
        $result =    \Rxx\Database::query($sql);
        if (!\Rxx\Database::numRows($result)) {
            return "";
        }
        $out =
             "<b>Transpacific DX Awards</b> (".\Rxx\Database::numRows($result)." qualifying stations)<br>"
            ."Yes, we must also be fair to those interested in the \"Really Big Pond\". This certificate recognises "
            ." reception of NDBs accross the pacific in either direction.<br>"
            ."<ol><li>For listeners in North, central or South America qualifying beacons must be in Asian countries or on "
            ."Australian mainland, in New Zealand or Papua New Guinea, but not other countries in Oceania.</li>"
            ."<li>For listeners in Asia, Australia or Papua New Guinea qualifying beacons must be in "
            ."North, Central or South America</li></ol>"
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='border:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='132' valign='top'><b>Stations</b></td>\n"
            ."    <td width='25' valign='top' rowspan='".(Count($transpacific_awards)+1)."' bgcolor='#ffffff' style='border:none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>"
            ."    <td width='591' valign='top'><b>Station</b></td>"
            ."    <td width='20' valign='top'><b>Order</b></td>"
            ."  </tr>\n";
        $old_level = 0;
        for ($i=0; $i<count($transpacific_awards); $i++) {
            $level =    $transpacific_awards[$i];
            $out.=
                 "  <tr>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>".$level."</td>\n";

            if (\Rxx\Database::numRows($result)>$old_level) {
                $Eligible = (\Rxx\Database::numRows($result) >= $level);
                $out.=
                     "    <td bgcolor='#f0f0f0' valign='top' style='font-family: courier;'>"
                    .($Eligible ? "" : "<font color='#808080'>");
                for ($j = $old_level; $j<$level; $j++) {
                    if ($j<\Rxx\Database::numRows($result)) {
                        $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                        $out.=  (float)$row['khz']."-".Rxx::pad_nbsp($row['call'], 3)." ";
                    }
                }
                $out.=
                     ($Eligible ? "" : "</font>")
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' align='center'>";
                if ($Eligible) {
                    $out.= $this->drawCartButton("Transpacific NDB DX ($level stations)");
                } else {
                    $out.=    "&nbsp;";
                }
                $out.=    "</td>";
                $old_level =    $level;
            } else {
                $out.=
                     "    <td bgcolor='#f0f0f0'>&nbsp;</td>\n"
                    ."    <td valign='top' bgcolor='#f0f0f0' align='center'>&nbsp;</td>";
            }
            $out.=    "  </tr>\n";
        }
        $out.=
             "</table>\n"
            ."<br><br>\n";
        return $out;
    }

    /**
     * @return string
     */
    protected function drawUsTranscontinental()
    {
        $usa_transcont_awards = array(1,2,3,4);
        $sql =
             "SELECT DISTINCT\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`type` = 0 AND\n"
            ."  `signals`.`ITU` = 'USA' AND\n"
            ."  (`signals`.`SP` = 'FL' OR `signals`.`SP` = 'GA' OR `signals`.`SP` = 'SC' OR `signals`.`SP` = 'NC' OR `signals`.`SP` = 'VA' OR `signals`.`SP` = 'MD' OR `signals`.`SP` = 'DE' OR `signals`.`SP` = 'NJ' OR `signals`.`SP` = 'NY' OR `signals`.`SP` = 'CT' OR `signals`.`SP` = 'RI' OR `signals`.`SP` = 'MA' OR `signals`.`SP` = 'NH' OR `signals`.`SP` = 'ME') AND\n"
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
            ."ORDER BY `khz`,`call`";
        $result_atl =    \Rxx\Database::query($sql);
        $sql =
             "SELECT DISTINCT\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`SP`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signals`.`ID` = `logs`.`signalID` AND\n"
            ."  `signals`.`type` = 0 AND\n"
            ."  (`signals`.`ITU` = 'USA' OR `signals`.`ITU` = 'ALS') AND\n"
            ."  (`signals`.`SP` = 'CA' OR `signals`.`SP` = 'OR' OR `signals`.`SP` = 'WA' OR `signals`.`SP` = 'AK') AND\n"
            ."  `logs`.`listenerID` = '".$this->_listenerID."'\n"
            ."ORDER BY `khz`,`call`";
        $result_pac =    \Rxx\Database::query($sql);

        if (!(\Rxx\Database::numRows($result_atl) && \Rxx\Database::numRows($result_pac))) {
            return "";
        }
        $out =
             "<b>US Transcontinental NDB DX Award</b> (".(\Rxx\Database::numRows($result_atl)>=\Rxx\Database::numRows($result_pac) ? \Rxx\Database::numRows($result_pac) : \Rxx\Database::numRows($result_atl))." qualifying pairs of stations)<br>"
            ."This cerificate recognises reception of NDBs on Pacific and Atlantic coasts of continental United States. "
            ."Qualifying beacons must be in states with oceanic shorelibes (excluding the Gulf of Mexico)."
            ."<table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='border:none; border-collapse:collapse;'>\n"
            ."  <tr class='downloadTableHeadings_nosort'>\n"
            ."    <td width='132' valign='top'><b>Stations</b></td>\n"
            ."    <td width='25' valign='top' rowspan='".(Count($usa_transcont_awards)+1)."' bgcolor='#ffffff' style='border: none;'><img src='".BASE_PATH."assets/spacer.gif' width='25' height='1' alt=''></td>"
            ."    <td width='591' valign='top'><b>Station</b></td>"
            ."    <td width='20' valign='top'><b>Order</b></td>"
            ."  </tr>\n";
        $old_level = 0;
        for ($i=0; $i<count($usa_transcont_awards); $i++) {
            $level =    $usa_transcont_awards[$i];
            $out.=
                 "  <tr>\n"
                ."    <td bgcolor='#f0f0f0' valign='top' nowrap>".$level." on each coast</td>\n";
            if (\Rxx\Database::numRows($result_atl)>$old_level && \Rxx\Database::numRows($result_pac)>$old_level) {
                $Eligible_atl = (\Rxx\Database::numRows($result_atl) >= $level);
                $Eligible_pac = (\Rxx\Database::numRows($result_pac) >= $level);
                $out.=
                     "    <td bgcolor='#f0f0f0' valign='top' style='font-family: courier;'>"
                    .($Eligible_atl ? "" : "<font color='#808080'>")
                    ."Atlantic Coast:<br>\n";
                for ($j = $old_level; $j<$level; $j++) {
                    if ($j<\Rxx\Database::numRows($result_atl)) {
                        $row_atl =    \Rxx\Database::fetchArray($result_atl, MYSQL_ASSOC);
                        $out.= (float)$row_atl['khz']."-".Rxx::pad_nbsp($row_atl['call'], 3)." (".$row_atl['SP'].") ";
                    }
                }
                $out.=
                     ($Eligible_atl ? "" : "</font>")
                    ."<br>\n"
                    .($Eligible_pac ? "" : "<font color='#808080'>")
                    ."Pacific Coast:<br>\n";
                for ($j = $old_level; $j<$level; $j++) {
                    if ($j<\Rxx\Database::numRows($result_pac)) {
                        $row_pac =    \Rxx\Database::fetchArray($result_pac, MYSQL_ASSOC);
                        $out.= (float)$row_pac['khz']."-".Rxx::pad_nbsp($row_pac['call'], 3)." (".$row_pac['SP'].") ";
                    }
                }
                $out.=
                     ($Eligible_pac ? "" : "</font>")
                    ."</td>"
                    ."    <td bgcolor='#f0f0f0' align='center'>";
                if ($Eligible_atl && $Eligible_pac) {
                    $out.= $this->drawCartButton("US Transcontinental ($level stations on each coast)");
                } else {
                    $out.= ">&nbsp;";
                }
                $out.=    "</td>";
                $old_level =    $level;
            } else {
                $out.=
                     "    <td bgcolor='#f0f0f0'>&nbsp;</td>\n"
                    ."    <td valign='top' bgcolor='#f0f0f0' align='center'>&nbsp;</td>\n";
            }
            $out.=
                "  </tr>\n";
        }
        $out.=
             "</table>\n"
            ."<br><br>\n";
        return $out;
    }

    protected function drawCartButton($id)
    {
        $id = str_replace(
            array(' / ',   ' ', '(', ')', '/'),
            array('_and_', '_', '',  '',  '_'),
            $id
        );
        return
             "<input type='hidden' id='Award_".$id."' value='0' />"
            ."<a id='".$id."_icon_show' href='.'"
            ." onclick=\"award('".$id."');return false\">"
            ."<img src='".BASE_PATH."assets/icon_cart.gif' alt='No'>"
            ."</a>"
            ."<a id='".$id."_icon_hide' style='display: none;' href='.'"
            ." onclick=\"award('".$id."');return false\">"
            ."<img src='".BASE_PATH."assets/icon_cart_added.gif' alt='Yes'></a>";
    }

    /**
     * @return string
     */
    protected function drawCheckout()
    {
        global $mode;
        return
             "<hr><br><h1><a name='checkout'></a>Checkout</h1><br>\n"
            ."<b>Reply To:</b>"
            ." <input type='text' size='30' name='awards_email' id='awards_email' value=\""
            .(Rxx::get_listener_email($this->_listenerID) ?
                Rxx::get_listener_email($this->_listenerID)
              :
                "(enter email address)"
             )
            ."\">\n"
            ."<input type='hidden' name='awards_url' id='awards_url' value=\""
            ."http://".$_SERVER["SERVER_NAME"].system_URL."/".$mode."/".$this->_listenerID
            ."\">\n"
            ."<input type='hidden' name='awards_coordinator_name' id='awards_coordinator_name' value=\""
            .awardsAdminName
            ."\">\n"
            ."<input type='hidden' name='awards_requester' id='awards_requester' value=\""
            .$this->_listener_details
            ."\">\n"
            ."<input type='hidden' name='awards_name' id='awards_name' value=\""
            .$this->_listener_name
            ."\">\n"
            ."<textarea name='awards_requested' id='awards_requested' rows='15' cols='40' style='width: 100%'>"
            ."(No certificates have been selected)"
            ."</textarea><br>\n"
            ."<p align='center'>"
            ."<input type='button' class='formButton' id='order' name='order' value='Place Order' disabled='disabled'"
            ." onclick='award_place_order(document.form)'"
            ."></p>\n";
    }

    protected function getListenerFilterSQL()
    {
        global $region;
        switch (system) {
            case "RNA":
                $this->listenerFilterSQL =
                    "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `SP` = 'hi'))";
                break;
            case "REU":
                $this->listenerFilterSQL =
                    "(`region` = 'eu')";
                break;
            case "RWW":
                if ($region!="") {
                    $this->listenerFilterSQL =
                        "(`region` = '$region')";
                } else {
                    $this->listenerFilterSQL =
                        "1";
                }
                break;
        }
    }

    protected function loadListenerDetails()
    {
        if (!$this->_listenerID) {
            return;
        }
        $sql = "SELECT * FROM `listeners` WHERE `ID` = ".$this->_listenerID;
        $result =                   \Rxx\Database::query($sql);
        $this->_listener_record =   \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
        $this->_listener_ITU =      $this->_listener_record['ITU'];
        $this->_listener_name =     $this->_listener_record['name'];
        $this->_listener_region =   Rxx::get_listener_region($this->_listenerID);
        $this->_listener_details =  Rxx::get_listener_details($this->_listenerID);
    }

    /**
     * @param $awards_requested
     * @param $awards_email
     * @param $awards_name
     * @return string
     */
    protected function send($awards_requested, $awards_email, $awards_name)
    {
        $mail = new PHPMailer();
        $mail->PluginDir =      "../";
        $mail->IsHtml(true);
        $mail->Mailer =         "smtp";
        $mail->Host =           SMTP_HOST;
        $mail->SMTPAuth = "";

        $mail->Sender =   'rxx@classaxe.com';
        $mail->AddCustomHeader('Errors-To:rxx@classaxe.com');
        $mail->AddReplyTo($awards_email, $awards_name);
        $mail->SetFrom($awards_email, $awards_name, false);
        $mail->AddAddress(awardsAdminEmail, awardsAdminName);
        $mail->AddAddress(awardsBCCEmail, awardsBCCName);

        $mail->Subject =        "RXX Awards";
        $mail->Body =           nl2br($awards_requested);
        $mail->AltBody =        $awards_requested;
        $mail_result =          ($mail->Send() ? "Message-ID: ".$mail->MessageID : $mail->ErrorInfo);
        if (substr($mail_result, 0, 12)!="Message-ID: ") {
            return "An error has occured - ".$mail_result.".<br />";
        }

        return
             "<h1>Awards</h1><p>Thank you. Your request has been submitted and a copy "
            ."will be sent to the email address you provided for your records.</p>\n"
            ."<table cellpadding='5' cellspacing='0' border='1' bordercolor='#000000' bgcolor='#ffffff'>\n"
            ."  <tr>\n"
            ."    <td>From:</td>\n"
            ."    <td>".stripslashes($awards_email)."</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td>Reply To:</td>\n"
            ."    <td>".stripslashes($awards_email)."</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td>TO:</td>\n"
            ."    <td>".awardsAdminEmail."</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td>CC:</td>\n"
            ."    <td>".stripslashes($awards_email)."</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td valign='top'>SUBJECT:</td>\n"
            ."    <td>NDBList Awards request</td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td valign='top'>MESSAGE:</td>\n"
            ."    <td><pre>".stripslashes($awards_requested)."</pre></td>\n"
            ."  </tr>\n"
            ."</table>\n";
    }
}
