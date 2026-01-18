<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use \app\Models\Client_PointLogT;


class PointLog extends AdminBase
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
        $AssetTypeDefs =  $statusdb -> where(['GroupId'=>8000,'IsShow'=>1]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('AssetTypeDefs', $AssetTypeDefs);
        $AssetStatusDefs =  $statusdb -> where(['GroupId'=>8100,'IsShow'=>1]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('AssetStatusDefs', $AssetStatusDefs);

        return $this->fetch();
    }
    
    protected function _InitViewData(){
        parent::_InitViewData();
        $this->assign('title', '积分变动记录');
    }
    public function query(){
        $data =[  ];
        
        $BeginTime = input('BeginTime','');
        $EndTime = input('EndTime','');

        $ClientUserId = input('ClientUserId','');
        $ClientNickName = input('ClientNickName','');
        $ClientRealName = input('ClientRealName','');
        $ClientPhone = input('ClientPhone','');
        $OrderNo = input('OrderNo','');
        $Rmk = input('Rmk','');

        $AssetTypeId = input('AssetTypeId','');
        $AssetStatusId = input('AssetStatusId','');


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
        if($OrderNo != ''){
            $where[] = ['OrderNo','like','%'.$OrderNo.'%'];
        }       

        if($Rmk != ''){
            $where[] = ['Rmk','like','%'.$Rmk.'%'];
        }        
            

        if($AssetStatusId != ''){
            $where[] = ['AssetStatusId','=',$AssetStatusId];
        } 
        if($AssetTypeId != ''){
            $where[] = ['AssetTypeId','=',$AssetTypeId];
        }       

        $db= new Client_PointLogT();

        $data = $db -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $db -> where($where) -> count();


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
