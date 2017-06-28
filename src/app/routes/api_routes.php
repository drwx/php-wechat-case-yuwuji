<?php

use App\Logic\Constant;

$app->get('/', function ($req, $res, $args) {
    return $this->renderer->render($res, '/index.php', $args);
});
$app->get('/test', function($req, $res, $args) {
    $material = $this->wechat->material;
    $lists = $material->lists('image', 0, 10);

    return $res->withJson($lists);
});
$app->group('/es', function() {
    $this->map(['GET'], '/addidx', '\App\Controller\EsController:addIndex');
    $this->map(['DELETE', 'GET'], '/delidx', '\App\Controller\EsController:delIndex');
    $this->map(['POST', 'GET'], '/putmap', '\App\Controller\EsController:putMapping');

    $this->map(['GET', 'POST'], '/adddoc', '\App\Controller\EsController:addDoc');
    $this->map(['GET', 'POST'], '/upddoc', '\App\Controller\EsController:updDoc');
    $this->map(['GET', 'POST'], '/deldoc', '\App\Controller\EsController:delDoc');
})->add(new \App\Middleware\JsMiddleware($container));

$app->group('/xs', function() {
    $this->map(['DELETE', 'GET'], '/delidx', '\App\Controller\XsController:delIdx');

    $this->map(['GET', 'POST'], '/adddoc', '\App\Controller\XsController:addDoc');
    $this->map(['GET', 'POST'], '/upddoc', '\App\Controller\XsController:updDoc');
    $this->map(['GET', 'POST'], '/deldoc', '\App\Controller\XsController:delDoc');
})->add(new \App\Middleware\JsMiddleware($container));

$app->group('/api', function() {
    $this->map(['POST'], '/items', '\App\Controller\ItemController:addItem');
})->add(new \App\Middleware\JsMiddleware($container));

$app->group('/api', function() {
    $this->map(['GET', 'POST'], '/items/{id:\d+}/edit', '\App\Controller\ItemController:editItem');
    $this->map(['POST'], '/menus', '\App\Controller\MenuController:createMenu');
    $this->map(['POST'], '/usertag', '\App\Controller\MenuController:updateUserTags');
});

$app->group('/content',function(){
    $this->map(['GET'], '/add', function ($req, $res, $args) {
        return $this->renderer->render($res, '/contentAdd.php', $args);
    });
    $this->map(['GET'], '/{id:\d+}/edit', '\App\Controller\ItemController:editItemPage');
    $this->map(['GET'], '/list', '\App\Controller\ItemController:listItem');
    $this->map(['GET'], '/dellist', '\App\Controller\ItemController:listItem');
});

$app->get('/rebuildidx', '\App\Controller\ItemController:rebuildIndex')
    ->add(new \App\Middleware\JsMiddleware($container));
$app->get('/buildemo', '\App\Controller\ItemController:buildEmotions')
    ->add(new \App\Middleware\JsMiddleware($container));

//公众号统一放置在/mp下
$app->group('/mp',function(){
    $this->map(['GET'], '/menu', '\App\Controller\MenuController:showMenu');
    $this->map(['GET'], '/psnmenu', '\App\Controller\MenuController:showMenu');

    $this->map(['GET'], '/usertag', function ($req, $res, $args) {
        $flash = $this->flash->getMessage('itemMsg');
        !empty($flash) && $this->renderer->addAttribute('flash', $flash);

        $userList = $this->redis->hGetAll(Constant::USER_TAG_OPENIDS);
        $this->renderer->addAttribute('userList', (array)$userList);

        return $this->renderer->render($res, '/tagMrg.php', $args);
    });

    $this->map(['GET'],'/staff', function ($req, $res, $args) {
        $staff = $this->wechat->staff->lists();
        return $res->withJson($staff);
    });
    $this->map(['GET'],'/stats', function ($req, $res, $args) {
        $stats = $this->wechat->stats->userCumulate(date('Y-m-d', time() - 7 * 86400), date('Y-m-d', time() - 86400));
        return $res->withJson($stats);
    });

    $this->map(['GET'],'/feedbacks', function ($req, $res, $args) {
        $jsonArrList = $this->redis->lRange(Constant::INF_USER_FEEDBACK_LIST, 0, -1);
        $words = [];
        foreach ($jsonArrList as $jsonStr) {
            $jsonArr = jsonDecode($jsonStr);
            $words[] = $jsonArr;
        }
        $this->renderer->addAttribute('words', $words);

        return $this->renderer->render($res, '/feedback.php', $args);
    });


    $this->map(['GET'],'/oppush', function ($req, $res, $args) {
        $this->renderer->addAttribute('users', (array)$this->redis->lRange('nc:push:user:info', 0, -1));
        return $this->renderer->render($res, '/push.php', $args);
    });

    $this->map(['GET'],'/activeuser', function ($req, $res, $args) {
        set_time_limit(0);
        ini_set('memory_limit', '128M');
        $users = $this->redis->hGetAll(Constant::INF_MESSAGE_USER_ACTIVE_POOL);
        $data = $cols = [];
        foreach ($users as $openId => $v) {
            $val = jsonDecode($v);
            $sentTime = $this->redis->get(sprintf('nc:usersend_%s', $openId));
            $data[] = [
                'openId'   => $openId,
                'nickname' => $val['nickname'],
                'date'     => date('Y-m-d H:i:s', $val['ts']),
                'sentAt'   => $sentTime > 0 ? date('Y-m-d H:i:s', $sentTime) : 0,
            ];
            $cols[] = $val['ts'];
        }
        array_multisort($cols, SORT_NUMERIC, SORT_DESC, $data);

        return $res->withJson(['total' => count($data), 'data' => $data]);
    });
    $this->map(['GET'],'/qrcode', function($req, $res, $args) {
        $redis = $this->redis;
        $qrInfo = $redis->hGetAll(Constant::WX_QRCODE_POOL);

        $this->renderer->addAttribute('qrInfo', empty($qrInfo) ? [] : $qrInfo);

        $flash = $this->flash->getMessage('qrInfo');
        !empty($flash) && $this->renderer->addAttribute('flash', $flash);

        return $this->renderer->render($res, '/qrInfo.php', $args);
    });
    $this->map(['POST'],'/qrcode', function($req, $res, $args) {
        $params = $req->getParams();
        if (!isset($params['scene']) || !isset($params['name']) || empty($params['scene']) || empty($params['name'])) {
            $this->flash->addMessage('qrInfo', '参数错误');
            return $res->withRedirect('/qrcode');
        }
        $scene = trim($params['scene']);
        $name = trim($params['name']);
        $qrcode = $this->wechat->qrcode;
        $result = $qrcode->forever($scene);
        $ticket = $result->ticket;
        $url = $result->url;
        $qrInfo = [
            'name' => $name,
            'scene' => $scene,
            'ticket' => $ticket,
            'url' => $url,
            'qrurl' => $qrcode->url($ticket),
        ];
        $redis = $this->redis;
        $rst = $redis->hSetNx(Constant::WX_QRCODE_POOL, md5($name . $scene), jsonEncode($qrInfo));;
        if (!$rst) {
            $this->flash->addMessage('qrInfo', sprintf('%s:%s 已存在', $name, $scene));
        } else {
            $this->flash->addMessage('qrInfo', sprintf('%s:%s 创建成功', $name, $scene));
        }

        return $res->withRedirect('/mp/qrcode');
    });

    $this->post('/push2user', function ($req, $res, $args) {
        set_time_limit(0);
        ini_set('memory_limit', '128M');
        $msg = $req->getParam('msg');
        if (empty($msg)) {
            return $res->withJson(['code' => -1, 'msg' => 'param msg is empty']);
        }
        $test = $req->getParam('test', 1);
        $users = $req->getParam('users');
        if ($test == 1) {
            if (!empty($users)) {
                $userList = array_filter(explode(',', $users));
                $err = 'err-openid:';
                foreach ($userList as $openId) {
                    try {
                        $this->wechat->staff->message($msg)->by('kf2002@yuwuji-dc')->to($openId)->send();
                    } catch (\EasyWeChat\Core\Exceptions\HttpException $e) {
                        $err .= $openId . '#~#';
                        continue;
                    }
                }
                return $res->withJson(['code' => 0, 'msg' => 'sent err: ' . $err . 'sent success. [user]' . $users . ' [msg]' . $msg]);
            } else {
                return $res->withJson(['code' => -1, 'msg' => 'param user is empty']);
            }
        }

        $users = $this->redis->hGetAll(Constant::INF_MESSAGE_USER_ACTIVE_POOL);
        $data = [];
        foreach ($users as $openId => $v) {
            $val = jsonDecode($v);
            $t = time() - 169200; // >47 hours
            if ($val['ts'] < $t) {
                $this->redis->hDel(Constant::INF_MESSAGE_USER_ACTIVE_POOL, $openId);
                continue;
            }
            $data[] = [
                'openId' => $openId,
                'nickname' => $val['nickname'],
                'date' => date('Y-m-d H:i:s', $val['ts']),
            ];
            $cols[] = $val['ts'];
        }
        array_multisort($cols, SORT_NUMERIC, SORT_DESC, $data);

        // send msg
        $sent = [];
        foreach ($data as $item) {
            $openId = $item['openId'];
            $userSendKey = sprintf('nc:usersend_%s', $openId);
            if ($this->redis->get($userSendKey)) {
                continue;
            }

            try {
                $this->wechat->staff->message($msg)->by('kf2002@yuwuji-dc')->to($openId)->send();
                $this->redis->setEx($userSendKey, 172800, time()); // 48 hours
            } catch (\EasyWeChat\Core\Exceptions\HttpException $e) {
                $sent[] = array_merge(['errcode' => $e->getCode(), 'errmsg' => $e->getMessage()], $item);
                continue;
            }

            $sent[] = $item;
        }
        $this->redis->lPush('nc:push:user:info', jsonEncode(['ts' => time(), 'msg' => $msg, 'sent' => $sent]));

        return $res->withJson(['code' => 0, 'msg' => 'ok', 'data' => ['all' => $data, 'sent' => $sent]]);
    });
});

$app->group('/tools/rdsstr', function() {
    $this->get('/{key:\w+}', function($req, $res, $args) {
        do {
            $rdsKey = sprintf(Constant::INF_RDS_COMMON_SET_STR, $args['key']);
            $rst = (string)$this->redis->get($rdsKey);
            $cb = $req->getParam('cb');
            if (!empty($cb)) {
                $rst = $cb . '(' . $rst . ')';
            }
        } while (0);


        return $res->write($rst);
    });
    $this->post('/{key:\w+}', function($req, $res, $args) {
        do {
            $rdsKey = sprintf(Constant::INF_RDS_COMMON_SET_STR, $args['key']);
            $str = $req->getParam('str', '');
            if (!empty($str)) {
                $rst = $this->redis->set($rdsKey, $str);
            } else {
                $rst = false;
            }
        } while (0);

        return $res->write($rst ? 'succ' : 'fail');
    });
});

$app->map(['GET'], '/result', function ($req, $res, $args) {
    $redis = $this->redis;
    $data = $redis->hGetAll('cwhinfoZ');
    foreach ($data as $name => $v) {
        if ($name == ''
            || strpos($name, '36') !== false
            || $name == 'a'
            || strpos($name, '1') !== false) {
            continue;
        }
        $info = jsonDecode($v);
        $users[] = array_merge(['name' => '', 'email' => '', 'hukou' => 0, 'pingkun' => 0], $info);
    }
    $this->renderer->addAttribute('users', $users);
    $this->renderer->addAttribute('count', count($users));

    return $this->renderer->render($res, '/cwhrst.php', $args);
});
$app->map(['GET'], '/excel', function ($req, $res, $args) {
    $redis = $this->redis;
    $data = $redis->hGetAll('cwhinfoZ');
    $users[] = ['姓名', '邮箱', '是否常住户口在农村', '是否建档立卡贫困家庭'];
    foreach ($data as $name => $v) {
        if ($name == ''
            || strpos($name, '36') !== false
            || $name == 'a'
            || strpos($name, '1') !== false) {
            continue;
        }
        $info = jsonDecode($v);
        $users[] = array_merge(['name' => '', 'email' => '', 'hukou' => 0, 'pingkun' => 0], $info);
    }
    $csv = '';
    foreach ($users as $u) {
        $csv .= '"' . implode('","', $u) . "\"\n";
    }
    return $res->withAddedHeader('Content-Type','text/csv')
        ->withAddedHeader('Content-Type','text/csv')
        ->write($csv);
});
$app->map(['GET'], '/cwh', function ($req, $res, $args) {
    $flash = $this->flash->getMessage('itemMsg');
    !empty($flash) && $this->renderer->addAttribute('flash', $flash);

    return $this->renderer->render($res, '/cwh.php', $args);
});
$app->map(['POST'], '/cwhinfo', function ($req, $res, $args) {
    $redis = $this->redis;
    $b = $redis->hSet('cwhinfoZ', $req->getParam('name'), jsonEncode($req->getParams()));

    $this->flash->addMessage('itemMsg', $b ? '提交成功' : '已覆盖信息');

    return $res->withRedirect('/');
});
