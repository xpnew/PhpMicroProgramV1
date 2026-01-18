<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Area_CountyT extends Model
{
    protected $table = 'Area_CountyT';
    protected $pk = 'Id';

    //除了ProductId 以外，这个表其它Id都是 BigInt类型
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容Clinet_PointLogT
    }

}

// 表名                    引擎    版本    行数    创建时间        更新时间        表备注
// Area_CountyT    InnoDB  10      3144    2026-01-10 06:15:37     None
// 字段名  数据类型        说明    类型    是否可为空      主键    默认值  额外信息
// county_id       int             int(10) NO      PRI
// county_name     varchar 县区名称        varchar(50)     NO
// city_id int     父地区ID        int(10) NO



?>

