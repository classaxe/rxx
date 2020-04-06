<?php
namespace Rxx;

class Log extends Record
{
    public function __construct($ID = false)
    {
        parent::__construct($ID, 'logs');
    }

    public function delete()
    {
        $log =          $this->getRecord();
        $listener =     new Listener($log["listenerID"]);
        $signal =       new Signal($log["signalID"]);
        parent::delete();
        $listener->updateLogCount();
        $data = $signal->getLogsAndLastHeardDate();
        $allRegions =   explode(',', Region::REGIONS);
        foreach ($allRegions as $r) {
            $data['heard_in_'.$r] = 0;
        }
        $regions = $signal->getRegionsHeardIn();
        foreach ($regions as $region) {
            $r = $region['region'];
            $data['heard_in_'.$r] = 1;
        }
        $signal->update($data);
        $signal->updateFromLogs($log["signalID"], true);
    }

    public static function checkIfDuplicate($signalID, $listenerID, $YYYYMMDD, $hhmm = false)
    {
        $sql =
             "SELECT\n"
            ."  `ID`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signalID` = ".$signalID." AND\n"
            ."  `date` = \"".$YYYYMMDD."\" AND\n"
            .($hhmm ?    "  `time` = \"".$hhmm."\" AND\n" : "")
            ."  `listenerID` = ".$listenerID;
        return static::getRecordForSql($sql);
    }

    public static function checkIfHeardAtPlace($signalID, $heardIn)
    {
        $sql =
             "SELECT\n"
            ."  `ID`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signalID` = ".$signalID." AND\n"
            ."  `heard_in` = \"".$heardIn."\"";
        return static::getRecordForSql($sql);
    }

    public static function countTimesHeardByListener($signalID, $listenerID)
    {
        $sql =
             "SELECT\n"
            ."  COUNT(*) AS `count`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signalID` = ".$signalID." AND\n"
            ."  `listenerID` = ".$listenerID;
        return static::getRecordForSql($sql);
    }

    public static function getLogDateRange($system, $region = false)
    {
        $filter = "1";
        switch ($system) {
            case "1":
                $filter =
                    "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `heard_in` = 'hi'))";
                break;
            case "2":
                $filter =
                    "(`region` = 'eu')";
                break;
            case "3":
                if ($region!="") {
                    $filter =
                        "(`region` = '".$region."')";
                }
                break;
        }
        $sql =
             "SELECT\n"
            ."    MIN(`date`) AS `first_log`,\n"
            ."    MAX(`date`) AS `last_log`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    ".$filter;
        return static::getRecordForSql($sql);
    }
}
