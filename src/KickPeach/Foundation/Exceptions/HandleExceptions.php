<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/20
 * Time: 20:56
 */

namespace Kickpeach\Framework\Foundation\Exceptions;

use Kickpeach\Framework\Foundation\Exceptions\Contracts\ExceptionsHandler;
use ErrorException;
/**
 * Class HandleExceptions
 * @package Kickpeach\Framework\Foundation\Exceptions
 * 框架系统异常错误处理接管类
 */
class HandleExceptions
{
    protected $testing;

    protected $handler;

    //初始化错误接管
    public function __construct(ExceptionsHandler $handler,$testing =false)
    {
        $this->testing = $testing;
        $this->handler = $handler;
    }

    /**
     * 异常接管注册
     */
    public function handle()
    {
        error_reporting(-1);

        //捕获错误
        set_error_handler(array($this,'handleError'));

        //捕获异常
        set_exception_handler(array($this,'handleException'));

        //程序结束
        register_shutdown_function(array($this, 'handleShutdown'), 0);

        if (!$this->testing){
            ini_set('display_errors','Off');
        }

    }

    /**
     * 捕获错误
     * 一般用于捕捉  E_NOTICE 、E_USER_ERROR、E_USER_WARNING、E_USER_NOTICE (trigger_error可以触发)
     * 不能捕捉: E_ERROR、 E_PARSE、 E_CORE_ERROR、 E_CORE_WARNING、 E_COMPILE_ERROR、 E_COMPILE_WARNING
     *          和在 调用 set_error_handler() 函数所在文件中产生的大多数 E_STRICT
     *将错误以异常抛出
     */

    public function handleError($errorno, $errorstr, $errorfile, $errorline)
    {
        if (error_reporting() & $errorno){
            throw new ErrorException($errorstr, 0, $errorno, $errorfile, $errorline);
        }
    }

    //异常处理接管
    public function handleException($exception){
        $this->handler->handle($exception);
    }

    //运行结束或者致命错误捕获
    public function handleShutdown($status = 0){
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])){
            $this->handleException(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line'])
            );
        }
    }

    //判断要捕捉的错误是致命的范围内
    protected function isFatal($type){
        return in_array($type,array(E_ERROR,E_CORE_ERROR,E_COMPILE_ERROR,E_PARSE));
    }

}