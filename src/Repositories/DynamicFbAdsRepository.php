<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 12/06/2018
 * Time: 14:03
 */

namespace DynamicFbAds\Repositories;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Models\DynamicFbAdsModel;
use DynamicFbAds\Models\ModelInterface;

/**
 * Class DynamicFbAdsRepository
 *
 * @package DynamicFbAds\Repositories
 */
class DynamicFbAdsRepository extends AbstractRepository implements RepositoryInterface
{
    private $model;

    /**
     * @param ModelInterface $model
     *
     * @return bool
     */
    public function save(ModelInterface $model)
    {
        $this->model = $model;
        $fieldValues = $this->model->getNormilizedArray();
        if (!$this->model->id) {
            if ($this->dbLocal->query('INSERT INTO dynamic_fb_ads %v', $fieldValues)) {
                return $this->dbLocal->getInsertId();
            }
        } elseif ($this->model->id > 0) {
            $this->dbLocal->query(
                "UPDATE dynamic_fb_ads SET",
                $fieldValues,
                'WHERE id = ?',
                $this->model->id
            );
            return $this->model->id;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getWebsites()
    {
        return $this->dbLocal->query(
            "SELECT * 
             FROM dynamic_fb_ads 
             ORDER BY website_name"
        )->fetchAll();
    }

    /**
     * @return mixed
     */
    public function getItemsId()
    {
        return $this->dbLocal->query(
            "SELECT id 
             FROM dynamic_fb_ads"
        )->fetchAll();
    }

    public function getDigitalRetailDealerList($websiteId)
    {
        $data = $this->dbLocal->query(
            "SELECT t1.module_config as data FROM 
	            builder.dfb_item_component_modules t1
            LEFT JOIN builder.dfb_website_menu_items t2 ON t1.item_id = t2.id
            LEFT JOIN builder.dfb_website_menus t3 ON t2.menu_id = t3.id
            LEFT JOIN builder.dfb_websites t4 ON t3.website_id = t4.id
            WHERE
	            t1.module_view_id = 634 AND
	            t1.is_active = 1 AND
	            t4.website_id = ?
            LIMIT 1", $websiteId)->fetch();
        if (!$data['data']) return [];
        $config = unserialize($data['data']);
        return $config['multiDealer']['dealersFilter']['dealerId'] ?? [];
    }
    /**
     * @param $id
     *
     * @return $this|bool
     */
    public function load($id)
    {
        $row = $this->dbLocal->fetch('SELECT * FROM dynamic_fb_ads WHERE id = ?', $id);
        if ($row) {
            return (new DynamicFbAdsModel)->loadFields($row);
        }
        return false;
    }
    /**
     * @param $id
     *
     * @return mixed
     */
    public function deleteById($id)
    {
        return $this->dbLocal
                ->query('DELETE FROM dynamic_fb_ads WHERE id = ?', $id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->dbLocal->fetch('SELECT * FROM dynamic_fb_ads WHERE id = ?', $id);
    }
}
