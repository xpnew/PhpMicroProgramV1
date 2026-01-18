<?php
namespace app\test\controller;
use app\Models\Client_User_View as ViewDB;
use app\Models\Biz_MarketingLevelT as DB;
use think\Controller;
use Ramsey\Uuid\Uuid;
use think\db\Where;

class  TestReturnEntity extends Controller
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


    public  function  test03()
    {
        $Msg =  new \app\Comm\CommMsg();
        $Msg -> Title  = '消息';

        $Msg['Body'] = '消息体';

        var_dump($Msg);


    }


    public function ViewUser($id){

        $Client_UserT = new ViewDB();
        $data = $Client_UserT -> where(['Id'=>$id]) ->find();

        var_dump($data) ;
    }

    public  function Test01(){
        $User = new \stdClass();

        $User -> Id = 55;
        $User -> Name = 'Tom';
        echo  '<br /> ========   stdClass =============<br />';

        echo '<br />$User ' .get_class($User). '<br />';
        echo  'gettype: ' . gettype($User);
        echo  '  >> is_array:' .  is_object($User);
        if(is_object($User)){  echo ' <br /> is_object = true <br />';}
        if(is_array($User)){  echo ' <br /> is_array = true <br />';}

        var_dump($User);

        echo '<br />';
        dump($User);

        echo '<br /> try output id: ';
//        echo $User['Id'];
        echo '<br />';
        echo $User -> Id;

        echo '<br />';

        echo  '<br /> ========  (object)[]  =============<br />';
        $People = (object)[];
        $People -> Id = 55;
        $People -> Name = 'Mike';
        $People -> Phone = '05555555555';



        echo '<br /> $data2 ' .get_class($People). '<br />';
        echo  'gettype: ' . gettype($People);
        echo  '  >> is_array:' .  is_array($People);
        if(is_object($People)){  echo ' <br /> is_object = true <br />';}
        if(is_array($People)){  echo ' <br /> is_array = true <br />';}

        var_dump($People);

        echo '<br />';
        dump($People);




    }

    public  function Test02()
    {
        $id = 2;
        $Client_UserT = new DB();
        $data1 = $Client_UserT -> where(['Id'=>$id]) ->find();

        echo  '<br /> ========   New Instance .where(id)  find =============<br />';
        echo '<br />$data1 ---->> ' .get_class($data1). '<br />';

        var_dump($data1);

        echo '<br />';
        dump($data1);

        echo '<br />';
        echo '<br /> ========  Static .where(id)   find=============<br />';
//        $date12 =  DB::where('id',$id)->find();
        $date12 =  DB::get($id);
        echo '<br />$date12 ---->>  ' .get_class($date12). '<br />';
        var_dump($date12);

        echo '<br />';
        dump($date12);

        echo  $id . '<br /> ========  获得字段的值   =============<br />';

        echo $data1['Id'];
        echo '<br />';
        echo $data1 -> Id;

        echo '<br />end';

    }



    public  function TestReadUser($id){

        echo  'controller: ' . get_class($this)  .'<br />';
//        echo  $id . '<br /> ========  Static .get(id)  =============<br />';
//
//        $User =  \app\Models\Client_User_View::get($id);
//
//        var_dump($User);

        echo '<br />';



        $Client_UserT = new DB();
        $data1 = $Client_UserT -> where(['Id'=>$id]) ->find();

        echo  '<br /> ========   New Instance .where(id)  find =============<br />';
        echo  'gettype: ' . gettype($data1);
        echo  '  >> is_array:' .  is_array($data1);
        echo '<br />$data1 ' .get_class($data1). '<br />';

        var_dump($data1);

        echo '<br />';
        dump($data1);

        echo '<br />';
        echo  '<br /> ========   New Instance .where(id)  select =============<br />';


        $data2 = $Client_UserT -> where(['Id'=>$id]) ->select();
        echo  'gettype: ' . gettype($data2);
        echo  '  >> is_array:' .  is_array($data2);
        echo '<br /> $data2 ' .get_class($data2). '<br />';
        var_dump($data2);

        echo '<br />';
        dump($data2);

        echo  '<br /> ========  Static .get(id)  =============<br />';


        $Data11 =  DB::get($id);
        echo  'gettype: ' . gettype($Data11);
        echo  '  >> is_array:' .  is_array($Data11);
        echo '<br />$Data11 ' .get_class($Data11). '<br />';

        var_dump($Data11);


        echo '<br />';
        dump($Data11);

        echo '<br /> ========  Static .where(id)   find=============<br />';
        $date12 =  DB::where('id',$id)->find();
        echo  'gettype: ' . gettype($date12);
        echo  '  >> is_array:' .  is_array($date12);
        echo '<br />$date12 ' .get_class($date12). '<br />';
        var_dump($date12);

        echo '<br />';
        dump($date12);

        echo  '<br /> ========  Static .where(id)   select=============<br />';

        $date13 =  DB::where('id',$id)->select();
        echo  'gettype: ' . gettype($date13);
        echo  '  >> is_array:' .  is_array($date13) .'★';
        if(is_array($date13)){  echo ' <br /> is_array = true <br />';}
        echo '<br />$date13 ' .get_class($date13). '<br />';


        var_dump($date13);
        echo '<br />';
        echo '<br />';
        echo  '<br /> ========  Static .where(id)   select  dump =============<br />';
        dump($date13);

        echo '<br /> ========  Static .where(PeerAwardMax)   =============<br />';

        echo '<br /> ========  Static .where(PeerAwardMax)   find=============<br />';
        $date22=  DB::where('PeerAwardMax',$id)->find();
        echo  'gettype: ' . gettype($date22);
        echo  '  >> is_array:' .  is_array($date22);
        echo '<br />$date22 ' .get_class($date22). '<br />';
        var_dump($date22);

        echo '<br />';
        dump($date22);

        echo  '<br /> ========  Static .where(PeerAwardMax)   select=============<br />';

        $date23 =  DB::where('PeerAwardMax',$id)->select();
        echo  'gettype: ' . gettype($date23);
        echo  '  >> is_array:' .  is_array($date23);
        echo '<br />$date23 ' .get_class($date23). '<br />';


        var_dump($date23);
        echo '<br />';
        echo '<br />';
        echo  '<br /> ========  Static .where(id)   select  dump =============<br />';
        dump($date23);


    }

    public function test1()
    {

    }

}


?>