<?php

namespace app\Comm\Framework;


///模仿 thinkphp实现 同时支持属性访问和数组访问
class BaseArrayAccess implements \ArrayAccess
{
    private $container = array();
    public function __construct() {
        $this->container = array();
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->container[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    // 百度千问还给出了下面的代码 。
    // 可选：通过 __get/__set 兼容 -> 访问
//    public function __get($name) { return $this->container[$name] ?? null; }
//    public function __set($name, $value) { $this->container[$name] = $value; }
}