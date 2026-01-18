<?php
namespace app\admin\controller;
use think\Controller;
use app\Models\SysOptions;
use think\Db;
use think\facade\Log;
use app\Models\Sys_UserT;

class Index extends AdminBase
{
    public function index()
    {
        return $this->fetch();

        // return 'Hello,This is Admin module.';
        // return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }


    public function test1()
    {
        return 'Hello,This is Admin module.Test1.';
    }   
    public function login()
    {
        
        return $this->fetch();
    }


    public function LoginAsync()
    {
        $post = $this->request->post();
        $LoginName = $post['LoginName'];
        $LoginPass = $post['LoginPass'];
        $rememberMe = isset($post['rememberMe']) ? true : false;

        $mng =  null; 
        try { 
            $mng =  \app\comm\SysSetCacheMng::getIns();

        }
         catch (\Exception $e) {
            return json(['Type' => 0, 'Content' => '初始化失败,可能是无法连接数据库，或者检查配置文件！']);
        }    

        // if ($mng) {
        //     return json(['Type' => 0, 'Content' => $mng -> GetSet('LockLogin')]);
        // }
        $Lock =  (int)$mng -> GetSet('LockLogin'); // 获取登录锁定设置

        if (1 <= $Lock && 'admin' !=  $LoginName) {
            $LockMsg =  $mng -> GetSet('LockLoginMsg')  ;
            if(null == $LockMsg || '' ==  trim( $LockMsg) ) {
                $LockMsg =  '登录已经被锁定，系统暂时无法使用！' ;
            }   
            return json(['Type' => 0, 'Content' => $LockMsg ]);

        }


        // $User =  M('Sys_User'); // 假设有一个User模型
        
        try { 
            $User= new Sys_UserT();
            $ExistUser = $User->where(['LoginName' => $LoginName])->find();
        }
         catch (\Exception $e) {
            return json(['Type' => 0, 'Content' => '初始化失败,可能是无法连接数据库，或者检查配置文件！']);
        }        
        $ExistUser = $User->where(['LoginName' => $LoginName])->find();


        if(!$ExistUser) {
            return json(['Type' => 0, 'Content' => '用户不存在']);
        }
        if($ExistUser['Pwd'] !== $LoginPass) {
            return json(['Type' => 0, 'Content' => '密码错误']);
        }
        session('AdminUser', $LoginName);
        return json(['Type' => 1, 'Content' => '登录成功']);
        // // 这里应该添加实际的用户验证逻辑
        // if ($LoginName === 'admin' && $LoginPass === 'admin') {
        //     // 登录成功，设置会话或cookie
        //     session('AdminUser', $username);
        //     if ($rememberMe) {
        //         cookie('AdminUser', $username, 3600 * 24 * 7); // 记住我，保存7天
        //     }
        //     return json(['Type' => 1, 'Content' => '登录成功']);
        // } else {
        //     return json(['Type' => 0, 'Content' => '用户名或密码错误']);
        // }

    }

    public function ModifyPassWord()
    {
        return $this->fetch();

    }
    public function ModifyPassWordAsync()
    { 
        $post = $this->request->post();
        $OldPwd = $post['OldPwd'];
        $NewPwd = $post['NewPwd'];
        $User =  session('AdminUser');
        if (!$User)
        {
            return json(['Type' => 0, 'Content' => '用户未登录']);
        }
        $UserModel= new Sys_UserT();
        $ExistUser = $UserModel->where(['LoginName' => $User])->find();
        if(!$ExistUser) {
            return json(['Type' => 0, 'Content' => '用户不存在']);
        }
        if($ExistUser['Pwd'] !== $OldPwd) {
            return json(['Type' => 0, 'Content' => '旧密码错误']);
        }
        $ExistUser->Pwd = $NewPwd;
        $ExistUser->save();

        return json(['Type' => 1, 'Content' => '修改成功']);
    }   
    public function Logout()
    {
        session('AdminUser', null);
        cookie('AdminUser', null);
        return redirect('admin/index/login');
    }

    private function  GetOptoins($OptionKey)
    {
        // $Options= new \app\admin\model\SysOptions();
        $Options= SysOptions::where('OptionKey',$OptionKey)->find();
        if ($Options)
        {
            return $Options->OptionValue;
        }
        else
        {
            return '';
        }

    }

    private function TransMenuInfo($InputMenu)
    {
        $ParentID = -1;
        $minfo = [];
        $TopMenu =  array_filter($InputMenu, function ($value) {
            return $value['ParentID'] == 0;
        });
        foreach ($TopMenu as $key => $DbMenu) {
            Log::record('测试日志信息' . json_encode( $DbMenu ));
            $ParentID = $DbMenu['ID'];
            Log::record('测试日志信息 ParentID=' . $ParentID );
            $submenu =  array_filter($InputMenu, function ($vv) use ($ParentID) {
                // return $vv['ParentID'] == $DbMenu['ID'];
                return $vv['ParentID'] == $ParentID;
            });
            $NewMenu = new MenuInfoItem($DbMenu);
            foreach ($submenu as $k => $v) {
                $NewMenu->child[] = new MenuInfoItem($v);
            }
            $minfo[] = $NewMenu;
        }
        return $minfo;
    }
    public function Init()
    {
    
        // $info = [
        //     'Name' => 'PhpAdmin4',
        //     'Version' => 'v1.0.0',
        //     'Author' => 'Mr.Lee',
        //     'HomePage' => 'https://gitee.com/mr-lee-233/PhpAdmin4'
        // ];

        $info = new InitInfo();
        // 基础信息对象
        $info -> homeInfo = new IninItemInfo();
        // Logo对象
        $info -> logoInfo = new IninItemInfo();

        // 2026年1月2日 添加检查，本地测试可能会遇到数据库没有启动的问题
        $HomeTitle = '';
        try { 
            $HomeTitle = $this-> GetOptoins('HomeTitle');
        }
         catch (\Exception $e) {
            return json(['ErrorCode' => 1, 'ErrorMessage' => '初始化失败,可能是无法连接数据库，或者检查配置文件！']);
        }

        // $info-> HomeInfo -> title = '欢迎使用 PhpAdmin4';
        $info-> homeInfo -> title = $HomeTitle; /// SysOptions::where ('OptionKey','HomeTitle')-> find()->OptionValue; // 主页


        $info-> homeInfo -> href = $this-> GetOptoins('HomeHref'); //SysOptions::where ('OptionKey','HomeHref') -> find();  //'/admin/index/welcome';

        // $info.homeInfo.href = 'page/welcome.html';
        // $info.logoInfo =[];
        $info-> logoInfo -> title = $this-> GetOptoins('LogoTitle'); //SysOptions::where ('OptionKey','LogoTitle')->find();  //'御享健康';
        $info-> logoInfo -> image = $this-> GetOptoins('LogoIcon'); //SysOptions::where ('OptionKey','LogoIcon')->find();  //'/images/left_logo.png';
        $info-> logoInfo -> href = $this-> GetOptoins('LogoHref'); //SysOptions::where ('OptionKey','LogoHref')->find();  //'';  

        // $info.logoInfo.title = 'PhpAdmin4';
        // $info.logoInfo.image = '/images/logo.png';
        // $info.logoInfo.href = '';
        $DbMenum = Db::table('Sys_Menus')->where('IsShow',1)->order(['Tops' => 'desc','Id' => 'asc' ])->select();
        $info-> menuInfo = $this->TransMenuInfo($DbMenum);

        return json($info);
    }
    public function welcome()
    {
        return $this->fetch();
    }
}


class MenuInfoItem{
    public $title;
    public $icon;
    public $href;
    public $target;
    public $child = [];
    public function __construct($input)
    {
        $this-> title = $input['Title'] ;
        $this-> icon = $input['Icon'] ;
        $this-> href = $input['Href'] ;
        
        $this-> target = '_self' ;

    }

}

class InitInfo{
    public $homeInfo;
    public $logoInfo;
    public $menuInfo;

}
class IninItemInfo{
    public $title;
    public $href;
    public $image;


}
?>

