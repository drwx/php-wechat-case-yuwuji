<?php

date_default_timezone_set('PRC');
define('APP_ROOT', dirname(__FILE__));
define('SRC_ROOT', dirname(APP_ROOT));
define('PROJ_ROOT', dirname(SRC_ROOT));


if (ENV == 'dev') {
    ini_set('display_errors', 'On');
}

// for slim-flash
session_start();

require PROJ_ROOT . '/vendor/autoload.php';


// Instantiate the app
$app = new \App\GApp(['settings' => ['displayErrorDetails' => (true || ENV == 'dev')]]);
\App\GApp::setGApp($app);

$container = $app->getContainer();
$container['data'] = [
    'code' => 0,
    'msg' => '',
    'result' => null,
];

require APP_ROOT . '/inc/func.php';
require APP_ROOT . '/inc/deps.php';
require APP_ROOT . '/inc/mws.php';
require APP_ROOT . '/inc/mysql.php';
require APP_ROOT . '/routes/api_routes.php';
require APP_ROOT . '/routes/web_routes.php';
require APP_ROOT . '/routes/cli_routes.php';
