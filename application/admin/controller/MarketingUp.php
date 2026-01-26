<?php

namespace app\admin\controller;
use think\Controller;
use think\Request;

use think\facade\Log;
use \app\Models\Client_User_View as ClientView;

use \app\Models\Client_UserT;
use \app\utils\GeneralTool;
use think\Db; // 旧版本命名空间 th6 变成门面类了。

class MarketingUp extends ClientUser
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
//    public function index()
//    {
//        $this -> _InitViewData();
//        $MakingLvList  =  \app\Models\Biz_MarketingLevelT::select();
//        $this->assign('MakingLvList', $MakingLvList);
//
//
//        return $this->fetch();
//    }
//    protected function _InitViewData()
//    {
//        parent::_InitViewData();
//        $this->assign('title', '代理升级');
//
//
//        // $this->assign('ProductClassList', \app\Models\Client_UserT::select());
//
//    }

    public function query(){
        $data =[  ];
        $BeginTime = input('BeginTime','');
        $EndTime = input('EndTime','');

        $Account = input('Account','');
        $Mobile = input('Mobile','');
        $NickName = input('NickName','');
        $RealityName = input('RealityName','');

        $MakerLevelId = input('MakerLevelId','');
        $ParentMarkerId = input('ParentMarkerId','');


        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量


        $where = [];
        if($NickName != ''){
            $where[] = ['NickName','like','%'.$NickName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        if(null == $ParentMarkerId || ! isSet($ParentMarkerId)){

            return $this->SendQOk("请指定一个目标级别",1,null);
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
         if($MakerLevelId != ''){
             $where[] = ['MakerLevelId','=',$MakerLevelId];
         }


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

        $sql = "SELECT * FROM Client_User_View AS T0
INNER JOIN
(
select GuiderUserId AS ParentMarkerId, Count(*) AS SonMarkerNum  from  Client_UserT
WHERE MakerLevelId =?
GROUP BY GuiderUserId
)  AS T1
ON T0.Id = T1.ParentMarkerId  order by T0.Id DESC";


         // 1. 参数准备


        // 2. 构建子查询 (注意安全过滤)
         $subQuery = "(SELECT GuiderUserId AS ParentMarkerId, COUNT(*) AS SonMarkerNum 
                     FROM Client_UserT 
                     WHERE MakerLevelId = " . intval( $ParentMarkerId) . " 
                     GROUP BY GuiderUserId)";

        // 3. 组合查询
         $data = Db::table('Client_User_View')
                ->alias('T0')
                // 1. 指定精确字段：T0的所有字段 + T1的SonMarkerNum
                // 这样写最清晰，避免字段歧义
                ->field('T0.*')
                // 2. 关联子查询
                // 注意：view方法的第二个参数是'inner'，第四个参数是关联条件
                ->view([ $subQuery => 'T1'], 'SonMarkerNum', 'T0.Id = T1.ParentMarkerId', 'inner')
                // ★ where 是附加条件
                -> where($where)
                // 3. 多字段排序 (字符串写法)
                ->order('T1.SonMarkerNum DESC, T0.Id DESC')
                // 4. 分页限制
                ->limit( ( $PageIndex-1) * $PageSize, $PageSize)
                // 执行查询
                ->select();

        // 4. 获取总记录数 (用于分页)
        // 注意：这里为了准确，通常建议使用 count(*)，ThinkPHP会自动处理
         $totalCount = Db::table('Client_User_View')
                    ->alias('T0')
                    ->view([ $subQuery => 'T1'], '', 'T0.Id = T1.ParentMarkerId', 'inner')
                    // ★ where 是附加条件
                    -> where($where)
                    ->count(); // 获取总条数
        $this->RecordCount = $totalCount;

        // 返回数据
//        $this->RecordCount = $Client_UserT -> where($where) -> count();
        //return $this->SendQOk2('查询成功',$this->RecordCount,$data); //  查询返回  layer 专用的消息格式 QueryMsg
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
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

}