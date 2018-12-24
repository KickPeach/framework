<?php

namespace Kickpeach\Framework\Foundation;

use Kickpeach\Framework\Foundation\Exceptions\HandleExceptions;
use Kickpeach\Framework\Foundation\Exceptions\Handler;
use Kickpeach\Framework\surpport\Helpers\Arr;

/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/20
 * Time: 19:58
 */
class Application
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
    private function __construct()
    {

    }

    //获得应用单例
    public function getInstance()
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
        $response = $this->handleRequest();
        //响应请求
        $response->send();
    }


    /**
     * 处理请求
     */
    public function handleRequest()
    {
        //框架初始化，做好异常处理以及路由初始化
        $this->boostrap();
        //使用中间件过滤请求

    }

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

    /**
     * 初始化路由
     */
    protected function initRouter()
    {
        $this->router = new Router();
    }

}

