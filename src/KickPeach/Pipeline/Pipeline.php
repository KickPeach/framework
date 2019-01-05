<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/27
 * Time: 8:57
 */

namespace Kickpeach\Framework\Pipeline;

use Closure;
use Kickpeach\Framework\Contracts\MiddlewareProvider;

class Pipeline
{
    protected $middlewareProvider;

    protected $passable;

    protected $pipes =[];

    protected $method = 'handle';

    public function __construct(MiddlewareProvider $middlewareProvider)
    {
        $this->middlewareProvider = $middlewareProvider;

    }

    public function send($passable){
        $this->passable = $passable;
        return $this;
    }

    public function through($pipes)
    {
        $this->pipes = is_array($pipes)?$pipes:func_get_args();
        return $this;
    }

    public function then(Closure $destination)
    {
        $firstSlice = $this->getInitalSlice($destination);

        $callable = array_reduce(array_reverse($this->pipes),$this->getSlice(),$firstSlice);

        return $callable($this->passable);
    }

    public function getInitalSlice(Closure $destination)
    {
        return function ($passable) use($destination){
            return $destination($passable);
        };
    }

    protected function getSlice()
    {
        return function ($stack,$pipe){
            return function ($passable)use($stack,$pipe){
                //匿名函数
                if ($pipe instanceof Closure){
                    return $pipe($passable,$stack);
                //字符串
                }elseif (!is_object($pipe)){
                    list($name,$parameters) = $this->parsePipeString($pipe);
                    $pipe = $this->middlewareProvider->getMiddleware($name);
                    $parameters = array_merge([$passable,$stack],$parameters);
                //对象实例
                }else{
                    $parameters = [$passable,$stack];
                }
                return $pipe->{$this->method}(...$parameters);
            };
        };
    }

    //解析字符串，获得名字和数据
    protected function parsePipeString($pipe){
        list($name,$parmeters) = array_pad(explode(':',$pipe,2),2,[]);
        if (is_string($parmeters)){
            $parmeters = explode(',',$parmeters);
        }
        return [$name,$parmeters];
    }
}