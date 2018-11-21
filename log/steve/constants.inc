<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  constants.inc                *
// * Owner:     Martin Francis               *
// * Created:   02/09/2003 (MF)              *
// * Revised:                                *
// *                                         *
// *******************************************

// ***********************************************************************
// * DESCRIPTION:                                                        *
// ***********************************************************************
//  Defines constants used throughout

// ***********************************************************************
// * CHANGES:                                                            *
// ***********************************************************************

// ***********************************************************************
// * ISSUES:                                                             *
// ***********************************************************************
//  None

// ***********************************************************************
// * FUNCTION LIST:                                                      *
// ***********************************************************************
//  None

// database types:
define("DATABASE_MYSQL", "mySQL");   // MySQL database type
define("DATABASE_MSQL", "mSQL");    // MSQL database type

// Types (Table Types - keep fixed values now to retain legacy data)
define("TYPE_LOG", 1); //
define("TYPE_STATION", 2); //

// table_set_field() operations and others triggered by Workflow:
define("OPERATION_ADD", 1); // table_set_field()
define("OPERATION_DECREMENT", 2); // table_set_field()
define("OPERATION_DELETE", 3); // table_delete()
define("OPERATION_DELETE_MULTI", 4); // table_delete_multi()
define("OPERATION_INCREMENT", 5); // table_set_field()
define("OPERATION_SET", 6); // table_set_field()
define("OPERATION_SUBTRACT", 7); // table_set_field()
define("OPERATION_TIMESTAMP", 8); // table_set_field()

// Error Level Reporting:
define("ALL", 0); // Even stuff that worked
define("WARN", 1); // Transaction completed, but slight issues
define("ERROR", 2); // Transaction failed, but continue anyway
define("CRITICAL", 3); // Transaction failed, stop everything.
define("NONE", 4); // Will never happen - effectively turns handlers off
