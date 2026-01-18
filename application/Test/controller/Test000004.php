<?php
namespace app\test\controller;
use think\Controller;

class Test000004 extends Controller
{
    public function index()
    {
        return 'Hello,This is Test module.Test000004.';
    }



    public function test0()
    {
        echo "这是将要被清除的内容";
        header("Content-Length: 0");
        each('aaa');

        exit();

        return "ok :";
    }

    public function test2()
    {

        ob_start();
        echo "这是将要被清除的内容";
        // 清除缓冲区内容但不发送到客户端
        ob_clean();

        return "ok";
    }
    public function test1()
    {
        \app\utils\x::Say("这是要发送的参数",$this);
        

      
        return 'ok';
    }

}


?>