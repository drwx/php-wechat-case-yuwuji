<?php
namespace App\Logic;

class Infinity
{
    const ITEM_TYPE_GYSP = 0;
    const ITEM_TYPE_GYWZ = 1;
    const ITEM_TYPE_PTSP = 2;
    const ITEM_TYPE_NHDZ = 3;
    const ITEM_TYPE_GXSP = 4;
    const ITEM_TYPE_YLDT = 5;
    const ITEM_TYPE_BQTP = 6;
    public static $aItemType = [
         self::ITEM_TYPE_GYSP => '高雅视频', // 云盘视频
         self::ITEM_TYPE_GYWZ => '高雅文章',
         self::ITEM_TYPE_PTSP => '普通视频', // 云盘视频
         self::ITEM_TYPE_NHDZ => '内涵段子',
         self::ITEM_TYPE_GXSP => '搞笑视频', // mp4视频链接
         self::ITEM_TYPE_YLDT => '娱乐动图', // gif动图链接
         self::ITEM_TYPE_BQTP => '表情图片', // 表情图片
    ];
    public static $aDftTypes = [
        Constant::USER_TYPE_NORMAL => [
            self::ITEM_TYPE_PTSP,
            self::ITEM_TYPE_NHDZ,
            self::ITEM_TYPE_GXSP,
            self::ITEM_TYPE_YLDT,
            self::ITEM_TYPE_BQTP,
        ],
        Constant::USER_TYPE_VIP => [
            self::ITEM_TYPE_GYSP,
            self::ITEM_TYPE_GYWZ,

            self::ITEM_TYPE_PTSP,
            self::ITEM_TYPE_NHDZ,
            self::ITEM_TYPE_GXSP,
            self::ITEM_TYPE_YLDT,
            self::ITEM_TYPE_BQTP,
        ],
    ];

    // 菜单相关
    public static $aMenuItemType = [
         self::ITEM_TYPE_GYSP => '百度云盘',
         self::ITEM_TYPE_GYWZ => '高雅文章',
         self::ITEM_TYPE_PTSP => '百度云盘',
         self::ITEM_TYPE_NHDZ => '内涵',
         self::ITEM_TYPE_GXSP => '美女视频',
         self::ITEM_TYPE_YLDT => '爆笑黄人动图',
         self::ITEM_TYPE_BQTP => '神表情',
    ];
    const MENU_TYPE_CLICK           = 'click';
    const MENU_TYPE_VIEW            = 'view';
    const MENU_TYPE_SCAN_PUSH       = 'scancode_push';
    const MENU_TYPE_SCAN_WAIT       = 'scancode_waitmsg';
    const MENU_TYPE_PIC_PHOTO       = 'pic_sysphoto';
    const MENU_TYPE_PIC_PHOTO_ALBUM = 'pic_photo_or_album';
    const MENU_TYPE_PIC_WWEXIN      = 'pic_weixin';
    const MENU_TYPE_LOCATION_SELECT = 'location_select';
    const MENU_TYPE_MEDIA_ID        = 'media_id';
    const MENU_TYPE_VIEW_LIMITED    = 'view_limited';
    public static $aMenuType = [
        self::MENU_TYPE_CLICK           => '点击事件',
        self::MENU_TYPE_VIEW            => '跳转URL',
        self::MENU_TYPE_SCAN_PUSH       => '扫码推事件',
        self::MENU_TYPE_SCAN_WAIT       => '扫码带提示',
        self::MENU_TYPE_PIC_PHOTO       => '系统拍照发图',
        self::MENU_TYPE_PIC_PHOTO_ALBUM => '拍照或者相册发图',
        self::MENU_TYPE_PIC_WWEXIN      => '微信相册发图',
        self::MENU_TYPE_LOCATION_SELECT => '发送位置',
        self::MENU_TYPE_MEDIA_ID        => '永久素材ID',
        self::MENU_TYPE_VIEW_LIMITED    => '跳转永久图文URL',
    ];
    public static $aMenuBtnAttr = [
        self::MENU_TYPE_CLICK           => ['type', 'name', 'key'],
        self::MENU_TYPE_VIEW            => ['type', 'name', 'url'],
        self::MENU_TYPE_SCAN_PUSH       => ['type', 'name', 'key'],
        self::MENU_TYPE_SCAN_WAIT       => ['type', 'name', 'key'],
        self::MENU_TYPE_PIC_PHOTO       => ['type', 'name', 'key'],
        self::MENU_TYPE_PIC_PHOTO_ALBUM => ['type', 'name', 'key'],
        self::MENU_TYPE_PIC_WWEXIN      => ['type', 'name', 'key'],
        self::MENU_TYPE_LOCATION_SELECT => ['type', 'name', 'key'],
        self::MENU_TYPE_MEDIA_ID        => ['type', 'name', 'media_id'],
        self::MENU_TYPE_VIEW_LIMITED    => ['type', 'name', 'media_id'],
    ];
}
