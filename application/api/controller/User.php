<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use app\comm\Token\TokenMng;
use app\comm\Token\TokenItem;
use app\Models\Client_UserT;
use app\Models\Client_User_View  as ViewDB;

use think\Db; // 旧版本命名空间 th6 变成门面类了。


class User extends ApiBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
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

    public function NeedLogin()
    {
        // -60200591 需要登录 ,一个特殊的错误码，前端可以据此跳转到登录页面
        // 很久以前就是这么定义的了，现在已经不知道为什么了
        return $this->SendJErr('需要登录', -60200591);
    }

    public function read($id)
    {
        if( isset($id) == false || $id == null ){
            return $this->SendJErr('参数错误 , id 不存在');
        }   
        if(0 >= $id ){
            return $this->SendJErr('参数错误 , id 必须大于 0');
        }

        //return $this->SendJErr('查询成功'. $id  );

        $Client_UserT = new ViewDB();
        $data = $Client_UserT -> where(['Id'=>$id]) ->find();
        if($data == null){
            return $this->SendJErr('用户不存在');
        }
        return $this->SendJOk('查询成功',1,$data);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function Login(Request $request)
    {
        $LoginName = $request->post('LoginName','');
        $LoginPwd = $request->post('LoginPwd','');
        
        if($LoginName == '' || $LoginPwd == ''){
            return $this->SendJErr('参数错误');
        }

        $mng =  \app\comm\SysSetCacheMng::getIns();
        // if ($mng) {
        //     return json(['Type' => 0, 'Content' => $mng -> GetSet('LockLogin')]);
        // }
        $Lock =  (int)$mng -> GetSet('LockLogin'); // 获取登录锁定设置

        if (1 <= $Lock ) {
            $LockMsg =  $mng -> GetSet('LockLoginMsg')  ;
            if(null == $LockMsg || '' ==  trim( $LockMsg) ) {
                $LockMsg =  '登录已经被锁定，系统暂时无法使用！' ;
            }   
            $this->SendJErr( $LockMsg );
        }

        $Client_UserT = new Client_UserT();
        
        $data = $Client_UserT -> where('Account|Mobile|OpenId', $LoginName) ->find();

        if($data == null){
            return $this->SendJErr('用户不存在');
        }   
        if($data['Password'] != md5($LoginPwd)){
            $this->Msg->Body =  '密码错误 : ' . $data['Password'] . '  <>  ' . md5($LoginPwd) .'   source:' . $LoginPwd;
            return $this->SendJErr('密码错误');
        }
        if(0 < $data['IsDelFlag']){
            return $this->SendJErr('用户已被锁定，不能登录');
        }
        $Mng = TokenMng::getIns();
        $tokenItem = new TokenItem();
        $tokenItem -> UserId = $data['Id'];
        $tokenItem -> UserName = $data['Account'];
        $tokenItem -> RealName = $data['RealityName'];
        $tokenItem -> NickName = $data['NickName'];
        $tokenItem -> Mobile = $data['Mobile'];
        $tokenItem -> OpenId = $data['OpenId'];
        $tokenItem -> CreateTime = date('Y-m-d H:i:s');
        $tokenItem -> CreateTS = time();
        $tokenItem -> ExpireTS = $Mng -> CacheExpire; // 7 天
        $tokenItem -> ExpireTime = date('Y-m-d H:i:s',$tokenItem -> ExpireTS);
        
        $Token = $Mng ->Add($tokenItem);
        if($Token == ''){
            return $this->SendJErr('生成Token失败');
        }

        return $this->SendJOk('登录成功',1,$Token);


    }


    public function UpdateOpenId(Request $request)
    {
        $UserId =  \think\facade\Request::param('UserId');
        $OpenId = $request->post('OpenId','');
        
        if($UserId == -9999 || $OpenId == ''){
            return $this->SendJErr('参数错误');
        }
        $Client_UserT = new Client_UserT();
        $data = $Client_UserT -> where('Id', $UserId) ->find();
        if($data == null){
            return $this->SendJErr('用户不存在');
        }
        $data['OpenId'] = $OpenId;
        $data -> save();
        $this -> SayLog('User UpdateOpenId 更新后的用户数据 :', $data);
        return $this->SendJOk('更新成功',1,$data);


    }

    /// 统计订单
    public function OrderStatistics()
    { 
        $UserId = input('UserId',-9999);
       if($UserId == -9999){
            return $this->SendJErr('请先登录',-9999);
        }
        $sql = "select T0.*,IFNULL(T1.OrderCounter,0) AS OrderCounter
from 
(SELECT * FROM `Sys_TypeDefinedT` WHERE GroupId = 1000 order by `GroupOrd`) AS T0
LEFT JOIN
(select Count(*)  AS OrderCounter,  OrderStatus from Client_OrderT 
where UserId =?
group by OrderStatus
) AS T1
ON T0.TypeId =T1.`OrderStatus`  order by `GroupOrd`";

        //$Data =  Db::query("select * from think_user where id=? AND status=?", [8, 1]);
        $Data =  Db::query($sql, [$UserId]);
        $this -> SayLog('StatisticsOrder 统计订单结果 :', $Data);

        return $this->SendJOk('查询成功',1,$Data);
        



    }
    public function Test1()
    {
        // $Model =   \app\Models\Product_ClassT::create();
        $Client_UserT = new Client_UserT();

        $Model = $Client_UserT -> where('Id', 1) ->find();
        dump($Model);
        echo "<br/>";
        $Model2 =  $Client_UserT -> where('Account|Mobile|OpenId', '13678002398') ->find();
        dump($Model2);
        echo "<br/>";

        $pwd = '123456';
        echo '密码： ' . $pwd . '  md5后： ' . md5($pwd);

        echo "<br/>";

        echo 'guid: '  . \app\Utils\GeneralTool::GetGuid();
        echo "<br/>";

        echo 'guid2: '  . \app\Utils\GeneralTool::CreateGuid();
        echo "<br/>";

        return  'ok';
    }
    
}
