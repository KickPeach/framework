<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/25
 * Time: 10:57
 */

namespace Kickpeach\Framework\Foundation\Exceptions;


class HttpException extends Exception
{
    private $statusCode;

    public function __construct($message = "", $code = 200)
    {
        $this->statusCode = $code;
        parent::__construct($message, $code);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}