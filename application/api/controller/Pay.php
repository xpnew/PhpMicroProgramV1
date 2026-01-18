<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use \app\Models\Client_OrderItemT;
use \app\Models\Client_OrderT;
use \app\Models\Client_BuyCarItemT;
use \app\Models\Product_InfoT;
use \app\Models\Client_UserT;
use \app\Models\Biz_PayBillT;
use \app\utils\GeneralTool;

use app\comm\WxPay\WxPayConfig;
use app\comm\WxPay\WxPayApi;
use app\comm\WxPay\WxPayUnifiedOrder;
use app\comm\WxPay\WxPayResults;

require_once  __DIR__ . "/../../Comm/WxPay/WxPay.Api.php";
require_once  __DIR__ . "/../../Comm/WxPay/WxPay.Config.php";
require_once  __DIR__ . "/../../Comm/WxPay/WxPay.Data.php";
require_once  __DIR__ . "/../../Comm/WxPay/WxPay.Exception.php";


class Pay extends ApiBase
{




    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function Create()
    {
        $UserId =  input('UserId',-9999);
        $OrderId =  input('OrderId',-9999);
        $OpenId =  input('OpenId','');
        $request = request();

        $orderdb = new Client_OrderT();
        $PayBill = new Biz_PayBillT();
        $order =   $orderdb -> where('Id',$OrderId) -> find();

        $UserDb = new Client_UserT();
        $user =   $UserDb -> where('Id',$UserId) -> find();
        if( $user == null ){
                return $this -> SendJErr('用户不存在');
        }        
        if('' ==  $OpenId ){
    
            $OpenId =  $user -> OpenId;
            if( !isset($OpenId) || ''  ==  $OpenId ){
                return $this -> SendJErr('用户微信标识不存在');
            }            
        }   

        if( !$order ){
            return $this -> SendJErr('订单不存在');
        }   
        $PayPrice =  $order -> PayPrice;
        if( !$PayPrice ){
            $PayPrice =  $order -> TotalPrice;
        }
        if( $PayPrice <= 0 ){
            return $this -> SendJErr('订单金额错误');
        }        
        $PayBill -> ClientUserId =  $UserId;
        $PayBill -> OrderId = $OrderId;
        $PayBill -> OrderNo = $order -> OrderNo;
        // $PayBill -> OrderAmountFee =  intval($order -> PayPrice *100); 
        $PayBill -> OrderAmountFee = 1;

        $PayBill -> Gid = GeneralTool::CreateGuid();  
        $PayBill -> BillNo = GeneralTool::GetBillNo();
        $PayBill -> BillStatus = 0;
        $PayBill -> CreateTime = date('Y-m-d H:i:s');

        $PayBill -> RequestURL =   $request->url(true) ; //GeneralTool::GetCurrUrl();  //request()->url();
        $PayBill -> CallbackURL = WxPayConfig::NOTIFY_URL;
        // $PayBill -> BillURL = request()->domain() . '/api/PayNotice/Handle';

        $PayBill -> Isfinish = 0;
        $PayBill -> IsAudit = 0;
        $PayBill -> IsSuccess = 0;
        $PayBill -> Isfinish = 0;
        $PayBill -> Isfinish = 0;

        $PayBill -> save();


        $uo  =  new WxPayUnifiedOrder();

        $uo -> SetOut_trade_no( $PayBill -> BillNo );
        $uo -> SetTotal_fee(  $PayBill -> OrderAmountFee  );
        $uo -> SetBody( '订单支付 : ' . $order -> OrderNo  );
        $uo -> SetTrade_type( 'JSAPI' );
        $uo -> SetOpenid( $OpenId );

        $result =  WxPayApi::unifiedOrder( $uo );

        if(isset($result['return_code']) && $result['return_code'] != 'SUCCESS' ){
            $this -> SayLog('Pay Create unifiedOrder fail :', $result );
            return $this -> SendJErr('微信支付下单失败 : ' . $result['return_msg'] );
        }
		if(isset($result['result_code']) &&  $result['result_code'] == 'FAIL'){ 	
            $this -> SayLog('Pay Create unifiedOrder fail :', $result );
            return $this -> SendJErr('微信支付下单失败 : ' . $result['err_code_des'] );
        }

        $this-> Msg -> Body =  $PayBill -> BillNo;


        $time_stamp = time();
		$pack	= 'prepay_id=' . $result['prepayid'];

        // $WxPayParam = WxPayApi::GetJsApiParameters($result);
		$prePayParams =array();
		$prePayParams['appId']		=$result['appid'];
		$prePayParams['signType']	= 'MD5';
		// $WxPayParam['prepayid']	=   $result['prepay_id'];
		$prePayParams['nonceStr']	=$result['noncestr'];
		$prePayParams['package']	=$pack;
		$prePayParams['timeStamp']	=$time_stamp;
        $WxPayParam  =   WxPayResults::InitFromArray($prePayParams,true)->GetValues();
        $WxPayParam['appId']	 = ''; // appid 参与签名，但是 前端调用不需要

        return $this -> SendJOK('ok',1, $WxPayParam );

    }
}


?>
