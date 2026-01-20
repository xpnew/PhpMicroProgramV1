<?php
namespace app\Models;

use think\Model;




/**
 * @package App\Models
 * @table Client_OrderT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property integer $Id 主键Id
 * @property string $OrderNo 订单号
 * @property string $OrderCode 订单编码
 * @property float $TotalQty 总数量
 * @property float $Weight 总重量
 * @property integer $UserId 用户Id
 * @property float $UnitPrice 单价
 * @property float $TotalPrice 总价
 * @property float $PayPrice 实付金额
 * @property float $DeliveryPrice 运费
 * @property float $TotalPoint 总积分
 * @property string $CreateTime 创建时间
 * @property string $UpdateTime 更新时间
 * @property string $Remark 备注
 * @property string $Comment 附注、说明
 * @property string $AuditUser 审核人
 * @property string $AuditTime 审核时间
 * @property integer $AuditStatusId 审核状态
 * @property string $Temp1 
 * @property string $Temp2 
 * @property integer $OrderStatus 10001000 未完成 ;10005000 已经完成  etc.
 * @property integer $PayStatus 20001000 未付款;20002000 付款中;20004000 已经取消;20005000 付款成功;20005500 退款;20009000 支付失败
 * @property string $ClientAddress 客户地址
 * @property string $ClientPhone 客户电话
 * @property string $ClientName 客户姓名
 * @property integer $ClientRegionId 用户邮编
 * @property string $TrackingNumber 运单号
 * @property string $ShippingAddress 发货地址
 * @property string $ShippingName 发货姓名
 * @property string $PayTime 付款时间
 * @property string $ShipingTime 发货时间
 * @property string $ArrivalTime 到货时间
 * @property integer $EnableBuildBonus 允许获取奖励
 * @property integer $EnableBuildPoint 允许生成积分/银果
 * @property integer $EnablePointBuy 允许积分/银果购买
 * @property integer $IsRepurchase 是否为复购(VIP以上会员)
 */
class Client_OrderT extends Model
{
    protected $table = 'Client_OrderT';
    protected $pk = 'Id';
    /** @var array $$Items 订单明细子项*/
    public  $Items =[];
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



?>