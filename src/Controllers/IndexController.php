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
//use DynamicFbAds\Services\GoogleApiService;
use DynamicFbAds\Services\DealerWebsitesService;

/**
 * Class IndexController
 *
 * @package DynamicFbAds\Controllers
 */
class IndexController
{
    private $request;
    private $response;
    private $renderer;
    private $dynamicFbAdsService;
    private $dealerWebsitesService;
    //private $googleApiService;

    public function __construct()
    {
        $this->request = Pocket::getDep()->make('HttpRequest');
        $this->response = Pocket::getDep()->make('HttpResponse');
        $this->renderer = Pocket::getDep()->make('Twig_Environment');
        $this->dynamicFbAdsService = new DynamicFbAdsService(
            new DynamicFbAdsRepository()
        );
        $this->dealerWebsitesService = new DealerWebsitesService(
            new DealerWebsitesRepository()
        );
        //$this->googleApiService = new GoogleApiService();
    }

    /**
     * Index
     */
    public function index()
    {
        $dealerWebsites = [];
        $websites = $this->dynamicFbAdsService->getWebsites();
        // check if google API refreshTonken is ok
        $data = [
            'webFolder' => Pocket::get('config')['web_folder'],
            'dynamicFbAds' => $websites,
            //'refreshTokenUrl' => !$this->googleApiService->checkRefreshToken()
            //                        ?$this->googleApiService->generateRefreshTokenURL()
            //                        :'',
            'msg' => $this->request->get('msg')
        ];
        $html = $this->renderer->render('index.html', $data);
        $this->response->setContent($html);
    }
   /**
     * json output
     */
    public function feed()
    {
        $result = $this->dealerWebsitesService->getWebsiteFeeds(
            $this->request->get('websiteId')
        );
        $this->response->setContent(json_encode($result));
    }
}