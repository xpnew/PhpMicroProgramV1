<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class SysTypeDefined extends ApiBase
{



    public function index()
    {
        return 'SysTypeDefined';
    }   


    public function Defs(){

        $data =[  ];
        $ClassName = input('ClassName','');
        $TypeId = input('TypeId','');
        $Code =  input('Code'); // $this->request->param('code');//input('Code','');
        $groupids =  input('groupids','');
        if('' == $Code){
            $Code = input('code','');
        }

        $where = [];
   
        if($Code != ''){
            // echo 'Code=' . $Code . "\n";
            $db2 = new \app\Models\Sys_TypeDefinedT();
            $type = $db2 -> where('CodeName',$Code) -> find();  
            if($type != null){
                $TypeId = $type -> TypeId;
            }else{
                return $this->SendJErr('指定的类型、状态不存在:' . $Code); 
            }       
        }else if($groupids != ''){
            // echo 'Code=null' . "\n";
            // echo  '$groupids=';
            // echo   dump($groupids);
            //where('GroupId', 'in', [1, 5, 80, 50])
            $where[] = ['GroupId','in',$groupids ];
        }
        if($TypeId != ''){
            $where[] = ['GroupId','=',$TypeId];
        }
        if(null == $TypeId ||  '' ==  $TypeId){
            // return $this->SendJErr('必须指定类型ID');
        }
        if(0 == count($where)){
            return $this->SendJErr('必须指定类型ID');
        }
         $where[] = ['IsShow','=',1];
        $db= new \app\Models\Sys_TypeDefinedT();
        $data = $db -> where($where) 
        -> order(['GroupOrd'=>'asc', 'TypeId'=>'asc','CodeName'=>'asc'   ])
        ->select();  
        $data = $data->toArray();  
        // 返回数据     
        return $this->SendJOk('查询成功',1,$data);
    }

}

?>