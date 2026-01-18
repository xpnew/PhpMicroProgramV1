<?php
namespace app\Models;

use think\Model;





/**
 * @package App\Models
 * @table Product_ClassT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property string $ClassName 分类名称
 * @property integer $Id Id
 * @property integer $SortId 排序（小-大）
 * @property string $ClassTitPic01 分类标题图
 * @property integer $EnableBuildBonus 允许获取奖励
 * @property integer $EnablePointBuy 允许积分购买
 * @property integer $EnableBuildPoint 允许生成积分
 * @property integer $IsMarkerLvlBonus 是否级组奖（报单区）
 * @property integer $ProductZoneId 商品分区 ：ProductZoneDef枚举
 */
class Product_ClassT extends Model
{
    /**
     * @var string
     */
    protected $table = 'Product_ClassT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



?>