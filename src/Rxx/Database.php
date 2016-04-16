<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-27
 * Time: 8:02 PM
 */

namespace Rxx;


/**
 * Class Database
 * @package Rxx
 */

class Database {
    /**
     * @var $_db \mysqli Database link object
     */
    private static $_db;

    /**
     *
     */
    public static function connect()
    {
        self::$_db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
        if (!self::$_db) {
            die("Cannot connect to database!");
        }
    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     */
    public static function query($sql)
    {
        return mysqli_query(self::$_db, $sql);
    }

    /**
     * @param $result
     * @return int
     */
    public static function numRows($result)
    {
        return mysqli_num_rows($result);
    }

    /**
     * @param $result
     * @param int $type
     * @return array|null
     */
    public static function fetchArray($result, $type = MYSQLI_BOTH)
    {
        return mysqli_fetch_array($result, $type);
    }

    /**
     * @param $result
     * @return array|null
     */
    public static function fetchRow($result)
    {
        return mysqli_fetch_row($result);
    }
    /**
     * @return int
     */
    public static function affectedRows()
    {
        return mysqli_affected_rows(self::$_db);
    }

    /**
     * @param $result
     */
    public static function freeResult($result)
    {
        mysqli_free_result($result);
    }

    /**
     * @return string
     */
    public static function getError()
    {
        return mysqli_error(self::$_db);
    }
}