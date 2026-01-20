<?php
namespace app\comm\Token;
use think\Exception;
use app\utils\GeneralTool;

class TokenMng
{   
    private static $instance = null;



    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
    throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance() {
        if (self::$instance === null) {
        self::$instance = new self();
        }
        return self::$instance;
    }
    // 如果需要获取实例的便捷方法，可以添加如下方法
    public static function getMy() {
        return self::getInstance();
    }
    public static function getIns() {
        return self::getInstance();
    }


    public  $CacheExpire = 3*60*60; // 3小时过期
    protected $TokenPrex = "Token:"; // token 前缀

    protected function  GetTokenKey($token){
        return $this->TokenPrex . $token;
    }   

    public function Add($tokenItem){
        $Token = $this->GetNewToken();
        $tokenItem->Token = $Token;
        $this->SetToken($Token,$tokenItem);
        return $tokenItem;  
    }
    public function GetToken($key)
    {
        $Token = \think\facade\Cache::get($this->GetTokenKey($key));
        return $Token;
    }
   
    public function DelToken($key)
    {
        $Token = \think\facade\Cache::delete($this->GetTokenKey($key));
        return $Token;
    }

    public function SetToken($key,$value)
    {
        $Token = \think\facade\Cache::set($this->GetTokenKey($key),$value,$this->CacheExpire); // 3小时过期
        return $Token;
    }
    public function GetNewToken(){
        $gid =  GeneralTool::CreateGuid();
        $Key = md5($gid);
        $this -> Log('GetNewToken  gid : ' . $gid);

        $this -> Log('GetNewToken  Key : ' . $Key);

        $flag = \think\facade\Cache:: has($this->GetTokenKey($Key));

        $this -> Log('GetNewToken  flag : ' . $flag);

        // $exist = $this->GetToken($Key);
        // $this -> Log('GetNewToken  exist : ' , $exist);

        if($flag){
            return $this->GetNewToken();
        }
        // $exist = $this->GetToken($Key);
        // if($exist != null){
        //     return $this->GetNewToken();
        // }   
        return $Key;     
    }



    protected function Log($msg){
        \think\facade\Log::record($msg);
    }   
    protected function Log2($msg,$model){
        \think\facade\Log::record($msg .'\n'  . json_encode($model)  )  ;
    }   
    



    
}
?>