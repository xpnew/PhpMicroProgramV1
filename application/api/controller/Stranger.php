<?php
namespace app\api\controller;
use think\Controller;
use app\comm\CommControllerBase;
use app\comm\AliSms\SmsCodeMng;
use app\comm\AliSms\SmsUtil;

use app\comm\Token\TokenMng;
use app\comm\Token\TokenItem;
use think\facade\Config  AS SysCfg;

use app\Models\Client_UserT;

///陌生人 陌生用户相关的功能


class Stranger extends CommControllerBase
{ 



    public function index()
    {
//        return 'test sms :'. json_encode($sendSmsRequest) ;
    }  



    
    public function PhoneCheck(){
        $phone = input('phone');


        $this -> SayLog('TestSms2 PhoneCheck phone:'.$phone);

        $EnableDebug = true;

        $SmsMng = SmsCodeMng::getIns();
        $flag = $SmsMng->HasSend($phone);
        if($flag){
            return $this ->  SendJErr('请不要连续发送验证码');        
        }
        $NewCode = $SmsMng->NewCode($phone);
        $this -> SayLog('TestSms2 PhoneCheck NewCode:'.$NewCode);
        $this -> Msg -> Body = $NewCode;
    
        // return $this-> SendJOk('发送成功' );

        $AliConfig = [];


        $AliConfig['AccessSet'] = [
            'accessKeyId' =>  SysCfg::get('sms.aliyunkey1'),
            'accessKeySecret' =>  SysCfg::get('sms.aliyunkey2'),
        ];

        $AliConfig['Template'] = [
            'signName' =>  SysCfg::get('sms.aliyun_sms_templateid'),
            'templateCode' =>  SysCfg::get('sms.aliyun_sms_templatcode'),
        ];

        if($EnableDebug){

            $this -> Msg -> IsDebug =  true;

            return $this-> SendJOk('短信发送成功' . $phone,20160119 ,[]);

        }
        $Return =  SmsUtil::Send($phone,$NewCode,$AliConfig);
        $this -> SayLog('TestSms2 PhoneCheck Return:'.json_encode($Return));
        if('OK' == $Return -> code){
            $SmsMng -> Set($phone,$NewCode);

            return $this-> SendJOk('短信发送成功' . $phone );

        }else{
            return $this-> SendJErr('发送失败:' . $Return -> message) ;

        }



    }
    public function CheckExistMobile(){

        $Mobile = input('Mobile');


        $this -> SayLog('Stranger ExistMobile Mobile:'.$Mobile);


        $db =  new \app\Models\Client_UserT();

        $Model = $db->where(['Mobile'=>$Mobile])->find();
        if($Model){
            return $this-> SendJErr('手机号已存在');
        }

        return $this-> SendJOk('手机号不存在' );
    }

    public function Register(){ 
        $InputModel = $this->request->post();

        $this -> SayLog('Stranger 注册用户 传入值  $InputModel :', $InputModel);


        $Mobile = input('Mobile');
        $SmsMng = SmsCodeMng::getIns();

        $ExistCode = $SmsMng->GetCode($InputModel['Mobile']);
        if(! isset($ExistCode)) {

            return $this -> SendJErr('验证码已过期');
        }

        if($ExistCode != $InputModel['code']) {

            return $this -> SendJErr('验证码错误');
        }
        $RealityName = input('RealityName');
        $NickName = input('NickName');

        if( isset($NickName)){
            if( 120 < strlen($NickName)){
                return  $this->SendJErr('呢称最长不能超过120个字');
            }
        }

        $db =  new \app\Models\Client_UserT();

        
        $DbModel = $db->where(['Mobile'=>$Mobile])->find();
        if($DbModel){
            return  $this -> SendJErr('手机号已存在');
        }

        ///新用户默认密码
        $mng =  \app\comm\SysSetCacheMng::getIns();
        $DefaultPwd =  $mng -> GetSet('NewUserPassword');
        $RootGuiderUserId = $mng -> GetInt('RootGuiderUserId',0);
        if( !isset($DefaultPwd) || $DefaultPwd == ''){
            $DefaultPwd = '123456';
        }
        $Password = md5($DefaultPwd);

        if(null != $InputModel['Password'] && isset($InputModel['Password'])){

            $Password = $InputModel['Password'];
            $Password = trim($Password);
            if($Password != $InputModel['Password']){
                return  $this -> SendJErr('密码不能包含空格');
            }
            $Password = md5($Password);
        }

        //IP 地址：
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]); // 获取第一个IP地址，通常是用户的真实IP
        }
        $this -> SayLog('Stranger Register ip:'.$ip);
        // echo $ip;


        $InputModel['RegPlatform'] = 'WeChat';
        $InputModel['IsBindPhone'] = 1;
        $InputModel['Password'] = $Password;
        $InputModel['DbModel'] = $DbModel;
        $InputModel['NickName'] = $NickName;
        $InputModel['RealityName'] = $RealityName;

        $InputModel['RegisterIp'] = $ip;
        $InputModel['RegisterDate'] = date('Y-m-d H:i:s');
        $InputModel['LoginDate'] = date('Y-m-d H:i:s');
        $InputModel['CreateTime'] = date('Y-m-d H:i:s');


        $InputModel['UserLevel'] = 0;
        $InputModel['AdminLevel'] = 0;
        $InputModel['VipLevel'] = 0;
        $InputModel['VipExp'] = 0;
        $InputModel['MakerLevelId'] = 0;
        $InputModel['IsRegionAgent'] = 0;
        $InputModel['IsPromote'] = 0;
        $InputModel['IsDelFlag'] = 0;

        $InputModel['GuiderBonus'] = 0;
        $InputModel['ManageBonus'] = 0;


        $InputModel['ShareholderLevel'] = 0;
        $InputModel['BuyTimes'] = 0;
        $InputModel['HisMonetary'] = 0;
        $InputModel['IsReality'] = 0;


        $InputModel['PointsHistory'] = 0;
        $InputModel['PointsBalance'] = 0;
        $InputModel['PointsFrozen'] = 0;
        $InputModel['BonusHistory'] = 0;
        $InputModel['BonusBalance'] = 0;
        $InputModel['BonusFrozen'] = 0;
        $InputModel['ScoreHistory'] = 0;
        $InputModel['ScoreBalance'] = 0;
        $InputModel['ScoreFrozen'] = 0;
        $InputModel['WithdrawHistory'] = 0;


        if(0 < $RootGuiderUserId){
            if(null  ==  $InputModel["GuiderUserId"] || ! isset($InputModel["GuiderUserId"])){
                $InputModel['GuiderUserId'] = $RootGuiderUserId;
            }
        }


        $this -> SayLog('Stranger 注册用户 准备保存的值   $InputModel :', $InputModel);
        
        
        // return $this -> SendJErr('测试接口（不保存）');
        
        //$db -> save($InputModel);
        $NewUser =  Client_UserT::create($InputModel);

        $this -> SayLog('$NewUser: ', $NewUser);

        $TkMng = TokenMng::getIns();
        $tokenItem = new TokenItem();
        $tokenItem -> UserId = $NewUser -> Id;
        $tokenItem -> NickName = $NewUser -> NickName;
        $tokenItem -> RealName = $NewUser -> RealityName;
        $tokenItem -> Mobile = $NewUser -> Mobile;

        if(isset($NewUser -> OpenId))
            $tokenItem -> OpenId = $NewUser -> OpenId;

        $tokenItem -> CreateTime = date('Y-m-d H:i:s');
        $tokenItem -> CreateTS = time();
        $tokenItem -> ExpireTS = $TkMng -> CacheExpire; // 7 天
        $tokenItem -> ExpireTime = date('Y-m-d H:i:s',$tokenItem -> ExpireTS);
        
        $Token = $TkMng ->Add($tokenItem);
        if($Token == ''){
            return $this->SendJErr('生成Token失败');
        }
        // var postdata = {
        //     "Mobile": this.formData.phone,
        //     "code": this.formData.code,
        //     "RegArea": this.formData.AreaCode,
        //     "RegAreaName": this.formData.phone,
        //     "Address": this.formData.province +  ' ' +  this.formData.city +  ' '  + this.formData.district +  ' '  + this.formData.address,			

        // }
			    
        return $this-> SendJOk('注册成功',1,$Token );



    }


}



?>