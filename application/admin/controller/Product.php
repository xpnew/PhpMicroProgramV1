<?php
namespace app\admin\controller;
use think\Controller;

use think\facade\Log;

class Product extends AdminBase
{
    public function index()
    {
        $this -> _InitViewData();
        return $this->fetch();
    }
    protected function _InitViewData()
    {
        parent::_InitViewData();
        $this->assign('title', '商品列表');
        $this->assign('ProductClassList', \app\Models\Product_ClassT::select());

    }
    public function query(){
        $data =[  ];
        $ClassName = input('ClassName','');
        $ClassId = input('ClassId','');
        $ProductName = input('ProductName','');
        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量


        $where = [];
        if($ClassName != ''){
            $where[] = ['ClassName','like','%'.$ClassName.'%'];
        }else{
            $where[] = ['Id','>',0];

        }
        if($ClassId != ''){
            $where[] = ['ClassId','=',$ClassId];
        }        
        if($ProductName != ''){
            $where[] = ['ProductName','like','%'.$ProductName.'%'];
        }        
        $Product_InfoT= new \app\Models\Product_InfoV();

        $data = $Product_InfoT -> where($where) 
        -> order(['Tops' => 'desc','Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $Product_InfoT -> where($where) -> count();
        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }    

    public function add(){
        $this -> _InitViewData();
        // $Model =   \app\Models\Product_ClassT::create();
        
        // $pro0 = new \app\Models\ProductInfo();
        // $pro1 = new \app\Models\Product_InfoT();
        $Model  = new \app\Models\Product_InfoT();
        $Model -> Id = 0;  //
        $Model -> ClassId = 0;  //
        $Model -> ProductName = ''; // 商品名称
        $Model -> ProductCode = ''; //
        $Model -> ProductPic = ''; // 商品标题图
        $Model -> CreateTime = ''; // 创建时间
        $Model -> UpdateTime = ''; // 修改时间
        $Model -> ProductCot = ''; // 商品介绍
        $Model -> Tops = 0;  // 推荐，由大到小排列
        $Model -> Hits = 0;  // 点击数
        $Model -> NormalPrice = 0;  // 标准价
        $Model -> DiscountPrice = 0;  // 标准价
        $Model -> BuyCount = 0;  // 购买次数
        $Model -> SellPoints = 0;  // 购买次数
        $Model -> DirectGuiderRatio = 0;  // 购买次数
        $Model -> IndirectGuiderRatio = 0;  // 购买次数
        $Model -> ReleaseTime = ''; // 发布时间
        $Model -> Summary = ''; // 发布时间
        $Model -> Tags = ''; // 发布时间
        // $arr =  $Model -> toArray();
        // var_dump($Model);
        // var_dump($arr);
        // echo  '转换完的数组长度： ' . count($arr);
        $this -> SayLog('尝试输出： ' , $Model);
        $this->assign('Model', $Model);

        $this->assign('ProductClassList', \app\Models\Product_ClassT::select());

        // if(1 == 1 ){
        //     return "";
        // }
        return $this->fetch();
    }
    public function edit(){
        $this -> _InitViewData();
        if(!input('?id')){
            return $this->error('参数错误');
        }

        $Id = input('id',0);
        $db = new \app\Models\Product_InfoT();

        $Model = $db->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->error('参数错误');
        }   



        $this-> SayLog('尝试输出： ' , $Model);

        $this->assign('Model', $Model);
        $this->assign('ProductClassList', \app\Models\Product_ClassT::select());
        return $this->fetch('add');
    }    

    public function del(){
        if(!input('?Id')){
            return  $this->SendJErr('参数错误');  
        }
        $db= new \app\Models\Product_InfoT();

        $Model = $db->where(['Id'=>$Id])->find();
        if(!$Model){
            return $this->SendJErr('参数错误');
        }   
        $Id = input('Id',0);
        $db->where(['Id'=>$Id])->delete();
        

        return $this->SendJok('删除成功');
    }
    public function save(){

        $db_class = new \app\Models\Product_ClassT();
        $ExistClass = $db_class -> where( ['Id'=> intval( input('ClassId',0) ) ] ) -> find();
        if( !$ExistClass ){
            return $this->SendJErr('参数错误,分类 不存在');
        }
        $InputModel = $this->request->post();
        if(0  ==  $ExistClass-> EnablePointBuy){
            if(!input('?SellPoints') || 0 < $InputModel['SellPoints'] ){
                return $this->SendJErr('参数错误,分类为不是积分兑换商品，必须设置积分为0或负数');
            }
        }
        if(40002000  ==  $ExistClass-> ProductZoneId){
//            echo  '' .  input('?DirectGuiderRatio');
            if(!input('?DirectGuiderRatio') || !input('?IndirectGuiderRatio') ){
                return $this->SendJErr('参数错误,您 选了一个优先区的商品， 请输入 直推奖比例 间推奖比例1');
            }
            if(null ==  $InputModel['DirectGuiderRatio'] || null ==  $InputModel['IndirectGuiderRatio'] ){
                return $this->SendJErr('参数错误,您 选了一个优先区的商品， 请输入 直推奖比例 间推奖比例2');
            }

            if(0 > $InputModel['DirectGuiderRatio'] || 0 > $InputModel['IndirectGuiderRatio'] ){
                return $this->SendJErr('参数错误,直推奖比例 间推奖比例 数值不对');
            }
            if(100 < $InputModel['DirectGuiderRatio'] + $InputModel['IndirectGuiderRatio'] ){
                return $this->SendJErr('参数错误,直推奖比例 间推奖比例 数值不对');
            }
        }



        $Id = isset($InputModel['Id']) ? intval($InputModel['Id']) : 0;
        $db= new \app\Models\Product_InfoT();
        if($Id > 0){        
            $db->save($InputModel,['Id'=>$Id]);
        }else{       
            $db->save($InputModel);
        }
        return $this->SendJOk('保存成功');
    }


}


?>