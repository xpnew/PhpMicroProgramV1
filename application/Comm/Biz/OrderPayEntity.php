<?php

namespace app\Comm\Biz;






use app\Comm\CommMsg;
use app\Models\Client_OrderItemT;
use app\Models\Client_OrderT;
use app\Models\Client_UserT;
use app\Models\Product_ClassT;
use think\App;
use think\facade\Log;

/**
 * 订单支付实体类
 * 包含订单支付完成之后的一系列过程
 */
class OrderPayEntity extends  CommMsg
{

    /** @var Client_UserT $CurrentUser 当前工作中使用的用户信息 */
    protected  $CurrentUser ;
    /** @var Client_OrderT $CurrentOrder 当前工作中使用的订单 */
    protected  $CurrentOrder;


    /** @var string $OrderNow 当前订单使用的时间，可能是CreateTime,也可能是UpdateTime */
    protected  $OrderNow;
    /** @var bool $IsPreferredZone 是否为优选区 */
    protected  $IsPreferredZone;

    /** @var Product_ClassT $CurrentProductClass 当前商品分类 */
    protected  $CurrentProductClass;

    protected  $Input ; // 注意添加 & 按引用 传递


    /** @var int $OrderId 订单Id */
    protected  $OrderId = 0;

    /** @var int $UserId 订单Id */
    protected  $UserId = 0;
    /** @var bool $IsPointBuy 是否为积分购买 */
    protected  $IsPointBuy =  false;


    protected  $request  = null;

    public function __construct($orderId,$userId,$rq )
    {
        parent::__construct();
        $this -> OrderId = $orderId;
        $this -> UserId = $userId;
        $this -> request = $rq;
        $db =  new Client_OrderT();
        $order = $db -> where(['Id'=>$orderId,'UserId'=>$userId]) -> find();
        if($order == null || empty($order) ){
            $this->SetErr('订单不存在');
            return;
        }
        if($order -> OrderStatus != 10001000){
//            $this->SetErr('订单状态不正确，不能支付');
//            return;
        }

        $ExistUser =  Client_UserT::get($userId );
        if(!$ExistUser){
            $this->SetErr('用户不存在');
            return;
        }

        $this -> OrderNow =  date('Y-m-d H:i:s');

        $dbitems =  Client_OrderItemT:: where('OrderId', $orderId) -> select();

        $order -> Items = $dbitems;
        $this-> CurrentUser = $ExistUser;
        $this -> CurrentOrder = $order;
        if( !isset($this -> CurrentProductClass)){
            $this -> CurrentProductClass = $this -> GetProductClass();
        }
        if( !isset($this -> IsPreferredZone)){
            $this -> IsPreferredZone = $this -> GetIsPreferredZone();
        }

        $this -> SetOk('初始化完成');
    }



    public  function Pay($inputData){
        $InputModel = $inputData;
        $this -> Input = &$InputModel; //按引用传递

        $ExistUser = $this -> CurrentUser;

        $InputModel['OrderNo'] =   $this -> CurrentOrder-> OrderNo ;
        $InputModel['OrderId'] =   $this -> CurrentOrder-> Id ;
        $InputModel['TotalPrice'] =   $this -> CurrentOrder-> PayPrice ;
        $InputModel['ClientRealName'] =  $this -> CurrentUser-> RealityName;
        $InputModel['ClientNickName'] =  $this ->  CurrentUser-> NickName;
        $InputModel['ClientPhone'] =  $this ->  CurrentUser-> Mobile ;
        $InputModel['ClientUserId'] = $this -> UserId;
        $InputModel['Rmk'] = $this ->  _BuildOrderRmk();
        $InputModel['CreateTime'] =$this -> OrderNow;
        $order =  & $this -> CurrentOrder;

        $this -> CurrentOrder -> UpdateTime =  $this -> OrderNow;
        $this -> CurrentOrder -> PayTime =  $this -> OrderNow;
        // 模拟支付成功
        $order -> PayStatus = 20005000;
        $order -> OrderStatus = 10002000;

        $ExistUser -> OpenId ='test';
        $this -> SayLog('检测 更新后的用户状态 ', $this->CurrentUser);

        $ConsumptionPoints  = $this -> BuildConsumptionPoints();

        if( 0 >= $this -> StatusCode){
            return;
        }

        if(null != $ConsumptionPoints){
            $ConsumptionPoints -> save();
        }


        $CacheMng =  \app\Comm\SysSetCacheMng::getInstance();

        $order -> save();
        $ExistUser -> save();


        //生成积分
        if(! $this -> IsPointBuy  && ! $this -> IsPreferredZone){
            //生成积分
//            $PointLog  =  [...$InputModel]; //展开运算符 7.4才支持
//            $PointLog = $InputModel;//直接使用 =  克隆，但是语义不明显
            $PointLog = array_merge($InputModel,[]); //展开运算符的替代品
//            $this -> SayLog('日志模型数据 ：', $PointLog);
            //$this->FillData4Log($PointLog);


            $PointLog['Points'] = $order -> PayPrice * $CacheMng -> GetDecimal('ProductPointRatio',100) * 0.01;
            $PointLog['AssetModeId'] =90007000;
            $PointLog['AssetTypeId'] =80007000;
            $PointLog['AssetStatusId'] =81002000;
            $PointLog['AssetModeName'] ='销售积分';
            $PointLog['AssetTypeName'] ='积分';
            $PointLog['AssetStatusName'] ='等待&冻结';

            $PointLog['IsFrozen'] =1;
            $PointLog['ChangePoints'] = $PointLog['Points'];
            $PointLog['Rmk']  .= "  为用户{$this->CurrentUser->Id}({$this->CurrentUser->RealityName})变动积分[{$PointLog['Points']}]";
            $PointLog['Rmk'] =mb_substr ( $PointLog['Rmk'] ,0,255, 'utf-8');


            $DB4PointLog= new \app\Models\Client_PointLogT();
            $this -> SayLog('积分操作： ' , $PointLog);

            $DB4PointLog->save($PointLog);
        }


        //生成奖金

        $BM  = BonusMng::getInstance();

//        InfoLog('准备 生成奖金');
        $poor =  $BM -> BuildPool4Model( $this -> CurrentUser, $this->CurrentOrder,$this -> CurrentProductClass);
        $this -> SayLog('奖金池处理： ' , $poor);
        $poor -> SetRmk( $InputModel['Rmk']);
        $poor -> DistributeBonus();
        $poor -> Save();


        //生成见单奖
        $RegionId = $order -> ClientRegionId;
        if( isset($RegionId) ||  !$this->IsPointBuy){
            $RegionAgent = \app\Models\Biz_RegionAgentT::where ('CountyId' , $RegionId) -> find();
            if(null != $RegionAgent &&  isset($RegionAgent) && ! empty($RegionAgent)  ){

                $AreaMaster  = array_merge($InputModel,[]); //展开运算符的替代品

                $AreaMaster['AssetTypeId'] =80001000;
                $AreaMaster['AssetTypeName'] ='现金';;
                $AreaMaster['AssetStatusId'] =81002000;
                $AreaMaster['AssetStatusName'] ='等待&冻结';
                $AreaMaster['AssetModeId'] =90005000;
                $AreaMaster['AssetModeName'] ='见单奖';
                $AreaMaster['ClientRealName'] =  $RegionAgent-> RealityName;
                $AreaMaster['ClientNickName'] =   $RegionAgent-> NickName;
                $AreaMaster['ClientPhone'] =   $RegionAgent-> Mobile ;
                $AreaMaster['ClientUserId'] = $RegionAgent -> ClientUserId;

                $AreaMaster['Bonus'] = $order -> PayPrice * $CacheMng -> GetDecimal('AreaMasterCommissions',2) * 0.01;
                $AreaMaster['ChangeBonus'] = $AreaMaster['Bonus'];

                $AreaMaster['Rmk']  .= " 为用户{$RegionAgent -> ClientUserId}({$RegionAgent-> RealityName})生成《见单奖》[{$AreaMaster['Bonus']}] ，来源用户：{$this->CurrentUser->Id}({$this->CurrentUser->RealityName})";
                $AreaMaster['Rmk'] =mb_substr ( $AreaMaster['Rmk'] ,0,500, 'utf-8');


                $DB4BonusLog= new \app\Models\Client_BonusLogT();
                $this -> SayLog('见单奖： ' , $AreaMaster);
                $DB4BonusLog->save($AreaMaster);

            }
        }




    }

    public  function SetAddress($name,$phone,$address,$regionId){

            $this -> CurrentOrder -> ClientName = $name;
            $this -> CurrentOrder -> ClientPhone = $phone;
            $this -> CurrentOrder -> ClientAddress = $address;
            $this -> CurrentOrder -> ClientRegionId = $regionId;

    }
    function  _BuildOrderRmk()
    {
        $ProductNames = array_column($this -> CurrentOrder ->Items -> toArray(), 'ProductName');
        $ResultString = implode(',', $ProductNames);
        $Rmk = mb_substr('订单商品：' . $ResultString, 0, 255, 'UTF-8');
        return $Rmk;
    }
    public function GetProductClass(){

        $orderitem =  $this -> CurrentOrder -> Items[0];
        $ClassId =  $orderitem -> ProductClassId;
        $Class =  \app\Models\Product_ClassT::get($ClassId);
        if($Class == null){
            throw new \Exception('数据错误找不到商品分类');
            return false;
        }

        return   $Class;
    }



    /**
     * 用户消费积分来支付订单
     * @param $user
     * @param $order
     * @return \app\Models\Client_PointLogT
     */
    protected  function BuildConsumptionPoints( ){
        $order =  $this -> CurrentOrder;
        $user = $this -> CurrentUser;
        if(0 < $order -> TotalPoint){
            $this -> IsPointBuy = true;
        }
        else{
            $this -> IsPointBuy = false;
            return null;
        }

        if(null ==  $user -> PointsBalance  || 0 ==  $user -> PointsBalance){
            $this -> SetErr('用户没有积分不能支付积分消费的订单');
            return  null;
        }

        if($user -> PointsBalance  < $order -> TotalPoint){
            $this -> SetErr('用户积分不足，无法完成交易');
            return  null;
        }
        SetModel4Names($user,['PointsHistory', 'PointsBalance','PointsFrozen'],0);





        $NewPoint =  new \app\Models\Client_PointLogT();

        $this->FillData4Log($NewPoint);

        $NewPoint -> Qty = $order -> TotalQty;
        $NewPoint -> Points = $order -> TotalPoint;


        $NewPoint -> ChangePoints = $order -> TotalPoint *-1;
        $NewPoint -> OldPoints = $user -> PointsBalance;
        $NewPoint -> NewPoints = $user -> PointsBalance + $NewPoint -> ChangePoints;

        $NewPoint -> AssetModeId =90007500;
        $NewPoint -> AssetModeName = '商城消费积分';
        $NewPoint -> AssetTypeId =80007000;
        $NewPoint -> AssetTypeName = '积分';
        $NewPoint -> AssetStatusId =81002000;
        $NewPoint -> AssetStatusName = '等待&冻结';



        $NewPoint -> CreateTime = date('Y-m-d H:i:s');
        $NewPoint -> IsFrozen = true;
        $NewPoint -> IsSuccess = 1;
        $NewPoint -> Rmk .= " 为用户{$this->CurrentUser->Id}({$this->CurrentUser->RealityName})变动积分[{$NewPoint -> ChangePoints}]";
        $NewPoint -> Rmk =mb_substr ( $NewPoint -> Rmk,0,255, 'utf-8');

        $user -> PointsBalance =  $NewPoint -> NewPoints;
        $user -> PointsHistory += $NewPoint -> Points;
        $user -> PointsFrozen += $NewPoint -> Points;

        return $NewPoint;





    }
    protected  function FillData4Log($log)
    {
//        InfoLog('尝试诊断 日志 的问题 ：' .json_encode ($log)) ;
        //$log  =  array_merge($log,$this -> Input );
        FillArr2Model( $this -> Input,$log);

//        $order =  $this -> CurrentOrder;
//        $user = $this -> CurrentUser;
//        $log -> ClientUserId = $order -> UserId;
//        $log -> ClientRealName = $user -> RealityName;
//        $log -> ClientNickName = $user -> NickName;
//        $log -> ClientPhone = $user -> Mobile;
//        $log -> OrderId = $order -> Id;
//        $log -> OrderNo = $order -> OrderNo;
    }


    protected function GetIsPreferredZone($orderitem = null){
        $Class =  null;
        if(null == $orderitem){

            $Class = $this -> CurrentProductClass;
        }else{
            $ClassId =  $orderitem -> ProductClassId;
            $Class =  \app\Models\Product_ClassT::get($ClassId);
            if($Class == null){
                return false;
            }
        }

        if(40002000 == $Class -> ProductZoneId){
            return true;
        }
        return  false;

    }


    protected function SayLog($title ,$model =  null){
        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo

        Log::record('日志输出：' . $title . ' pathinfo=' . $pathinfo  );
        if(null != $model){
            Log::record('模型数据：\n'  . json_encode($model)   );
        }
    }
}