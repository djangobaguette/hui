<?php

namespace Brezgalov\IPoint\Traits;

use Brezgalov\IPoint\IPoint;

/**
 * Trait IPointEqualsTrait used to create IPoint class easily
 * @package Brezgalov\PortTransitCommon\Models\Traits
 */
trait IPointEqualsTrait
{
    /**
     * is Point equal
     * @param IPoint $point
     * @return bool
     */
    public function pointEqualTo(IPoint $point)
    {
        return $this->getLat() == $point->getLat() && $this->getLon() == $point->getLon();
    }
}