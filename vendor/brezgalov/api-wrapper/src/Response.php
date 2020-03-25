<?php

namespace Brezgalov\ApiWrapper;

/**
 * Class contains api response
 * @package brezgalov\ApiWrapper
 */
class Response
{
    /**
     * @var http response status
     */
    public $status;

    /**
     * Possible errors
     * @var array
     */
    public $errors = [];

    /**
     * Server response
     * @var array
     */
    public $data = [];

    /**
     * Response has no errors and $status ~ 200?
     * @return bool
     */
    public function isSuccessful()
    {
        return empty($this->errors) && $this->status >= 200 && $this->status < 300;
    }

    /**
     * Add error message
     * @param $message
     */
    public function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * Returns all error messages as a single string
     * @return string
     */
    public function getErrorsConcat($separator = '; ')
    {
        return implode($separator, $this->errors);
    }
}