<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

use think\facade\Log;
use \app\Models\Biz_PayBillT;



class PayBill extends AdminBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this -> _InitViewData();

        return $this->fetch();
    }
    protected function _InitViewData()
    {
        parent::_InitViewData();
        $this->assign('title', '交易流水');

    }
    public function query(){
        $data =[  ];
        $BeginTime = input('BeginTime','');
        $EndTime = input('EndTime','');

        $OrderNo = input('OrderNo','');
        $ClientPhone = input('ClientPhone','');


        $ClientUserId = input('ClientUserId','');
        $ClientNickName = input('ClientNickName','');
        $ClientRealName = input('ClientRealName','');
        $ClientPhone = input('ClientPhone','');
        $IsSuccess = input('IsSuccess','');
        $IsFinished = input('IsFinished','');
        $PayStatus = input('PayStatus','');
        $DeliveryPrice = input('DeliveryPrice','');
        $ClassId = input('ClassId','');
        $ProductName = input('ProductName','');
        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量
        // $PageSize = 26; // 每页显示数量

        $where = [];
        
        if('' != $BeginTime ){
            $where[] = ['CreateTime','>=',$BeginTime . ' 00:00:00' ];
        }
        if('' != $EndTime ){
            $EndTime = new \DateTime($EndTime . ' 00:00:00');
            $EndTime -> modify('+1 day');
            $EndTime = $EndTime -> format('Y-m-d H:i:s');
            $where[] = ['CreateTime','<=',$EndTime ];        
        }


        if($ClientUserId != ''){
            $where[] = ['ClientUserId','=',$ClientUserId];
        } 
        if($ClientRealName != ''){
            $where[] = ['ClientRealName','like','%'.$ClientRealName.'%'];
        }        
        if($ClientNickName != ''){
            $where[] = ['ClientNickName','like','%'.$ClientNickName.'%'];
        }        
        if($ClientPhone != ''){
            $where[] = ['ClientPhone','=',$ClientPhone];
        }        
        if($IsFinished != ''){
            $where[] = ['IsFinished','=',$IsFinished];
        } 
        if($IsSuccess != ''){
            $where[] = ['IsSuccess','=',$IsSuccess];
        } 
        if($PayStatus != ''){
            $where[] = ['PayStatus','=',$PayStatus];
        } 
        if($OrderNo != ''){
            $where[] = ['OrderNo','=',$OrderNo];
        } 

      
        if($ClientPhone != ''){
            $where[] = ['ClientPhone','like','%'.$ClientPhone.'%'];
        }        



        $db= new Biz_PayBillT();

        $data = $db -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $db -> where($where) -> count();
        // if(2 ==   $PageIndex ){
        //     $data[0]['RowVer'] = null;
        // }  
        for($i=0; $i< count($data); $i++ ){
            $data[$i]['RowVer'] = null;

        }
        //dump($this-> QMsg);

        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
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




}
