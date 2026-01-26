<?php

namespace app\Test\controller;

class TestBase
{


    protected  function _Print($var){

        echo '<br /> ===================<br/>';

        if( is_scalar($var)){
            echo $var;
        }else{
            dump($var);
        }

        echo '<br /> ===================<br/>';
    }

}