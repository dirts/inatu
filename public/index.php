<?php

//xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_CPU);

ini_set('display_errors', 0);
error_reporting(E_ALL);

$start = microtime(true);

define('FRAME_PATH', __DIR__ . '/../../frame');
define('LIB_PATH', __DIR__ . '/../../libs');
define('VENDOR_PATH', __DIR__ . '/../../inauth');

define('CONFIG', 'Passport');
define('MODLUES_NAMESPACE', '\\Inauth\\Modules\\');

require(VENDOR_PATH . '/config/passport/config.inc.php');
require(FRAME_PATH . '/Autoloader.class.php');
require(FRAME_PATH . '/Application.class.php');

$root_path_setting = array(
	'Frame' => FRAME_PATH,
	'Libs' => LIB_PATH,
    'default' => VENDOR_PATH,
);
$autoloader = Autoloader::register($root_path_setting);

$app = \Frame\Application::instance();

$app->singleton('config', function () {
    return new \Inauth\Libs\Config(CONFIG);
});

//注册module的namespace
$app->module_namespace = '\\Inauth\\Modules\\';

$app->singleton('request', function($c) {
    return new \Inauth\Libs\Http\Request($c);
    //return new \Libs\Http\BasicRequest(); 
});

//response
$app->singleton('response', function($c) {
    return new \Inauth\Libs\Http\Response($c); 
});

//router
$app->singleton('router', function($c) {
    return new \Libs\Router\BasicRouter($c);
});

$app->singleton('view', function($c) {
    return new \Libs\View\Json($c);
});

$app->singleton('logWriter', function () {
    return new \Inauth\Libs\Log\Logger();
});

$app->run();
$spend = microtime(true) - $start;
$app->log->log("inauth.request", "[{$app->request->path}]\t[request:" .json_encode($_REQUEST) ."]\t [time: {$spend}s]");

//register_shutdown_function(function() {
/*
    $xhprof_data = xhprof_disable();
    $XHPROF_ROOT = realpath(dirname(__FILE__) . '/../../xhprof');
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php"; 
    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo"); 
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
*/
