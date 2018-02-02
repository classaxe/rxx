<?php
namespace Rxx;

/**
 * Class Region
 * @package Rxx
 */
class Region extends Record
{
    /**
     *
     */
    const REGIONS = 'af,an,as,ca,eu,iw,na,oc,sa,xx';

    /**
     * @param bool $ID
     */
    public function __construct($ID = false)
    {
        parent::__construct($ID, 'region');
    }

    public static function getRegionByCode($region)
    {
        $sql = "SELECT `name` FROM region WHERE region='".$region."'";
        return static::getFieldForSql($sql);
    }
}
