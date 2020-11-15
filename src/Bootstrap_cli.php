<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 31/05/2018
 * Time: 14:25
 */
require __DIR__ . '/../vendor/autoload.php';

use Whoops\Run;
use DynamicFbAds\Library\Pocket;

if (is_file(__DIR__ . '/../config/local/Config.php')) {
    $config = include __DIR__ . '/../config/local/Config.php';
} else {
    $config = include __DIR__.'/../config/Config.php';
}

// save config into pocket
Pocket::set('config', $config);

require __DIR__.'/../src/Dependencies.php';

$environment = Pocket::get('config')['environment'];
if ($environment == 'development') {
    error_reporting(E_ALL);
}
/**
 * Register the error handler
 */
$whoops = new Run();
if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
        echo 'Todo: Friendly error page and send an email to the developer';
    });
}
$whoops->register();
