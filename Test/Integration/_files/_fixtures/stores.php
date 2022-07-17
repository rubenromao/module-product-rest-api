<?php

$storeDetails = [
    [
        'storelocator_id'       => 1,
        'store_parent_id'       => 1,
        'storename'             => 'Test Store 1',
        'storeemail'            => 'test.store@example.com',
        'url_key'               => 'test',
        'description'           => '<p>A test description</p>',
        'website_url'           => 'https://www.example.com',
        'facebook_url'          => 'https://facebook.com/example',
        'twitter_url'           => 'https://twitter.com/example',
        'is_active'             => '1',
        'address'               => '123 Main Street\\n456 Second Street',
        'city'                  => 'Leeds',
        'country'               => 'GB',
        'region_id'             => null,
        'state'                 => 'West Yorkshire',
        'zipcode'               => 'LS1 2AB',
        'phone_frontend_status' => '1',
        'longitude'             => '-2.23',
        'latitude'              => '5.56',
        'store_id'              => '0',
        'meta_title'            => '',
        'meta_keywords'         => '',
        'meta_description'      => '',
        'telephone'             => '0123456789:9876542310',
        'conditions_serialized' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":true,"is_value_processed":null,"aggregator":"all"}',
        'storetime'             => '[{"days":"Monday","day_status":"1","open_hour":"04","open_minute":"00","close_hour":"14","close_minute":"00","delete":""}]',
        'product_ids'           => '1,2,3,4,5,7,100,101,102,6,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209',
    ],
    [
        'storelocator_id'       => 2,
        'storename'             => 'Test Store 2',
        'url_key'               => 'test-store-2',
        'description'           => '<p>A different store</p>',
        'website_url'           => null,
        'facebook_url'          => null,
        'twitter_url'           => null,
        'address'               => '987 Grand Plaza',
        'city'                  => 'New York',
        'state'                 => null,
        'country'               => 'US',
        'zipcode'               => '012345',
        'longitude'             => 7.56,
        'latitude'              => 2.54,
        'phone_frontend_status' => 0,
        'telephone'             => '987654321',
        'storeimage'            => '/2/0/2018-03-16-111347_237x259_scrot.png',
        'storetime'             => null,
        'meta_title'            => null,
        'meta_keywords'         => null,
        'meta_description'      => null,
        'is_active'             => 1,
        'region_id'             => 670,
        'conditions_serialized' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":true,"is_value_processed":null,"aggregator":"all"}',
        'product_ids'           => '1,2,3,4,5,7,100,101,102,6,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209',
        'store_parent_id'       => 2,
        'store_id'              => 0,
        'storeemail'            => 'test2@example.com',
    ],
    [
        'storelocator_id'       => 20,
        'store_parent_id'       => 20,
        'storename'             => 'Disabled Store',
        'storeemail'            => 'disabled.store@example.com',
        'url_key'               => 'test',
        'description'           => '<p>A test description</p>',
        'website_url'           => 'https://www.example.com/disabled',
        'facebook_url'          => 'https://facebook.com/example.disabled',
        'twitter_url'           => 'https://twitter.com/example.disabled',
        'is_active'             => '0',
        'address'               => '147 Disabled Drive\\n456 Second Street',
        'city'                  => 'London',
        'country'               => 'GB',
        'region_id'             => null,
        'state'                 => 'London',
        'zipcode'               => 'SW1 2AB',
        'phone_frontend_status' => '1',
        'longitude'             => '-1.23',
        'latitude'              => '4.56',
        'store_id'              => '0',
        'meta_title'            => '',
        'meta_keywords'         => '',
        'meta_description'      => '',
        'telephone'             => '0123456789:9876542310',
        'conditions_serialized' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":true,"is_value_processed":null,"aggregator":"all"}',
        'storetime'             => '[{"days":"Monday","day_status":"1","open_hour":"04","open_minute":"00","close_hour":"14","close_minute":"00","delete":""}]',
        'product_ids'           => '1,2,3,4,5,7,100,101,102,6,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209',
    ]
];
