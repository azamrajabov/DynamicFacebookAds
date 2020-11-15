<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 28/06/2018
 * Time: 09:51
 */

namespace DynamicFbAds\Library;

use DynamicFbAds\Repositories\IncentivesRepository;
use DynamicFbAds\Services\IncentivesService;
use DynamicFbAds\Repositories\DealersRepository;
use DynamicFbAds\Services\DealersService;

/**
 * Class DataPuller
 *
 * @package DynamicFbAds\Library
 */
class DataPuller
{
    public $vehicles;
    public $vehicle;
    protected $newPrice;
    protected $usedPrice;
    protected $fields = [];
    protected $type;
    protected $dataFilter;
    protected $id;
    protected $images;
    protected $marketplace;
    public $dealers;

    protected function setDataFilter()
    {
        $dataFilterClass = "DynamicFbAds\Library\DataFilter";
        if (class_exists("DynamicFbAds\Library\CustomLibrary\CustomDataFilter"
            .$this->id)) {
            // custom DataFilter for specific item
            $dataFilterClass = "DynamicFbAds\Library\CustomLibrary\CustomDataFilter"
                .$this->id;
        } elseif (class_exists("DynamicFbAds\Library\CustomLibrary\CustomDataFilter")) {
            $dataFilterClass = "DynamicFbAds\Library\CustomLibrary\CustomDataFilter";
        }
        $this->dataFilter = new $dataFilterClass();
    }

    /**
     * @param $vehicles
     *
     * @return array
     */
    public function getRows($vehicles)
    {
        $this->vehicles = $vehicles;
        $fieldsConfig = Pocket::get('config')['fields']['default'];
        // do actions before process
        $this->actionsBeforeGetRows();
        // start generating rows
        $rows = [];
        foreach ($this->dataFilter->vehicles as $vehicle) {
            $row = [];
            $this->vehicle = $vehicle;
            $this->images = isset($vehicle['Images'])
                ?explode(',', $vehicle['Images'])
                :[];
            Pocket::set('vehicle', $vehicle);
            foreach ($this->fields as $field) {
                $dealer = $this->loadVehicleDealer($vehicle['dealer_id']);
                $fieldStr = $fieldsConfig[$field]??$field;
                $method = "get".ucfirst($fieldStr);
                if (method_exists($this, $method)) {
                    // run if Method exists
                    $row[$field] = $this->{$method}();
                } elseif (isset($vehicle[$fieldStr])) {
                    // apply if Field available
                    $row[$field] = $vehicle[$fieldStr];
                } elseif (isset($dealer[$fieldStr])) {
                    // apply if Field available
                    $row[$field] = $dealer[$fieldStr];
                } elseif (preg_match('/image\[(\d+)\].url/', $field, $match)) {
                    $row[$field] = $this->getImageUrl($match[1]);
                } else {
                    // Otherwise show values by fields
                    $row[$field] = $this->getByFields($this->splitByCapitalized($field));
                }
            }
            if ($row) {
                $rows[] = $row;
            }
        }
        // actions after done rows
        //$rows = $this->orderByYearMakeModel($rows);
        return $rows;
    }

    protected function getImageUrl($num)
    {
        if (!$num && $this->vehicle['main_image_url']) {
            return $this->vehicle['main_image_url'];
        }
        # http?s://domain/stock/DealerID-VIN/Image
        if (isset($this->images[$num])) {
            return 'https://cdn-ds.com/stock/'
                . $this->vehicle['DealerID'] . '-' . $this->vehicle['VIN']
                . '/' . $this->images[$num];
        }
        return '';
    }

    protected function getNotDefined()
    {
        return 'Not Defined';
    }

    protected function getVin()
    {
        return $this->vehicle['VIN'];
    }

    protected function loadVehicleDealer($dealerId)
    {
        if (isset($this->dealers[$dealerId])) {
            return $this->dealers[$dealerId];
        }
        // get from DB
        $dealerService = new DealersService(new DealersRepository());
        $this->dealers[$dealerId] = $dealerService->getById($dealerId);

        return $this->dealers[$dealerId];
    }

    protected function getBody_style()
    {
        return $this->vehicle['Body'];
    }

    protected function getVehicledescription()
    {
        return trim($this->vehicle['VehicleDescription'])
            ? strip_tags($this->vehicle['VehicleDescription'])
            : 'No Description';
    }

    protected function getExteriorcolor()
    {
        return trim($this->vehicle['ExteriorColor'])?:'No Color';
    }

    protected function getPrice()
    {
        return $this->vehicle[
            $this->vehicle['NewUsed'] === 1
                ?$this->newPrice
                :$this->usedPrice
        ]. ' USD'
            ??'';
    }
    protected function getCondition()
    {
        return "EXCELLENT";
    }
    protected function getTitle()
    {
        return $this->vehicle['Year']
            . " " . $this->vehicle['Make']
            . " " . $this->vehicle['Model']
            . " " . $this->vehicle['Trim']
            . " " . $this->vehicle['Body'];
    }
    protected function getNewUsed()
    {
        return $this->vehicle['NewUsed'] === 1
            ? "New"
            : "Used";
    }
    protected function getDealerCountry()
    {
        $countryCode = $this->dealers[$this->vehicle['dealer_id']]['dealer_country'];
        switch ($countryCode) {
            case 223:
                return "US";
                break;
            case 38:
                return "Canada";
                break;
            default:
                return "US";
                break;
        }
    }

    protected function getAvailability()
    {
        return 'available';
    }
    /**
     * @return string
     */
    protected function getMileageUnit()
    {
        return $this->getDealerCountry() != 'US'
            ? "KM"
            : "MI";
    }
    protected function getFuelType()
    {
        $fuelType = $this->vehicle['FuelType'];
        if (stristr($fuelType, "gas")) {
            return "GASOLINE";
        } elseif (stristr($fuelType, "flex")) {
            return "FLEX";
        } elseif (stristr($fuelType, "diesel")) {
            return "DIESEL";
        } elseif (stristr($fuelType, "hybrid")) {
            return "HYBRID";
        } elseif (stristr($fuelType, "electric")) {
            return "ELECTRIC";
        } else {
            return "OTHER";
        }
    }
    protected function getBody()
    {
        if (!in_array(strtolower($this->vehicle['Body']), [
                "convertible",
                "coupe",
                "crossover",
                "hatchback",
                "minivan",
                "truck",
                "suv",
                "sedan",
                "van",
                "wagon",])) {
            return "Other";
        }
        return $this->vehicle['Body'];
    }
    protected function getDriveTrain()
    {
        $driveTrain = $this->vehicle['Drivetrain'];
        if (stristr($driveTrain, "front")) {
            return "FWD";
        } elseif (stristr($driveTrain, "all") || stristr($driveTrain, "awd")) {
            return "AWD";
        } elseif (stristr($driveTrain, "rear")) {
            return "RWD";
        } elseif (stristr($driveTrain, "4x2")) {
            return "4x2";
        } elseif (stristr($driveTrain, "4x4") || stristr($driveTrain, "four")) {
            return "4x4";
        } else {
            return "Other";
        }
    }
    protected function getSalePrice()
    {
        return $this->vehicle['NewUsed'] === 1
            ? $this->vehicle['MSRP'] . ' USD'
            : '';
    }

    protected function getTransmission()
    {
        if (stristr($this->vehicle['Transmission'],'auto')) {
            return "Automatic";
        } elseif (stristr($this->vehicle['Transmission'],'man')) {
            return "Manual";
        } else {
            return "Other";
        }
    }

    protected function getDealer_main_phone()
    {
        $dealer = $this->loadVehicleDealer($this->vehicle['dealer_id']);
        return "1" . str_replace(
            ["(", ")", " ","-"],
            "",
            $dealer['dealer_main_phone']
        );
    }

    protected function actionsBeforeGetRows()
    {
        $fb_website = Pocket::get('dynamicFbAdsWebsite');
        $this->newPrice = $fb_website['new_price_field'];
        $this->usedPrice = $fb_website['used_price_field'];
        $this->id = Pocket::get('currentId');
        $this->type = $fb_website['marketplace']
            ?'Used'
            :Pocket::get('currentType');
        $this->setFields();
        // set filters and group
        $this->setDataFilter();
        $this->dataFilter->vehicles = $this->applyAdjusts();
        // filter vehicles by tyoe and filter out zero price
        $this->dataFilter
            ->filterByType($this->type)
            ->filterZeroPrice($this->newPrice, $this->usedPrice)
            ->filterZeroPhoto();
    }

    protected function applyAdjusts()
    {
        $vehicles = $this->vehicles;
        foreach ($this->fields as $field) {
            // check if custom adjust exists
            if (method_exists($this, 'adjust' . $field)) {
                $vehicles = $this->{'adjust' . $field}($vehicles);
            }
            // adjust by config
            if ($adjusts = Pocket::get('config')['adjusts'][strtolower($field)]
                    ?? false) {
                $vehicles = $this->adjustField($vehicles, $field, $adjusts);
            }
        }
        return $vehicles;
    }

    protected function setFields()
    {
        $fieldsConfig = Pocket::get('config')['fields'];
        // set fields
        if (!isset($fieldsConfig[$this->type])) {
            $this->fields = array_keys($fieldsConfig['default']);
        }
        if ($this->getMarketplace()) {
            $this->fields = array_merge($this->fields, $this->getMarketplaceFields());
        }
    }

    protected function getMarketplaceFields()
    {
        $fields = [
            'fb_page_id',
            'dealer_communication_channel',
            'dealer_privacy_policy_url'
        ];
        foreach (range(1, $this->getImagesLimit()) as $i) {
            $fields[] = 'image[' . $i . '].url';
        }
        return $fields;
    }

    protected function getImagesLimit()
    {
        # default image limit is 10
        return 10;
    }

    protected function getDealer_address()
    {
        // Required | The dealership address, formatted as {addr 1: [STREET ADDRESS],
        // city: [CITY], region: [STATE, COUNTY, REGION OR PROVINCE], country: [COUNTRY]. Postal code is optional.
        // {addr1: '550 Auto Center Dr', city: 'Watsonville', region: 'CA', postal_code: '96075', country: 'US'}
        $dealer = $this->loadVehicleDealer($this->vehicle['dealer_id']);
        return "{"
                . "addr1: '" . $dealer['dealer_address'] . "', "
                . "city: '" .  $dealer['dealer_city'] . "', "
                . "region: '" .  $dealer['dealer_state'] . "', "
                . "postal_code: '" . $dealer['dealer_zip'] . "', "
                . "country: '" . $this->getDealerCountry() . "'"
                ."}";
    }
    /**
     * @param $key
     *
     * @return array[]|false|string[]
     */
    protected function splitByCapitalized($key)
    {
        return $this->dataFilter->splitByCapitalized($key);
    }

    /**
     * @param $fields
     *
     * @return string
     */
    protected function getByFields($fields)
    {
        $values = [];
        foreach ($fields as $field) {
            if (isset($this->vehicle[$field]) && $this->vehicle[$field]) {
                $values[] = $this->vehicle[$field];
            }
        }
        return implode(" ", $values);
    }
    /**
     * @param $vehicle
     *
     * @return string
     */
    protected function getMainImageTag()
    {
        return "Main Image";
    }
    /**
     * @return string
     */
    protected function getUrl()
    {
        $website = Pocket::get('website');
        return $this->getWebsiteURL() . "/"
                . (!$website['IsTT']
                ? Pocket::get('dynamicFbAdsWebsite')['vehicle_details_url'] . "/"
                : "")
                . $this->vehicle['seo_url']
                . (Pocket::get('dynamicFbAdsWebsite')['precise_price']
                ? "/dr"
                : "");
    }

    /**
     * @return string
     */
    protected function getWebsiteURL()
    {
        $website = Pocket::get('website');
        return ($website['HttpsEnabled']
            ? "https://"
            : "http://")
            . (substr_count($website['domain'], '.') == 1
            ? 'www.'
            : '')
            . $website['domain'];
    }

    protected function getFb_page_id()
    {
        return Pocket::get('dynamicFbAdsWebsite')['fb_page_id']??'';
    }

    protected function getMarketplace()
    {
        return Pocket::get('dynamicFbAdsWebsite')['marketplace']??'0';
    }

    /**
     * terms page domain/terms default
     * @return string
     */
    protected function getDealer_privacy_policy_url()
    {
        return $this->getWebsiteURL() . '/terms';
    }

    protected function getDealer_communication_channel()
    {
        return 'CHAT';
    }
    /**
     * @param $rows
     *
     * @return mixed
     */
    protected function orderByYearMakeModel($rows)
    {
        array_multisort(
            array_column($rows, 'Year'),
            SORT_DESC,
            array_column($rows, 'Make'),
            SORT_ASC,
            array_column($rows, 'Model'),
            SORT_ASC,
            $rows
        );
        return $rows;
    }

    /**
     * @param $vehicles
     * @param $field
     * @param $adjusts
     *
     * @return array
     */
    protected function adjustField($vehicles, $field, $adjusts)
    {
        if ($field == 'body_style') {
            $field = 'Body';
        }
        return array_map(
            function ($vehicle) use ($field, $adjusts) {
                $vehicle = (array) $vehicle;
                foreach ($adjusts as $key => $values) {
                    foreach ($values as $val) {
                        if (stristr($vehicle[$field], $val)) {
                            $vehicle[$field] = $key;
                        }
                    }
                }
                return $vehicle;
            },
            $vehicles
        );
    }
}
