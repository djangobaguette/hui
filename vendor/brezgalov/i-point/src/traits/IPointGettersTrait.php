<?php

namespace Brezgalov\IPoint\Traits;

/**
 * Trait IPointGettersTrait used to create IPoint class easily
 * @package Brezgalov\PortTransitCommon\Models\Traits
 */
trait IPointGettersTrait
{
    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }
}