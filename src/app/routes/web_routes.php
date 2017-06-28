<?php

use App\Logic\Constant;

$app->group('/web', function() {
    $this->map(['GET'], '/items', '\App\Controller\WebController:getItems');
})->add(new \App\Middleware\JsMiddleware($container));
