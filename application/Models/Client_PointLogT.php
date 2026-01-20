<?php
namespace app\Models;

use think\Model;




/**
 * 客户奖金记录表
 * @package App\Models
 * @table Client_PointLogT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property integer $Id 主键
 * @property integer $ClientUserId 用户Id
 * @property string $ClientRealName 客户姓名
 * @property string $ClientNickName 客户呢称
 * @property string $ClientPhone 客户电话
 * @property float $Points 积分（原始数据）
 * @property float $TotalPrice 价格
 * @property integer $OrderItemId 来源id
 * @property integer $OrderId 订单号
 * @property string $OrderNo 订单号
 * @property string $CreateTime 创建时间
 * @property float $Qty 数量
 * @property integer $ProductId 商品Id
 * @property string $ProductName 商品名称
 * @property string $Rmk 备注
 * @property integer $AssetTypeId 资产类型
 * @property string $AssetTypeName 资产类型
 * @property integer $AssetStatusId 资产状态
 * @property string $AssetStatusName 资产状态
 * @property bool $IsSuccess 是否已经成功
 * @property string $UpdateTime 更新时间
 * @property integer $AssetModeId 资产模式
 * @property string $AssetModeName 资产模式
 * @property bool $IsFrozen 是否处于冻结状态
 * @property float $OldPoints 原来的积分
 * @property float $ChangePoints 变动积分 可正可负
 * @property float $NewPoints 新的积分 （不显示）调度代码用的
 */
class Client_PointLogT extends Model
{
    protected $table = 'Client_PointLogT';
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
// Point   decimal 积分    decimal(18,4)   YES
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
// IsSuccess       bit     是否已经成功    bit(1)  YES
// UpdateTime      datetime        更新时间        datetime        YES 

?>