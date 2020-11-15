<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 19/06/2018
 * Time: 14:21
 */

namespace DynamicFbAds\Services;

use DynamicFbAds\Repositories\RepositoryInterface;

 /**
 * Class DynamicFbAdsService
 *
 * @package DynamicFbAds\Services
 */
class DynamicFbAdsService
{
    private $dynamicFbAdsRepository;

    /**
     * DynamicFbAdsService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->dynamicFbAdsRepository = $repository;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function delete($id)
    {
        return $id > 0
            ? $this->dynamicFbAdsRepository->deleteById($id)
            : false;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function loadData($id)
    {
        return $id > 0
            ? $this->dynamicFbAdsRepository->load($id)
            : false;
    }

    /**
     * @param $model
     *
     * @return mixed
     */
    public function save($model)
    {
        return $this->dynamicFbAdsRepository->save($model);
    }

    /**
     * @return mixed
     */
    public function getWebsites()
    {
        $websites = [];
        foreach ($this->dynamicFbAdsRepository->getWebsites() as $website) {
            $websites[] = $this->setParams($website);
        }
        return $websites;
    }

    public function getItemsId($id)
    {
        return $id=='all'
            ? $this->dynamicFbAdsRepository->getItemsId()
            : [$this->getById($id)];
    }

    /**
     * @return mixed
     */
    public function getById($id)
    {
        $item = $this->dynamicFbAdsRepository->getById($id);
        return $this->setParams($item);
    }

    protected function setParams(&$item)
    {
        $item['marketplace'] = '';
        if ($item['params'] && $params = unserialize($item['params'])) {
            foreach ($params as $param => $value) {
                $item[$param] = $value;
            }
        }
        return (array) $item;
    }

}
