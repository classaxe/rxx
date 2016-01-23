<?php
//ob_start("ob_gzhandler");
ini_set('display_errors', 1);

function __autoload($className)
{
    if (class_exists($className)) {
        return;
    }
    $fileName = $className.'.php';
    $filePath = 'classes/'.$fileName;
    if (file_exists($filePath)) {
        require_once($filePath);
        return;
    }
    $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $className.'.php');
    $filePath = 'classes/'.$fileName;
    if (file_exists($filePath)) {
        require_once($filePath);
        return;
    }
    print 'looking for '.$className;
}


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
require_once('config.php');

if (!defined('system_ID')) {
    header('location: '.BASE_PATH.'rna', 302);
    die();
}

$stat = stat(realpath($_SERVER['DOCUMENT_ROOT']).'/dx/ndb/.git/HEAD');
define("system_date", date("d M Y H:i T", $stat['mtime']));
$gitlog = explode(":", `git log master -n 1 --format="%s"`);
define("system_version", array_shift($gitlog));
define("system_revision", implode(":", $gitlog));

$script =    getenv("SCRIPT_NAME");
$server =    getenv("SERVER_NAME");
$root =        getenv('DOCUMENT_ROOT');
ini_set("mysql.trace_mode", 1);

include_once("backup.php");
include_once("donate.php");
include_once("img.php");
include_once("attachment.php");
include_once("functions.php");
include_once("cle.php");
include_once("export.php");
include_once("listener.php");
include_once("maps.php");
include_once("signal.php");
include_once("stats.php");
include_once("tools.php");
include_once("weather.php");
include_once("inline.php");
