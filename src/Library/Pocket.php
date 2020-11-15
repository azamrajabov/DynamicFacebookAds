<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 24/05/2018
 * Time: 11:01
 */

namespace DynamicFbAds\Library;

/**
 * Class Pocket
 *
 * @package DynamicFbAds\Library
 */
class Pocket
{
    public static $depends;
    public static $bunch = [];

    public static function setDep($depends)
    {
        self::$depends = $depends;
    }

    public static function getDep()
    {
        return self::$depends;
    }

    public static function set($key, $value)
    {
        self::$bunch[$key] = $value;
    }

    public static function get($key)
    {
        if (isset(self::$bunch[$key])) {
            return self::$bunch[$key];
        }
        return null;
    }
}
