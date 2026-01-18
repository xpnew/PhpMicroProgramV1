<?php
namespace app\Models;

use think\Model;

class ProductInfo extends Model
{
    protected $table = 'Product_InfoT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
        // echo 'ProductInfo init';  
    }

}



?>