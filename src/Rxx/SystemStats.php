<?php
namespace Rxx;

class SystemStats
{
    public static function getLastLogDate()
    {
        switch(system) {
            case "RNA":
                $filter_log_SQL = "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `heard_in` = 'hi'))";
                break;
            case "REU":
                $filter_log_SQL = "(`region` = 'eu')";
                break;
            default:
                $filter_log_SQL = "1";
                break;
        }
        $sql =
             "SELECT\n"
            ."  DATE_FORMAT(MAX(`date`),'%Y-%m-%d') AS `last`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  ".$filter_log_SQL." AND\n"
            ."  `date` !=\"\" AND\n"
            ."  `date` !=\"0000-00-0000\"";
        $result =   mysql_query($sql);
        $row =        mysql_fetch_array($result, MYSQL_ASSOC);
        return $row["last"];
    }
}
