<?php
namespace app\Models;

use think\Model;



/**
 * 任务请求日志
 * @package App\Models
 * @table Task_RequestLogT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property integer $Id Id
 * @property string $FullUrl 请求的完整地址
 * @property string $CreateTime 创建时间
 * @property integer $CreateTS 创建时间戳
 * @property bool $IsFinished 是否完成
 * @property string $FinishedTime 完成时间
 * @property bool $IsSuccess 是否成功
 * @property string $TaskTypeName 任务类型（显示用）
 * @property integer $TaskTypeId 任务类型
 */
class Task_RequestLogT extends Model
{
    protected $table = 'Task_RequestLogT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }
/*
字段名  类型    是否可为空      主键    默认值  额外信息
Id      bigint  Id      bigint(20)      NO      PRI
FullUrl varchar 请求的完整地址  varchar(255)    YES
CreateTime      datetime        创建时间        datetime        YES
CreateTS        bigint  创建时间戳      bigint(20)      YES
IsFinished      bit     是否完成        bit(1)  YES
FinishedTime    datetime        完成时间        datetime        YES
IsSuccess       bit     是否成功        bit(1)  YES
TaskTypeName    varchar 任务类型（显示用）      varchar(120)    YES
TaskTypeId      int     任务类型        int(11) YES
*/

}



?>