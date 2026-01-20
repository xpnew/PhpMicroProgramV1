<?php
namespace app\Models;

use think\Model;




/**
 * @package App\Models
 * @table Client_OrderItemT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property integer $Id 主键
 * @property integer $OrderId 订单Id
 * @property string $OrderNo 订单号
 * @property integer $SourceItemId 来源Id
 * @property integer $ProductId 商品Id
 * @property string $ProductName 商品名称
 * @property string $ProductCode 商品代码
 * @property string $ProductPic 商品图片
 * @property string $Summary 商品摘要
 * @property float $Qty 数量
 * @property float $Weight 重量
 * @property string $LineRmk 行备注
 * @property integer $IsLock 锁定
 * @property float $UnitPrice 单价
 * @property float $TotalPrice 总价
 * @property float $UnitPoint 单品积分
 * @property float $TotalPoint 总积分
 * @property integer $ProductClassId 商品分类Id
 * @property string $ProductClassName 商品分类名称
 * @property integer $ClassId2 
 */
class Client_OrderItemT extends Model
{

    protected $table = 'Client_OrderItemT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



?>