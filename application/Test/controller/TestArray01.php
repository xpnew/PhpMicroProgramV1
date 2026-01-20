<?php

namespace app\Test\controller;

class TestArray01
{

    public  function  test00()
    {
        $Arr1 = [1,2,3];

//        var_dump($Arr1);


        foreach ($Arr1 as $k => $v) {


            echo  '[key : '. $k .']'. $v . PHP_EOL  ;

        }

        echo '<br /> ===================<br/>';


        $Arr2 = [
            'good' => [1,2,3],
            'bad'  => [ 4,5,6 ]];
//        var_dump($Arr2);
        foreach ($Arr2 as $k => $v) {
            echo  '[key : '. $k .']' . PHP_EOL  ;
        }
        echo '<br /> ===================<br/>';
        echo '<br /> ===================<br/>';


        $Amount  = null;
        $Point  = null;
        $Balance =  5.5;
        $Arr3=  [$Amount , $Point , $Balance ];

        $this -> FillArr($Arr3, 0);


        var_dump($Arr3);

        echo '<br /> ===================<br/>';



    }
    public  function  test01(){
        echo '<br /> ===================<br/>';


        $Amount  = null;
        $Point  = null;
        $Balance =  5.5;
        $Arr3=  [&$Amount , &$Point , &$Balance ];

        $this -> FillArr($Arr3, 0);


        var_dump($Arr3);

        echo '<br /> ===================<br/>';
        echo $Amount;
        echo '<br /> ===================<br/>';
        echo $Point;
        echo '<br /> ===================<br/>';
        echo $Balance;
        echo '<br /> ===================<br/>';

    }
    public  function  test02(){
        $Amount  = null;
        $Point  = null;
        $Balance =  5.5;
        $Arr3=  [$Amount , $Point , $Balance ];

        $this -> FillArr([&$Amount,&$Point,&$Balance], 0);

        echo '<br /> ===================<br/>';
        echo $Amount;
        echo '<br /> ===================<br/>';
        echo $Point;
        echo '<br /> ===================<br/>';
        echo $Balance;
        echo '<br /> ===================<br/>';

    }
    public  function  test05(){
        $Amount  = null;
        $Point  = null;
        $Balance =  5.5;
        $Arr3=  [$Amount , &$Point , $Balance ];

//        FillVariate($Arr3, 0);
        $this -> FillArr4($Arr3, 0);
        echo '<br /> $Amount ===================<br/>';
        echo $Amount;
        echo '<br /> ===================<br/>';
        echo $Point;
        echo '<br /> ===================<br/>';
        echo $Balance;
        echo '<br /> ===================<br/>';
        var_dump($Arr3);
        echo '<br /> ===================<br/>';

        FillVariate($Amount, 0);
        echo '<br /> $Amount ===================<br/>';
        echo $Amount;
    }


    function FillArr($target,$def){
        foreach ($target as $k => &$v) {
            if(!isset($target[$k])){
                $target[$k] = $def;
            }
        }
    }
    function FillArr2($target,$def){
        foreach ($target as &$child) {
            if (!isset($child)) {
                $child = $def;
            }
        }
        unset($child);
    }

    ///吸有定义了变量引用，才会保证修改的是传入的变量，但是这时就不能传入匿名数组（字面量）了。
    function FillArr3(&$target,$def){
        foreach ($target as &$child) {
            if (!isset($child)) {
                $child = $def;
            }
        }
        unset($child);
    }
    function FillArr4(&$target,$def){
        foreach ($target as $k => &$v) {
            if(!isset($target[$k])){
                $target[$k] = $def;
            }
        }
    }
}