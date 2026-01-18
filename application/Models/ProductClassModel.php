<?php
namespace app\Models;

use think\Model;

class ProductClassModel extends Model
{
    protected $table = 'Product_ClassT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}

// class Product_ClassT extends Model
// {
//     protected $table = 'Product_ClassT';
//     protected $pk = 'Id';
     
//     // 模型初始化
//     protected static function init()
//     {
//         //TODO:初始化内容
//     }

// }

?>