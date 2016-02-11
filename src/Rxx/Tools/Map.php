<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:36 PM
 */

namespace Rxx\Tools;

/**
 * Class Map
 * @package Rxx\Tools
 */
class Map
{
    /**
     * @return mixed
     */
    public static function maps()
    {
        switch (\Rxx\Rxx::$system) {
            case "RNA":
                return self::maps_rna();
            break;
            case "REU":
                return self::maps_reu();
            break;
            case "RWW":
                return self::maps_rww();
            break;
        }
    }

    /**
     * @return string
     */
    public static function map_af()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='af'></a>Africa Country Codes</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>\n"
        ."<a href='javascript:show_itu(\"af\")' title='NDBList Country codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0'></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_af','map_af','scrollbars=0,resizable=0',646,652,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'><img src='".\Rxx\Rxx::$base_path."assets/images/af_map.gif'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_alaska()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='alaska'></a>Beacons in Alaska</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_alaska','map_alaska','scrollbars=0,resizable=0',466,411,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>OR... try the <a href='./?mode=state_map&simple=1&SP=AK' target='_blank'><b>interactive map of Alaska</b></a></td>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'><img src='".\Rxx\Rxx::$base_path."assets/images/map_alaska_beacons.gif'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_as()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='as'></a>Asia Country Codes</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>"
        ."<a href='javascript:show_itu(\"as\")' title='NDBList Country codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0'></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_as','map_as','scrollbars=0,resizable=0',856,575,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'><img src='".\Rxx\Rxx::$base_path."assets/images/as_map.gif'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_au()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='au'></a>Australian NDB List approved Country Codes</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>"
        ."<a href='javascript:show_itu(\"oc\")' title='NDBList Country codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0'></a>\n"
        ."<a href='javascript:show_sp()' title='NDBList Territory codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-territory-codes.gif' border='0'></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_au','map_au','scrollbars=0,resizable=0',511,469,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent' align='center'><img src='".\Rxx\Rxx::$base_path."assets/images/au_map.gif' alt='Australian map'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_eu()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='eu'></a>Europe Country Codes</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>"
        ."<a href='javascript:show_itu(\"eu\")' title='NDBList Country codes'>"
        ."<img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0' alt=''></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_eu','map_eu','scrollbars=0,resizable=0',704,696,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0' alt=''></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'><img src='".\Rxx\Rxx::$system_url."/generate_map_eu' alt='European map'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_japan()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='japan'></a>Japan Country Codes</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>\n"
        ."<a href='javascript:show_itu(\"as\")' title='NDBList Country codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0'></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_japan','map_japan','scrollbars=0,resizable=0',517,690,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'><img src='".\Rxx\Rxx::$base_path."assets/images/japan_map.gif'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_na()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='na'></a>North + Central American NDB List approved Country Codes</th>\n"

        ."        <th align='right' class='downloadTableHeadings_nosort'>"
        ."<a href='javascript:show_itu(\"na\")' title='NDBList Country codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0'></a>\n"
        ."<a href='javascript:show_sp()' title='NDBList State and Province codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-state-codes.gif' border='0'></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_na','map_na','scrollbars=0,resizable=0',669,651,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent' align='center'><img src='".\Rxx\Rxx::$system_url."/generate_map_na' alt='North American map'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_pacific()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='pacific'></a>Pacific Beacons Map</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>"
        ."<a href='javascript:show_itu(\"oc\")' title='NDBList Country codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0'></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_pacific','map_pacific','scrollbars=0,resizable=0',366,429,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'><img src='".\Rxx\Rxx::$base_path."assets/images/pacific_map.gif'></td>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent' align='center'>(Originally produced for Steve Ratzlaff's <a href='../log/steve' target='_blank'><b>Pacific Report</b></a>)</td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_place_finder()
    {
        global $place;
        if (!isset($place)) {
            $place='';
        }
        $out = array();

        $out[]="<form name='form' action='".\Rxx\Rxx::$system_url."' method='post'>\n"
            ."<input type='hidden' name='mode' value='".\Rxx\Rxx::$system_mode."'>\n"
            ."<input type='hidden' name='submode' value='lookup'>\n"
            ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='places'></a>USA / Canada Place Finder</th>\n"
            ."        <th align='right' class='downloadTableHeadings_nosort'><a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a></th>\n"
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'><b>Place Name:</b>\n"
            ."<input type='text' class='formField' name='place' value='$place'>\n"
            ."<input type='submit' class='formButton' value='Find'></td>\n"
            ."  </tr>";

        if (\Rxx\Rxx::$system_submode<>'' && $place<>'') {
            $sql =  "SELECT * FROM `places` WHERE `name` LIKE \"%".addslashes($place)."%\" ORDER BY `itu`,`sp`,`population` DESC";
            $result =   @\Rxx\Database::query($sql);
            if (\Rxx\Database::numRows($result)) {
                $out[] =     "  <tr>\n"
                    ."    <td class='downloadTableContent'><b>Matches</b><br>\n"
                    ."    <table cellpadding='1' cellspacing='0' border='1' bordercolor='#c0c0c0' style='border-collapse: collapse;'>\n"
                    ."      <tr class='downloadTableHeadings_nosort'>\n"
                    ."        <td width='65' valign='top' nowrap><b>Name</b></td>\n"
                    ."        <td width='25' valign='top'><b>State</b></td>\n"
                    ."        <td width='25' valign='top'><b>ITU</b></td>\n"
                    ."        <td width='65' valign='top'><b>Population</b></td>"
                    ."        <td width='65' valign='top'><b>Lat</b></td>"
                    ."        <td width='65' valign='top'><b>Lon</b></td>"
                    ."        <td width='25' valign='top'><b>GSQ</b></td>"
                    ."      </tr>\n";

                while ($row = \Rxx\Database::fetchArray($result, MYSQL_ASSOC)) {
                    $out[] =     "      <tr>\n"
                        ."        <td>".$row['name']."</td>\n"
                        ."        <td>".$row['sp']."</td>\n"
                        ."        <td>".$row['itu']."</td>\n"
                        ."        <td align='right'>".($row['population'] ? $row['population'] : "&nbsp;")."</td>\n"
                        ."        <td>".$row['lat']."</td>\n"
                        ."        <td>".$row['lon']."</td>\n"
                        ."        <td><a onmouseover='window.status=\"View map for ".$row['name']." (".$row['sp'].")\";return true' onmouseout='window.status=\"\";return true;' href='javascript:popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\")' title='Show map'><span class='fixed'>".\Rxx\Rxx::deg_GSQ($row['lat'], $row['lon'])."</span></td>\n"
                        ."      </tr>\n";
                }
                $out[] =     "    </table></td>\n"
                    ."  </tr>\n";
            } else {
                $out[] =     "  <tr>\n"
                    ."    <td class='downloadTableContent'><b>No Matches found</b></td>\n"
                    ."  </tr>\n";
            }
        }
        $out[] =    "</table>\n"
            ."</form>\n";
        return implode($out, "");
    }

    /**
     * @return string
     */
    public static function map_polynesia()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='frpoly'></a>French Polynesian Map</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_polynesia','map_polynesia','scrollbars=0,resizable=0',458,440,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent' align='center'><img src='".\Rxx\Rxx::$base_path."assets/images/map_french_polynesia.gif'></td>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent' align='center'>(Originally produced for Steve Ratzlaff's <a href='../log/steve' target='_blank'><b>Pacific Report</b></a>)</td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_sa()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='sa'></a>South American NDB List approved Country Codes</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'>"
        ."<a href='javascript:show_itu(\"sa\")' title='NDBList Country codes'><img src='".\Rxx\Rxx::$base_path."assets/icon-country-codes.gif' border='0'></a>\n"
        .(\Rxx\Rxx::$system_mode=="maps" ?
            "<a href=\"javascript:popWin('".\Rxx\Rxx::$system_url."/map_sa','map_sa','scrollbars=0,resizable=0',499,696,'centre')\"><img src='".\Rxx\Rxx::$base_path."assets/icon-popup.gif' border='0'></a>\n"
            ."<a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a>\n":"")
        ."</th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent' align='center'><img src='".\Rxx\Rxx::$base_path."assets/images/sa_map.gif'></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function map_state_popup()
    {
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='state'></a>Detailed State Maps</th>\n"
        ."        <th align='right' class='downloadTableHeadings_nosort'><a href='#top' class='yellow'><img src='".\Rxx\Rxx::$base_path."assets/icon-top.gif' border='0'></a></th>\n"
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>Click <a href='".\Rxx\Rxx::$system_url."/state_map?simple=1&SP=AK' target='_blank'><b>here</b></a> to see detailed customisable maps for each US state.</td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function maps_reu()
    {
        return "<h2>Maps for European Listeners</h2>\n"
        ."<p align='center'><small>Quick Links [\n"
        ."<nobr><a href='#eu'><b>Europe</b></a></nobr> |\n"
        ."<nobr><a href='#as'><b>Asia</b></a></nobr> |\n"
        ."<nobr><a href='#af'><b>Africa</b></a></nobr>\n"
        ."]</small></p><br><br>\n"
        .\Rxx\Tools\Map::map_eu()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_as()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_af()."<br><br><br>\n";
    }

    /**
     * @return string
     */
    public static function maps_rna()
    {
//http://earth-info.nga.mil/gns/html/index.html
        $out = array();
        return "<h2>Maps for North American Listeners</h2>\n"
        ."<p align='center'><small>Quick Links [\n"
        ."<nobr><a href='#places'><b>USA Place finder</b></a></nobr> |\n"
        ."<nobr><a href='#state'><b>Detailed State Maps</b></a></nobr> |\n"
        ."<nobr><a href='#na'><b>North + Central America</b></a></nobr> |\n"
        ."<nobr><a href='#alaska'><b>Alaska</b></a></nobr> |\n"
        ."<nobr><a href='#sa'><b>South America</b></a></nobr> |\n"
        ."<nobr><a href='#pacific'><b>Pacific</b></a></nobr> |\n"
        ."<nobr><a href='#japan'><b>Japan</b></a></nobr> |\n"
        ."<nobr><a href='#frpoly'><b>French Polynesia</b></a></nobr>\n"
        ."]</small></p><br><br>\n"
        .\Rxx\Tools\Map::map_place_finder()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_state_popup()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_na()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_alaska()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_sa()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_pacific()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_japan()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_polynesia();
    }

    /**
     * @return string
     */
    public static function maps_rww()
    {
        return "<h2>Maps for All Listeners</h2>\n"
        ."<p align='center'><small>Quick Links [\n"
        ."<nobr><a href='#na'><b>North + Central America</b></a></nobr> |\n"
        ."<nobr><a href='#sa'><b>South America</b></a></nobr> |\n"
        ."<nobr><a href='#eu'><b>Europe</b></a></nobr> |\n"
        ."<nobr><a href='#as'><b>Asia</b></a></nobr> |\n"
        ."<nobr><a href='#af'><b>Africa</b></a></nobr> |\n"
        ."<nobr><a href='#au'><b>Australia</b></a></nobr> \n"
        ."]</small></p><br><br>\n"
        .\Rxx\Tools\Map::map_na()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_eu()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_as()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_af()."<br><br><br>\n"
        .\Rxx\Tools\Map::map_au()."\n";
    }

    /**
     * @return string
     */
    public static function state_map()
    {
        global  $SP, $ITU, $listenerID, $simple, $test, $lat, $lon, $filter_active, $hide_labels, $hide_placenames;
        global  $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER, $places, $ID;

        switch (\Rxx\Rxx::$system) {
            case "RNA":
                $filter_listener_SQL = "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `SP` = 'hi'))";
                break;
            case "REU":
                $filter_listener_SQL = "(`region` = 'eu')";
                break;
            case "RWW":
                $filter_listener_SQL = "1";
                break;
        }

        $filter_type =  array();
        if (!($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
            switch (\Rxx\Rxx::$system) {
                case "RNA":
                    $type_NDB = 1;
                    break;
                case "REU":
                    $type_NDB = 1;
                    break;
                case "RWW":
                    $type_NDB = 1;
                    break;
            }
        }
        if ($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
            if ($type_NDB) {
                $filter_type[] =     "`type` = ".NDB;
            }
            if ($type_DGPS) {
                $filter_type[] =     "`type` = ".DGPS;
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
        $filter_type =  "(".implode($filter_type, " OR ").")";


        $out =  array();

        $out[] =     "<script language='javascript' type='text/javascript'>\n"
            ."function get_type(form){\n"
            ."  if (form.type_DGPS.checked)   return ".DGPS.";\n"
            ."  if (form.type_HAMBCN.checked) return ".HAMBCN.";\n"
            ."  if (form.type_NAVTEX.checked) return ".NAVTEX.";\n"
            ."  if (form.type_NDB.checked)    return ".NDB.";\n"
            ."  if (form.type_HAMBCN.checked) return ".HAMBCN.";\n"
            ."  if (form.type_OTHER.checked)  return ".OTHER.";\n"
            ."  if (form.type_TIME.checked)   return ".TIME.";\n"
            ."  return \"\";\n"
            ." }\n"
            ."</script>"
            ."<form name='form' action='".\Rxx\Rxx::$system_url."' method='GET'>\n"
            ."<input type='hidden' name='mode' value='\Rxx\Rxx::$system_mode'>\n"
            ."<table cellpadding='0' cellspacing='0' border='0'>\n"
            ."  <tr>\n"
            ."    <td><h1>Detailed State maps</h1></td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td><li>These maps are derived from those produced by Ray Sterner,  licenced by <a href='http://www.landforms.biz' target='_blank'><b>www.landforms.biz</b></a> and used here with their kind permission.</li>\n"
            .     "<li>Move mouse over transmitters to see information for any station shown or click for full details. Darker icons indicate a cluster of transmitters at a location.</li><br>&nbsp;</td>\n"
            ."  </tr>\n"
            ."</table>\n"
            ."<table cellpadding='0' border='0' cellspacing='0'>\n"
            ."  <tr>\n"
            ."    <td align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0'>\n"
            ."      <tr>\n"
            ."        <td align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_left.gif' width='15' height='18' class='noprint'></td>\n"
            ."            <td width='100%' class='downloadTableHeadings_nosort' align='center'>Customise Map</td>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_right.gif' width='15' height='18' class='noprint'></td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td><table cellpadding='0' cellspacing='0' class='tableForm' border='1' bordercolor='#c0c0c0'>\n"
            ."          <tr class='rowForm'>\n"
            ."            <th align='left'>Types&nbsp;</th>\n"
            ."            <td nowrap style='padding: 0px;'><table cellpadding='0' cellspacing='1' border='0' width='100%' class='tableForm'>\n"
            ."              <tr>\n"
            ."                <td bgcolor='#00D8FF' width='16%' nowrap onclick='toggle(document.form.type_DGPS)'><input type='checkbox' onclick='toggle(document.form.type_DGPS);' name='type_DGPS' value='1'".($type_DGPS? " checked" : "").">DGPS</td>"
            ."                <td bgcolor='#B8FFC0' width='17%' nowrap onclick='toggle(document.form.type_HAMBCN)'><input type='checkbox' onclick='toggle(document.form.type_HAMBCN)' name='type_HAMBCN' value='1'".($type_HAMBCN ? " checked" : "").">Ham</td>"
            ."                <td bgcolor='#FFB8D8' width='17%' nowrap onclick='toggle(document.form.type_NAVTEX)'><input type='checkbox' onclick='toggle(document.form.type_NAVTEX)' name='type_NAVTEX' value='1'".($type_NAVTEX ? " checked" : "").">NAVTEX&nbsp;</td>"
            ."                <td bgcolor='#FFFFFF' width='17%' nowrap onclick='toggle(document.form.type_NDB)'><input type='checkbox' onclick='toggle(document.form.type_NDB)' name='type_NDB' value='1'".($type_NDB? " checked" : "").">NDB</td>"
            ."                <td bgcolor='#FFE0B0' width='17%' nowrap onclick='toggle(document.form.type_TIME)'><input type='checkbox' onclick='toggle(document.form.type_TIME)' name='type_TIME' value='1'".($type_TIME? " checked" : "").">Time</td>"
            ."                <td bgcolor='#B8F8FF' width='16%' nowrap onclick='toggle(document.form.type_OTHER)'><input type='checkbox' onclick='toggle(document.form.type_OTHER)' name='type_OTHER' value='1'".($type_OTHER ? " checked" : "").">Other</td>"
            ."              </tr>\n"
            ."            </table></td>"
            ."          </tr>\n"
            ."          <tr class='rowForm'>\n"
            ."            <th align='left' width='70'>State</th>\n"
            ."            <td><select name='SP' class='formfield' style='font-family: monospace; width: 100%''>\n"
            ."<option value='' style='color: #0000ff;'>Select a state</option>\n";


        $sql =   "SELECT\n"
            ."  `maps`.`SP`,\n"
            ."  `maps`.`ITU`,\n"
            ."  `sp`.`name`\n"
            ."FROM\n"
            ."  `maps`\n"
            ."left join\n"
            ."  `sp` on `sp`.`SP` = `maps`.`sp`\n"
            ."ORDER BY\n"
            ."  `maps`.`itu`,\n"
            ."  `maps`.`SP";
        $result =   @\Rxx\Database::query($sql);
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
            $thisITU =  $row['ITU'];
            $thisSP =   $row['SP'];
            $thisName =     $row['name'];
            $out[] =    "<option value='$thisSP'".($thisSP == $SP ? " selected" : "").">$thisITU $thisSP $thisName</option>\n";
        }
        $out[] =     "            </select></td>\n"
            ."          </tr>\n"
            ."          <tr class='rowForm'>\n"
            ."            <th align='left' width='70'>Signal</th>\n"
            ."            <td><select name='ID' class='formfield' style='font-family: monospace; width: 100%''>\n"
            ."<option value='' style='color: #0000ff;'>Select a station to highlight it</option>\n";


        $sql =  "SELECT `ID`,`khz`,`call`,`QTH`,`type` FROM `signals` WHERE `sp` = '$SP' ORDER BY `khz`,`call`";
        $result =   \Rxx\Database::query($sql);
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
            $thisID =   $row['ID'];
            $thisKhz =  (float)$row['khz'];
            $thisCall =     $row['call'];
            $thisQTH =  $row['QTH'];
            $out[] =    "<option value='$thisID'".($thisID == $ID ? " selected" : "").">$thisKhz $thisCall $thisQTH</option>\n";
        }
        $out[] =     "            </select></td>\n"
            ."          </tr>\n"
            ."          <tr class='rowForm'>\n"
            ."            <th align='left' nowrap>Logged by</th>\n"
            ."            <td><select name='listenerID' class='formfield' style='font-family: monospace;' style='width: 100%'>\n"
            .\Rxx\Rxx::get_listener_options_list($filter_listener_SQL, $listenerID, "Anyone")
            ."            </select></td>\n"
            ."          </tr>\n"
            ."          <tr class='rowForm'>\n"
            ."            <th align='left'>Details</th>\n"
            ."            <td><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."              <tr>\n"
            ."                <td><select name='simple' class='formfield' style='font-family: monospace; width: 110'>\n"
            ."                  <option value='1'".($simple==1 ? " selected" : "").">Hide terrain</option>"
            ."                  <option value='0'".($simple==0 ? " selected" : "").">Show terrain</option>"
            ."                  </select>"
            ."                </td>\n"
            ."                <td><select name='places' class='formfield' style='font-family: monospace; width: 230'>\n"
            ."                  <option value='0'".($places==0 ? " selected" : "").">Hide all places</option>"
            ."                  <option value='400000'".($places==400000 ? " selected" : "").">Show capitals + Pop >400,000</option>"
            ."                  <option value='200000'".($places==200000 ? " selected" : "").">Show capitals + Pop >200,000</option>"
            ."                  <option value='100000'".($places==100000 ? " selected" : "").">Show capitals + Pop >100,000</option>"
            ."                  <option value='40000'".($places==40000 ? " selected" : "").">Show capitals + Pop >40,000</option>"
            ."                  <option value='20000'".($places==20000 ? " selected" : "").">Show capitals + Pop >20,000</option>"
            ."                  <option value='10000'".($places==10000 ? " selected" : "").">Show capitals + Pop >10,000</option>"
            ."                  </select>"
            ."                </td>\n"
            ."              </tr>\n"
            ."            </table></td>\n"
            ."          </tr>\n"
            ."          <tr class='rowForm'>\n"
            ."            <th align='left'>Hide</th>\n"
            ."            <td><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."              <tr>\n"
            ."                <td><label for='chk_filter_active'><input id='chk_filter_active'type='checkbox' name='filter_active' value='1'".($filter_active ? " checked" : "").">Inactive stations&nbsp;</label></td>\n"
            ."                <td><label for='chk_hide_labels'><input id='chk_hide_labels' type='checkbox' name='hide_labels' value='1'".($hide_labels ? " checked" : "").">Station labels&nbsp;</label></td>\n"
            ."                <td><label for='chk_hide_placenames'><input id='chk_hide_placenames' type='checkbox' name='hide_placenames' value='1'".($hide_placenames ? " checked" : "").">Place names&nbsp;</label></td>\n"
            ."              </tr>\n"
            ."            </table></td>\n"
            ."          </tr>\n"
            ."          <tr class='rowForm'>\n"
            ."            <th colspan='2' class='noprint'><center><input type='submit' onclick='return send_form(form)' name='go' value='Go' style='width: 100px;' class='formButton' title='Execute search'>\n"
            ."    <input name='clear' type='button' class='formButton' value='Clear' style='width: 100px;' onclick='clear_state_map(document.form)'></center></th>"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."    </table></td>"
            ."    <td width='20'>&nbsp;</td>\n"
            ."    <td valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <td class='noprint' align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_left.gif' width='15' height='18'></td>\n"
            ."            <td width='100%' class='downloadTableHeadings_nosort' align='center' nowrap>Signal Details</td>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_right.gif' width='15' height='18'></td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td class='noprint'><table cellpadding='0' cellspacing='0' class='tableForm' border='1' bordercolor='#c0c0c0' width='100%'>"
            ."          <tr>\n"
            ."            <th align='left'>Type</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='info_type' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left' width='100%'>Call</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='info_call' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left'>KHz</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='info_khz' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left'>QTH</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='info_QTH' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left'>Lat</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='info_lat' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left'>Lon</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='info_lon' size='10'></td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."    </table></td>"
            ."    <td width='20'>&nbsp;</td>\n"
            ."    <td valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <td class='noprint' align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_left.gif' width='15' height='18'></td>\n"
            ."            <td width='100%' class='downloadTableHeadings_nosort' align='center' nowrap>Place Details</td>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_right.gif' width='15' height='18'></td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td class='noprint'><table cellpadding='0' cellspacing='0' class='tableForm' border='1' bordercolor='#c0c0c0' width='100%'>"
            ."          <tr>\n"
            ."            <th align='left'>Name</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='place_name' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left'>Type</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='place_type' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left' width='100%'>Pop</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 100' name='place_population' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left'>Lat</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 70' name='place_lat' size='10'></td>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <th align='left'>Lon</th>\n"
            ."            <td align='left'><input type='text' class='formField' style='width: 70' name='place_lon' size='10'></td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."    </table></td>"
            ."    <td width='20' class='noprint'>&nbsp;</td>\n"
            ."    <td valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <td align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."          <tr>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_left.gif' width='15' height='18' class='noprint'></td>\n"
            ."            <td width='100%' class='downloadTableHeadings_nosort' align='center' nowrap>Map Key</td>\n"
            ."            <td width='18'><img src='".\Rxx\Rxx::$base_path."assets/corner_top_right.gif' width='15' height='18' class='noprint'></td>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."      <tr>\n"
            ."        <td><table cellpadding='0' cellspacing='0' class='tableForm' border='1' bordercolor='#c0c0c0' width='100%'>"
            ."          <tr>\n"
            ."            <td align='right' width='20'><img src='".\Rxx\Rxx::$base_path."assets/map_point_DGPS.gif' alt='Represents a DGPS station'></td>\n"
            ."            <th align='left' nowrap>DGPS Station</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_HAMBCN.gif' alt='Represents an amateur radio beacon'></td>\n"
            ."            <th align='left' nowrap>Ham Beacon</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_NAVTEX.gif' alt='Represents a NAVTEX station'></td>\n"
            ."            <th align='left' nowrap>NAVTEX Station</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_NDB.gif' alt='Represents an NDB'></td>\n"
            ."            <th align='left' nowrap>NDB</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_TIME.gif' alt='Represents a Time Signal station'></td>\n"
            ."            <th align='left' nowrap>Time Station</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_OTHER.gif' alt='Represents a form of transmitter not otherwise classified'></td>\n"
            ."            <th align='left' nowrap>Other</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_inactive.gif' alt='Represents an inactive or decommissioned transmitter'></td>\n"
            ."            <th align='left' nowrap>Inactive</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_capital.gif' alt='Represents a state capital'></td>\n"
            ."            <th align='left' nowrap>State Capital</th>\n"
            ."          </tr>\n"
            ."          <tr>\n"
            ."            <td align='right'><img src='".\Rxx\Rxx::$base_path."assets/map_point_place.gif' alt='Represents other populated place'></td>\n"
            ."            <th align='left' nowrap>Town / City</th>\n"
            ."          </tr>\n"
            ."        </table></td>\n"
            ."      </tr>\n"
            ."    </table></td>"
            ."  </tr>\n"
            ."</table><br>\n";

        $sql =  "SELECT * FROM `maps` WHERE `SP` = '$SP'";
        $result =   \Rxx\Database::query($sql);
        if (\Rxx\Database::numRows($result)) {
            $coords =   \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
            global $ID;
            if (!$test) {
                $out[] =     "<img usemap=\"#map\" galleryimg=\"no\" src=\"./?mode=state_map_gif&SP=$SP&simple=$simple&listenerID=$listenerID&filter_active=$filter_active&"
                    ."type_DGPS=$type_DGPS&type_HAMBCN=$type_HAMBCN&type_NAVTEX=$type_NAVTEX&type_NDB=$type_NDB&type_TIME=$type_TIME&type_OTHER=$type_OTHER&"
                    ."hide_labels=$hide_labels&places=$places&hide_placenames=$hide_placenames&ID=$ID\" border=\"1\" bordercolor=\"#000000\">\n"
                    ."<map name=\"map\">\n";

                if ($places) {
                    $sql =      "SELECT * FROM `places` WHERE `sp` = '$SP' AND (`population`>=$places OR `capital` = '1')";
                    $result =   @\Rxx\Database::query($sql);
                    for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                        $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                        $xpos =     (int)($coords['ix2'] -  ((($coords['ix2'] - $coords['ix1'])/($coords['lon2'] - $coords['lon1'])) * ($coords['lon2'] - $row['lon'])));
                        $ypos =     (int)($coords['iy1'] + ((($coords['lat2'] - $row['lat']) / ($coords['lat2'] - $coords['lat1'])) * ($coords['iy2']-$coords['iy1'])));
                        $out[] =
                            "<area shape=\"circle\" alt=\"".$row['name'].($row['capital']=='1' ? " (State Capital)" : "")." - Population: ".$row['population']." (lat:".$row['lat'].", lon:".$row['lon'].")\" "
                            ."onmouseover=\"show_map_place('".$row['name']."','".$row['population']."','".$row['lat']."','".$row['lon']."','".$row['capital']."'); return true;\" "
                            ."onmouseout=\"show_map_place('','','','','',''); return true;\" "
                            ."coords=\"".($xpos).",".($ypos).",3"."\">\n";
                    }
                }
                $sql =   "SELECT DISTINCT\n"
                    ."  `signals`.`ID`,\n"
                    ."  `signals`.`active`,\n"
                    ."  `signals`.`call`,\n"
                    ."  `signals`.`khz`,\n"
                    ."  `signals`.`QTH`,\n"
                    ."  `signals`.`lat`,\n"
                    ."  `signals`.`lon`,\n"
                    ."  `signals`.`type`\n"
                    ."FROM\n"
                    ."  `signals`,\n"
                    ."  `logs`\n"
                    ."WHERE\n"
                    ."  `logs`.`signalID` = `signals`.`ID`\n"
                    .($filter_active ? " AND\n `active` = 1\n" : "")
                    .($SP         ? "AND\n  `signals`.`SP` = '$SP'\n" : "")
                    .($ITU        ? "AND\n  `signals`.`ITU` = '$ITU'\n" : "")
                    .($listenerID ? "AND\n  `logs`.`listenerID` = '$listenerID'\n" : "")
                    .($filter_type ? " AND\n $filter_type" : "");
                $result =    @\Rxx\Database::query($sql);
                for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                    $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
                    $xpos =     (int)($coords['ix2'] -  ((($coords['ix2'] - $coords['ix1'])/($coords['lon2'] - $coords['lon1'])) * ($coords['lon2'] - $row['lon'])));
                    $ypos =     (int)($coords['iy1'] + ((($coords['lat2'] - $row['lat']) / ($coords['lat2'] - $coords['lat1'])) * ($coords['iy2']-$coords['iy1'])));
                    $out[] =     "<area shape=\"poly\" alt=\"".(float)$row['khz']."-".$row['call'].' '.$row['QTH']."\n(lat:".$row['lat'].", lon:".$row['lon'].")\" "
                        ."onmouseover='show_map_info(\"".(float)$row['khz']."\",\"".$row['call']."\",\"".$row['type']."\",\"".str_replace("'", "&#39;", $row['QTH'])."\",\"".$row['lat']."\",\"".$row['lon']."\",\"".$row['active']."\"); return true;' "
                        ."onmouseout=\"show_map_info('','','','','',''); return true;\" "
                        ."onclick=\"signal_info(".$row['ID'].")\" coords=\"".($xpos-3).",".($ypos-2).",".($xpos).",".($ypos+4).",".($xpos+3).",".($ypos-2)."\">\n";
                }
                $out[] =    "</map>\n";
            } else {
                $out[] =    "<img galleryimg=\"no\" src=\"./?mode=state_map_gif&SP=$SP&ITU=$ITU&simple=$simple&listenerID=$listenerID&test=1\" border=\"1\" bordercolor=\"#000000\">\n";
            }
        }
        $out[] =    "</form>\n";
        return implode($out, "");
    }

    /**
     * @return string
     */
    public static function map_locator()
    {
        global $system, $map_x, $map_y, $name, $QTH, $lat, $lon;
        $map_x=($map_x=="" ? 1 : $map_x);
        $map_y=($map_y=="" ? 1 : $map_y);
        $out =
            "<script language='javascript' type='text/javascript'>\n"
            ."//<!--\n"
            ."current_xpos = $map_x;\n"
            ."current_ypos = $map_y;\n"
            ."if (isNS4) {\n"
            ."  document.write(\"<layer name='point_here'><img src='".\Rxx\Rxx::$base_path."assets/cursor_map.gif' onmousedown='(!isNS6 ? xy(window.event.x,window.event.y) : xy(event.pageX,event.pageY));'></layer>\");\n"
            ."}\n"
            ."else {\n"
            ."  document.write(\"<div ID='point_here' style='position: absolute; display: none;'><img src='".\Rxx\Rxx::$base_path."assets/cursor_map.gif' onmousedown='(!isNS6 ? xy(window.event.x,window.event.y) : xy(event.pageX,event.pageY));'></div>\");\n"
            ."}\n"
            ."function xy(xpos,ypos) {\n"
            ."  xpos = xpos + document.body.scrollLeft;\n"
            ."  ypos = ypos + document.body.scrollTop;\n"
            ."  current_xpos = xpos;\n"
            ."  current_ypos = ypos;\n"
            ."  document.form.map_x.value=xpos;\n"
            ."  document.form.map_y.value=ypos;\n"
            ."  show_point(xpos,ypos);\n"
            ."}\n"
            ."function show_point(x,y) {\n"
            ."  var ie_XOffset = -10;\n"
            ."  var ie_YOffset = -10;\n"
            ."  var ns_XOffset = -10;\n"
            ."  var ns_YOffset = -10;\n"
            ."  var div;\n"
            ."  if (isW3C) div =	document.getElementById('point_here');\n"
            ."  if (isIE4) div =	document.all['point_here'];\n"
            ."  if (isNS4) div =	document.layers['point_here'];\n"
            ."  if (isNS4) {\n"
            ."    div.moveTo(x+ns_XOffset,y+ns_YOffset);\n"
            ."    div.display = 'inline';\n"
            ."    return;\n"
            ."  }\n"
            ."  if (isIE5) {\n"
            ."    div.style.display = 'inline';\n"
            ."    div.style.left = x + ie_XOffset;\n"
            ."    div.style.top = y + ie_YOffset;\n"
            ."    return;\n"
            ."  }\n"
            ."  if (isNS6) {\n"
            ."    div.style.display = 'inline';\n"
            ."    div.style.left = x + ns_XOffset;\n"
            ."    div.style.top = y + ns_YOffset;\n"
            ."  }\n"
            ."}\n"
            ."function popup_map(call,lat,lon){\n"
            ."  popWin('http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude='+lat+'&longitude='+lon+'&size=big&zoom=2','popMap','scrollbars=1,resizable=1',740,600,'centre');\n"
            ."}\n"
            ."//-->\n"
            ."</script>\n"
            ."<table cellpadding='0' cellspacing='0' border='0'>\n"
            ."  <tr>\n"
            ."    <td>\n";
        switch ($system) {
            case "eu":
                $out.=  "<img src='./?mode=generate_map_eu' width='688' height='665' alt='' ";
                break;
            case "na":
                $out.=  "<img src='./?mode=generate_map_na' width='653' height='620' alt='' ";
                break;
        }
        $out.=
            "galleryimg='no' onmousedown='(!isNS6 ? xy(window.event.x,window.event.y) : xy(event.pageX,event.pageY));'\n"
            ."></td>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td align='center'><form name='form'><table cellpadding='5' cellspacing='0' border='0'>\n"
            ."      <tr>\n"
            ."        <td><b>".stripslashes($name)."</b> (".stripslashes($QTH).")</td>\n"
            ."        <td>Map X</td>\n"
            ."        <td><input type='text' value='' name='map_x' size='4'></td>\n"
            ."        <td>Map Y</td>\n"
            ."        <td><input type='text' value='' name='map_y' size='4'></td>\n"
            ."        <td><input type='button' value='GSQ Pos' onclick=\"popup_map('','$lat','$lon');\"></td>\n"
            ."        <td><input type='button' value='Reset' onclick=\"xy($map_x,$map_y);\"></td>\n"
            ."        <td><input type='button' value='Save' onclick=\"window.opener.form.map_x.value=document.form.map_x.value; window.opener.form.map_y.value=document.form.map_y.value; if (window.opener && window.opener.form && confirm('Save changes for this listener?')) { window.opener.form.save.onclick(); window.opener.form.submit(); window.close();}\"></td>\n"
            ."      </tr>\n"
            ."    </table></form></td>\n"
            ."  </tr>\n"
            ."</table>"
            ."<script language='javascript' type='text/javascript'>\n"
            ."xy(current_xpos,current_ypos);\n"
            ."</script>\n";
        return $out;
    }
}
