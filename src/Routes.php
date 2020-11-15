<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 11/06/2018
 * Time: 13:40
 */

use DynamicFbAds\Library\Pocket;

Pocket::set('Routers', [
    ['GET', '/', ['DynamicFbAds\Controllers\IndexController', 'index']],
    ['GET', '/index/feed', ['DynamicFbAds\Controllers\IndexController', 'feed']],
    ['GET', '/add', ['DynamicFbAds\Controllers\ActionController', 'showForm']],
    ['GET', '/edit/{id:\d+}', ['DynamicFbAds\Controllers\ActionController', 'edit']],
    ['POST', '/sync/{id:\d+}', ['DynamicFbAds\Controllers\ActionController', 'syncById']],
    ['GET', '/opencatalog/{id:\d+}', ['DynamicFbAds\Controllers\ActionController', 'openCatalog']],
    ['POST', '/add', ['DynamicFbAds\Controllers\ActionController', 'save']],
    ['POST', '/delete', ['DynamicFbAds\Controllers\ActionController', 'delete']],
    ['POST', '/refreshtoken', ['DynamicFbAds\Controllers\ActionController', 'refreshToken']],
]);
