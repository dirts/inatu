<?php

date_default_timezone_set("Asia/Shanghai"); 
ini_set('display_errors', 1);
error_reporting(E_ALL);

$start = microtime(true);

define('FRAME_PATH', __DIR__ . '/../../frame');
define('LIB_PATH', __DIR__ . '/../../libs');
define('VENDOR_PATH', __DIR__ . '/../../inauth');

define('CONFIG', 'Passport');
require(VENDOR_PATH . '/config/passport/config.inc.php');
require(FRAME_PATH . '/Autoloader.class.php');
//or
//require(FRAME_PATH . '/PsrAutoloader.class.php');
require(FRAME_PATH . '/Application.class.php');

$root_path_setting = array(
	'Frame' => FRAME_PATH,
	'Libs' => LIB_PATH,
    'default' => VENDOR_PATH,
);
$autoloader = Autoloader::register($root_path_setting);
//or
//$autoloader = PsrAutoloader::register($root_path_setting);

//get the app
$app = \Frame\Application::instance();

$app->singleton('config', function () {
    return new \Inauth\Libs\Config(CONFIG);
});

//注册module的namespace
$app->scripts_namespace = '\\Inauth\\Scripts\\';

$app->singleton('request', function($c) {
    return new \Libs\Http\BasicScriptsRequest(); 
});

//router
$app->singleton('router', function($c) {
    return new \Libs\Router\BasicScriptsRouter($c);
});

$app->singleton('response', function($c) {
    return new \Libs\Http\BasicScriptsResponse(); 
});

//声明logWriter
$app->singleton('logWriter', function($c) {
    return new \Libs\Log\BasicLogWriter();
});

$app->singleton('view', function($c) {
    return new \Libs\View\None($c);
});

$app->run();
