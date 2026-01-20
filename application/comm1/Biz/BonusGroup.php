<?php
namespace app\comm\Biz;



class BonusGroup{

    public  $OrderId ;

    public  $Name ;

    public  $GroupIdx = 0;
    public  $GroupBaseAmount; //组内计算基数

    public  $MarketingLevel; //级组


    public  $IsSkip =  false;

    public  $BonusDict  = [];

    public  $LevelId;
    protected  $OrderModel ;

    public  $RealtimeLevelDifference = 0.0;
    public function __construct( $levelId, $order)
    {
        $this->LevelId = $levelId;
        $this-> OrderModel = $order;

        $this-> MarketingLevel  =  \app\Models\Biz_MarketingLevelT::get($levelId)  ;





    }

//    public function AddUser($user)  {
//        $this -> UserList[] = $user;
//        $NewBonus =  BonusMng::  getIns() -> MkBonus($user, $this -> OrderModel);
//
//        $this -> BonusDict[$user->Id] = $NewBonus;
//
//
//    }

    public  function AddItem($bonusItem){
        $bonusItem -> GroupIdx = $this -> _GetGroupIdx();
        $this -> BonusDict[$bonusItem -> UserId] = $bonusItem;

    }

    protected  function  _GetGroupIdx()
    {
        $this -> GroupIdx =  $this -> GroupIdx +1;
        return $this -> GroupIdx;
    }

}





?>