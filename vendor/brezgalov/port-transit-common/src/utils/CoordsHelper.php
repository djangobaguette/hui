<?php

namespace Brezgalov\PortTransitCommon\Utils;

use Brezgalov\IPoint\IPoint;

class CoordsHelper
{
    /**
     * @param IPoint $p1
     * @param IPoint $p2
     * @param string $unit
     * @return float
     */
    public static function getDistance(IPoint $p1, IPoint $p2, $unit = 'K')
    {
        if ($p1->pointEqualTo($p2)) {
            return 0;
        }
        $theta = $p1->getLon() - $p2->getLon();
        $dist = sin(deg2rad($p1->getLat())) * sin(deg2rad($p2->getLat())) + cos(deg2rad($p1->getLat())) * cos(deg2rad($p2->getLat())) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);


        if ($unit == 'K') {
            return ($miles * 1.609344);
        } elseif ($unit == 'N') {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}