<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 28/06/2018
 * Time: 11:26
 */

namespace DynamicFbAds\Library\CustomLibrary;

use DynamicFbAds\Library\DataFilter;
use DynamicFbAds\Library\Pocket;

/**
 * Class CustomDataFilter878
 *
 * @package DynamicFbAds\Library\CustomLibrary
 */
class CustomDataFilter extends DataFilter
{
    /**
     * @return DataFilter
     */
    public function filterZeroPhoto()
    {
        $websiteId = Pocket::get('website')['id'];
        $domain = Pocket::get('website')['domain'];
        if (in_array($websiteId, [1189, 1190, 2036])) {
            $this->vehicles = array_filter($this->vehicles, function ($vehicle) {
                return $vehicle['PhotoCount'] > 1;
            });
        } elseif (in_array($websiteId, [3041, 3042])) {
            // MB Indianapolis, WW Motors dynamic dynamicFbAds update case# 02373932
            $this->vehicles = array_filter($this->vehicles, function ($vehicle) {
                return $vehicle['PhotoCount'] <= 30;
            });
        } elseif (in_array($domain, ['holidayautomotive.com','holidayfordusa.com','holidaymazda.com'])) {
            // remove vehicles if photo count less than 2
            $this->vehicles = array_filter($this->vehicles, function ($vehicle) {
                return $vehicle['PhotoCount'] >= 6;
            });
        } elseif (in_array($websiteId, [4570])) {
            // Rule for Dynamic ads case# 02452685
            $this->vehicles = array_filter($this->vehicles, function ($vehicle) {
                return $vehicle['PhotoCount'] < 2;
            });
        } elseif ($websiteId == 4312) {
            // show vehicles that have a maximum of 40 images case# 02473642
            $this->vehicles = array_filter($this->vehicles, function ($vehicle) {
                return $vehicle['PhotoCount'] <= 40;
            });
        } elseif ($websiteId == 2755) {
            // show vehicles that have a minimum of 6 images
            $this->vehicles = array_filter($this->vehicles, function ($vehicle) {
                return $vehicle['PhotoCount'] >= 5;
            });
        }
        return parent::filterZeroPhoto();
    }
}
