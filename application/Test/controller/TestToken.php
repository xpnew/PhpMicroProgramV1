<?php
namespace app\Test\controller;


class TestToken{


    public function index()
    {

        $TokenMng = \app\comm\Token\TokenMng::getIns();
        $Token = $TokenMng->Add(new \app\comm\Token\TokenItem());
        echo 'Token: ' . json_encode($Token);

        $TokenItem = $TokenMng->GetToken($Token->Token);
        echo '<br />';
        echo 'TokenItem: ' . json_encode($TokenItem);   
    }


}


?>