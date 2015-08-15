<?php

class Log extends Record
{
    public function __construct($ID = false)
    {
        parent::__construct($ID, 'logs');
    }

    public function delete()
    {
        $log =          $this->getRecord();
        $listenerID =   $log["listenerID"];
        $signalID =     $log["signalID"];
        parent::delete();
        update_listener_log_count($listenerID);
        signal_update_heard_in($signalID);

        $sql =
             "UPDATE\n"
            ."  `signals`\n"
            ."SET\n"
            ."  `heard_in_af` = 0,\n"
            ."  `heard_in_as` = 0,\n"
            ."  `heard_in_ca` = 0,\n"
            ."  `heard_in_eu` = 0,\n"
            ."  `heard_in_iw` = 0,\n"
            ."  `heard_in_na` = 0,\n"
            ."  `heard_in_oc` = 0,\n"
            ."  `heard_in_sa` = 0\n"
            ."WHERE\n"
            ."  `ID` = ".$signalID;
        $this->doSqlQuery($sql);

        $sql =
             "SELECT DISTINCT\n"
            ."  `region`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signalID` = ".$signalID;
        $regions = $this->getRecordsForSql($sql);

        foreach ($regions as $region) {
            $sql =
                 "UPDATE\n"
                ."  `signals`\n"
                ."SET\n"
                ."  `heard_in_".$region['region']."` = 1\n"
                ."WHERE\n"
                ."  `ID` = ".$signalID;
            $this->doSqlQuery($sql);
        }

        $sql =
             "SELECT\n"
            ."    COUNT(*) AS `logs`,\n"
            ."    MAX(`date`) AS `last_heard`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    `signalID` = ".$signalID." AND\n"
            ."    `listenerID` IS NOT NULL AND\n"
            ."    `listenerID` !=0";
        $stats = $this->getRecordForSql($sql);

        $sql =
             "UPDATE `signals` SET\n"
            ."    `last_heard` = \"".$stats["last_heard"]."\",\n"
            ."    `logs` = \"".$stats["logs"]."\"\n"
            ."WHERE\n"
            ."    `ID` = ".$signalID;
        $this->doSqlQuery($sql);
    }
}
