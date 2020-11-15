<?php
/*
 * CLI commands:
 * php cli.php --sync=[all|Id]
 */

require __DIR__ . '/src/Bootstrap_cli.php';

use DynamicFbAds\Services\DynamicFbAdsService;
use DynamicFbAds\Repositories\DynamicFbAdsRepository;
use DynamicFbAds\Services\SyncService;

$syncParam = current(
    array_filter($_SERVER['argv'], function ($param) {
        return strstr($param, "--sync=");
    })
);

if (!$syncParam) {
    die("Sync param is missing..\n");
}

$syncId = explode('--sync=', $syncParam)[1];

$adwSrv = new DynamicFbAdsService(new DynamicFbAdsRepository());

$items = $adwSrv->getItemsId($syncId);

if (!$items) {
    die("Invalid ID.\n");
}

///  TODO: install pthreads extension and execute with multi threads
$syncService = new SyncService();
$i = 1;
foreach ($items as $item) {
    print $i++ . " / " . count($items) . PHP_EOL;
    $syncService->doSyncById($item['id']);
}
die('Finished..');
