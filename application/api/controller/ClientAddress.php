<?php
namespace app\api\controller;
use think\Controller;
use think\Request;

use \app\Models\Client_AddressT as PageDBModel;


class ClientAddress extends ApiBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return 'api test ok';
    }
    public function query(){
        $data =[  ];
        $Address = input('Address','');
        $UserId = input('UserId','');
        $AddressName = input('AddressName','');


        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',9999); // 每页显示数量


        $where = [];
        if($Address != ''){
            $where[] = ['FullAddress','like','%'.$Address.'%'];
        }else{
          

        }
        if($UserId != ''){
            $where[] = ['UserId','=',$UserId];
        }        
        if($AddressName != ''){
            $where[] = ['AddressName','like','%'.$AddressName.'%'];
        }        
 
        $Product_InfoT= new \app\Models\Client_AddressT();

        $data = $Product_InfoT -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        $data = $data->toArray();    
        // 返回数据      
        return $this->SendJOk('查询成功',1,$data); 
    }    
    public function Save()
    {

        $UserId =  \think\facade\Request::param('UserId');

        if(null == $UserId || '' == $UserId ){
             $UserId = input('UserId','');
        }
        if(null == $UserId || '' == $UserId ){
            return $this->SendJError('用户ID不能为空');
        }

        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;

        $IsNew = $Id == 0;

        $where = [];
        $where[] = ['AddressName','=',$InputModel['AddressName']];
        $where[] = ['UserId','=',$UserId];
        $DB= new \app\Models\Client_AddressT();
        $Exist = $DB -> where($where) ->find();
        if($Exist != null){
           if($IsNew)
                return $this->SendJErr('同名地地址已存在');
            else if($Exist -> Id != $Id)
                return $this->SendJErr('同名地地址已存在'); 
        }
        $InputModel['UserId'] = $UserId;

        // $pro = \app\Models\Product_InfoT::get($id);
        // if($pro == null){
        //     return $this->SendJErr('商品不存在');
        // }
        // $Price = null;

        if($IsNew){        
            $InputModel['CreateTime'] = date('Y-m-d H:i:s');
            $DB->save($InputModel);
        }else{       
            $InputModel['UpdateTime'] = date('Y-m-d H:i:s');
            $DB->save($InputModel,['Id'=>$Id]);
        }

        // if(null !=  $InputModel['IsDefautAddress']  && 0 < $InputModel['IsDefautAddress']){



        // }else
        // {

        // }



        return $this->SendJOk('保存成功');

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




    public function GetUserId(){

               $UserId =  \think\facade\Request::param('UserId');

                 $UserId2 = input('UserId','-9999');
                 echo 'input UserId2 = ' . $UserId2;
                 echo '<br>';

                return 'api test  userid: ' . $UserId;
    }

}