<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/24
 * Time: 13:57
 */

namespace Kickpeach\Framework\Foundation\Exceptions\Contracts;


interface ExceptionsHandler
{
    public function handle($e);
}