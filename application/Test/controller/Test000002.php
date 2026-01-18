<?php
namespace app\test\controller;
use think\Controller;

class Test000002 extends Controller
{
    public function index()
    {
        return 'Hello,This is Test module.Test000002.';
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
        $clinetuser  =  new \app\Models\Client_UserT();

        $buycar = new \app\Models\Client_BuyCarItemT();
        $buycar2 = new \app\Models\Client_BuyCarT();

        $systype =  new \app\Models\Sys_TypeDefinedT();
        $db33 =  new \app\Models\Clinet_BonusLogT();
        $db33 -> select();

        $db34 =  new \app\Models\Biz_PayBillT();
        $list34 =  $db34 -> select();
        echo 'ok ' . ' paybill count: ' . count( $db34->select() );

        $db40 =  new \app\Models\IndexSwitchT();
        echo 'ok ' . ' IndexSwitchT count: ' . count( $db40->select() );



        // $upload2 =  new \app\Models\UploadFile2();
        echo 'ok ' . ' buycar count: ' . count( $buycar->select() );
        echo( '<br />');


        $this->assign('title', '测试');
        $this->assign('ModelList', $Options->select());

        return 'ok ' . ' clientuser count: ' . count( $clinetuser->select() );
    }

    public function test2()
    {
        $toto  =  new Subclass222();
        $toto -> title = 'hello title';
    
        var_dump( $toto );
        echo( '<br />');

        echo  json_encode( $toto );
        echo( '<br />');



        return 'Hello,This is Test module.Test000002.test2.';
    }       

}


class Subclass222{

  public $title;
    public $icon;

}

?>