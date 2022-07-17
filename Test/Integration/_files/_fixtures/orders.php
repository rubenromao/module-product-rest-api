<?php

$orderDetails = [
    [
        'rezolve_order_id' => '100000001',
        'partner_id' => 1,
        'increment_id' => 100000001,
        'customer'     => [
                'first_name' => 'Grayce',
                'last_name'  => 'Cummings',
                'email'      => 'olson.leta@example.org',
                'street'     => '10667 Noemie Estate 
A Second Street',
                'city'       => 'Arvillamouth',
                'region'     => 'Hipolito Towne',
                'postcode'   => '49583',
                'telephone'  => '1-536-558-2753 x5698',
                'country_id' => 'GB',
            ],
        'items'        => [
                [
                    'sku' => 'tori_tank_indigo_l',
                    'qty' => 4,
                    'tax' => 12,
                ],
            ],
        'state'        => 'complete',
        'currency'     => 'GBP',
        'shipping'     => 10,
        'type'         => 'scan',
        'location'     => [
                'lat'  => -78.597594999999998,
                'long' => -137.232474,
            ],
        'created_at'   => '2016-01-01 15:41:18',
        'payment'   => [
            'additional_data' => [
                ['label' => 'payment_type', 'value' => 'Credit card'],
                ['label' => 'psp', 'value' => 'SPGateway']
            ],
            'type' => [
                'label' => 'SPGateway',
                'code' => 'spgateway',
            ]
        ]
    ],
    [
        'rezolve_order_id' => '100000002',
        'partner_id' => 1,
        'increment_id' => 100000002,
        'customer'     => [
                'first_name' => 'Duane',
                'last_name'  => 'Halvorson',
                'email'      => 'armstrong.kenna@example.com',
                'street'     => '47568 Lavinia Road',
                'city'       => 'Rosamondchester',
                'region'     => 'Pat VonRueden',
                'postcode'   => '31606',
                'telephone'  => '562-616-8762',
                'country_id' => 'AW',
            ],
        'items'        => [
                [
                    'sku' => 'tori_tank_indigo_l',
                    'qty' => 1,
                ],
            ],
        'state'        => 'complete',
        'currency'     => 'LAK',
        'type'         => 'scan',
        'location'     => [
                'lat'  => 67.569982999999993,
                'long' => -175.850763,
            ],
        'created_at'   => '2016-01-05 18:07:33',
        'payment'   => [
            'additional_data' => [
                ['label' => 'payment_type', 'value' => 'Credit card'],
                ['label' => 'psp', 'value' => 'Payone']
            ],
            'type' => [
                'label' => 'Payone',
                'code' => 'payone',
            ]
        ]
    ],
    [
        'rezolve_order_id' => '100000003',
        'partner_id' => 1,
        'increment_id' => 100000003,
        'customer'     => [
                'first_name' => 'Talon',
                'last_name'  => 'Berge',
                'email'      => 'hmarks@example.com',
                'street'     => '96299 Darrion Junctions',
                'city'       => 'North Schuylerhaven',
                'region'     => 'Dr. Kylee Lubowitz',
                'postcode'   => '40227',
                'telephone'  => '552.520.8991 x36829',
                'country_id' => 'GD',
            ],
        'items'        => [
                [
                    'sku' => 'tori_tank_indigo_m',
                    'qty' => 2,
                ],
            ],
        'state'        => 'canceled',
        'currency'     => 'AED',
        'type'         => 'scan',
        'location'     => [
                'lat'  => 54.024625999999998,
                'long' => -14.238258,
            ],
        'created_at'   => '2016-01-08 09:39:40',
        'payment'   => [
            'additional_data' => [
                ['label' => 'payment_type', 'value' => 'Quickpass'],
                ['label' => 'psp', 'value' => 'Union Pay'],
                ['label' => 'transaction_id', 'value' => '1818181818']
            ],
            'type' => [
                'label' => 'Union Pay',
                'code' => 'union_pay',
            ]
        ]
    ]
];
