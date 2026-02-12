<?php

namespace app\Comm;

use app\utils\GeneralTool;
use think\Exception;
use think\facade\Cache;

class WxTokenMng
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

    protected $CachePrex = "WxToken"; // token 前缀 使用了tp的缓存管理，已经有了默认前缀 yxjk:Sys:


    protected function _Init(){
        $this->_InitData();
    }
    protected function _InitData(){
//        $this -> DelToken();
//        $tk = $this -> GetNewToken();
//        $this -> SetToken($tk);
    }


    protected function GetRedis(){
        $cache = Cache::init();
        // 获取缓存对象句柄
        $handler = $cache->handler();
        return $handler;

    }

    public  function SetToken($value, $expire = -1 )
    {
        if(-1 == $expire){
            $expire = $this->CacheExpire;
        }
        $Token = \think\facade\Cache::set($this->CachePrex,$value,$this->CacheExpire); // 3小时过期
        return $Token;
    }
    public  function GetToken(){
        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }
        $tk = \think\facade\Cache::get($this->CachePrex);
        if(!$tk){
            fwrite(STDOUT, "缓存不存在，重新请求 wx token \n");
            $this-> Log('缓存不存在，重新请求 wx token');
            $tk = $this -> GetNewToken();
            $this -> SetToken($tk);
        }
        return $tk;
    }
//    public function DelToken()
//    {
//        $Token = \think\facade\Cache::delete($this->CachePrex);
//        return $Token;
//    }
//




    public function Test($key,$value  )
    {

        $redis = $this->GetRedis();
        $redis -> hset($this->CachePrex , $key,$value);
        $val = $redis -> hget($this->CachePrex , $key);
        return $val;


    }

    protected function GetNewToken(){
        $appid='wxd0e490e1de04f720';
        $secret='e50ef7ae53a096c863c1b083f82c92a4';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;



        $res = $this->getFileCurlTwo($url);
        $this -> Log('微信获取token返回： $ res :', $res);
        $jsoninfo = json_decode($res, true);
        $AccessToken =  $jsoninfo['access_token'];
        $expire = $jsoninfo['expires_in'];  //过期时间，微信 按秒计算
        $expire = intval( $expire )- 50;
        $this -> CacheExpire = $expire;

        return $AccessToken;
    }

    function getFileCurlTwo($url)
    {
        //设置Header头
        $header[] = "Accept: application/json";
        // $header[] = "Accept-Encoding: gzip";
        //添加HTTP header头采用压缩和GET方式请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt($ch,CURLOPT_ENCODING , "gzip");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    protected function Log($msg){
        \think\facade\Log::record($msg);
    }
    protected function Log2($msg,$model){
        \think\facade\Log::record($msg .'\n'  . json_encode($model)  )  ;
    }





}
?>