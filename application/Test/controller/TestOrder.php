<?php
namespace app\test\controller;
use think\Controller;
use \app\Models\Client_OrderItemT;
use app\comm\CommControllerBase;



class TestOrder extends CommControllerBase
{ 




    public function index(){
        return 'test order';
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