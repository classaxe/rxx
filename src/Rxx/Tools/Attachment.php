<?php

/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 8:19 PM
 */

namespace Rxx\Tools;

/**
 * Class Attachment
 * @package Rxx\Tools
 */
class Attachment
{
    /**
     * @param $table
     * @param $ID
     * @param string $type
     * @return array
     */
    public static function getAttachments($table, $ID, $type = '')
    {
        global $sortBy;
        $out = array();
        if ($ID=='') {
            return $out;
        }
        switch ($sortBy) {
        case 'type':
            $sort = "`attachment`.`type`,`attachment`.`title` ASC";
            break;
        case 'type_d':
            $sort = "`attachment`.`type`,`attachment`.`title` DESC";
            break;
        case 'description':
            $sort = "`attachment`.`type`,`attachment`.`title` ASC";
            break;
        case 'description_d':
            $sort = "`attachment`.`type`,`attachment`.`title` DESC";
            break;
        default:
            $sort = "`attachment`.`type`,`attachment`.`title` ASC";
            break;
        }
        $sql
            = "SELECT\n"
            ."  *\n"
            ."FROM\n"
            ."  `attachment`\n"
            ."WHERE\n"
            .($type!='' ? "  `type` = \"".addslashes($type)."\" AND\n" : "")
            ."  `destinationTable` = \"".addslashes($table)."\" AND\n"
            ."  `destinationID` = ".addslashes($ID)."\n"
            .($sort ?
                "ORDER BY\n"
                ."  ".$sort
                : "")
        ;
        if (!$result = mysql_query($sql)) {
            \Rxx\Rxx::z($sql);
        }
        if (!mysql_num_rows($result)) {
            return $out;
        }
        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $out[] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
        return $out;
    }

    /**
     * @param $table
     * @param $ID
     * @param string $type
     * @return int
     */
    public static function countAttachments($table, $ID, $type = '')
    {
        if ($ID == '') {
            return 0;
        }
        $sql
            = "SELECT\n"
            . "  COUNT(*) as `count`\n"
            . "FROM\n"
            . "  `attachment`\n"
            . "WHERE\n"
            . ($type != '' ? "  `type` = \"" . addslashes($type) . "\" AND\n" : "")
            . "  `destinationTable` = \"" . addslashes($table) . "\" AND\n"
            . "  `destinationID` = " . addslashes($ID) . "\n";

        $result = mysql_query($sql);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        return $row['count'];
    }
}
