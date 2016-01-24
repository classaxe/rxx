<?php
namespace Rxx;

/**
 * Class Record
 * @package Rxx
 */
class Record
{
    /**
     * @var string
     */
    protected $ID;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var
     */
    public $record;

    /**
     * @param string $ID
     * @param string $table
     */
    public function __construct($ID = '', $table = '')
    {
        $this->ID = addslashes($ID);
        $this->table = $table;
    }

    /**
     * @return resource
     */
    public function delete()
    {
        $sql =
             "DELETE FROM\n"
            ."  `".$this->table."`\n"
            ."WHERE\n"
            ."  `ID` = ".$this->getID();
        return $this->doSqlQuery($sql);
    }

    /**
     * @param $sql
     * @return resource
     */
    public static function doSqlQuery($sql)
    {
        return mysql_query($sql);
    }


    /**
     * @return string
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return array|bool
     */
    public function getRecord()
    {
        if ($this->getID()=='') {
            return false;
        }
        $sql =
             "SELECT\n"
            ."  *\n"
            ."FROM\n"
            ."  `".$this->table."`\n"
            ."WHERE\n"
            ."  `ID` = \"".$this->getID()."\"";
        $result = $this->doSqlQuery($sql);
        return mysql_fetch_array($result, MYSQL_ASSOC);
    }

    /**
     * @return int
     */
    public static function getAffectedRows()
    {
        return mysql_affected_rows();
    }

    /**
     * @param $sql
     * @return bool
     */
    public static function getFieldForSql($sql)
    {
        if (!$result = static::doSqlQuery($sql)) {
            return false;
        }
        if (!mysql_num_rows($result)) {
            return false;
        }
        $record = mysql_fetch_array($result, MYSQL_NUM);
        return $record[0];
    }

    /**
     * @param $sql
     * @return array|bool
     */
    public static function getRecordForSql($sql)
    {
        if (!$result = static::doSqlQuery($sql)) {
            return false;
        }
        if (!mysql_num_rows($result)) {
            return false;
        }
        return mysql_fetch_array($result, MYSQL_ASSOC);
    }

    /**
     * @param $sql
     * @return array|bool
     */
    public static function getRecordsForSql($sql)
    {
        $out = array();
        if (!$result = static::doSqlQuery($sql)) {
            \Rxx\Rxx::z($sql);
            print mysql_error();
            return false;
        }
        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $out[] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
        return $out;
    }

    /**
     * @param $data
     * @return resource
     */
    public function insert($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = "  `".$key."` = \"".$value."\"";
        }
        natcasesort($fields);
        $sql =
             "INSERT INTO\n"
            ."  `".$this->table."`\n"
            ."SET\n"
            .implode(",\n", $fields);
        return static::doSqlQuery($sql);
    }

    /**
     * @param bool $data
     * @return array|bool
     */
    public function load($data = false)
    {
        if ($data!==false) {
            $this->record = $data;
        } else {
            $this->record = $this->getRecord();
        }
        $this->setID(isset($this->record['ID']) ? $this->record['ID'] : false);
        return $this->record;
    }

    /**
     * @param $ID
     */
    public function setID($ID)
    {
        $this->ID = $ID;
    }

    /**
     * @param $field
     * @param $value
     * @return resource
     */
    public function updateField($field, $value)
    {
        $data = array($field=>$value);
        return $this->update($data);
    }

    /**
     * @param $data
     * @return resource
     */
    public function update($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = "  `".$key."` = \"".$value."\"";
        }
        natcasesort($fields);
        $sql =
             "UPDATE\n"
            ."  `".$this->table."`\n"
            ."SET\n"
            .implode(",\n", $fields)."\n"
            ."WHERE\n"
            ."  `ID` = ".$this->getID();
        return $this->doSqlQuery($sql);
    }
}
