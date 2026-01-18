<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Log;
use think\facade\Route;
use app\comm\CommMsg;
use app\comm\QueryMsg;
use app\comm\CommControllerBase;

class AdminBase extends CommControllerBase
{

    protected $middleware = [
        'AdminAuth' 	=> ['except' 	=> ['login','LoginAsync' ]],
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

        $this->assign('Request', $this->request);
        $this->assign('ControllerName', $this->request->controller());
        $this->assign('ModuleName', $this->request->module());  
        $this->assign('ActionName', $this->request->action());
        $this->assign('RootPath', $this->request->root());
        $this->assign('StaticPath', '/static');
        $this->assign('UploadPath', '/uploads');
        $this->assign('Version', '1.0.0');
        $this->assign('AppName', 'AppName');
        $this->assign('AppShortName', 'AppShortName');
        $this->assign('AppCompany', 'AppCompany');
        $this->assign('AppDomain', 'www.app.com');
        $this->assign('AppICP', '粤ICP备00000000号');
        $this->assign('AppEmail', '<EMAIL>');
        
    }


    

}


// ///页面返回消息 封装  layer table  数据格式 
// class PageMsg{
//     public $code;  //0成功  1失败
//     public $msg;   //提示信息
//     public $count; //数据总数
//     public $data;  //数据

//     function __construct($code=0,$msg='',$count=0,$data=array()){
//         $this->code=$code;
//         $this->msg=$msg;
//         $this->count=$count;
//         $this->data=$data;
//     }

// }
?>

