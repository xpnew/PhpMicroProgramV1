<?php

namespace app\Comm;

use think\Controller;
use think\facade\Log;
use think\facade\Route;
use app\Comm\CommMsg;
use app\Comm\QueryMsg;

class CommControllerBase extends Controller
{
    protected $Msg;
    protected $QMsg;

    public $RecordCount ;


    /** @var bool $HasError 已经存在错误标志 */
    public $HasError =  false;

    /** @var array $ErrorList 错误消息列表，一般只用来处理整批数据的时候使用，平常都是用 NoticeStr*/
    public $ErrorList = [];
    /** @var string $NoticeStr 页面通知消息，错误消息内容 */
    public $NoticeStr  = '';



    /** @var \app\Comm\SysSetCacheMng $_CacheMng 缓存管理实例 */
    protected $_CacheMng = null;
    protected function initialize()
    {
        parent::initialize();

        $this -> QMsg = new QueryMsg();
        $this -> Msg = new CommMsg();

        $this -> _CacheMng =  \app\Comm\SysSetCacheMng::getInstance();
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
        $current =  Route:: getCurrentRule();


        Log::record('程序出错' . $title . ' ex=' . json_encode($ex) . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current)  );
        // Log::error('程序出错' . $title . ' ex=' . json_encode($ex)   . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current));

        $this -> LogError($title ,$data, $ex );
        
        return json($this->QMsg);
    }


    /** 内部打包处理，出错现了错误，准备退出程序
     * @param string $errorMsg 错误消息的内容
     * @return void
     */
    protected  function _SetFail($errorMsg){
        $this -> HasError = true;
        $this -> ErrorList[] = $errorMsg;
        $this -> NoticeStr = $errorMsg;

        $this -> QMsg -> msg = $this -> NoticeStr;
        $this->Msg->SetErr($errorMsg,-1 ,   null, null);
    }

    /** 发送Json消息
     * @return \think\response\Json
     */
    protected  function SendJMsg(){
        return json($this->Msg);
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

        Log::record('程序出错' . $title . ' ex=' . json_encode($ex) . ' pathinfo=' . $pathinfo  );
        // Log::error('程序出错' . $title . ' ex=' . json_encode($ex)   . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current));

        $this-> LogError($title ,$data, $ex );
        
        return json($this->Msg);
    }

    protected function SayLog($title ,$model =  null){
        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo

        $CurrentRoute = $this->GetRoute();
        Log::record('日志输出：' . $title . ' pathinfo=' . $pathinfo .  ' 路由规则=' . $this->GetRoute2Str() );
        if(null != $model){
            Log::record('模型数据：\n'  . json_encode($model)   );
        }
    }
    protected function LogError($title ,$model =  null, $ex=null){
        $pathinfo = $this->request->pathinfo(); // 获取当前请求的pathinfo


//        $currentAction =  $dispatch->controller() . '/' .  $dispatch->action(); // 当前操作
        //

        Log::record('程序出错：' . $title . ' pathinfo=' . $pathinfo .  ' 路由规则=' . $this->GetRoute2Str() );
        if(null != $model ){
             Log::record('模型数据：\n'  . json_encode($model)   );
        }
        if(null != $ex){
            Log::record('异常信息：\n'  . json_encode($ex)   );
        }
    }

    //获取路由信息 并且转为字符串
    protected function GetRoute2Str(){
        $r = $this->GetRoute();
        if(null == $r){
            return null;
        }
        $arr =  $r -> toArray();
        return json_encode($arr);
    }


    protected  function GetRoute(){
//        if(Route::getRule() ) // 根据千问的反馈，getRule 只能获取指定名称的路由，这里不适用
//            $current = Route::getRule()->getRule('current'); // 获取当前路由规则（如果有的话）
//        if(null != $current && isset($current) && ! empty($current)){
//            return $current;
//        }
        $current =  Route:: getCurrentRule();
        if(null != $current && isset($current) && ! empty($current)){
            return $current;
        }
        // 获取调度信息
        $dispatch =  $this->request->dispatch();
        if(null != $dispatch &&  isset($dispatch) && null != $dispatch-> rule() ) {
//            $current = $dispatch->rule()->getRule() ?? 'N/A'; // 命中的路由规则
        }
        return null;
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


    protected  function _GetName(){

        $Name1 = get_class($this);
        $Name2 = '';
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        if (isset($trace[1]['function'])) {
            $Name2  = $trace[1]['function'];
        }

        return 'Class: ' .$Name1 .' Method: ' .$Name2;
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

