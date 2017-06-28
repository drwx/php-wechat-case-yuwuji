<?php

namespace App\Middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// php [index.php] [path] [params] [method]
class CliMiddleware
{
    public function __invoke(Request $req, Response $res, callable $next)
    {
        $request = $req;

        if (PHP_SAPI === 'cli') {
            $path   = $this->_getArgv(1);
            $params = $this->_getArgv(2);
            $method = strtoupper($this->_getArgv(3, 'GET'));
            if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'])) {
                $method = 'GET';
            }

            $request = \Slim\Http\Request::createFromEnvironment(\Slim\Http\Environment::mock([
                'REQUEST_METHOD'    => $method,
                'REQUEST_URI'       => $this->_getUri($path, $params),
                'QUERY_STRING'      => $params
            ]));
        }

        return $next($request, $res);
    }

    private function _getArgv($key, $default = '')
    {
        global $argv;

        if (!array_key_exists($key, $argv)) {
            return $default;
        }

        return $argv[$key];
    }

    private function _getUri($path, $params)
    {
        $uri = '';
        if (strlen($path) > 0) {
            $uri = $path;
        }
        if (strlen($params) > 0) {
            $uri .= '?' . $params;
        }

        return '/' . ltrim($uri, '/');
    }
}
