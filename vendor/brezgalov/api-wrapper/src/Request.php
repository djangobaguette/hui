<?php

namespace Brezgalov\ApiWrapper;

class Request
{
    /**
     * base api url
     * @var string
     */
    public $baseUrl = '';

    /**
     * endpoint name
     * @var string
     */
    public $path = '';

    /**
     * request verb
     * @var string
     */
    public $method = 'GET';

    /**
     * @var array|string
     */
    public $queryParams = [];

    /**
     * @var array|string
     */
    public $bodyParams = [];

    /**
     * @var array of IResourceDecorator
     */
    public $decorators = [];

    /**
     * @var string|null
     */
    protected $responseClass = null;

    /**
     * @return string
     */
    protected function getDefaultResponseClass()
    {
        return '\Brezgalov\ApiWrapper\Response';
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * @param $className
     * @return $this
     */
    public function setResponseClass($className)
    {
        $this->responseClass = $className;
        return $this;
    }

    /**
     * @return string
     */
    public function getResponseClass()
    {
        if ($this->responseClass && class_exists($this->responseClass)) {
            return $this->responseClass;
        }

        return $this->getDefaultResponseClass();
    }

    /**
     * @param $url
     * @return $this
     */
    public function setDecorators(array $decorators)
    {
        $this->decorators = $decorators;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param $param
     * @return bool
     */
    public function validateParams($param)
    {
        if (!is_string($param) && !is_array($param)) {
            throw new \Exception('Формат параметров обязан быть string или array');
        }
    }

    /**
     * @param $params
     * @return $this
     * @throws \Exception
     */
    public function setBodyParams($params)
    {
        $this->validateParams($params);
        $this->bodyParams = $params;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     * @throws \Exception
     */
    public function setQueryParams($params)
    {
        $this->validateParams($params);
        $this->queryParams = $params;
        return $this;
    }

    /**
     * Prepare resource
     * @return resource
     */
    public function prepareCurlResourse()
    {
        $method = strtoupper($this->method);
        $path = $this->path;
        if (!empty($this->queryParams)) {
            if (is_string($this->queryParams)) {
                $path .= '?' . $this->queryParams;
            } else {
                $path .= '?' . http_build_query($this->queryParams);
            }

        }
        $ch = curl_init($this->baseUrl . $path);
        if (!in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($this->bodyParams)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->bodyParams);
            }
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        foreach ($this->decorators as $decorator) {
            if ($decorator instanceof IResourceDecorator) {
                $decorator->decorate($ch);
            }
        }

        return $ch;
    }

    /**
     * Execute request and get response
     * @return Response
     */
    public function exec()
    {
        $ch = $this->prepareCurlResourse();
        $data = $this->getResponse($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Execute request and parse json response
     * @return Response
     */
    public function execJson()
    {
        $response = $this->exec();
        if ($response->data && empty($response->errors)) {
            $data = json_decode($response->data, 1);
            if (!empty($data)) {
                $response->data = $data;
            } else {
                $response->errors[] = 'Could not parse json!';
            }
        }
        return $response;
    }

    /**
     * Parse response from resource
     * @param resource $ch
     * @return Response
     */
    public function getResponse(&$ch)
    {
        $responseClass = $this->getResponseClass();
        $response = new $responseClass();
        $response->data = curl_exec($ch);
        $response->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        if ($errno > 0) {
            $msg = curl_strerror($errno);
            $response->errors[] = "Error {$errno}: $msg";
        }
        return $response;
    }
}