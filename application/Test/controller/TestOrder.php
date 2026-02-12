<?php
namespace app\Test\controller;
use app\Comm\Biz\BonusMng;
use app\Comm\Biz\OrderPayEntity;
use app\Models\Client_OrderT;
use app\Models\Client_UserT;

use app\Models\Client_UserT as UserDB;
use think\Controller;
use \app\Models\Client_OrderItemT;
use app\Comm\CommControllerBase;
use \app\Models\Client_BonusLogT;
use \app\Models\Client_PointLogT;



class TestOrder extends CommControllerBase
{ 




    public function index(){
        return 'test order';
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
            $items =  $items -> toArray();
            $line -> Items = $items;
            $this -> SayLog('订单数据：',$line,null);
        }
        $this -> SayLog('生成的订单数据：',$data,null);
        return $this->SendJOk('查询成功',1,$data);

    }

    public function TestNewItem(){
        $db = new \app\Models\Client_OrderItemT();
       
        $Item = new Client_OrderItemT();
            $Item -> OrderId = 555; // 还没有订单ID
            // $Item -> ProductId = 123;
            // $Item -> ProductName = '测试商品';
            // $Item -> ProductPrice = 99.9;
            // $Item -> ProductNum = 2;
            // $Item -> TotalPrice = 199.8;
            // $Item -> CreateTime = date('Y-m-d H:i:s');  
            // $it = $db -> insertGetId($Item);

        // $NewItem -> OrderId = 556; // 还没有订单ID
        // $NewItem -> ProductId = 124;
        // $NewItem -> ProductName = '测试商品2';
        // $NewItem -> ProductPrice = 88.8;
        $NewItem = [
            'OrderId' => 556,
            'ProductId' => 124,
            'ProductName' => '测试商品2',
            'ProductPrice' => 88.8,
            'ProductNum' => 3,
            'TotalPrice' => 266.4,
            'CreateTime' => date('Y-m-d H:i:s')
        ];
        $list = [ $NewItem ];
        $db -> saveAll($list, false);

            // $db -> save($Item);
            // $it = $Item -> Id;
  

        return  'TestNewItem OK ' ;
    }   

    public function QueryItems(){

        $db = new \app\Models\Client_OrderItemT();
        $list = $db -> select();
        return json($list);
    }
    public  function Test0003(){
        $ProductZoneId = 40001000;
        $ProductZoneName = '';
        $ZoneTypeDef =  \app\Models\Sys_TypeDefinedT::get($ProductZoneId);
        if(null != $ZoneTypeDef){
            $ProductZoneName = $ZoneTypeDef -> TypeName;
        }
        echo $ProductZoneName;

    }

    public  function read($id){


        $db = new \app\Models\Client_OrderT();
        $data = $db -> find($id);

        dump($data);
    }
    public  function read2(){

        $OrderId = \think\facade\Request::param('OrderId',0);
        $UserId =  \think\facade\Request::param('UserId',0);
        $db = new \app\Models\Client_OrderT();
        $order = $db -> where(['Id'=>$OrderId,'UserId'=>$UserId]) -> find();

        dump($order);
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

            $BonusUser =  Client_UserT::get($CashBonusLog -> ClientUserId );

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

            $ScoreUser =  Client_UserT::get($ScoreBonusLog -> ClientUserId );

            SetModel4Names($ScoreUser,['ScoreHistory', 'ScoreBalance','ScoreFrozen'],0);

            $OldBonus = $ScoreUser -> ScoreBalance;
            $ChangeBonus =  $ScoreBonusLog -> Bonus;
            $ChangeBonus =  abs($ChangeBonus ) * -1;
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

            $PointUser =  Client_UserT::get($PointLog -> ClientUserId );

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

    /** @var Client_UserT $CurrentUser 当前工作中使用的用户信息 */
    protected  $CurrentUser ;
    /** @var Client_OrderT $CurrentOrder 当前工作中使用的订单 */
    protected  $CurrentOrder;

    /** @var string $OrderNow 当前订单使用的时间，可能是CreateTime,也可能是UpdateTime */




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
        $op =  new  OrderPayEntity($OrderId,$UserId, $this->request);

        if(0>= $op -> StatusCode){
            return $this->SendJErr($op -> Title);
        }


        $InputModel = $this->request->post();
        $op -> Pay($InputModel);



        if(0>= $op -> StatusCode){
            return $this->SendJErr($op -> Title);
        }




        return $this->SendJOk('支付成功');

    }




    function  _BuildOrderRmk()
    {
        $ProductNames = array_column($this -> CurrentOrder ->Items -> toArray(), 'ProductName');
        $ResultString = implode(',', $ProductNames);
        $Rmk = mb_substr('订单商品：' . $ResultString, 0, 255, 'UTF-8');
        return $Rmk;
    }




    public function Defs(){

        $data =[  ];
        $ClassName = input('ClassName','');
        $TypeId = input('TypeId','');
        $Code =  input('Code'); // $this->request->param('code');//input('Code','');
        if('' == $Code){
            $Code = input('code','');
        }
        $where = [];
   
        if($Code != ''){
            // echo 'Code=' . $Code . "\n";
            $db2 = new \app\Models\Sys_TypeDefinedT();
            $type = $db2 -> where('CodeName',$Code) -> find();  
            if($type != null){
                $TypeId = $type -> TypeId;
            }else{
                return $this->SendJErr('指定的类型、状态不存在:' . $Code); 
            }       
        }else{
            // echo 'Code=null' . "\n";
        }
        if($TypeId != ''){
            $where[] = ['GroupId','=',$TypeId];
        }
        if(null == $TypeId ||  '' ==  $TypeId){
            return $this->SendJErr('必须指定类型ID');
        }
        $db= new \app\Models\Sys_TypeDefinedT();
        $data = $db -> where($where) 
        -> order(['GroupOrd'=>'asc', 'TypeId'=>'asc','CodeName'=>'asc'   ])
        ->select();  
        $data = $data->toArray();  
        // 返回数据     
        return $this->SendJOk('查询成功',1,$data);
    }
}




?>