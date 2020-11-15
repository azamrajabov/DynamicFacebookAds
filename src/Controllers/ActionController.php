<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 11/06/2018
 * Time: 14:47
 */

namespace DynamicFbAds\Controllers;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Models\DynamicFbAdsModel;
use DynamicFbAds\Repositories\DynamicFbAdsRepository;
use DynamicFbAds\Repositories\DealerWebsitesRepository;
use DynamicFbAds\Services\DynamicFbAdsService;
use DynamicFbAds\Services\DealerWebsitesService;
//use DynamicFbAds\Services\GoogleApiService;
use DynamicFbAds\Services\SyncService;

/**
 * Class AddController
 *
 * @package DynamicFbAds\Controllers
 */
class ActionController
{
    private $request;
    private $response;
    private $renderer;
    private $dynamicFbAdsModel;
    private $errors;
    private $dynamicFbAdsService;
    private $dealerWebsitesService;
//    private $googleApiService;
    private $syncService;

    public function __construct()
    {
        $this->request = Pocket::getDep()->make('HttpRequest');
        $this->response = Pocket::getDep()->make('HttpResponse');
        $this->renderer = Pocket::getDep()->make('Twig_Environment');
        $this->dynamicFbAdsModel = new DynamicFbAdsModel();
        $this->dynamicFbAdsService = new DynamicFbAdsService(
            new DynamicFbAdsRepository()
        );
        $this->dealerWebsitesService = new DealerWebsitesService(
            new DealerWebsitesRepository()
        );
        $this->syncService = new SyncService();
    }

    public function save()
    {
        $this->errors = $this->dynamicFbAdsModel->validate();
        if (empty($this->errors) && $this->dynamicFbAdsService
                ->save($this->dynamicFbAdsModel)) {
            $this->redirectToIndex();
        } else {
            $this->showForm();
        }
    }

    public function delete()
    {
        if ($this->dynamicFbAdsService
                ->delete($this->request->get('id'))) {
            $this->redirectToIndex();
        }
        die("Failed to remove item.");
    }

    public function showForm()
    {
        $dealerWebsites = $this->dealerWebsitesService
                ->getDealerWebsites();
        $data = [
            'webFolder' => Pocket::get('config')['web_folder'],
            'prices' => Pocket::get('config')['prices'],
            'dealerWebsites' => $dealerWebsites,
            'data' => $this->dynamicFbAdsModel,
            'website_feeds' => $this->dealerWebsitesService->getWebsiteFeeds($this->dynamicFbAdsModel->website_id),
            'errors' => $this->errors
        ];
        $html = $this->renderer->render('add_edit.html', $data);
        $this->response->setContent($html);
    }

    /**
     * @param $query
     */
    public function edit($query)
    {
        $this->checkId($query);
        $this->dynamicFbAdsModel = $this
                                    ->dynamicFbAdsService
                                    ->loadData($query['id']);
        $this->showForm();
    }
    /**
     * @param $query
     */
    public function syncById($query)
    {
        $this->checkId($query);
        echo "<pre>";
        $this->syncService->doSyncById($query['id']);
        echo "</pre>";
        $url = $this->getCatalogLink($query['id']);
        if ($url) {
            echo "<hr><a href='{$url}'>Open Facebook Catalog</a>";
        }
        die('.');
    }
    /**
     * @param $query
     */
    private function checkId($query)
    {
        if (!isset($query['id']) || !$query['id']) {
            $this->redirectToIndex();
        }
    }

    public function refreshToken()
    {
        if (!$this->request->get('code')) {
            $this->redirectToIndex('?msg=Incorrect+Code');
        }
//        try {
//            $this->googleApiService->setFreshToken($this->request->get('code'));
//        } catch (\Google_Auth_Exception $e) {
//            $this->redirectToIndex('?msg=Invalid+Code');
//        }
        $this->redirectToIndex();
    }
    public function getCatalogLink($id)
    {
        $name = $this->dynamicFbAdsService->getById($id)['website_name'];
        $url = '';
        $folder = __DIR__.'/../../' . Pocket::get('config')['facebook_api']['folder_file_ids'] . '/';
        $folderFile = $folder . md5($name);
        if (is_file($folderFile)) {
            $feedCatalogIds = file_get_contents($folderFile);
            $catalogId = explode(':', $feedCatalogIds)[1];
            $url = 'https://www.facebook.com/products/catalogs/' . $catalogId. '/products';
        }
        return $url;
    }

    public function openCatalog($query)
    {
        $this->checkId($query);
        $url = $this->getCatalogLink($query['id']);
        if ($url) {
            header('location: ' . $url);
        }
        die('Catalog not found!');
    }

    /**
     * @param string $params
     */
    public function redirectToIndex($params = '')
    {
        header('Location: ' . Pocket::get('config')['web_folder'] . $params);
        exit();
    }
}