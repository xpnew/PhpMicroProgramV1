<?php
namespace app\test\controller;
use think\Controller;
use Ramsey\Uuid\Uuid;

class Test000003 extends Controller
{
    public function index()
    {
        // 生成标准v4 UUID
        $orderNumber = Uuid::uuid4(); 
        echo $orderNumber;      
        echo '<br />';
        // 输出：f47ac10b-58cc-4372-a567-0e02b2c3d479

        return 'Hello,This is Test module.Test000003.';
    }

public  function info(){

    phpinfo();
}

    public function test1()
    {

        $Id =  \app\Utils\GeneralTool::CreateGuid();   
                 echo $Id;      
        echo '<br />';
        return "";
    }

    public  function Test3()
    {

        $p=  \app\Models\Product_InfoT::get(3);

        $NeedFillArr = [($p -> Hits), $p-> NormalPrice, $p-> DirectGuiderRatio, $p['IndirectGuiderRatio']];
        FillVariate($NeedFillArr, 0);

        var_dump($p);
        echo '<br /> Hits ===================<br/>';
        echo $p -> Hits;




//        echo '<br /> ===================<br/>';
//        $HitsRef =  &$p->Hits;
//        $NeedFillArr2 = [($p -> Hits), $p-> NormalPrice];
//        FillVariate($NeedFillArr2, 0);
//
//
//        echo '<br /> Hits ===================<br/>';
//        echo $p -> Hits;
//        echo '<br /> NormalPrice ===================<br/>';
//        echo $p -> NormalPrice;
//        echo '<br /> ===================<br/>';






    }

    public  function  Test04    ()
    {

        $p=  \app\Models\Product_InfoT::get(3);

//        $NeedFillArr = [($p -> Hits), $p-> NormalPrice, $p-> DirectGuiderRatio, $p['IndirectGuiderRatio']];
//        FillVariate($NeedFillArr, 0);

        var_dump($p);
        echo '<br /> Hits ===================<br/>';
        echo $p -> Hits;

        SetModel4Names($p,['Hits', 'NormalPrice','DirectGuiderRatio','IndirectGuiderRatio'],0);


        echo '<br /> Hits ===================<br/>';
        echo $p -> Hits;
        echo '<br /> NormalPrice ===================<br/>';
        echo $p -> NormalPrice;
        echo '<br /> ===================<br/>';

    }

    public  function test2(){

        $Qty =  null;

        FillVariate($Qty, 50);

        echo $Qty;

        $Amount  = null;
        $Point  = null;
        $Balance =  5.5;
        $Arr1 =  [$Amount , $Point , $Balance ];

        FillVariate($Arr1, 50);
        var_dump($Arr1);


        $His1 = 0;
        $His2=  null;
        $His3 =  null;

        echo '<br /> ===================<br/>';
        var_dump($His3);
        echo '<br /> ===================<br/>';
        FillVariateList([&$His1 , &$His2 , &$His3 ], 5 );
        echo '<br /> ===================<br/>';
        var_dump($His3);
        echo '<br /> ===================<br/>';

// 语法： $var ??= $default;

//// 情况1：变量是 null，会被赋值
//        $myVar = null;
//        $myVar ??= '我是默认值';
//        echo $myVar; // 输出: 我是默认值
//
//// 情况2：变量已经有值，不会被改变
//        $myVar = '我已经有值了';
//        $myVar ??= '我是默认值';
//        echo $myVar; // 输出: 我已经有值了
//
//// 情况3：变量未定义，会被赋值
//        unset($myVar);
//        $myVar ??= '我是默认值';
//        echo $myVar; // 输出: 我是默认值



    }

}


?>