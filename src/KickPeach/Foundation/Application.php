<?php
namespace Kickpeach\Framework\Foundation;
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/20
 * Time: 19:58
 */
class Application
{
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
    }

    /**
     * 异常与错误捕获处理
     */
    public function handleException()
    {

    }


}

