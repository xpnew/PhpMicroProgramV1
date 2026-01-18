<?php

namespace app\comm\Biz;

use app\comm\Biz\BonusPoolBase;

class BonusPool4Guider extends BonusPoolBase
{

    public $BonusDict =[];
    public function __construct($orderId,$userId, $orderModel, $orderClass){

        $this->OrderId = $orderId;



        parent::__construct($orderId,$userId,$orderModel,$orderClass);

    }
    protected function _Init(){  //初始化
        parent :: _Init();

    }

    protected function _BuildBonus(){





    }

    protected  function  LoopGuider($userId){

            $NewUser =  \app\Models\Client_User_View::get($userId);
            if(null == $NewUser){
                return;
            }
            $NewBonusItem =  new BonusItem();
            $NewBonusItem -> PoolMode =2;

            $NewBonusLog =  BonusMng::getIns() -> MkBonusLog($NewUser, $this->OrderModel);
            $NewBonusItem -> UserModel =  $NewUser;
            $NewBonusItem -> BonusLogModel = $NewBonusLog;
            $NewBonusItem -> GuiderLayerNum =  $this -> GetLayerNum();
            $NewBonusItem -> UserId = $userId;

            $this -> BonusDict[$NewUser -> Id] = $NewBonusItem;

            $this -> GuilerModelArr[] = $NewUser;

            $this -> LoopStep ++;

            if( $this -> LoopStep >=  $this -> LoopMax){
                return;
            }

            $GuiderId = $NewUser -> GuiderUserId;

            if(null == $GuiderId || $GuiderId == $this->RootUserId){
                return;
            }
            $this->LoopGuider($GuiderId);
    }


    public function GetAllBonusItems ()
    {
// --- 核心逻辑：提取所有 BonusModel ---

// 1. 提取外层：把每个组里的 BonusDict 拿出来
// array_column 提取所有 'BonusDict' 的值，结果是一个二维数组
        $allBonusModels = array_column($this-> BonusDict, 'BonusModel');


// --- 输出结果 ---
        //echo "【提取出的所有 BonusModel】\n";
//        print_r($allBonusModels);

        return $allBonusModels;
    }

    ///计算奖金、分配奖金
    public  function DistributeBonus()
    {
        $i = 0;
        foreach ($this ->BonusDict as $key => $value) {
//            echo "Key: $key, Value: ";
            // 处理$value
            $Level = $i +1;
            $Ratio = $this -> CacheMng -> GetDecimal('GuiderBonusLevel'.$Level.'Ratio',10.0);

            $Ratio = floatval($Ratio) * 0.01;
            $BonusAmount = $this -> BaseAmount * $Ratio ;
            $BonusLog =  $value -> BonusLogModel ;
            $BonusLog -> Bonus = $BonusAmount;
            $BonusLog -> ChangeBonus = $BonusAmount;
            $BonusLog -> AssetTypeId =  80001000;  //资产类型
            $BonusLog -> AssetTypeName = '现金';
            $BonusLog -> AssetStatusId = 81002000;//资产状态
            $BonusLog -> AssetStatusName = '等待&冻结';


            if( 0 == $i){
                $BonusLog -> AssetModeId = 90003000; //资产模式
                $BonusLog -> AssetModeName = '直推奖';
                $this-> BonusItems[] = $BonusLog;
            }else if (1 == $i){
                $BonusLog -> AssetModeId = 90004000; //资产模式
                $BonusLog -> AssetModeName = '间推奖';
                $this-> BonusItems[] = $BonusLog;
            }else{
                $BonusLog -> AssetModeId = 90004000; //资产模式
                $BonusLog -> AssetModeName = '间推奖 ** (错误数据)';
            }

            $i++;
        }


    }

    public function Save(){
//        $lst =  $this -> GetAllBonusItems();
        $lst =  $this -> BonusItems;
        foreach($lst as $it){
            // $it['OrderId']  = $OrderId;

            $it -> save();
            // $dbitem -> save($it);
        }


    }



}