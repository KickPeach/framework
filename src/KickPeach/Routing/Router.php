<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/25
 * Time: 9:02
 */

namespace Kickpeach\Framework\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use InvalidArgumentException;
use Kickpeach\Framework\surpport\Helpers\Arr;
use RuntimeException;
use Kickpeach\Framework\Foundation\Exceptions\HttpException;

class Router
{
    protected $cacheFile = false;

    protected $defaultHandler = 'defaultHandler';

    public function __construct($cacheFile)
    {
        if (!is_string($cacheFile) && $cacheFile!=false){
            throw new InvalidArgumentException('Router cacheFile must be string or false');
        }

        if ($cacheFile!=false && !is_writable(dirname($cacheFile))){
            throw new RuntimeException('Router cacheFile directory must be writable');
        }

        $this->cacheFile = $cacheFile;

        $this->dispatch();
    }

    protected $dispatcher;

    protected $routeCollector;

    protected function dispatch()
    {
        $routeInfo = $this->createDispatcher()->dispatch($this->getHttpMethod(),$this->getUri());
        switch ($routeInfo[0]){
            case Dispatcher::NOT_FOUND:
                throw new HttpException('NOT FOUND',404);
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpException('Method Not Allowed');
            case Dispatcher::FOUND:
                break;
            default:
                throw new HttpException('NOT FOUND',404);
        }

        $this->parseRouteInfo($routeInfo[1],$routeInfo[2]);
    }

    protected function createDispatcher()
    {
        if ($this->dispatcher){
            return $this->dispatcher;
        }

        $routeCallBack = function (RouteCollector $r){
            $this->routeCollector = $r;
            $this->getRouteDefinition();
        };

        if ($this->cacheFile){
            $this->dispatcher = \FastRoute\cachedDispatcher($routeCallBack,[
                'cacheFile'=>$this->cacheFile,
            ]);
        }else{
            $this->dispatcher = \FastRoute\simpleDispatcher($routeCallBack);;
        }

        return $this->dispatcher;
    }

    protected function getRouteDefinition()
    {
        $this->any('[{module}[/{controller}[/{action}[/{paths:.+}]]]]', $this->defaultHandler);
    }

    public function any($uri, $action)
    {
        $this->addRoute(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $uri, $action);

        return $this;
    }

    protected function addRoute($method,$uri,$action)
    {
        $uri = '/'.trim($uri,'/');
        if (is_string($action)){
            $action = ['uses'=>$action];
        }

        if (!is_array($action) || empty($action['uses']) || !is_string($action['uses'])){
            throw new \Exception('路由错误'.$uri);
        }

        $this->routeCollector->addRoute($method,$uri,$action);
    }

    public function setDispatcher(Dispatcher $dispatcher){
        $this->dispatcher = $dispatcher;
    }

    public function getHttpMethod()
    {
        return Arr::get($_SERVER,'REQUEST_METHOD','GET');
    }

    public function getUri()
    {
        if (PHP_SAPI=='cli'){
            return '/'.trim(Arr::get($_SERVER['argv'],1,'/'),'/');
        }

        $requestUri = parse_url('http://example.com' . Arr::get($_SERVER, 'REQUEST_URI', ''), PHP_URL_PATH);

        $requestUri = empty($requestUri) ? '/' : $this->filterPath($requestUri);

        return '/' . trim($requestUri, '/');
    }

    protected function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    /**
     * @var array
     * 默认路由
     */
    protected $defRouterVar = [
        'module'=>'home',
        'controller'=>'index',
        'action'=>'index'
    ];
    protected function parseRouteInfo($handler,$var)
    {
        //非tp式解析，走路由文件解析
        if ($this->defaultHandler!==$handler['uses']){
            $this->parseDefinitionRouteInfo($handler,$var);
            return;
        }

        //类似tp式解析，走控制器方法
        $var = array_merge($this->defRouterVar,$var);
        $args = [];

        //解析剩余的url参数
        if (isset($var['paths'])){
            preg_replace_callback('@(\w+)\/([^\/]+)@',function ($matches) use (&$args){
                $args[$matches[1]] = $matches[2];
            },$var['paths']);
        }
        //自定义路径解析
        $this->controller = $this->getControllerName(
            sprintf('%s\\%s',ucfirst($var['module']),ucfirst($var['controller']))
        );

        $this->action = $var['action'];

        $this->attr = $args;


    }

    /**
     * @param $handler
     * @param $var
     * @throws HttpExceptio
     * 解析路由文件中的的普通路由
     */

    protected function parseDefinitionRouteInfo($handler,$var){
        list($controller,$action) = array_pad(explode('@',$handler['uses'],2),2,'');
        $this->controller = $this->getControllerName($controller);

        if (empty($action)){
            throw new HttpException('错误的路由',404);
        }

        $this->action = $action;

        $this->attr = $var;
    }

    protected function getControllerName($controller)
    {
        if (PHP_SAPI=='cli'){ //命令行模式
            $controller = '\\App\\Commands\\'.$controller;
        }else{
            $controller = '\\App\\Controllers\\'.$controller;
        }

        if (!class_exists($controller)){
            throw new HttpException('控制器'.$controller.'不存在',404);
        }

        return $controller;
    }

    protected $controller;

    protected  $action;

    protected $attr;




}