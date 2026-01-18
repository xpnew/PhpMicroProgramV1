<?php

namespace app\api\controller;

use think\Controller;
use think\facade\Log;
use think\facade\Route;
use app\comm\CommMsg;
use app\comm\QueryMsg;
use app\comm\CommControllerBase;

class ApiBase extends CommControllerBase
{

    protected $middleware = [
        'AppApiAuth' 	=> ['except' 	=> ['Login','Reg','AutoLogin','LoginAsync','Test1','NeedLogin' ]],
        //'AllowCross' 	=> ['except' 	=> ['Test000']],

    ];
    protected function initialize()
    {
        parent::initialize();

        // $this->QMsg = new QueryMsg();
        // $this -> Msg = new CommMsg();

        // 中间件注册
        // $this->middleware(\app\http\middleware\AdminAuth::class);
    }

    protected function _InitViewData(){


    }


    

}


?>

