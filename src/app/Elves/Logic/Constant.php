<?php
namespace App\Logic;

class Constant
{
    const INF_MESSAGE_USER_ACTIVE_POOL = 'inf:rds:message:user:active:pool'; // 保存用户活跃信息

    const INF_MESSAGE_USER_INFO        = 'inf:rds:message:user:info:%s'; // 保存用户信息
    const INF_MESSAGE_USER_STAFF       = 'inf:rds:message:user:staff:%s'; // 保存推送给用户信息list

    const INF_EVENT_MENU_ITEM_STR     = 'inf:rds:str:%s'; // 最后一个切换类型
    const INF_EVENT_MENU_ITEM_POOL     = 'inf:rds:event:menu:item:pool'; // 最后一个切换类型
    const INF_EVENT_RANDOM_WORD_POOL   = 'inf:rds:event:random:word:pool'; // 最后一个换一换的词
    const INF_GET_MEDIA_LIST           = 'inf:rds:get:media:list'; // 永久素材缓存
    const INF_GET_MEDIA_LIST_CNT       = 'inf:rds:get:media:list:cnt'; // 永久素材记数
    const INF_REMOTE_IMAGE_MEDIA_ID    = 'inf:rds:media:id:%s'; // mediaId 缓存
    const INF_USER_FEEDBACK_LIST       = 'inf:rds:user:feedback:list'; // 反馈列表

    const INF_USER_WORD_LIST_RST       = 'inf:rds:user:word:list:%s'; // 用户查询词结果集
    const INF_USER_WORD_KEY            = 'inf:rds:word:%s'; // 用户查询词计数

    const INF_USER_TOPIC_LIST_RST      = 'inf:rds:user:topic:list:%s'; // 用户查询主题结果集
    const INF_USER_TOPIC_KEY           = 'inf:rds:topic:%s'; // 用户查询主题计数

    const INF_MENU_TOPIC_LIST          = 'inf:rds:menu:topic:list'; // 主题推荐
    const INF_MENU_TOPIC_SET           = 'inf:rds:menu:topic:set'; // 主题词集
    const INF_MENU_HOT_LIST            = 'inf:rds:menu:hot:list'; // 热门表情
    const INF_MENU_HOT_SET             = 'inf:rds:menu:hot:set'; // 热门表情词集

    const INF_WORD_RECOMMAND_NOREST    = 'inf:rds:word:rec:rst:%s'; // 两次连续搜索结果为空的状态
    const INF_WORD_RECOMMAND_TOBOTT    = 'inf:rds:word:rec:end:%s'; // 换一换到底的状态
    const INF_WORD_GUIDE_TIPS          = 'inf:rds:word:guide:tips::%s'; // 换一换引导

    const INF_RDS_COMMON_SET_STR       = 'inf:rds:common:setstr::%s'; // 通用redis str 设置

    const MENU_TOPIC                   = 'topic`';
    const MENU_HOT                     = 'hot`';

    const WX_REQ_NAMING_RDS            = 'inf:rds:req:naming:%s';
    const WX_REQ_NAMING_LMT            = 3500;

    const WX_SUCCESS                   = 'success';
    const WX_QRCODE_POOL               = 'inf:rds:wx:qrc:pool';

    CONST CONF_TYPE_MENU_NORMAL        = 1;
    CONST CONF_TYPE_MENU_PERSONAL      = 2;

    const USER_TYPE_NORMAL             = 0; // 未分组
    const USER_TYPE_BLACK              = 1; // 黑名单
    const USER_TYPE_STAR               = 2; // 星标用户
    const USER_TYPE_VIP                = 100; // 高雅用户
    const USER_TAG_OPENIDS             = 'inf:rds:user:tag:oids';
}
