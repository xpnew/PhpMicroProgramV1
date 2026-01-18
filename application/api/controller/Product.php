<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class Product extends ApiBase
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
        $ClassId = input('ClassId','');
        $ProductName = input('ProductName','');
        $TagName = input('tagname','');
        $SearchKey = input('SearchKey','');
        $this -> SayLog( 'api 查询  参数',$ClassName.'|'.$ClassId.'|'.$ProductName.'|'.$TagName.'|'.$SearchKey );


        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',1000); // 每页显示数量

        $where = [];
        if($ClassName != ''){
            $where[] = ['ClassName','like','%'.$ClassName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        if($ClassId != ''){
            $where[] = ['ClassId','=',$ClassId];
        }        
        if($ProductName != ''){
            $where[] = ['ProductName','like','%'.$ProductName.'%'];
        }        
        if($TagName != ''){
            $where[] = ['Tags','like','%|'.$TagName.'|%'];
        }        
        $Product_InfoT= new \app\Models\Product_InfoV();

        if( isset($SearchKey) && $SearchKey != '')
        {
            // $where[] =[function ($query) use($SearchKey) {
            //     $query->where('ProductName','like','%'.$SearchKey.'%')
            //         ->whereOr('ClassName','like','%'.$SearchKey.'%')
            //         ->whereOr('Tags','like','%|'.$SearchKey.'|%');
            // }];

            $data = $Product_InfoT -> where(function ($query) use($SearchKey) {
                $query->where('ProductName','like','%'.$SearchKey.'%')
                    ->whereOr('ClassName','like','%'.$SearchKey.'%')
                    ->whereOr('Tags','like','%|'.$SearchKey.'|%');
            }) 
            -> order(['Tops' => 'desc','Id'=>'desc'])
            -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
            // $data = $data->toArray();    
            // 返回数据      

            return $this->SendJOk('查询成功',1,$data); 

        }        


        $data = $Product_InfoT -> where($where) 
        -> order(['Tops' => 'desc','Id'=>'desc'])
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

    public function ProductClass(){
        $data =[  ];
        $ClassName = input('ClassName','');

        $where = [];
        if($ClassName != ''){
            $where[] = ['ClassName','like','%'.$ClassName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        $Product_ClassT= new \app\Models\Product_ClassT();

        $data = $Product_ClassT -> where($where) ->select();

        return $this->SendJOk('查询成功',1,$data); 
        // 返回数据      
       
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
