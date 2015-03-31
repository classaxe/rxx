<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  index.php                    *
// * Owner:     Martin Francis               *
// * Created:   01/09/2003 (MF)              *
// * Revised:   10/09/2003 (MF)              *
// *                                         *
// *******************************************

error_reporting(15);
include("../config.inc");			// Configuration settings

include("../db.inc");				// Database reset and backup
include("../sql.inc");				// SQL Commands
include("../table.inc");			// General table operations

include("../log.inc");				// Log table routines
include("../station.inc");			// Station table routines

include("../constants.inc");			// Sets up variables
include("../functions.inc");			// Miscellaneous functions

connect_db();
$admin =	1;

include("../main.inc");				// Main program loop


?>
