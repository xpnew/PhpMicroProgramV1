<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Biz_RegionAgentT extends Model
{
    protected $table = 'Biz_RegionAgentT';
    protected $pk = 'Id';

    //除了ProductId 以外，这个表其它Id都是 BigInt类型
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容Clinet_PointLogT
    }


}
// 表名                    引擎    版本    行数    创建时间        更新时间        表备注
// Biz_RegionAgentT        InnoDB  10      0       2026-01-10 10:46:40     None    区域代理（暂时是区县代理）
// 字段名  数据类型        说明    类型    是否可为空      主键    默认值  额外信息
// Id      int     Id      int(11) NO      PRI
// ClientUserId    bigint  客户Id  bigint(20)      YES
// ProvinceId      int     省Id    int(11) YES
// CityId  int     市Id    int(11) YES
// CountyId        int     县Id    int(11) YES
// RegionName      varchar 地区名称        varchar(150)    YES
// AgentLevel      int     代理等级：1 省 2 市  3县        int(11) YES
// NickName        varchar 呢称    varchar(120)    YES
// RealityName     varchar 实名    varchar(150)    YES
// Mobile  varchar 手机号  varchar(30)     YES
// CreateTime      datetime        创建时间        datetime        YES
// UpdateTime      datetime        修改时间        datetime        YES
// CommenceTime    datetime        开始时间        datetime        YES
// ExpireStopTime  datetime        过期时间        datetime        YES
// InaugurateTime  datetime        首次合作开始时间        datetime        YES
// Rmk     varchar 备注    varchar(255)    YES


?>

