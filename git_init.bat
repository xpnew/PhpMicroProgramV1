@echo off
:: 说明：请先复制你的Git远程仓库地址（如：https://gitee.com/xxx/xxx.git），然后运行本脚本。

echo 正在获取剪贴板中的Git地址...
:: 1. 获取剪贴板内容并赋值给变量
for /f "delims=" %%i in ('powershell -command "Get-Clipboard"') do set "GIT_URL=%%i"

:: 2. 安全检查：确保剪贴板里有内容
if "%GIT_URL%"=="" (
    echo 错误：未检测到剪贴板内容，请先复制Git仓库地址！
    pause
    exit /b
)

echo 检测到Git地址: %GIT_URL%
echo.

:: 3. 执行Git操作
git init
git remote add origin %GIT_URL%
git pull origin master --allow-unrelated-histories
if %errorlevel% neq 0 git pull origin main --allow-unrelated-histories

git add .
git commit -m "Initial commit"
git push -u origin master
if %errorlevel% neq 0 git push -u origin main

echo.
echo 操作完成！
pause