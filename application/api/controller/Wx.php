<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use \think\facade\Cache;

class Wx extends ApiBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return 'Wx api test ok';
    }

    protected  $WxTokenKey = 'Wechat:AccessToken';
    protected function GetRedis(){

        $cache = Cache::init();
        // 获取缓存对象句柄
        $handler = $cache->handler();
        return $handler;

    }
    public function GetToken(){
        $Token = \think\facade\Cache::get($this->WxTokenKey);

        if($Token == null){
           $Token = $this->GetNewToken();
        }
        $redis =  $this->GetRedis();
        $Fullkey =  config('cache.prefix'   ) . $this->WxTokenKey;

        $expire  =  $redis -> ttl($Fullkey);
        if(0 > $expire ){
           $Token = $this->GetNewToken();
            $expire  =  $redis -> ttl($Fullkey);
        }
        if(30 > $expire ){
          $Token =  $this->GetNewToken();
           $expire  =  $redis -> ttl($Fullkey);
        }

        $model  =  new  \stdClass;
        // $obj2 = new class{}; // Instantiate anonymous class
        // $obj3 = (object)[]; // Cast empty array to object
        $model -> AccessToken = $Token;
        $model -> Expire = $expire;
        
        return $this -> SendJOk('获取成功',1, $model );
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function GetNewToken()
    {
        $appid='wxd0e490e1de04f720';
        $secret='e50ef7ae53a096c863c1b083f82c92a4';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret; 


        $res = $this->getFileCurlTwo($url);
        $this -> SayLog('微信获取token返回： $ res :', $res);
        $jsoninfo = json_decode($res, true);
        $AccessToken =  $jsoninfo['access_token'];
        $expire = $jsoninfo['expires_in'];  //过期时间，微信 按秒计算
        $expire = intval( $expire )- 50;        
        $Token = \think\facade\Cache::set('Wechat:AccessToken',$AccessToken, $expire); //  微信默认 2小时过期

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


    public function GetOpenId(){
        $Code = input('Code');
        if( !$Code ){
            return $this -> SendJErr('参数错误');
        }

        $appid='wxd0e490e1de04f720';
        $secret='e50ef7ae53a096c863c1b083f82c92a4';
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret ."&js_code=". $Code ."&grant_type=authorization_code"; 

        $res = $this->getFileCurlTwo($url);
        $this -> SayLog('微信获取open id 返回： $ res :', $res);
        $jsoninfo = json_decode($res, true);  
        $OpenId =  $jsoninfo['openid'];
        if( isset($jsoninfo['errcode']) && $jsoninfo['errcode'] != 0 ){
            return $this -> SendJErr('获取失败:' . $jsoninfo['errmsg']);
        }
        $Return = [];
        $Return['OpenId'] = $OpenId;
        $Return['SessionKey'] = $jsoninfo['session_key'];
        if( isset( $jsoninfo['unionid'] ) ){
             $Return['UnionId'] = $jsoninfo['unionid'];

        }        
        return $this -> SendJOk('获取成功',1, $Return );      

    }


}