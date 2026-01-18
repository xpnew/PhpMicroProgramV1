<?php
namespace app\Comm;


/**
 * 查询 结果消息
 * 查询返回消息 封装  layer table  数据格式
 * 
 * 
 * 
 */
class QueryMsg{
    public $code;  //0成功  1失败
    public $msg;   //提示信息
    public $count; //数据总数
    public $data;  //数据

    function __construct($code=0,$msg='',$count=0,$data=array()){
        $this->code=$code;
        $this->msg=$msg;
        $this->count=$count;
        $this->data=$data;
    }
}