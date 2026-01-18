<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Client_BonusLogT extends Model
{
    protected $table = 'Client_BonusLogT';
    protected $pk = 'Id';

    //除了ProductId 以外，这个表其它Id都是 BigInt类型
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}

// 字段名  类型    是否可为空      主键    默认值  额外信息
// Id      bigint  主键    bigint(11)      NO      PRI
// UserId  bigint  用户Id  bigint(11)      YES
// UserName        varchar 用户名  varchar(120)    YES
// Bonus   decimal 奖金    decimal(18,4)   YES
// TotalPrice      decimal 价格    decimal(18,4)   YES
// OrderItemId     bigint  来源id  bigint(20)      YES
// OrderId int     订单号  int(11) YES
// OrderNo varchar 订单号  varchar(64)     YES
// CreateTime      datetime        创建时间        datetime        YES
// Qty     decimal 数量    decimal(18,4)   YES
// ProductId       int     商品Id  int(11) YES
// ProductName     varchar 商品名称        varchar(255)    YES
// Rmk     varchar 备注    varchar(255)    YES
// BonusTypeId     int     奖金类型        int(11) YES
// BonusTypeName   varchar 奖金类型名称    varchar(100)    YES
// BonusStatusId   int     奖励状态        int(11) YES
// BonusStatusName varchar 奖励状态名称    varchar(100)    YES
// UpdateTime      datetime        更新时间        datetime        YES

?>


