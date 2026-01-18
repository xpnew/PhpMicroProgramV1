<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Setting extends AdminBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
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

        $where = [];
        $where[] = ['IsShow','>',0];
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
        $this -> SayLog( 'where 参数', $where);
        $ModelList = new \app\Models\Sys_SettingT();

        $data = $ModelList -> where($where) -> order(['Tops' => 'desc','Id'=>'asc'])->select();
        $data = $data->toArray();    
        // 返回数据      

        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
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

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        

        // dump($request);     
        $Id =  $request->post('Id',0);
        $SetVal =  $request->post('SetVal','');   
        $ValType =  $request->post('ValType','');   
        $OldValue =  $request->post('OldVal','');   
        $SetName =  $request->post('Code','');
        // 当 值 为0 时 也不能通过  empty 判断
        // 放弃 empty 判断empty( $SetVal)
        if( null == $SetVal || '' == $SetVal   ){
            return $this->SendJErr('参数错误，参数不能为空！');
        }
        $SetVal = trim( $SetVal );
        $SetVal = str_replace( "'", "''", $SetVal ); // 防止SQL注入
        // 将 SetVal 转换为小写字母
        $SetVal = strtolower($SetVal);


        if($ValType == 'int'){
            if(!is_numeric($SetVal)){
                return $this->SendJErr('参数错误，参数格式不对！参数必须是整数！');
            }
            
        }elseif($ValType == 'decimal'){
            if(!is_numeric($SetVal)){
                return $this->SendJErr('参数错误，参数格式不对！参数必须是小数！');
            }
           
        }elseif($ValType == 'bool'){
            if( $SetVal != '0' &&  $SetVal != '1'  && 'true' != $SetVal && 'false' != $SetVal ){
                return $this->SendJErr('参数错误，参数格式不对！    参数必须是布尔值！（可选 值 ：1，0，true，false）');
            }
        } elseif($ValType == 'date' ||  'time' ==  $ValType || 'datetime' ==  $ValType ){
            $timestamp = strtotime($SetVal);
            if($timestamp === false){
                return $this->SendJErr('参数错误，参数格式不对！参数必须是有效的日期时间格式！');
            }
        }elseif($ValType == 'string'){
            if(strlen($SetVal) > 200){
                return $this->SendJErr('参数错误，参数格式不对！');
            }
        }   
        else{
            return $this->SendJErr('参数错误，参数类型不对：' . $ValType );
        }

        $ModelList = new \app\Models\Sys_SettingT();
        
        $NewModel = [];

        $NewModel['Id'] = $Id;
        $NewModel['SetVal'] = $SetVal;

        if($Id > 0){ 
            $Model = $ModelList->where(['Id'=>$Id])->find();
            if($Model){ 
                $OldValue   = $Model -> Val;
                if($OldValue == $SetVal){
                    return $this->SendJErr('保存成功，无需修改');
                }           

                $Model -> Val = $SetVal;
                $SetName = $Model -> Code;
                $Model -> save();


            }else{
                return $this->SendJErr('参数错误，无法保存');
            }
        }else{
            return $this->SendJErr('参数错误，无法保存');
        }
        $mng =  \app\comm\SysSetCacheMng::getIns();
        $mng -> Set($SetName,$SetVal);
        return $this->SendJOk('保存成功');

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
