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
class DealersService
{
    private $dealersRepository;

    /**
     * DealerWebsitesService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->dealersRepository = $repository;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return (array) $this->dealersRepository->getDealerById($id);
    }
}