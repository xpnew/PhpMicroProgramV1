<?php
namespace app\Comm;


use think\Exception;
use app\utils\GeneralTool;
use \think\facade\Cache;

class SysSetCacheMng
{   
    private static $instance = null;








    private function __construct() {


        $this->_Init();
    }
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

    protected $CachePrex = "yxjk:Sys:Setting"; // token 前缀


    protected function _Init(){
        $this->_InitData();
    }
    protected function _InitData(){
        $db =  new \app\Models\Sys_SettingT();
        $list = $db -> where([ ['Id','>',0] ]) -> select();
        $redis = $this->GetRedis();
        $redis-> del($this->CachePrex);
        //$redis -> hmset($this->CachePrex , $list);

        foreach($list as $item){
            $redis -> hset($this->CachePrex , $item['Code'],$item['Val']);
        }   
        $redis -> expire($this->CachePrex, $this->CacheExpire);
    }


    protected function GetRedis(){

        $cache = Cache::init();
        // 获取缓存对象句柄
        $handler = $cache->handler();
        return $handler;

    }
    protected function RefrushExpire(){
        $redis = $this->GetRedis();
        $redis -> expire($this->CachePrex, $this->CacheExpire);
    }

    public function Reload(){
        $this->_InitData();
    }
    public function GetSet($key)
    {
        $Token = \think\facade\Cache::hget($this->CachePrex , $key);
        $this -> RefrushExpire();
        return $Token;
    }

    public function GetDecimal($key, $def = 0.0){
        $val = $this -> GetSet($key);
        if(null == $val || '' == $val){
            return $def;
        }
        if(is_numeric($val)){
            return  floatval($val);
        }
        return $val;
    }
    //获取一个项的百分比结果

    /** 获取一个项的百分比结果
     * @param $key 配置项
     * @param $def 默认值（70％填70）
     * @return float
     */
    public  function GetPercentage($key, $def = 0.0){
        $val =  $this -> GetDecimal($key, $def);
        return $val * 0.01;
    }
    public function GetInt($key, $def = 0){
        $val = $this -> GetSet($key);
        if(null == $val || '' == $val){
            return $def;
        }
        if(is_numeric($val)){
            return  intval($val);
        }
        return $val;
    }


public function Set($key,$value)
{
    $Token = \think\facade\Cache::hset($this->CachePrex , $key,$value); // 3小时过期
    $this -> RefrushExpire();
    return $Token;
}


public function Test($key,$value  )
{

    $redis = $this->GetRedis();
    $redis -> hset($this->CachePrex , $key,$value);
    $val = $redis -> hget($this->CachePrex , $key);
    return $val;


}


    protected function  GetTokenKey($token){
        return $this->CachePrex . $token;
    }   

    public function Add($tokenItem){
        $TokenKey = $this->GetNewToken();
        $tokenItem->Token = $TokenKey;
        $this->SetToken($TokenKey,$tokenItem);
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