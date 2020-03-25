<?php

namespace Brezgalov\ApiWrapper;

/**
 * Use this in order to make any class a decorator instance
 * @package brezgalov\ApiWrapper
 */
interface IResourceDecorator
{
    /**
     * decorate your resourse
     * @param $ch
     */
    public function decorate(&$ch);
}