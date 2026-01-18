<?php
namespace app\admin\model;

use think\Model;

class Sys_User extends Model
{
    protected $table = 'sys_user';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}

?>