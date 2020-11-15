<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 28/06/2018
 * Time: 11:01
 */

namespace DynamicFbAds\Library;

/**
 * Class DataFilter
 *
 * @package DynamicFbAds\Library
 */
class DataFilter
{
    public $vehicles;

    /**
     * @param $type
     *
     * @return $this
     */
    public function filterByType($type)
    {
        switch ($type) {
            case 'New':
                $this->vehicles = $this->filterByNewUsed(1);
                break;
            case 'Used':
                $this->vehicles = $this->filterByNewUsed(2);
                break;
            case 'Certified':
                $this->vehicles = $this->filterByCertified();
        }
        return $this;
    }

    /**
     * @param $priceFieldNew
     * @param $priceFieldUsed
     *
     * @return $this
     */
    public function filterZeroPrice($priceFieldNew, $priceFieldUsed)
    {
        $this->vehicles = array_filter($this->vehicles, function ($vehicle) use ($priceFieldNew, $priceFieldUsed) {
            return $vehicle[
                    $vehicle["NewUsed"] === 1 ? $priceFieldNew : $priceFieldUsed
                ] > 0;
        });
        return $this;
    }

    /**
     * @return $this
     */
    public function filterZeroPhoto()
    {
        $this->vehicles = array_filter($this->vehicles, function ($vehicle) {
            return $vehicle['PhotoCount']>0;
        });
        return $this;
    }

    /**
     * @param $newUsed
     *
     * @return array
     */
    public function filterByNewUsed($newUsed)
    {
        return array_filter($this->vehicles, function ($vehicle) use ($newUsed) {
            return $vehicle['NewUsed'] == $newUsed;
        });
    }

    /**
     * @return array
     */
    public function filterByCertified()
    {
        return array_filter($this->vehicles, function ($vehicle) {
            return $vehicle['NewUsed'] == 2 && $vehicle['Certified'];
        });
    }

    /**
     * @param $key
     *
     * @return array[]|false|string[]
     */
    public function splitByCapitalized($key)
    {
        return preg_split('/(?=[A-Z])/', $key, -1, PREG_SPLIT_NO_EMPTY);
    }
}