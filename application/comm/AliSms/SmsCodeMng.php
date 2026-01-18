<?php
namespace app\comm\AliSms;


use think\Exception;
use app\utils\GeneralTool;
use \think\facade\Cache;

class SmsCodeMng
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


    public  $CacheExpire = 5*60; // 5分钟过期

    protected $CachePrex = "Client:Sms:VerifyCode:"; // token 前缀

   



    protected function _Init(){
        $this->_InitData();
    }
    protected function _InitData(){
        
        //$redis -> expire($this->CachePrex, $this->CacheExpire);
    }


    protected function GetRedis(){

        $cache = Cache::init();
        // 获取缓存对象句柄
        $handler = $cache->handler();
        return $handler;

    }

    public function Reload(){
        $this->_InitData();
    }
    // public function Get($phone,$code)
    // {
    //     $Token = \think\facade\Cache::hget($this->CachePrex , $key);
    //     return $Token;
    // }

    public function Set($phone,$code)
    {
        $Key1 =  $this->CachePrex .'SendFlag:'. $phone;
        $Key2 =  $this->CachePrex .'Code:'. $phone;
        
        \think\facade\Cache::set($Key1,$code,60); // 1分钟之内只能发送一次

        \think\facade\Cache::set($Key2,$code,$this->CacheExpire); // 5分钟过期
    }


    public function HasSend($phone){
        $FullKey =  $this->CachePrex .'SendFlag:'. $phone;
        $flag = \think\facade\Cache:: has($FullKey);
        return $flag;
    }

    public function GetCode($phone)
    {
        $FullKey =  $this->CachePrex .'Code:'. $phone;
        $code = \think\facade\Cache:: get($FullKey);
        return $code;
    }

    public function NewCode($phone)
    {
        $FullKey =  $this->CachePrex .'Code:'. $phone;
        $ExistCode = \think\facade\Cache:: get($FullKey);
        $randomNumber = random_int(100000, 999999);
        $NewCode = strval($randomNumber);
        if($NewCode == $ExistCode)
            return $this->NewCode($phone);       
        
        $this-> Set($phone,$NewCode);

        return $NewCode;
    }




    protected function Log($msg){
        \think\facade\Log::record($msg);
    }   
    protected function Log2($msg,$model){
        \think\facade\Log::record($msg .'\n'  . json_encode($model)  )  ;
    }   
    



    
}
?>