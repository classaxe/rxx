<?php
define("READONLY", 0);

require __DIR__ . '/vendor/autoload.php';

//ob_start("ob_gzhandler");
ini_set('display_errors', 1);

$request =  explode("?", urldecode($_SERVER["REQUEST_URI"]));
$request =  trim($request[0], '/');
if (strlen($_SERVER['SCRIPT_NAME'])-(strlen('index.php'))-1) {
    define('BASE_PATH', substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME'])-(strlen('index.php'))));
} else {
    define('BASE_PATH', '/');
}
$request =  substr($request, strlen(BASE_PATH)-1);
$request_arr = explode("/", $request);
define("system", strToUpper($request_arr[0]));
define("system_URL", BASE_PATH.$request_arr[0]);
if (isset($request_arr[1])) {
    $_REQUEST['mode'] = $request_arr[1];
}

require_once 'config.php';
if (file_exists('credentials.php')) {
    require_once 'credentials.php';
} else {
    require_once 'default.credentials.php';
}

if (!defined('system_ID')) {
    header('location: '.BASE_PATH.'rna', 302);
    die();
}

\Rxx\Database::connect();

$stat = stat(dirname(__FILE__).'/.git/HEAD');
define("system_date", date("d M Y H:i T", $stat['mtime']));
$gitlog = explode(":", `git log master -n 1 --format="%s"`);
define("system_version", array_shift($gitlog));
define("system_revision", implode(":", $gitlog));

$script =    getenv("SCRIPT_NAME");
$server =    getenv("SERVER_NAME");
$root =        getenv('DOCUMENT_ROOT');
ini_set("mysql.trace_mode", 1);

// inline.php
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

/*
  2013-08-23
    1) Added support for 'donate'

*/

// Issues:

// Stats a bit odd on processing
// Need to display offset graphs
// When using wildcard chracters, highlight fails to highlight


session_name("RXX");
session_cache_limiter('must-revalidate');
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', false);
session_start();

$REQUEST_ICAO =     @$_GET['ICAO'];
$REQUEST_hours =    @$_GET['hours'];
$REQUEST_list =     @$_GET['list'];

// Extracts all request variables (GET and POST) into global scope.
extract($_REQUEST);

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
        header("Location: ".BASE_PATH."rna/".$mode);
        die;
        break;
    case "system_REU":
        header("Location: ".BASE_PATH."reu/".$mode);
        die;
        break;
    case "system_RWW":
        header("Location: ".BASE_PATH."rww/".$mode);
        die;
        break;
}

switch ($mode) {
    // Public functions
    case "admin_help":  // made public for Brian
    case "awards":
    case "changes":
    case "cle":
    case "donate":
    case "help":
    case "listener_list":
    case "maps":
    case "poll_list":
    case "signal_list":
    case "signal_seeklist":
    case "stats":
    case "tools":
    case "weather":
        \Rxx\Rxx::main();
        break;

    case "logon":
        if (isset($submode) && $submode == "logon"  && strtolower($user)==admin_user && strtolower($password)==admin_password) {
            $_SESSION['admin'] = true;
            header("Location: ".system_URL."/".$mode);
        }
        \Rxx\Rxx::main();
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
        \Rxx\Rxx::popup();
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
        \Rxx\Rxx::mini_popup();
        break;
    case "lastlog":
        $Obj = new \Rxx\SystemStats();
        die($Obj->getLastLogDate());
        break;
    case "metar":
        print(METAR(@$REQUEST_ICAO, @$REQUEST_hours, @$REQUEST_list));
        break;
    case "ILGRadio_signallist":
        header('Content-Type: application/download');
        header('Content-Disposition: attachment;filename='.system.'.txt');
        \Rxx\Tools\Export::ILGRadio_signallist();
        break;
    case "get_local_icao":
        \Rxx\Rxx::get_local_icao();
        break;
    case "export_javascript_DGPS":
        header('Content-type: text/javascript');
        print(\Rxx\Tools\Export::export_javascript_DGPS());
        break;
    case "export_ndbweblog":
        \Rxx\Tools\Export::export_ndbweblog();
        break;
    case "export_ndbweblog_config":
        global $save;
        if ($save==1) {
            header('Content-Disposition: attachment;filename=config.js');
        } else {
            header('Content-type: text/javascript');
        }
        print(\Rxx\Tools\Export::export_ndbweblog_config());
        break;
    case "ndbweblog.css":
        header('Content-type: text/css');
        readfile('log/ndbweblog.css');
        break;
    case "help.html":
        header('Content-type: text/html');
        readfile('log/help.html');
        break;
    case "export_ndbweblog_index":
        header('Content-type: text/html');
        print(\Rxx\Tools\Export::export_ndbweblog_index());
        break;
    case "export_ndbweblog_log":
        global $save;
        if ($save==1) {
            header('Content-Disposition: attachment;filename=log.js');
        } else {
            header('Content-type: text/javascript');
        }
        print(\Rxx\Tools\Export::export_ndbweblog_log());
        break;
    case "export_ndbweblog_stations":
        global $save;
        if ($save==1) {
            header('Content-Disposition: attachment;filename=stations.js');
        } else {
            header('Content-type: text/javascript');
        }
        print(\Rxx\Tools\Export::export_ndbweblog_stations());
        break;
    case "export_station_map_na":
        // TODO: Find missing method: export_station_map_na
        //\Rxx\Tools\Export::export_station_map_na();
        break;
    case "export_kml_signals":
        \Rxx\Tools\Export::export_kml_signals();
        break;
    case "export_signallist_excel":
        \Rxx\Tools\Export::export_signallist_excel();
        break;
    case "export_signallist_pdf":
        \Rxx\Tools\Export::export_signallist_pdf();
        break;
    case "export_text_signals":
        \Rxx\Tools\Export::export_text_signals();
        break;
    case "export_raw_log":
        \Rxx\Tools\Export::export_raw_log();
        break;
    case "export_text":         // old function may still be called from URLs
    case "export_text_log":
        \Rxx\Tools\Export::export_text_log();
        break;
    case "generate_map_eu":
        \Rxx\Tools\Image::generate_map_eu();
        break;
    case "generate_listener_map":
        \Rxx\Tools\Image::generate_listener_map();
        break;
    case "generate_map_na":
        \Rxx\Tools\Image::generate_map_na();
        break;
    case "generate_station_map":
        \Rxx\Tools\Image::generate_station_map();
        break;
    case "state_map_gif":
        \Rxx\Tools\Image::state_map_gif();
        break;
    case "xml_signallist":
        \Rxx\Rxx::xml_signallist();
        break;
    case "xml_listener_stats":
        \Rxx\Rxx::xml_listener_stats(@$submode, @$listenerID);
        break;




    // Admin functions
    case "admin_manage":
    case "sys_info":
        if (!\Rxx\Rxx::isAdmin()) {
            header("Location: ".system_URL."/logon");
        } else {
            \Rxx\Rxx::main();
        }
        break;

    case "log_upload":
    case "poll_edit":
        if (!\Rxx\Rxx::isAdmin()) {
            header("Location: ".system_URL."/logon");
        } else {
            \Rxx\Rxx::popup();
        }
        break;

    case "db_export":
        if (!\Rxx\Rxx::isAdmin()) {
            header("Location: ".system_URL."/logon");
        } else {
            \Rxx\Tools\Backup::dbBackup(0);
        }
        break;


    case "logoff":
        unset($_SESSION['admin']);
        header("Location: ".system_URL);
        break;

    default:
        header("Location: ".system_URL);
        break;

}
