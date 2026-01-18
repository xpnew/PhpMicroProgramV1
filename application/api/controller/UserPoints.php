<?php
namespace app\api\controller;
use think\Controller;
use think\Request;

use \app\Models\Client_PointLogT as PageDBModel;
use \app\utils\GeneralTool;

class UserPoints extends ApiBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return 'api test ok';
    }
    public function query(){
        $data =[  ];
        $Address = input('Address','');
        $UserId = input('UserId','');
        $AddressName = input('AddressName','');


        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量


        $where = [];
        if($Address != ''){
            $where[] = ['FullAddress','like','%'.$Address.'%'];
        }else{
          

        }
        if($UserId != ''){
            $where[] = ['ClientUserId','=',$UserId];
        }        
        if($AddressName != ''){
            $where[] = ['AddressName','like','%'.$AddressName.'%'];
        }        
 
        $DB= new PageDBModel();

        $data = $DB -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        $data = $data->toArray();    
        // 返回数据      
        return $this->SendJOk('查询成功',1,$data); 
    }    
    public function Save()
    {

        $UserId =  \think\facade\Request::param('UserId');

        if(null == $UserId || '' == $UserId ){
             $UserId = input('UserId','');
        }
        if(null == $UserId || '' == $UserId ){
            return $this->SendJErr('用户ID不能为空');
        }

        $InputModel = $this->request->post();
        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;

        $IsNew = $Id == 0;

        $where = [];

        $where[] = ['ClientUserId','=',$UserId];
        $where[] = ['IsFinished','=',0];
        
        $DB= new PageDBModel();
        $Exist = $DB -> where($where) ->find();
        if($Exist != null){
            // return $this->SendJErr('已经存在未处理的提现申请');
        }
        $AmountFee  = 0;
        $AmountStr =  $InputModel['Amount'];
        // php bug 符号和小数点 同时存在会冲突
        // $pattern = ' /^[￥¥]?\d{1,3}(,\d{3})*(\.\d{1,2})?$/';        
        $pattern = '/^\d{1,3}(,\d{3})*(\.\d{1,2})?$/';
        $AmountStr = preg_replace('/[#,¥\s]/', '', $AmountStr);
        if (!preg_match($pattern, $AmountStr)) {            
            return $this->SendJErr('金额的格式错误');
        }
        // 检查是否包含小数点
        if (strpos($AmountStr, '.') !== false) {
            // 如果有小数点，转换为浮点数，乘以100，然后四舍五入或直接取整
            // 这里使用 round 避免浮点数精度问题，确保转为分
            $AmountFee =  (int) round((float) $AmountStr * 100);
        } else {
            // 如果没有小数点，直接乘以100
            $AmountFee =  (int) $AmountStr * 100;
        }

        if($AmountFee <= 0){
            return $this->SendJErr('提现金额必须大于0');
        }
        $mng =  \app\comm\SysSetCacheMng::getIns();
        $WithdrawMin =  (int)$mng -> GetSet('WithdrawMin'); // 最小提现金额 这个按元 单位

        if($AmountFee < $WithdrawMin * 100){
            return $this->SendJErr('提现金额不能小于' . $WithdrawMin);
        }

        $InputModel['OrderAmountFee'] =  $AmountFee;
        //$InputModel['PayAmountFee'] =  $AmountFee;
        $InputModel['PaymentFee'] =  0;

        $Client_UserT = new \app\Models\Client_UserT();
        $UserModel = $Client_UserT -> where(['Id'=>$UserId]) ->find();
        if($UserModel == null){
            return $this->SendJErr('用户不存在');
        }
        $InputModel['ClientRealName'] =  $UserModel-> RealityName;
        $InputModel['ClientNickName'] =   $UserModel-> NickName;
        // die();
        // $pro = \app\Models\Product_InfoT::get($id);
        // if($pro == null){
        //     return $this->SendJErr('商品不存在');
        // }
        // $Price = null;

        if($IsNew){        
            $DatePart =  date('Ymd');
            $InputModel['OrderNo'] = GeneralTool::CreateGuid();
            $InputModel['Gid'] = $InputModel['OrderNo'] ;
            $InputModel['BillNo'] = GeneralTool::GetBillNo();
            $InputModel['DatePart'] = $DatePart;
            $InputModel['ClientUserId'] = $UserId;
            $InputModel['IsAudit'] =0;
            $InputModel['IsSuccess'] = 0;
            $InputModel['IsFinished'] = 0;
            $InputModel['RequestURL'] =  $this->request->url(true);            
            $InputModel['CreateTime'] = date('Y-m-d H:i:s');
            $InputModel['CreateTS'] = time();
            //echo '<br> will save new  <br>';
            $DB->save($InputModel);
            return $this->SendJErr('用户不存在');
        }else{       
            $InputModel['UpdateTime'] = date('Y-m-d H:i:s');
            $DB->save($InputModel,['Id'=>$Id]);
        }



        return $this->SendJOk('保存成功');

    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }




    public function GetUserId(){

               $UserId =  \think\facade\Request::param('UserId');

                 $UserId2 = input('UserId','-9999');
                 echo 'input UserId2 = ' . $UserId2;
                 echo '<br>';

                return 'api test  userid: ' . $UserId;
    }

}