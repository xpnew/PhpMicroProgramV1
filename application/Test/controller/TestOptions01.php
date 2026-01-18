<?php
namespace app\test\controller;

use think\Controller;

// class TestOptions extends Controller
// {
//     public function index()
//     {
//         //显示路由信息

//         // return 'Hello,This is Test module.TestOptions.';

//         // return $this->fetch();

//         $Options= new \app\admin\model\SysOptions();

//         $this->assign('title', '测试');
//         $this->assign('ModelList', $Options->select());

//         return $this->fetch();
//     }   
// }


class TestOptions01 extends Controller
{
    public function index()
    {


        return 'Hello,This is Test module.TestOptions.';
    }

    public function test1()
    {
        $Options= new \app\Models\SysOptions();

        $this->assign('title', '测试');
        $this->assign('ModelList', $Options->select());

        return $this->fetch();
    }
}


?>