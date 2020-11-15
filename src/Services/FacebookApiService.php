<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 25/06/2018
 * Time: 09:46
 */

namespace DynamicFbAds\Services;

use DynamicFbAds\Library\Pocket;
use FacebookAds\Api;
use FacebookAds\Object\Business;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\ProductCatalog;
use FacebookAds\Object\Fields\ProductCatalogFields;

/**
 * Class GoogleApiService
 *
 * @package DynamicFbAds\Services
 */
class FacebookApiService
{
    // google client
    private $api;
    // refresh token file name from config
    private $refresh_token_file;
    // refresh code from file or get from google refreshed
    private $refresh_token;
    // refreshed default false
    private $refreshed = false;
    // folder_file_ids from config
    private $folderFileIds;
    // main FolderID loads from mainFolderIdFile or google drive
    private $mainFolder;
    // current website folder
    private $websiteFolder;
    // google drive Service
    private $service;
    // all folders od folder
    private $folder_file_ids;
    // facebook api config
    private $config;

    public function __construct()
    {
        $this->api = Pocket::getDep()->make('Facebook_Api');
        $this->config = Pocket::get('config')['facebook_api'];
    }

    /**
     * @param $catalogName
     *
     * @return @string
     * @throws \Exception
     */
    private function createAutomotiveCatalog($catalogName)
    {
        $this->api->setLogger(new CurlLogger());
        $id = $this->config['business_id'];
        $product_catalog = new ProductCatalog();
        $product_catalog->setParentId($id);
        $product_catalog->setData(
            [
                ProductCatalogFields::NAME => $catalogName,
                ProductCatalogFields::VERTICAL => "vehicles",
            ]
        );
        return $product_catalog->create()->getData()['id'];
    }

    private function getFolderIdFile($catalogName)
    {
        $catalogNameHash = md5($catalogName);
        return __DIR__ . '/../../' . $this->config['folder_file_ids'] . '/' . $catalogNameHash;
    }

    public function getCatalogAndFeedIds($catalogName)
    {
        $file = $this->getFolderIdFile($catalogName);
        if (is_file($file)) {
            $fileContentSplit = explode(":", file_get_contents($file));
            return $fileContentSplit;
        }
        return false;
    }

    public function createCatalogFeed($catalogName, $url, $hours = 8)
    {
        $catProId = $this->getCatalogAndFeedIds($catalogName);
        if ($catProId) {
            $catalogId = $catProId[1];
            $feedId = $catProId[0];
            print "Website Catalog already exist.\n";
        } else {
            $catalogId = $this->createAutomotiveCatalog($catalogName);
            $fields = [];
            $params = [
                'name' => $catalogName,
                'schedule' => [
                    'interval' => 'DAILY',
                    'url' => $url,
                    'hour' => $hours
                ]
            ];
            $feedId = (new ProductCatalog($catalogId))->createProductFeed(
                $fields,
                $params
            )->getData()['id'];
            $file = $this->getFolderIdFile($catalogName);
            file_put_contents($file, $feedId.":".$catalogId);
        }
        $this->createProductSet($catalogId);
        return [
            'CatalogId' => $catalogId,
            'FeedId' => $feedId,
            ];
    }

    private function createProductSet($catalogId)
    {
        $filters = [
            'New Auto' => '{"state_of_vehicle":{"eq":"New"}}',
            'Used Auto' => '{"state_of_vehicle":{"eq":"Used"}}',
            'Certified Auto' => '{"custom_label_0":{"eq":"certified"}}'
            ];
        foreach ($filters as $name => $filter) {
            try {
                (new ProductCatalog($catalogId))->createProductSet(
                    [],
                    [
                        'name' => $name,
                        'filter' => $filter
                    ]
                );
            } catch (\Exception $e) {
                echo 'Caught exception: ', $name, " - ", $e->getMessage(), "\n";
            }
        }
    }
}
