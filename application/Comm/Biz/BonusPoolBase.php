<?php

namespace app\comm\Biz;





use think\facade\Log;
use think\facade\Route;

abstract class BonusPoolBase extends \app\Comm\CommMsg
{



    public  $IsReady = false;
    public  $ErrorMsg = '';


    public  $BonusItems = array();

    //区县代理奖金
    public  $RegionAgentBounsItems = array();
    /** @var \app\Models\Client_OrderT $OrderModel 相关订单 */
    public  $OrderModel = null;
    /** @var \app\Models\Client_UserT $OriginUserModel 消费用户 */
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
    public function __construct($user, $orderModel,$orderClass){
        parent::__construct();



//        $this->OrderAmount = $orderAmount;
        $this->OrderModel = $orderModel;
        $this->OrderClass = $orderClass;
        $this -> OriginUserModel = $user;

        $this->OrderId = $this->OrderModel ->Id;

        $this -> OriginUserId = $user ->Id;

        $this -> CacheMng =  \app\Comm\SysSetCacheMng::getInstance();

        $this->_Init();


        $this->LoopGuider( $this-> OriginUserModel -> GuiderUserId );
    }


    protected function _Init(){
         $this -> RootUserId  = $this->CacheMng ->GetSet('RootGuiderUserId');
        if('' != $this -> RootUserId){
            $this -> RootUserId = null;
        }
        if( null == $this -> OrderModel ){
            $this -> OrderModel = \app\Models\Client_OrderT::get($this->OrderId);
        }
        $this  -> OrderAmount = $this -> OrderModel -> PayPrice;

        $this -> BaseAmount =  $this-> OrderAmount  ;




    }

    protected  function GetLayerNum(){
        $this -> LayerNum  = $this -> LayerNum + 1;
        return $this -> LayerNum;
    }

    protected  function SayErrLog($title,$model,$ex){
        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo
        // $current = Route::getRule()->getRule('current'); // 获取当前路由规则（如果有的话）
        $current =  Route:: getCurrentRule();
        Log::error('程序出错：' . $title . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current)  );
        if(null != $model ){
            Log::record('模型数据：\n'  . json_encode($model)   );
        }
        if(null != $ex){
            Log::record('异常信息：\n'  . json_encode($ex)   );
        }
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