<?php
namespace app\Models;

use think\Model;


class Client_User_View extends Model
{
    protected $table = 'client_user_view';
    protected $pk = 'Id';
     
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }

}

// 表名                    引擎    版本    行数    创建时间        更新时间        表备注
// client_user_view        None    None    None    None    None    VIEW
// 字段名  数据类型        说明    类型    是否可为空      主键    默认值  额外信息      
// MarketingLevelName      varchar 级组名称        varchar(120)    YES
// Id      int     主键    int(20) NO
// OpenId  varchar 微信OpenId      varchar(64)     YES
// TokenId varchar TokenId varchar(64)     YES
// Mobile  varchar 手机号  varchar(30)     YES
// RealityName     varchar 实名    varchar(150)    YES
// GuiderUserId    varchar 推荐人UserId    varchar(64)     YES
// Account varchar 账号    varchar(64)     YES
// Password        varchar 密码    varchar(64)     YES
// NickName        varchar 呢称    varchar(120)    YES
// IsBindPhone     int     已经绑定手机    int(11) YES
// IsReality       int     是否已经实名    int(11) YES
// StoreId int     商户Id 本项目未使用     int(11) YES
// LockState       int     用来和其它Web通信的接口 int(11) YES
// RegPlatform     varchar 注册平台        varchar(50)     YES
// RegisterIp      varchar 注册IP  varchar(64)     YES
// RegisterDate    datetime        注册日期        datetime        YES
// LoginDate       datetime        登录日期        datetime        YES
// LogoutDate      datetime        登出日期        datetime        YES
// LoginIp varchar 登录IP  varchar(64)     YES
// UserLevel       int     用户等级        int(11) YES
// VipLevel        int     VIP等级 int(11) YES
// VipExp  bigint  VIP经验 bigint(20)      YES
// Sex     int     性别 1 男 2 女 3 其它   int(11) YES
// Remarks varchar 备注    varchar(255)    YES
// RegisterTerminal        varchar 注册终端        varchar(50)     YES
// GId     varchar Guid    varchar(64)     YES
// OldLoginIp      varchar 上一次登录的Ip  varchar(255)    YES
// FaceImg varchar 头像    varchar(255)    YES
// RegArea varchar 注册区域（邮编区号）    varchar(150)    YES
// RegAreaName     varchar 注册区域名称    varchar(150)    YES
// UserRegSource   varchar 注册来源        varchar(20)     YES
// Address varchar 地址    varchar(150)    YES
// IDCard  varchar 身份证  varchar(150)    YES
// BirthDay        varchar 生日    varchar(150)    YES
// IsPromete       int     已经提权        int(11) YES
// CreateTime      datetime        创建日期        datetime        YES
// FirstLoginTime  datetime        首次登录日期    datetime        YES
// IsDelFlag       int     删除标记        int(1)  YES
// DelTime datetime        删除日期        datetime        YES
// LoginClientID   varchar 登录的客户端ID  varchar(64)     YES
// LoginPlatform   varchar 登录平台        varchar(64)     YES
// LoginVersion    varchar 登录版本        varchar(64)     YES
// SignoutStatus   int     注销状态  0 未注销 1 已经注销   int(11) YES
// SignoutTime     datetime        注销日期        datetime        YES
// MakerLevelId    int     创客等级        int(11) YES
// ShareholderLevel        int     股东等级        int(11) YES
// BuyTimes        int     购买次数        int(11) YES
// HisMonetary     decimal 消费金额        decimal(18,4)   YES
// PointsHistory   decimal 历史积分        decimal(18,4)   YES
// PointsBalance   decimal 剩余积分        decimal(18,4)   YES
// PointsFroze     decimal 冻结积分        decimal(18,4)   YES
// BounsHistory    decimal 奖金历史        decimal(18,4)   YES
// BounsBalance    decimal 奖金余额        decimal(18,4)   YES
// BounsFrozen     decimal 奖金冻结数额    decimal(18,4)   YES
// IsRegionAgent   bit     是否为区县代理  bit(1)  YES
// GuiderRealName  varchar 实名    varchar(150)    YES
// GuiderNickName  varchar 呢称    varchar(120)    YES

?>