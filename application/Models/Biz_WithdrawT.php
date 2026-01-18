<?php
namespace app\Models;

use think\Model;



/// 文件 名带下划线的 读取不到！！！


class Biz_WithdrawT extends Model
{
    protected $table = 'Biz_WithdrawT';
    protected $pk = 'Id';

    //除了ProductId 以外，这个表其它Id都是 BigInt类型
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容Clinet_PointLogT
    }

}

// 表名                    引擎    版本    行数    创建时间        更新时间        表备注
// Biz_WithdrawT   InnoDB  10      27      2026-01-12 07:16:48     209276  提现记录
// 字段名  数据类型        说明    类型    是否可为空      主键    默认值  额外信息
// Id      bigint          bigint(20)      NO      PRI
// CreateTime      datetime        创建时间        datetime        YES
// OrderNo varchar 订单号  varchar(64)     YES
// BillNo  varchar 流水号  varchar(64)     YES
// OrderAmountFee  bigint  订单金额 申请金额       bigint(20)      YES
// PayAmountFee    bigint  支付金额 发放金额       bigint(20)      YES
// ProcessingFee   int     手续费  int(11) YES
// RefundAmountFee bigint  退款金额        bigint(20)      YES
// IsSuccess       int     成功标志        int(11) YES
// IsAudit int     审核标志        int(11) YES
// ArriveTime      datetime        到账时间        datetime        YES
// ClientUserId    bigint  客户Id  bigint(20)      YES
// Gid     varchar Guid    varchar(64)     YES
// PayPlatNo       varchar 支付平台的订单号        varchar(64)     YES
// Rmk     varchar 备注    varchar(500)    YES
// CreateTS        bigint  创建时间戳      bigint(20)      YES
// FinishedTS      bigint  完成时间戳      bigint(20)      YES
// IsFinished      int     完成标志        int(11) YES
// FinishedTime    datetime        完成时间        datetime        YES
// RequestURL      varchar 请求地址        varchar(255)    YES
// CallbackURL     varchar 回调地址        varchar(255)    YES
// LastVisitTime   datetime                datetime        YES
// DatePart        int     日期    int(11) YES
// PayClientPlatform       varchar 支付平台名称    varchar(64)     YES
// PayResultStatus int     支付平台结果    int(11) YES
// ClientRealName  varchar 客户姓名        varchar(50)     YES
// ClientNickName  varchar 客户呢称        varchar(120)    YES
// BankName        varchar 银行名称        varchar(50)     YES
// BankAccount     varchar 银行账号(姓名)  varchar(200)    NO      PRI
// BankCardNo      varchar 银行卡卡号      varchar(120)    YES
// Mobile  varchar 联系用的手机号  varchar(20)     YES



?>


