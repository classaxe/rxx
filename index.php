<?php
/*
Version History:
  1.0.2 (2013-09-28)
    1) Safety code added to redirect if system was not one of rna, reu or rww
  1.0.1 (2013-09-24)
    1) Added constant for type DSC
*/ 

//ob_start("ob_gzhandler");
ini_set('display_errors',1);

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
    print 'looking for '.$className;
}


$request =  explode("?",urldecode($_SERVER["REQUEST_URI"]));
$request =  trim($request[0],'/');
if (strlen($_SERVER['SCRIPT_NAME'])-(strlen('index.php'))-1){
  define('BASE_PATH',substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-(strlen('index.php'))));
}
else {
  define('BASE_PATH','/');
}
$request =  substr($request,strlen(BASE_PATH)-1);
$request_arr = explode("/",$request);
switch($request_arr[0]){
  case 'rna':
  case 'reu':
  case 'rww':
    // okay;
  break;
  default:
    header('location: /dx/ndb/rna',302);
    die();
  break;
}
define ("system",strToUpper($request_arr[0]));
define ("system_URL",BASE_PATH.$request_arr[0]);
switch($request_arr[0]){
  case 'rna':
    define("system_ID","1");
    define(
        "system_editor",
        "<script type='text/javascript'>//<!--\ndocument.write(\"<a title='Contact the Editor' href='mail\"+\"to\"+\":smoketronics\"+\"@\"+\"ymail\"+\".\"+\"com?subject=".system."%20System'>S M O'Kelley\"+\"<\/a> (for NDBs) and <a title='Contact the NDB Editor' href='mail\"+\"to\"+\":vlecler\"+\"@\"+\"ozone\"+\".\"+\"net?subject=".system."%20System'>Vincent Lecler\"+\"<\/a> (for DSC signals)\");\n//--></script>"
    );
    define("system_title","Signals Received in N &amp; C America + Hawaii");
  break;
  case 'reu':
    define(
        "system_editor",
        "<script language='javascript' type='text/javascript'>//<!--\n document.write(\"<a title='Contact the Editor' href='mail\"+\"to\"+\":aunumero13\"+\"@\"+\"gmail\"+\".\"+\"com?subject=".system."%20System'>Pat Vignoud\"+\"<\/a> (for NDBs) and <a title='Contact the NDB Editor' href='mail\"+\"to\"+\":vlecler\"+\"@\"+\"ozone\"+\".\"+\"net?subject=".system."%20System'>Vincent Lecler\"+\"<\/a> (for other modes)\");\n//--></script>"
    );
    define("system_ID","2");
    define("system_title","Signals Received in Europe");
  break;
  case 'rww':
  default:
    define("system_ID","3");
    define(
        "system_editor",
        "<script language='javascript' type='text/javascript'>//<!--\n document.write(\"<a title='Contact the Editor' href='mail\"+\"to\"+\":martin\"+\"@\"+\"classaxe\"+\".\"+\"com?subject=".system."%20System'>Martin Francis\"+\"<\/a> (if I get chance!)\");\n//--></script>"
    );
    define("system_title","Signals Received Worldwide");
  break;
}
if (isset($request_arr[1])){
  $_REQUEST['mode'] = $request_arr[1];
}

define ("admin_user","admin");
define ("admin_password","j35g8sc");
define ("admin_session","24tl2yl");

define ("NDB",0);
define ("DGPS",1);
define ("TIME",2);
define ("NAVTEX",3);
define ("HAMBCN",4);
define ("OTHER",5);
define ("DSC",6);

define ("swing_LF",0.6);	// How much signals may be off frequency before being considered wrong
define ("swing_HF",1.5);	// LF is enough to pull signals such as 414 RPB on to correct frequency

define ("poll_column_width",80);					// Width of a bar of 100% in pixels
define ("poll_column_height",14);					// Height of a results bar in pixels

define ("g_highlight","#20b020");

define ("awardsAdminEmail","kj8o.ham@gmail.com");
define ("awardsAdminName","Joseph Miller KJ8O");

define ("awardsBCCEmail","Martin@classaxe.com");
define ("awardsBCCName","Martin Francis (Awards copy)");



switch (getenv("SERVER_NAME")) {
  case "desktop.classaxe.com" :
  case "127.0.0.1" :
    define ("system_backup","c:\\backup\\");
    define ("system_mysql","c:\\mysql\\bin\\mysql");
    define ("question","c:\\www.classaxe.com\\dx\\ndb\\poll\\question.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("results","c:\\www.classaxe.com\\dx\\ndb\\poll\\results.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("smtp_host","mail.classaxe.com");

  break;
  case "www.classaxe.com":
    define ("system_backup","/home/classaxe/backup/");
    define ("system_mysql","mysql");
    define ("question","/home/classaxe/backup/question.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("results","/home/classaxe/backup/results.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("smtp_host","mail.classaxe.com");
  break;
  case "linux" :
    define ("system_backup","/var/www/html/backup/");
    define ("system_mysql","mysql");
    define ("question","/var/www/html/dx/ndb/poll/question.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("results","/var/www/html/dx/ndb/poll/results.txt");		// Path and filename - CHMOD file 666, Directory 777
    define ("smtp_host","mail.classaxe.com");
  break;
}

$stat = stat(realpath($_SERVER['DOCUMENT_ROOT']).'/dx/ndb/.git/ORIG_HEAD');
define("system_date", gmdate("d M Y H:i",$stat['mtime']));
$gitlog = explode(":", `git log master -n 1 --format="%s"`);
define("system_version", array_shift($gitlog));
define("system_revision", implode(":", $gitlog));

$script =	getenv("SCRIPT_NAME");
$server =	getenv("SERVER_NAME");
$root =	    getenv('DOCUMENT_ROOT');
ini_set("mysql.trace_mode",1);

include_once("backup.php");
include_once("donate.php");
include_once("img.php");
include_once("attachment.php");
include_once("admin.php");
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
?>