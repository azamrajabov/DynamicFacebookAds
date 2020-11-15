<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 22/06/2018
 * Time: 10:00
 */

namespace DynamicFbAds\Repositories;

use DynamicFbAds\Library\Pocket;

/**
 * Class CWVehiclesRepository
 *
 * @package DynamicFbAds\Repositories
 */
class CWVehiclesRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * @param $websiteId
     *
     * @return mixed
     */
    public function getWebsiteVehicles($websiteId)
    {
        return $this->db->query('SELECT 
            '. Pocket::get('config')['vehicleTableFields'] .'
            FROM c_website_vehicles
            WHERE website_id = ?
            ', $websiteId)
        ->fetchAll();
    }

    public function getWebsiteVehiclesByFeeds($websiteId, $feeds, $withImages = 0)
    {
        $selectFields = Pocket::get('config')['vehicleTableFields'];
        $where = 'v.website_id =' . $websiteId;
        $join = $groupBy = '';
        if ($withImages) {
            $selectFields = Pocket::get('config')['vehicleTableFields'].', GROUP_CONCAT(i.image) as Images ';
            $join = 'JOIN `vehicle_images` i ON i.vuidvuid=v.vuid ';
            $groupBy = ' GROUP BY v.VUID';
        }
        if (!empty($feeds) || $feeds) {
            $where .= ' AND v.DealerID IN ("' .implode('","', $feeds) . '")';
        }
        return $this->db->query('SELECT 
            '.$selectFields.'
            FROM c_website_vehicles v ' . $join .
            'WHERE ' . $where . $groupBy)
            ->fetchAll();
    }
}