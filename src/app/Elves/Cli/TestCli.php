<?php
namespace App\Cli;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// php7 src/public/index.php /cli/test "t[]=f&f=df&s[]=fsd"
class TestCli extends Cli
{
    public function run(Request $req, Response $res, array $args)
    {
        return $res->write(jsonEncode($this->config->get('wechat')) . jsonEncode($args) . jsonEncode($req->getParams()));
    }
}
