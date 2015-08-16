<?php

class Record
{
    protected $ID;
    protected $table;

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

    public function doSqlQuery($sql)
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

    public function getFieldForSql($sql)
    {
        if (!$result = $this->doSqlQuery($sql)) {
            return false;
        }
        if (!mysql_num_rows($result)) {
            return false;
        }
        $record = mysql_fetch_array($result, MYSQL_NUM);
        return $record[0];
    }

    public function getRecordForSql($sql)
    {
        if (!$result = $this->doSqlQuery($sql)) {
            return false;
        }
        if (!mysql_num_rows($result)) {
            return false;
        }
        return mysql_fetch_array($result, MYSQL_ASSOC);
    }

    public function getRecordsForSql($sql)
    {
        $out = array();
        if (!$result = $this->doSqlQuery($sql)) {
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
        // z($sql);
        return $this->doSqlQuery($sql);
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
        // z($sql);
        return $this->doSqlQuery($sql);
    }
}
