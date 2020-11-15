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
class DealersRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * @return array
     */
    public function getDealerById($dealerId)
    {
        return $this->db->fetch(
            "SELECT *
             FROM dealers
             WHERE id = ?",
            $dealerId
        );
    }
}
