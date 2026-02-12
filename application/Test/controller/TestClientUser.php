<?php

namespace app\Test\controller;

class TestClientUser
{


    public function TestFaceImg(){

            $Sex =  input('Sex');

        $url =  $this ->_MkFaceUrl($Sex);
        echo '<br /> ===================<br/>';
        echo $url;

        echo '<br /> ===================<br/>';
        echo  "<img src='$url' /> ";

    }
    protected function _MkFaceUrl($sex){
        $SexName= 'boy';
        if(2 ==  $sex) $SexName = 'girl';
        $randomNumber = mt_rand(1, 3);

        $Result =  "/images/FaceImg/{$SexName}{$randomNumber}.jpg";

        return $Result;

    }
}