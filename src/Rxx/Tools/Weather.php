<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:56 PM
 */

namespace Rxx\Tools;

/**
 * Class Weather
 * @package Rxx\Tools
 */
class Weather
{
    /**
     * @return string
     */
    public static function weather()
    {
        global $script, $mode, $submode, $ICAO, $hours, $GSQ;
        ini_set("default_socket_timeout", 10);
        return "<form name='form' action='$script' method='POST'>\n"
            ."<input type='hidden' name='mode' value='$mode'>\n"
            ."<input type='hidden' name='submode' value=''>\n"
            ."</form>\n"
            ."<h2>Weather</h2>\n"
            ."<p align='center'><small>Quick Links [\n"
            ."<a href='#aurora_n'><b>Northern Aurora</b></a> |\n"
            ."<a href='#aurora_s'><b>Southern Aurora</b></a> |\n"
            .(system=="RNA" || system=="RWW" ?
                "<a href='#ltg_usa'><b>Lightning - USA</b></a> |\n"
                ."<a href='#ltg_can'><b>Lightning - Canada</b></a> |\n"
                ."<a href='#pres_na'><b>Pressure Map - North America</b></a> |\n"
             : ""
            )
            .(system=="REU" || system=="RWW" ?
                "<a href='#ltg_eu'><b>Lightning - Europe</b></a> |\n"
                ."<a href='#pres_eu'><b>Pressure Map - Europe</b></a> |\n" : ""
            )
            .(system=="RWW" ?
                "<a href='#pres_au'><b>Pressure Map - Australia</b></a> |\n" : ""
            )
            ."<a href='#pressure'><b>Pressure History</b></a>\n"
            ."]</small></p><br><br>\n"
            ."<p>See Also <a href='http://www.qsl.net/ve6wz/geomag.html' target='_blank'>http://www.qsl.net/ve6wz/geomag.html</a>.</p>\n"
            ."<div>"
            ."<div style='float: left; margin: 0 1em 0 0'>".Weather::weather_solar_map('n')."</div>"
            ."<div style='float: left'>".Weather::weather_solar_map('s')."</div>\n"
            ."</div><br style='clear: both;'><br><br><br>"
            .(system=="RNA" || system=="RWW" ?
                Weather::weather_lightning_na()."<br><br><br>\n".Weather::weather_lightning_canada()."<br><br><br>\n".Weather::weather_pressure_na()."<br><br><br>\n"
                : "")
            .(system=="REU" || system=="RWW" ?
                Weather::weather_lightning_europe()."<br><br><br>\n".Weather::weather_pressure_europe()."<br><br><br>\n"
                : "")
            .(system=="RWW" ?
                Weather::weather_pressure_au()."<br><br><br>\n" : "")
            .Weather::weather_metar();
    }

    /**
     * @return string
     */
    public static function weather_solar_map($pole)
    {
        global $mode;
        return
            "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'>"
            ."<a name='aurora_$pole'></a>".($pole==='n' ? 'Northern' : 'Southern')." Solar Activity Chart</th>\n"
            .($mode=="weather" ?
                "        <th align='right' class='downloadTableHeadings_nosort'>"
                ."<a href=\"javascript:popWin('".system_URL."/weather_solar_map_$pole','weather_solar_map_$pole','scrollbars=0,resizable=0',471,448,'centre')\">"
                ."<img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> "
                ."<a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n"
                : ""
            )
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>From "
            ."<a href='https://services.swpc.noaa.gov/images/animations/ovation-". ($pole==='n' ? 'north' : 'south') ."/latest.jpg' target='_blank'>"
            ."NOAA Auroral Activity</a> site.<br>\n"
            ."<a href='https://services.swpc.noaa.gov/images/animations/ovation-". ($pole==='n' ? 'north' : 'south') ."/latest.jpg' target='_blank'><img\n"
            ."src='https://services.swpc.noaa.gov/images/animations/ovation-". ($pole==='n' ? 'north' : 'south') ."/latest.jpg' height='400' width='450'></a></td>\n"
            ."  </tr>\n"
            ."</table>\n";
    }

    /**
     * @param $ICAO
     * @param $hours
     * @param $list
     * @return bool|string
     */
    public static function METAR($ICAO, $hours, $list)
    {
        $out =  array();
        if ($my_file = @implode(file("http://www.aviationweather.gov/adds/metars/index.php?station_ids=$ICAO&std_trans=standard&chk_metars=on&hoursStr=past+$hours+hours"), " ")) {
            $my_file =   explode("<", $my_file);
            for ($i=0; $i<count($my_file); $i++) {
                preg_match("/FONT FACE=\"Monospace,Courier\">([0-9a-zA-Z \r\n\t\f\/\-]+)/", $my_file[$i], $result);
                if ($result) {
                    $alt =  "";
                    $slp =  "";
                    $j =    1;
                    $row = $result[1];
                    $fields =   explode(" ", $row);
                    $date =         substr($fields[$j], 0, 2);
                    $time =         substr($fields[$j], 2, 4);
                    $j++;                               // Skip over station ID

                    while ($j<count($fields)) {
                        if (preg_match("/(SLP[0-9]+)/i", $fields[$j], $tmp)) {
                            $slp = substr($tmp[1], 3);
                        }
                        if (preg_match("/(Q[0-9]+)/i", $fields[$j], $tmp)) {
                            $alt = (float) substr($tmp[1], 1);
                        }
                        if (preg_match("/(A[0-9]+)/i", $fields[$j], $tmp)) {
                            $alt = floor(substr($tmp[1], 1) * 3.38674)/10;
                        }
                        $j++;
                    }
                    if ($alt) {
                        $out[] =    $date." ".$time." ".\Rxx\Rxx::pad($alt, 6).($slp ? " ".($alt>=1000 ? substr($alt, 0, 2) : substr($alt, 0, 1)).substr($slp, 0, 2).".".substr($slp, 2, 1) : "");
                        $alt =  0;
                        $slp =  0;
                    }
                    $result =   false;
                }
            }
        }
        if (!count($out)) {
            return false;
        }
        if (!$list) {
            return implode($out, "\n");
        }
        if ($list=='1') {
            return ("document.write(\"<li>".implode($out, "</li>\");\ndocument.write(\"<li>")."</li>\");\n");
        }
    }

     /**
     * @return string
     */
    public static function weather_lightning_canada()
    {
        global $mode,$script;
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='ltg_can'></a>Lightning - Environment Canada</th>\n"
        .($mode=="weather" ?
            "        <th align='right' class='downloadTableHeadings_nosort'><a href=\"javascript:popWin('$script?mode=weather_lightning_canada','weather_lightning_canada','scrollbars=0,resizable=0',656,608,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'>\n</th>" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>From <a href='http://weatheroffice.ec.gc.ca/lightning/index_e.html' target='_blank'><b>Environment Canada</b></a>,\n"
        ."this lightning flash product displays only a portion of the lightning activity in any<br>\n"
        ."given hour.<br>\n"
        ."It shows cloud to ground flashes observed during a 10 minute interval ending at the time shown on the image<br>\n"
        ."(upper right hand corner). Data are updated hourly, with a 2 hour delay.<br><br>\n"
        ."This map sometimes fails - compare it with the Vaisala lightning map if you ever doubt its validity.</p>\n"
        ."<img\n"
        ."src='http://weatheroffice.ec.gc.ca/data/lightning/canadian_ltng.png' width='640' height='480' alt='From Environment Canada'><br></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function weather_lightning_europe()
    {
        global $mode,$script;
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='ltg_eu'></a>Lightning - Europe</th>\n"
        .($mode=="weather" ?
            "        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<a href=\"javascript:popWin('$script?mode=weather_lightning_europe','weather_lightning_europe','scrollbars=0,resizable=0',795,704,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>From <a href='http://www.wetterzentrale.de/pics/Rsfloc.html' target='_blank'><b>http://www.wetterzentrale.de/</b></a><br>\n"
        ."<img\n"
        ."src='http://www.wetterzentrale.de/pics/Rsfloc.gif' width='779' height='660' alt='From www.wetterzentrale.de'><br></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function weather_lightning_na()
    {
        global $mode,$script;
        $vaisala = @file("http://www.classaxe.com/dx/ndb/vaisala.php");
        $out =  array();
        $out[] =     "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
            ."  <tr>\n"
            ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <th align='left' class='downloadTableHeadings_nosort'><a name='ltg_usa'></a>Lightning - Vaisala USA</th>\n"
            .($mode=="weather" ?
                "        <th align='right' class='downloadTableHeadings_nosort'>"
                ."<a href=\"javascript:popWin('$script?mode=weather_lightning_na','weather_lightning_na','scrollbars=0,resizable=0',530,483,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
            ."      </tr>\n"
            ."    </table></th>\n"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <td class='downloadTableContent'>\n";
        if (@$vaisala) {
            $out[] =     "This 30 minute-delayed USA lightning map is available at<br>\n"
                ."<a target='_blank' href='https://thunderstorm.vaisala.com/tux/jsp/explorer/explorer.jsp'><b>https://thunderstorm.vaisala.com/tux/jsp/explorer/explorer.jsp</b></a>.<br><br>\n"
                ."The background has been modified to show state names and bordering countries."
                ."    <table bgcolor='#424242'>\n"
                ."      <tr>\n"
                ."        <TD align='top'><TABLE WIDTH='508' height='350' BORDER='0' CELLSPACING='0' CELLPADDING='0'>\n"
                ."          <TR>\n"
                ."            <TD BACKGROUND='../../images/NationalMap.gif' valign='top' nowrap><a target='_blank'\n"
                ."href='https://thunderstorm.vaisala.com/tux/jsp/explorer/explorer.jsp'><IMG SRC='https://thunderstorm.vaisala.com/gpg/dynamic/lex1/images/overlays/Lex1NationalMap2hourOverlay".@$vaisala[0].".gif' alt='Vaisala Lightning Map (Background enhanced by M Francis)' border='0'></a>\n"
                ."           </TD>\n"
                ."           </TR>\n"
                ."        </TABLE></TD>\n"
                ."      </tr>\n"
                ."      <tr>\n"
                ."        <td><table border='1' cellpadding='2' cellspacing='0' bordercolor='#ffffff'>\n"
                ."          <tr>\n"
                ."            <th valign='top' align='left'><font color='#ffffff'>Times</font></th><td><font color='#ffffff'><small>".@$vaisala[2]." to ".@$vaisala[3]."</small></font></TD>\n"
                ."          </tr>\n"
                ."          <tr>\n"
                ."            <th valign='top' align='left'><font color='#ffffff'>Total Strikes</font></th><td><font color='#ffffff'><small>".@$vaisala[1]."</small></font></TD>\n"
                ."          </tr>\n"
                ."        </table></td>\n"
                ."      </tr>\n"
                ."    </table>\n";
        } else {
            $out[] =     "<h3><font color='#ff0000'>This service is temporarily unavailable<br>Please contact <script language='javascript' type='text/javascript'>document.write(\"<a title='Contact the Developer' href='mail\"+\"to\"+\":martin\"+\"@\"+\"classaxe\"+\".\"+\"com?subject=".system."%20Problem'>Martin Francis\"+\"</a>\")</script> if this symptom persists</font></h3>";
        }
        $out[] =     "    </td>\n"
            ."  </tr>\n"
            ."</table>\n";
        return implode($out, "");
    }

    /**
     * @return string
     */
    public static function weather_metar()
    {
        global $mode,$script,$hours,$ICAO;
        $ICAO =     strtoUpper($ICAO);
        if ($hours=="") {
            $hours = "24";
        }
        $pressure =     array();
        if ($ICAO && $hours) {
            $pressure[] =   "QNH at $ICAO:\n---------------------\nDD UTC  MB     SLP\n---------------------";
            $pressure[] =   \Rxx\Tools\Weather::METAR($ICAO, $hours, 0);
            $pressure[] =   "---------------------\n(From ".system.")\n";
        } else {
            $pressure[] = "HELP\nEnter the ICAO code for any METAR compatible weather station.\n\nClick 'ICAO ID' to find your local station\n\nYour preferences will be saved.";
        }
        return "<form name='pressure' action='$script' method='GET' onsubmit='set_ICAO_cookies(document.pressure)'>\n"
        ."<input type='hidden' name='mode' value='$mode'>\n"
        ."<input type='hidden' name='submode' value=''>\n"
        ."<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='pressure'></a>Pressure History</th>\n"
        .($mode=="weather" ?
            "        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<a href=\"javascript:popWin('$script?mode=weather_metar','weather_metar','scrollbars=0,resizable=0',386,209,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'><a href='javascript:find_ICAO()'><b>ICAO ID</b></a> <input type='text' size='4' maxlength='4' name='ICAO' value='$ICAO' class='formField'>\n"
        ."<input type='text' size='2' maxlength='2' name='hours' value='$hours' class='formField'> hours\n"
        ."<input type='submit' value='QNH' class='formButton'>\n"
        ."<input type='button' value='METAR' class='formButton' onclick='popWin(\"http://adds.aviationweather.noaa.gov/metars/index.php?station_ids=\"+document.pressure.ICAO.value+\"&std_trans=&chk_metars=on&hoursStr=past+\"+document.pressure.hours.value+\"+hours\",\"popMETAR\",\"scrollbars=1,resizable=1,location=1\",640,380,\"\");'>\n"
        ."<input type='button' value='Decoded' class='formButton' onclick='popWin(\"http://adds.aviationweather.noaa.gov/metars/index.php?station_ids=\"+document.pressure.ICAO.value+\"&std_trans=translated&chk_metars=on&hoursStr=past+\"+document.pressure.hours.value+\"+hours\",\"popDecoded\",\"scrollbars=1,resizable=1,location=1\",640,380,\"\");'><br>\n"
        ."<textarea rows='10' cols='50' class='formFixed'>".implode($pressure, "\n")."</textarea><br><br>\n"
        .($mode=='weather' ?
            "<b>Add live QNH data to your web page</b><br>\n"
            ."<a href='javascript:toggleDivDisplaySimple(\"how_to\")'><b>Read how</b></a><br><br>\n"
            ."<div id='how_to' class='no_screen' style='display: none'>For those who have their own web sites, you can automatically add a pressure history to it using the following code pasted into your HTML document.</p>\n"
            ."<p>Replace both instances of <b>CYYZ</b> with your own local weather station code and replace <b>36</b> with the number of hours you wish to include reports for (up to a maximum of 99).</p>\n"
            ."<p>Please <script language='javascript' type='text/javascript'>document.write(\"<a title='Contact the Developer' href='mail\"+\"to\"+\":martin\"+\"@\"+\"classaxe\"+\".\"+\"com?subject=".system."%20System%20Automated%20Weather'><b>let me know</b>\"+\"</a>\")</script> if you decide to use this method so I can ensure I don't cause problems for you if I change anything!</p>\n"
            ."<textarea rows='8' cols='60' class='formField'><pre>Pressure Readings at Station CYYZ\r<ul>DD UTC  MB     SLP\r<script language='javascript' type='text/javascript'\rsrc='".system_URL."/?mode=metar&ICAO=CYYZ&hours=36&list=1'></script>\r</ul>\r</pre></textarea><br>\n"
            ."</div>" : "")
        ."</td>\n"
        ."  </tr>\n"
        ."</table></form>\n";
    }

    /**
     * @return string
     */
    public static function weather_pressure_au()
    {
        global $mode,$script;
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='pres_au'></a>Pressure Map - Australia</th>\n"
        .($mode=="weather" ?
            "        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<a href=\"javascript:popWin('$script?mode=weather_pressure_au','weather_pressure_au','scrollbars=0,resizable=0',656,478,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>From <a href='http://www.bom.gov.au/' target='_blank'><b>http://www.bom.gov.au/</b></a><br>\n"
        ."<img\n"
        ."src='http://www.bom.gov.au/fwo/IDY00050.gif' width='641' height='434' alt='From www.bom.gov.au'><br></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function weather_pressure_europe()
    {
        global $mode,$script;
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='pres_eu'></a>Pressure Map - Europe</th>\n"
        .($mode=="weather" ?
            "        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<a href=\"javascript:popWin('$script?mode=weather_pressure_europe','weather_pressure_europe','scrollbars=0,resizable=0',616,438,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>From <a href='http://www.metoffice.com/weather/charts/index.html' target='_blank'><b>http://www.metoffice.com/weather/charts/index.html</b></a><br>\n"
        ."<img\n"
        ."src='http://www.metoffice.com/weather/charts/FSXX00T_00.jpg' width='600' height='397' alt='From www.metoffice.com'><br></td>\n"
        ."  </tr>\n"
        ."</table>\n";
    }

    /**
     * @return string
     */
    public static function weather_pressure_na()
    {
        global $mode,$script;
        if ($my_file = @file("http://www.atmos.uiuc.edu/weather/tree/viewer.pl?current/sfcslp/N")) {
            $my_file =  implode($my_file, "\n");
            preg_match("/<IMG src=\"([^\"]+)/i", $my_file, $result);


        }
        return "<table cellpadding='2' border='0' cellspacing='1' class='downloadtable'>\n"
        ."  <tr>\n"
        ."    <th class='downloadTableHeadings_nosort'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <th align='left' class='downloadTableHeadings_nosort'><a name='pres_na'></a>Pressure Map - North America</th>\n"
        .($mode=="weather" ?
            "        <th align='right' class='downloadTableHeadings_nosort'>"
            ."<a href=\"javascript:popWin('$script?mode=weather_pressure_na','weather_pressure_na','scrollbars=0,resizable=0',620,523,'centre')\"><img src='".BASE_PATH."assets/icon-popup.gif' border='0'></a> <a href='#top' class='yellow'><img src='".BASE_PATH."assets/icon-top.gif' border='0'></th>\n" : "")
        ."      </tr>\n"
        ."    </table></th>\n"
        ."  </tr>\n"
        ."  <tr>\n"
        ."    <td class='downloadTableContent'>\n"
        .(@$result ?     "Latest barometric Pressure map from the <a href='http://ww2010.atmos.uiuc.edu/(Gh)/wx/surface.rxml' target='_blank'>"
            ."University of Illinois WW2010 Project</a></h2><a href='http://ww2010.atmos.uiuc.edu/(Gh)/wx/surface.rxml' target='_blank'><br><br>"
            ."<img src=\"".$result[1]."\"></a></td>\n":
            "<h3><font color='#ff0000'>http://www.atmos.uiuc.edu -<br>\n"
            ."This service is temporarily unavailable<br>Please contact "
            ."<script language='javascript' type='text/javascript'>document.write(\"<a title='Contact the Developer' "
            ."href='mail\"+\"to\"+\":martin\"+\"@\"+\"classaxe\"+\".\"+\"com?subject=".system."%20Problem'>Martin Francis\"+\"</a>\")</script> "
            ."if this symptom persists</font></h3></td>")
        ."  </tr>\n"
        ."</table>\n";
    }
}
