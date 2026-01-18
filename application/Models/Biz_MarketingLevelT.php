<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Biz_MarketingLevelT extends Model
{
    protected $table = 'Biz_MarketingLevelT';
    protected $pk = 'Id';

    //除了ProductId 以外，这个表其它Id都是 BigInt类型
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容Clinet_PointLogT
    }

}

// 字段名  类型    是否可为空      主键    默认值  额外信息
// Id      int     Id      int(11) NO      PRI
// LevelName       varchar 级组名称        varchar(120)    YES
// LevelDifference decimal 级差    decimal(18,2)   YES
// IsNeedSequential        int     连续要求        int(11) YES
// PeerAwardMax    int     平级奖人数      int(11) YES
// Remarks varchar 备注    varchar(255)    YES
// $Model -> Id = 0;  // Id
// $Model -> LevelName = ''; // 级组名称
// $Model -> LevelDifference = 0;  // 级差
// $Model -> IsNeedSequential = 0;  // 连续要求
// $Model -> PeerAwardMax = 0;  // 平级奖人数


?>

