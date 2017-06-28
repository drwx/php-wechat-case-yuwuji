<?php
namespace App\Controller;

class BaseController
{
    protected $container;
    protected $config;

    public function __construct($container)
    {
        $this->container = $container;
        $this->config = $container['config'];
    }

    public function alert($message , $url = null) {
        if(!$url) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/main';
        }
        echo "<script>alert('{$message}');location.href='{$url}'</script>";
        exit;
    }

}
