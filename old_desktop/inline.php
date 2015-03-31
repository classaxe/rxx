<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   RXX log system               *
// * Filename:  inline.php                   *
// *                                         *
// * Created:   26/01/2005 (MF)              *
// * Revised:                                *
// * Email:     martin@classaxe.com          *
// *******************************************
// in-line code for mode switching

// Issues:

// Stats a bit odd on processing
// Need to display offset graphs
// When using wildcard chracters, highlight fails to highlight

define ("READONLY",0);

$REQUEST_ICAO =	@$_GET['ICAO'];
$REQUEST_hours =	@$_GET['hours'];
$REQUEST_list =	@$_GET['list'];

extract($_REQUEST);		// Extracts all request variables (GET and POST) into global scope.
$cookie_key =		@$_COOKIE['cookie_key'];
$cookie_user =	@$_COOKIE['cookie_user'];

//$li = mysql_connect("localhost","ndb-ndbrna","kj356y9945m");
$li = mysql_connect("localhost","rxx","k24l3459");
// mysqldump -undb-ndbrna -pkj356y9945m

//if (!mysql_selectdb("ndb-ndbrna",$li)) {
if (!mysql_selectdb("rxx",$li)) {
  print("Cannot connect to database!");
  die;
}
$debug=0;

if (!isset($mode)) {
  $mode = "signal_list";
}

if (!isset($submode)) {
  $submode = "";
}

if (!isset($sys)) {
  $sys = "";
}

switch ($sys) {
  case "system_RNA":
    header("Location: http://$server/dx/ndb/rna/?mode=$mode");
  break;
  case "system_REU":
    header("Location: http://$server/dx/ndb/reu/?mode=$mode");
  break;
  case "system_RWW":
    header("Location: http://$server/dx/ndb/rww/?mode=$mode");
  break;
}

if (isset($submode) && $submode=="vote") {
  $sql =	"UPDATE `poll_answer` SET `votes` = `votes` + 1 WHERE `ID` = '$my_answer'";
  @mysql_query($sql);
  setcookie('cookie_rxx_poll',$questionID,time()+31536000,"/");	// One year expiry
  header("Location: $script?mode=$mode");
}

switch ($mode) {
  // Public functions
  case "admin_help":	// made public for Brian
  case "awards":
  case "cle":
  case "help":
  case "listener_list":
  case "maps":
  case "poll_list":
  case "signal_list":
  case "signal_seeklist":
  case "stats":
  case "tools":
  case "weather":
    main();
  break;

  case "logon":
    if (isset($submode) && $submode == "logon"  && strtolower($user)==admin_user && strtolower($password)==admin_password) {
      setcookie('cookie_admin',admin_session);
      header("Location: $script?mode=$mode");
    }  
    main();
  break;

  case "find_ICAO":
  case "listener_signals":
  case "listener_edit":
  case "listener_log":
  case "listener_log_export":
  case "listener_map":
  case "listener_QNH":
  case "listener_stats":
  case "show_itu":
  case "show_sp":
  case "signal_attachments":
  case "signal_dgps_messages":
  case "signal_merge":
  case "signal_info":
  case "signal_listeners":
  case "signal_log":
  case "signal_map_eu":
  case "signal_map_na":
  case "signal_QNH":
  case "state_map":
    popup();
  break;

  case "map_af":
  case "map_alaska":
  case "map_as":
  case "map_au":
  case "map_eu":
  case "map_japan":
  case "map_na":
  case "map_pacific":
  case "map_polynesia":
  case "map_sa":
  case "map_locator":
  case "tools_coordinates_conversion":
  case "tools_DGPS_popup":
  case "tools_links":
  case "tools_navtex_fixer":
  case "tools_sunrise_calculator":
  case "weather_lightning_canada":
  case "weather_lightning_europe":
  case "weather_lightning_na":
  case "weather_metar":
  case "weather_pressure_au":
  case "weather_pressure_europe":
  case "weather_pressure_na":
  case "weather_solar_map":
    mini_popup();
  break;

  case "metar":
    print(METAR(@$REQUEST_ICAO,@$REQUEST_hours,@$REQUEST_list));
  break;
  case "ILGRadio_signallist":
    header('Content-Type: application/download');
    header('Content-Disposition: attachment;filename='.system.'.txt');
    ILGRadio_signallist();
  break;
  case "get_local_icao":
    get_local_icao();
  break;
  case "export_javascript_DGPS":
    header('Content-type: text/javascript');
    print(export_javascript_DGPS());
  break;
  case "export_ndbweblog":
    export_ndbweblog();
  break;
  case "export_ndbweblog_config":
    global $save;
    if ($save==1) {
      header('Content-Disposition: attachment;filename=config.js');
    }
    else {
      header('Content-type: text/javascript');
    }
    print(export_ndbweblog_config());
  break;
  case "export_ndbweblog_index":
    header('Content-type: text/html');
    print(export_ndbweblog_index());
  break;
  case "export_ndbweblog_log":
    global $save;
    if ($save==1) {
      header('Content-Disposition: attachment;filename=log.js');
    }
    else {
      header('Content-type: text/javascript');
    }
    print(export_ndbweblog_log());
  break;
  case "export_ndbweblog_stations":
    global $save;
    if ($save==1) {
      header('Content-Disposition: attachment;filename=stations.js');
    }
    else {
      header('Content-type: text/javascript');
    }
    print(export_ndbweblog_stations());
  break;

  case "generate_map_eu":
    generate_map_eu();
  break;

  case "generate_listener_map":
    generate_listener_map();
  break;

  case "generate_map_na":
    generate_map_na();
  break;

  case "generate_station_map":
    generate_station_map();
  break;

  case "export_station_map_na":
    export_station_map_na();
  break;

  case "state_map_gif":
    state_map_gif();
  break;

  case "export_kml_signals":
    export_kml_signals();
  break;
  case "export_text_signals":
    export_text_signals();
  break;
  case "export_text":		// old function may still be called from URLs
  case "export_text_log":
    export_text_log();
  break;
  case "show_pdf":
    show_pdf();
  break;
  case "export_signallist_excel":
    export_signallist_excel();
  break;
  case "xml_signallist":
    xml_signallist();
  break;
  case "xml_listener_stats":
    xml_listener_stats(@$submode,@$listenerID);
  break;




  // Admin functions
  case "admin_manage":
  case "sys_info":
    if (@$_COOKIE['cookie_admin']!=admin_session) {
      header("Location: http://$server$script?mode=logon");
    }
    else {
      main();
    }
  break;

  case "log_upload":
  case "poll_edit":
    if (@$_COOKIE['cookie_admin']!=admin_session) {
      header("Location: http://$server$script?mode=logon");
    }
    else {
      popup();
    }
  break;

  case "db_export":
    if (@$_COOKIE['cookie_admin']!=admin_session) {
      header("Location: http://$server$script?mode=logon");
    }
    else {
      db_backup(0);
    }
  break;


  case "logoff":
    setcookie('cookie_admin','');
    header("Location: http://$server$script?mode=logon");
  break;

  default:
    header("Location: http://$server$script");
  break;

}
?>
