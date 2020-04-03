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

    public function array_group_by($key, $data) {
        $result = [];
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[''][] = $val;
            }
        }
        return $result;
    }

    private function getLogsHeardIn($signalId = false)
    {
        $WHERE = ($signalId ? "WHERE\n    signalID = $signalId" : '');
        $sql = <<<EOD
            SELECT
                signalID,
                heard_in,
                MAX(daytime) AS daytime,
                region
            FROM
                logs
            $WHERE
            GROUP BY
                heard_in,
                region,
                signalID
            ORDER BY
                signalID,
                (region='na' OR region='ca' OR (region='oc' AND heard_in='HI')),
                region,
                heard_in
EOD;
        $results =    @\Rxx\Database::query($sql);
        return $this->array_group_by('signalID', $results);
    }

    private function getLogsStats($signalId = false)
    {
        $WHERE = ($signalId ? "WHERE\n    signalID = $signalId" : '');
        $sql = <<<EOD
            SELECT
                signalID,
                COUNT(*) AS count_logs,
                COUNT(DISTINCT listenerID) as count_listeners,
                MIN(`date`) as first_heard,
                MAX(`date`) as last_heard
            FROM
                logs
            $WHERE
            GROUP BY
                signalID
EOD;
        $results =    @\Rxx\Database::query($sql);
        $out = [];
        foreach ($results as $r) {
            $out[$r['signalID']] = [
                'first_heard' =>    $r['first_heard'],
                'last_heard' =>     $r['last_heard'],
                'logs' =>           $r['count_logs'],
                'listeners' =>      $r['count_listeners']
            ];
        }
        return $out;
    }

    private function getLogsLatestSpec($signalId = false)
    {
        $sql = <<<EOD
SELECT
    signalID,
    (SELECT LSB    FROM logs l WHERE l.signalID = logs.signalID AND (l.LSB    IS NOT NULL AND l.LSB    != 0) AND (l.LSB_approx IS NULL OR l.LSB_approx = '') ORDER BY l.date DESC LIMIT 1) as LSB,
    (SELECT USB    FROM logs l WHERE l.signalID = logs.signalID AND (l.USB    IS NOT NULL AND l.USB    != 0) AND (l.USB_approx IS NULL OR l.USB_approx = '') ORDER BY l.date DESC LIMIT 1) as USB,
    (SELECT sec    FROM logs l WHERE l.signalID = logs.signalID AND (l.sec    IS NOT NULL AND l.sec    != '') ORDER BY l.date DESC LIMIT 1) as sec
FROM
    logs
GROUP BY
    signalID
EOD;
        if ($signalId) {
            $sql = str_replace('GROUP BY', "WHERE\n    logs.signalID = $signalId\nGROUP BY", $sql);
        }

        $results =    @\Rxx\Database::query($sql);

        $out = [];
        foreach ($results as $r) {
            $out[$r['signalID']] = [
                'LSB' =>    $r['LSB'],
                'USB' =>    $r['USB'],
                'sec' =>    $r['sec']
            ];
        }
        return $out;
    }

    public function updateFromLogs($signalId = false, $updateSpecs = false)
    {
        $all_regions =      ['af', 'an', 'as', 'ca', 'eu', 'iw', 'na', 'oc', 'sa'];
        $logsHeardIn =      $this->getLogsHeardIn($signalId);
        $logsStats =        $this->getLogsStats($signalId);
        if ($updateSpecs) {
            $logsLatestSpec =   $this->getLogsLatestSpec($signalId);
        }

        $data =         [];
        foreach ($logsHeardIn as $signalID => $result) {
            $heardIn =      [];
            $regions =      [];
            $old_link =     false;
            $link =         false;
            foreach ($result as $row) {
                $region = $row['region'];
                $regions[$region] = $region;
                switch ($region) {
                    case "ca":
                    case "na":
                        $link = "<a data-signal-map-na='%s'>";
                        break;
                    case "oc":
                        if ('HI' === $row["heard_in"]) {
                            $link = "<a data-signal-map-na='%s'>";
                        }
                        break;
                    case "eu":
                        $link = "<a data-signal-map-eu='%s'>";
                        break;
                    default:
                        $link = false;
                }
                $heardIn[] =
                    ($old_link && ($link !== $old_link) ? '</a> ' : ' ')
                    . ($link && ($link !== $old_link) ? sprintf($link, $row['signalID']) : '')
                    . ($row["daytime"] ? sprintf("<b>%s</b>", $row["heard_in"]) : $row["heard_in"]);
                $old_link = $link;
            }
            if ($link !== false) {
                $heardIn[] = "</a> ";
            }
            $entry = [
                'id' =>             $row['signalID'],
                'heard_in' =>       trim(strip_tags(implode('', $heardIn))),
                'heard_in_html' =>  trim(implode('', $heardIn))
            ];
            foreach($all_regions as $r) {
                $entry['heard_in_' . $r] = (isset($regions[$r]) ? 1 : 0);
            }
            $data[$row['signalID']] = $entry;
        }
        foreach ($logsStats as $signalID => $stats) {
            $data[$signalID] = array_merge($data[$signalID], $stats);
        }
        if ($updateSpecs) {
            foreach ($logsLatestSpec as $signalID => $spec) {
                $data[$signalID] = array_merge($data[$signalID], $spec);
            }
        }
        $affected = 0;
        foreach ($data as $signalID => $s) {
            $sql = "
UPDATE
    signals
SET
" . ($updateSpecs && $s['LSB'] !== null ? "    `LSB` =             '" . addslashes($s['LSB']) . "'," : '') ."
" . ($updateSpecs && $s['USB'] !== null ? "    `USB` =             '" . addslashes($s['USB']) . "'," : '') ."
" . ($updateSpecs && $s['sec'] !== null ? "    `sec` =             '" . addslashes($s['sec']) . "'," : '') ."
    `first_heard` =     '" . addslashes($s['first_heard']) . "',
    `heard_in` =        '" . addslashes($s['heard_in']) . "',
    `heard_in_html` =   '" . addslashes($s['heard_in_html']) . "',
    `heard_in_af` =     '" . $s['heard_in_af'] . "',
    `heard_in_an` =     '" . $s['heard_in_an'] . "',
    `heard_in_as` =     '" . $s['heard_in_as'] . "',
    `heard_in_ca` =     '" . $s['heard_in_ca'] . "',
    `heard_in_eu` =     '" . $s['heard_in_eu'] . "',
    `heard_in_iw` =     '" . $s['heard_in_iw'] . "',
    `heard_in_na` =     '" . $s['heard_in_na'] . "',
    `heard_in_oc` =     '" . $s['heard_in_oc'] . "',
    `heard_in_sa` =     '" . $s['heard_in_sa'] . "',
    `last_heard` =      '" . addslashes($s['last_heard']) . "',
    `logs` =            '" . addslashes($s['logs']) . "',
    `listeners` =       '" . addslashes($s['listeners']) . "'
WHERE
    ID =                $signalID";
            \Rxx\Database::query($sql);
            if (\Rxx\Database::affectedRows()) {
                $affected += 1;
            }
        }
        return $affected;
    }

}
