<?php
namespace app\Test\controller;
use app\Comm\CommControllerBase;
use app\utils\UserQrBuilder;
use think\Controller;
use think\facade\Log;
use \think\facade\Cache;

class TestWX extends CommControllerBase
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



    public function test3(){

        echo '<br/>';
        echo  $_SERVER['DOCUMENT_ROOT'];
        echo '<br/>';
        $Src =  '/UserQr/96.png';
        $RealPath = \app\utils\PathConverter::ToStoragePath($Src);
        echo '<br/>';
        echo $RealPath;
        echo '<br/>';
        echo $Src;
        echo '<br/>';

    }

    public function TestTkMng()
    {
        $Mng =  \app\Comm\WxTokenMng::getIns();

        echo '<br /> ===================<br/>';
        echo $Mng-> GetToken();
        echo '<br /> ===================<br/>';
        dump($Mng-> GetToken());
        echo '<br /> ===================<br/>';

    }
    /**
     * 获取微信小程序二维码 (接口B)
     * 适用于需要的码数量较少的情况
     */

    function getWxaQrcode($accessToken, $scene, $page = '', $width = 430)
    {
        // 1. 构建请求URL
        $url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token={$accessToken}";

        // 2. 准备POST数据
        // 注意：接口B仅支持path参数，不支持scene参数（scene用于接口C）
        // 这里的scene参数实际上是作为path的一部分传入的，或者你需要直接传入完整的path
        // 如果是简单的path，直接传入path即可
        $data = [
            "path" => $scene, // 例如：pages/index/index?query=1
            "width" => $width
        ];

        // 3. 初始化cURL
        $ch = curl_init();

        // 4. 设置cURL参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略SSL证书验证（生产环境建议开启）
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // 5. 执行请求
        $result = curl_exec($ch);

        // 6. 检查错误
        if (curl_errno($ch)) {
            return ['error' => 'cURL Error: ' . curl_error($ch)];
        }

        // 7. 关闭连接
        curl_close($ch);

        // 8. 解析微信返回结果
        // 注意：如果调用成功，返回的是图片二进制数据；如果失败，返回的是JSON错误信息
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $body = substr($result, $headerSize);

        // 尝试解码JSON，如果失败说明是图片数据
        $json = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($json['errcode'])) {
            return ['error' => $json];
        } else {
            // 成功获取二维码图片
            return ['image' => $body];
        }
    }


    public  function GetQr($id){

        $Src =  "/UserQr/{$id}.png";
        $RealPath = \app\utils\PathConverter::ToStoragePath($Src);
        if(file_exists($RealPath)){

            echo '<br/>';
            echo $RealPath;
            echo '<br/>';
            echo $Src;
            echo '<br/>';
            echo  '文件已经存在';
            echo '<br/>';
            echo "<img src='{$Src}' alt='{$RealPath}' />";
            exit();
        }

        $Token =  $this -> GetToken();
        $qb=  new UserQrBuilder($Token);

        $qb -> Build("/pages/about/index?id={$id}",430);

        $qb -> Save($RealPath);

        echo '生成了图片';
        echo '<br/>';
        echo "<img src='{$Src}' alt='{$RealPath}' />";

    }

    protected  function GetToken(){


        return $this -> GetNewToken();

    }
    protected function GetNewToken(){
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
