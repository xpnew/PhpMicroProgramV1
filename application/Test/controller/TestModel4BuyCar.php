<?php

namespace app\Test\controller;

class TestModel4BuyCar extends TestBase
{

    public function query(){

        $lst =  \app\Models\Client_BuyCarItemT::select();
        $this->_Print('全部数据');
        $this->_Print($lst);


        $this->_Print('尝试获取分类');
        $this->_Print($lst[0]-> ProductClass());



    }


}