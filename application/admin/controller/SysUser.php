<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\Models\Sys_UserT;

class SysUser extends AdminBase
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
        $this->assign('title', '管理员列表');

    }

    public function query(){
        $data =[  ];
        $LoginName = input('LoginName','');
        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量
        
        $where = [];
        if($LoginName != ''){
            $where[] = ['LoginName','like','%'.$LoginName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        $DB= new \app\Models\Sys_UserT();

        $data = $DB -> where($where) 
        -> order(['UpdateTime' => 'desc','Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        $data = $data->toArray();    
        // 返回数据      

        $this->RecordCount = $DB -> where($where) -> count();
        return $this->SendQOk2('查询成功',$this->RecordCount,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }

    public function add(){
        $this -> _InitViewData();
        // $Model =   \app\Models\Product_ClassT::create();
        $Model  = new \app\Models\Sys_UserT();
        $Model -> Id = 0;  //
        $Model -> LoginName = ''; //
        $Model -> Pwd = ''; //
        $Model -> CreateTime = ''; //
        $Model -> Remark = ''; //
        $Model -> UpdateTime = ''; //
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
        $DB= new \app\Models\Sys_UserT();

        $Model = $DB->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
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
    public function save(){
        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;
        $DB= new \app\Models\Sys_UserT();
        if($Id > 0){       
            $InputModel['UpdateTime'] = date('Y-m-d H:i:s');
            $DB->save($InputModel,['Id'=>$Id]);
        }else{       
            $Exist = $DB->where(['LoginName'=>$InputModel['LoginName']])->find();
            if($Exist){
                return $this->SendJErr('登录名已存在');
            }
            $InputModel['CreateTime'] = date('Y-m-d H:i:s');
            $DB->save($InputModel);
        }
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
    public function del(){
        if(!input('?Id')){
            return  $this->SendJErr('参数错误');  
        }
        $db= new \app\Models\Sys_UserT();
        $Id = input('Id',0);
        $Model = $db->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->SendJErr('参数错误');
        }   
        if('admin' ==  $Model-> LoginName){
            return $this->SendJErr('不能删除超级管理员');
        }

        // $db->where(['Id'=>$Id])->delete();
        

        return $this->SendJok('删除成功');
    }
    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $db= new \app\Models\Sys_UserT();
        $Id = input('Id',0);
        $Model = $db->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->SendJErr('参数错误');
        }   
        if('admin' ==  $Model-> LoginName){
            return $this->SendJErr('不能删除超级管理员');
        }

        // $db->where(['Id'=>$Id])->delete();
        

        return $this->SendJok('删除成功');
    }
}
