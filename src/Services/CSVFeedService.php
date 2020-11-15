<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 27/06/2018
 * Time: 16:33
 */

namespace DynamicFbAds\Services;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Library\DataPuller;
use DynamicFbAds\Library\CustomLibrary;

/**
 * Class CSVFeedService
 *
 * @package DynamicFbAds\Services
 */
class CSVFeedService
{
    private $dataPuller;
    private $type;
    private $subTypes;
    private $data;
    private $vehicles;
    private $id;
    private $csvFileName;

    /**
     * CSVFeedService constructor.
     *
     * @param $id
     * @param $type
     * @param $vehicles
     */
    public function __construct($vehicles)
    {
        $this->id = Pocket::get('currentId');
        $this->type = Pocket::get('currentType');
        $this->subTypes = Pocket::get('currentSubTypes');
        $this->vehicles = $vehicles;
        $this->csvFileName = $this->getCsvFileName();
        $this->setDataPuller();
    }

    public function setDataPuller()
    {
        $class = "DynamicFbAds\Library\DataPuller";
        if (class_exists("DynamicFbAds\Library\CustomLibrary\CustomDataPuller"
                    . $this->id)) {
            $class = "DynamicFbAds\Library\CustomLibrary\CustomDataPuller" . $this->id;
        } elseif (class_exists("DynamicFbAds\Library\CustomLibrary\CustomDataPuller")) {
            $class = "DynamicFbAds\Library\CustomLibrary\CustomDataPuller";
        }
        $this->dataPuller = new $class();
    }
    /**
     * @return string
     */
    public function getCsvFileName()
    {
        return __DIR__."/../../".strtolower(sprintf(
            "%s/%s-%s%s.csv",
            Pocket::get('config')['facebook_api']['csv_folder'],
            $this->id,
            $this->type,
            $this->subTypes
                ? '-'.implode('-', $this->subTypes)
                : ''
        ));
    }
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->csvFileName;
    }

    /**
     * @return bool
     */
    public function writeCsv()
    {
        // return true with message
        if (empty($this->vehicles)) {
            echo "Nothing to write to feed. 0 vehicles found in "
                . Pocket::get('dynamicFbAdsWebsite')['website_name'] . "\n";
            return false;
        }
        $rows = $this->dataPuller->getRows($this->vehicles);
        $outputTitle = $this->type . ($this->subTypes
            ? " + ".implode(" + ", $this->subTypes)
            : "");
        // return true with message
        if (empty($rows)) {
            echo sprintf(
                "Skipped to write to feed. 0 %s vehicles found in %s",
                $outputTitle,
                Pocket::get('dynamicFbAdsWebsite')['website_name']
            )."\n";
            return false;
        }

        // create feed file
        $fp = fopen($this->csvFileName, 'w+');
        if (!$fp) {
            die('Can not create feed file' . $this->csvFileName);
        }

        // Write Header
        fputcsv($fp, array_keys(current($rows)));

        // start pulling data
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        // close file
        fclose($fp);

        print Pocket::get('dynamicFbAdsWebsite')['website_name']
            . " " . $outputTitle ." feed file generated.\n";

        return $this->csvFileName;
    }
}