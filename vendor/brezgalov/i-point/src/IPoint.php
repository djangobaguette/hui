<?php

namespace Brezgalov\IPoint;

interface IPoint
{
    /**
     * @return float
     */
    public function getLat();

    /**
     * @return float
     */
    public function getLon();

    /**
     * @param IPoint $point
     * @return bool
     */
    public function pointEqualTo(IPoint $point);
}