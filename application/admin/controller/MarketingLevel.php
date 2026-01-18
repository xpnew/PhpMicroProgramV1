<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\Models\Sys_TypeDefinedT;
use app\Models\Biz_MarketingLevelT;


class MarketingLevel extends AdminBase
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
    protected function _InitViewData(){
        parent::_InitViewData();
        $this->assign('title', '推广级组');
    }
    public function query(){
        $data =[  ];
        $LevelName = input('LevelName','');

        $where = [];
        if($LevelName != ''){
            $where[] = ['LevelName','like','%'.$LevelName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        $Model= new \app\Models\Biz_MarketingLevelT();

        $data = $Model -> where($where) ->select();
        $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $Model -> where($where) -> count();
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }

    public function add(){
        $this -> _InitViewData();
        // $Model =   \app\Models\Product_ClassT::create();
        $Model  = new \app\Models\Biz_MarketingLevelT();
        $Model -> Id = 0;
        $Model -> LevelName = '';
        $Model -> LevelDifference = '';

        $Model -> IsNeedSequential = 0;
        $Model -> PeerAwardMax = 0;

        $Model -> Remarks = '';
        // $arr =  $Model -> toArray();
        // var_dump($Model);
        // var_dump($arr);
        // echo  '转换完的数组长度： ' . count($arr);
        $this -> SayLog('尝试输出： ' , $Model);
        $this->assign('Model', $Model);
        // if(1 == 1 ){
        //     return "";
        // }
        return $this->fetch();
    }
    public function edit(){
        $this -> _InitViewData();
        if(!input('?id')){
            return $this->error('参数错误');
        }

        $Id = input('id',0);
        $DB= new \app\Models\Biz_MarketingLevelT();

        $Model = $DB->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }   
        $this->assign('Model', $Model);
        return $this->fetch('add');
    }
    public function save(){
        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;
        $DB= new \app\Models\Biz_MarketingLevelT();
        if($Id > 0){        
            $DB->save($InputModel,['Id'=>$Id]);
        }else{       
            $DB->save($InputModel);
        }
        return $this->SendJOk('保存成功');
    }

    public function test1()
    {
        $User= new \app\admin\model\Sys_User();
        $op  = new \app\Models\SysOptions();
        $Product_ClassT= new \app\Models\Biz_MarketingLevelT();

        $this->assign('title', '测试');
        $this->assign('ModelList', $Product_ClassT->select());


        foreach ($Product_ClassT->select() as $key => $value) {
            echo $value['LevelName'].'='.$value['LevelDifference'].'-'.$value['Remarks'].'<br/>';
        }
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


}
