<?php
namespace app\Models;

use think\Model;

/**
 * 上传文件日志表
 * @package App\Models
 * @table UploadFileT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property integer $Id 
 * @property string $GId 源文件名
 * @property string $SourceName 
 * @property string $SaveName 
 * @property string $ExtName 
 * @property string $MimeType 
 * @property string $ClientTypeName 
 * @property integer $ClientTypeId 
 * @property string $FullSavePath 文件保存完整路径
 * @property string $SaveRootDir 
 * @property string $SaveSubDir 
 * @property string $Remark 
 * @property string $VisualPath 
 * @property string $VisualDir 
 * @property string $CreateTime 
 * @property string $ClientGId 
 * @property integer $ActId 
 * @property string $ClientWorkName 客户端工作的内容，可能是控制器类名
 * @property integer $IsUsed 
 * @property string $UpdateTime 
 * @property integer $FileSize 文件大小
 */
class UploadFileModel extends Model
{
    // protected $table = 'Sys_User';
    protected $pk = 'Id';
    protected $table = 'UploadFileT';
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
        // echo 'UploadFileModel init';
    }

}
// class UploadFileT extends Model
// {
    
//     // protected $table = 'Sys_User';
//     protected $pk = 'Id';
     
//     // 模型初始化
//     protected static function init()
//     {
//         //TODO:初始化内容
//         echo 'UploadFileT init (在文件 UploadFileModel 里) ' ;
//     }

// }
?>