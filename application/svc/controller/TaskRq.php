<?php

namespace app\svc\controller;

use think\Controller;
use think\Request;
use app\comm\CommControllerBase;
use app\Models\Task_RequestLogT;


///服务：任务请求
class TaskRq extends CommControllerBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    public function TestLog(){

        $this->SayLog('测试日志输出');

        $FullUrl = $this->request->url(true);

        $NewLog = new Task_RequestLogT();

        $NewLog -> CreateTime = date('Y-m-d H:i:s');
        $NewLog -> CreateTS = time();
        $NewLog -> IsFinished = 0;
        $NewLog -> IsSuccess = 0;
        $NewLog -> TaskTypeName = '测试日志';
        $NewLog -> TaskTypeId = 0;
        $NewLog -> FullUrl = $FullUrl;
        $NewLog -> save();

        $NewId = $NewLog -> Id;

        $NewLog = Task_RequestLogT::get($NewId);
        $NewLog -> IsFinished = 1;
        $NewLog -> IsSuccess = 1;
        $NewLog -> FinishedTime = date('Y-m-d H:i:s');
        $NewLog -> save();


        return $this->SendJOk('保存成功');

    }

}
