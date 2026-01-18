<?php

namespace app\Comm;

use think\Controller;
use think\facade\Log;
use think\facade\Route;
use app\comm\CommMsg;
use app\comm\QueryMsg;

class CommControllerBase extends Controller
{
    protected $Msg;
    protected $QMsg;

    public $RecordCount ;
    protected function initialize()
    {


        $this -> QMsg = new QueryMsg();
        $this -> Msg = new CommMsg();
        

    }



    protected function SendQOk($title,$code =0, $data = array() ){
        $this->QMsg->code = $code;
        if(null != $this    -> RecordCount ){     
            $this->QMsg->count = $this->RecordCount;
        }
        $this->QMsg->msg = $title;
        $this->QMsg->data = $data;
        //dump($this-> QMsg);
        return json($this->QMsg);
    }
    protected function SendQOk2($title,$count,$data = array() ){
        $this->QMsg->code = 0;
        $this->QMsg->count = $count;
        $this->QMsg->msg = $title;
        $this->QMsg->data = $data;

        return json($this->QMsg);
    }

    protected function SendQErr($title,$code =1, $data = array(),$ex=null)
    {
        $this->QMsg->code = $code;
        $this->QMsg->msg = $title;
        $this->QMsg->data = $data;

        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo
        // $current = Route::getRule()->getRule('current'); // 获取当前路由规则（如果有的话）
        $$current =  Route:: getCurrentRule();


        Log::record('程序出错' . $title . ' ex=' . json_encode($ex) . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current)  );
        // Log::error('程序出错' . $title . ' ex=' . json_encode($ex)   . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current));

        LogError($title ,$model, $ex );
        
        return json($this->QMsg);
    }

    protected function SendJOk($title,$code =1, $data = array())
    {
        $this->Msg->SetOk($title, $code,   $data);
        return json($this->Msg);
    }

    protected function SendJErr($title,$code = -1, $data = array(),$ex=null)
    {
        $this->Msg->SetErr($title, $code,   $data,$ex);

        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo
        // $current = Route::getRule()->getRule('current'); // 获取当前路由规则（如果有的话）
        $current =  Route:: getCurrentRule();


        Log::record('程序出错' . $title . ' ex=' . json_encode($ex) . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current)  );
        // Log::error('程序出错' . $title . ' ex=' . json_encode($ex)   . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current));

        $this-> LogError($title ,$data, $ex );
        
        return json($this->Msg);
    }

    protected function SayLog($title ,$model =  null){
        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo
        // $current = Route::getRule()->getRule('current'); // 获取当前路由规则（如果有的话）
        $current =  Route:: getCurrentRule();       
        Log::record('日志输出：' . $title . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current)  );
        if(null != $model){
            Log::record('模型数据：\n'  . json_encode($model)   );
        }
    }
    protected function LogError($title ,$model =  null, $ex=null){
        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo
        // $current = Route::getRule()->getRule('current'); // 获取当前路由规则（如果有的话）
        $current =  Route:: getCurrentRule();    
        Log::record('程序出错：' . $title . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current)  );
        if(null != $model ){
             Log::record('模型数据：\n'  . json_encode($model)   );
        }
        if(null != $ex){
            Log::record('异常信息：\n'  . json_encode($ex)   );
        }
    }

    ///移除不要的字段，通常是为了保存的时候，不想修改什么数据
    protected function RemoveFields($arr,$fieldArr){
        //简洁写法：   foreach ($SkipFields as $field)

        // 注意：这种写法 fkey 是索引 0~n fvalue 是字段名称
        foreach ($fieldArr as $fkey => $fvalue) {
            if(array_key_exists($fvalue, $arr)){
                unset($arr[$fvalue]);
            }
        }
        return $arr;
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

