<?php
namespace app\test\controller;
use think\Controller;

class TestAmount01 extends Controller
{

    
/**
 * 将金额字符串（元）转换为整数分
 * 
 * @param string $amountStr 输入的金额字符串
 * @return int|false 返回分的整数值，如果格式错误返回 false
 */
protected function yuanToCent($amountStr) {
    
    // 1. 格式检查：正则表达式
    // 解释：
    // ^           : 字符串开始
    //          : 匹配可选的人民币符号（注意：这里用了中文全角符号）
    // \s*         : 匹配可选的空白字符
    // \d{1,3}     : 匹配1到3位数字（整数部分开头）
    // (,\d{3})*   : 匹配可选的千分位逗号和三位数字（处理如 12,345.66）
    // (\.\d{1,2})? : 匹配可选的小数点和1到2位小数
    // $           : 字符串结束
    // $pattern = '/^?\s*\d{1,3}(,\d{3})*(\.\d{1,2})?$/';
    $pattern = '/^¥?\d{1,3}(?:,\d{3})*\.\d{2}$/';
    if (!preg_match($pattern, $amountStr)) {
        return false; // 格式不符合要求
    }
    
    // 去除¥符号和千分位分隔符
    $amount = str_replace(['¥', ','], '', $amountStr);
    
    // 转换为分单位
    $cents = (int)($amount * 100);
    return $cents;
    // // 检查是否包含小数点
    // if (strpos($cleanStr, '.') !== false) {
    //     // 如果有小数点，转换为浮点数，乘以100，然后四舍五入或直接取整
    //     // 这里使用 round 避免浮点数精度问题，确保转为分
    //     return (int) round((float) $cleanStr * 100);
    // } else {
    //     // 如果没有小数点，直接乘以100
    //     return (int) $cleanStr * 100;
    // }
}
    public function test() {
        $amountStr = '55.66'; 
        echo 'start <br />';
        $pattern = '/^¥?\d{1,3}(?:,\d{3})?(\.\d{2}?)?$/';
        $pattern = '/^[¥￥]?(\d{1,3}(,\d{3})*|\d+)(\.\d{1,2})?$/';        
        if (!preg_match($pattern, $amountStr)) {
            echo '格式错误 <br />' .$amountStr;
        }
        $result = $this->yuanToCent('55.66');
    
        echo "结果: {$result} \n  <br />";

        $amountStr = '55'; 
        $result = $this->yuanToCent('55');
    
        echo "结果: {$result} \n  <br />";
        if (!preg_match($pattern, $amountStr)) {
            echo '格式错误 <br />' .$amountStr;
        }

        echo 'ok <br />';

    }


    public function index(){


        // --- 测试用例 ---

        $testCases = [
            '55.66',      // 标准格式
            '12,333.66',  // 带千分位逗号
            '¥100',       // 带人民币符号
            '¥1,234.56',  // 符号+逗号+小数
            '0.01',       // 最小单位：1分
            '10',         // 整数
            '-10.50',     // 错误：负数（会被正则拦截）
            'abc',        // 错误：非数字
            '12,34.56'    // 错误：错误的千分位格式
        ];

        foreach ($testCases as $case) {
            $result = $this-> yuanToCent($case);
            if ($result !== false) {
                echo "输入: {$case} -> 输出(分): {$result}\n <br />";
            } else {
                echo "输入: {$case} -> 格式错误\n  <br />";
            }
        }

    }



}


?>