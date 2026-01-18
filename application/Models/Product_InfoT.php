<?php
namespace app\Models;

use think\Model;


/**
 * @package App\Models
 * @table Product_InfoT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property integer $Id Id
 * @property integer $ClassId 分类Id
 * @property string $ProductName 商品名称
 * @property string $ProductCode 商品编码 
 * @property string $ProductCot 商品介绍
 * @property string $Summary 商品摘要
 * @property string $ProductPic 商品标题图
 * @property string $CreateTime 创建时间
 * @property string $UpdateTime 修改时间
 * @property string $ReleaseTime 发布时间
 * @property integer $Tops 推荐，由大到小排列
 * @property integer $Hits 点击数
 * @property float $NormalPrice 标准价
 * @property float $DiscountPrice 优惠价
 * @property integer $BuyCount 购买次数
 * @property string $Tags 标签
 * @property float $SellPoints 销售积分
 * @property float $DirectGuiderRatio 直推奖比例
 * @property float $IndirectGuiderRatio 间推奖比例
 */
class Product_InfoT extends Model
{
    protected $table = 'Product_InfoT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        // echo 'Product_InfoT init';  
        //TODO:初始化内容
    }

}

?>