<?php

namespace Brezgalov\IPoint;

use Brezgalov\IPoint\Traits\IPointEqualsTrait;
use Brezgalov\IPoint\Traits\IPointGettersTrait;

/**
 * Class represents a point with coords
 */
class Point implements IPoint
{
    use IPointEqualsTrait;
    use IPointGettersTrait;
    /**
     * @var float
     */
    public $lat;

    /**
     * @var float
     */
    public $lon;

    /**
     * Point constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach (['lat', 'lon'] as $key) {
            if (array_key_exists($key, $config)) {
                $this->{$key} = $config[$key];
            }
        }
    }
}