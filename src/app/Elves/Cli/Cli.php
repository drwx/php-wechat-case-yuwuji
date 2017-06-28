<?php
namespace App\Cli;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class Cli
{
    protected $container;
    protected $config;
    protected $redis;

    public function __construct($container)
    {
        $this->container = $container;
        $this->config    = $container['config'];
        $this->redis     = $container['redis'];
    }

    abstract function run(Request $req, Response $res, array $args);
}
