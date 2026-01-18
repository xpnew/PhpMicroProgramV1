<?php
namespace app\comm\AliSms;


use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\Credentials\Credential;
use AlibabaCloud\Tea\Utils\Utils;
use AlibabaCloud\Tea\Console\Console;
use \Exception;
use AlibabaCloud\Tea\Exception\TeaError;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;



class SmsUtil{

    /**
     * 使用凭据初始化账号Client
     * @return Dysmsapi Client
     */
    public  static function createClient(){
        // 工程代码建议使用更安全的无AK方式，凭据配置方式请参见：https://help.aliyun.com/document_detail/311677.html。
        $credential = new Credential();
        $config = new Config([
           //"credential" => $credential
            'accessKeyId' => 'LTAI5tMTZWMejgVM3sdPKLaT',
    'accessKeySecret' => 'Qv2gtYY86V5oNGTqgtaEt2fYvbRqPv',
        ]);
        // 配置协议类型为 HTTPS
$config->protocol = "HTTPS";
        // Endpoint 请参考 https://api.aliyun.com/product/Dysmsapi
        $config->endpoint = "dysmsapi.aliyuncs.com";
        return new Dysmsapi($config);
    }

    public static function Send($phone, $code){
        // $obj = new stdClass();
        // var_dump($obj);

        // $empty_object = new stdClass();        


        // $Rt = new stdClass();
        $Rt = (object)[];
        $Rt -> Status = false;
        $Rt -> Message = '';
        $Rt -> code = 'error';


        if(empty($phone)){
            $Rt -> Message = '手机号码不能为空';
            return $Rt;
        }

        $client = self::createClient();
        $sendSmsRequest = new SendSmsRequest([
                  "phoneNumbers" => $phone,
            "signName" => "沈北新区御享健康",
            "templateCode" => "SMS_495855539",
            "templateParam" => "{\"code\":\"".$code."\"}" 
        
        ]);
        $runtime = new RuntimeOptions([]);
        // true 忽略证书校验；false 设置证书校验
		$runtime->ignoreSSL = true;
        
        try {
            $resp = $client->sendSmsWithOptions($sendSmsRequest, $runtime);
            Console::log(Utils::toJSONString($resp));
            
            // $this -> SayLog('TestSms2 resp:'.json_encode($resp));
            // echo  '测试发送: ' . json_encode($resp) . '<br>';

            $Return =  $resp -> body;
            if( '200' == $resp -> statusCode)
            {
                return $resp -> body;
            }
            // if('OK' == $Return -> code){
            //     return '发送短信成功';
            // }else{
            //     return '发送短信失败: ' . $Return -> message;
            // }
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }
            // 此处仅做打印展示，请谨慎对待异常处理，在工程项目中切勿直接忽略异常。
            // // 错误 message
            // var_dump($error->message);
            // // 诊断地址
            // var_dump($error->data["Recommend"]);
            // Utils::assertAsString($error->message);
           
            $Rt -> message = $error->message;
            $Rt -> code = 'error';

            return $Rt;
        }


        return $Rt;

    }




}




?>