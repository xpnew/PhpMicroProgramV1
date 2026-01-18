<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use \app\Models\Biz_WithdrawT as PageDBModel;
use \app\utils\GeneralTool;


class Withdraw extends AdminBase
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
        $this->assign('title', '提现记录');
    }
    public function query(){
        $data =[  ];
        
        $BeginTime = input('BeginTime','');
        $EndTime = input('EndTime','');

        $ClientUserId = input('ClientUserId','');
        $ClientRealName = input('ClientRealName','');
        $ClientNickName = input('ClientNickName','');

        $Mobile = input('Mobile','');

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

        if($ClientNickName != ''){
            $where[] = ['ClientNickName','like','%'.$ClientNickName.'%'];
        }        
        if($ClientRealName != ''){
            $where[] = ['ClientRealName','like','%'.$ClientRealName.'%'];
        }        
        if($OrderNo != ''){
            $where[] = ['OrderNo','like','%'.$OrderNo.'%'];
        }       
        if($Mobile != ''){
            $where[] = ['Mobile','=',$Mobile];
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

 
        $DB= new PageDBModel();

        $data = $DB -> where($where) 

        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $DB -> where($where) -> count();


        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }  
    public function edit(){
        $this -> _InitViewData();
        if(!input('?id')){
            return $this->error('参数错误');
        }

        $Id = input('id',0);
        $DB= new PageDBModel();

        $Model = $DB->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }
        if(null == $Model['FinishedTime']){
            $Model['IsFinished'] =1;
            $Model['IsSuccess'] =1;
        }
        $this->assign('Model', $Model);
        return $this->fetch('add');
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

        $PayAmountFee  = 0;
        $PayAmountStr =  $InputModel['PayAmount'];
        $ProcessingFee  = 0;
        $ProcessingAmountStr =  $InputModel['ProcessingAmount'];
        $AmountResult =  GeneralTool::TransAmount2Fee($PayAmountStr);
        if(!$AmountResult->Ready){
            return $this->SendJErr($AmountResult->Message);
        }
        $PayAmountFee = $AmountResult->AmountFee;        
        $AmountResult =  GeneralTool::TransAmount2Fee($ProcessingAmountStr);
        if(!$AmountResult->Ready){
            return $this->SendJErr($AmountResult->Message);
        }
        $ProcessingFee = $AmountResult->AmountFee;


        $DB= new PageDBModel();

        $ExistModel = $DB->where(['Id'=>$Id])->find();
        if(!$ExistModel){
            return $this->SendJErr('参数错误 订单不存在');
        }
        $this-> SayLog(' admin 提现申请 save ExistModel： ' , $ExistModel);

        if(null  ==  $ExistModel['FinishedTime']){
            $ExistModel['FinishedTime'] = date('Y-m-d H:i:s');
            $ExistModel['IsFinished'] = 1;
        }

        //首次 成功到账时，填充到账时间
        if($ExistModel['IsSuccess'] == 0 || 1== $InputModel['IsSuccess'] ){
            if(null  ==  $ExistModel['ArriveTime']){
                $ExistModel['ArriveTime'] = date('Y-m-d H:i:s');
            }
        }
        $ExistModel['PayoutBankAccount'] =  $InputModel['PayoutBankAccount'];
        $ExistModel['PayoutBankName'] =  $InputModel['PayoutBankName'];
        $ExistModel['IsSuccess'] =  $InputModel['IsSuccess'];
        $ExistModel['PayPlatNo'] =  $InputModel['PayPlatNo'];
        $ExistModel['PayAmountFee'] = $PayAmountFee;
        $ExistModel['ProcessingFee'] = $ProcessingFee;
        $this-> SayLog(' admin 提现申请 save ExistModel new ： ' , $ExistModel);


        // $DB->save($ExistModel,['Id'=>$Id]);
        $ExistModel -> save();

        $User =  \app\Models\Client_UserT::get($ExistModel['$ClientUserId']);

        if(null == $User -> WithdrawHistory ) $User -> WithdrawHistory   = 0.0;

        $User -> WithdrawHistory +=   $PayAmountFee;

        $User -> save();


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
