<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

use think\facade\Log;
use \app\Models\Client_User_View as ClientView;

use \app\Models\Client_UserT;
use \app\utils\GeneralTool;


class ClientUser extends AdminBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this -> _InitViewData();
        $MakeingLvList  =  \app\Models\Biz_MarketingLevelT::select();
        $this->assign('MakeingLvList', $MakeingLvList);


        return $this->fetch();
    }
    protected function _InitViewData()
    {
        parent::_InitViewData();
        $this->assign('title', '客户列表');


        // $this->assign('ProductClassList', \app\Models\Client_UserT::select());

    }
    public function query(){
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
            $where[] = ['RegisterDate','<',$EndTime ];
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

        $Client_UserT= new \app\Models\Client_User_View();

        $data = $Client_UserT -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $Client_UserT -> where($where) -> count();
        //return $this->SendQOk2('查询成功',$this->RecordCount,$data); //  查询返回  layer 专用的消息格式 QueryMsg
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
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
        $DB= new \app\Models\Client_UserT();
        //dump($InputModel);
        // if (array_key_exists('RegisterDate', $InputModel)) {
        //     unset($InputModel['RegisterDate']);
        // }


        
        if( 0 ==  $InputModel['IsDelFlag'] ){
            $InputModel['DelTime'] = null;

        }   else{
            $InputModel['DelTime'] = date('Y-m-d H:i:s');
        }

        // foreach ($SkipFields as $field) {
        //     echo $field . "\n";
        // }    

        // echo   "\n============\n";

        // foreach($SkipFields as $key => $value){
        //      echo $key . " => " . $value . "\n";
        // }
        // echo   "\n============\n";
        // 注意：这种写法 fkey 是索引 0~n fvalue 是字段名称
        // foreach($SkipFields as $key => $value){
        //     if(array_key_exists( $value, $InputModel)){
        //         unset( $InputModel[ $value ]);
        //     }
        // }
        $SkipFields = [ 'RegisterDate',   'RegisterIp', 'RegPlatform', 'LoginDate', 'LogoutDate','OldLoginIp', 'Password', 'CreateTime' ,'FirstLoginTime' ,'SignoutTime' ];        
        $InputModel = $this->RemoveFields($InputModel, $SkipFields);    


        // dump($InputModel);
        // die();
        if($Id > 0){        
            $DB->save($InputModel,['Id'=>$Id]);
        }else{       
            // $DB->save($InputModel);
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
        $Client_UserT= new \app\Models\Client_UserT();
        $Model = $Client_UserT->where(['Id'=>$id])->find();
        if(!$Model){
            return $this->SendJErr('用户不存在');
        }
        return $this->SendJOk('查询成功',1,$Model);
    }   
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $this -> _InitViewData();
        if(!input('?id')){
            return $this->error('参数错误');
        }

        $Id = input('id',0);
        $db= new \app\Models\Client_UserT();


        $Model = $db->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }   
        $this-> SayLog('尝试输出： ' , $Model);

        $this->assign('Model', $Model);
        $this->assign('ProductClassList', \app\Models\Product_ClassT::select());
        $MakeingLvList  =  \app\Models\Biz_MarketingLevelT::select();
        $this->assign('MakeingLvList', $MakeingLvList);


        return $this->fetch();
    }

    public function OptLevel(){
        $InputModel = $this->request->post();

        if( !isset($InputModel['MakerLevelId'])   ){
            return $this->SendJErr('参数错误: MakerLevelId');
        }

        if( !isset($InputModel['ClientUserId'])   ){
            return $this->SendJErr('参数错误: ClientUserId');
        }

        $ClientUserId = isset($InputModel['ClientUserId']) ? intval($InputModel['ClientUserId']) : 0;

        $ExistUser =  Client_UserT::get($ClientUserId );
        if(!$ExistUser){
            return $this->SendJErr('用户不存在');
        }   


        $ExistUser['MakerLevelId'] = $InputModel['MakerLevelId'];
        

        // $InputModel['ChangePoints'] = $ChangePoints;
        // $InputModel['NewPoints'] = $NewPoints;
        // $InputModel['OldPoints'] = $OldPoints;


        // $InputModel['CreateTime'] = date('Y-m-d H:i:s');
        // $InputModel['AssetModeId'] =90008000;
        // $InputModel['AssetTypeId'] =80007000;
        // $InputModel['AssetStatusId'] =81005000;

        // $InputModel['ClientRealName'] =  $ExistUser-> RealityName;
        // $InputModel['ClientNickName'] =   $ExistUser-> NickName;
        // $InputModel['ClientPhone'] =   $ExistUser-> Mobile ;

        // $InputModel['Rmk']  =  GeneralTool::PushRmk($InputModel['Rmk'], ($InputModel['ChangeType'] == -1? '扣除' : '增加') . ' [积分]' . $ChangePoints );

        // $this -> SayLog('积分操作： ' , $InputModel);
        // $DB->save($InputModel);

        $ExistUser->save();
        $ResultData = [
            "MakerLevelId" => $InputModel['MakerLevelId'],
        ];
        return $this->SendJOk('保存成功',1, $ResultData);
    } // OptLevel end 
    public function OptPoints(){
        $InputModel = $this->request->post();

        if( !isset($InputModel['Points'])   ){
            return $this->SendJErr('参数错误: Points');
        }
        if( !isset($InputModel['ChangeType'])   ){
            return $this->SendJErr('参数错误: ChangeType');
        }
        if( !isset($InputModel['ClientUserId'])   ){
            return $this->SendJErr('参数错误: ClientUserId');
        }

        $ClientUserId = isset($InputModel['ClientUserId']) ? intval($InputModel['ClientUserId']) : 0;

        $ExistUser =  Client_UserT::get($ClientUserId );
        if(!$ExistUser){
            return $this->SendJErr('用户不存在');
        }
    
        $DB= new \app\Models\Client_PointLogT();

        $OldPoints =  $ExistUser['PointsBalance'];
        $ChangePoints = $InputModel['Points'];

        $ChangePoints  = abs( $ChangePoints  );

        if($InputModel['ChangeType'] == -1){
            // if($OldPoints < $ChangePoints){
            //     return $this->SendJErr('积分不足');
            // }
            $ChangePoints= -1 * $ChangePoints;
        }


        if(null ==$OldPoints){
            $OldPoints = 0;
        }
        if(null == $ExistUser['PointsHistory']){
            $ExistUser['PointsHistory'] = 0;
        }

        $NewPoints = $OldPoints + $ChangePoints;

        $ExistUser['PointsBalance'] = $NewPoints;
        $ExistUser['PointsHistory'] =$ExistUser['PointsHistory'] + $ChangePoints;
        

        $InputModel['ChangePoints'] = $ChangePoints;
        $InputModel['NewPoints'] = $NewPoints;
        $InputModel['OldPoints'] = $OldPoints;


        $InputModel['CreateTime'] = date('Y-m-d H:i:s');
        $InputModel['AssetModeId'] =90008000;
        $InputModel['AssetTypeId'] =80007000;
        $InputModel['AssetStatusId'] =81005000;

        $InputModel['ClientRealName'] =  $ExistUser-> RealityName;
        $InputModel['ClientNickName'] =   $ExistUser-> NickName;
        $InputModel['ClientPhone'] =   $ExistUser-> Mobile ;

        $InputModel['Rmk']  =  GeneralTool::PushRmk($InputModel['Rmk'], ($InputModel['ChangeType'] == -1? '扣除' : '增加') . ' [积分]' . $ChangePoints );

        $this -> SayLog('积分操作： ' , $InputModel);
        $DB->save($InputModel);

        $ExistUser->save();
        $ResultData = [
            "OldPoints" => $OldPoints,
            'PointsBalance' => $NewPoints,
            'ChangeType' => $InputModel['ChangeType'],
            'InputPoints' => $InputModel['Points'],
            'PointsHistory' => $ExistUser['PointsHistory'],
            'ChangePoints' => $InputModel['ChangePoints'],
        ];
        return $this->SendJOk('保存成功',1, $ResultData);
    } // OptPoints end 
    public function OptBonus(){
        $InputModel = $this->request->post();

        if( !isset($InputModel['Bonus'])   ){
            return $this->SendJErr('参数错误: Bonus');
        }
        if( !isset($InputModel['ChangeType'])   ){
            return $this->SendJErr('参数错误: ChangeType');
        }
        if( !isset($InputModel['ClientUserId'])   ){
            return $this->SendJErr('参数错误: ClientUserId');
        }
        if( !isset($InputModel['AssetTypeId'])   ){
            return $this->SendJErr('参数错误: AssetTypeId');
        }

        $ClientUserId = isset($InputModel['ClientUserId']) ? intval($InputModel['ClientUserId']) : 0;

        $AssetTypeId = isset($InputModel['AssetTypeId']) ? intval($InputModel['AssetTypeId']) : 0;
        $ExistUser =  Client_UserT::get($ClientUserId );
        if(!$ExistUser){
            return $this->SendJErr('用户不存在');
        }
    
        $DB= new \app\Models\Client_BonusLogT();
        $OldBonus =  $ExistUser['BonusBalance'];
        $HistoryBouns =  $ExistUser['BonusHistory'];


        if(80001000 == $AssetTypeId)    {
        }
        else if(80002000 == $AssetTypeId)    {
            $OldBonus =  $ExistUser['ScoreBalance'];
            $HistoryBouns =  $ExistUser['ScoreHistory'];

        }else{
            return $this->SendJErr('参数错误 错误的类型:'.$AssetTypeId);
        }


        $ChangeBonus = $InputModel['Bonus'];

        $ChangeBonus  = abs( $ChangeBonus  );

        if($InputModel['ChangeType'] == -1){
            // if($OldBonus < $ChangeBonus){
            //     return $this->SendJErr('积分不足');
            // }
            $ChangeBonus= -1 * $ChangeBonus;
        }


        if(null ==$OldBonus){
            $OldBonus = 0;
        }
        if(null == $HistoryBouns){
            $HistoryBouns = 0;
        }

        $NewBonus = $OldBonus + $ChangeBonus;


        if(80001000 == $AssetTypeId)    {
            $ExistUser['BonusBalance'] = $NewBonus;
            $ExistUser['BonusHistory'] =$HistoryBouns+ $ChangeBonus;
        }
        else if(80002000 == $AssetTypeId)    {
            $ExistUser['ScoreBalance'] = $NewBonus;
            $ExistUser['ScoreHistory'] =$HistoryBouns + $ChangeBonus;
        }        

        $InputModel['ChangeBonus'] = $ChangeBonus;
        $InputModel['NewBonus'] = $NewBonus;
        $InputModel['OldBonus'] = $OldBonus;


        $InputModel['CreateTime'] = date('Y-m-d H:i:s');
        $InputModel['AssetModeId'] =90008000;
        $InputModel['AssetTypeId'] =$AssetTypeId;
        $InputModel['AssetStatusId'] =81005000;

        $InputModel['ClientRealName'] =  $ExistUser-> RealityName;
        $InputModel['ClientNickName'] =   $ExistUser-> NickName;
        $InputModel['ClientPhone'] =   $ExistUser-> Mobile ;

        $InputModel['Rmk']  =  GeneralTool::PushRmk($InputModel['Rmk'], ($InputModel['ChangeType'] == -1? '扣除' : '增加') . ' [积分]' . $ChangeBonus );

        $this -> SayLog('奖金操作： ' , $InputModel);
        $DB->save($InputModel);

        $ExistUser->save();
        $ResultData = [
            "OldBonus" => $OldBonus,
            'BonusBalance' => $NewBonus,
            'ChangeType' => $InputModel['ChangeType'],
            'AssetTypeId' => $InputModel['AssetTypeId'],
            'InputBonus' => $InputModel['Bonus'],
            'BonusHistory' => $HistoryBouns,
            'ChangeBonus' => $InputModel['ChangeBonus'],
        ];
        return $this->SendJOk('保存成功',1, $ResultData);
    } //OptBonus end


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
