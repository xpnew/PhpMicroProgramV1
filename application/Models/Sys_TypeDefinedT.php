<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Sys_TypeDefinedT extends Model
{
    protected $table = 'Sys_TypeDefinedT';
    protected $pk = 'TypeId';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



// 字段名  类型    是否可为空      主键    默认值  额外信息
// TypeId  int             int(11) NO      PRI
// TypeName        varchar         varchar(100)    YES
// GroupId int             int(11) YES
// GroupOrd        int             int(11) YES
// CodeName        varchar         varchar(100)    YES
// IsShow  tinyint         tinyint(4)      YES
// IsSelector      tinyint         tinyint(4)      YES
// Rmk     varchar         varchar(255)    YES

?>