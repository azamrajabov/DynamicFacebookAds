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

require __DIR__.'/../src/Routes.php';
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

// Requests
$request = Pocket::getDep()->make('HttpRequest');
$response = Pocket::getDep()->make('HttpResponse');

// Add Routing Callback
$routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
    foreach (Pocket::get('Routers') as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};

// set up routing
$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        //$response->setContent('404 - Page not found');
        //$response->setStatusCode($response::HTTP_NOT_FOUND);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];
        $class = Pocket::getDep()->make($className);
        $class->$method($vars);
        break;
}

$response->setCharset('UTF-8');
$response->prepare($request);
$response->send();