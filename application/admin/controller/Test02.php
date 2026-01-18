<?php
namespace app\admin\controller;
use think\Controller;

class Test02 extends Controller
{
    public function index()
    {

        //显示路由信息

        // return 'Hello,This is Admin module.Test01.';



        
        return $this->fetch();

    }   


    public function test1()
    {

        $Options= new \app\Models\SysOptions();
        $Product_ClassT= new \app\Models\Product_ClassT();
        // $Product_ClassT2= new \app\Models\ProductClassModel();

        $upload2 =  new \app\Models\UploadFileModel();

        $pro0 = new \app\Models\ProductInfo();
        $pro1 = new \app\Models\Product_InfoT();
        $pro2 = new \app\Models\Product_InfoV();

        $upload1 =  new \app\Models\UploadFileModel();
        $upload =  new \app\Models\UploadFileT();
 
        // $upload2 =  new \app\Models\UploadFile2();

        return 'Hello,This is Admin Test02 test1.';
    }
}


?>