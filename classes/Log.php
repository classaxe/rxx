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
        $signal->updateHeardInList();
    }
}
