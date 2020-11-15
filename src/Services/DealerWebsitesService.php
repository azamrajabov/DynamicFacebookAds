<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 19/06/2018
 * Time: 15:37
 */

namespace DynamicFbAds\Services;

use DynamicFbAds\Repositories\RepositoryInterface;

/**
 * Class DealerWebsitesService
 *
 * @package DynamicFbAds\Services
 */
class DealerWebsitesService
{
    private $dealerWebsitesRepository;

    /**
     * DealerWebsitesService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->dealerWebsitesRepository = $repository;
    }

    /**
     * @return mixed
     */
    public function getDealerWebsites()
    {
        return $this->dealerWebsitesRepository->getDealerWebsites();
    }

    public function getWebsiteFeeds($websiteId)
    {
        return $this->dealerWebsitesRepository->getWebsiteFeeds($websiteId);
    }
    /**
     * @return mixed
     */
    public function getById($id)
    {
        return $this->dealerWebsitesRepository->getById($id);
    }
}