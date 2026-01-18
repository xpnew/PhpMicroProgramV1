<?php
namespace app\http\middleware;
use app\comm\Token\TokenMng;
use app\comm\Token\TokenItem;

class AppApiAuth
{
    public function handle($request, \Closure $next)
    {
        // header('Access-Control-Allow-Origin:*');
        // header('Access-Control-Allow-Methods:POST,GET');
        // header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $info = $request -> header();
        // $info2 = \think\facade\Request::header();
        // var_dump($info);
        // echo '<br>';
        // echo 'request';
        // echo '<br>';
        // var_dump($request);   
        // echo '<br>';
        // echo '<br>';

        // echo 'info =======';
        // var_dump($info);   
        // echo '<br>';
        // // echo $info['Authorization'];
        
        if( !isset($info) ){
            $this-> Log('AppApiAuth  header  not exist '  );
             $info  = $request['header'];
        }

        // echo 'AppApiAuth =====';
        // var_dump($info);   
        // echo '<br>';
        // //php 转换为小写了 ！！！！ 
        // echo $info['authorization'];
        if (!isset($info['authorization'])) {
            $this-> Log('AppApiAuth  header  authorization not exist '  );
            return redirect('/api/User/NeedLogin');
        }
        $token = $info['authorization'];

        if(substr($token, 0,12) == 'AccessToken '){
            $token = substr($token , 12);
        }
        if ($this->CheckToken($token) === false) {
            // return "";
            $this-> Log('AppApiAuth  CheckToken  false  token : ' . $token);
            return redirect('/api/User/NeedLogin');
        }
        // echo 'request =====';
        // var_dump($request);   
        // echo '<br>';
        if($request-> has('UserId') == false){
            // echo 'set UserId ' . $this-> UserId  . '<br>'    ;
            $request -> UserId = $this-> UserId;
        }
        // echo 'request =====';
        // var_dump($request);   
        // echo '<br>';
        return $next($request);
    }

    protected $UserId = 0;

    public function CheckToken($token)
    {

        $Mng =  TokenMng::getIns();
        $tokenItem = $Mng-> GetToken($token);
  
        if (!$tokenItem) {
            $this -> Log(' token not exist : ' . $token   );  
            return false;
        }
        if ($tokenItem->CreateTS + $tokenItem->ExpireTS < time()) {
            $Mng->DelToken($token);
            return false;
        }
        $this-> UserId = $tokenItem->UserId;
        return true;
    }
    protected function Log($msg){
        \think\facade\Log::info($msg);
    }
}
