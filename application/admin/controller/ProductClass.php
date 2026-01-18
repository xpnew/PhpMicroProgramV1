<?php
namespace app\admin\controller;
use think\Controller;



class ProductClass extends AdminBase
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
        $Product_ClassT= new \app\Models\Product_ClassT();

        $data = $Product_ClassT -> where($where) ->select();
        $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $Product_ClassT -> where($where) -> count();
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }

    protected function _InitViewData(){
        parent::_InitViewData();
        $this->assign('title', '商品分类');


    }
    public function add(){
        $this -> _InitViewData();
        $statusdb  = new \app\Models\Sys_TypeDefinedT();
        $ProductZoneDefs =  $statusdb -> where(['GroupId'=>4000,'IsShow'=>1]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('ProductZoneDefs', $ProductZoneDefs);

        // $Model =   \app\Models\Product_ClassT::create();
        $Model  = new \app\Models\Product_ClassT();
        $Model -> Id = 0;
        $Model -> ClassName = '';
        $Model -> ClassCode = '';
        $Model -> SortId = 1000;
        $Model -> EnableBuildBonus = 0;
        $Model -> EnableBuildPoint = 0;
        $Model -> EnablePointBuy = 0;
        $Model -> ClassTitPic01 = '';
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
        $statusdb  = new \app\Models\Sys_TypeDefinedT();
        $ProductZoneDefs =  $statusdb -> where(['GroupId'=>4000,'IsShow'=>1]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('ProductZoneDefs', $ProductZoneDefs);
                
        if(!input('?id')){
            return $this->error('参数错误');
        }

        $Id = input('id',0);
        $Product_ClassT= new \app\Models\Product_ClassT();

        $Model = $Product_ClassT->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }   
        $this->assign('Model', $Model);
        return $this->fetch('add');
    }
    public function del(){
        if(!input('?Id')){
            return  $this->SendJErr('参数错误');  
        }
        $Product_ClassT= new \app\Models\Product_ClassT();
        $Id = input('Id',0);
        $Model = $Product_ClassT->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->SendJErr('参数错误');
        }   

        $Product_ClassT->where(['Id'=>$Id])->delete();
        

        return $this->SendJOk('删除成功');
    }
    public function save(){
        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;
        $Product_ClassT= new \app\Models\Product_ClassT();
        if($Id > 0){        
            $Product_ClassT->save($InputModel,['Id'=>$Id]);
        }else{       
            $Product_ClassT->save($InputModel);
        }
        return $this->SendJOk('保存成功');
    }

    public function test1()
    {
        $User= new \app\admin\model\Sys_User();
        $op  = new \app\Models\SysOptions();
        $Product_ClassT= new \app\Models\Product_ClassT();

        $this->assign('title', '测试');
        $this->assign('ModelList', $Product_ClassT->select());


        foreach ($Product_ClassT->select() as $key => $value) {
            echo $value['ClassName'].'='.$value['ClassCode'].'<br/>';
        }
    }
}


?>