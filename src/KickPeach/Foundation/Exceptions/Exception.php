<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/25
 * Time: 11:01
 */

namespace Kickpeach\Framework\Foundation\Exceptions;

use Throwable;

/**
 * Class Exception
 * @package Kickpeach\Framework\Foundation\Exceptions
 */
class Exception extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
    }
}