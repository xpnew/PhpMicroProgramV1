<?php

namespace app\Test\controller;
use think\facade\Config;
use think\facade\Config as SysCfg;


class TestConfig02
{


    public function testConfig02(){

        dump(Config::get());

        echo  Config::get('aliyun_sms_templatcode');
    }

    public  function index(){

//        dump(Config::get());
        echo  Config::get('sms.aliyun_sms_templatcode');


        $AliConfig = [];
        $AliConfig['Access'] = [
            'key1' =>  Config::get('sms.aliyun_sms_templatcode'),
            'key2' =>  Config::get('sms.aliyun_sms_templateid'),
        ];

        var_dump($AliConfig);



    }

    public  function Test66()
    {
        $AliConfig['AccessSet'] = [
            'accessKeyId' =>  SysCfg::get('sms.aliyunkey1'),
            'accessKeySecret' =>  SysCfg::get('sms.aliyunkey2'),
        ];

        $AliConfig['Template'] = [
            'signName' =>  SysCfg::get('sms.aliyun_sms_templateid'),
            'templateCode' =>  SysCfg::get('sms.aliyun_sms_templatcode'),
        ];
        echo '<br/> ============================<br/>';

        $code = '2134';
        $phone = '13072422014';
        $ReqArguments =[
            "templateParam" => "{\"code\":\"".$code."\"}",
            "phoneNumbers" => $phone,

        ];
        $ReqArguments   =  $ReqArguments +  $AliConfig['Template'];
        var_dump($ReqArguments);

    }
    public  function Test55(){
        $AliConfig =
        [
            'key1' =>  Config::get('sms.aliyun_sms_templatcode'),
            'key2' =>  Config::get('sms.aliyun_sms_templateid'),
        ];

        var_dump($AliConfig);

        $Arr2 =  [
            'key3' => 'abc',
            'key4' => 'def',

        ];
        echo '<br/> ============================<br/>';
        var_dump($Arr2);

        echo '<br/> ============================<br/>';

        $Arr3 = array_merge( $Arr2 , $AliConfig);

        var_dump($Arr3);

        echo '<br/> ================  Arr4 ============<br/>';
        $Arr4 = $Arr2 +  $AliConfig;
        var_dump($Arr4);
        echo '<br/> ================  Arr5 ============<br/>';
        $Arr5 = $AliConfig +  $Arr2;
        var_dump($Arr5);

    }

}