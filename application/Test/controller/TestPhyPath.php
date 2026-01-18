<?php


namespace app\test\controller;

use think\Controller;
use app\utils\GeneralTool;

class TestPhyPath extends Controller
{ 


    public function index()
    {

        //显示路由信息

        // return 'Hello,This is Admin module.Test01.';

        // $this->assign('title', '测试');
        $RootPath = $_SERVER["DOCUMENT_ROOT"];

        echo  $_SERVER["DOCUMENT_ROOT"];
        echo '<br/>';

        if(! GeneralTool::EndWith( $RootPath,'\\'))  {
            $RootPath = $RootPath . '\\';
        }

        if(! GeneralTool::EndWith( $RootPath,'public\\')){
            $RootPath = $RootPath . 'public\\';
            
        }

        echo ' 获取到 的结果 ： ' . $RootPath;

        echo '<br/>';

        $Root2 =  GeneralTool::GetPhyRoot(true);

        echo ' 获取到 的结果 ： ' . $Root2;

        echo '<br/>';


        echo ' 获取到 的操作系统  ： ' . PHP_OS;

        echo '<br/>';

        return  "ok";

    }

}   

?>