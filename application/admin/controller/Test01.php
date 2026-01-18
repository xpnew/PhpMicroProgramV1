<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Log;


class Test01 extends Controller
{
    public function index()
    {

         Log::record('测试日志信息' );

        // 单字母函数 在 thinkphp5.1.0中已弃用
        // $User= M('Sys_User'); // 假设有一个User模型
        $User= new \app\admin\model\Sys_User();


        $this->assign('title', '测试');
        $this->assign('users', $User->select());
    


        // return 'Hello,This is Admin module.Test01.';

        return $this->fetch();

    }   
}


?>