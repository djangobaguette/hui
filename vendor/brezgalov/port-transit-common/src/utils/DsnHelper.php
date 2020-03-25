<?php

namespace Brezgalov\PortTransitCommon\Utils;

class DsnHelper
{
    /**
     * @param string $name
     * @param string $dsn
     * @return string|null
     */
    public static function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return count($match) >= 2 ? $match[1] : null;
        } else {
            return null;
        }
    }
}