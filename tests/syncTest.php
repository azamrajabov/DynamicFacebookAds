<?php

namespace tests;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Repositories\DynamicFbAdsRepository;
use DynamicFbAds\Models\DynamicFbAdsModel;
use DynamicFbAds\Services\DynamicFbAdsService;
use DynamicFbAds\Services\SyncService;

require __DIR__ . '/../vendor/autoload.php';
// save config into pocket
Pocket::set('config', include __DIR__ . '/../config/local/Config.php');
require __DIR__.'/../src/Dependencies.php';

/**
 * Class AdwordWebsitesRepositoryTest
 *
 * @package tests
 */

class SyncTest extends \PHPUnit\Framework\TestCase
{
    private $syncService;

    public function setUp()
    {
        $this->syncService = new SyncService();
    }

    public function testMockedSyncDoSyncByIdWithoutFacebookApi()
    {
        // Mock google Sheet saving
        $syncService = $this->getMockBuilder('DynamicFbAds\Services\SyncService')
            ->setMethods(['saveToFacebook', 'checkFacebookToken'])->getMock();
        $syncService->method('saveToFacebook')->willReturn(true);
        $syncService->method('checkFacebookToken')->willReturn(true);
        self::assertEquals(true, $syncService->doSyncById(125));
    }
}
