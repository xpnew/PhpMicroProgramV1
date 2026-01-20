<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\Models\Client_OrderT;
use app\Models\Client_OrderItemT;

use think\facade\Log;



class Order extends AdminBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this -> _InitViewData();

        $statusdb  = new \app\Models\Sys_TypeDefinedT();

        $OrderStatusDefs =  $statusdb -> where(['GroupId'=>1000]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('OrderStatusDefs', $OrderStatusDefs);
        $PayStatusDefs =  $statusdb -> where(['GroupId'=>2000]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('PayStatusDefs', $PayStatusDefs);
        $ProductTypeDefs =  $statusdb -> where(['GroupId'=>3000]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('ProductTypeDefs', $ProductTypeDefs);


        return $this->fetch();
    }
    protected function _InitViewData()
    {
        parent::_InitViewData();
        $this->assign('title', '订单');
        $this->assign('ProductClassList', \app\Models\Product_ClassT::select());

    }

    public function query(){
        $data =[  ];
        $ClientName = input('ClientName','');
        $ClientPhone = input('ClientPhone','');

        $UserId = input('UserId','');
        $OrderStatus = input('OrderStatus','');
        $PayStatus = input('PayStatus','');
        $DeliveryPrice = input('DeliveryPrice','');
        $ClassId = input('ClassId','');
        $ProductName = input('ProductName','');
        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量

        $where = [];

        if($UserId != ''){
            $where[] = ['UserId','=',$UserId];
        } 
        if($OrderStatus != ''){
            $where[] = ['OrderStatus','=',$OrderStatus];
        } 
        if($PayStatus != ''){
            $where[] = ['PayStatus','=',$PayStatus];
        } 
        if($DeliveryPrice != ''){
            $where[] = ['DeliveryPrice','=',$DeliveryPrice];
        } 

        if($ClientName != ''){
            $where[] = ['ClientName','like','%'.$ClientName.'%'];
        }        
        if($ClientPhone != ''){
            $where[] = ['ClientPhone','like','%'.$ClientPhone.'%'];


        }        



        $db= new Client_OrderT();

        $data = $db -> where($where) 
        -> order(['UpdateTime' => 'desc','Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $db -> where($where) -> count();
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }  

    public function QueryItems(){
        
        $data =[  ];


        $OrderId = input('OrderId','');

        $ProductId = input('ProductId','');

        $ProductName = input('ProductName','');
        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量

        $where = [];

        if($ProductId != ''){
            $where[] = ['ProductId','=',$ProductId];
        } 

        if($OrderId != ''){
            $where[] = ['OrderId','=',$OrderId];
        } 
   




        $db= new Client_OrderItemT();

        $data = $db -> where($where)
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      

        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }  




    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        
        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;
        if(0>= $Id ){
            return $this->SendJErr('参数错误');
        }
        $this-> SayLog(' admin order save InputModel： ' , $InputModel);
        $db= new Client_OrderT();

        $ExistOrder =  $db -> where(['Id'=>$Id])->find();
        if(!$ExistOrder){
            return $this->SendJErr('参数错误 订单不存在');
        }

        if(1 ==  $InputModel['SaveType']){
            $ExistOrder['Remark'] =  $InputModel['Remark'];
            $ExistOrder['Comment'] =  $InputModel['Comment'];
            $ExistOrder["UpdateTime"]   =  date('Y-m-d H:i:s');
            $ExistOrder -> save();
            return $this->SendJOk('修改备注成功！');
        }


        if(null == $InputModel['TrackingNumber'] || ! isset($InputModel['TrackingNumber'])){
            return $this->SendJErr('请输入运单号');
        }


        $this-> SayLog(' admin order save ExistOrder： ' , $ExistOrder);
        if( 10002000 !=  $ExistOrder['OrderStatus']){
            return $this->SendJErr('参数错误 订单状态不允许修改');
        }

        $InputModel["UpdateTime"]   =  date('Y-m-d H:i:s');
        $InputModel["OrderStatus"] =  10003000;
        $InputModel["ShipingTime"] =  date('Y-m-d H:i:s');




        if($Id > 0){        
            $db->save($InputModel,['Id'=>$Id]);
        }else{       
            $db->save($InputModel);
        }
        return $this->SendJOk('保存成功');

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
        $this -> _InitViewData();
        if(!input('?id')){
            return $this->error('参数错误');
        }

        $Id = input('id',0);
        $db = new \app\Models\Client_OrderT();

        $Model = $db->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }   
        $itemdb =  new \app\Models\Client_OrderItemT();
        $Model -> Items = $itemdb->where(['OrderId'=>$Id])->select();


        $this-> SayLog('尝试输出： ' , $Model);

        $this->assign('Model', $Model);
        $this->assign('ProductClassList', \app\Models\Product_ClassT::select());
        return $this->fetch();
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
