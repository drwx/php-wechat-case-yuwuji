<?php

$container['config'] = function ($c) {
    $config = new \Noodlehaus\Config([
        APP_ROOT . '/config/',
        APP_ROOT . '/config/' . ENV,
    ]);

    return $config;
};
$container['redis'] = function ($c) {
    $conf = $c['config']->get('redis');
    if (false && extension_loaded('redis')) {
        $redis = new \Redis();
        $redis->connect($conf['host'], $conf['port']);
        $redis->setOption(\Redis::OPT_PREFIX, $conf['prefix']);
    } else {
        $redis = new \Predis\Client([
            'scheme' => $conf['scheme'],
            'host'   => $conf['host'],
            'port'   => $conf['port'],
        ], ['prefix' => $conf['prefix']]);
    }
    return $redis;
};
$container['httpCache'] = function ($c) {
    return new \Slim\HttpCache\CacheProvider();
};
$container['httpClient'] = function ($c) {
    $client = new \GuzzleHttp\Client();
    return $client;
};
$container['renderer'] = function ($c) {
    return new \Slim\Views\PhpRenderer(APP_ROOT . '/templates');
};
$container['wechat'] = function ($c) {
    $options = $c['config']['wechat'];
    return new \EasyWeChat\Foundation\Application($options);
};
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};
$container['xs'] = function ($c) {
    define('XS_APP_ROOT', PROJ_ROOT . '/src/app/config/xunsearch');
    return new \XS($c['config']->get('xsproj'));
};
