<?php


namespace app\comm\Biz;


class BonusMng{

    private  function  __construct() {

    }
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    // 如果需要获取实例的便捷方法，可以添加如下方法
    public static function getMy() {
        return self::getInstance();
    }
    public static function getIns() {
        return self::getInstance();
    }



    public  function  BuildPool($orderId,$userId){
        $ResultPool   =  BonusPoolBase::CreateEmptyPool();
        $Order  =  \app\Models\Client_OrderT::get($orderId);

        $FirstItem  =  \app\Models\Client_OrderItemT::where('OrderId',$orderId)->findOrFail();

        if(! $FirstItem){
            $ResultPool-> SetErr('订单项不存在');
            return $ResultPool;
        }
//        var_dump($FirstItem)  ;
        //PHPStorm 自动添加 了 ProductClassId  属性，造成赋值 失败
        $ProductClass =  \app\Models\Product_ClassT::get($FirstItem -> ProductClassId);

        if( ! $ProductClass){
            $ResultPool-> SetErr('商品分类：不存在' );
            return $ResultPool;
        }
//        var_dump($ProductClass)  ;
        if(1 == $ProductClass -> EnablePointBuy){
            $ResultPool-> SetErr('积分不计算奖金');
            // 积分不计算奖金
            return $ResultPool;
       }

        if(0 == $ProductClass -> EnableBuildBonus){
            $ResultPool-> SetErr('分类设置为允许生成奖励才能进行结算');
            // 分类设置为允许生成奖励才能进行结算
            return $ResultPool;
        }


// 检查框架是否重写了属性
//        var_dump($ProductClass->getAttributes()); // Laravel Eloquent方法
//        var_dump($ProductClass->toArray()); // 检查实际存储的属性

// 检查PHPStorm生成的代码
//        var_dump(get_class_vars(get_class($ProductClass))); // 查看类的所有属性



        if(1 ==  $ProductClass -> IsMarkerLvlBonus ){
            $ResultPool = new BonusPool4MarkerLvl($orderId,$userId,$Order,$ProductClass);
//            echo  'erorr branch 1  $ProductClass -> IsMarkerLvlBonus '  ;
//            // 检查框架是否重写了属性
//            var_dump($ProductClass->getAttributes()); // Laravel Eloquent方法
//            var_dump($ProductClass->toArray()); // 检查实际存储的属性

        }else{
            $ResultPool = new BonusPool4Guider($orderId,$userId,$Order,$ProductClass);

//            echo  'erorr branch 2  $ProductClass -> IsMarkerLvlBonus '  ;


        }

        $ResultPool -> DistributeBonus();
        $ResultPool -> Save();

        return $ResultPool;






    }


    public  function  MkBonusLog($user,$order){
        $NewBonus = new \app\Models\Client_BonusLogT();
        $NewBonus -> CreateTime =  new \DateTime();
        $NewBonus -> ClientUserId =  $user -> Id;
        $NewBonus -> ClientNickName =  $user -> NickName;
        $NewBonus -> ClientRealName =  $user -> RealityName;
        $NewBonus -> ClientPhone =  $user -> Mobile;
        $NewBonus -> OrderId = $order-> Id;
        $NewBonus -> OrderNo =  $order-> OrderNo;

        $NewBonus -> TotalPrice = $order-> PayPrice;

        return $NewBonus;
    }



}
