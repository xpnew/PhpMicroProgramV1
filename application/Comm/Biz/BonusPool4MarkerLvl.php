<?php

namespace app\Comm\Biz;



///推广级组的奖金池计算机
class BonusPool4MarkerLvl extends  BonusPoolBase
{



    public  $GroupDict =[];


    public function __construct($user, $orderModel, $orderClass){




        parent::__construct($user,$orderModel,$orderClass,false);

        $this -> LoopMax =  99999999;


    }
    protected function _Init(){  //初始化
        parent :: _Init();

        /// 用户确认收货时才生产 BuyTimes ，所以，即便是已经修改了用户的状态，这时也不会影响对新购买用户的判断
        ///首次购买 按照 100% 计算
        //if( null !=  $this -> OriginUserModel -> BuyTimes &&  0 < $this -> OriginUserModel -> BuyTimes){

        //vip 用户会被认为是 复购
        if( null != $this  -> OriginUserModel -> MakerLevelId  && 0 < $this  -> OriginUserModel -> MakerLevelId){
            $Ratio =  $this -> CacheMng -> GetDecimal('RepeatPurchaseRatio',70);
            $this -> RepeatPurchaseRatio =  $Ratio ;
            $this -> BaseAmount =  $this-> OrderAmount * $Ratio  * 0.01;
        }


    }


    protected  function PushUser($user){
//        $this -> GuiderModelArr[] = $user;

        $NewBonusItem =  new BonusItem();
        $NewBonusItem -> PoolMode =1;

        $NewBonusLog =  BonusMng::getIns() -> MkBonusLog($user, $this->OrderModel);
        $NewBonusItem -> UserModel =  $user;
        $NewBonusItem -> BonusLogModel = $NewBonusLog;
        $NewBonusItem -> GuiderLayerNum =  $this -> GetLayerNum();
        $NewBonusItem -> UserId = $user -> Id;


//        $this -> LayerNum ++;


        if (array_key_exists($user -> MakerLevelId, $this-> GroupDict)) {
            $g  =   $this -> GroupDict[$user -> MakerLevelId];
            $g -> AddItem($NewBonusItem);
        } else {
            $g  =  new BonusGroup($user -> MakerLevelId, $this-> OrderModel );
            $g -> AddItem($NewBonusItem);
            $this -> GroupDict[$user -> MakerLevelId] = $g;
        }
    }
    protected function LoopGuider($userId){
        $GuiderUser =  \app\Models\Client_User_View::get($userId);
        if(null == $GuiderUser){
            return;
        }

        $this ->PushUser($GuiderUser);
        $GuiderId = $GuiderUser -> GuiderUserId;

        if(null == $GuiderId || $GuiderId == $this->RootUserId){
            return;
        }
        $this->LoopGuider($GuiderId);
    }


    ///计算奖金、分配奖金
    public  function DistributeBonus()
    {

        $TotalLevelRatio = 0;
        $PeerAwardTotalShare =  $this -> CacheMng -> GetDecimal('PeerAwardTotalShare',45);
        $PeerAwardTotalShare  = $PeerAwardTotalShare * 0.01;

        $this -> AddLog('$PeerAwardTotalShare ' . $PeerAwardTotalShare);
        //先处理 级差 和分组基数
        foreach ($this ->GroupDict as $key => $value) {

            $Group =  $value;
            $LevelSet =  $Group -> MarketingLevel;
            if(null == $LevelSet){
                $Group -> IsSkip =true;
                continue;
            }
            $LevelDifference =  $LevelSet -> LevelDifference;
            $a =  $LevelDifference - $TotalLevelRatio;
            $Group -> RealtimeLevelDifference = $a * 0.01;
            if( 0 < $a){
                $TotalLevelRatio +=   $LevelDifference;
                $Group -> GroupBaseAmount =  $Group -> RealtimeLevelDifference  * $this -> BaseAmount ;
                $this -> AddLog('RealtimeLevelDifference ' . $Group -> RealtimeLevelDifference);
                $this -> AddLog('BaseAmount ' . $this -> BaseAmount);
                $this -> AddLog('GroupBaseAmount ' . $Group -> GroupBaseAmount);
            }else{
                $Group -> GroupBaseAmount = 0;
                $Group -> IsSkip =true;
            }
        }

        //现金奖励最大值
        $CashMax = 2;
        //已经发放现金奖励的数量
        $CashCount = 0;

        //开始 计算组内的 奖金分配
        foreach ($this ->GroupDict as $key => $value) {
            $Group =  $value;
            if ($Group -> IsSkip) {
                continue;
            }
            //普通创客要求连续层级才会有效

            $LevelSet =  $Group -> MarketingLevel;
            $IsNeedSequential =  $LevelSet -> IsNeedSequential;
            $IsStart =  false;
            $LastItem = null;
            $GroupCanDistributeMax =  $LevelSet -> PeerAwardMax;
            $CanDist = [];
            foreach ($Group -> BonusDict as $key2 => $value2) {
                $CurrentItem = $value2;
                if( $IsNeedSequential &&  $IsStart ){ //创客必是连续的才会有第二个人获奖
                    if(1  <  ($CurrentItem -> GuiderLayerNum  - $LastItem -> GuiderLayerNum  )){
                        break;
                    }else{
                        if( $CashCount <  $CashMax){
                            $CurrentItem -> BounsTypeId = 80001000;
                            $CashCount ++;
                        }
                    }
                }
                if( $CashCount <  $CashMax){
                    if(!$IsStart){
                        $CurrentItem -> BounsTypeId = 80001000;
                        $CashCount ++;
                    }
                }

                $CanDist[] = $CurrentItem;

                if( $GroupCanDistributeMax <  count($CanDist)){ // 每一级都有一个级差奖，所以还要额外多一个 ，所以 没有<=
                    break;
                }
                $LastItem  =  $value2;
                $IsStart = true;
            }
            //防止除零错误
//            if(1 >=   count($CanDist)){
//                continue;
//            }
            //平级奖要扣除 每组当中第一个、级差奖
            $PeerNum = count($CanDist) -1;
            $PeerAmount = 0;
            if(0< $PeerNum){
                $PeerAmount =  $Group -> GroupBaseAmount  * $PeerAwardTotalShare/  $PeerNum;
            }
            foreach($CanDist as $idx  => $it){
                $BonusLog =  $it -> BonusLogModel ;
                $BonusLog -> AssetStatusId = 81002000;//资产状态
                $BonusLog -> AssetStatusName = '等待&冻结';
                $BonusLog -> Rmk = $this ->Rmk;
                $BonusLog -> Rmk .= " ，来源用户：{$this->OriginUserModel->Id}({$this->OriginUserModel->RealityName})";
                $BonusLog -> Rmk =mb_substr ( $BonusLog -> Rmk,0,500, 'utf-8');
                if( 80001000  ==  $it-> BounsTypeId){
                    $BonusLog -> AssetTypeId =  80001000;  //资产类型
                    $BonusLog -> AssetTypeName = '现金';
                }else{
                    $BonusLog -> AssetTypeId =  80002000;  //资产类型
                    $BonusLog -> AssetTypeName = '金果';
                }
                if(0 == $idx){
                    $BonusLog -> AssetModeId = 90001000; //资产模式
                    $BonusLog -> AssetModeName = '级差奖';
                    $BonusLog -> Bonus =  $Group -> GroupBaseAmount ;
                    $BonusLog -> ChangeBonus =  $Group -> GroupBaseAmount ;
                }else{
                    if(0 ==  $PeerNum)
                        continue;
                    $BonusLog -> AssetModeId = 90002000; //资产模式
                    $BonusLog -> AssetModeName = '平级奖';
                    $BonusLog -> Bonus = $PeerAmount;
                    $BonusLog -> ChangeBonus = $PeerAmount;
                }
                $this-> BonusItems[] = $BonusLog;
            }

        }

    }


    public  function GetAllBonusItems ()
    {
// 1. 提取外层：把每个组里的 BonusDict 拿出来
// array_column 提取所有 'BonusDict' 的值，结果是一个二维数组
        $bonusDicts = array_column($this->GroupDict, 'BonusDict');

// 2. 提取内层并合并：
// $bonusDicts 现在是二维的 [[...], [...]]
// 使用 ... 将其展开传给 array_merge，把所有子数组合并成一个大数组
// 此时我们得到的是 [1000=>对象, 2000=>对象, 3000=>对象]
        $allBonusItems = array_merge(...$bonusDicts);

// 3. 最终提取：从合并后的数组中，提取每一项的 BonusModel 属性
// array_map 遍历每个对象，取出 ->BonusModel
        $allBonusModels = array_map(function($item) {
            return $item->BonusModel;
        }, $allBonusItems);


        return $allBonusModels;

    }

    public function  Save()
    {
        $lst =  $this -> BonusItems;
        foreach($lst as $it){
            // $it['OrderId']  = $OrderId;

            $it -> save();
            // $dbitem -> save($it);
        }

    }

}