<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/28
 * Time: 14:15
 */

namespace Kickpeach\Framework\Contracts;


interface MiddlewareProvider
{
    /**
     * @param $middleware
     * @return mixed
     * 解析出中间件实例
     */
    public function getMiddleware($middleware);

}