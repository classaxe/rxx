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

    public function do_sql_query($sql)
    {
        return mysql_query($sql);
    }

    public function get_ID()
    {
        return $this->ID;
    }

    public function get_record()
    {
        if ($this->ID=='') {
            return false;
        }
        $sql =
         "SELECT\n"
        ."  *\n"
        ."FROM\n"
        ."  `".$this->table."`\n"
        ."WHERE\n"
        ."  `ID` = \"".$this->ID."\"";

        $result =        mysql_query($sql);
        return mysql_fetch_array($result, MYSQL_ASSOC);
    }

    public function getFieldForSql($sql)
    {
        if (!$result = $this->do_sql_query($sql)) {
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
        if (!$result = $this->do_sql_query($sql)) {
            return false;
        }
        if (!mysql_num_rows($result)) {
            return false;
        }
        return mysql_fetch_array($result, MYSQL_ASSOC);
    }

    public function get_records_for_sql($sql)
    {
        $out = array();
        if (!$result = $this->do_sql_query($sql)) {
            z($sql);
            print mysql_error();
            return false;
        }
        for ($i=0; $i<mysql_num_rows($result); $i++) {
            $out[] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
        return $out;
    }
}
