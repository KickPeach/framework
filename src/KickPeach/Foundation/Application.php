<?php

namespace Kickpeach\Framework\Foundation;

use Kickpeach\Framework\Contracts\MiddlewareProvider;
use Kickpeach\Framework\Foundation\Exceptions\HandleExceptions;
use Kickpeach\Framework\Foundation\Exceptions\Handler;
use Kickpeach\Framework\surpport\Helpers\Arr;
use Kickpeach\Framework\Contracts\Application  as ApplicationContracts;
use Kickpeach\Framework\Routing\Router;
use Kickpeach\Framework\Pipeline\Pipeline;
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/20
 * Time: 19:58
 */
class Application implements ApplicationContracts,MiddlewareProvider
{
    protected $config = [
        'environment'   => 'production',
        'debug'         => false,
        'Version'     => 'TFramework/1.0',
        'timezone'      => 'PRC',
        // 'xhprof_dir'    => __DIR__ . '/../public/xhprof',
    ];

    /**
     *使用框架单例模式，只保存一个Application实例
     */

    //创建静态私有变量保存该类对象
    private static $instance = null;

    //私有克隆函数
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    //私有构造函数，防止直接创建对象
    protected function __construct()
    {

    }

    //获得应用单例
    public static function getInstance()
    {
        if (is_null(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 框架入口
     */
    public function run()
    {
        //处理请求
        $response = $this->handle();
        //响应请求
        $response->send();
    }

    protected $router;

    protected $middleware = [];

    /**
     * 框架初始化
     */
    public function boostrap()
    {
        //异常与错误捕获处理
        $this->handleException();
        //初始化一些配置
        $this->initRuntime();
        //初始化路由
        $this->initRouter();

    }

    /**
     * 异常与错误捕获处理
     */
    public function handleException()
    {
        (new HandleExceptions($this->setExceptionsHandler()))->handle();

    }

    /**
     * 构造异常接管对象
     */
    protected function setExceptionsHandler()
    {
        return new Handler('', $this->config('Version'));
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     * 获取配置文件配置
     */
    public function config($key = null, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }


    /**
     * 初始化一些配置
     */
    protected function initRuntime()
    {
        mb_internal_encoding('UTF-8');

        header(sprintf("X-Powered-By: %s", $this->config('Version')));

        // 设置中国时区
        date_default_timezone_set($this->config('timezone'));

    }

    public function handle(){
        //框架初始化，做好异常处理以及路由初始化
        $this->boostrap();
        //使用中间件过滤请求
        $middleware = array_merge($this->middleware,$this->router->getRouteMiddleware());

        return $this->sendThroughPipeline($middleware,function (){
            return $this->router->execute($this);
        });
    }

    protected function sendThroughPipeline(array $middleware,\Closure $then)
    {
        //如果有中间件，则需要走中间件
        if (count($middleware)>0){
            return (new Pipeline($this))
                ->send($this)
                ->through($middleware)
                ->then($then);
        }
        return $then();
    }


    /**
     * 初始化路由
     */
    protected function initRouter()
    {
        $this->router = new Router();
    }

    /**
     * 获取路由
     *
     * @return \Tree6bee\Framework\Routing\Router
     */
    public function getRouter(){
        return $this->router;
    }

    /**
     * 获取路由参数
     *
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getAttr($key = null, $default = null){
        return $this->router->getAttr($key, $default);
    }

    /**
     * @param $middleware
     * @return mixed
     * 获取路由中间件
     */
    public function getMiddleware($middleware)
    {
        $middleware = new $middleware();
        return $middleware;
    }
}

