<?php

class Signal extends Record
{
    public static $colors = array(
        DGPS =>     '#00d8ff',
        DSC =>      '#ffb000',
        HAMBCN =>   '#b8ffc0',
        NAVTEX =>   '#ffb8d8',
        NDB =>      '#ffffff',
        TIME =>     '#ffe0b0',
        OTHER =>    '#b8f8ff'
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
}
