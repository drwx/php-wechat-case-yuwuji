<?php
namespace App\Logic;

class Menu
{
    private $wechat;

    public function __construct($wechatApp)
    {
        $this->wechat = $wechatApp;
    }

    public function getMenus()
    {
        $menus = $this->wechat->menu->all();
        return $menus;
    }

    public function delMenus($menuId = null)
    {
        if (!empty($menuId)) {
            $rst = $this->wechat->menu->destroy($menuId);
        } else {
            $rst = $this->wechat->menu->destroy();
        }
        return $rst;
    }

    public function createMenu($buttons, $matchRule = [])
    {
        if (!empty($matchRule)) {
            $menuRsp = $this->wechat->menu->add($buttons, $matchRule);
        } else {
            $menuRsp = $this->wechat->menu->add($buttons);
        }

        return $menuRsp;
    }

    public function createMenuCustom($buttons, $matchRule)
    {
        $this->wechat->menu->add($buttons, $matchRule);

        return true;
    }
}
