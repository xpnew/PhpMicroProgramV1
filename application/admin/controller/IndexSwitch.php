<?php
namespace app\admin\controller;
use think\Controller;
use app\Models\IndexSwitchT;


class IndexSwitch extends AdminBase
{
    public function index()
    {
        $this -> _InitViewData();
        return $this->fetch();
    }

    public function query(){
        $data =[  ];
        $ClassName = input('ClassName','');

        $where = [];
        if($ClassName != ''){
            $where[] = ['ClassName','like','%'.$ClassName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        $IndexSwitchT= new \app\Models\IndexSwitchT();

        $data = $IndexSwitchT -> where($where) ->select();
        $data = $data->toArray();    
        // 返回数据      

        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }

    protected function _InitViewData(){
        parent::_InitViewData();
        $this->assign('title', '首页轮播图');


    }
    public function add(){
        $this -> _InitViewData();
        // $Model =   \app\Models\IndexSwitchT::create();
        $Model  = new \app\Models\IndexSwitchT();
        $Model -> Id = 0;
        $Model -> Title = '';
        $Model -> Pic = '';
        $Model -> UpdateTime = '';
        $Model -> CreateTime = '';
        $Model -> TopNum = 0;
        $Model -> Remarks = '';
        // $arr =  $Model -> toArray();
        // var_dump($Model);
        // var_dump($arr);
        // echo  '转换完的数组长度： ' . count($arr);
        //$this -> SayLog('尝试输出： ' , $Model);
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
        $IndexSwitchT= new \app\Models\IndexSwitchT();

        $Model = $IndexSwitchT->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }   
        $this->assign('Model', $Model);
        return $this->fetch('add');
    }
    public function delete(){
        if(!input('?Id')){
            return  $this->SendJErr('参数错误');  
        }
        $Id = input('Id',0);
        $IndexSwitchT= new \app\Models\IndexSwitchT();

        $Model = $IndexSwitchT->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->SendJErr('参数错误');
        }   

        $IndexSwitchT->where(['Id'=>$Id])->delete();
        

        return $this->SendJok('删除成功');
    }
    public function save(){
        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;
        $IndexSwitchT= new \app\Models\IndexSwitchT();
        if($Id > 0){        
            $InputModel['UpdateTime'] = date('Y-m-d H:i:s');
            $IndexSwitchT->save($InputModel,['Id'=>$Id]);
        }else{       
            $InputModel['CreateTime'] = date('Y-m-d H:i:s');
            $IndexSwitchT->save($InputModel);
        }
        return $this->SendJOk('保存成功');
    }

    public function test1()
    {
        $User= new \app\admin\model\Sys_User();
        $op  = new \app\Models\SysOptions();
        $IndexSwitchT= new \app\Models\IndexSwitchT();

        $this->assign('title', '测试');
        $this->assign('ModelList', $IndexSwitchT->select());


        foreach ($IndexSwitchT->select() as $key => $value) {
            echo $value['ClassName'].'='.$value['ClassCode'].'<br/>';
        }
    }
}


?>