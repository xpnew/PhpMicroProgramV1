<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Biz_PayBillT extends Model
{
    protected $table = 'Biz_PayBillT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



?>