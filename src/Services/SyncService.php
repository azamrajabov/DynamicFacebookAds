<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 29/08/2018
 * Time: 16:49
 */

namespace DynamicFbAds\Services;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Repositories\DynamicFbAdsRepository;
use DynamicFbAds\Repositories\DealerWebsitesRepository;
use DynamicFbAds\Repositories\CWVehiclesRepository;

/**
 * Class SyncService
 *
 * @package DynamicFbAds\Services
 */
class SyncService
{
    private $website;
    private $dynamicFbAdsWebsite;
    private $vehicles;
    private $websiteFeeds;
    private $currentId;
    private $types = [
        'all' => 'All',
    ];
    private $subTypes = [];
    private $dataFilter;
    private $dynamicFbAdsService;
    private $dealerWebsitesService;
    private $cwvehiclesService;
    private $facebookApiService;

    public function __construct()
    {
        $this->dynamicFbAdsService = new DynamicFbAdsService(new DynamicFbAdsRepository());
        $this->dealerWebsitesService = new DealerWebsitesService(new DealerWebsitesRepository());
        $this->cwvehiclesService = new CWVehiclesService(new CWVehiclesRepository());
        $this->facebookApiService = new FacebookApiService();
    }

    public function getDynamicFbAdsWebsiteById()
    {
        $this->dynamicFbAdsWebsite = $this->dynamicFbAdsService->getById($this->currentId);
        $this->websiteFeeds = $this->dynamicFbAdsWebsite['feed']
                            ?explode(",", $this->dynamicFbAdsWebsite['feed'])
                            :'';
        Pocket::set('dynamicFbAdsWebsite', $this->dynamicFbAdsWebsite);
    }

    public function getDealerWebsiteById()
    {
        $this->website = $this->dealerWebsitesService
                            ->getById($this->dynamicFbAdsWebsite['website_id']);
        Pocket::set('website', $this->website);
    }

    public function getWebsiteVehicles()
    {
        $this->vehicles = $this->cwvehiclesService->getWebsiteVehiclesByFeeds(
            $this->website['id'],
            $this->websiteFeeds,
            1
        );
    }

    public function loadData()
    {
        // Default Types
        $this->getDynamicFbAdsWebsiteById();
        $this->getDealerWebsiteById();
        $this->getWebsiteVehicles();
    }

    /**
     * @param $id
     * @return false
     */
    public function doSyncById($id)
    {
        if (!$id || !$this->dynamicFbAdsService->getItemsId($id)[0]) {
            return false;
        }
        $this->currentId = $id;
        Pocket::set('currentId', $id);
        $this->loadData();
        foreach ($this->types as $key => $type) {
            if ($key == 'is_certified' && !$this->dynamicFbAdsWebsite['is_certified']) {
                continue;
            }
            Pocket::set('currentType', $type);
            Pocket::set('currentSubTypes', []);
            // do action
            $feedFile = $this->writeCSVFeedFile();
            if ($feedFile) {
                $this->saveToFacebook($feedFile);
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function writeCSVFeedFile()
    {
        $service = new CSVFeedService($this->vehicles);
        return $service->writeCsv();
    }

    /**
     * @param $feedUrl
     */
    public function saveToFacebook($feedUrl)
    {
        // upload feed to facebook
        $file = $this->facebookApiService->createCatalogFeed(
            $this->dynamicFbAdsWebsite['website_name'],
            Pocket::get('config')['feedUrl'] . basename($feedUrl)
        );
    }
}
