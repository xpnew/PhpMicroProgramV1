<?php
namespace app\Comm;


use app\utils\GeneralTool;

class CommMsg extends  Framework\BaseArrayAccess {
    public $Title;
    public $StatusCode;
    public $DataInfo;
    public $Body;
    public $Name;
    public $Exception;


    public  $Log;
    

    public function __construct($title= '',$statusCode = 0 ,$dataInfo= NULL,$body ='' ,$name=''){
        parent::__construct();

        $this->Title = $title;
        $this->StatusCode = $statusCode;
        $this->DataInfo = $dataInfo;
        $this->Body = $body;
        if($name == ''){
            $this->Name  =  $this-> GetName();
        }else
        $this->Name = $name;
    }


    public  function AddLog($log){
        $this-> Log =  $this-> Log  . '/n' . $log;
    }

    public function ToString(){
        return json_encode($this);
    }
    public function SetOk($title='', $statusCode = 1, $dataInfo = NULL)    {
        $this->Title = $title;
        $this->StatusCode = $statusCode;
        $this->DataInfo = $dataInfo;
    }
    public function SetErr($title, $statusCode = -1, $dataInfo = NULL, $ex = NULL)    {
        $this->Title = $title;
        $this->StatusCode = $statusCode;
        $this->DataInfo = $dataInfo;
        $this->Exception = $ex;
    }

    private  function GetName(){
        $Nm =   get_class( $this);
        if(! GeneralTool::EndWith( $Nm, 'Msg')){

            return $Nm;
        }
        $name1=  $this->GetParentClassName();

        $name2 = $this-> GetParentMethodName();

        return 'Class: '. $name1 . ' Method:' . $name2;

    }
    // 获取调用者的类名
    public function GetParentClassName() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        // 检查是否存在上一级调用且包含类信息
        if (isset($trace[1]['class'])) {
            return $trace[1]['class'];
        }
        return 'N/A'; // 如果没有（例如是全局函数调用），返回 N/A
    }

    // 获取调用者的方法名
    public function GetParentMethodName() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
        if (isset($trace[2]['function'])) {
            return $trace[2]['function'];
        }
        return 'N/A';
    }

    // 额外功能：获取调用者对象（如果需要操作调用者实例）
    public function GetParentObject() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        if (isset($trace[1]['object'])) {
            return $trace[1]['object'];
        }
        return null;
    }
}