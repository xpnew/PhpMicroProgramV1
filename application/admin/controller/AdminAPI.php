<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

use think\facade\Log;

use app\comm\CommControllerBase;


class AdminAPI extends CommControllerBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this -> _InitViewData();

        return $this->fetch();
    }
    protected function _InitViewData()
    {
        // parent::_InitViewData();
        $this->assign('title', '交易流水');

    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function typedefs($groupid)
    {
        $statusdb  = new \app\Models\Sys_TypeDefinedT();
        $TypeDefs =  $statusdb -> where(['GroupId'=>$groupid,'IsShow'=>1]) -> order(['GroupOrd' ,'TypeId']) -> select();

        return $this->SendJOk('ok',1, $TypeDefs);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }




}
