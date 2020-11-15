<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 28/06/2018
 * Time: 10:30
 */

namespace DynamicFbAds\Library\CustomLibrary;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Library\DataPuller;

/**
 * Class CustomDataPuller
 *
 * @package DynamicFbAds\Library\CustomLibrary
 */
class CustomDataPuller extends DataPuller
{
    protected function getUrl()
    {
        $domain = Pocket::get('website')['domain'];
        if (in_array($domain, ['fdlhyundai.com','vanhornhyundai.com'])) {
            return sprintf(
                "http://www." . $domain . "/vehicle-details/"
                .($this->vehicle['NewUsed'] == 1 ? "new" : "used")
                ."-%s-%s-%s-%s",
                $this->vehicle['Year'],
                str_replace(" ", "-", $this->vehicle['Make']),
                str_replace(" ", "-", $this->vehicle['Model']),
                $this->vehicle['VIN']
            );
        } elseif (in_array($domain, ['michaelchevrolet.com','michaelcadillac.com'])) {
            return "http://www." . $domain
                ."/VehicleSearchResults?search=new&stockOrVIN="
                .$this->vehicle['VIN'];
        } elseif (in_array($domain, ['ingramparknissan.com','ipacauto.com'])) {
            return "https://www." . $domain
                ."/vehicledetailsvin.aspx?vin=" . $this->vehicle['VIN'];
        } elseif ($domain == 'randymariongmc.net') {
            return sprintf(
                "http://www.%s/VehicleSearchResults?search="
                .($this->vehicle['NewUsed']==1 ? "new" : "used")
                ."&vin=%s",
                $domain,
                $this->vehicle['VIN']
            );
        } elseif (in_array($domain, ['holidayfordfdl.net', 'holidayfordusa.com'])) {
            return sprintf(
                "https://www.holidayfordusa.com/searchall.aspx?q=%s",
                $this->vehicle['VIN']
            );
        } elseif ($domain == 'perrisvalleykia.com') {
            return sprintf(
                "http://www.%s/%s-inventory/index.htm?search=%s",
                $domain,
                $this->vehicle['NewUsed'] == 1 ? "new" : "used",
                $this->vehicle['VIN']
            );
        }
        return parent::getUrl();
    }

    /**
     * @return mixed|string
     */
    protected function getPrice()
    {
        // CANADA currency case# 00433327
        $price = parent::getPrice();
        $websiteId = Pocket::get('website')['id'];
        if (in_array($websiteId, [2199, 2755, 5703, 4312, 4731, 4746, 4655, 4654, 5595])) {
            $price = str_replace("USD", "CAD", $price);
        }
        return $price;
    }
    /**
     * @return mixed|string
     */
    protected function getSalePrice()
    {
        // CANADA currency case# 00433327
        $price = parent::getSalePrice();
        $websiteId = Pocket::get('website')['id'];
        if (in_array($websiteId, [2199, 2755])) {
            $price = str_replace("USD", "CAD", $price);
        }
        return $price;
    }
    protected function getId()
    {
        $websiteId = Pocket::get('website')['id'];
        if ($websiteId == 4505) {
            return $this->vehicle['VIN'];
        }
        return $this->vehicle['id'];
    }
}
