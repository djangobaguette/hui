<?php

namespace Brezgalov\ApiWrapper;

/**
 * Extend this class in order to create api client
 * @package brezgalov\ApiWrapper
 */
abstract class Client
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var array of IResourceDecorator
     */
    protected $requestDecorators = [];

    /**
     * Client constructor.
     * @param string $token - default is null
     */
    public function __construct($token = null)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    protected function getDefaultRequestClass()
    {
        return '\Brezgalov\ApiWrapper\Request';
    }

    /**
     * @param array $decorators
     */
    public function setRequestDecorators(array $decorators = [])
    {
        if (empty($decorators)) {
            $this->requestDecorators = [];
        } else {
            foreach ($decorators as $decorator) {
                $this->addRequestDecorator($decorator);
            }
        }
    }

    /**
     * Add this decorator to every request
     * @param IResourceDecorator $decorator
     * @return $this
     */
    public function addRequestDecorator(IResourceDecorator $decorator)
    {
        $this->requestDecorators[] = $decorator;
        return $this;
    }

    /**
     * get Api base url
     * @return string
     */
    abstract public function getBasePath();

    /**
     * prepare request
     * @param $path
     * @param string|null $requestClass
     * @return mixed
     */
    public function prepareRequest($path, $requestClass = null)
    {
        if (empty($requestClass)) {
            $requestClass = $this->getDefaultRequestClass();
        }
        return (new $requestClass())
            ->setUrl($this->getBasePath())
            ->setPath($path)
            ->setDecorators($this->requestDecorators)
        ;
    }
}