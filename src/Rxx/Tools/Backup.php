<?php

/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 8:22 PM
 */

namespace Rxx\Tools;

/**
 * Class Backup
 * @package Rxx\Tools
 */
class Backup
{
    /**
     * @param bool $local
     * @param bool $orderBy
     * @param bool $structure
     * @param bool $tableNames
     * @return string
     */
    public static function dbBackup($local = true, $orderBy = false, $structure = true, $tableNames = false)
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $date =     mktime();
        $server =   getenv("SERVER_NAME");
        $filename = strftime('%Y%m%d_%H%M', $date).".sql";

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename");

        $spaces =    "                                                        ";

        print
            "# ***********************************************************************\n"
            ."# * RNA / REU / RWW Database Export Dump                                *\n"
            ."# ***********************************************************************\n"
            ."# * Filename:  ".substr($filename.$spaces, 0, 56)." *\n"
            ."# * System:    ".substr($server.$spaces, 0, 56)." *\n"
            ."# * Date:      ".substr(strftime('%a %d/%m/%Y %H:%M:%S', $date).$spaces, 0, 56)." *\n"
            ."# ***********************************************************************\n"
            ."\n";
        if ($structure) {
            db_export_sql_structure($tableNames);
        }
        db_export_sql_data($tableNames, $orderBy);
        return $filename;
    }


    /**
     * @param bool $tableNames
     * @param bool $orderBy
     * @return bool
     */
    public static function db_export_sql_data($tableNames = false, $orderBy = false)
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $tables =        array();
        if (!$tableNames) {
            $sql =    "SHOW TABLE STATUS ";
            if (!$result = \Rxx\Database::query($sql)) {
                return \Rxx\Database::getError();
            }
            if (!\Rxx\Database::numRows($result)) {
                return false;
            }
            while ($row = \Rxx\Database::fetchArray($result, MYSQL_ASSOC)) {
                $table =        array();
                $table['Name'] =    $row['Name'];
                $tables[] =    $table;
            }
        } else {
            $tableNamesArray =    explode(',', $tableNames);
            for ($i=0; $i<count($tableNamesArray); $i++) {
                $table =        array();
                $table['Name'] =    $tableNamesArray[$i];
                $tables[] =    $table;
            }
        }
        for ($i=0; $i<count($tables); $i++) {
            $sql =    "SHOW COLUMNS FROM `".$tables[$i]['Name']."`";
            if (!$result = \Rxx\Database::query($sql)) {
                return    \Rxx\Database::getError();
            }
            if (!\Rxx\Database::numRows($result)) {
                return false;
            }
            $columns =            array();
            while ($row = \Rxx\Database::fetchArray($result, MYSQL_ASSOC)) {
                $column =            array();        // Hold results for this one field
                $column['Field'] =    $row['Field'];
                ereg("([^/(]+)", $row['Type'], $type);
                $column['Type'] =        $type[0];
                $columns[] =        $column;
            }
            $tables[$i]['columns'] = $columns;

        }
        print
            "# ************************************\n"
            ."# * Table Data:                      *\n"
            ."# ************************************\n"
            ."\n";

        for ($i=0; $i<count($tables); $i++) {
            $table =    $tables[$i];
            $sql =
                "SELECT * FROM `".$tables[$i]['Name']."`\n"
                .($orderBy ? "ORDER BY $orderBy" : '');
            if (!$result = @\Rxx\Database::query($sql)) {
                return \Rxx\Database::getError();
            }
            if (\Rxx\Database::numRows($result)) {
                $data =        array();
                while ($row = \Rxx\Database::fetchRow($result)) {
                    $line =        array();
                    for ($j=0; $j<count($row); $j++) {
                        switch($tables[$i]['columns'][$j]['Type']) {    // Numbers require no quotes, all others do.
                            case 'tinyint':
                            case 'smallint':
                            case 'mediumint':
                            case 'int':
                            case 'bigint':
                            case 'float':
                            case 'double':
                            case 'decimal':
                                $quote =    "";
                                break;
                            default:
                                $quote =    "'";
                                break;
                        }
                        if ($row[$j]=='' and $quote=='') {
                            $line[] =    "\N";
                        } else {
                            $line[] =    $quote.addslashes($row[$j]).$quote;
                        }
                    }
                    print
                        "INSERT IGNORE INTO `".$tables[$i]['Name']."` VALUES (".implode($line, ",").");\n";
                }
            }
        }
        print
            "#\n"
            ."# (End of table data)\n"
            ."#\n"
            ."\n";
        return true;
    }

    /**
     * @return bool|string
     */
    public static function db_export_sql_structure()
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $sql =    "SHOW TABLE STATUS ";
        if (!$result = \Rxx\Database::query($sql)) {
            return \Rxx\Database::getError();
        }
        if (!\Rxx\Database::numRows($result)) {
            return false;
        }

        $tables =        array();
        while ($row = \Rxx\Database::fetchArray($result, MYSQL_ASSOC)) {
            $temp =        array();
            $temp['Name'] =    $row['Name'];
            $temp['Type'] =    (isset($row['Type']) ? $row['Type'] : $row['Engine']);
            $tables[] =        $temp;
        }
        for ($i=0; $i<count($tables); $i++) {
            $sql =    "SHOW COLUMNS FROM `".$tables[$i]['Name']."`";
            if (!$result = \Rxx\Database::query($sql)) {
                return \Rxx\Database::getError();
            }
            if (!\Rxx\Database::numRows($result)) {
                return false;
            }
            $columns =            array();
            while ($row = \Rxx\Database::fetchArray($result, MYSQL_ASSOC)) {
                $column =            array();        // Hold results for this one field
                $column['Default'] =    '';
                if ($row['Extra'] != 'auto_increment') {
                    $column['Default'] =    " default '".$row['Default']."'";
                    if ($row['Default'] == '') {
                        if ($row['Null']!='') {
                            $column['Default'] =    " default NULL";
                        }
                    }
                }
                $column['Extra'] =  ($row['Extra'] ? " ".$row['Extra'] : "");
                $column['Field'] =  $row['Field'];
                $column['Null'] =   ($row['Null']=="YES" ? "" : " NOT NULL");
                $column['Type'] =   $row['Type'];
                $columns[] =        $column;
            }
            $tables[$i]['Columns'] = $columns;

            $sql =    "SHOW INDEX FROM `".$tables[$i]['Name']."`";
            if (!$result = \Rxx\Database::query($sql)) {
                return \Rxx\Database::getError();
            }

            $indexes =    array();
            if (\Rxx\Database::numRows($result)) {
                while ($row = \Rxx\Database::fetchArray($result, MYSQL_ASSOC)) {
                    $indexes[] =    $row;
                }
                if (count($indexes)) {
                    $index =    array();
                    for ($j=0; $j<count($indexes); $j++) {
                        $Comment =    $indexes[$j]['Comment'];
                        $Key_name =    $indexes[$j]['Key_name'];
                        $Non_unique =    $indexes[$j]['Non_unique'];
                        if (!isset($index[$Key_name])) {
                            $index[$Key_name] =    array();
                            if ($Key_name == 'PRIMARY') {
                                $index[$Key_name]['Type'] = 'PRIMARY KEY';
                            } else {
                                if ($Non_unique == '0') {
                                    $index[$Key_name]['Type'] = 'UNIQUE KEY';
                                } else {
                                    $index[$Key_name]['Type'] = 'KEY';
                                }
                            }
                            if ($Comment) {
                                $index[$Key_name]['Type'] = $Comment." ".$index[$Key_name]['Type'];
                            }
                        }
                        $index[$Key_name]['Column_name'][] = $indexes[$j]['Column_name'];
                    }
                    $index_list =    array();
                    foreach ($index as $key => $index_name) {
                        if ($index_name['Type'] == 'PRIMARY KEY') {
                            $index_list[] =
                                $index_name['Type']. "(`".implode($index_name['Column_name'], "`,`")."`)";
                        } else {
                            $index_list[] =
                                $index_name['Type']. " `$key` (".implode($index_name['Column_name'], ",").")";
                        }
                    }
                }
                $tables[$i]['index'] =    $index_list;
            } else {
                $tables[$i]['index'] =    false;
            }
        }
        $out =
            "# ************************************\n"
            ."# * Table Structures:                *\n"
            ."# ************************************\n"
            ."\n";

        for ($i=0; $i<count($tables); $i++) {
            $table =    $tables[$i];

            $out.=
                "DROP TABLE IF EXISTS `".$tables[$i]['Name']."`;\n"
                ."CREATE TABLE `".$tables[$i]['Name']."` (\n";
            for ($j=0; $j<count($table['Columns']); $j++) {
                $column =        $table['Columns'][$j];
                switch($column['Type']) {
                    case 'tinyblob':
                    case 'mediumblob':
                    case 'longblob':
                    case 'tinytext':
                    case 'text':
                    case 'mediumtext':
                    case 'longtext':
                        $default =    '';
                        break;
                    default:
                        $default =    $column['Default'];
                        break;
                }

                $out.=
                    "  `".$column['Field']."`"
                    ." ".$column['Type']
                    .$column['Null']
                    .$default
                    .$column['Extra']
                    .($j!=count($table['Columns'])-1 || $table['index'] ? ",\n" : "\n");
            }

            if ($table['index']) {
                $out.=
                    "  ".implode($table['index'], ",\n  ")."\n";
            }
            $out.=
                ") TYPE=".$table['Type'].";\n\n";
        }
        $out.=
            "# ************************************\n"
            ."# * (End of Table Structures)        *\n"
            ."# ************************************\n"
            ."\n";

        print $out;
    }
}
