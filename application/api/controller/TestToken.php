<?php
namespace app\api\controller;



use think\Controller;
use think\Request;
use app\comm\Token\TokenMng;
use app\comm\Token\TokenItem;
use app\comm\CommControllerBase;


class TestToken extends CommControllerBase
{
    
    
    public function index(Request $request)
    {
        $name =  $request->param('name','');
        $value =  $request->param('value','');
        if($name == '' || $value == ''){
            return '参数错误';
        }
        $Mng =  TokenMng::getIns();
        if(  $Mng-> GetToken($name)){
            return '存在';
        }
        $Mng -> SetToken($name,$value);
        return '设置成功';
         
    }

    public function Exist($name){
        $Mng =  TokenMng::getIns();
        $tokenItem = $Mng-> GetToken($name);
        if (!$tokenItem) {
            return '不存在';
        }
        return ' 存在';
    }
    public function Check($name){
        $Mng =  TokenMng::getIns();
        $tokenItem =$this -> CheckToken($name);

        echo 'CheckToken tokenItem ='.$tokenItem;
        echo '<br>';
        if (!$tokenItem) {
            return '不存在';
        }
        return ' 存在';
    }

    public function TestClean(){
        $token = 'AccessToken c8461193479a3cfa732fc971c0701d35';
        echo 'token='.$token;
        echo '<br>';
        echo 'substr  =' . (substr($token, 0,12));
        echo '<br>';

        
        if(substr($token, 0,12) == 'AccessToken '){
            $token = substr($token,12);
        }

        echo 'token='.$token;
        echo '<br>';

    }

    private function CheckToken($token)
    {
        echo 'input token='.$token;
        echo '<br>';
        $Mng =  TokenMng::getIns();
        $tokenItem = $Mng-> GetToken($token);
        echo 'tokenItem='.json_encode($tokenItem);
        echo '<br>';
        if (!$tokenItem) {
            return false;
        }
        if ($tokenItem->CreateTS + $tokenItem->ExpireTS < time()) {
            $Mng->DelToken($token);
            return false;
        }
        return true;
    }

    public function GetReqHeader(){ 
        $header =   \think\facade\Request::header();
        //echo 'header='.json_encode($header);
        return json($header);
    }
}




