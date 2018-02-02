<?php
namespace Rxx;

class Listener extends Record
{
    public function __construct($ID)
    {
        parent::__construct($ID, 'listeners');
    }

    public function tabs()
    {
        $record = $this->getRecord();
        return
             Rxx::tabItem("Profile", "listener_edit", 50)
            .($record['ID'] ?
                 Rxx::tabItem("Signals (".$record['count_signals'].")", "listener_signals", 105)
                .Rxx::tabItem("Logs (".$record['count_logs'].")", "listener_log", 85)
                .Rxx::tabItem("Export", "listener_log_export", 45)
                .($record['GSQ'] ? Rxx::tabItem("QNH", "listener_QNH", 35) : "")
                .Rxx::tabItem("Stats", "listener_stats", 45)
                .(Rxx::isAdmin() ?
                     "                <td class='tabOff' onclick='log_upload(\"".$this->ID."\");'"
                    ." onmouseover='return tabOver(this,1);' onmouseout='return tabOver(this,0);' width='45'>Add...</td>\n"
                 :
                    ""
                )
             :
                ""
            );
    }

    public function getLog($sortBy_SQL, $limit = -1, $offset = 0)
    {
        if (!$this->getID()) {
            return array();
        }
        $sql =
             "SELECT\n"
            ."  `logs`.`ID`,\n"
            ."  `logs`.`date`,\n"
            ."  `logs`.`daytime`,\n"
            ."  `logs`.`dx_km`,\n"
            ."  `logs`.`dx_miles`,\n"
            ."  `logs`.`format`,\n"
            ."  `logs`.`sec`,\n"
            ."  `logs`.`time`,\n"
            ."  `logs`.`LSB`,\n"
            ."  `logs`.`USB`,\n"
            ."  `logs`.`LSB_approx`,\n"
            ."  `logs`.`USB_approx`,\n"
            ."  `signals`.`active`,\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`GSQ`,\n"
            ."  `signals`.`ITU`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`type`,\n"
            ."  `signals`.`SP` AS `signalSP`,\n"
            ."  `signals`.`ID` AS `signalID`\n"
            ."FROM\n"
            ."  `signals`\n"
            ."INNER JOIN `logs` ON\n"
            ." `signals`.`ID` = `logs`.`signalID`\n"
            ."WHERE\n"
            ." `listenerID` = ".$this->getID()."\n"
            .($sortBy_SQL ?
                 "ORDER BY\n"
                ."  $sortBy_SQL"
             :
                ""
            )
            .($limit<>-1 ?
                "\nLIMIT\n  $offset, $limit"
             :
                ""
            );
  //  \Rxx\Rxx::z($sql);
        return $this->getRecordsForSql($sql);
    }

    public function getLogCount()
    {
        if (!$this->getID()) {
            return 0;
        }
        $sql =
             "SELECT\n"
            ."    COUNT(*) AS `count`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."   `listenerID` = ".$this->getID();
  //  \Rxx\Rxx::z($sql);
        $result = $this->getRecordForSql($sql);
        return $result['count'];
    }

    public function isDaytime($hhmm)
    {
        return
            $hhmm + 2400 >= ($this->record['timezone']*100) + 3400 &&
            $hhmm + 2400 <  ($this->record['timezone']*100) + 3800;

    }

    public function updateLogCount()
    {
        $types =  array(DGPS,DSC,HAMBCN,NAVTEX,NDB,TIME,OTHER);
        $sql =
             "SELECT\n"
            ."    MAX(`date`) AS `log_latest`,\n"
            ."    COUNT(*) AS `count_logs`,\n"
            ."    COUNT(DISTINCT(`signalID`)) AS `count_signals`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    `listenerID` = ".$this->getID();
        $row = $this->getRecordForSql($sql);
        $data =  array(
            'count_logs' =>     $row["count_logs"],
            'count_signals' =>  $row["count_signals"],
            'log_latest' =>     $row["log_latest"]
        );
        foreach (array_keys(Signal::$types) as $type) {
            $sql =
                 "SELECT\n"
                ."    COUNT(DISTINCT(`signalID`)) AS `count`\n"
                ."FROM\n"
                ."    `logs`\n"
                ."INNER JOIN `signals` ON\n"
                ."    `signals`.`ID` = `logs`.`signalID`\n"
                ."WHERE\n"
                ."    `signals`.`type` = ".$type." AND\n"
                ."    `listenerID` = ".$this->getID();
            $data['count_'.Signal::$types[$type]] = $this->getFieldForSql($sql);
        }
        return $this->update($data);
    }

    public function updateLogFormat($logFormat)
    {
        return $this->updateField('log_format', addslashes($logFormat));
    }
}
