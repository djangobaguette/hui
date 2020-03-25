<?php

namespace Brezgalov\ApiWrapper;

abstract class ResourceDecorator implements IResourceDecorator
{
    /**
     * data you might need
     * @var array
     */
    public $data = [];

    /**
     * decorate your resource here
     * @param resource $ch
     */
    public abstract function decorate(&$ch);
}