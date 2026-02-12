<?php

namespace app\api\controller;

use app\Comm\Biz\BonusMng;
use app\Comm\Biz\OrderPayEntity;
use think\Controller;
use think\Request;
use \app\Models\Client_OrderItemT;
use \app\Models\Client_OrderT;
use \app\Models\Client_BuyCarItemT;
use \app\Models\Product_InfoT;

use \app\Models\Client_BonusLogT;
use \app\utils\GeneralTool;


use \app\Models\Client_UserT as UserDB;

use \app\Models\Product_InfoV as ProductInfoV;


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
        foreach($data as $line){
            $items = $dbitems -> where('OrderId',$line -> Id) -> select();
            $this -> SayLog('订单子项集合：',$items,null);
            $line -> Items = $items;
            $this -> SayLog('订单数据：',$line,null);
        }
        $this -> SayLog('生成的订单数据：',$data,null);
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
            'OrderNo' => GeneralTool::GetBillNo(),
            'CreateTime' => date('Y-m-d H:i:s'),
            'UpdateTime' => date('Y-m-d H:i:s'),
            'OrderStatus' => 10001000, //10001000 未完成 ;10005000 已经完成  etc.
            'PayStatus' => 20001000, //20001000 未付款;20002000 付款中;20004000 已经取消;20005000 付款成功;20005500 退款;20009000 支付失败
            'DeliveryStatus' => 70001000,//70001000 未发货;70002000 已发货;70004000 已到货;70005000 已签收;70006000 已取消 
            'TotalPrice' => 0,
            'PayPrice' => 0,
            'DeliveryPrice' => 0,
        ];

        
        $prodb = new ProductInfoV();

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
        $ProductZoneId4SameJudge  = -1; // 同类别Id检查
        $ProductClass  = null;
        $ProductZoneId = -1;

        foreach($Items as $it){
            $product = $prodb -> where('Id',$it['ProductId']) -> find();
            if($product == null){
                return $this->SendJErr('商品不存在' . $it['ProductId'] . ' '. $it['ProductName']);
            }

            if(-1  != $ProductZoneId  ){
                if( $product -> ProductZoneId != $ProductZoneId){
                    return $this->SendJErr('只允许相同分区的商品一起购买');
                }
            }else{
                $ProductZoneId = $product -> ProductZoneId;
                if(40006000 ==  $ProductZoneId){
                    $this->IsPointBuy = 1;
                }
            }
//            if( null == $ProductClass)
//            {
//                $ProductClass =  \app\Models\Product_ClassT::get($product -> ClassId);
//                if($ProductClass != null && 1 ==  $ProductClass -> EnablePointBuy){
//                    $IsPointBuyClass =  1;
//                }
//
//            }

            $ProductClass =  \app\Models\Product_ClassT::get($product -> ClassId);

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
        $ProductZoneName = '';
        $ZoneTypeDef =  \app\Models\Sys_TypeDefinedT::get($ProductZoneId);
        if(null != $ZoneTypeDef){
            $ProductZoneName = $ZoneTypeDef -> TypeName;
        }
        $NewOrder['TotalPrice'] = $TotalPrice;
        $NewOrder['TotalPoint'] = $TotalPoint;
        $NewOrder['PayPrice'] = $TotalPrice;
        $NewOrder['TotalQty'] = $TotalQty;
        $NewOrder['ProductZoneId'] = $ProductZoneId;
        $NewOrder['ProductZoneName'] = $ProductZoneName;
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



    protected  $Input ; // 注意添加 & 按引用 传递

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
        $op =  new  OrderPayEntity($OrderId,$UserId , $this ->request);

        $op -> SetAddress($ClientName,$ClientPhone,$ClientAddress,$ClientRegionId);

        if(0>= $op -> StatusCode){
            return $this->SendJErr($op -> Title);
        }

        $InputModel = $this->request->post();
        $op -> Pay($InputModel);


        if(0>= $op -> StatusCode){
            return $this->SendJErr($op -> Title);
        }

//        return $this->SendJErr('测试：拦截处理，提前返回',-1, $ExistUser);

        return $this->SendJOk('支付成功');

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

        $dbuser =  new UserDB();
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





    function  _BuildOrderRmk()
    {
        $ProductNames = array_column($this -> CurrentOrder ->Items -> toArray(), 'ProductName');
        $ResultString = implode(',', $ProductNames);
        $Rmk = mb_substr('订单商品：' . $ResultString, 0, 255, 'UTF-8');
        return $Rmk;
    }







    protected $MaxGuiderLevel = 2;
    private $Lv1Bonus = 0.1;
    private $Lv2Bonus = 0.05;
    private $Lv2GoldBounus = 0.02;
    private $Lv1Require=1;
    private $Lv2Require=4;
    private $Lv3Require=7;

    // 统计订单
    public function StatisticsOrder($order,$user){ 
        $mng =  \app\Comm\SysSetCacheMng::getIns();
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
        $dbuser =  new UserDB();
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
        $dbuser =  new UserDB();
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





    /** 订单退款
     * @return \think\response\Json|void
     */
    public  function Refund(){
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
        if($order -> OrderStatus != 10004000){
            return $this->SendJErr('订单状态不正确，不能 确认收货 ' . $order -> OrderStatus);
        }



        //    'OrderStatus' => 10001000, //10001000 未完成 ;10005000 已经完成  etc.
        //     'PayStatus' => 20001000, //20001000 未付款;20002000 付款中;20004000 已经取消;20005000 付款成功;20005500 退款;20009000 支付失败
        //     'DeliveryStatus' => 70001000,//70001000 未发货;70002000 已发货;70004000 已到货;70005000 已签收;70006000 已取消
        // 模拟支付成功
        $ExistUser = UserDB::get($UserId);




        $this -> OrderNow =  date('Y-m-d H:i:s');
        $order -> OrderStatus = 10007000;
        $order -> UpdateTime =$this -> OrderNow ;
//        $order -> ArrivalTime = date('Y-m-d H:i:s');

        $order -> Remark  .= "用户已经于[{$this -> OrderNow}]进行了退款";
        $order -> Remark =mb_substr ( $order -> Remark  ,-1000,1000, 'utf-8');
        $order -> Comment  .= "用户已经于[{$this -> OrderNow}]进行了退款";
        $order -> Comment =mb_substr ( $order -> Remark  ,0,1000, 'utf-8');
        $this -> CurrentOrder = $order;
        $this -> CurrentUser = $ExistUser;

        $order -> save();
        $ExistUser -> save();
        $this -> RefundOrderLogs();
        return $this->SendJOk('订单退款');


    }


    /** 处理订单退款相关 奖金、积分的记录
     * @return void
     */
    protected  function RefundOrderLogs(){
        $OrderId =  $this -> CurrentOrder -> Id;
        $Now =$this -> OrderNow;
        //处理现金
        $LstCashBonus = Client_BonusLogT:: where([
            'OrderId'  => $OrderId,
            'AssetTypeId' => 80001000,
            'AssetStatusId' => 81005000,
        ]) -> select();
        $BonusLogDb =  new Client_BonusLogT();

        foreach ($LstCashBonus as $CashBonusLog){
            $NewLog =   $CashBonusLog -> toArray();

            $NewLog['Id'] = null;
            $NewLog['UpdateTime'] = null;
            $NewLog['CreateTime'] = $this -> OrderNow ; // 确保创建时间是当前时间
            $NewLog['AssetModeId'] = 90006000;

            $BonusUser =  Client_UserT::get($CashBonusLog -> ClientUserId );

            SetModel4Names($BonusUser,['BonusBalance', 'BonusHistory','BonusFrozen'],0);

            $OldBonus = $BonusUser -> BonusBalance;
            $ChangeBonus =  $CashBonusLog -> Bonus;
            $ChangeBonus =  abs($ChangeBonus ) * -1;
            $NewBonus =  $OldBonus + $ChangeBonus;

            $NewLog['OldBonus']  =  $OldBonus;
            $NewLog['ChangeBonus']  =  $ChangeBonus;
            $NewLog['NewBonus']  =  $NewBonus;


            $BonusUser -> BonusBalance = $NewBonus;
            $BonusUser -> Save();

            $BonusLogDb -> save($NewLog);

            $CashBonusLog -> Rmk  .= "用户已经于[{$this -> OrderNow }]进行了退款";
            $CashBonusLog -> Rmk =mb_substr ( $CashBonusLog -> Rmk  ,-255,255, 'utf-8');
            $CashBonusLog -> AssetStatusId =  81006000;
            $CashBonusLog -> AssetStatusName =  '退款';

            $CashBonusLog -> UpdateTime =  $this -> OrderNow;
            $CashBonusLog -> save();
        }
        //处理金果
        $LstScore = Client_BonusLogT:: where([
            'OrderId'  => $OrderId,
            'AssetTypeId' => 80002000,
            'AssetStatusId' => 81005000,
        ]) -> select();
        $BonusLogDb =  new Client_BonusLogT();

        foreach ($LstScore as $ScoreBonusLog){
            $NewLog =   $ScoreBonusLog -> toArray();

            $NewLog['Id'] = null;
            $NewLog['UpdateTime'] = null;
            $NewLog['CreateTime'] = $this -> OrderNow ; // 确保创建时间是当前时间
            $NewLog['AssetModeId'] = 90006000;

            $ScoreUser =  Client_UserT::get($ScoreBonusLog -> ClientUserId );

            SetModel4Names($ScoreUser,['ScoreHistory', 'ScoreBalance','ScoreFrozen'],0);

            $OldBonus = $ScoreUser -> ScoreBalance;
            $ChangeBonus =  $ScoreBonusLog -> Bonus;
            $ChangeBonus =  abs($ChangeBonus ) * -1;
            $NewBonus =  $OldBonus + $ChangeBonus;

            $NewLog['OldBonus']  =  $OldBonus;
            $NewLog['ChangeBonus']  =  $ChangeBonus;
            $NewLog['NewBonus']  =  $NewBonus;

            $ScoreUser -> ScoreBalance = $NewBonus;
            $ScoreUser -> Save();

            $BonusLogDb -> save($NewLog);

            $ScoreBonusLog -> Rmk  .= "用户已经于[{$this -> OrderNow }]进行了退款";
            $ScoreBonusLog -> Rmk =mb_substr ( $ScoreBonusLog -> Rmk  ,-255,255, 'utf-8');
            $ScoreBonusLog -> AssetStatusId =  81006000;
            $ScoreBonusLog -> AssetStatusName =  '退款';

            $ScoreBonusLog -> UpdateTime =  $this -> OrderNow;
            $ScoreBonusLog -> save();
        }

        //处理积分
        $LstPoint =  \app\Models\Client_PointLogT:: where([
            'OrderId'  => $OrderId,
            'AssetTypeId' => 80007000,
            'AssetStatusId' => 81005000,
        ]) -> select();
        $PointLogDb =  new Client_PointLogT();

        foreach ($LstPoint as $PointLog){
            $NewLog =   $PointLog -> toArray();

            $NewLog['Id'] = null;
            $NewLog['UpdateTime'] = null;
            $NewLog['CreateTime'] = $this -> OrderNow ; // 确保创建时间是当前时间
            $NewLog['AssetModeId'] = 90006000;

            $PointUser =  Client_UserT::get($PointLog -> ClientUserId );

            SetModel4Names($PointUser,['PointsHistory', 'PointsBalance','PointsFrozen'],0);

            $OldPoints = $PointUser -> PointsBalance;
            $ChangePoints =  $PointLog -> Points;
            $ChangePoints =  abs($ChangePoints ) * -1;
            $NewPoints =  $OldPoints + $ChangePoints;

            $NewLog['OldPoints']  =  $OldPoints;
            $NewLog['ChangePoints']  =  $ChangePoints;
            $NewLog['NewPoints']  =  $NewPoints;


            $PointUser -> PointsBalance = $NewPoints;
            $PointUser -> Save();

            $PointLogDb -> save($NewLog);

            $PointLog -> Rmk  .= "用户已经于[{$this -> OrderNow }]进行了退款";
            $PointLog -> Rmk =mb_substr ( $PointLog -> Rmk  ,-255,255, 'utf-8');
            $PointLog -> AssetStatusId =  81006000;
            $PointLog -> AssetStatusName =  '退款';

            $PointLog -> UpdateTime =  $this -> OrderNow;
            $PointLog -> save();
        }




    }


    /** 订单签收（送达）
     * @return \think\response\Json
     */
    public  function Delivered(){

        $OrderId = \think\facade\Request::param('OrderId',0);
        $UserId =  \think\facade\Request::param('UserId',0);

        $this -> OrderNow =  date('Y-m-d H:i:s');

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



        $order -> OrderStatus = 10004000;
        $order -> UpdateTime = $this -> OrderNow ;
        $order -> ArrivalTime = $this -> OrderNow ;

        $order -> Remark  .= "用户已经于[{$this -> OrderNow}]进行了签收";
        $order -> Remark =mb_substr ( $order -> Remark  ,-1000,1000, 'utf-8');
        $order -> Comment  .= "用户已经于[{$this -> OrderNow}]进行了签收";
        $order -> Comment =mb_substr ( $order -> Remark  ,0,1000, 'utf-8');

        $this -> CurrentOrder = $order;
        $this -> CurrentUser = $ExistUser;

        $order -> save();
        $ExistUser -> save();
        $this -> DeliveredOrderLogs();

        return $this->SendJOk('确认收货');

    }
    /** 处理订单签收（送达）相关 奖金、积分的记录
     * @return void
     */
    protected  function DeliveredOrderLogs(){
        $OrderId =  $this -> CurrentOrder -> Id;
        $Now =$this -> OrderNow;
        //处理现金
        $LstCashBonus = Client_BonusLogT:: where([
            'OrderId'  => $OrderId,
            'AssetTypeId' => 80001000,
            'AssetStatusId' => 81002000,
        ]) -> select();


        foreach ($LstCashBonus as $CashBonusLog){

            $BonusUser =  UserDB::get($CashBonusLog -> ClientUserId );

            SetModel4Names($BonusUser,['BonusBalance', 'BonusHistory','BonusFrozen'],0);

            $OldBonus = $BonusUser -> BonusBalance;
            $ChangeBonus =  $CashBonusLog -> Bonus;
            $NewBonus =  $OldBonus + $ChangeBonus;

            $CashBonusLog['OldBonus']  =  $OldBonus;
            $CashBonusLog['ChangeBonus']  =  $ChangeBonus;
            $CashBonusLog['NewBonus']  =  $NewBonus;


            $BonusUser -> BonusBalance = $NewBonus;
            $BonusUser -> Save();



            $CashBonusLog -> Rmk  .= "用户已经于[{$this -> OrderNow }]进行了签收";
            $CashBonusLog -> Rmk =mb_substr ( $CashBonusLog -> Rmk  ,-255,255, 'utf-8');
            $CashBonusLog -> AssetStatusId =  81005000;
            $CashBonusLog -> AssetStatusName =  '成功';

            $CashBonusLog -> UpdateTime =  $this -> OrderNow;
            $CashBonusLog -> save();
        }
        //处理金果
        $LstScore = Client_BonusLogT:: where([
            'OrderId'  => $OrderId,
            'AssetTypeId' => 80002000,
            'AssetStatusId' => 81002000,
        ]) -> select();

        foreach ($LstScore as $ScoreBonusLog){

            $ScoreUser =  UserDB::get($ScoreBonusLog -> ClientUserId );

            SetModel4Names($ScoreUser,['ScoreHistory', 'ScoreBalance','ScoreFrozen'],0);

            $OldBonus = $ScoreUser -> ScoreBalance;
            $ChangeBonus =  $ScoreBonusLog -> Bonus;
            $NewBonus =  $OldBonus + $ChangeBonus;


            $ScoreBonusLog['OldBonus']  =  $OldBonus;
            $ScoreBonusLog['ChangeBonus']  =  $ChangeBonus;
            $ScoreBonusLog['NewBonus']  =  $NewBonus;

            $ScoreUser -> ScoreBalance = $NewBonus;
            $ScoreUser -> Save();


            $ScoreBonusLog -> Rmk  .= "用户已经于[{$this -> OrderNow }]进行了签收";
            $ScoreBonusLog -> Rmk =mb_substr ( $ScoreBonusLog -> Rmk  ,-255,255, 'utf-8');
            $ScoreBonusLog -> AssetStatusId =  81005000;
            $ScoreBonusLog -> AssetStatusName =  '成功';

            $ScoreBonusLog -> UpdateTime =  $this -> OrderNow;
            $ScoreBonusLog -> save();
        }

        //处理积分
        $LstPoint =  \app\Models\Client_PointLogT:: where([
            'OrderId'  => $OrderId,
            'AssetTypeId' => 80007000,
            'AssetStatusId' => 81002000,
        ]) -> select();


        foreach ($LstPoint as $PointLog){

            $PointUser =  UserDB::get($PointLog -> ClientUserId );

            SetModel4Names($PointUser,['PointsHistory', 'PointsBalance','PointsFrozen'],0);

            $OldPoint = $PointUser -> PointsBalance;
            $ChangePoint =  $PointLog -> Points;
            $NewPoint =  $OldPoint + $ChangePoint;

            $PointLog['OldPoints']  =  $OldPoint;
            $PointLog['ChangePoints']  =  $ChangePoint;
            $PointLog['NewPoints']  =  $NewPoint;


            $PointUser -> PointsBalance = $NewPoint;
            $PointUser -> Save();

            $PointLog -> Rmk  .= "用户已经于[{$this -> OrderNow }]进行了签收";
            $PointLog -> Rmk =mb_substr ( $PointLog -> Rmk  ,-255,255, 'utf-8');
            $PointLog -> AssetStatusId =  81005000;
            $PointLog -> AssetStatusName =  '成功';

            $PointLog -> UpdateTime =  $this -> OrderNow;
            $PointLog -> save();
        }




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
