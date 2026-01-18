<?php
namespace app\Test\controller;
use think\Controller;
use think\facade\Log;

require_once  __DIR__ . "/../../Comm/WxPay/WxPay.Config.php";

// require_once "./../config/TestConfg1.php";
require_once __DIR__ . "\..\config\TestConfg1.php";
// require_once __DIR__ . "\..\..\Comm\WxPay\WxPay.Config.php";

class TestWxPay extends Controller
{


    public function index()
    {
        return 'Hello,This is TestWx module.Test01.';
    }   

    public function test1()
    {


        

        echo("<br />");

        $urls =  \WxPayConfig::NOTIFY_URL;

        echo( ' WxPayConfig::NOTIFY_URL : ' .  $urls  );
    }
}

?>