<?php
namespace app\comm\Token;


class TokenItem
{
    public $UserId = 0;
    public $UserName = "";
    public $NickName = "";
    public $RealName = "";
    public $Mobile = "";
    public $OpenId = "";




    public $CreateTime = null;
    public $ExpireTime = null;
    public $Token = "";

    public $CreateTS  = 0;
    public $ExpireTS = 0;


    // 其他信息
    public $OtherInfo = null;


}



?>