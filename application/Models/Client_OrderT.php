<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Client_OrderT extends Model
{
    protected $table = 'Client_OrderT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



?>