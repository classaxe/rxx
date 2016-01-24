<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:57 PM
 */

namespace Rxx\Tools;


/**
 * Class Cle
 * @package Rxx\Tools
 */
class Cle {
    /**
     * @param $var
     * @return string
     */
    function cle_set($var){
        return addslashes(htmlentities(isset($_POST[$var]) ? $_POST[$var] : ''));
    }

    /**
     * @param $prefix
     * @return string
     */
    function cle_set_type($prefix){
        $vars = array();
        if (isset($_POST[$prefix."_DGPS"]))  { $vars[] = "type_DGPS=1"; }
        if (isset($_POST[$prefix."_HAMBCN"])){ $vars[] = "type_HAMBCN=1"; }
        if (isset($_POST[$prefix."_NAVTEX"])){ $vars[] = "type_NAVTEX=1"; }
        if (isset($_POST[$prefix."_NDB"]))   { $vars[] = "type_NDB=1"; }
        if (isset($_POST[$prefix."_TIME"]))  { $vars[] = "type_TIME=1"; }
        if (isset($_POST[$prefix."_OTHER"])) { $vars[] = "type_OTHER=1"; }
        return implode('&amp;',$vars);
    }

    /**
     * @return string
     */
    function cle() {
        global $ID, $mode, $submode, $script, $sortBy, $target;
        $special = false;

        if (isAdmin() && $submode=='save'){
            $sql =
                "UPDATE\n"
                ."  `cle`\n"
                ."SET\n"
                ."  `cle` = '".cle_set('cle')."',\n"
                ."  `date_start` = '".cle_set('date_start')."',\n"
                ."  `date_end` = '".cle_set('date_end')."',\n"
                ."  `date_timespan` = '".cle_set('date_timespan')."',\n"
                ."  `scope` = '".cle_set('scope')."',\n"
                ."  `additional` = '".cle_set('additional')."',\n"
                ."  `world_range_1_low` = '".cle_set('world_range_1_low')."',\n"
                ."  `world_range_1_high` = '".cle_set('world_range_1_high')."',\n"
                ."  `world_range_1_channels` = '".cle_set('world_range_1_channels')."',\n"
                ."  `world_range_1_type` = '".cle_set_type('world_range_1_type')."',\n"
                ."  `world_range_1_itu` = '".cle_set('world_range_1_itu')."',\n"
                ."  `world_range_1_sp` = '".cle_set('world_range_1_sp')."',\n"
                ."  `world_range_1_filter_other` = '".cle_set('world_range_1_filter_other')."',\n"
                ."  `world_range_1_text_extra` = '".cle_set('world_range_1_text_extra')."',\n"
                ."  `world_range_2_low` = '".cle_set('world_range_2_low')."',\n"
                ."  `world_range_2_high` = '".cle_set('world_range_2_high')."',\n"
                ."  `world_range_2_channels` = '".cle_set('world_range_2_channels')."',\n"
                ."  `world_range_2_type` = '".cle_set_type('world_range_2_type')."',\n"
                ."  `world_range_2_itu` = '".cle_set('world_range_2_itu')."',\n"
                ."  `world_range_2_sp` = '".cle_set('world_range_2_sp')."',\n"
                ."  `world_range_2_filter_other` = '".cle_set('world_range_2_filter_other')."',\n"
                ."  `world_range_2_text_extra` = '".cle_set('world_range_2_text_extra')."',\n"
                ."  `europe_range_1_low` = '".cle_set('europe_range_1_low')."',\n"
                ."  `europe_range_1_high` = '".cle_set('europe_range_1_high')."',\n"
                ."  `europe_range_1_channels` = '".cle_set('europe_range_1_channels')."',\n"
                ."  `europe_range_1_type` = '".cle_set_type('europe_range_1_type')."',\n"
                ."  `europe_range_1_itu` = '".cle_set('europe_range_1_itu')."',\n"
                ."  `europe_range_1_sp` = '".cle_set('europe_range_1_sp')."',\n"
                ."  `europe_range_1_filter_other` = '".cle_set('europe_range_1_filter_other')."',\n"
                ."  `europe_range_1_text_extra` = '".cle_set('europe_range_1_text_extra')."',\n"
                ."  `europe_range_2_low` = '".cle_set('europe_range_2_low')."',\n"
                ."  `europe_range_2_high` = '".cle_set('europe_range_2_high')."',\n"
                ."  `europe_range_2_channels` = '".cle_set('europe_range_2_channels')."',\n"
                ."  `europe_range_2_type` = '".cle_set_type('europe_range_2_type')."',\n"
                ."  `europe_range_2_itu` = '".cle_set('europe_range_2_itu')."',\n"
                ."  `europe_range_2_sp` = '".cle_set('europe_range_2_sp')."',\n"
                ."  `europe_range_2_filter_other` = '".cle_set('europe_range_2_filter_other')."',\n"
                ."  `europe_range_2_text_extra` = '".cle_set('europe_range_2_text_extra')."'\n";
            mysql_query($sql);
        }
        $sql =        "SELECT * FROM `cle`";
        $result =     mysql_query($sql);
        $record =     mysql_fetch_array($result);
        $cle =                $record['cle'];
        $date_start =         $record['date_start'];
        $date_end =           $record['date_end'];
        $date_timespan =      $record['date_timespan'];
        $scope =              $record['scope'];
        $additional =         $record['additional'];

        $a_text_extra =       $record['world_range_1_text_extra'];
        $a_khz_l =            $record['world_range_1_low'];
        $a_khz_h =            $record['world_range_1_high'];
        $a_channels =         $record['world_range_1_channels'];
        $a_type =             $record['world_range_1_type'];
        $a_itu =              $record['world_range_1_itu'];
        $a_sp =               $record['world_range_1_sp'];
        $a_filter_other =     $record['world_range_1_filter_other'];
        $a_url =              "filter_khz_1=".$a_khz_l."&amp;filter_khz_2=".$a_khz_h."&amp;".$a_type.($a_channels ? "&amp;filter_channels=".$a_channels : "").($a_itu ? "&amp;filter_itu=".str_replace(' ','%20',$a_itu) : "").($a_sp ? "&amp;filter_sp=".str_replace(' ','%20',$a_sp) : "").($a_filter_other ? "&amp;".$a_filter_other : "");
        $a_text =             "<b>".$a_khz_l."kHz to ".$a_khz_h."kHz ".$a_text_extra."</b>";

        $b_text_extra =       $record['world_range_2_text_extra'];
        $b_khz_l =            $record['world_range_2_low'];
        $b_khz_h =            $record['world_range_2_high'];
        $b_channels =         $record['world_range_2_channels'];
        $b_type =             $record['world_range_2_type'];
        $b_itu =              $record['world_range_2_itu'];
        $b_sp =               $record['world_range_2_sp'];
        $b_filter_other =     $record['world_range_2_filter_other'];
        $b_url =              "filter_khz_1=".$b_khz_l."&amp;filter_khz_2=".$b_khz_h."&amp;".$b_type.($b_channels ? "&amp;filter_channels=".$b_channels : "").($b_itu ? "&amp;filter_itu=".str_replace(' ','%20',$b_itu) : "").($b_sp ? "&amp;filter_sp=".str_replace(' ','%20',$b_sp) : "").($b_filter_other ? "&amp;".$b_filter_other : "");
        $b_text =             "<b>".$b_khz_l."kHz to ".$b_khz_h."kHz ".$b_text_extra."</b>";

        $eu_a_text_extra =    ($record['europe_range_1_text_extra'] ?     $record['europe_range_1_text_extra'] :      $record['world_range_1_text_extra']);
        $eu_a_khz_l =         ($record['europe_range_1_low'] ?            $record['europe_range_1_low'] :             $record['world_range_1_low']);
        $eu_a_khz_h =         ($record['europe_range_1_high'] ?           $record['europe_range_1_high'] :            $record['world_range_1_high']);
        $eu_a_channels =      ($record['europe_range_1_channels'] ?       $record['europe_range_1_channels'] :        $record['world_range_1_channels']);
        $eu_a_type =          ($record['europe_range_1_type'] ?           $record['europe_range_1_type'] :            $record['world_range_1_type']);
        $eu_a_itu =           ($record['europe_range_1_itu'] ?            $record['europe_range_1_itu'] :             $record['world_range_1_itu']);
        $eu_a_sp =            ($record['europe_range_1_sp'] ?             $record['europe_range_1_sp'] :              $record['world_range_1_sp']);
        $eu_a_filter_other =  ($record['europe_range_1_filter_other'] ?   $record['europe_range_1_filter_other'] :    $record['world_range_1_filter_other']);
        $eu_a_url =           "filter_khz_1=".$eu_a_khz_l."&amp;filter_khz_2=".$eu_a_khz_h."&amp;".$eu_a_type.($eu_a_channels ? "&amp;filter_channels=".$eu_a_channels : "").($eu_a_itu ? "&amp;filter_itu=".str_replace(' ','%20',$eu_a_itu) : "").($eu_a_sp ? "&amp;filter_sp=".str_replace(' ','%20',$eu_a_sp) : "").($eu_a_filter_other ? "&amp;".$eu_a_filter_other : "");
        $eu_a_text =          "<b>".$eu_a_khz_l."kHz to ".$eu_a_khz_h."kHz ".$eu_a_text_extra."</b>";

        $eu_b_text_extra =    ($record['europe_range_2_text_extra'] ?     $record['europe_range_2_text_extra'] :      $record['world_range_2_text_extra']);
        $eu_b_khz_l =         ($record['europe_range_2_low'] ?            $record['europe_range_2_low'] :             $record['world_range_2_low']);
        $eu_b_khz_h =         ($record['europe_range_2_high'] ?           $record['europe_range_2_high'] :            $record['world_range_2_high']);
        $eu_b_channels =      ($record['europe_range_2_channels'] ?       $record['europe_range_2_channels'] :        $record['world_range_2_channels']);
        $eu_b_type =          ($record['europe_range_2_type'] ?           $record['europe_range_2_type'] :            $record['world_range_2_type']);
        $eu_b_itu =           ($record['europe_range_2_itu'] ?            $record['europe_range_2_itu'] :             $record['world_range_2_itu']);
        $eu_b_sp =            ($record['europe_range_2_sp'] ?             $record['europe_range_2_sp'] :              $record['world_range_2_sp']);
        $eu_b_filter_other =  ($record['europe_range_2_filter_other'] ?   $record['europe_range_2_filter_other'] :    $record['world_range_2_filter_other']);
        $eu_b_url =           "filter_khz_1=".$eu_b_khz_l."&amp;filter_khz_2=".$eu_b_khz_h."&amp;".$eu_b_type.($eu_b_channels ? "&amp;filter_channels=".$eu_b_channels : "").($eu_b_itu ? "&amp;filter_itu=".str_replace(' ','%20',$eu_b_itu) : "").($eu_b_sp ? "&amp;filter_sp=".str_replace(' ','%20',$eu_b_sp) : "").($eu_b_filter_other ? "&amp;".$eu_b_filter_other : "");
        $eu_b_text =          "<b>".$eu_b_khz_l."kHz to ".$eu_b_khz_h."kHz ".$eu_b_text_extra."</b>";

        $out =
            "<h2>CLE ".$record['cle']." Seek Lists and Signal Lists "
            .(isAdmin() ?
                "<span style='font-size: 80%'>\n"
                ."<a href=\"#\" id=\"show_editor\" onclick=\"window.focus();document.getElementById('cle_editor').style.display='';document.getElementById('show_editor').style.display='none';document.getElementById('hide_editor').style.display='';return false;\">[Show Editor]</a>"
                ."<a href=\"#\" id=\"hide_editor\" style='display: none;' onclick=\"window.focus();document.getElementById('cle_editor').style.display='none';document.getElementById('show_editor').style.display='';document.getElementById('hide_editor').style.display='none';return false;\">[Hide Editor]</a>"
                ."</span>\n"
                :  ""
            )
            ."</h2>\n";
        if (isAdmin()) {
            $out.=
                "<script type=\"text/javascript\" src=\"".BASE_PATH."assets/calendar_db.js\"></script>\n"
                ."<link rel=\"stylesheet\" href=\"".BASE_PATH."assets/calendar.css\">\n"
                ."<div id='cle_editor' style='display:none;text-align:center;border:2px solid #888;background-color:#f0f0ff'>\n"
                ."<form action='".system_URL."/".$mode."' name='form' method='POST'>\n"
                ."<input type='hidden' name='mode' value='$mode'>\n"
                ."<input type='hidden' name='submode' value='save'>\n"
                ."<h3>Event Overview</h3>\n"
                ."<table cellpadding='2' cellspacing='0' border='1' style='border:1px solid #888;border-collapse:collapse;background-color:#f8f8f8' >\n"
                ."  <tr>\n"
                ."    <th style='width: 80px'>CLE#</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='cle' id='cle' value=\"".$cle."\" style='text-align:right;width:40px;' />\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Start Date</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='date_start' id='date_start' value=\"".$date_start."\" style='width:80px;' />\n"
                ."<script type='text/javascript'>\n"
                ."  var o_cal = new tcal (\n"
                ."  {\n"
                ."    'formname':'form', 'controlname':'date_start'\n"
                ."  });\n"
                ."  o_cal.a_tpl.yearscroll = false;\n"
                ."  o_cal.a_tpl.weekstart = 0;\n"
                ."</script>\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>End Date</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='date_end' id='date_end' value=\"".$date_end."\" style='width:80px;' />\n"
                ."<script type='text/javascript'>\n"
                ."  var o_cal = new tcal (\n"
                ."  {\n"
                ."    'formname':'form', 'controlname':'date_end'\n"
                ."  });\n"
                ."  o_cal.a_tpl.yearscroll = false;\n"
                ."  o_cal.a_tpl.weekstart = 0;\n"
                ."</script>\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Time span</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='date_timespan' id='date_timespan' value=\"".$date_timespan."\" style='width:880px;' />\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Scope</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='scope' id='scope' value=\"".$scope."\" style='width:880px;' />\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Additional</th>\n"
                ."    <td>\n"
                ."      <textarea name='additional' id='additional' style='width:880px;height:60px'>".$additional."</textarea>\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."</table>"
                ."<h3>Regional Settings</h3>\n"
                ."<table cellpadding='2' cellspacing='0' border='1' style='border:1px solid #888;border-collapse:collapse;background-color:#f8f8f8' >\n"
                ."  <tr>\n"
                ."    <th style='width: 80px'>&nbsp;</th>\n"
                ."    <th>World Range 1</th>\n"
                ."    <th>Europe Range 1</th>\n"
                ."    <th>World Range 2</th>\n"
                ."    <th>Europe Range 2</th>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Low</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_1_low' id='world_range_1_low' value=\"".$a_khz_l."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_1_low' id='europe_range_1_low' value=\"".$eu_a_khz_l."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_2_low' id='world_range_2_low' value=\"".$b_khz_l."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_2_low' id='europe_range_2_low' value=\"".$eu_b_khz_l."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>High</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_1_high' id='world_range_1_high' value=\"".$a_khz_h."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_1_high' id='europe_range_1_high' value=\"".$eu_a_khz_h."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_2_high' id='world_range_2_high' value=\"".$b_khz_h."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_2_high' id='europe_range_2_high' value=\"".$eu_b_khz_h."\" style='text-align:right;width:40px;' /> KHz\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Channels</th>\n"
                ."    <td>\n"
                ."      <select name='world_range_1_channels' class='formField'>\n"
                ."        <option value=''". ($a_channels=='' ?  " selected='selected'" : "").">All</option>\n"
                ."        <option value='1'".($a_channels=='1' ? " selected='selected'" : "").">Only 1 KHz</option>\n"
                ."        <option value='2'".($a_channels=='2' ? " selected='selected'" : "").">Not 1 KHz</option>\n"
                ."      </select>"
                ."    </td>\n"
                ."    <td>\n"
                ."      <select name='europe_range_1_channels' class='formField'>\n"
                ."        <option value=''". ($eu_a_channels=='' ?  " selected='selected'" : "").">All</option>\n"
                ."        <option value='1'".($eu_a_channels=='1' ? " selected='selected'" : "").">Only 1 KHz</option>\n"
                ."        <option value='2'".($eu_a_channels=='2' ? " selected='selected'" : "").">Not 1 KHz</option>\n"
                ."      </select>"
                ."    </td>\n"
                ."    <td>\n"
                ."      <select name='world_range_2_channels' class='formField'>\n"
                ."        <option value=''". ($b_channels=='' ?  " selected='selected'" : "").">All</option>\n"
                ."        <option value='1'".($b_channels=='1' ? " selected='selected'" : "").">Only 1 KHz</option>\n"
                ."        <option value='2'".($b_channels=='2' ? " selected='selected'" : "").">Not 1 KHz</option>\n"
                ."      </select>"
                ."    </td>\n"
                ."    <td>\n"
                ."      <select name='europe_range_2_channels' class='formField'>\n"
                ."        <option value=''". ($eu_b_channels=='' ?  " selected='selected'" : "").">All</option>\n"
                ."        <option value='1'".($eu_b_channels=='1' ? " selected='selected'" : "").">Only 1 KHz</option>\n"
                ."        <option value='2'".($eu_b_channels=='2' ? " selected='selected'" : "").">Not 1 KHz</option>\n"
                ."      </select>"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Types</th>\n"
                ."    <td>\n"
                ."      <label style='width:75px;background:#00D8FF;'><input type='checkbox' name='world_range_1_type_DGPS'    id='world_range_1_type_DGPS'         ".(strpos($a_type,'type_DGPS=1')===false ? '' : " checked='checked'")." />DGPS</label><br />\n"
                ."      <label style='width:75px;background:#B8FFC0;'><input type='checkbox' name='world_range_1_type_HAMBCN'  id='world_range_1_type_type_HAMBCN'  ".(strpos($a_type,'type_HAMBCN=1')===false ? '' : " checked='checked'")." />HAMBCN</label><br />\n"
                ."      <label style='width:75px;background:#FFB8D8;'><input type='checkbox' name='world_range_1_type_NAVTEX'  id='world_range_1_type_type_NAVTEX'  ".(strpos($a_type,'type_NAVTEX=1')===false ? '' : " checked='checked'")." />NAVTEX</label><br />\n"
                ."      <label style='width:75px;background:#FFFFFF;'><input type='checkbox' name='world_range_1_type_NDB'     id='world_range_1_type_type_NDB'     ".(strpos($a_type,'type_NDB=1')===false ? '' : " checked='checked'")." />NDB</label><br />\n"
                ."      <label style='width:75px;background:#FFE0B0;'><input type='checkbox' name='world_range_1_type_TIME'    id='world_range_1_type_type_TIME'    ".(strpos($a_type,'type_TIME=1')===false ? '' : " checked='checked'")." />TIME</label><br />\n"
                ."      <label style='width:75px;background:#B8F8FF;'><input type='checkbox' name='world_range_1_type_OTHER'   id='world_range_1_type_type_OTHER'   ".(strpos($a_type,'type_OTHER=1')===false ? '' : " checked='checked'")." />OTHER</label>\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <label style='width:75px;background:#00D8FF;'><input type='checkbox' name='europe_range_1_type_DGPS'   id='europe_range_1_type_DGPS'        ".(strpos($eu_a_type,'type_DGPS=1')===false ? '' : " checked='checked'")." />DGPS</label><br />\n"
                ."      <label style='width:75px;background:#B8FFC0;'><input type='checkbox' name='europe_range_1_type_HAMBCN' id='europe_range_1_type_type_HAMBCN' ".(strpos($eu_a_type,'type_HAMBCN=1')===false ? '' : " checked='checked'")." />HAMBCN</label><br />\n"
                ."      <label style='width:75px;background:#FFB8D8;'><input type='checkbox' name='europe_range_1_type_NAVTEX' id='europe_range_1_type_type_NAVTEX' ".(strpos($eu_a_type,'type_NAVTEX=1')===false ? '' : " checked='checked'")." />NAVTEX</label><br />\n"
                ."      <label style='width:75px;background:#FFFFFF;'><input type='checkbox' name='europe_range_1_type_NDB'    id='europe_range_1_type_type_NDB'    ".(strpos($eu_a_type,'type_NDB=1')===false ? '' : " checked='checked'")." />NDB</label><br />\n"
                ."      <label style='width:75px;background:#FFE0B0;'><input type='checkbox' name='europe_range_1_type_TIME'   id='europe_range_1_type_type_TIME'   ".(strpos($eu_a_type,'type_TIME=1')===false ? '' : " checked='checked'")." />TIME</label><br />\n"
                ."      <label style='width:75px;background:#B8F8FF;'><input type='checkbox' name='europe_range_1_type_OTHER'  id='europe_range_1_type_type_OTHER'  ".(strpos($eu_a_type,'type_OTHER=1')===false ? '' : " checked='checked'")." />OTHER</label>\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <label style='width:75px;background:#00D8FF;'><input type='checkbox' name='world_range_2_type_DGPS'    id='world_range_2_type_DGPS'         ".(strpos($b_type,'type_DGPS=1')===false ? '' : " checked='checked'")." />DGPS</label><br />\n"
                ."      <label style='width:75px;background:#B8FFC0;'><input type='checkbox' name='world_range_2_type_HAMBCN'  id='world_range_2_type_type_HAMBCN'  ".(strpos($b_type,'type_HAMBCN=1')===false ? '' : " checked='checked'")." />HAMBCN</label><br />\n"
                ."      <label style='width:75px;background:#FFB8D8;'><input type='checkbox' name='world_range_2_type_NAVTEX'  id='world_range_2_type_type_NAVTEX'  ".(strpos($b_type,'type_NAVTEX=1')===false ? '' : " checked='checked'")." />NAVTEX</label><br />\n"
                ."      <label style='width:75px;background:#FFFFFF;'><input type='checkbox' name='world_range_2_type_NDB'     id='world_range_2_type_type_NDB'     ".(strpos($b_type,'type_NDB=1')===false ? '' : " checked='checked'")." />NDB</label><br />\n"
                ."      <label style='width:75px;background:#FFE0B0;'><input type='checkbox' name='world_range_2_type_TIME'    id='world_range_2_type_type_TIME'    ".(strpos($b_type,'type_TIME=1')===false ? '' : " checked='checked'")." />TIME</label><br />\n"
                ."      <label style='width:75px;background:#B8F8FF;'><input type='checkbox' name='world_range_2_type_OTHER'   id='world_range_2_type_type_OTHER'   ".(strpos($b_type,'type_OTHER=1')===false ? '' : " checked='checked'")." />OTHER</label>\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <label style='width:75px;background:#00D8FF;'><input type='checkbox' name='europe_range_2_type_DGPS'   id='europe_range_2_type_DGPS'        ".(strpos($eu_b_type,'type_DGPS=1')===false ? '' : " checked='checked'")." />DGPS</label><br />\n"
                ."      <label style='width:75px;background:#B8FFC0;'><input type='checkbox' name='europe_range_2_type_HAMBCN' id='europe_range_2_type_type_HAMBCN' ".(strpos($eu_b_type,'type_HAMBCN=1')===false ? '' : " checked='checked'")." />HAMBCN</label><br />\n"
                ."      <label style='width:75px;background:#FFB8D8;'><input type='checkbox' name='europe_range_2_type_NAVTEX' id='europe_range_2_type_type_NAVTEX' ".(strpos($eu_b_type,'type_NAVTEX=1')===false ? '' : " checked='checked'")." />NAVTEX</label><br />\n"
                ."      <label style='width:75px;background:#FFFFFF;'><input type='checkbox' name='europe_range_2_type_NDB'    id='europe_range_2_type_type_NDB'    ".(strpos($eu_b_type,'type_NDB=1')===false ? '' : " checked='checked'")." />NDB</label><br />\n"
                ."      <label style='width:75px;background:#FFE0B0;'><input type='checkbox' name='europe_range_2_type_TIME'   id='europe_range_2_type_type_TIME'   ".(strpos($eu_b_type,'type_TIME=1')===false ? '' : " checked='checked'")." />TIME</label><br />\n"
                ."      <label style='width:75px;background:#B8F8FF;'><input type='checkbox' name='europe_range_2_type_OTHER'  id='europe_range_2_type_type_OTHER'  ".(strpos($eu_b_type,'type_OTHER=1')===false ? '' : " checked='checked'")." />OTHER</label>\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>ITU Codes</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_1_itu' id='world_range_1_itu' value=\"".$a_itu."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_1_itu' id='europe_range_1_itu' value=\"".$eu_a_itu."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_2_itu' id='world_range_2_itu' value=\"".$b_itu."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_2_itu' id='europe_range_2_itu' value=\"".$eu_b_itu."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>SP Codes</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_1_sp' id='world_range_1_sp' value=\"".$a_sp."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_1_sp' id='europe_range_1_sp' value=\"".$eu_a_sp."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_2_sp' id='world_range_2_sp' value=\"".$b_sp."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_2_sp' id='europe_range_2_sp' value=\"".$eu_b_sp."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Extra Filter</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_1_filter_other' id='world_range_1_filter_other' value=\"".$a_filter_other."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_1_filter_other' id='europe_range_1_filter_other' value=\"".$eu_a_filter_other."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_2_filter_other' id='world_range_2_filter_other' value=\"".$b_filter_other."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_2_other' id='europe_range_2_other' value=\"".$eu_b_filter_other."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <th>Text Suffix</th>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_1_text_extra' id='world_range_1_text_extra' value=\"".$a_text_extra."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_1_text_extra' id='europe_range_1_text_extra' value=\"".$eu_a_text_extra."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='world_range_2_text_extra' id='world_range_2_text_extra' value=\"".$b_text_extra."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."    <td>\n"
                ."      <input type='text' name='europe_range_2_text_extra' id='europe_range_2_text_extra' value=\"".$eu_b_text_extra."\" style='text-align:left;width:220px;' />\n"
                ."    </td>\n"
                ."  </tr>\n"
                ."</table>"
                ."<p><input type='submit' value='Save Settings' /></p>\n"
                ."</form>\n"
                ."</div>";
        }
        $out.=
            "<p><b>Date".($record['date_end']!='0000-00-00' ? 's' : '').":</b><br />".date('l j\<\s\u\p\>S\<\/\s\u\p\> F Y',strtotime($record['date_start']))
            .($record['date_end']!='0000-00-00' ? " to ".date('l j\<\s\u\p\>S\<\/\s\u\p\> F Y',strtotime($record['date_end'])) : "")
            .($record['date_timespan'] ? " ".$record['date_timespan'] : "").".</p>\n"
            ."<p><b>Scope:</b><br />".$record['scope']."</p>\n"
            .($record['additional'] ? "<p><b>Additional Info:</b><br />".$record['additional']."</p>\n" : "");
        if ($special){
            $out.=
                "<ul>\n"
                ."  <li><b>Logs recorded at University of Twente Web SDR, Enschede</b><br>"
                ."  <table cellpadding='0' cellspacing='0' border='0'>\n"
                ."    <tr>\n"
                ."      <td width='140' valign='top'>Detailed Signal List</td>\n"
                ."      <td><a href='../reu/signal_list?listenerID[]=714'>Twente University SDR (MULTI-OP)</a></td>\n"
                ."    </tr>\n"
                ."    <tr>\n"
                ."      <td valign='top'>Signal Seeklist</td>\n"
                ."      <td><a href='../reu/signal_seeklist?listenerID[]=714'>Twente University SDR (MULTI-OP)</a></td>\n"
                ."    </tr>\n"
                ."  </table>\n"
                ."  </li>"
                ."</ul>";
        }
        if (!$special){
            $out.=
                "<ul>\n"
                ."  <li><b>European listeners:</b><br>"
                ."<table cellpadding='0' cellspacing='0' border='0'>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Detailed Signal List</td>\n"
                ."    <td><a href='".BASE_PATH."reu/signal_list?".$eu_a_url."'>".$eu_a_text."</a>"
                .($eu_b_khz_l ? "<br /><a href='".BASE_PATH."reu/signal_list?".$eu_b_url."'>".$eu_b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Signals Map</td>\n"
                ."    <td><a href='".BASE_PATH."reu/signal_list?".$eu_a_url."&amp;show=map'>".$eu_a_text."</a>"
                .($eu_b_khz_l ? "<br /><a href='".BASE_PATH."reu/signal_list?".$eu_b_url."&amp;show=map'>".$eu_b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td valign='top'>Signal Seeklist</td>\n"
                ."    <td><a href='".BASE_PATH."reu/signal_seeklist?".$eu_a_url."'>".$eu_a_text."</a>"
                .($eu_b_khz_l ? "<br /><a href='".BASE_PATH."reu/signal_seeklist?".$eu_b_url."'>".$eu_b_text."</a>" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<br><br></li>"
                ."  <li><b>North American Listeners:</b><br>"
                ."<table cellpadding='0' cellspacing='0' border='0'>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Detailed Signal List</td>\n"
                ."    <td><a href='".BASE_PATH."rna/signal_list?".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rna/signal_list?".$b_url."'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Signals Map</td>\n"
                ."    <td><a href='".BASE_PATH."rna/signal_list?".$a_url."&amp;show=map'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rna/signal_list?".$b_url."&amp;show=map'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td valign='top'>Signal Seeklist</td>\n"
                ."    <td><a href='".BASE_PATH."rna/signal_seeklist?".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rna/signal_seeklist?".$b_url."'>".$b_text."</a>" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<br><br></li>"
                ."  <li><b>South American Listeners:</b><br>"
                ."<table cellpadding='0' cellspacing='0' border='0'>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Detailed Signal List</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=sa&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=sa&amp;".$b_url."'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Signals Map</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=sa&amp;".$a_url."&amp;show=map'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=sa&amp;".$b_url."&amp;show=map'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td valign='top'>Signal Seeklist</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_seeklist?region=sa&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_seeklist?region=sa&amp;".$b_url."'>".$b_text."</a>" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<br><br></li>"
                ."  <li><b>Pacific Listeners:</b><br>"
                ."<table cellpadding='0' cellspacing='0' border='0'>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Detailed Signal List</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=oc&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=oc&amp;".$b_url."'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Signals Map</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=oc&amp;".$a_url."&amp;show=map'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=oc&amp;".$b_url."&amp;show=map'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td valign='top'>Signal Seeklist</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_seeklist?region=oc&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_seeklist?region=oc&amp;".$b_url."'>".$b_text."</a>" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<br><br></li>"
                ."  <li><b>Asian Listeners:</b><br>"
                ."<table cellpadding='0' cellspacing='0' border='0'>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Detailed Signal List</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=as&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=as&amp;".$b_url."'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Signals Map</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=as&amp;".$a_url."&amp;show=map'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=as&amp;".$b_url."&amp;show=map'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td valign='top'>Signal Seeklist</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=as&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=as&amp;".$b_url."'>".$b_text."</a>" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<br><br></li>"
                ."  <li><b>African Listeners:</b><br>"
                ."<table cellpadding='0' cellspacing='0' border='0'>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Detailed Signal List</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=af&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=af&amp;".$b_url."'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td width='140' valign='top'>Signals Map</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_list?region=af&amp;".$a_url."&amp;show=map'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_list?region=af&amp;".$b_url."&amp;show=map'>".$b_text."</a>" : "")
                ."<br>&nbsp;</td>\n"
                ."  </tr>\n"
                ."  <tr>\n"
                ."    <td valign='top'>Signal Seeklist</td>\n"
                ."    <td><a href='".BASE_PATH."rww/signal_seeklist?region=af&amp;".$a_url."'>".$a_text."</a>"
                .($b_khz_l ? "<br><a href='".BASE_PATH."rww/signal_seeklist?region=af&amp;".$b_url."'>".$b_text."</a>" : "")
                ."</td>\n"
                ."  </tr>\n"
                ."</table>\n"
                ."<br></li>"
                ."</ul>";
        }
        return $out;

    }
}