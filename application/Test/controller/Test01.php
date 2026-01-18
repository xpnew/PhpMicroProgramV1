<?php
namespace app\Test\controller;
use think\Controller;

class Test01 extends Controller
{
    public function index()
    {
        return 'Hello,This is Test module.Test01.';
    }

    public function test1()
    {
        
                $Options= new \app\admin\model\SysOptions();

        $this->assign('title', '测试');
        $this->assign('ModelList', $Options->select());


        foreach ($Options->select() as $key => $value) {
            echo $value['OptionKey'].'='.$value['OptionValue'].'<br/>';
        }
        $lst = $Options->select();
        dump($lst);     

        $lll =  $lst->toArray();
        dump($lll);


        foreach ($lll as $key => $value) {
            // echo $value['OptionKey'].'='.$value['OptionValue'].'<br/>';
            foreach ($value as $k => $v) {
                echo $k.'='.$v.'<br/>';
            }
        }

    }   
}
