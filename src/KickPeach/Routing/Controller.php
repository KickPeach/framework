<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/26
 * Time: 21:18
 */

namespace Kickpeach\Framework\Routing;



use Kickpeach\Framework\Contracts\Application;
use Kickpeach\Framework\surpport\Helpers\Arr;

abstract class Controller
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected static $middleware = [];

    public static function getMiddleware($action)
    {
        return Arr::get(static::$middleware,$action,[]);
    }

}