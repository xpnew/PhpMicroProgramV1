<?php
namespace app\utils;
use Ramsey\Uuid\Uuid;

class GeneralTool{


    public static function  IsEmpty($str){
        if(null == $str || '' == $str){
            return true;
        }
        return false;
    }
    public static function  IsNumeric($str){
        if(null == $str || '' == $str){
            return false;
        }
        return is_numeric($str);
    }
    public static function PushString($str,$substr,$len){
        return substr($str . $substr, 0, $len  );
    }

    // 为 备注 追加字符串 备注一般都是255 个字符
    public static function PushRmk($rmk,$new,$len =  255){
        if(null != $rmk && '' != $rmk  && '' != $new){
            $new = ' ' . $new;
        }
        return GeneralTool:: PushString($rmk,$new,$len);
    }

    public static function  EndWith($str,$substr){
        return substr($str,strlen($str)-strlen($substr)) == $substr;
    }   

    public static function  StartWith($str,$substr){
        return substr($str,0,strlen($substr)) == $substr;
    }

    public static function startsWith($haystack, $needle) {
        return (strpos($haystack, $needle) === 0);
    }
    public static function  endsWith($haystack, $needle) {
        return (strpos(strrev($haystack), strrev($needle)) === 0);
    }

    /// 金额转换 元 => 分
    public static function  TransFee2Amount($str) {
        $Rt = (object)[];
        $Rt -> Ready = false;
        $Rt -> Message = '';
        $Rt -> Amount = 0.0;
        if(null == $str || '' == $str){
            $Rt -> Message = '空值';
            return $Rt;
        }
        if(! is_numeric($str)){
            $Rt -> Message = '金额格式错误';
            return $Rt;
        }
        $Amount  = floatval($str);
        $Amount =  $Amount * 0.01;
        

        if($Amount < 0.01){
            $Rt -> Message = '金额不能小于0.01';
            return $Rt;
        }
        $Rt -> Ready = true;
        $Rt -> Amount = $Amount;
        $Rt -> Message = '';
       
        return $Rt;
    }
    /// 金额转换 分 => 元
    public static function  TransAmount2Fee($str) {
        $Rt = (object)[];
        $Rt -> Ready = false;
        $Rt -> Message = '';
        $Rt -> AmountFee = 0;
        if(null == $str || '' == $str){
            $Rt -> Message = '空值';
            return $Rt;
        }
        // php bug 符号和小数点 同时存在会冲突
        // $pattern = ' /^[￥¥]?\d{1,3}(,\d{3})*(\.\d{1,2})?$/';        
        $pattern = '/^\d{1,3}(,\d{3})*(\.\d{1,2})?$/';
        $AmountStr = preg_replace('/[#￥,¥\s]/', '', $str);
        if (!preg_match($pattern, $AmountStr)) {            
            $Rt -> Message = '金额格式错误';
            return $Rt;
        }        

        if(! is_numeric($AmountStr)){
            $Rt -> Message = '金额格式错误';
            return $Rt;
        }
        $Amount  = floatval($AmountStr);
        $Amount =  $Amount * 100.0;
        

        $Rt -> Ready = true;
        $Rt -> AmountFee = round($Amount);
        $Rt -> Message = '';
       
        return $Rt;
    }



    /// 获取物理根目录  因为是thinkphp 所以放在 publick 目录下
    public static function GetPhyRoot($isUsedPublice =  true   ){
        $RootPath = $_SERVER["DOCUMENT_ROOT"];


        if(! GeneralTool::EndWith( $RootPath,'\\'))  {
            $RootPath = $RootPath . '\\';
        }
        if($isUsedPublice){
            if(! GeneralTool::EndWith( $RootPath,'public\\')){
                $RootPath = $RootPath . 'public\\';            
            }          
        }


        if(GeneralTool::IsWindowsOS() == false){
            // 非 Windows 系统
            $RootPath = str_replace('\\','/',$RootPath);        
        }

        return $RootPath;


    }

    public static function IsWindowsOS(){
        $OS =  PHP_OS;
        if(strtoupper($OS) == 'WINNT'   || strtoupper($OS) == 'WIN32'  || strtoupper($OS) == 'WINDOWS' ){
            return true;
        }else{
            return false;
        }
    }

    public static  function CreateDir($DirPath){
        if(GeneralTool::IsWindowsOS() == false){
            // 非 Windows 系统
            $DirPath = str_replace('\\','/',$DirPath);        
        }
        if(!is_dir($DirPath)){
            mkdir($DirPath,0777,true);
        }

    }

    public static function  CreateGuid(){
        $NewId = Uuid::uuid4();
        return $NewId->toString();
    }


        // 注意 ： 有时候 会不好使！  所有 一般要用 CreateGuid ()
    public static function GetGuid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
        }
    }

    public static function GetBillNo(){
        return date('YmdHis') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }



	 public static function GetCurrUrl() {
		// 判断当前协议是 HTTP 还是 HTTPS
		$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		
		// 获取当前脚本的文件路径
		$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
		
		// 获取额外的路径信息
		$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
		
		// 获取完整的请求 URI
		$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
		
		// 返回完整的 URL
		return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
	}
 


}




?>