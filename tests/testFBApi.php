<?php

namespace tests;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Services\FacebookApiService;

require __DIR__ . '/../vendor/autoload.php';
// save config into pocket
Pocket::set('config', include __DIR__ . '/../config/local/Config.php');
require __DIR__.'/../src/Dependencies.php';

/**
 * Class AdwordWebsitesRepositoryTest
 *
 * @package tests
 */

class FacebookApiTest extends \PHPUnit\Framework\TestCase
{
    private $fbApi;

    protected function setUp()
    {
        $this->fbApi = new FacebookApiService();
    }

    public function testCreateAutomotiveCatalog()
    {
        print_r($this->fbApi->createAutomotiveCatalog());
    }

    public function testCreateCatalogFeed()
    {
        $r = $this->fbApi->createCatalogFeed(
            'testCatalog',
            'http://{{URL}}/125-new.csv'
        );
        print_r($r);
    }
    public function testProductSets()
    {
        $r = $this->fbApi->createProductSet('NUMBER');
        print_r($r);
    }
}
