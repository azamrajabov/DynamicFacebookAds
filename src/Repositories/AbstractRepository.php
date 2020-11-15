<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 18/06/2018
 * Time: 10:03
 */

namespace DynamicFbAds\Repositories;

use DynamicFbAds\Models\ModelInterface;
use DynamicFbAds\Library\Pocket;

/**
 * Class AbstractRepository
 *
 * @package DynamicFbAds\Repositories
 */
abstract class AbstractRepository
{
    public $db;
    public $dbLocal;
    /**
     * AbstractRepository constructor.
     *
     * @param ModelInterface $model
     */
    public function __construct()
    {
        $this->db = Pocket::getDep()->make('DataBase');
        $this->dbLocal = new \Dibi\Connection([
            'driver'   => 'mysqli',
            'host'     => Pocket::get('config')['mysql_local']['host'],
            'username' => Pocket::get('config')['mysql_local']['user'],
            'password' => Pocket::get('config')['mysql_local']['pass'],
            'database' => Pocket::get('config')['mysql_local']['database'],
        ]);
    }
}