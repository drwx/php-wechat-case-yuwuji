<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$config = $container->get('config');
$dbs = array_keys($config->get('db'));
foreach ($dbs as $dbname) {
    $capsule->addConnection([
        'driver'    => $config->get('db.' . $dbname . '.driver'),
        'host'      => $config->get('db.' . $dbname . '.host'),
        'port'      => $config->get('db.' . $dbname . '.port'),
        'database'  => $config->get('db.' . $dbname . '.name'),
        'username'  => $config->get('db.' . $dbname . '.username'),
        'password'  => $config->get('db.' . $dbname . '.password'),
        'charset'   => $config->get('db.' . $dbname . '.charset'),
        'collation' => $config->get('db.' . $dbname . '.collation'),
        'prefix'    => $config->get('db.' . $dbname . '.prefix'),
    ], $config->get('db.' . $dbname . '.conn'));
}

$capsule->setAsGlobal();
$capsule->bootEloquent();
// set db instance to container
// $container['capsule'] = $capsule;


// use Illuminate\Database\Capsule\Manager as DB;
// DB::connection()->enableQueryLog();
// ... sql ..
// ... sql ..
// $queries = DB::getQueryLog();

