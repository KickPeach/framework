<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2019/1/2
 * Time: 14:37
 */

namespace Kickpeach\Framework\surpport\Helpers;
use Kickpeach\Framework\surpport\Helpers\File;

class Logger
{
    protected static $instance;

    public function __construct()
    {
    }

    public static function __callStatic($name, $arguments)
    {
        if (empty(static::$instance)){
            static::$instance = new static();
        }

        switch (count($arguments)){
            case 1:
                return static::$instance->write($name,$arguments[0]);
            default:
                throw new \Exception('参数数量错误');
        }

    }

    public function write($level,$content)
    {
        $filename = $this->getLogFile($level);
        File::write($filename,$content);

        return $this;
    }

    public function getLogFile($level)
    {
        $baseLogPath = $this->getStoragePath().'/logs/';

        $wrapper = (PHP_SAPI =='cli')?'cli':'web';
        $logPath = $baseLogPath.$wrapper;

        $logPath = $this->getLogPath($logPath);

        return $logPath.$level.'.log';
    }

    protected function getLogPath($dir)
    {
        return $dir.'/'.date('Ym').'/';
    }

    protected function getStoragePath()
    {
        return '/tmp';
    }



}