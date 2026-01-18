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



    public function test1()
    {

        $Id =  \app\Utils\GeneralTool::CreateGuid();   
                 echo $Id;      
        echo '<br />';
        return "";
    }

}


?>