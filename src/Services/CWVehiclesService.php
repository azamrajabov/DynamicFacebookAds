<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 22/06/2018
 * Time: 10:06
 */

namespace DynamicFbAds\Services;

use DynamicFbAds\Repositories\RepositoryInterface;

/**
 * Class CWVehiclesService
 *
 * @package DynamicFbAds\Services
 */
class CWVehiclesService
{
    private $cwvehiclesRepository;

    /**
     * DealerWebsitesService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->cwvehiclesRepository = $repository;
    }

    /**
     * @param $websiteId
     * @param $feeds
     * @param $withImages
     *
     * @return mixed
     */
    public function getWebsiteVehiclesByFeeds($websiteId, $feeds, $withImages)
    {
        return $this->cwvehiclesRepository->getWebsiteVehiclesByFeeds($websiteId, $feeds, $withImages);
    }
}