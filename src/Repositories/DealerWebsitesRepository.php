<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 14/06/2018
 * Time: 13:11
 */

namespace DynamicFbAds\Repositories;

use DynamicFbAds\Library\Pocket;

/**
 * Class DealerWebsitesRepository
 *
 * @package DynamicFbAds\Repositories
 */
class DealerWebsitesRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * @return array
     */
    public function getDealerWebsites()
    {
        return $this->db->query(
            "SELECT id, domain 
             FROM dealer_websites
             WHERE status = 1 AND Archive = 0
             "
        )->fetchAll();
    }

    /**
     * get website feeds
     * @param $websiteId
     *
     * @return array
     */
    public function getWebsiteFeeds($websiteId)
    {
        if (!$websiteId) {
            return [];
        }
        $result = $this->db->query(
            "SELECT DISTINCT(dealerId)
             FROM c_website_vehicles
             WHERE website_id = ?
             ORDER BY dealerId
             ",
            $websiteId
        )->fetchAll();
        return array_column($result, 'dealerId');
    }
    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->db->fetch('SELECT 
                                        dw.id,
                                        dw.domain,
                                        dw.AisInlineEnabled,
                                        wc.IsTwinTurbo as IsTT,
                                        wc.HttpsEnabled
                                FROM dealer_websites dw 
                                JOIN dealer_website_configuration wc
                                ON wc.WebsiteId = dw.id
                                WHERE dw.id = ?', $id);
    }
}