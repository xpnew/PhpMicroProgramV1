<?php

namespace app\comm\Biz;
///用来保存奖金处理过程当中的数据
class BonusItem{

    public  $UserId;
    public  $OrderId;



    ///奖池处理方式
    public  $PoolMode= 0;  //  1 为 级组  2 推荐奖

    public  $UserModel;
    public  $BonusLogModel;


    public  $GuiderLayerNum ;  //推荐人层级 从1开始

    public  $GroupIdx ;  //组内顺序 从0开始

    public $BounsTypeId = 80002000; // 奖励类型 80002000 金果

}
?>