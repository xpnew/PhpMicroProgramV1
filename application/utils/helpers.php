<?php


// 检查函数是否已存在，防止重复定义报错
if (!function_exists('FillVariate')) {
//    /**
//     * 如果变量未设置，则赋予默认值
//     */
//    function FillVariate(&$var, $default) {
//        if (!isset($var)) {
//            $var = $default;
//        }
//    }

    //前面是简单的版本，我保存下来是为了方便理解这个函数的意义。


// app/support/helpers.php

    /**
     * 通用填充函数：支持变量、数组、ThinkPHP集合
     * @param mixed &$target 目标变量（支持引用）
     * @param mixed $default 默认值
     * @param mixed $key 如果 target 是数组或集合，需要指定键名
     * @return void (直接修改原变量，无返回值)
     */
    function FillVariate(&$target =[], $default, $key = null)
    {

        // 情况1：目标是 ThinkPHP 的 Collection 集合
        if (is_object($target) && $target instanceof \think\model\Collection) {
            // 集合通常需要先转成数组处理，或者使用集合的方法
            // 这里简单处理：如果集合为空，则赋值（通常集合赋值比较特殊，更多是处理集合里的数据）
            if ($target->isEmpty()) {
                if(!is_object($default) || !($default instanceof \think\model\Collection)){
                        throw new \Exception('默认值不对，当$key为空时，需要提供一个集合对象。');  //▲这个“\think\Exception” 是phpstorm代码提示“自动完成”写出来的，我不清楚php有没有默认的Exception
                }
                $target = $default; // 直接替换整个集合
            }else{
                if(null !=  $key){
                    foreach ($target as $entity) {
                        SetObjectProperty($entity, $key, $default);
                    }
                }
            }

        // 情况2：目标是数组
        } elseif (is_array($target)) {
            // 如果传了 $key，说明是给数组的某个键赋值
            if ($key !== null) {
                if (!isset($target[$key])) {
                    $target[$key] = $default;
                }
            } else {
                // 如果没传 $key，且数组为空，则把整个数组替换为默认值
                if (empty($target) &&  is_array($default)) {
                    $target = $default;
                }else{
                    foreach ($target as &$child) {  //▲这种才是我原本准备使用的功能，就是传是 [$Amount, $Point, $Balance]这种数值变量，然后统一给它们补充一个默认值
                        if (!isset($child)) {
                            $child = $default;
                        }
                    }
                    // 注意：使用引用后，最好 unset 一下，防止后续操作污染
                    unset($child);
                }
            }
        // 情况3：目标是普通变量（包括 null, string, int 等）
        } else {
            if (!isset($target)) {
                $target = $default;
            }
        }
    }

}

if (!function_exists('FillVariateList')) {
    /**
     * 填充变量列表 ：
     * @param mixed $target 目标数组，必须是传入参数是带引用符号的
     * @param mixed $default 默认值
     * @return void (直接修改原变量，无返回值)
     */
    function FillVariateList($target, $default ){
        foreach ($target as $k => $v) {
            if(!isset($target[$k])){
                $target[$k] = $default;
            }
        }
    }
}


if (!function_exists('SetObjectProperty')) {
    /**
     * 为对象设置属性值（如果属性不存在或值为空）
     * @param object &$obj 对象引用
     * @param string $propertyName 属性名
     * @param mixed $default 默认值
     * @return void
     */
    function SetObjectProperty(&$obj, $propertyName, $default)
    {

        // 检查对象是否存在该属性（包括 public, private, protected）
        // property_exists 检查的是“定义”，isset 检查的是“是否有值(null不算)”
        if (property_exists($obj, $propertyName)) {
            // 属性已定义，但要看它有没有值
            if (!isset($obj->$propertyName)) {
                $obj->$propertyName = $default;
            }
        } else {
            // 属性从未定义过，直接创建并赋值（PHP 允许动态添加属性）
            $obj->$propertyName = $default;
        }
    }
}
if (!function_exists('SetModel4Names')) {
    /**
     * 为对象设置属性值（如果属性不存在或值为空）
     * @param object &$obj 对象引用
     * @param array $nameArr 属性名列表
     * @param mixed $default 默认值
     * @return void
     */
    function SetModel4Names(object &$obj,array $nameArr,$default)
    {
        foreach ($nameArr as $k => $propertyName) {

            // 检查对象是否存在该属性（包括 public, private, protected）
            // property_exists 检查的是“定义”，isset 检查的是“是否有值(null不算)”
            if (property_exists($obj, $propertyName)) {
                // 属性已定义，但要看它有没有值
                if (!isset($obj->$propertyName)) {
                    $obj->$propertyName = $default;
                }
            } else {
                // 属性从未定义过，直接创建并赋值（PHP 允许动态添加属性）
                $obj->$propertyName = $default;
            }
        }
    }
}

?>
