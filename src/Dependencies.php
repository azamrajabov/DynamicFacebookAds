<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 11/06/2018
 * Time: 12:57
 */

use DynamicFbAds\Library\Pocket;

$injector = new \Auryn\Injector;

// HttpRequest and HttpResponse
$injector->alias('HttpRequest', 'Symfony\Component\HttpFoundation\Request');
$injector->share('Symfony\Component\HttpFoundation\Request');
$injector->define('Symfony\Component\HttpFoundation\Request', [
    ':query' => $_GET,
    ':request' => $_POST,
    ':attributes' => [],
    ':cookies' => $_COOKIE,
    ':files' => $_FILES,
    ':server' => $_SERVER,
]);
$injector->alias('HttpResponse', 'Symfony\Component\HttpFoundation\Response');
$injector->share('Symfony\Component\HttpFoundation\Response');

// Twig
$injector->delegate('Twig_Environment', function () use ($injector) {
    $loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
    $twig = new Twig_Environment($loader);
    return $twig;
});

// MySQL
$injector->alias('DataBase', 'Dibi\Connection');
$injector->share('Dibi\Connection');
$injector->define('Dibi\Connection', [[
    'driver'   => 'mysqli',
    'host' => Pocket::get('config')['mysql']['host'],
    'username' => Pocket::get('config')['mysql']['user'],
    'password' => Pocket::get('config')['mysql']['pass'],
    'database' => Pocket::get('config')['mysql']['database']
]]);

// Facebook Api
$injector->delegate('Facebook_Api', function () use ($injector) {
    $api = FacebookAds\Api::init(
        Pocket::get('config')['facebook_api']['app_id'],
        Pocket::get('config')['facebook_api']['app_secret'],
        Pocket::get('config')['facebook_api']['access_token']
    );
    return $api;
});

// Save Dependency to static Class Method
Pocket::setDep($injector);
