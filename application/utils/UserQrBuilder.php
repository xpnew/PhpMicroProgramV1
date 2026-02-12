<?php

namespace app\utils;

<?php

/**
 * UserQrBuiler
 * 用于根据微信小程序参数生成小程序二维码
 */
class UserQrBuiler {

    private $accessToken;
    private $lastError = '';
    private $qrImageData = null; // 用于存储Build生成的图片数据

    /**
     * 构造函数
     * @param string $token 微信公众号/小程序的 ACCESS_TOKEN
     */
    public function __construct($token) {
        $this->accessToken = $token;
    }

    /**
     * 获取二维码图片资源 (二进制数据)
     * @param string $path 小程序页面路径，例如 /pages/index/index
     * @param int $width 二维码宽度，默认430
     * @return $this 返回$this实现链式操作
     */
    public function Build($path, $width = 430) {
        $url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token={$this->accessToken}";

        // 构建POST数据
        $postData = json_encode([
            'path' => $path,
            'width' => $width
        ]);

        // 初始化CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        // 关键设置：接受二进制数据
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postData)
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // 检查错误
        if ($curlError) {
            $this->lastError = "CURL Error: " . $curlError;
            $this->qrImageData = false;
            return $this;
        }

        if ($httpCode != 200) {
            $this->lastError = "HTTP Error: " . $httpCode . " - " . $result;
            $this->qrImageData = false;
            return $this;
        }

        // 检查微信返回的错误码 (JSON格式错误)
        $jsonResult = json_decode($result, true);
        if (is_array($jsonResult) && isset($jsonResult['errcode'])) {
            $this->lastError = "WeChat API Error: [{$jsonResult['errcode']}] {$jsonResult['errmsg']}";
            $this->qrImageData = false;
            return $this;
        }

        // 成功：存储二进制图片数据
        $this->qrImageData = $result;
        $this->lastError = '';
        return $this;
    }

    /**
     * 保存二维码到文件
     * @param string $filePath 文件路径，支持 '@' 前缀去除
     * @return bool
     */
    public function Save($filePath) {
        // 如果Build没有成功执行，直接返回false
        if ($this->qrImageData === null) {
            $this->lastError = "请先调用 Build() 方法生成二维码";
            return false;
        }

        if ($this->qrImageData === false) {
            return false; // Build已执行但失败了
        }

        // 处理路径中的 '@' 符号 (如：@/Public/... -> /Public/...)
        $savePath = preg_replace('/^@/', '', $filePath);

        // 确保目录存在
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // 写入文件
        $bytes = file_put_contents($savePath, $this->qrImageData);
        if ($bytes === false) {
            $this->lastError = "文件写入失败: {$savePath}";
            return false;
        }

        return true;
    }

    /**
     * 获取最后的错误信息
     * @return string
     */
    public function getLastError() {
        return $this->lastError;
    }
}

// --- 调用示例 ---

// 1. 实例化
$Token = 'YOUR_ACCESS_TOKEN_HERE'; // 替换为真实的Token
$qb = new UserQrBuiler($Token);

// 2. 按照你的要求调用：先Build生成，再Save保存
// 注意：Build现在接收你指定的路径和宽度
$qb->Build('/pages/about/index?id=96', 430);

// 3. 保存成图片
if ($qb->Save('@/Public/UserQr/96.png')) {
    echo "二维码生成并保存成功！";
} else {
    echo "失败: " . $qb->getLastError();
}

?>