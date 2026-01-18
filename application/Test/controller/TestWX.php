<?php
namespace app\Test\controller;
use think\Controller;
use think\facade\Log;


class TestWx extends Controller
{
    public function index()
    {
        return 'Hello,This is TestWx module.Test01.';
    }

    public function test1()
    {
        $appid='wxd0e490e1de04f720';
        $secret='e50ef7ae53a096c863c1b083f82c92a4';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret; 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        echo $jsoninfo['access_token'];
        echo '<br/>';
        echo $jsoninfo['expires_in'];
        echo '<br/>';
        echo $jsoninfo['errcode'];
        echo '<br/>';
        echo $jsoninfo['errmsg'];
        echo '<br/>';
        echo $jsoninfo['access_token'];
        echo '<br/>';
        echo $jsoninfo['expires_in'];
        echo '<br/>';
        echo $jsoninfo['errcode'];
        echo '<br/>';
        echo $jsoninfo['errmsg'];
        echo '<br/>';
        echo $jsoninfo['access_token'];
        echo '<br/>';
        echo $jsoninfo['expires_in'];
        echo '<br/>';
        echo $jsoninfo['errcode'];
        echo '<br/>';
        echo $jsoninfo['errmsg'];
        echo '<br/>';


        
      return ' TestWx test1';
    }   



    public function test2()
    {
        $appid='wxd0e490e1de04f720';
        $secret='e50ef7ae53a096c863c1b083f82c92a4';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret; 

        // $cc =  getFileCurlTwo($url);
        $res = $this->getFileCurlTwo($url);


        Log::record('日志输出：   res=' . json_encode($res)  );
        
        // $this -> SayLog(' $res :', $res);
        $jsoninfo = json_decode($res, true);
        $AccessToken =  $jsoninfo['access_token'];
        $expire = $jsoninfo['expires_in'];  //过期时间，微信 按秒计算
        // Log::record('日志输出：   AccessToken=' . $AccessToken . ' expire=' . $expire  );

        $expire = intval( $expire )- 100;
        // $Token = \think\facade\Cache::set('Wechat:AccessToken',$AccessToken,7200); // 3小时过期
        // $Token = \think\facade\Cache::get('Wechat:AccessToken');
        $Token = \think\facade\Cache::set('Wechat:AccessToken',$AccessToken, $expire); //  微信默认 2小时过期

        return $res;

    }

    public function TestGetConfig(){
        $Prefix =  config('cache.prefix'   );

        return $Prefix;

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


}
