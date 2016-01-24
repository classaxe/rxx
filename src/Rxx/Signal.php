<?php
namespace Rxx;

/**
 * Class Signal
 * @package Rxx
 */
class Signal extends Record
{
    /**
     * @var array
     */
    public static $colors = array(
        DGPS =>     '#00d8ff',
        DSC =>      '#ffb000',
        HAMBCN =>   '#b8ffc0',
        NAVTEX =>   '#ffb8d8',
        NDB =>      '#ffffff',
        TIME =>     '#ffe0b0',
        OTHER =>    '#b8f8ff'
    );

    /**
     * @var array
     */
    public static $types = array(
        0 =>    "NDB",
        1 =>    "DGPS",
        2 =>    "TIME",
        3 =>    "NAVTEX",
        4 =>    "HAMBCN",
        5 =>    "OTHER",
        6 =>    "DSC"
    );

    /**
     * @param bool $ID
     */
    public function __construct($ID = false)
    {
        parent::__construct($ID, 'signals');
    }

    /**
     * @return int
     */
    public function countDgpsMessages()
    {
        return \Rxx\Tools\Attachment::countAttachments($this->table, $this->ID, 'DGPS Message');
    }

    /**
     * @return array
     */
    public function getDgpsMessages()
    {
        return \Rxx\Tools\Attachment::getAttachments($this->table, $this->ID, 'DGPS Message');
    }

    /**
     * @param $qth_lat
     * @param $qth_lon
     * @return array
     */
    public function getDX($qth_lat, $qth_lon)
    {
        if (!$qth_lat) {
            return array(false,false);
        }
        $sql =
             "SELECT\n"
            ."    `lat`,\n"
            ."    `lon`\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE\n"
            ."    `ID` = ".$this->getID();
        $row =    $this->getRecordForSql($sql);
        if (!$row["lat"]) {
            return array(false,false);
        }
        return get_dx($qth_lat, $qth_lon, $row["lat"], $row["lon"]);
    }

    /**
     * @return array|bool
     */
    public function getRegionsHeardIn()
    {
        $sql =
             "SELECT DISTINCT\n"
            ."  `region`\n"
            ."FROM\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `signalID` = ".$this->getID();
        return $this->getRecordsForSql($sql);

    }

    /**
     * @return array|bool
     */
    public function getLogsAndLastHeardDate()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*) AS `logs`,\n"
            ."    MAX(`date`) AS `last_heard`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    `signalID` = ".$this->getID()." AND\n"
            ."    `listenerID` IS NOT NULL AND\n"
            ."    `listenerID` !=0";
        return $this->getRecordForSql($sql);
    }

    /**
     * @return string
     */
    public function tabs()
    {
        $signal = $this->getRecord();
        $out = tabItem("Profile", "signal_info", 50);
        if (!$signal) {
            return $out;
        }
        if ($signal['logs']) {
            $out.=
             tabItem("Listeners", "signal_listeners", 80)
            .tabItem("Logs (".$signal['logs'].")", "signal_log", 85);
        }
        if ($signal['GSQ']) {
            $out.=
             tabItem("QNH", "signal_QNH", 35);
        }
        if ($signal['type']=='1') {
            $messages = $this->countDgpsMessages();
            $out.=
             tabItem("Messages (".$messages.")", "signal_dgps_messages", 110);
        }
        return $out;
    }

    /**
     * @return int
     */
    public function updateHeardInList()
    {
        $sql =
             "SELECT DISTINCT\n"
            ."    `heard_in`,\n"
            ."     MAX(`daytime`) as `daytime`,\n"
            ."    `region`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    `signalID` = ".$this->getID()."\n"
            ."GROUP BY\n"
            ."    `heard_in`\n"
            ."ORDER BY\n"
            ."    (`region`='na' OR `region`='ca' OR (`region`='oc' AND `heard_in`='HI')),\n"
            ."    `region`,\n"
            ."    `heard_in`";
        $rows = $this->getRecordsForSql($sql);
        $arr =        array();
        $html_arr =        array();
        $region =        "";
        $old_link =        "";
        foreach ($rows as $row) {
            $heard_in = $row["heard_in"];
            $daytime =  $row["daytime"];
            $region =   $row["region"];
            $link =     "";
            switch ($region) {
                case "ca":
                    $link =
                         "<a class='hover' href='#' onclick='return signal_map_na(".$this->getID().")'"
                        ." title='North American Reception Map'>";
                    break;
                case "na":
                    $link =
                         "<a class='hover' href='#' onclick='return signal_map_na(".$this->getID().")'"
                        ." title='North American Reception Map'>";
                    break;
                case "oc":
                    if ($heard_in=='HI') {
                        $link =
                             "<a class='hover' href='#' onclick='return signal_map_na(".$this->getID().")'"
                            ." title='North American Reception Map'>";
                    }
                    break;
                case "eu":
                    $link =
                         "<a class='hover' href='#' onclick='return signal_map_eu(".$this->getID().")'"
                        ." title='European Reception Map'>";
                    break;
            }
            $html_arr[] =
                 ($old_link!="" && $old_link != $link ? "</a>" : "")
                .($link != $old_link ? $link : "")
                .($daytime ? "<b>".$heard_in."</b>" : $heard_in);
            $arr[] =        $heard_in;
            $old_link =     $link;
        }
        $data = array(
            'heard_in' =>       implode($arr, " "),
            'heard_in_html' =>  implode($html_arr, " ")
        );
        $this->update($data);
        return $this->getAffectedRows();
    }
}
