<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


/**
 * @package App\Models
 * @table Biz_PayBillT
 *
 * @property-read \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @property integer $Id 
 * @property string $CreateTime 
 * @property string $OrderNo 
 * @property integer $OrderId 
 * @property string $BillNo 
 * @property integer $OrderAmountFee 
 * @property integer $PayAmountFee 
 * @property integer $PaymentFee 
 * @property integer $RefundAmountFee 
 * @property integer $IsSuccess 
 * @property integer $IsAudit 
 * @property string $ArriveTime 
 * @property integer $ClientUserId 
 * @property string $ClientNickName 客户呢称
 * @property string $ClientRealName 客户姓名
 * @property string $ClientPhone 客户电话
 * @property string $Gid 
 * @property string $PayPlatNo 支付平台的订单号
 * @property integer $PayPlatId 
 * @property string $Rmk 
 * @property integer $CreateTS 
 * @property integer $FinishedTS 
 * @property integer $IsFinished 
 * @property string $FinishedTime 
 * @property string $TrueName 
 * @property string $RequestURL 
 * @property string $CallbackURL 
 * @property string $LastVisitTime 
 * @property blob $RowVer 
 * @property integer $TerminalType 
 * @property integer $DatePart 
 * @property string $PayClientPlatform 
 * @property integer $PayResultStatus 
 */
class Biz_PayBillT extends Model
{
    protected $table = 'Biz_PayBillT';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}



?>