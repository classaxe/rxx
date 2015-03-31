<?php
// ***********************************************************************
// * FILE HEADER:                                                        *
// ***********************************************************************
// * Project:   Mirak Knowledge System        __  __   _                 *
// * URL:       http://www.mirak.ca          |  \/  | | | __  _____      *
// * Version:   1.0.5                        | |\/| | | |/ / / ___/      *
// *                                         | |  | | |   <  \___ \      *
// * Filename:  html.inc                     | |  | | | |\ \  __/ /      *
// * Owner:     Martin Francis               |_|  |_| |_| \_\ \__/       *
// * Created:   05/06/2002 (MF)              ########################    *
// * Revised:   27/03/2003 (MF)              # http://www.mirak.ca/ #    *
// *                                         ########################    *
// ***********************************************************************

// ***********************************************************************
// * DESCRIPTION:                                                        *
// ***********************************************************************
//   Provides database-neutral connectivity for interfacing system to
//   other types of database. Other methods such as mysql_query() should
//   not be used in this system.
// 
//   Based on idea by Jesus Castagneto in 'Professional PHP Programming'
//   (WROX)(Page 682) but now includes switches for other database types.
// 
//   DATABASE_TYPE is presently defined in connect.inc
//

// ***********************************************************************
// * CHANGES:                                                            *
// ***********************************************************************
// 27/03/2003 Changes to SQL_connect so warnings are supressed here

// ***********************************************************************
// * ISSUES:                                                             *
// ***********************************************************************

// ***********************************************************************
// * FUNCTION LIST:                                                      *
// ***********************************************************************
//
// All functions are defined in alphabetical order.
//
//  I =		SQL(db,query,link)
//
//  I =		SQL_affected_rows(res)
//
//  B =		SQL_close(link)
//
//  R =		SQL_connect([host],[username],[password])
//
//  B =		SQL_data_seek(res,row)
//
//  S =		SQL_error()
//
//  A[] =	SQL_fetch_array(res,[type])
//
//  N[] =	SQL_fetch_row(res)
//
//  S =		SQL_fieldname(res,field)
//
//  B =		SQL_free_result(res)
//
//  I =		SQL_num_fields(res)
//
//  I =		SQL_num_rows(res)
//
//  R =		SQL_pconnect([host],[username],[password])
//
//  R =		SQL_query(query,[link])
//
//  B =		SQL_select_db(db,[link])
//


// ************************************
// * SQL()                            *
// ************************************
function SQL($db,$query,$link) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql($db,$query,$link);
    break;

    case DATABASE_MSQL:
      return msql($db,$query,$link);
    break;
  }
}


// ************************************
// * SQL_affected_rows()              *
// ************************************
function SQL_affected_rows($res) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_affected_rows($res);
    break;

    case DATABASE_MSQL:
      return msql_affected_rows($res);
    break;
  }
}


// ************************************
// * SQL_close()                      *
// ************************************
function SQL_close($link) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_close($link);
    break;

    case DATABASE_MSQL:
      return msql_close($link);
    break;
  }
}


// ************************************
// * SQL_connect()                    *
// ************************************
function SQL_connect($host="local",$username="",$password="") {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      if ($host == "local")	return @mysql_connect();
      else			return @mysql_connect($host,$username,$password);
    break;

    case DATABASE_MSQL:
      if ($host == "local")	return @msql_connect();
      else			return @msql_connect($host,$username,$password);
    break;
  }
}


// ************************************
// * SQL_data_seek()                  *
// ************************************
function SQL_data_seek($res,$row) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_data_seek($res,$row);
    break;

    case DATABASE_MSQL:
      return msql_data_seek($res,$row);
    break;
  }
}


// ************************************
// * SQL_error()                      *
// ************************************
function SQL_error() {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_errno().": ".mysql_error();
    break;

    case DATABASE_MSQL:
      return msql_error();
    break;
  }
}


// ************************************
// * SQL_fetch_array()                *
// ************************************
function SQL_fetch_array($res,$type="") {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      if ($type == "")		return mysql_fetch_array($res);
      else			return mysql_fetch_array($res,$type);
    break;

    case DATABASE_MSQL:
      if ($type == "")		return msql_fetch_array($res);
      else			return msql_fetch_array($res,$type);
    break;
  }
}


// ************************************
// * SQL_fetch_row()                  *
// ************************************
function SQL_fetch_row($res) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_fetch_row($res);
    break;

    case DATABASE_MSQL:
      return msql_fetch_row($res);
    break;
  }
}


// ************************************
// * SQL_fieldname()                  *
// ************************************
function SQL_fieldname($res,$field) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_fieldname($res,$field);
    break;

    case DATABASE_MSQL:
      return msql_fieldname($res,$field);
    break;
  }
}


// ************************************
// * SQL_free_result()                *
// ************************************
function SQL_free_result($res) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_free_result($res);
    break;

    case DATABASE_MSQL:
      return msql_free_result($res);
    break;
  }
}


// ************************************
// * SQL_num_fields()                 *
// ************************************
function SQL_num_fields($res) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_num_fields($res);
    break;

    case DATABASE_MSQL:
      return msql_num_fields($res);
    break;
  }
}


// ************************************
// * SQL_num_rows()                   *
// ************************************
function SQL_num_rows($res) {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      return mysql_num_rows($res);
    break;

    case DATABASE_MSQL:
      return msql_num_rows($res);
    break;
  }
}


// ************************************
// * SQL_pconnect()                   *
// ************************************
function SQL_pconnect($host="local",$username="",$password="") {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      if ($host == "local")	return @mysql_pconnect();
      else			return @mysql_pconnect($host,$username,$password);
    break;

    case DATABASE_MSQL:
      if ($host == "local")	return @msql_pconnect();
      else			return @msql_pconnect($host,$username,$password);
    break;
  }
}


// ************************************
// * SQL_query()                      *
// ************************************
function SQL_query($query,$link="") {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      if ($link == "")		return mysql_query($query);
      else			return mysql_query($query,$link);
    break;

    case DATABASE_MSQL:
      if ($link == "")		return msql_query($query);
      else			return msql_query($query,$link);
    break;
  }
}


// ************************************
// * SQL_select_db()                  *
// ************************************
function SQL_select_db($db,$link="") {
  switch(DATABASE_TYPE) {
    case DATABASE_MYSQL:
      if ($link == "")		return mysql_select_db($db);
      else			return mysql_select_db($db,$link);
    break;

    case DATABASE_MSQL:
      if ($link == "")		return msql_select_db($db);
      else			return msql_select_db($db,$link);
    break;
  }
}
?>
