<?php

namespace app\Test\controller;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\AddShortUrlResponseBody\data;
use app\Comm\CommControllerBase;

class TestClient extends CommControllerBase
{

    public function index()
    {
        //
        return $this -> _GetName().'  index is ok' ;
    }




    public function Batch($start, $len =5){

        if(null == $start || 0 == $start){

            return $this->SendJErr('错误的批次号');
        }

        $BaseUserInfo = [
            'RealityName' => '测试用户',
            'NickName' => 'Test User ',
            'Password' => 'e10adc3949ba59abbe56e057f20f883e',
            'Account' => 'auto',
            'RegisterDate' => date('Y-m-d H:i:s'),
            'Sex' => 1,
            'Age' => 5,
            'BuyTimes' => 0,
            'HisMonetary' => 0,
            'PointsHistory' => 0,
            'PointsBalance' => 0,
            'PointsFrozen' => 0,
            'BonusHistory' => 0,
            'BonusBalance' => 0,
            'BonusFrozen' => 0,
            'IsRegionAgent' => 0,
            'ScoreHistory' => 0,
            'ScoreBalance' => 0,
            'ScoreFrozen' => 0,
            'WithdrawHistory' => 0,
        ];

        $Arr = [];

        $DB =  new \app\Models\Client_UserT();

        for($i=0;$i<=$len;$i++){
            $NewUser=  $BaseUserInfo;

            $NewId =  $start+$i;
            $Mobile =  $start+$i;
            $NewUser['Mobile'] = $Mobile;
            $NewUser['RealityName'] = $NewUser['RealityName'] . $NewId;
            $NewUser['NickName'] = $NewUser['NickName'] . $NewId;




            $Arr[] = $NewUser;



            dump($NewUser);

        }

        $DB ->  saveAll($Arr);



    }



}