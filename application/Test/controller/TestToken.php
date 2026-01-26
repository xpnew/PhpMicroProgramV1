<?php
namespace app\Test\controller;


class TestToken{


    public function index()
    {

        $TokenMng = \app\Comm\Token\TokenMng::getIns();
        $Token = $TokenMng->Add(new \app\Comm\Token\TokenItem());
        echo 'Token: ' . json_encode($Token);

        $TokenItem = $TokenMng->GetToken($Token->Token);
        echo '<br />';
        echo 'TokenItem: ' . json_encode($TokenItem);   
    }


}


?>