<?php

class Record
{
    protected   $ID;
    protected   $table;
    public      $record;

    public function __construct($ID = '', $table = '')
    {
        $this->ID = addslashes($ID);
        $this->table = $table;
    }

    public function delete()
    {
        $sql =
             "DELETE FROM\n"
            ."  `".$this->table."`\n"
            ."WHERE\n"
            ."  `ID` = ".$this->getID();
        return $this->doSqlQuery($sql);
    }

    public static function doSqlQuery($sql)
    {
        return mysql_query($sql);
    }


    public function getID()
    {
        return $this->ID;
    }

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

    public static function getAffectedRows()
    {
        return mysql_affected_rows();
    }

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

    public static function getRecordsForSql($sql)
    {
        $out = array();
        if (!$result = static::doSqlQuery($sql)) {
            z($sql);
            print mysql_error();
            return false;
        }
        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $out[] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
        return $out;
    }

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

    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function updateField($field, $value)
    {
        $data = array($field=>$value);
        return $this->update($data);
    }

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
