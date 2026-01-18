<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Client_AddressT extends Model
{
    protected $table = 'Client_AddressT';
    protected $pk = 'Id';

    //除了ProductId 以外，这个表其它Id都是 BigInt类型
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容Clinet_PointLogT
    }


}
// 表名                    引擎    版本    行数    创建时间        更新时间        表备注
// Client_AddressT InnoDB  10      0       2026-01-10 06:33:55     1 客户地址列表
// 字段名  数据类型        说明    类型    是否可为空      主键    默认值  额外信息
// Id      bigint  主键Id  bigint(20)      NO      PRI
// UserId  int     用户Id  int(11) YES
// CreateTime      datetime        创建时间        datetime        YES
// UpdateTime      datetime        更新时间        datetime        YES
// ClientAddress   varchar 客户地址        varchar(255)    YES
// ClientPhone     varchar 客户电话        varchar(120)    YES
// ClientName      varchar 客户姓名        varchar(150)    YES
// ClientRegionId  int     用户邮编        int(11) YES
// IsDefautAddress bit     是否默认地址    bit(11) YES
// Remark  varchar 备注    varchar(255)    YES

?>

