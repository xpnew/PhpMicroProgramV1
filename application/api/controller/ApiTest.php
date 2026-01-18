<?php
namespace app\api\controller;
use think\Controller;
use think\Request;


class ApiTest extends ApiBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return 'api test ok';
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }


    public function GetUserId(){

               $UserId =  \think\facade\Request::param('UserId');

                 $UserId2 = input('UserId','-9999');
                 echo 'input UserId2 = ' . $UserId2;
                 echo '<br>';

                return 'api test  userid: ' . $UserId;
    }

}