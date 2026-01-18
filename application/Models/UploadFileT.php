<?php
namespace app\Models;

use think\Model;


class UploadFileT extends Model
{
    protected $table = 'UploadFileT';
    // protected $table = 'Sys_User';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
        // echo 'UploadFile2 init ';
    }

}

//字段名  类型    是否可为空      主键    默认值  额外信息
//Id      bigint          bigint(20)      NO      PRI
//GId     varchar         varchar(64)     YES
//SourceName      varchar         varchar(150)    YES
//SaveName        varchar         varchar(150)    YES
//ExtName char            char(10)        YES
//MimeType        varchar         varchar(150)    YES
//ClientTypeName  varchar         varchar(150)    YES
//ClientTypeId    int             int(11) YES
//FullSavePath    varchar         varchar(250)    YES
//SaveRootDir     varchar         varchar(150)    YES
//SaveSubDir      varchar         varchar(150)    YES
//Remark  varchar         varchar(300)    YES
//VisualPath      varchar         varchar(250)    YES
//VisualDir       varchar         varchar(200)    YES
//CreateTime      datetime                datetime        YES
//ClientGId       longtext                longtext        YES
//ActId   int             int(11) YES
//ClientWorkName  varchar         varchar(150)    YES
//IsUsed  int             int(11) YES
//UpdateTime      datetime                datetime        YES
//FileSize        int             int(11) YES

?>

