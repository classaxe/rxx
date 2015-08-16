<?php

class Region extends Record
{
    const REGIONS = 'af,an,as,ca,eu,iw,na,oc,sa,xx';

    public function __construct($ID = false)
    {
        parent::__construct($ID, 'region');
    }
}
