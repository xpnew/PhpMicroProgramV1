<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\Models\Client_BuyCarItemT  AS ItemDB;

class BuyCar extends ApiBase
{
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
        $UserId = input('UserId','');

        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',9999); // 每页显示数量


        $where = [];
        if($ClassName != ''){
            $where[] = ['ClassName','like','%'.$ClassName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        if($UserId != ''){
            $where[] = ['UserId','=',$UserId];
        }        
 
        $Product_InfoT= new \app\Models\Client_BuyCarItemT();

        $data = $Product_InfoT -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        $data = $data->toArray();    
        // 返回数据      
        return $this->SendJOk('查询成功',1,$data); 
    }    





    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function add($id,$userid)
    {
        $where = [];
        $where[] = ['ProductId','=',$id];
        $where[] = ['UserId','=',$userid];
        $db= new \app\Models\Client_BuyCarItemT();
        $Exist = $db -> where($where) ->find();
        if($Exist != null){
            $Exist -> Qty = $Exist -> Qty + 1;
            $Exist -> save();
            return $this->SendJOk('添加成功',1,$Exist);
        }
        $pro = \app\Models\Product_InfoT::get($id);
        if($pro == null){
            return $this->SendJErr('商品不存在');
        }
        $ProductClass =  \app\Models\Product_ClassT::get($pro -> ClassId);
        if($ProductClass == null){
            return $this->SendJErr('商品分类不存在');
        }   
        // $this -> SayLog('$ProductClass', $ProductClass);

        $Price = null;
        if($pro -> DiscountPrice !=  null ){
            $Price = $pro -> DiscountPrice;
        }else{
            $Price = $pro -> NormalPrice;
        }   
        if($Price == null){
            return $this->SendJErr('商品没有价格');
        }
        $data = [
            'ProductId' => $id,
            'UserId' => $userid,
            'Qty' => 1,
            'CreateTime' => date('Y-m-d H:i:s'),
            'UnitPrice' => $Price   ,
            'TotalPrice' => $Price,
            'ProductName' => $pro -> ProductName,
            'ProductPic' => $pro -> ProductPic, 
            'ProductCode' => $pro -> ProductCode,
            'ProductClassName' => $ProductClass -> ClassName,
            'ProductClassId' => $ProductClass -> Id,
            'Summary' => $pro -> Summary,
        ];
        $db -> save($data);
        return $this->SendJOk('添加成功',1,$db);

    }

    public function sulessenpplis($id,$userid )
    {
        //
    }


    public function test()
    { 
        $db= new \app\Models\Client_BuyCarItemT();

        $data = $db -> limit( 10)  ->select();
        // $data = $data->toArray();    
        // 返回数据      

        return $this->SendJOk('查询成功',1,$data);         
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
        //
    }

    /**
       * 删除 购物车里面一部分商品
     */
    public function del()
    {
        $Items = \think\facade\Request::param('Items');
        $UserId =  \think\facade\Request::param('UserId');


      
        // 使用 array_column 提取 'CardId' 列
        $CardIdArr = array_column($Items, 'CardId');  


        $Exists =  ItemDB::where('UserId',$UserId) -> whereIn('Id',$CardIdArr) -> select();
        
        // $DB =  new ItemDB();

        // $Exists =  $DB -> where('UserId',$UserId) -> whereIn('Id',$CardIdArr) -> select();


        $this -> SayLog('$Exists', $Exists);
        // 返回折是模型，而不是数组，所以不能用 array_column 获取 'Id' 列
        // $ExistId =  array_column($Exists, 'Id');
        $ExistId =  [];
        // $ExistId = array_map(function($card) {
        //     return $card -> Id ;
        // }, $Exists);
        foreach ($Exists as $exist) {
            $ExistId[] = $exist -> Id;
        }

        $DiffIds =  array_diff($CardIdArr,$ExistId);

        // // 根据结果筛选Items
        // $filteredItems = array_filter($Items, function($item) use ($DiffIds) {
        //     return in_array($item["CardId"], $DiffIds);
        // });

        if( 0 < count($DiffIds) ){


            $DiffArr = array_filter($Items, function($item) use ($DiffIds) {
                return in_array($item['CardId'] , $DiffIds);
            });
               
            // 提取所有名字，生成新数组
            $NameAlias = array_map(function($item) {
                return  '[商品名称： ' . $item["ProductName"] . '  商品Id ' . $item["ProductId"] .']';
            }, $DiffArr);

            $this -> SayLog('$NameAlias', $NameAlias);

            $ErorrTitle = '相关商品：' .   implode(", ", $NameAlias);
            $this -> Msg ->Body =  $ErorrTitle;
            return $this->SendJErr('购物车里出现了意外的商品(不属于自己的购物车)');
        }

        ItemDB::destroy($ExistId);

        //$NewList=  ItemDB::where('UserId',$UserId) -> select();

        // $dbitems =  new Client_OrderItemT();
        // // $dbitems -> saveAll($ItemList,false);


        return $this->SendJOk('删除成功',1,$Items);    

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
