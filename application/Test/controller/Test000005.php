<?php
namespace app\test\controller;
use app\Models\Client_User_View as ViewDB;
use think\Controller;
use Ramsey\Uuid\Uuid;

class Test000005 extends Controller
{
    public function index()
    {
        echo 'Test000005<br />';
        // 生成标准v4 UUID
        $orderNumber = Uuid::uuid4();
        echo $orderNumber;
        echo '<br />';
        // 输出：f47ac10b-58cc-4372-a567-0e02b2c3d479

        return 'Hello,This is Test module.Test000003.';
    }


    public  function TestReadUser($id){

        echo  'controller: ' . get_class($this)  .'<br />';
        echo  $id . '<br />';

        $User =  \app\Models\Client_User_View::get($id);

        var_dump($User);

        echo '<br />';
        echo '<br />';

        $Client_UserT = new ViewDB();
        $data = $Client_UserT -> where(['Id'=>$id]) ->find();
        var_dump($User);

    }

    public function test1()
    {

    }

}


?>