<?php declare(strict_types=1);

return [
    'web_folder' => '/public/',
    'mysql' => [
        'host' => 'readonly_db_host',
        'user' => 'root',
        'pass' => '',
        'database' => 'products'
    ],
    'mysql_local' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'database' => 'products'
    ],
    'facebook_api' => [
        'app_id' => 'APP_ID',
        'app_secret' => 'APP_SECRET',
        'business_id' => 'BUSINESS_ID',
        'product_catalog_id' => 'PRODUCT CATALOG ID',
        'feed_url_path' => 'http://URL.COM/csv/',
        'folder_file_ids' => 'folderFileIds',
        'csv_folder' => 'csv',
        'access_token' => 'ACCESS_TOKEN'
    ],
    'prices' => [
        'Final_Price' => 'Final Price',
        'InternetPrice' => 'Internet Price',
        'RoundPrice' => 'Round Price',
        'SellingPrice' => 'Selling Price',
        'SpecialsPrice' => 'Specials Price',
        'StickerPrice' => 'Sticker Price',
        'MSRP' => 'MSRP'
    ],
    'vehicleTableFields' => 'vuid as id,
                dealer_id,
                v.DealerID,
                Year,
                Make,
                Model,
                Trim,
                v.VIN,
                Series,
                Final_Price,
                NewUsed,
                seo_url,
                Certified,
                SellingPrice,
                InvoiceAmount,
                BlueBook,
                MSRP,
                Miles,
                MiscPrice1,
                MiscPrice2,
                MiscPrice3,
                InternetPrice,
                NADARetailPrice,
                SpecialsPrice,
                StandardBody as Body,
                ExtColorGeneric as Color,
                ExteriorColor,
                InteriorColor,
                main_image_url,
                VehicleDescription,
                PhotoCount,
                Drivetrain,
                FuelType,
                Transmission,
                Certified,
                CASE WHEN Certified = 1 THEN "certified" ELSE "" END as custom_label_0
                '
    ,
    'fields' => [
        'groupFields' => '',
        'default' => [
            'vehicle_id' => 'id',
            'vin' => 'VIN',
            'make' => 'Make',
            'model' => 'Model',
            'year' => 'Year',
            'body_style' => 'Body',
            'description' => 'VehicleDescription',
            'exterior_color' => 'ExteriorColor',
            'image[0].tag[0]' => 'mainImageTag',
            'image[0].url' => 'image[0].url',
            'mileage.value' => 'Miles',
            'mileage.unit' => 'MileageUnit',
            'url' => 'Url',
            'title' => 'Title',
            'price' => 'Price',
            'state_of_vehicle' => 'NewUsed',
            'address' => 'dealer_address',
            //'addr1' => 'dealer_address_2',
            'city' => 'dealer_city',
            'region' => 'dealer_state_full',
            'country' => 'DealerCountry',
            'longitude' => 'dealer_lng',
            'latitude' => 'dealer_lat',
            'transmission' => 'Transmission',
            'drivetrain' => 'Drivetrain',
            'fuel_type' => 'FuelType',
            'trim' => 'Trim',
            'tag' => 'Tag',
            'interior_color' => 'InteriorColor',
            'condition' => 'condition',
            'sale_price' => 'SalePrice',
            'availability' => 'Availability',
            'dealer_id' => 'dealer_id',
            'dealer_name' => 'dealer_name',
            'dealer_phone' => 'dealer_main_phone',
            'postal_code' => 'dealer_zip',
            'custom_label_0' => 'CustomLabel0',
        ],
    ],
    'adjusts' => [
        'body_style' => [
            'Coupe' => [
                'Coupe',
                '2D Hatchback',
                '2D Sport Utility',
                '2dr Car'],
            'SUV' => [
                'Sport Utility',
                'SUV',
            ],
            'Convertible' => ['Convertible'],
            'Hatchback' => ['Hatchback'],
            'Sedan' => [
                'Sedan',
                'Wagon',
                '4 Door',
                '4dr Car',
            ],
            'Truck' => [
                'Cab',
                'Crew',
                'Pickup',
                'Truck',
                'Cutaway'
            ],
            'Minivan' => [
                'Mini-van',
                'Minivan'],
            'Van' => ['Van'],
            'Roadster' => ['Roadster'],
            '4dr Crossover' => ['4dr Crossover']
        ]
    ],
    'feedUrl' => 'http://semtools.dealerfire.com/dynamicfbads/csv/',
    'emails' => 'arajabov@dealersocket.com',
    'environment' => 'development'
];
