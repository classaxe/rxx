<?php

// ************************************
// * connect_db()                     *
// ************************************
// returns database link or FALSE

function connect_db() {
  global $config_db_host,$config_db_name,$config_db_pass,$config_db_user,$config_path_backup;
  define ("DATABASE_TYPE", DATABASE_MYSQL);  // define ("DATABASE_TYPE", DATABASE_MSQL);

  $header =	 "<body>\n"
		."<p><font face='Verdana, Arial'><b>Status:</b><br>\n";




  $link=SQL_pconnect($config_db_host,$config_db_user,$config_db_pass);
  if (!($link && mysql_select_db($config_db_name))) {
    print(	"<html><head><title>Database Error</title></head>\n"
		.$header
		."System account <b>$config_db_user</b>@<b>$config_db_host</b> cannot connect to <b>$config_db_name</b>.</b><br>\n"
		."<p><b>Next Steps:</b><br>\n"
		."<ol>\n"
		."<li>Check the database settings and make corrections if required.</li>\n"
		."<li>If those settings are correct, and the database <b>$config_db_name</b> does not exist,<br>\n"
		."create it using this SQL command (as a user with CREATE rights):</li>\n"
		."<pre><b>CREATE DATABASE</b> `$config_db_name`;</pre></li>\n"
		."<li>If <b>$config_db_user</b>@<b>$config_db_host</b> does not have access to <b>$config_db_name</b>,<br>\n"
		."grant it using these SQL commands (as a user with GRANT rights to  <b>$config_db_name</b>) - <br>\n"
		."Versions of mySQL <b>before 4.0.2</b> should <b>omit the parameter \", <b>Create Temporary Tables</b>\"</b><br>\n"
		."<pre><b>GRANT</b> <b>Select</b>, <b>Insert</b>, <b>Update</b>, <b>Delete</b>, "
		."<b>Index</b>, <b>Alter</b>, <b>Create</b>, <b>Drop</b>, <b>References</b>, <b>Create Temporary Tables</b>\n"
		."      <b>ON</b> `$config_db_name`.*\n"
		."      <b>TO</b> '$config_db_user'@'$config_db_host'\n"
		."      <b>IDENTIFIED BY</b> '$config_db_pass';\n\n"
		."<b>FLUSH PRIVILEGES</b>;</pre></li>"
		."<li><b>Refresh this page</b> to continue system installation;</li>\n");
    die();
  }
  $sql =	"SHOW TABLES";
  if (!$result = SQL_query($sql)) { 
    die("connect_db() Cannot query SQL database");
  }
  if (!SQL_num_rows($result))	{		// If there was no match, database is empty - rebuild it
    print(	"<html><head><title>Installer</title></head>\n"
		.$header);

    if (db_load("reset.sql")) {
      print(	 "Loaded <b>".$config_path_backup."reset.sql</b> into database <b>$config_db_name</b></b></p>\n"
		."<p><b>Next Steps:</b><br>\n"
		."<ol>\n"
		."<li><b>Refresh this page</b> to access the new system;</li>\n"
		."<li><b>Logon as Administrator</b> using the default password <b>000</b>;</li>\n"
		."<li><b>Change the administrator password</b> using the administrator's User Preferences Page - <b>and remember it!</b></li></ol>\n"
		."</font></p></body></html>");
      die();
    }
    else {
      print(	 "Error loading <b>".$config_path_backup."reset.sql</b> into database <b>$config_db_name</b></p>\n"
		."<p><b>Next Steps:</b><br>\n"
		."<ul>\n"
		."<li><b>Check</b> that the file <b>".$config_path_backup."reset.sql</b> exists;</li>"
		."<li><b>Refresh this page</b> to try the system reset again.</li></ol></font></p></body></html>");
      die();
    }
  }
  return $link;
}


// ************************************
// * db_export_sql_data()             *
// ************************************
function db_export_sql_data($tableNames=false,$orderBy=false) {
  // Method:
  // 1) Reads table status to get names of tables

  $tables =		array();

  if (!$tableNames) {
    $sql =	"SHOW TABLE STATUS ";
    if (!$result = SQL_query($sql)) {
      $error =	SQL_error();
      error_handler(	CRITICAL,ERROR_DB_CANNOT_SELECT_RECORD,"db_export_sql_data()\n\n"
			.txt("cannotExecuteQuery").":\n\n$sql\n\n$error");
      return false;
    }
    if (!SQL_num_rows($result))	{		// If there was no match, quit.
      return false;
    }
  
    while ($row = SQL_fetch_array($result,MYSQL_ASSOC)) {
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
    if (!$result = SQL_query($sql)) {
    $error =	SQL_error();
      error_handler(	CRITICAL,ERROR_DB_CANNOT_SELECT_RECORD,"db_export_sql_data()\n\n"
			.txt("cannotExecuteQuery").":\n\n$sql\n\n$error");
      return false;
    }
    if (!SQL_num_rows($result))	{	// If there was no match, quit - tables cannot have 0 columns.
      return false;
    }
    $columns =			array();
    while ($row = SQL_fetch_array($result,MYSQL_ASSOC)) {
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
  $out[] =	 "# ************************************\n"
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
    if (!$result = SQL_query($sql)) {
    $error =	SQL_error();
      error_handler(	CRITICAL,ERROR_DB_CANNOT_SELECT_RECORD,"db_export_sql_data()\n\n"
			.txt("cannotExecuteQuery").":\n\n$sql\n\n$error");
      return false;
    }
    if (SQL_num_rows($result))	{		// If there was no match, skip adding data
      $out[] =		"INSERT IGNORE INTO `".$tables[$i]['Name']."` VALUES\n";
      $data =		array();
      while ($row = SQL_fetch_row($result)) {
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
        $data[] =	"(".implode($line,",").")";
      }
      $out[] =	implode($data,",\n").";\n\n";
    }
  }
  $out[] =	 "#\n"
		."# (End of table data)\n"
		."#\n"
		."\n";

  return (implode($out,''));
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

  $sql =	"SHOW TABLE STATUS ";
  if (!$result = SQL_query($sql)) {
    $error =	SQL_error();
    error_handler(	CRITICAL,ERROR_DB_CANNOT_SELECT_RECORD,"db_export_sql_structure()\n\n"
			.txt("cannotExecuteQuery").":\n\n$sql\n\n$error");
    return false;
  }
  if (!SQL_num_rows($result))	{		// If there was no match, quit.
    return false;
  }

  $tables =		array();
  while ($row = SQL_fetch_array($result,MYSQL_ASSOC)) {
    $temp =		array();
    $temp['Name'] =	$row['Name'];
    $temp['Type'] =	$row['Type'];
    $tables[] =		$temp;
  }

  // Read columns for each table:
  for ($i=0; $i<count($tables); $i++) {
    $sql =	"SHOW COLUMNS FROM `".$tables[$i]['Name']."`";
    if (!$result = SQL_query($sql)) {
    $error =	SQL_error();
      error_handler(	CRITICAL,ERROR_DB_CANNOT_SELECT_RECORD,"db_export_sql_structure()\n\n"
			.txt("cannotExecuteQuery").":\n\n$sql\n\n$error");
      return false;
    }
    if (!SQL_num_rows($result))	{	// If there was no match, quit - tables cannot have 0 columns.
      return false;
    }
    $columns =			array();

    while ($row = SQL_fetch_array($result,MYSQL_ASSOC)) {
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
    if (!$result = SQL_query($sql)) {
    $error =	SQL_error();
      error_handler(	CRITICAL,ERROR_DB_CANNOT_SELECT_RECORD,"db_export_sql_structure()\n\n"
			.txt("cannotExecuteQuery").":\n\n$sql\n\n$error");
      return false;
    }

    $indexes =	array();
    if (SQL_num_rows($result))	{		// If there was no match, quit.
      while ($row = SQL_fetch_array($result,MYSQL_ASSOC)) {
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
            $index_list[] =	$index_name['Type']. " ".$key."(".implode($index_name['Column_name'],",").")";
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

  return (implode($out,''));
}



// ************************************
// * db_load()                        *
// ************************************
function db_load($file) {
  global $config_path_backup;
  $filename =	$config_path_backup.$file;

  $size =	@filesize($filename);

  if (!@$file_hd  = fopen($filename,"r")) {
    return false;
  }
  else {
    set_time_limit(600);	// Extend maximum execution time
    $sql =	trim(fread($file_hd,$size));
    $commands = db_split_sql($sql);
    for ($i=0; $i<count($commands); $i++) {
      if (!$result = SQL_query($commands[$i])) {
        $error = 	SQL_error();
        echo("Error :<br>\n\n$sql<br>\n\n$error");
        return false;
      }
    }
  }
  fclose($file_hd);
  return true;
}


// ************************************
// * db_split_sql()                   *
// ************************************
// Adapted from code used in phpMyAdmin -
// a GPL project hosted at http://sourceforge.net/projects/phpmyadmin

function db_split_sql($sql) {
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


?>
