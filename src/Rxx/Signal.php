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
        return Tools\Attachment::countAttachments($this->table, $this->ID, 'DGPS Message');
    }

    /**
     * @return array
     */
    public function getDgpsMessages()
    {
        return Tools\Attachment::getAttachments($this->table, $this->ID, 'DGPS Message');
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
        return \Rxx\Rxx::get_dx($qth_lat, $qth_lon, $row["lat"], $row["lon"]);
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
            ."    MIN(`date`) AS `first_heard`,\n"
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
        $out = Rxx::tabItem("Profile", "signal_info", 50);
        if (!$signal) {
            return $out;
        }
        if ($signal['logs']) {
            $out.=
             Rxx::tabItem("Listeners", "signal_listeners", 80)
            .Rxx::tabItem("Logs (".$signal['logs'].")", "signal_log", 85);
        }
        if ($signal['GSQ']) {
            $out.=
             Rxx::tabItem("QNH", "signal_QNH", 35);
        }
        if ($signal['type']=='1') {
            $messages = $this->countDgpsMessages();
            $out.=
             Rxx::tabItem("Messages (".$messages.")", "signal_dgps_messages", 110);
        }
        return $out;
    }

    public function setAsHeardInRegion($region) {
        $data = array('heard_in_'.$region => 1);
        $this->update($data);
        return $this->getAffectedRows();
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
        $arr =          array();
        $html_arr =     array();
        $region =       "";
        $old_link =     false;
        $link =         false;
        $eu_link =      "<a data-signal-map-eu='".$this->getID()."'>";
        $na_link =      "<a data-signal-map-na='".$this->getID()."'>";
        foreach ($rows as $row) {
            $heard_in = $row["heard_in"];
            $daytime =  $row["daytime"];
            $region =   $row["region"];
            $link =     false;
            switch ($region) {
                case "ca":
                case "na":
                    $link = $na_link;
                    break;
                case "oc":
                    if ($heard_in=='HI') {
                        $link = $na_link;
                    }
                    break;
                case "eu":
                    $link = $eu_link;
                    break;
            }
            $html_arr[] =
                 ($old_link !==false && $link !== $old_link ? "</a> " : " ")
                .($link !==false     && $link !== $old_link ? $link : "")
                .($daytime ? "<b>".$heard_in."</b>" : $heard_in);
            $arr[] =        htmlentities($heard_in);
            $old_link =     $link;
        }
        if ($link !== false) {
            $html_arr[] = "</a>";
        }
        $data = array(
            'heard_in' =>       implode(" ", $arr),
            'heard_in_html' =>  implode("", $html_arr)
        );
        $this->update($data);
        return $this->getAffectedRows();
    }
}
