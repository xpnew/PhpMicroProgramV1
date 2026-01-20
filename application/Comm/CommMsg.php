<?php
namespace app\Comm;


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
            $this->Name  = get_class( $this);
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
}