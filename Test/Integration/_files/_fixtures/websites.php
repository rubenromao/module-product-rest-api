<?php

use Rezolve\APISalesV4\Model\Stores\Merchants;

$websiteDetails = [
    [
        'name'             => 'Clean Merchant 10900',
        'code'             => Merchants::WEBSITE_PREFIX . 4,
        'default_group_id' => 1,
        'is_default'       => 1,
        'root_category'    => 2,
        'stores'           => [
            [
                'name'       => 'Clean Merchant 10900 Store',
                'code'       => Merchants::WEBSITE_PREFIX . '4_store',
                'sort_order' => 10,
                'is_active'  => 1
            ]
        ],
        'config'           => [
            'rezolve/merchants/logo'    => 'websites/1/logo.jpg',
            'rezolve/merchants/banner'  => 'websites/1/findstore.jpg',
            'rezolve/merchants/tagline' => 'Clean Merchant 10900',
        ]
    ],
    [
        'name'             => 'Clean Merchant 80768',
        'code'             => Merchants::WEBSITE_PREFIX . 5,
        'default_group_id' => 1,
        'is_default'       => 1,
        'root_category'    => 5,
        'stores'           => [
            [
                'name'       => 'Clean Merchant 80768',
                'code'       => Merchants::WEBSITE_PREFIX . '5_store',
                'sort_order' => 10,
                'is_active'  => 1
            ]
        ],
        'config'           => [
            'rezolve/merchants/logo'    => 'websites/2/logo.jpg',
            'rezolve/merchants/banner'  => 'websites/2/findstore.jpg',
            'rezolve/merchants/tagline' => 'Clean Merchant 80768',
        ]
    ],
    [
        'name'             => 'Clean Merchant 28744',
        'code'             => Merchants::WEBSITE_PREFIX . 6,
        'default_group_id' => 1,
        'is_default'       => 1,
        'root_category'    => 10,
        'stores'           => [
            [
                'name'       => 'Clean Merchant 28744',
                'code'       => Merchants::WEBSITE_PREFIX . '6_store',
                'sort_order' => 10,
                'is_active'  => 1
            ]
        ],
        'config'           => [
            'rezolve/merchants/logo'    => 'websites/3/logo.jpg',
            'rezolve/merchants/banner'  => 'websites/3/kohls-store_flickr-user-mike-mozart_large.jpg',
            'rezolve/merchants/tagline' => 'Kohl\'s',
        ]
    ]
];
