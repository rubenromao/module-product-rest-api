<?php

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;

$actsAttributeSetId = 4;

$acts = [
    [
        'id'                => 300,
        'attributeSetId'    => $actsAttributeSetId,
        'name'              => 'A simple Act Product',
        'sku'               => 'act_300',
        'price'             => 0,
        'visibility'        => Visibility::VISIBILITY_BOTH,
        'status'            => Status::STATUS_ENABLED,
        'type'              => Type::TYPE_VIRTUAL,
        'websiteIds'        => [1],
        'stockData'         => [
                'qty'         => 10000,
                'is_in_stock' => 1,
            ],
        'image'             => '/a/b/abasicimage.png',
        'description'       => 'A simple Act',
        'weight'            => 0,
        'short_description' => 'Act',
        'custom_options'    => [
            [
                'title'          => 'Name',
                'type'           => 'full_name',
                'required'       => '1',
                'sort_order'     => '1',
                'price'          => 0,
                'price_type'     => 'fixed'
            ]
        ]
    ],
    [
        'id'                => 301,
        'attributeSetId'    => $actsAttributeSetId,
        'name'              => 'A different Act Product',
        'sku'               => 'act_301',
        'price'             => 0,
        'visibility'        => Visibility::VISIBILITY_BOTH,
        'status'            => Status::STATUS_ENABLED,
        'type'              => Type::TYPE_VIRTUAL,
        'websiteIds'        => [1],
        'stockData'         => [
                'qty'         => 10000,
                'is_in_stock' => 1,
            ],
        'image'             => '/a/b/abasicimage.png',
        'description'       => 'A simple Act with multiple options',
        'weight'            => 0,
        'short_description' => 'Act',
        'custom_options'    => [
            [
                'title'          => 'Name',
                'type'           => 'full_name',
                'required'       => '1',
                'sort_order'     => '1',
                'price'          => 0,
                'price_type'     => 'fixed'
            ],
            [
                'title'          => 'Text field',
                'type'           => 'field',
                'required'       => '1',
                'sort_order'     => '2',
                'price'          => 0,
                'price_type'     => 'fixed'
            ]
        ]
    ]
];
