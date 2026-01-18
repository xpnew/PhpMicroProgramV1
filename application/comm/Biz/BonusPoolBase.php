<?php

namespace app\comm\Biz;





abstract class BonusPoolBase extends \app\Comm\CommMsg
{



    public  $IsReady = false;
    public  $ErrorMsg = '';


    public  $BonusItems = array();

    //区县代理奖金
    public  $RegionAgentBounsItems = array();

    public  $OrderModel = null;

    public  $OriginUserModel = null ;

    public  $OrderId;
    public  $OriginUserId;


    public $OrderAmount; //订单金额
    public  $BaseAmount; //基准金额
    public  $RepeatPurchaseRatio  = 100.0; //复购结算比例

    public  $OrderClass;

    protected  $CacheMng;

    protected  $BuyTimes = 0;


    //顶级用户id 向上追溯的极限
    protected  $RootUserId;



    protected  $GuilerModelArr = array();

    protected  $LayerNum = 0;

    protected  $LoopMax =  2;
    protected  $LoopStep = 0;
    public function __construct($orderId,$userId, $orderModel,$orderClass){
        parent::__construct();

        if( null ==  $orderId &&  null ==  $userId ){
            return false;
        }
        $this->OrderId = $orderId;

        $this -> OriginUserId = $userId;
//        $this->OrderAmount = $orderAmount;
        $this->OrderModel = $orderModel;
        $this->OrderClass = $orderClass;


        $this -> CacheMng =  \app\Comm\SysSetCacheMng::getInstance();

        $this->_Init();


        $this->LoopGuider( $this-> OriginUserModel -> GuiderUserId );
    }


    protected function _Init(){
         $this -> RootUserId  = $this->CacheMng ->GetSet('');
        if('' != $this -> RootUserId){
            $this -> RootUserId = null;
        }
        if( null == $this -> OrderModel ){
            $this -> OrderModel = \app\Models\Client_OrderT::get($this->OrderId);
        }
        $this  -> OrderAmount = $this -> OrderModel -> PayPrice;
        $this -> OriginUserModel =  \app\Models\Client_User_View::get($this -> OriginUserId);
        /// 用户确认收货时才生产 BuyTimes ，所以，即便是已经修改了用户的状态，这时也不会影响对新购买用户的判断
        ///首次购买 按照 100% 计算
        if( null !=  $this -> OriginUserModel -> BuyTimes &&  0 < $this -> OriginUserModel -> BuyTimes){

            $Ratio =  $this -> CacheMng -> GetSet('RepeatPurchaseRatio');
            if(null == $Ratio  || !isset($Ratio)  || '' == $Ratio  ){

                $Ratio =  '70';
            }
            $Ratio = floatval($Ratio) * 0.01;
            $this -> RepeatPurchaseRatio =  $Ratio ;
            $this -> BaseAmount =  $this-> OrderAmount * $Ratio ;

        }else{
            $this -> BaseAmount =  $this-> OrderAmount  ;
        }

    }

    protected  function GetLayerNum(){
        $this -> LayerNum  = $this -> LayerNum + 1;
        return $this -> LayerNum;
    }

    abstract protected function  LoopGuider($GuiderId);

    ///获取全部的奖金项
    abstract public function  GetAllBonusItems();


    //计算奖金
    abstract public function  DistributeBonus();
    //计算奖金
    abstract public function  Save();



    public  static  function CreateEmptyPool(){

        return new EmptyPool(null,null,null,null  );
    }




}

class EmptyPool extends BonusPoolBase{


    protected function LoopGuider($GuiderId)
    {
        // TODO: Implement LoopGuider() method.
    }

    public function GetAllBonusItems()
    {
        // TODO: Implement GetAllBonusItems() method.
    }

    public function DistributeBonus()
    {
        // TODO: Implement DistributeBonus() method.
    }

    public function Save()
    {
        // TODO: Implement Save() method.
    }
}