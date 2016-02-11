<?php
namespace Rxx;

class SystemStats
{
    /**
     * @return mixed
     */
    public static function getLastLogDate()
    {
        switch(Rxx::$system) {
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
        $result =   \Rxx\Database::query($sql);
        $row =        \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
        return $row["last"];
    }
}
