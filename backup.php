<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDBRNA / NDBRE               *
// * Filename:  backup.php                   *
// *                                         *
// * Created:   25/04/2004 (MF)              *
// * Revised:   20/08/2004 (MF)              *
// * Email:     martin@classaxe.com          *
// *******************************************

// ***********************************************************************
// * DESCRIPTION:                                                        *
// ***********************************************************************
// Used for backup, restore and reset of database tables and data
//
// TIP:
//  If PHP runs as nobody, chown backup directory to nobody to allow PHP to unlink old files
//

// ***********************************************************************
// * CHANGES:                                                            *
// ***********************************************************************
// 21/03/2003 Changed $config_backup_path to $config_path_backup
// 27/03/2003 Improvements to code comments


// ***********************************************************************
// * ISSUES:                                                             *
// ***********************************************************************
//  www.mirak.ca doesn't allow db821640@localhost to write, so no local export
//  Each db_infile and db_outfile should be checked for success.
//

// ***********************************************************************
// * FUNCTION LIST:                                                      *
// ***********************************************************************
//  All functions are defined in alphabetical order.
//
//  S =		db_backup([$local],[$orderBy],[$structure],[$tableNames])
//                Produces sql export file either stored locally or exported.
//		  Returns filename for backup file or false if error
//
//  S =		db_export_sql_data([$tableNames],[$orderBy])
//		  Returns SQL string for table data - all tables if tableNames is false
//
//  S =		db_export_sql_structure()
//		  Returns SQL string for dropping all tables and recreating their structures
//		  Please read notes with this routine if using for purposes other than MKS backup.
//
//  B =		db_load($file)
//		  Opens server-hosted backup file, splits it (using db_split_sql() routine by Robin Johnson)
//		  and processes each command contained.
//		  Returns true, or false if error
//
//  N[]S =	db_split_sql()
//                Adapted from code by Robin Johnson for use in phpMyAdmin -
//                a GPL project hosted at http://sourceforge.net/projects/phpmyadmin
//		  Takes a string of SQL statements and splits them into individual SQL commands
//		  Returns array of sql strings.
//
//  S =		db_status([$filter])
//		  Returns mySQL server STATUS variable, or false if no match or error
//
//  S =		db_variables([$filter])
//		  Returns mySQL server variable, or false if no match or error
//




// ************************************
// * db_backup()                      *
// ************************************
function db_backup($local=true,$orderBy=false,$structure=true,$tableNames=false) {
  set_time_limit(600);	// Extend maximum execution time to 10 mins
  $date =	mktime();
  $server =	getenv("SERVER_NAME");
  $filename =	strftime('%Y%m%d_%H%M',$date).".sql";

  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename=$filename");

  $spaces =	"                                                        ";

  print		 "# ***********************************************************************\n"
		."# * NDBRNA / NDBRE Export File                                          *\n"
		."# ***********************************************************************\n"
		."# * Filename:  ".substr($filename.$spaces,0,56)." *\n"
		."# * System:    ".substr($server.$spaces,0,56)." *\n"
		."# * Date:      ".substr(strftime('%a %d/%m/%Y %H:%M:%S',$date).$spaces,0,56)." *\n"
		."# ***********************************************************************\n"
		."\n"
		."# To build an update file:\n"
		."#   1) Remove all drop and create statements\n"
		."#   2) Remember the INSERT IGNORE option avoids duplicates only with mySQL 3.22.10 and later.\n"
		."\n"
		."# Remember to triple escape any single quotes in data if manually editing:\n"
		."#    e.g. \"The user\\\\\'s account\"\n"
		."\n";

  if ($structure) {
    db_export_sql_structure($tableNames);
  }
  db_export_sql_data($tableNames,$orderBy);

  return $filename;
}


// ************************************
// * db_export_sql_data()             *
// ************************************
function db_export_sql_data($tableNames=false,$orderBy=false) {
  // Method:
  // 1) Reads table status to get names of tables

  set_time_limit(600);	// Extend maximum execution time to 10 mins

  $tables =		array();

  if (!$tableNames) {
    $sql =	"SHOW TABLE STATUS ";
    if (!$result = mysql_query($sql)) {
      return mysql_error();
    }
    if (!mysql_num_rows($result))	{	// If there was no match, quit.
      return false;
    }
  
    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      $table =		array();
      $table['Name'] =	$row['Name'];
      $tables[] =	$table;
    }
  }
  else {
    $tableNamesArray =	explode(',',$tableNames);
    for ($i=0; $i<count($tableNamesArray); $i++) {
      $table =		array();
      $table['Name'] =	$tableNamesArray[$i];
      $tables[] =	$table;
    }
  }



  // Read columns for each table:
  for ($i=0; $i<count($tables); $i++) {
    $sql =	"SHOW COLUMNS FROM `".$tables[$i]['Name']."`";
    if (!$result = mysql_query($sql)) {
      return	mysql_error();
    }
    if (!mysql_num_rows($result))	{	// If there was no match, quit - tables cannot have 0 columns.
      return false;
    }
    $columns =			array();
    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      $column =			array();		// Hold results for this one field
      $column['Field'] =	$row['Field'];
      ereg("([^/(]+)",$row['Type'],$type);
      $column['Type'] =		$type[0];
      $columns[] =		$column;
    }
    $tables[$i]['columns'] = $columns;

  }
  // Now generate SQL to reproduce tables:
  $out =	array();
  print 	 "# ************************************\n"
		."# * Table Data:                      *\n"
		."# ************************************\n"
		."\n";

  for($i=0; $i<count($tables); $i++) {
    $table =	$tables[$i];


    $sql =	"SELECT * FROM `".$tables[$i]['Name']."`\n"
		.(($orderBy)?
		  ("ORDER BY $orderBy"):
		  ('')
		 );
    if (!$result = @mysql_query($sql)) {
      return mysql_error();
    }
    if (mysql_num_rows($result))	{		// If there was no match, skip adding data
      $data =		array();
      while ($row = mysql_fetch_row($result)) {
        $line =		array();
        for ($j=0; $j<count($row); $j++) {
          switch($tables[$i]['columns'][$j]['Type']) {	// Numbers require no quotes, all others do.
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'float':
            case 'double':
            case 'decimal':
              $quote =	"";
            break;
            default:
              $quote =	"'";
            break;
          }
          if ($row[$j]=='' and $quote=='') {	// Don't forget $row[$j] may be a perfectly valid 0
            $line[] =	"\N";
          }
          else {
            $line[] =	$quote.addslashes($row[$j]).$quote;
          }
        }
        print	"INSERT IGNORE INTO `".$tables[$i]['Name']."` VALUES ";
        print	"(".implode($line,",").");\n";
      }
    }
  }
  print	 "#\n"
		."# (End of table data)\n"
		."#\n"
		."\n";

  return true;
}


// ************************************
// * db_export_sql_structure()        *
// ************************************
function db_export_sql_structure() {
  // Built specifically to handle types specific to the MKS.
  // Not tested on ENUM or SET types
  // Not guaranteed for general purpose use.
  // Does not wait for table locks.
  // Doesn't take account of order of tables in compound indexes.
  // Please advise of any issues encountered whatever application these routines are used with.

  set_time_limit(600);	// Extend maximum execution time to 10 mins

  $sql =	"SHOW TABLE STATUS ";
  if (!$result = mysql_query($sql)) {
    return mysql_error();
  }
  if (!mysql_num_rows($result))	{		// If there was no match, quit.
    return false;
  }

  $tables =		array();
  while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    $temp =		array();
    $temp['Name'] =	$row['Name'];
    $temp['Type'] =	(isset($row['Type']) ? $row['Type'] : $row['Engine']);
    $tables[] =		$temp;
  }

  // Read columns for each table:
  for ($i=0; $i<count($tables); $i++) {
    $sql =	"SHOW COLUMNS FROM `".$tables[$i]['Name']."`";
    if (!$result = mysql_query($sql)) {
      return mysql_error();
    }
    if (!mysql_num_rows($result))	{	// If there was no match, quit - tables cannot have 0 columns.
      return false;
    }
    $columns =			array();

    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      $column =			array();		// Hold results for this one field
      $column['Default'] =	'';
      if ($row['Extra'] != 'auto_increment') {
        $column['Default'] =	" default '".$row['Default']."'";

        if ($row['Default'] == '') {
          if ($row['Null']!='') {
            $column['Default'] =	" default NULL";
          }
        }
      }
      $column['Extra'] =	($row['Extra'])?(" ".$row['Extra']):("");
      $column['Field'] =	$row['Field'];
      $column['Null'] =		($row['Null']=="YES")?(""):(" NOT NULL");
      $column['Type'] =		$row['Type'];
      $columns[] =		$column;
    }
    $tables[$i]['Columns'] = $columns;

    // Now establish indexes:
    $sql =	"SHOW INDEX FROM `".$tables[$i]['Name']."`";
    if (!$result = mysql_query($sql)) {
      return mysql_error();
    }

    $indexes =	array();
    if (mysql_num_rows($result))	{		// If there was no match, quit.
      while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
        $indexes[] =	$row;
      }
      if (count($indexes)) {
        $index =	array();
        for ($j=0; $j<count($indexes); $j++) {
          $Comment =	$indexes[$j]['Comment'];
          $Key_name =	$indexes[$j]['Key_name'];
          $Non_unique =	$indexes[$j]['Non_unique'];
          if (!isset($index[$Key_name])) {
            $index[$Key_name] =	array();
            if ($Key_name == 'PRIMARY') {
              $index[$Key_name]['Type'] = 'PRIMARY KEY';
            }
            else {
              if ($Non_unique == '0') {
                $index[$Key_name]['Type'] = 'UNIQUE KEY';
              }
              else {
                $index[$Key_name]['Type'] = 'KEY';
              }
            }
            if ($Comment) {
              $index[$Key_name]['Type'] = $Comment." ".$index[$Key_name]['Type'];
            }
          }
          $index[$Key_name]['Column_name'][] = $indexes[$j]['Column_name'];
        }
        $index_list =	array();
        foreach ($index as $key=>$index_name) {
          if ($index_name['Type'] == 'PRIMARY KEY') {
            $index_list[] =	$index_name['Type']. "(`".implode($index_name['Column_name'],"`,`")."`)";
          }
          else {
            $index_list[] =	$index_name['Type']. " `$key` (".implode($index_name['Column_name'],",").")";
          }
        }
      }
      $tables[$i]['index'] =	$index_list;
    }
    else {
      $tables[$i]['index'] =	false;
    }
  }  



  // Now generate SQL to reproduce tables:
  $out =	array();
  $out[] =	 "# ************************************\n"
		."# * Table Structures:                *\n"
		."# ************************************\n"
		."\n";

  for($i=0; $i<count($tables); $i++) {
    $table =	$tables[$i];

    $out[] =	 "DROP TABLE IF EXISTS `".$tables[$i]['Name']."`;\n"
		."CREATE TABLE `".$tables[$i]['Name']."` (\n";
    for ($j=0; $j<count($table['Columns']); $j++) {
      $column =		$table['Columns'][$j];
      switch($column['Type']) {
        case 'tinyblob':
        case 'text':
        case 'mediumblob':
        case 'longblob':
        case 'tinytext':
        case 'text':
        case 'mediumtext':
        case 'longtext':
          $default =	'';
        break;
        default:
          $default =	$column['Default'];
        break;
      }

      $out[] =	 "  `".$column['Field']."`"
		." ".$column['Type']
		.$column['Null']
		.$default
		.$column['Extra']
		.(($j!=count($table['Columns'])-1 or $table['index'])?
		  (",\n"):
		  ("\n")
		 );
    }

    if ($table['index']) {
      $out[] =	"  ".implode($table['index'],",\n  ")."\n";
    }
    $out[] =	") TYPE=".$table['Type'].";\n\n";
  }
  $out[] =	 "# ************************************\n"
		."# * (End of Table Structures)        *\n"
		."# ************************************\n"
		."\n";

  print implode($out,'');
}


// ************************************
// * db_load()                        *
// ************************************
function db_load($file) {
  $filename =	system_backup.$file;

  $size =	filesize($filename);

  if (!$file_hd  = fopen($filename,"r")) {
    return false;
  }
  else {
    set_time_limit(600);	// Extend maximum execution time
    $sql =	trim(fread($file_hd,$size));
    $commands = db_split_sql($sql);

    for ($i=0; $i<count($commands); $i++) {
//      $out[] =	"<pre>test".$commands[$i]."</pre>";
      if (!$result = mysql_query($commands[$i])) {
        return mysql_error();
      }
    }
  }
  fclose($file_hd);
  $out[] =	"Success - Data restored from $file, ".count($commands)." records processed";
  return implode($out,"");
}


// ************************************
// * db_split_sql()                   *
// ************************************
// Adapted from code used in phpMyAdmin -
// a GPL project hosted at http://sourceforge.net/projects/phpmyadmin

function db_split_sql($sql) {
  set_time_limit(600);	// Extend maximum execution time to 10 mins
  $out =		array();
  $sql =		trim($sql);
  $sql_len =		strlen($sql);
  $char =		'';
  $string_start =	'';
  $in_string =		false;
  $time0 =		time();

  for ($i = 0; $i < $sql_len; ++$i) {
    $char =		$sql[$i];
    // We are in a string, check for not escaped end of strings except for
    // backquotes that can't be escaped
    if ($in_string) {
      for (;;) {
        $i =		strpos($sql, $string_start, $i);
        // No end of string found -> add the current substring to the
        // returned array
        if (!$i) {
           $out[] =	$sql;
           return	$out;
        }
        // Backquotes or no backslashes before quotes: it's indeed the
        // end of the string -> exit the loop
        else if ($string_start == '`' || $sql[$i-1] != '\\') {
          $string_start =	'';
          $in_string =	FALSE;
          break;
        }
        // one or more Backslashes before the presumed end of string...
        else {
          // ... first checks for escaped backslashes
          $j = 2;
          $escaped_backslash = false;
          while ($i-$j > 0 && $sql[$i-$j] == '\\') {
            $escaped_backslash = !$escaped_backslash;
            $j++;
          }
          // ... if escaped backslashes: it's really the end of the
          // string -> exit the loop
          if ($escaped_backslash) {
             $string_start  = '';
             $in_string     = FALSE;
             break;
          }
          // ... else loop
          else {
            $i++;
          }
        } // end if...elseif...else
      } // end for
    } // end if (in string)

    // We are not in a string, first check for delimiter...
    else if ($char == ';') {
      // if delimiter found, add the parsed part to the returned array
      $out[]      = substr($sql, 0, $i);
      $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
      $sql_len    = strlen($sql);
      if ($sql_len) {
        $i      = -1;
      } else {
        // The submited statement(s) end(s) here
        return $out;
      }
    } // end else if (is delimiter)

    // ... then check for start of a string,...
    else if (($char == '"') || ($char == '\'') || ($char == '`')) {
      $in_string    = TRUE;
      $string_start = $char;
    } // end else if (is start of string)

    // ... for start of a comment (and remove this comment if found)...
    else if ($char == '#' || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
      // starting position of the comment depends on the comment type
      $start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
      // if no "\n" exits in the remaining string, checks for "\r"
      // (Mac eol style)
      $end_of_comment   = (strpos(' ' . $sql, "\012", $i+2)) ?
                           strpos(' ' . $sql, "\012", $i+2) :
                           strpos(' ' . $sql, "\015", $i+2);
      if (!$end_of_comment) {
        // no eol found after '#', add the parsed part to the returned
        // array if required and exit
        if ($start_of_comment > 0) {
          $out[]    = trim(substr($sql, 0, $start_of_comment));
        }
        return $out;
      }
      else {
        $sql =	substr($sql, 0, $start_of_comment).ltrim(substr($sql, $end_of_comment));
        $sql_len      = strlen($sql);
        $i--;
      } // end if...else
    } // end else if (is comment)

    // loic1: send a fake header each 30 sec. to bypass browser timeout
    $time1     = time();
    if ($time1 >= $time0 + 30) {
      $time0 = $time1;
      header('X-pmaPing: Pong');
    } // end if
  } // end for

  // add any rest to the returned array
  if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
    $out[] = $sql;
  }
  return $out;
}



// ************************************
// * db_status()                      *
// ************************************
// Used to show number of questions if debug flag is set.
function db_status($filter=false) {
  set_time_limit(600);	// Extend maximum execution time to 10 mins
  $sql =	 "SHOW\n"
		."  STATUS\n"
		.(($filter)?("LIKE '$filter'"):(""));

  if (!$result = mysql_query($sql)) { 
    return mysql_error();
  }
  if (!mysql_num_rows($result))	{		// If there was no match, quit.
    return false;
  }

  $out =	array();
  while($row = mysql_fetch_row($result)) {
    $out[$row[0]] =	$row[1];
  }
  return $out;
}


// ************************************
// * db_variables()                   *
// ************************************
// Used to show number of questions if debug flag is set.
function db_variables($filter=false) {
  $sql =	 "SHOW\n"
		."  VARIABLES\n"
		.(($filter)?("LIKE '$filter'"):(""));

  if (!$result = mysql_query($sql)) { 
    return mysql_error();
  }
  if (!mysql_num_rows($result))	{		// If there was no match, quit.
    return false;
  }

  $out =	array();
  while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    $out[$row['Variable_name']] =	$row['Value'];
  }
  return $out;
}?>
