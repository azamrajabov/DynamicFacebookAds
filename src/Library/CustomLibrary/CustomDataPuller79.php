<?php

namespace DynamicFbAds\Library\CustomLibrary;

use DynamicFbAds\Library\Pocket;
use DynamicFbAds\Library\DataPuller;

class CustomDataPuller79 extends CustomDataPuller
{
    public function getVehicleDescription()
    {
        return "Our Sale Price is plus GST Only! No Fees whatsoever on the sale price."
        ." Looking for Upfront Pricing and a Great Experience? We are your store! :) VISIT US TODAY: For more"
        ." information or to book a Test Drive give us a Call or come on down to Sherwood Ford we are located at 2540"
        ." Broadmoor Boulevard, Sherwood Park, Alberta T8H 1B4. 2018 Consumer satisfaction Award from DealerRater."
        ." 2018 Dealer of the Year Ford Alberta.";
    }
    protected function getId()
    {
        return $this->vehicle['VIN'];
    }
}
