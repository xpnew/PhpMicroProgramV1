<?php

namespace app\api\controller;

use app\comm\Biz\BonusMng;
use think\Controller;
use think\Request;
use \app\Models\Client_OrderItemT;
use \app\Models\Client_OrderT;
use \app\Models\Client_BuyCarItemT;
use \app\Models\Product_InfoT;
use \app\Models\Client_UserT;
use \app\Models\Client_BonusLogT;
use \app\utils\GeneralTool;
use \app\Models\Client_UserT as UserDB;


class Order extends ApiBase
{

    /** @var Client_UserT $CurrentUser 当前工作中使用的用户信息 */
    protected  $CurrentUser ;
    /** @var Client_OrderT $CurrentOrder 当前工作中使用的订单 */
    protected  $CurrentOrder;






    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }



    public function query(){ 
        $data =[  ];
        $ClassName = input('ClassName','');
        $UserId = input('UserId',-9999);
        $OrderStatus = input('OrderStatus','');
        $Status = input('Status','');
        $ProductName = input('ProductName','');

        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',10); // 每页显示数量


        if($UserId == -9999){
            return $this->SendJErr('请先登录',-9999);
        }

        $where = [];
        $where[] = ['UserId','=',$UserId];
        if($ClassName != ''){
            $where[] = ['ClassName','like','%'.$ClassName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        if($OrderStatus != ''){
            $where[] = ['OrderStatus','=',$OrderStatus];
        }        
        if($Status != ''){
            $where[] = ['OrderStatus','=',$Status];
        }        
        if($ProductName != ''){
            $where[] = ['ProductName','like','%'.$ProductName.'%'];
        }        
        $db= new \app\Models\Client_OrderT();

        $data = $db -> where($where) 
        -> order(['UpdateTime' => 'desc','Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      

        $dbitems =  new Client_OrderItemT();
        foreach($data as $k => $v){
            $items = $dbitems -> where('OrderId',$v -> Id) -> select();
            $data[$k] -> Items = $items;
        }

        return $this->SendJOk('查询成功',1,$data); 


    }

    public  const IS_NOT_REPURCHASE = 0; // 非复购
    public  const IS_REPURCHASE = 1;     // 是复购
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $Items = \think\facade\Request::param('Items');
        $UserId =  \think\facade\Request::param('UserId');
        $db =  new Client_OrderT();
        $NewOrder = [
            'UserId' => $UserId,
            'OrderNo' => GeneralTool::CreateGuid(),
            'CreateTime' => date('Y-m-d H:i:s'),
            'UpdateTime' => date('Y-m-d H:i:s'),
            'OrderStatus' => 10001000, //10001000 未完成 ;10005000 已经完成  etc.
            'PayStatus' => 20001000, //20001000 未付款;20002000 付款中;20004000 已经取消;20005000 付款成功;20005500 退款;20009000 支付失败
            'DeliveryStatus' => 70001000,//70001000 未发货;70002000 已发货;70004000 已到货;70005000 已签收;70006000 已取消 
            'TotalPrice' => 0,
            'PayPrice' => 0,
            'DeliveryPrice' => 0,
        ];

        
        $prodb = new Product_InfoT();

        //2026年1月19日  新增复购买1赠1
        /** @var int $IsRepurchase 复购的标志 */ $IsRepurchase  =  -1;
        /** @var int $IsPointBuyClass 积分购买分类标志 */ $IsPointBuyClass  =  0;

        $ExistUser =  \app\Models\Client_UserT::get($UserId);
        if(0 < $ExistUser -> MakerLevelId){
            $IsRepurchase  = 1;
        }
        $ItemList =[];
        $TotalPoint = 0;
        $TotalPrice = 0;
        $TotalQty = 0;
        $ClassId4SameJudge  = -1; // 同类别Id检查
        $ProductClass  = null;
        foreach($Items as $it){
            $product = $prodb -> where('Id',$it['ProductId']) -> find();
            if($product == null){
                return $this->SendJErr('商品不存在' . $it['ProductId'] . ' '. $it['ProductName']);
            }
            if(-1  != $ClassId4SameJudge   &&  $product -> ClassId != $ClassId4SameJudge){
                return $this->SendJErr('只允许同类商品一起购买');
            }
            if( null == $ProductClass)
            {
                $ProductClass =  \app\Models\Product_ClassT::get($product -> ClassId);
                if($ProductClass != null && 1 ==  $ProductClass -> EnablePointBuy){
                    $IsPointBuyClass =  1;
                }
            }

            $ClassId4SameJudge = $product -> ClassId;

            //提前处理 有一些字段可能为空的情况。
            SetModel4Names($product,['UnitPrice','SellPoints'],0);

            $Item = new Client_OrderItemT();

            SetModel4Names($Item,['UnitPrice','UnitPoint'],0);


            $Item -> OrderId = 0; // 还没有订单ID
            $Item -> OrderNo = $NewOrder['OrderNo'];
            $Item -> ProductId = $it['ProductId'];
            $Item -> ProductName = $it['ProductName'];
            $Item -> UnitPrice = $it['UnitPrice'];
            if( null == $it['Qty'] || !isset($it['Qty'] ) || !is_int($it['Qty'])  ){
                return $this->SendJErr('商品数量异常（'.$it['ProductId'] . ' '. $it['ProductName']. '）');
            }
            $Item -> Qty = $it['Qty'];
//            FillVariate($it['Qty'],0);
            if(1 ==  $IsPointBuyClass)
                $Item -> UnitPoint  = $product-> SellPoints;
            $Item -> TotalPoint  = $Item -> UnitPoint * $it['Qty'];

            $Item -> TotalPrice = $it['UnitPrice'] * $it['Qty'];

            $Item -> ProductPic = $product-> ProductPic;
            $Item -> Summary = $product-> Summary  .' ClassId: ' . $product-> ClassId;
            $Item -> ProductClassId = $product-> ClassId;
            $Item -> ProductClassName = $ProductClass-> ClassName;
            // $Item -> ProductClassId = $ProductClass-> Id;
            $Item -> LineRmk =  '购买商品：' .  $it['ProductName'] . '' . $it['UnitPrice'] . ' x ' . $it['Qty']   . ' = ' . $Item -> TotalPrice ;
            // $Item -> CreateTime = date('Y-m-d H:i:s');

            if( 1 ==  $IsRepurchase ){
                $Item -> Qty = $it['Qty'] * 2;
            }

            $ItemList[] = $Item;
            $TotalPrice += $Item -> TotalPrice;
            $TotalPoint += $Item -> TotalPoint;
            $TotalQty += $it['Qty'];
            // $NewItem = [
            //     'OrderId' => 0,
            //     'ProductId' => $it['ProductId'],
            //     'ProductName' => $it['ProductName'],
            //     'ProductPrice' =>  $it['UnitPrice'],
            //     'ProductNum' =>  $it['Qty'],
            //     'TotalPrice' => $it['UnitPrice'] * $it['Qty'],
            //     'CreateTime' => date('Y-m-d H:i:s')
            // ];
            
            // $ItemList[] = $NewItem;
            // $TotalPrice += $NewItem['TotalPrice'];
            
        }
        $NewOrder['TotalPrice'] = $TotalPrice;
        $NewOrder['TotalPoint'] = $TotalPoint;
        $NewOrder['PayPrice'] = $TotalPrice;
        $NewOrder['TotalQty'] = $TotalQty;
        $NewOrder['IsRepurchase'] = $IsRepurchase;

        $this -> SayLog('New Order:'.json_encode($NewOrder));
        $this -> SayLog('New Items:'.json_encode($ItemList));

        $db -> save($NewOrder);
        $OrderId = $db -> Id;
        $BuyCarProvider = new Client_BuyCarItemT();
        foreach($ItemList as $it){
            // $it['OrderId']  = $OrderId;
            $it -> OrderId = $OrderId;
            $BuyCarProvider -> where('UserId',$UserId) ->where('ProductId',$it -> ProductId) -> delete();
            $it -> save();
            // $dbitem -> save($it);
        }
        // 批量插入

        $dbitems =  new Client_OrderItemT();
        // $dbitems -> saveAll($ItemList,false);


        return $this->SendJOk('查询成功',1,$Items);
    }


    protected  $IsPreferredZone;
    protected  $CurrentProductClass;

    public function TestPayOrder(){
        $OrderId = \think\facade\Request::param('OrderId',0);
        $UserId =  \think\facade\Request::param('UserId',0);
        
        $ClientName = \think\facade\Request::param('ClientName','');
        $ClientPhone = \think\facade\Request::param('ClientPhone','');
        $ClientAddress = \think\facade\Request::param('ClientAddress','');
        $ClientRegionId = \think\facade\Request::param('ClientRegionId','');
        
        if($OrderId == 0 || $UserId == 0){
            return $this->SendJErr('参数错误');
        }
        $db =  new Client_OrderT();
        $order = $db -> where(['Id'=>$OrderId,'UserId'=>$UserId]) -> find();
        if($order == null){
            return $this->SendJErr('订单不存在');
        }
        $this -> CurrentOrder = $order;

        if($order -> OrderStatus != 10001000){
            return $this->SendJErr('订单状态不正确，不能支付');
        }
        $ExistUser =  Client_UserT::get($UserId );
        $this-> CurrentUser = $ExistUser;

        if(!$ExistUser){
            return $this->SendJErr('用户不存在');
        }
        $dbitems =  Client_OrderItemT:: where('OrderId', $OrderId) -> select();

        $order -> Items = $dbitems;
        if( !isset($this -> CurrentProductClass)){
            $this -> CurrentProductClass = $this -> GetProductClass();
        }
        if( !isset($this -> IsPreferredZone)){
            $this -> IsPreferredZone = $this -> GetIsPreferredZone();
        }


        $ConsumptionPoints  = $this -> BuildConsumptionPoints();

        if($this -> HasError){
            return  $this->SendJMsg();
        }


        $order -> UpdateTime = date('Y-m-d H:i:s');
        // $order -> PayTime = date('Y-m-d H:i:s');
        $order -> ClientName = $ClientName;
        $order -> ClientPhone = $ClientPhone;
        $order -> ClientAddress = $ClientAddress;
        $order -> ClientRegionId = $ClientRegionId;

        // 模拟支付成功
        $order -> PayStatus = 20005000;
        $order -> OrderStatus = 10005000;
        $order -> save();

        if(null != $ConsumptionPoints){
            $ConsumptionPoints -> save();
        }


        $CacheMng =  \app\Comm\SysSetCacheMng::getInstance();


        // $dbuser =  new Client_UserT();
        // $user = $dbuser -> where('Id',$order -> UserId) -> find();
        // if($user == null){
        //     return $this->SendJErr('用户不存在');
        // }





        $ExistUser -> save();

        $InputModel = $this->request->post();






        $InputModel['OrderNo'] =   $order-> OrderNo ;



        $InputModel['ClientRealName'] =  $ExistUser-> RealityName;
        $InputModel['ClientNickName'] =   $ExistUser-> NickName;
        $InputModel['ClientPhone'] =   $ExistUser-> Mobile ;
        $InputModel['ClientUserId'] = $UserId;

        if(! $this -> IsPointBuy  && ! $this -> IsPreferredZone){
            //生成积分
            $PointLog  =  array_values($InputModel);


            $PointLog['CreateTime'] = date('Y-m-d H:i:s');

            $PointLog['Points'] = $order -> PayPrice * $CacheMng -> GetDecimal('ProductPointRatio',100) * 0.01;

            $PointLog['AssetModeId'] =90007000;
            $PointLog['AssetTypeId'] =80007000;
            $PointLog['AssetStatusId'] =81002000;
            $PointLog['ChangePoints'] = $PointLog['Points'];
            $DB4PointLog= new \app\Models\Client_PointLogT();
            $this -> SayLog('积分操作： ' , $PointLog);
            $DB4PointLog->save($PointLog);
        }


        //生成奖金

        $BM  = BonusMng::getInstance();

        $poor =  $BM -> BuildPool4Id($OrderId,$UserId);
        $this -> SayLog('奖金池处理： ' , $poor);

        //生成见单奖


        $AreaMaster  = array_values($InputModel);


        
        $RegionId = $order -> ClientRegionId;
        if( isset($RegionId)){

            //$RegionAgent =  Biz_RegionAgentT::get($RegionId);



        }

        // $AreaMaster['CreateTime'] = date('Y-m-d H:i:s');

        // $AreaMaster['AssetTypeId'] =80001000;
        // $AreaMaster['AssetTypeName'] ='现金';;
        // $AreaMaster['AssetStatusId'] =81002000;
        // $AreaMaster['AssetStatusName'] ='等待&冻结';
        // $AreaMaster['AssetModeId'] =90005000;
        // $AreaMaster['AssetModeName'] ='见单奖';
      

        // $AreaMaster['Bonus'] = $order -> PayPrice * $CacheMng -> GetDecimal('AreaMasterCommissions',2) * 0.01;
        // $AreaMaster['ChangeBonus'] = $AreaMaster['Bonus'];

        // $DB4BonusLog= new \app\Models\Client_BonusLogT();
        // $this -> SayLog('见单奖： ' , $AreaMaster);
        // $DB4BonusLog->save($AreaMaster);










        // 清空购物车
        // $buycar = new Client_BuyCarItemT();
        // $buycar -> where('UserId',$UserId) -> delete();

        
        return $this->SendJErr('支付成功');

    }

    /** 用户发送支付
     * @return \think\response\Json
     */
    public function SendPay(){
        $OrderId = \think\facade\Request::param('OrderId',0);
        $UserId =  \think\facade\Request::param('UserId',0);
        
        $ClientName = \think\facade\Request::param('ClientName','');
        $ClientPhone = \think\facade\Request::param('ClientPhone','');
        $ClientAddress = \think\facade\Request::param('ClientAddress','');
        $ClientRegionId = \think\facade\Request::param('ClientRegionId','');

        if($OrderId == 0 || $UserId == 0){
            return $this->SendJErr('参数错误');
        }
        $db =  new Client_OrderT();
        $order = $db -> where(['Id'=>$OrderId,'UserId'=>$UserId]) -> find();
        if($order == null){
            return $this->SendJErr('订单不存在');
        }
        if($order -> OrderStatus != 10001000){
            return $this->SendJErr('订单状态不正确，不能支付 ' . $order -> PayStatus);
        }
        //    'OrderStatus' => 10001000, //10001000 未完成 ;10005000 已经完成  etc.
        //     'PayStatus' => 20001000, //20001000 未付款;20002000 付款中;20004000 已经取消;20005000 付款成功;20005500 退款;20009000 支付失败
        //     'DeliveryStatus' => 70001000,//70001000 未发货;70002000 已发货;70004000 已到货;70005000 已签收;70006000 已取消         
        // 模拟支付成功
        // $order -> PayStatus = 20005000;
        // $order -> OrderStatus = 10002000;
        $order -> UpdateTime = date('Y-m-d H:i:s');
        // $order -> PayTime = date('Y-m-d H:i:s');
        $order -> ClientName = $ClientName;
        $order -> ClientPhone = $ClientPhone;
        $order -> ClientAddress = $ClientAddress;
        $order -> ClientRegionId = $ClientRegionId;


        $order -> save();

        $dbuser =  new Client_UserT();
        $user = $dbuser -> where('Id',$order -> UserId) -> find();
        if($user == null){
            return $this->SendJErr('用户不存在');
        }
        // $user -> HisMonetary += $order -> TotalPrice;
        // $user -> BuyTimes += 1;
        // if(0 == $user -> IsPromote|| !isset($user -> IsPromote)){
        //     $user -> IsPromote = 1;
        // }
        $user -> save();
        $this -> SayLog('User:'.json_encode($user));


    
    
        // 清空购物车
        // $buycar = new Client_BuyCarItemT();
        // $buycar -> where('UserId',$UserId) -> delete();

        // 统计订单
        //$this -> StatisticsOrder($order,$user);
        return $this->SendJOk('支付成功');

    }


    /**
     * 用户消费积分来支付订单，只是生成数据并没有保存
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

        if(null ==  $user -> PointsBalance  || 0 ==  $user -> PointsBalance){
            $this -> _SetFail('用户没有积分不能支付积分消费的订单');
            return  null;
        }

        if($user -> PointsBalance  < $order -> TotalPoint){
            $this -> _SetFail('用户积分不足，无法完成交易');
            return  null;
        }
        SetModel4Names($user,['PointsHistory', 'PointsBalance','PointsFrozen'],0);



        $ProductNames = array_column($order->Items, 'ProductName');
        $ResultString = implode(',', $ProductNames);
        $Rmk = mb_substr('订单商品：' . $ResultString, 0, 255, 'UTF-8');

        $NewPoint =  new \app\Models\Client_PointLogT();

        $NewPoint -> ClientUserId = $order -> UserId;
        $NewPoint -> ClientRealName = $user -> RealityName;
        $NewPoint -> ClientNickName = $user -> NickName;
        $NewPoint -> ClientPhone = $user -> Mobile;

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
        $NewPoint -> Rmk = $Rmk ;

        $user -> PointsBalance =  $NewPoint -> NewPoints;
        $user -> PointsHistory += $NewPoint -> Points;
        $user -> PointsFrozen += $NewPoint -> Points;
//        $user -> Save();


        return $NewPoint;

    }

    protected function GetIsPreferredZone($orderitem = null){
        $Class =  null;
        if(null == $orderitem -> IsPreferredZone){

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

    public function GetProductClass($orderitem){

        if(null  ==  $orderitem){
            if(null ==  $this -> CurrentOrder  || null ==  $this -> CurrentOrder -> Items || 0 ==  count( $this -> CurrentOrder -> Items)){
                throw new \Exception('数据错误找不到商品分类');
            }
            $orderitem =  $this -> CurrentOrder -> Items[0];
        }

        $ClassId =  $orderitem -> ProductClassId;
        $Class =  \app\Models\Product_ClassT::get($ClassId);
        if($Class == null){
            throw new \Exception('数据错误找不到商品分类');
            return false;
        }

        return   $Class;
    }
    protected  $IsPointBuy =  false;



    protected $MaxGuiderLevel = 2;
    private $Lv1Bonus = 0.1;
    private $Lv2Bonus = 0.05;
    private $Lv2GoldBounus = 0.02;
    private $Lv1Require=1;
    private $Lv2Require=4;
    private $Lv3Require=7;

    // 统计订单
    public function StatisticsOrder($order,$user){ 
        $mng =  \app\comm\SysSetCacheMng::getIns();
        $this -> Lv1Bonus =  (float)$mng -> GetSet('Maker2Commissions');
        $this -> Lv2Bonus =  (float)$mng -> GetSet('Maker3Commissions');
        $this -> Lv2GoldBounus =  (float)$mng -> GetSet('Maker3GoldCommissions');

        $this -> Lv1Require =  (int)$mng -> GetSet('MakerLevel1Require');
        $this -> Lv2Require =  (int)$mng -> GetSet('MakerLevel2Require');
        $this -> Lv3Require =  (int)$mng -> GetSet('MakerLevel3Require');
       



        //$Price =  $order -> TotalPrice;
        $dbitems =  new Client_OrderItemT();
        $Items = $dbitems -> where('OrderId',$order -> Id) -> select();
        $prodb = new Product_InfoT();
        foreach($Items as $it){
            $product = $prodb -> where('Id',$it['ProductId']) -> find();
            if($product == null){
                return $this->SendJErr('商品不存在' . $it['ProductId'] . ' '. $it['ProductName']);
            }
            $product -> BuyCount += 1;
            $product -> save();
            $this -> SayLog('商品:'.json_encode($product));
            // 只有指定分类的商品才计算分成
            if( 1 ==   $product -> ClassId){
                $this -> SayLog('计算分成');
                $this -> StatisticsGuider($user -> GuiderUserId,$it -> TotalPrice,1,$it);
                $this -> SayLog('处理推荐人等级');                
                $this -> StatisticsGuiderLevel($user -> Id,$it -> Qty,0);
            }

        }
        

    }

    // 统计推荐人等级
    private function StatisticsGuiderLevel($userid,$qty,$level){ 
        $this -> SayLog('StatisticsGuiderLevel: ' . $userid . ' qty=' . $qty . ' level=' . $level);

        if(2<= $level  ){
            return;
        }
        $dbuser =  new Client_UserT();
        $cuser = $dbuser -> where('Id',$userid) -> find();
        if($cuser == null){
            return;
        }
        if(0 ==  $level){
            $cuser -> PersonalPerformance += $qty;
        }
        $cuser -> GuiderPerformance += $qty;
        if($cuser->GuiderPerformance >= $this  -> Lv3Require)
            $cuser -> MakerLevel = 3;
        else if($cuser -> GuiderPerformance >= $this  -> Lv2Require)
            $cuser -> MakerLevel = 2;
        else 
            $cuser -> MakerLevel = 1;
    
        $cuser-> save();
        
        $this -> SayLog('StatisticsGuiderLevel User:'.json_encode($cuser));

        $puserid = $cuser -> GuiderUserId;
        if(isset($puserid) == false || $puserid == null || $puserid == ''){
            return;
        }
        $puserid = intval($puserid);
        if($puserid == 0){
            return;
        }
        $this -> StatisticsGuiderLevel($puserid,$qty,$level+1);
        


    }


    protected function StatisticsGuider($guiderUserid,$price,$level,$orderItem){
        if($level > $this->MaxGuiderLevel){
            return;
        }
        if(isset($guiderUserid) == false || $guiderUserid == null || $guiderUserid == ''){
            return;
        }
        $dbuser =  new Client_UserT();
        $puser = $dbuser -> where('GuiderUserId',$guiderUserid) -> find();
        if($puser == null){
            return;
        }
        if( $puser -> IsPromote != 1){
            $this -> StatisticsGuider($puser -> GuiderUserId,$price,$level+1);
            return;
        }
        $this -> SayLog('StatisticsGuiderLevel puser:'.json_encode($puser));
        if( $puser -> MakerLevel < 2){
            $this -> StatisticsGuider($puser -> GuiderUserId,$price,$level+1,$orderItem);
            return;
        }
        // 计算分成
        $rate =$this -> Lv1Bonus; // 10% 1级
        if($level == 2){
            $rate = $this -> Lv2Bonus; // 5% 2级
            if($puser -> MakerLevel > 2){
                $rate = $this -> Lv2GoldBounus; // 2% 2级黄金会员
            }
        }
        $money = $price * $rate;
        if($money < 0.01){
            return;
        }
        $puser -> GuiderBonus += $money;
        $puser -> save();

        // $dblog =  new \app\Models\Client_BonusLogT();

        // 记录日志
        $log = new \app\Models\Client_BonusLogT();
        $log -> UserId = $puser -> Id;
        $log -> UserName = $puser -> NickName;
        $log -> Bonus = $money;
        $log -> OrderId = $orderItem -> OrderId;
        $log -> OrderNo = $orderItem -> OrderNo;
        $log -> OrderItemId = $orderItem -> Id;
        $log -> ProductId = $orderItem -> ProductId;
        $log -> ProductName = $orderItem -> ProductName;
        $log -> Qty = $orderItem -> Qty;
        $log -> TotalPrice = $orderItem -> TotalPrice;


        $log -> Rmk = '用户 ' . $puser -> NickName . ' 下单，分成 ' . ($rate*100) . '% ,金额 ' . $money ;
        $log -> CreateTime = date('Y-m-d H:i:s');
        $log -> save();

        // 继续上级
        $this->StatisticsGuider($puser -> GuiderUserId,$price,$level+1,$orderItem);

    }



    // 确认收货
    public function SendArrival(){
        $OrderId = \think\facade\Request::param('OrderId',0);
        $UserId =  \think\facade\Request::param('UserId',0);
        


        if($OrderId == 0 || $UserId == 0){
            return $this->SendJErr('参数错误');
        }
        $db =  new Client_OrderT();
        $order = $db -> where(['Id'=>$OrderId,'UserId'=>$UserId]) -> find();
        if($order == null){
            return $this->SendJErr('订单不存在');
        }
        if($order -> OrderStatus != 10003000){
            return $this->SendJErr('订单状态不正确，不能 确认收货 ' . $order -> OrderStatus);
        }



        //    'OrderStatus' => 10001000, //10001000 未完成 ;10005000 已经完成  etc.
        //     'PayStatus' => 20001000, //20001000 未付款;20002000 付款中;20004000 已经取消;20005000 付款成功;20005500 退款;20009000 支付失败
        //     'DeliveryStatus' => 70001000,//70001000 未发货;70002000 已发货;70004000 已到货;70005000 已签收;70006000 已取消         
        // 模拟支付成功
        $ExistUser = UserDB::get($UserId);

         if( $this -> _CacheMng -> GetDecimal('MakerLevel3Require',19900) <= $order -> PayPrice){

             if(null ==  $ExistUser -> MakerLevelId || 3  >$ExistUser -> MakerLevelId ) {
                 $ExistUser -> MakerLevelId  =3;
             }

         }else if($this -> _CacheMng-> GetDecimal('MakerLevel2Require',3980) <= $order -> PayPrice){
             if(null ==  $ExistUser -> MakerLevelId || 2  >$ExistUser -> MakerLevelId ) {
                 $ExistUser -> MakerLevelId  =2;
             }

         }else if ($this -> _CacheMng -> GetDecimal('MakerLevel2Require',398) <= $order -> PayPrice){
             if(null ==  $ExistUser -> MakerLevelId ||  0 == $ExistUser -> MakerLevelId ) {
                 $ExistUser -> MakerLevelId  =1;
             }
         }
         $ExistUser -> save();
        $order -> OrderStatus = 10005000;
        $order -> UpdateTime = date('Y-m-d H:i:s');
        $order -> ArrivalTime = date('Y-m-d H:i:s');

        $order -> save();
         $ExistUser -> save();
        // 统计订单
        $this -> StatisticsOrder($order,$ExistUser);
        return $this->SendJOk('确认收货');

    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save ($request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
