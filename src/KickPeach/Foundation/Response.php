<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/12/27
 * Time: 8:36
 */

namespace Kickpeach\Framework\Foundation;

/**
 * Class Response
 * @package Kickpeach\Framework\Foundation
 * 只处理content实体，方便中间件传递对象而不是具体的响应内容
 * * 返回内容 如果 需要特殊处理，则包装为 对象实现 __toString 方法即可
 * 如 json 类型的 response 写一个 实现了  __toString 的类，里边json_encode 同时输出 header 为json 即可
 *
 * !!! 如果返回的是响应是流的话直接 content 为null就行了，
 */
class Response
{
    protected $content;

    public function __construct($content='')
    {
        $this->setContent($content);
    }

    public function setContent($content)
    {
        if ($content!==null && !is_string($content) && !is_numeric($content) && is_callable(array($content,'__toString'))){
            throw new \Exception(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.',gettype($content)));
        }

        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function send()
    {
        echo (string) $this->content;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->content;
    }

}