<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Station Watch            *
// *                                         *
// * Filename:  table.inc                    *
// * Owner:     Martin Francis               *
// * Created:   02/09/2003 (MF)              *
// * Revised:                                *
// *                                         *
// *******************************************

// ***********************************************************************
// * DESCRIPTION:                                                        *
// ***********************************************************************
//  Handles generic queries common to all tables
//  ALL table functions perform operations when given an ID to reference record
//  Other operations (such as those which determine ID from name) should be
//  handled by table specific include files.

// ***********************************************************************
// * CHANGES:                                                            *
// ***********************************************************************
//  18/03/2003 Improvements to table_get_range() to allow it to use filters

// ***********************************************************************
// * ISSUES:                                                             *
// ***********************************************************************
//  None

// ***********************************************************************
// * FUNCTION LIST:                                                      *
// ***********************************************************************
//  All functions are defined in alphabetical order.
//
//  I =		table_count_match($destinationType,[$filter])
//		  Returns count for records matching optional filter
//
//  S =		table_copy_record($destinationID,$destinationType)
//		  Creates new copy of record and returns ID
//
//  B =		table_delete_record($destinationID,$destinationType)
//		  Returns true if no error (true does NOT mean successful deletion)
//
//  B =		table_delete_multi($destinationID,$destinationType)
//		  Calls native object deletion routines.
//		  Returns true if no error (true does NOT mean successful deletion)
//
//  S =		table_copy_record($destinationID,$destinationType)
//		  Returns ID for new copy of record
//
//  B =		table_exists_record($destinationID,$destinationType)
//		  Returns true if record exists in given table
//
//  A[] =	table_get($destinationID,$destinationType)
//		  Returns associative array for given destination, or false if no result
//
//  A[] =	table_get_all($destinationType)
//		  Returns associative array containing all result for given destination type, or false if none
//
//  S =		table_get_field($destinationID,$destinationSubType,$destinationType)
//		  Returns value for given destination field or false if no result
//
//  N[]A[] =	table_get_range($destinationType,[$records],[$offset],[$sortBy],[$filter])
//		  Returns a page of results matching criteria given.
//		  Cannot be used for user or group reports (complex types, and hides system account)
//		  Connot be used for phrasebook as this has a compound primary key.
//
//  B =		table_set_field($destinationID,$destinationSubType,$destinationType,$destinationValue,$operation)
//		  Returns true if no error (true != successful update)
//		  Values for $operation are:
//		    OPERATION_ADD		Adds given value to destination value
//		    OPERATION_DECREMENT		decrements destination value
//		    OPERATION_INCREMENT		increments destination value
//		    OPERATION_SET		sets $destinationSubType to $destinationValue
//		    OPERATION_SUBTRACT		Subtracts given value to destination value
//		    OPERATION_TIMESTAMP		sets destination value to current time, as a unix timestamp
//
//  S =		table_uniqID($table)
//		  Returns uniqueID (13 character string) for a tested unique
//                ID value - retries if collision until no collision.
//		  These could now be changed to be completely random if required.
//



// ************************************
// * table_count_match()              *
// ************************************
function table_count_match($destinationType,$filter=false) {
  $destinationType =	text_table_name($destinationType);

  $sql =		 "SELECT\n"
			."  COUNT(*)\n"
			."FROM\n"
			."  `$destinationType`\n"
			.(($filter)?
			  ("WHERE\n  $filter"):
			  ("")
			);

  if (!$result = SQL_query($sql)) {
    $error =	SQL_error();
    print(SQL_error());
    return false;
  }
  if (!SQL_num_rows($result)) {			// If there was no match, quit.
    return false;
  }
  $row = SQL_fetch_row($result);
  return $row[0];
}


// ************************************
// * table_delete_record()            *
// ************************************
function table_delete_record($destinationID,$destinationType) {
  $destinationType =	text_table_name($destinationType);
  $sql =		 "DELETE FROM\n"
			."  `$destinationType`\n"
			."WHERE\n"
			."  ID = '$destinationID'";

  if (!$result = SQL_query($sql)) {
    print(SQL_error());
    return false;
  }
  return true;
}


// ************************************
// * table_exists_record()            *
// ************************************
function table_exists_record($destinationID,$destinationType) {
  $destinationType =	text_table_name($destinationType);
  $sql =		 "SELECT\n"
			."  COUNT(*)\n"
			."FROM\n"
			."  `$destinationType`\n"
			."WHERE\n"
			."  ID = '$destinationID'";

  if (!$result = SQL_query($sql)) {
    print(SQL_error());
    return false;
  }
  $row = SQL_fetch_row($result);
  return $row[0]>0;
}



// ************************************
// * table_get()                      *
// ************************************
function table_get($destinationID,$destinationType) {
// Problem - this function does not get back unix datestamps but text for dates.

  $destinationType =	text_table_name($destinationType);
  $sql =		 "SELECT\n"
			."  *\n"
			."FROM\n"
			."  `$destinationType`\n"
			."WHERE\n"
			."  ID = '$destinationID'";

  if (!$result = SQL_query($sql)) {
    print(SQL_error());
    return false;
  }
  if (!SQL_num_rows($result)) {			// If there was no match, quit.
    return false;
  }
  return SQL_fetch_array($result,MYSQL_ASSOC);
}



// ************************************
// * table_get_all()                  *
// ************************************
function table_get_all($destinationType,$orderBy=false) {
  $destinationType =	text_table_name($destinationType);
  $sql =		 "SELECT\n"
			."  *\n"
			."FROM\n"
			."  `$destinationType`\n"
			.(($orderBy)?
			  ("ORDER BY '$orderBy'"):
			  ('')
			 );

  if (!$result = SQL_query($sql)) {
    print(SQL_error());
  }
  if (!SQL_num_rows($result)) {			// If there was no match, quit.
    return false;
  }

  $out = array();
  while($row = SQL_fetch_array($result,MYSQL_ASSOC)) {
    $out[] =	$row;
  }

  return $out;
}



// ************************************
// * table_get_field()                *
// ************************************
function table_get_field($destinationID,$destinationSubType,$destinationType) {
  $destinationType =	text_table_name($destinationType);
  $sql =	 "SELECT\n"
		."  $destinationSubType\n"
		."FROM\n"
		."  `$destinationType`\n"
		."WHERE\n"
		."  ID = '$destinationID'";

  if (!$result = SQL_query($sql)) {
    print(SQL_error());
    return false;
  }
  if (!SQL_num_rows($result)) {			// If there was no match, quit.
    return false;
  }
  $row =	SQL_fetch_row($result);
  SQL_free_result($result);
  return $row[0];
}



// ************************************
// * table_get_range()                *
// ************************************
function table_get_range($destinationType,$records=false,$offset=false,$sortBy=false,$filter=false) {
  $destinationType =	text_table_name($destinationType);
  $sql =	 "SELECT\n"
		."  *\n"
		."FROM\n"
		."  `$destinationType`\n"
		.(($filter)?(" WHERE ".ereg_replace(chr(1)," ",$filter)):(""))
		."\n"
		.(($sortBy)?("  ORDER BY $sortBy"):(""))
		.(($records and $range)?(" LIMIT $offset,$records"):(''));

  if (!$result = SQL_query($sql)) {
    print(SQL_error());
    return false;
  }
  if (!SQL_num_rows($result)) {			// If there was no match, quit.
    return false;
  }
  $out =		array();
  while ($row = 	SQL_fetch_array($result,MYSQL_ASSOC)) {
    $out[] =		$row;
  }
  return $out;
}

// ************************************
// * table_set_field()                *
// ************************************
function table_set_field($destinationID,$destinationSubType,$destinationType,$destinationValue,$operation) {
  switch ($operation) {
    case OPERATION_ADD:
      $destinationValue = $destinationValue+table_get_field($destinationID,$destinationSubType,$destinationType);
    break;

    case OPERATION_DECREMENT:
      $destinationValue = "$destinationSubType - 1";
    break;

    case OPERATION_INCREMENT:
      $destinationValue = "$destinationSubType + 1";
    break;

    case OPERATION_TIMESTAMP:
      $destinationValue = "'".mktime()."'";
    break;

    case OPERATION_SET:
      $destinationValue = "'$destinationValue'";
    break;

    case OPERATION_SUBTRACT:
      $destinationValue = $destinationValue-table_get_field($destinationID,$destinationSubType,$destinationType);
    break;

    default:
      jsAlert("Unknown Operation $operation");
    break;
  }

  $destinationType =	text_table_name($destinationType);
  $sql =		 "UPDATE\n"
			."  `$destinationType`\n"
			."SET\n"
			."  $destinationSubType = $destinationValue\n"
			."WHERE\n"
			."  ID = '$destinationID'";

  if (!$result = SQL_query($sql)) {
    print(SQL_error());
    return false;
  }
  return true;
}

// ************************************
// * table_uniqID()                   *
// ************************************
function table_uniqID($destinationType) {
  $notDone =		true;
  $n = 0 ;
  while ($notDone) {
    $ID = 		uniqid('');
    $notDone =		table_exists_record($ID,$destinationType);
  }
  return $ID;
}
?>
