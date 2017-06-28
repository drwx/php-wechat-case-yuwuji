<?php

return [
    'db' => [
        'default' => [
            'conn'     => 'default',
            'driver'   => 'mysql',
            'host'     => '127.0.0.1',
            'port'     => '3306',
            'name'     => 'infinity',
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
        'prefix' => 'prod:',
    ],
    'wechat' => [
        'debug'  => true,
        'app_id' => '你的id',
        'secret' => '你的key',
        'token'  => 'drwcwh',
        'xs' => [
            'api' => 'http://127.0.0.1:8322/',
        ],
        'preset' => [
            'msghelp' => [ // 使用帮助
                'SD0_bq_D8o_yvOLP7YKvQ9VotbE23XecdyMOmLj-ADk',
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
            ],
            'msg413' => [ // 换一换到底了
                'bmkYXxgiHa8wSzu3X1IVYJqFazQGuKPUtk_xryeMePQ',
            ],
            'msg415' => [ // 不支持的类型
                'bmkYXxgiHa8wSzu3X1IVYJiOAWDPCBtzexxrN_cUR8Y',
            ],
        ],
        'oauth' => [
            'scopes'   => ['snsapi_userinfo'],
            'callback' => '/oauth_callback',
        ],
        'log' => [
            'SYS' => [
                'level' => 'info',
                'file'  => sprintf('/data/logs/infinity/inf.log.%s', date('Ymd')),
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
