<?php
namespace app\Models;

use think\Model;


/**
 * VIEW
 * @package App\Models
 * @table Product_InfoV
 * @see Product_InfoT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property string $ClassName 分类名称
 * @property string $ClassTitPic01 分类标题图
 * @property integer $EnableBuildBonus 允许获取奖励
 * @property integer $EnablePointBuy 允许积分购买
 * @property integer $EnableBuildPoint 允许生成积分
 * @property integer $ProductZoneId 商品分区 ：ProductZoneDef枚举
 * @property string $ProductZoneName 
 */
class Product_InfoV extends Product_InfoT{
    protected $table = 'Product_InfoV';
}

?>