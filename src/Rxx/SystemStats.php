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
        $sql = <<< EOD
            SELECT
                DATE_FORMAT(MAX(`date`),'%Y-%m-%d') AS `last`
            FROM
                `logs`
            WHERE
                $filter_log_SQL
EOD;

        $result =   \Rxx\Database::query($sql);
        $row =      \Rxx\Database::fetchArray($result, MYSQLI_ASSOC);
        return $row["last"];
    }
}
