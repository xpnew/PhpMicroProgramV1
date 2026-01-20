<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use \app\Models\Client_OrderItemT;
use \app\Models\Client_OrderT;
use \app\Models\Client_BuyCarItemT;
use \app\Models\Product_InfoT;
use \app\Models\Client_UserT;
use \app\Models\Clinet_BonusLogT;
use \app\utils\GeneralTool;
use app\comm\WxPay\WxPayNotify;
use think\facade\Log;
use think\facade\Route;


class PayNotice extends WxPayNotify
{




    // /**
    //  * 删除指定资源
    //  *
    //  * @param  int  $id
    //  * @return \think\Response
    //  */
    // public function CallBack()
    // {
    //     $Notify = new WxPayNotify();
    //     $UserId =  input('UserId',-9999);
    //     $OrderId =  input('OrderId',-9999);

    //     $msg = "OK";
	// 	$result = $this->NotifyProcess($data, $msg);
		
	// 	if($result == true){
	// 		$Notify->SetReturn_code("SUCCESS");
	// 		$Notify->SetReturn_msg("OK");
	// 	} else {
	// 		$Notify->SetReturn_code("FAIL");
	// 		$Notify->SetReturn_msg($msg);
	// 	}
	// 	return $result;


    // }

    public function Test(){
        return 'ok test PayNotice';
    }

    public function NotifyProcess($data, &$msg)
    {
        $this -> SayLog('PayNotice NotifyProcess data:', $data);
        $this -> SayLog('PayNotice NotifyProcess data type :',   is_array($data));
        $this -> SayLog('PayNotice NotifyProcess msg:', $msg);


        $status = $data['return_code'] == 'FAIL';


        if($data['return_code'] != 'SUCCESS'){ 		         
            
            $msg = '支付失败2222';
            $this -> SayLog('PayNotice NotifyProcess msg:', $msg);
            return true;
        }


        if(isset($data['result_code']) && $data['result_code'] == 'FAIL'){
            $orderNo = $data['out_trade_no']; //商户订单号
            $this -> SayLog('PayNotice NotifyProcess orderNo:', $orderNo);
            $WxPayId = $data['transaction_id']; //微信支付订单号

            $BillDb = new \app\Models\Biz_PayBillT();

            $BillItem = $BillDb -> where('BillNo',$orderNo) -> find();
            if( !$BillItem ){
                $msg = '交易流水不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);

                return false;

            }
            if(1 ==  $BillItem -> IsFinished ){
                $msg = '交易流水已经处理完成';
                $this -> SayLog('PayNotice NotifyProcess msg:' + $msg, $BillItem );

                return true;

            }

            $BillItem -> PayPlatNo = $WxPayId;
            $BillItem -> IsFinished = 1;
            $BillItem -> IsAudit = 1;
            $BillItem -> IsSuccess = 0;
            $BillItem -> FinishedTime = date('Y-m-d H:i:s');
            $BillItem -> FinishedTS =  time();
            if( isset($data['err_code_des']) ){

                $BillItem -> Rmk =$BillItem -> Rmk . $data['err_code_des'];
                if( strlen( $BillItem -> Rmk) > 450 ){
                    $BillItem -> Rmk = substr( $BillItem -> Rmk, 0, 450);
                }       
            };
            

            $BillItem -> save();

            $orderdb = new Client_OrderT();
            $order =   $orderdb -> where('Id',$BillItem-> OrderId) -> find();
            if( !$order ){
                $msg = '订单不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);                
                return false;        
            }   
            $order -> PayStatus = 20005000;
            $order -> OrderStatus = 10002000;
            $order -> UpdateTime = date('Y-m-d H:i:s');
            $order -> PayTime = date('Y-m-d H:i:s');

            $order -> save();

            $userdb = new Client_UserT();
            $user =   $userdb -> where('Id',$order -> ClientUserId) -> find();
            if( $user ){
               /// $user -> HisMonetary += $order -> TotalPrice;
//              $user -> BuyTimes += 1;
                if(0 == $user -> IsPromote|| !isset($user -> IsPromote)){
                    $user -> IsPromote = 1;
                }
                $user -> save();
            }
            return true;
        }

        if(isset($data['result_code']) && $data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no']; //商户订单号
            $this -> SayLog('PayNotice NotifyProcess orderNo:', $orderNo);
            $WxPayId = $data['transaction_id']; //微信支付订单号
            $PayAmountFee =  $data['total_fee']; //支付金额，单位分
            $PayAmountFee = intval( $PayAmountFee );
            $BillDb = new \app\Models\Biz_PayBillT();

            $BillItem = $BillDb -> where('BillNo',$orderNo) -> find();
            if( !$BillItem ){
                $msg = '交易流水不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);

                return false;

            }
             if(1 ==  $BillItem -> IsFinished ){
                $msg = '交易流水已经处理完成';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);
                return true;

            }

            $BillItem -> PayPlatNo = $WxPayId;
            $BillItem -> PayAmountFee = $PayAmountFee;
            
            $BillItem -> IsFinished = 1;
            $BillItem -> IsAudit = 1;
            $BillItem -> IsSuccess = 1;
            $BillItem -> FineshTime = date('Y-m-d H:i:s');
            $BillItem -> FinishTS =  time();

            $BillItem -> save();

            $orderdb = new Client_OrderT();
            $order =   $orderdb -> where('Id',$BillItem-> OrderId) -> find();
            if( !$order ){
                $msg = '订单不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);
                return false;        
            }   

        // $order -> PayStatus = 20005000;
        // $order -> OrderStatus = 10002000;
        // $order -> UpdateTime = date('Y-m-d H:i:s');
        // $order -> PayTime = date('Y-m-d H:i:s');
        // $user -> HisMonetary += $order -> TotalPrice;
        // $user -> BuyTimes += 1;
        // if(0 == $user -> IsPromote|| !isset($user -> IsPromote)){
        //     $user -> IsPromote = 1;
        // }

            $order -> PayStatus = 20005000;
            $order -> OrderStatus = 10002000;
            $order -> UpdateTime = date('Y-m-d H:i:s');
            $order -> PayTime = date('Y-m-d H:i:s');
            $order -> save();

            $userdb = new Client_UserT();
            $user =   $userdb -> where('Id',$order -> UserId) -> find();
            if( $user ){
                $user -> HisMonetary += $order -> TotalPrice;
                $user -> BuyTimes += 1;
                if(0 == $user -> IsPromote|| !isset($user -> IsPromote)){
                    $user -> IsPromote = 1;
                }
                $user -> save();
            }
            return true;
        }


        return true;        
        
    }   
    public function NotifyProcessOld($data, &$msg)
    {
        $this -> SayLog('PayNotice NotifyProcess data:', $data);
        $this -> SayLog('PayNotice NotifyProcess data type :',   is_array($data));
        $this -> SayLog('PayNotice NotifyProcess msg:', $msg);


        $status = $data['return_code'] == 'FAIL';


        if($data['return_code'] != 'SUCCESS'){

            $msg = '支付失败2222';
            $this -> SayLog('PayNotice NotifyProcess msg:', $msg);
            return true;
        }


        if(isset($data['result_code']) && $data['result_code'] == 'FAIL'){
            $orderNo = $data['out_trade_no']; //商户订单号
            $this -> SayLog('PayNotice NotifyProcess orderNo:', $orderNo);
            $WxPayId = $data['transaction_id']; //微信支付订单号

            $BillDb = new \app\Models\Biz_PayBillT();

            $BillItem = $BillDb -> where('BillNo',$orderNo) -> find();
            if( !$BillItem ){
                $msg = '交易流水不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);

                return false;

            }
            if(1 ==  $BillItem -> IsFinished ){
                $msg = '交易流水已经处理完成';
                $this -> SayLog('PayNotice NotifyProcess msg:' + $msg, $BillItem );

                return true;

            }

            $BillItem -> PayPlatNo = $WxPayId;
            $BillItem -> IsFinished = 1;
            $BillItem -> IsAudit = 1;
            $BillItem -> IsSuccess = 0;
            $BillItem -> FinishedTime = date('Y-m-d H:i:s');
            $BillItem -> FinishedTS =  time();
            if( isset($data['err_code_des']) ){

                $BillItem -> Rmk =$BillItem -> Rmk . $data['err_code_des'];
                if( strlen( $BillItem -> Rmk) > 450 ){
                    $BillItem -> Rmk = substr( $BillItem -> Rmk, 0, 450);
                }
            };


            $BillItem -> save();

            $orderdb = new Client_OrderT();
            $order =   $orderdb -> where('Id',$BillItem-> OrderId) -> find();
            if( !$order ){
                $msg = '订单不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);
                return false;
            }
            $order -> PayStatus = 20005000;
            $order -> OrderStatus = 10002000;
            $order -> UpdateTime = date('Y-m-d H:i:s');
            $order -> PayTime = date('Y-m-d H:i:s');

            $order -> save();

            $userdb = new Client_UserT();
            $user =   $userdb -> where('Id',$order -> ClientUserId) -> find();
            if( $user ){
                $user -> HisMonetary += $order -> TotalPrice;
                $user -> BuyTimes += 1;
                if(0 == $user -> IsPromote|| !isset($user -> IsPromote)){
                    $user -> IsPromote = 1;
                }
                $user -> save();
            }
            return true;
        }

        if(isset($data['result_code']) && $data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no']; //商户订单号
            $this -> SayLog('PayNotice NotifyProcess orderNo:', $orderNo);
            $WxPayId = $data['transaction_id']; //微信支付订单号
            $PayAmountFee =  $data['total_fee']; //支付金额，单位分
            $PayAmountFee = intval( $PayAmountFee );
            $BillDb = new \app\Models\Biz_PayBillT();

            $BillItem = $BillDb -> where('BillNo',$orderNo) -> find();
            if( !$BillItem ){
                $msg = '交易流水不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);

                return false;

            }
             if(1 ==  $BillItem -> IsFinished ){
                $msg = '交易流水已经处理完成';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);
                return true;

            }

            $BillItem -> PayPlatNo = $WxPayId;
            $BillItem -> PayAmountFee = $PayAmountFee;

            $BillItem -> IsFinished = 1;
            $BillItem -> IsAudit = 1;
            $BillItem -> IsSuccess = 1;
            $BillItem -> FineshTime = date('Y-m-d H:i:s');
            $BillItem -> FinishTS =  time();

            $BillItem -> save();

            $orderdb = new Client_OrderT();
            $order =   $orderdb -> where('Id',$BillItem-> OrderId) -> find();
            if( !$order ){
                $msg = '订单不存在';
                $this -> SayLog('PayNotice NotifyProcess msg:', $msg);
                return false;
            }

        // $order -> PayStatus = 20005000;
        // $order -> OrderStatus = 10002000;
        // $order -> UpdateTime = date('Y-m-d H:i:s');
        // $order -> PayTime = date('Y-m-d H:i:s');
        // $user -> HisMonetary += $order -> TotalPrice;
        // $user -> BuyTimes += 1;
        // if(0 == $user -> IsPromote|| !isset($user -> IsPromote)){
        //     $user -> IsPromote = 1;
        // }

            $order -> PayStatus = 20005000;
            $order -> OrderStatus = 10002000;
            $order -> UpdateTime = date('Y-m-d H:i:s');
            $order -> PayTime = date('Y-m-d H:i:s');
            $order -> save();

            $userdb = new Client_UserT();
            $user =   $userdb -> where('Id',$order -> UserId) -> find();
            if( $user ){
                $user -> HisMonetary += $order -> TotalPrice;
                $user -> BuyTimes += 1;
                if(0 == $user -> IsPromote|| !isset($user -> IsPromote)){
                    $user -> IsPromote = 1;
                }
                $user -> save();
            }
            return true;
        }


        return true;

    }

    protected function SayLog($title ,$model =  null){
        //$request = \think\facade\Request::instance();
        $pathinfo = \think\facade\Request::pathinfo(); // 获取当前请求的pathinfo
        // $current = Route::getRule()->getRule('current'); // 获取当前路由规则（如果有的话）
        $current =  Route:: getCurrentRule();       
        Log::record('日志输出：' . $title . ' pathinfo=' . $pathinfo . ' current=' . json_encode($current)  );
        if(null != $model){
            Log::record('模型数据：\n'  . json_encode($model)   );
        }
    }
}
