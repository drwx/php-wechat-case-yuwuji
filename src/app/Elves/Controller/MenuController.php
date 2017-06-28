<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Logic\Helper;
use \App\Model\Config;
use \App\Logic\Constant;

class MenuController extends BaseController
{
    /* {{{ create menu */
    public function createMenu(Request $req, Response $res, array $args)
    {
        $menus = $req->getParam('menus');
        $leftBtns = Helper::getMenuBtns($menus['left']);
        $centerBtns = Helper::getMenuBtns($menus['center']);
        $rightBtns = Helper::getMenuBtns($menus['right']);

        $menuInfo = array_filter([$leftBtns, $centerBtns, $rightBtns], function ($btns) {
            return !empty($btns);
        });
        // 个性化菜单
        $matchRules = Helper::getMatchRule($menus['matchRule']);

        $menu = new \App\Logic\Menu($this->container->wechat);

        if (empty($menuInfo)) {
            $bMenu = $menu->delMenus();
        } else {
            $bMenu = $menu->createMenu($menuInfo, $matchRules);
            // save menu
            if ($bMenu) {
                $configMenu['name'] = 'menu';
                $configMenu['type'] = empty($matchRules) ? Constant::CONF_TYPE_MENU_NORMAL : Constant::CONF_TYPE_MENU_PERSONAL;
                $configMenu['config'] = jsonEncode($menus);
                $configMenu['extra'] = jsonEncode($bMenu->toArray());
                Config::create($configMenu);
            }
        }

        $this->container->flash->addMessage('itemMsg', $bMenu ? '菜单更新成功' : '菜单更新失败，请重试');

        return $res->withRedirect('/mp/menu');
    } /* }}} */

    /* {{{ show menu */
    public function showMenu(Request $req, Response $res, array $args)
    {
        $renderer = $this->container->get('renderer');

        $type = $req->getParam('type', Constant::CONF_TYPE_MENU_NORMAL);
        $config = Config::getConfByType($type);
        $menus = jsonDecode($config->config);
        $renderer->addAttribute('menus', $menus);

        $flash = $this->container->flash->getMessage('itemMsg');
        !empty($flash) && $renderer->addAttribute('flash', $flash);

        /* $menu = new \App\Logic\Menu($this->container->wechat);
        var_dump($menu->getMenus());
        die; */

        $tags = $this->container->redis->get('inf:user:tags');
        if (empty($tags)) {
            $userTag = $this->container->wechat->user_tag;
            $tags = $userTag->lists();
            $this->container->redis->setEx('inf:user:tags', 7200, jsonEncode($tags));
        } else {
            $tags = jsonDecode($tags);
        }
        $tagSel[''] = '选择标签';
        foreach ($tags['tags'] as $tag) {
            $tagSel[$tag['id']] = $tag['name'];
        }
        $tagSel && $renderer->addAttribute('tags', $tagSel);

        return $renderer->render($res, '/showMenu.php', $args);
    } /* }}} */

    public function updateUserTags(Request $req, Response $res, array $args)
    {
        $redis = $this->container->redis;
        $userTags = $this->container->wechat->user_tag;
        $tagsData = $userTags->lists();
        $tags = $tagsData['tags'];
        $bRst = true;
        foreach ($tags as $tag) {
            if ($tag['id'] != Constant::USER_TYPE_VIP) {
                continue;
            }
            $redis->del(Constant::USER_TAG_OPENIDS); // 清除现存Key
            $uList = $userTags->usersOfTag($tag['id'], '');
            $openIds = isset($uList['data']['openid']) ? $uList['data']['openid'] : [];
            foreach ($openIds as $oid) {
                $bRst = $redis->hSet(Constant::USER_TAG_OPENIDS, $oid, $tag['id']);
            }
        }
        $this->container->flash->addMessage('itemMsg', $bRst ? '更新成功' : '更新失败，请重试');

        return $res->withRedirect('/mp/usertag');
    }

}
