<?php

namespace App\Middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class JsMiddleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $req, Response $res, callable $next)
    {
        $res = $next($req, $res);

        $code   = $this->container['data']['code'];
        $msg    = $this->container['data']['msg'];
        $result = $this->container['data']['result'];

        $isList = isset($this->container['data']['result']['total']);
        $result['data'] = isset($this->container['data']['result']['data']) ?
            $this->container['data']['result']['data'] :
            ($isList ? [] : new \stdClass);

        $cb = $req->getParam('cb');
        if (!empty($cb) && preg_match('/^\w+$/', $cb)) {
            $str = json_encode([
                    'code'   => (int)$code,
                    'msg'    => (string)$msg,
                    'result' => $result,
                ], JSON_ENCODE_OPT);
            $res = $res->write($cb . '(' . $str . ')');
        } else {
            $res = $res->withJson(
                [
                    'code'   => (int)$code,
                    'msg'    => (string)$msg,
                    'result' => $result,
                ],
                null,
                JSON_ENCODE_OPT
            );
        }

        return $res;
    }
}
