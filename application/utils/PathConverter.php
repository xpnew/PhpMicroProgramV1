<?php

namespace app\utils;

class PathConverter {
    /**
     * 将用户路径转换为实际存储路径
     *
     * @param string $userPath 用户输入的路径（如："/UserQr/1.jpg"）
     * @return string 转换后的物理存储路径
     */
    public static function ToStoragePath($userPath) {
        // 判断是否以"/"开头的地址
        if (strpos($userPath, '/') === 0) {
            // 补全public目录前缀（去除可能多余的开头斜杠）
            $userPath = ltrim($userPath, '/');
        }

        //$relativePath = 'public/' . $userPath;
        //$_SERVER['DOCUMENT_ROOT'] 指定thinkphp 的public 所以，上面的注释掉了
        // 获取根目录路径并适配Windows/Linux
        $rootPath = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);

        // 构造真实路径并规范化目录分隔符
        $realPath = $rootPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $userPath);

        return $realPath;
    }

    /**
     * 将用户路径转换为Web访问路径
     *
     * @param string $userPath 用户输入的路径
     * @return string Web可访问的URL路径
     */
    public static function ToWebPath($userPath) {
        return '/Public/' . ltrim(str_replace('\\', '/', $userPath), '/');
    }

    /**
     * 自动处理Linux文件权限
     *
     * @param string $filePath 文件物理路径
     */
    public static function SetLinuxPermissions($filePath) {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && file_exists($filePath)) {
            chmod($filePath, 0644);
            $dir = dirname($filePath);
            if (is_dir($dir)) {
                chmod($dir, 0755);
            }
        }
    }
}
