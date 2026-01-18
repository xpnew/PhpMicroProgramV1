<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Selector extends AdminBase
{
   

    public function Client()
    {
        $this -> _InitViewData();
        return $this->fetch();
    }


    
    protected function _InitViewData(){
        parent::_InitViewData();
        $this->assign('title', '管理员列表');

    }




    public function ClientQuery(){
        $data =[  ];
        $BeginTime = input('BeginTime','');
        $EndTime = input('EndTime','');

        $Account = input('Account','');
        $Mobile = input('Mobile','');
        $NickName = input('NickName','');
        $RealityName = input('RealityName',''); 
        
        $MakerLevel = input('MakerLevel','');

        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量


        $where = [];
        if($NickName != ''){
            $where[] = ['NickName','like','%'.$NickName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }

        if('' != $BeginTime ){
            $where[] = ['RegisterDate','>=',$BeginTime . ' 00:00:00' ];
        }
        if('' != $EndTime ){
            $EndTime = new \DateTime($EndTime . ' 00:00:00');
            $EndTime -> modify('+1 day');
            $EndTime = $EndTime -> format('Y-m-d H:i:s');
            $where[] = ['RegisterDate','<=',$EndTime ];
        }
        // if($ClassId != ''){
        //     $where[] = ['ClassId','=',$ClassId];
        // }        

        if($NickName != ''){
            $where[] = ['NickName','like','%'.$NickName.'%'];
        }        
      
        if($RealityName != ''){
            $where[] = ['RealityName','like','%'.$RealityName.'%'];
        }        
        if($Mobile != ''){
            $where[] = ['Mobile','like','%'.$Mobile.'%'];
        }        
         
        $Client_UserT= new \app\Models\Client_UserT();

        $data = $Client_UserT -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $Client_UserT -> where($where) -> count();
        //return $this->SendQOk2('查询成功',$this->RecordCount,$data); //  查询返回  layer 专用的消息格式 QueryMsg
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }



}
