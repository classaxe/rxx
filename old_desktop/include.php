<?php
ob_start("ob_gzhandler");
ini_set('display_errors',1);

define ("admin_user","admin");
define ("admin_password","j35g8sc");
define ("admin_session","24tl2yl");

define ("NDB",0);
define ("DGPS",1);
define ("TIME",2);
define ("NAVTEX",3);
define ("HAMBCN",4);
define ("OTHER",5);

define ("swing_LF",0.6);	// How much signals may be off frequency before being considered wrong
define ("swing_HF",1.5);	// LF is enough to pull signals such as 414 RPB on to correct frequency

define ("poll_column_width",60);					// Width of a bar of 100% in pixels
define ("poll_column_height",10);					// Height of a results bar in pixels

define ("g_highlight","#20b020");

define ("awardsAdminEmail","radiodxer2000@yahoo.com");
define ("awardsAdminName","Joseph Miller KJ8O");

define ("awardsBCCEmail","Martin@classaxe.com");
define ("awardsBCCName","Martin Francis (Awards copy)");



switch (getenv("SERVER_NAME")) {
  case "127.0.0.1" :
    define ("system_backup","c:\\backup\\");
    define ("system_mysql","c:\\mysql\\bin\\mysql");
    define ("question","c:\\www.classaxe.com\\dx\\ndb\\poll\\question.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("results","c:\\www.classaxe.com\\dx\\ndb\\poll\\results.txt");	// Path and filename - CHMOD file 666, Directory 777 
    define ("smtp_host","smtp3.sympatico.ca");

  break;
  case "www.classaxe.com":
    define ("system_backup","/home/classaxe/backup/");
    define ("system_mysql","mysql");
    define ("question","/home/classaxe/backup/question.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("results","/home/classaxe/backup/results.txt");	// Path and filename - CHMOD file 666, Directory 777 
    define ("smtp_host","mta.mail.classaxe.com");
  break;
  case "linux" :
    define ("system_backup","/var/www/html/backup/");
    define ("system_mysql","mysql");
    define ("question","/var/www/html/dx/ndb/poll/question.txt");	// Path and filename - CHMOD file 666, Directory 777
    define ("results","/var/www/html/dx/ndb/poll/results.txt");		// Path and filename - CHMOD file 666, Directory 777 
    define ("smtp_host","smtp3.sympatico.ca");
  break;
}

define ("system_software",gmdate("d M Y H:i",filemtime("../functions.php")));

$script =	getenv("SCRIPT_NAME");
$server =	getenv("SERVER_NAME");
$root =	getenv('DOCUMENT_ROOT');
ini_set("mysql.trace_mode",1);


include_once("../class.phpmailer.php");
include_once("../class.smtp.php");
include("../backup.php");	// Backup functions
include("../img.php");		// image functions
include("../poll.php");		// Poll functions
include("../attachment.php");	// Admin functions
include("../admin.php");	// Admin functions
include("../functions.php");	// Main routine
include("../awards.php");	// Awards functions
include("../cle.php");		// CLE functions
include("../export.php");	// Export functions
include("../listener.php");	// Listener functions
include("../maps.php");		// Map functions
include("../signal.php");	// Signal functions
include("../stats.php");	// Statistics page
include("../tools.php");	// Tools page
include("../weather.php");	// Weather page and functions

include("../inline.php");	// In-line code
?>
