<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/24
 * Time: 14:15
 */

namespace Kickpeach\Framework\Foundation\Exceptions;

use Kickpeach\Framework\Foundation\Exceptions\Contracts\ExceptionsHandler;
use Kickpeach\Framework\surpport\Helpers\Exceptions\Reporter\Debugger;
use Kickpeach\Framework\surpport\Helpers\Arr;

class Handler implements ExceptionsHandler
{
    protected $collapseDir;
    protected $cfVersion;

    public function __construct($collapseDir='',$cfVersion='KickPeach/1.0')
    {
        $this->collapseDir = $collapseDir;
        $this->cfVersion = $cfVersion;
    }

    //异常接管
    public function handle($e){
        restore_error_handler();
        restore_exception_handler();

        $this->report($e);
        $this->render($e);
    }

    protected function report($e){
        // todo 错误日志记录
    }

    protected function render($e){
        if(php_sapi_name()=='cli'){
            $this->renderForConsole($e);
        }else{
            $this->renderWebException($e);
        }
    }

    /**
     * 命令行模式
     */
    protected function renderForconsole($e){
        echo (string) $e;
    }

    protected function renderWebException($e){
        (new Debugger($this->collapseDir, $this->cfVersion))->displayException($e);
    }

    /**
     * @param $e
     * 获取记录日志用的异常字符串
     */
    protected function getLogOfException($e){
        $request_uri = (php_sapi_name() == 'cli') ? Arr::get($GLOBALS['argv'], 1, '/') : $_SERVER['REQUEST_URI'];
        return sprintf(
            "[%s %s] %s\n%s\n",
            date('Y-m-d H:i:s'),
            date_default_timezone_get(),
            $request_uri,
            (string) $e
        );
    }
}