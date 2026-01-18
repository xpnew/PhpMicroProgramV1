<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Sys_UserT extends Model
{
    protected $table = 'Sys_UserT';
    protected $pk = 'Id';

    //除了ProductId 以外，这个表其它Id都是 BigInt类型
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



?>



