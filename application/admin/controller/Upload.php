<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\utils\GeneralTool;
use app\utils\x;
use app\Models\UploadFileModel;

use think\facade\Log;
use \app\Models\UploadFileT;
use \app\Models\Sys_TypeDefinedT;


class Upload extends AdminBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this -> _InitViewData();


        $statusdb  = new \app\Models\Sys_TypeDefinedT();
        $UploadClientTypeDefs =  $statusdb -> where(['GroupId'=>6000,'IsShow'=>1]) -> order(['GroupOrd' ,'TypeId']) -> select();
        $this->assign('UploadClientTypeDefs', $UploadClientTypeDefs);

        return $this->fetch();
    }
    protected function _InitViewData(){
        parent::_InitViewData();
        $this->assign('title', '上传文件日志');
    }
    public function query(){
        $data =[  ];

        $BeginTime = input('BeginTime','');
        $EndTime = input('EndTime','');

        $SaveName = input('SaveName','');
        $ExtName = input('ExtName','');
        $SourceName = input('SourceName','');

        $ClientTypeId = input('ClientTypeId','');
        $IsUsed = input('IsUsed','');


        $PageIndex = input('PageIndex',1);
        $PageSize = input('PageSize',15); // 每页显示数量
        // $PageSize = 26; // 每页显示数量

        $where = [];


        if('' != $BeginTime ){
            $where[] = ['CreateTime','>=',$BeginTime . ' 00:00:00' ];
        }
        if('' != $EndTime ){
            $EndTime = new \DateTime($EndTime . ' 00:00:00');
            $EndTime -> modify('+1 day');
            $EndTime = $EndTime -> format('Y-m-d H:i:s');
            $where[] = ['CreateTime','<=',$EndTime ];        }

        if($SaveName != ''){
            $where[] = ['SaveName','like','%'.$SaveName.'%'];
        }        
        if($ExtName != ''){
            $where[] = ['ExtName','like','%'.$ExtName.'%'];
        }        
        if($SourceName != ''){
            $where[] = ['SourceName','like','%'.$SourceName.'%'];
        }        
            

        if($IsUsed != ''){
            $where[] = ['IsUsed','=',$IsUsed];
        } 
        if($ClientTypeId != ''){
            $where[] = ['ClientTypeId','=',$ClientTypeId];
        }       

        $db= new UploadFileT();

        $data = $db -> where($where) 
        -> order(['Id'=>'desc'])
        -> limit( ( $PageIndex-1) * $PageSize, $PageSize)  ->select();
        // $data = $data->toArray();    
        // 返回数据      
        $this->RecordCount = $db -> where($where) -> count();

        //dump($this-> QMsg);

        return $this->SendQOk('查询成功',0,$data); //  查询返回  layer 专用的消息格式 QueryMsg
    }  


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        ob_start();
        // 获取表单上传文件 例如上传了001.jpg
        $file = $request->file('file');
        // 移动到框架应用根目录/uploads/ 目录下

        $SourceInfo =  $file->getInfo();
        x::Say('SourceInfo:', $SourceInfo);



        $ClientWorkName =  $request->param('ClientWorkName');
        $ClientTypeId =  $request->param('ClientTypeId');
        $ClientTypeName =  $request->param('ClientTypeName');
        $Remark =  $request->param('Remark');
        
        x::Say('file:', $file);
    
        $TypePath = '';
        if(isset($ClientTypeId)){
            $TypePath =  $ClientTypeId .'\\';
        }

        $SaveRootDir =  GeneralTool::GetPhyRoot(true);// $_SERVER["DOCUMENT_ROOT"]; // '../uploads/';

        $SourceName  =  $SourceInfo['name'];
        $nameArr =  explode('.', $SourceName);
        if(count($nameArr) <= 1  ){
            $this-> SendJErr('上传失败：文件格式错误');
        }
        $ExtName  = $nameArr[count($nameArr) - 1];

        //$DatePath =   date('Ymd') ;

        $DatePath =   date('Ymd') ;
        $NewFileName =  date('Ymd_His') . '_' . substr(microtime(), 2, 6) ; // 例如：20230401123045_123456.txt
        // $ExtName = $file->getExtension();
        $SaveSubDir =  'uploads\\' . $TypePath . $DatePath .'\\';

        if(GeneralTool::IsWindowsOS() == false){
            // 非 Windows 系统
            $SaveSubDir = str_replace('\\','/',$SaveSubDir);        
        }

        // $SaveFilePath =   $SaveSubDir . $NewFileName . '.' . $ExtName;
        $SaveName =  $NewFileName . '.' . $ExtName;
        $FullSaveDir =  $SaveRootDir . $SaveSubDir;
        $FullSavePath = $FullSaveDir . $SaveName;

        x::Say('SourceName:', $SourceName);
        x::Say('ExtName:', $ExtName);
        x::Say('FullSavePath:', $FullSavePath);
        x::Say('SaveSubDir:', $SaveSubDir);
        x::Say('IsWindowsOS:', GeneralTool::IsWindowsOS()   );



        GeneralTool::CreateDir($FullSaveDir);

        
        $VisualDir  =  '/uploads/' . $TypePath . $DatePath .'/';
        $VisualDir =  str_replace('\\', '/', $VisualDir);
        x::Say('VisualDir:', $VisualDir);
        $VisualPath = $VisualDir . $SaveName;


        $info =  $file->move($FullSaveDir, $SaveName);
        x::Say('info:', $info);
        $this-> LogError('info', $info  , null  );

        
        
        // if( ! file_exists($FullSavePath) ){
        //     Log::record('上传失败：' . $FullSavePath . ' 不存在');

        //     throw new \Exception('上传失败：' . $FullSavePath . ' 不存在');
        // }
        // x::Say('UploadModel:', $UploadModel);

        $UploadModel = new UploadFileModel();
        $UploadModel -> SourceName =  $SourceName;

        $UploadModel -> SaveName = $info->getSaveName();

        $UploadModel -> ExtName = $info->getExtension();
        $UploadModel -> FileSize = $info->getSize();


        $UploadModel -> SaveRootDir = $SaveRootDir;
        $UploadModel -> SaveSubDir = $SaveSubDir;

        $UploadModel -> FullSavePath = $FullSavePath;
        $UploadModel -> VisualDir = $VisualDir;
            $UploadModel -> VisualPath = $VisualPath;
            $UploadModel -> ClientWorkName = $ClientWorkName;
            $UploadModel -> ClientTypeId = $ClientTypeId;
            $UploadModel -> ClientTypeName = $ClientTypeName;
            $UploadModel -> Remark = $Remark;
            $UploadModel -> CreateTime = date('Y-m-d H:i:s');
            $UploadModel -> GId =  \app\Utils\GeneralTool::CreateGuid();



            $UploadModel -> save();

        

        // 清除缓冲区内容但不发送到客户端
        ob_clean();
        return  $this->SendJOk("ok",1, $UploadModel  );




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
