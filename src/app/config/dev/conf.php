<?php

return [
    'db' => [
        'default' => [
            'conn'     => 'default',
            'driver'   => 'mysql',
            'host'     => '127.0.0.1',
            'port'     => '3306',
            'name'     => 'infinity_dev',
            'username' => 'root',
            'password' => 'root',
            'charset'  => 'utf8',
            'prefix'   => ''
        ],
    ],
    'redis' => [
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
        'prefix' => 'dev:',
    ],
    'wechat' => [
        'debug'  => true,
        'app_id' => 'wxbe3c4bad3d',
        'secret' => '2ba146b4b61e',
        'token'  => 'ywjdev',
        'xs' => [
            'api' => 'http://127.0.0.1:8322/',
        ],
        'preset' => [
            'msghelp' => [ // 使用帮助
                'bmkYXxgiHa8wSzu3X1IVYAxYbnDMJ8ydvYnCqnzs8w4',
            ],
            'msgspit' => [ // 吐槽
                'bmkYXxgiHa8wSzu3X1IVYE4mtptIIOL7bEJl9rlKxWE',
            ],
            'msg100' => [ // 关注
                'bmkYXxgiHa8wSzu3X1IVYAxYbnDMJ8ydvYnCqnzs8w4',
            ],
            'msg400' => [ // 未输入换一换搜索词
                'bmkYXxgiHa8wSzu3X1IVYNvhaBd7Kl2Ew4vLpeEfb4Y',
            ],
            'msg404' => [ // 未搜索到结果
                'bmkYXxgiHa8wSzu3X1IVYEVk1gx5sZW-LspaBZNffDQ',
                'bmkYXxgiHa8wSzu3X1IVYGn5QSxGVI9aQuNyBDLBlLA',
                'bmkYXxgiHa8wSzu3X1IVYDglA1RipHYt5joZS8uEqf8',
            ],
            'msg413' => [ // 换一换到底了
                'bmkYXxgiHa8wSzu3X1IVYJqFazQGuKPUtk_xryeMePQ',
            ],
            'msg415' => [ // 不支持的类型
                'bmkYXxgiHa8wSzu3X1IVYJiOAWDPCBtzexxrN_cUR8Y',
                'bmkYXxgiHa8wSzu3X1IVYBfG7z65wu9kwUad4w5OaMo',
            ],
        ],
        'oauth' => [
            'scopes'   => ['snsapi_userinfo'],
            'callback' => '/oauth_callback',
        ],
        'log' => [
            'SYS' => [
                'level' => 'info',
                'file'  => sprintf('/tmp/inf.log.%s', date('Ymd')),
                'processors' => [
                    ['\Monolog\Processor\UidProcessor', [10]],
                    function ($record) {
                        $record['extra']['time'] = microtime(true);
                        return $record;
                    },
                ],
            ],
        ],
    ],
];
