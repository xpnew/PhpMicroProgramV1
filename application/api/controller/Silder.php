<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class Silder extends ApiBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        
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

        if($ClassName != ''){
            $where[] = ['ClassName','like','%'.$ClassName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
      
        if($ProductName != ''){
            $where[] = ['ProductName','like','%'.$ProductName.'%'];
        }        
        $db= new \app\Models\IndexSwitchT();

        $data = $db -> where($where) 
        -> order(['TopNum' => 'desc','UpdateTime' => 'desc','Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      



        return $this->SendJOk('查询成功',1,$data); 

    }    

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function view($id)
    {

        $db= new \app\Models\Product_InfoV();
       
        $Model = $db->where(['Id'=>$id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }   

        return $this->SendJOk('查询成功',1,$Model);

    }


    public function test()
    { 
        $db= new \app\Models\IndexSwitchT();

        $data = $db -> limit( 10)  ->select();
        // $data = $data->toArray();    
        // 返回数据      

        return $this->SendJOk('查询成功',1,$data);         
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
