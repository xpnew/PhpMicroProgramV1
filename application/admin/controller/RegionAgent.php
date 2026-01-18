<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\Models\Biz_RegionAgentT;



class RegionAgent extends AdminBase
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
        $this->assign('title', '区县代理');
    }
    public function query(){
        $data =[  ];
        $BeginTime = input('BeginTime','');
        $EndTime = input('EndTime','');


        $RegionName = input('RegionName','');
        $Mobile = input('Mobile','');
        $ProvinceId = input('ProvinceId','');
        $CityId = input('CityId','');
        $CountyId = input('CountyId','');
        $ClientUserId = input('ClientUserId','');
        $AgentLevel = input('AgentLevel','');
        $Rmk = input('Rmk','');

        $where = [];
        if('' != $BeginTime ){
            $where[] = ['CreateTime','>=',$BeginTime . ' 00:00:00' ];
        }
        if('' != $EndTime ){
            $EndTime = new \DateTime($EndTime . ' 00:00:00');
            $EndTime -> modify('+1 day');
            $EndTime = $EndTime -> format('Y-m-d H:i:s');
            $where[] = ['CreateTime','<',$EndTime ];
        }        
        if($RegionName != ''){
            $where[] = ['RegionName','like','%'.$RegionName.'%'];
        // }else{
        //     $where[] = ['Id','>',0];
        }
        if($Mobile != ''){
            $where[] = ['Mobile','like','%'.$Mobile.'%'];
        }      
        if($ProvinceId != ''){
            $where[] = ['ProvinceId','=',$ProvinceId];
        }    
        if($CityId != ''){
            $where[] = ['CityId','=',$CityId];
        }    
        if($CountyId != ''){
            $where[] = ['CountyId','=',$CountyId];
        }    
          
        if($AgentLevel != ''){
            $where[] = ['AgentLevel','=',$AgentLevel];
        }    
        if($ClientUserId != ''){
            $where[] = ['ClientUserId','=',$ClientUserId];
        }    



        $Model= new \app\Models\Biz_RegionAgentT();

        $data = $Model -> where($where) ->select();
        $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $Model -> where($where) -> count();
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }

    public function add(){
        $this -> _InitViewData();
        // $Model =   \app\Models\Product_ClassT::create();
        $Model  = new \app\Models\Biz_RegionAgentT();
        $Model -> Id = 0;
        $Model -> ClientUserId = 0;  // 客户Id
        $Model -> ProvinceId = 0;  // 省Id
        $Model -> CityId = 0;  // 市Id
        $Model -> CountyId = 0;  // 县Id
        $Model -> RegionName = ''; // 地区名称
        $Model -> AgentLevel = 0;  // 代理等级：1 省 2 市  3县
        $Model -> NickName = ''; // 呢称
        $Model -> RealityName = ''; // 实名
        $Model -> Mobile = ''; // 手机号
        $Model -> CreateTime = ''; // 创建时间
        $Model -> UpdateTime = ''; // 修改时间
        $Model -> CommenceTime = date('Y-m-d H:i:s'); // 开始时间
        $Model -> ExpireStopTime = ''; // 过期时间
        $Model -> InaugurateTime = date('Y-m-d H:i:s'); // 首次合作开始时间
        $Model -> Rmk = ''; // 备注
        // $arr =  $Model -> toArray();
        // var_dump($Model);
        // var_dump($arr);
        // echo  '转换完的数组长度： ' . count($arr);
        //$this -> SayLog('尝试输出： ' , $Model);
        $this->assign('Model', $Model);
        $this->assign('ProvinceList', \app\Models\Area_ProvinceT::select());        
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
        $DB= new \app\Models\Biz_RegionAgentT();

        $Model = $DB->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        } 
        if(null ==  $Model -> CityId)
            $Model -> CityId = ($Model -> CountyId) / 100 * 100;
        if( null == $Model -> ProvinceId)
            $Model -> ProvinceId = ($Model -> CountyId) / 10000 * 10000;


        $this->assign('Model', $Model);
        return $this->fetch('add');
    }
    public function save(){
        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;
        $DB= new \app\Models\Biz_RegionAgentT();


        if(null  == $InputModel['ClientUserId'] || '' ==  trim($InputModel['ClientUserId'] ) || '0' ==  $InputModel['ClientUserId']){
            return $this->SendJErr('客户Id 不能为空');
        }   


        if(null  == $InputModel['CommenceTime'] || '' ==  trim($InputModel['CommenceTime'])){
            unset($InputModel['CommenceTime']);
        }   
        if(null  == $InputModel['ExpireStopTime'] || '' ==  trim($InputModel['ExpireStopTime'])){
            unset($InputModel['ExpireStopTime']);
        }   
        if(null  == $InputModel['InaugurateTime'] || '' ==  trim($InputModel['InaugurateTime'])){
            unset($InputModel['InaugurateTime']);
        }   
        // echo '准备保存的数据： ' . ($InputModel['ClientUserId'] == '0' ? 'true' : 'false');
        // dump($InputModel );
        // die();
        
        $user = \app\Models\Client_UserT::get($InputModel['ClientUserId']);
        if(!$user){
            return $this->SendJErr('客户Id不存在');
        }
        if( 0 == $Id    ){
            $ExistModel = $DB->where(function($queyr) use($InputModel,$Id) {
                $queyr->where('ClientUserId','=',$InputModel['ClientUserId']) -> whereOr('CountyId','=',$InputModel['CountyId']) ;


            })->find();
            if($ExistModel){
               return $this->SendJErr('不能添加重复数据');
            }

        }



        if($Id > 0){        
            $InputModel['UpdateTime'] = date('Y-m-d H:i:s');
            $DB->save($InputModel,['Id'=>$Id]);
        }else{       
            $InputModel['CreateTime'] = date('Y-m-d H:i:s');
            $DB->save($InputModel);
        }
        $user-> IsRegionAgent = true ;

        $user->save();

        return $this->SendJOk('保存成功');
    }
    public function del(){
        if(!input('?Id')){
            return  $this->SendJErr('参数错误');  
        }
        $Product_ClassT= new \app\Models\Biz_RegionAgentT();
        $Id = input('Id',0);
        $Model = $Product_ClassT->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->SendJErr('参数错误');
        }   

        $Product_ClassT->where(['Id'=>$Id])->delete();
        

        return $this->SendJOk('删除成功');
    }
    public function test1()
    {
        $User= new \app\admin\model\Sys_User();
        $op  = new \app\Models\SysOptions();
        $Product_ClassT= new \app\Models\Biz_RegionAgentT();

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
