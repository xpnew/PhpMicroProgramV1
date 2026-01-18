<?php

namespace app\api\controller;
use app\comm\CommControllerBase;

use think\Controller;
use think\Request;

class Region extends CommControllerBase
{



    public function index()
    {
        return 'Region';
    }   


    protected  $CodeAlias  ='code';
    protected $NameAlias  = 'name';

    public function CommRegion(){
        $LevelId = input('LevelId','');
        $ParentId = input('ParentId','');
        $Fromtype = input('Fromtype','');
        if( null != $Fromtype && '' != $Fromtype){
            if('1'== $Fromtype){
                $this -> CodeAlias = 'RegionId';
                $this -> NameAlias = 'RegionName';
            }

        }

        $ArrData = [];
        if( 1 ==  $LevelId ){ // 省份           
            $ArrData = $this->GetProvince4Region();
        }   
        else if( 2 ==  $LevelId ){ // 市           
            $ArrData = $this->GetCity4Region($ParentId);
        }   
        else if( 3 ==  $LevelId ){// 区县            
            $ArrData = $this->GetCounty4Region($ParentId);
        }   
        else{
            return $this->SendJErr('必须指定正确的级别ID');
        }

        return $this->SendJOk('查询成功',1,$ArrData);
    }

    public function GetCity4Region($ProvinceId){ 
    
        $Model  = new \app\Models\Area_CityT();
        $where = [];
        if( null != $ProvinceId && '' != $ProvinceId ){
            $where['province_id'] = $ProvinceId;
        }
        $data = $Model -> where($where)
        -> field('province_id as parentid,  city_id as '.$this->CodeAlias.',city_name as '.$this->NameAlias) 
        -> order(['province_id'=>'asc','city_id'=>'asc'])-> select();  
        $data = $data->toArray();
        return $data;
    }
    public function GetCounty4Region($CityId){ 
    
        $Model  = new \app\Models\Area_CountyT();
        $data = $Model -> where('city_id',$CityId)
        -> field('county_id as '.$this->CodeAlias.',county_name as '.$this->NameAlias) -> select();  
        $data = $data->toArray();
        return $data;
    }

    protected function GetProvince4Region(){ 
        $Model  = new \app\Models\Area_ProvinceT();
        $data = $Model -> field('province_id as '.$this->CodeAlias.',province_name as '.$this->NameAlias.'') -> select();  
        $data = $data->toArray();
        return $data;
    }   

}

?>