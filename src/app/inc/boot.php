<?php

$ST = microtime(true);

if (function_exists('libxml_disable_entity_loader')) {
    libxml_disable_entity_loader(true);
}

require PROJ_ROOT . '/vendor/autoload.php';
require PROJ_ROOT . '/src/app/inc/func.php';

use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Log;
use \App\Logic\Constant;

$conf = include PROJ_ROOT . '/src/app/config/' . ENV . '/conf.php';
$settings = include PROJ_ROOT . '/src/app/config/settings.php';
$options = ['settings' => $settings] + $conf['wechat'];

if (false && extension_loaded('redis')) {
    $redis = new Redis();
    $redis->connect($conf['redis']['host'], $conf['redis']['port']);
    $redis->setOption(Redis::OPT_PREFIX, $conf['redis']['prefix']);

    $cache = new \Doctrine\Common\Cache\RedisCache();
    $cache->setRedis($redis);
} else {
    $redis = new \Predis\Client([
        'scheme' => $conf['redis']['scheme'],
        'host'   => $conf['redis']['host'],
        'port'   => $conf['redis']['port'],
    ], ['prefix' => $conf['redis']['prefix']]);

    $cache = new \Doctrine\Common\Cache\PredisCache($redis);
}

\App\Logic\Helper::createLogger('SYS', $conf['wechat']['log']['SYS']);
Log::setLogger(\Monolog\Registry::SYS());

$app = new Application($options);
$server = $app->server;

$app->redis = $redis;
$app->access_token->setCache($cache);
Log::info('token:' . $app->access_token->getToken());

define('XS_APP_ROOT', PROJ_ROOT . '/src/app/config/xunsearch');
$xs = new \XS($options['settings']['xsproj']);
$app->xsClient = $xs->search;

$msgObj = new \App\Logic\Message($app);
$server->setMessageHandler(function ($message) use ($msgObj, $app) {
    $openId = $message->FromUserName;
    $userKey = sprintf(Constant::INF_MESSAGE_USER_INFO, $openId);
    $userInfo = $app->redis->get($userKey);
    if (empty($userInfo)) {
        $userInfo = $app->user->get($openId);
        if (!empty($userInfo)) {
            $app->redis->setEx($userKey, 7200, jsonEncode($userInfo));
            Log::info('redis set: ' . $userKey);
        } else {
            Log::error('get user api: ' . $userKey);
        }
    } else {
        $userInfo = jsonDecode($userInfo);
    }
    $app->userInfo = $userInfo;

    // 保存活跃用户及时间
    $app->redis->hSet(Constant::INF_MESSAGE_USER_ACTIVE_POOL, $openId, jsonEncode(['ts' => time(), 'nickname' => $userInfo['nickname'], 'uinfo' => $userInfo]));

    $result = '';
    switch ($message->MsgType) {
        case 'event':
            $result = $msgObj->handleEventMsg($message);
            break;
        case 'text':
            $result = $msgObj->handleTextMsg($message);
            break;
        case 'image':
        case 'voice':
        case 'video':
        case 'shortvideo':
        case 'location':
        case 'link':
        default:
            break;
    }

    return $result;
});

$succ = 1;
$msg = '';
try {
    $res = $server->serve();
    $res->send();
} catch (\Exception $e) {
    $msg = $e->getMessage();
    $succ = 0;
}

$time = microtime(true) - $ST;
Log::info('exec script time: ' . $time);
if (!$succ) {
    echo $msg;
    die('<br/>err service!!');
}
