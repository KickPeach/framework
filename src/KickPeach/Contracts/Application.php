<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/25
 * Time: 22:26
 */

namespace Kickpeach\Framework\Contracts;


interface Application
{
    /**
     * 应用单例
     *
     * @return static
     */
    public static function getInstance();

    /**
     * 获取应用配置
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function config($key = null, $default = null);

    /**
     * 运行
     */
    public function run();

    /**
     * handle request
     *
     * @return string
     */
    public function handle();

    /**
     * 获取路由
     *
     * @return \Tree6bee\Framework\Routing\Router
     */
    public function getRouter();

    /**
     * 获取路由参数
     *
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getAttr($key = null, $default = null);
}
