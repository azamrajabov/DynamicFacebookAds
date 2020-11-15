<?php
/**
 * @author Marco Troisi
 * @created 04.04.15
 */

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

class AdwordWebsitesBaseTest extends \PHPUnit\Framework\TestCase
{
    private $db;
    public function setUp()
    {
        $this->db = Pocket::getDep()->make('DataBase');
    }
    public function testHelloWorld()
    {
        $var = "Hello World";
        self::assertEquals($var, "Hello World", "Failed!");
    }
    public function testShowAllTableData()
    {
        $rows = $this->db->query(
            "SELECT * 
             FROM dynamic_fb_ads 
             ORDER BY website_name"
        )->fetchAll();
        self::assertNotEmpty($rows, "Failed!");
    }
    public function testDynamicFbAdsService()
    {
        $service = new DynamicFbAdsService(new DynamicFbAdsRepository());
        $websites = $service->getWebsites();
        self::assertNotEmpty($websites, "Failed!");
    }
    public function testGetDealerIdData()
    {
        $dealerService = new \DynamicFbAds\Services\DealersService(
            new \DynamicFbAds\Repositories\DealersRepository());
        self::assertNotEmpty($dealerService->getById(7907));
    }
}
