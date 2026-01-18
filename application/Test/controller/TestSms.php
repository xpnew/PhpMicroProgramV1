<?php
namespace app\test\controller;
use think\Controller;
use app\comm\CommControllerBase;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Dysmsapi\Dysmsapi;

class TestSms extends CommControllerBase
{ 


    public function index()
    {

        // 设置全局客户端
// Please ensure that the environment variables ALIBABA_CLOUD_ACCESS_KEY_ID and ALIBABA_CLOUD_ACCESS_KEY_SECRET are set.
AlibabaCloud::accessKeyClient('LTAI5tMTZWMejgVM3sdPKLaT','Qv2gtYY86V5oNGTqgtaEt2fYvbRqPv')

// use STS Token
// AlibabaCloud::stsClient(getenv('ALIBABA_CLOUD_ACCESS_KEY_ID'), getenv('ALIBABA_CLOUD_ACCESS_KEY_SECRET'), getenv('ALIBABA_CLOUD_SECURITY_TOKEN'))
    ->regionId('cn-qingdao')
    ->asDefaultClient()->options([

    ]);

try {
    $request = Dysmsapi::V20170525()->sendSms();
    $result = $request

        ->withPhoneNumbers("18640265939")
        ->withSignName("沈北新区御享健康")
        ->withTemplateCode("SMS_495855539")
        ->withTemplateParam("{\"code\":\"112233\"}")


        ->debug(true) // Enable the debug will output detailed information

        ->request();
    print_r($result->toArray());
} catch (ClientException $exception) {
    echo $exception->getMessage() . PHP_EOL;
} catch (ServerException $exception) {
    echo $exception->getMessage() . PHP_EOL;
    echo $exception->getErrorCode() . PHP_EOL;
    echo $exception->getRequestId() . PHP_EOL;
    echo $exception->getErrorMessage() . PHP_EOL;
}




        //
        return 'test sms';
    }



}

?>