<?php

class Signal extends Record
{
    public static $colors = array(
        DGPS =>     '#00D8FF',
        DSC =>      '#FFB000',
        HAMBCN =>   '#B8FFC0',
        NAVTEX =>   '#FFB8D8',
        NDB =>      '#FFFFFF',
        TIME =>     '#FFE0B0',
        OTHER =>    '#B8F8FF'
    );

    public function __construct($ID = false)
    {
        parent::__construct($ID, 'signals');
    }

    public function countDgpsMessages()
    {
        return count_attachments($this->table, $this->ID, 'DGPS Message');
    }

    public function get_dgps_messages()
    {
        return get_attachments($this->table, $this->ID, 'DGPS Message');
    }

    public function tabs()
    {
        $signal = $this->get_record();
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
}
