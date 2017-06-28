<?php

// php [index.php] [path] [params] [method]
$app->add(new \App\Middleware\CliMiddleware());
$app->group('/cli', function() {
    $this->any('/{cli:[a-zA-Z][a-z0-9A-Z_]*}', function ($req, $res, $args) {
            $class = sprintf('\\App\\Cli\\%sCli', ucfirst(strtolower($args['cli'])));
            $newCli = new $class($this);
            $callable = [$newCli, 'run'];
            $params = func_get_args();
            return call_user_func_array($callable, $params);
    });
});
