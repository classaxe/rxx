<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  index.php                    *
// * Owner:     Martin Francis               *
// * Created:   2003-09-01 (MF)              *
// * Revised:   2018-11-21 (MF)              *
// *                                         *
// *******************************************

error_reporting(15);
ini_set('display_errors', true);
include("config.inc");				// Configuration settings

include("db.inc");				// Database reset and backup
include("table.inc");				// General table operations

include("log.inc");				// Log table routines
include("station.inc");				// Station table routines

include("constants.inc");			// Sets up variables
include("functions.inc");			// Miscellaneous functions

$ObjMysql = connect_db();

$admin = 0;
include("main.inc");				// Main program loop
