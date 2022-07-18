<?php

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;

$actsAttributeSetId = 4;

$productsWithMultipleCustomOptions = [
    [
        'id'                => 305,
        'attributeSetId'    => $actsAttributeSetId,
        'name'              => 'Multiple Option Values',
        'sku'               => 'custom_302',
        'price'             => 19,
        'visibility'        => Visibility::VISIBILITY_BOTH,
        'status'            => Status::STATUS_ENABLED,
        'type'              => Type::TYPE_SIMPLE,
        'websiteIds'        => [1],
        'stockData'         => [
                'qty'         => 10000,
                'is_in_stock' => 1,
            ],
        'image'             => '/a/b/abasicimage.png',
        'description'       => 'Product Has Multiple Custom Option Values',
        'weight'            => 1,
        'short_description' => 'Act',
        'custom_options'    => [
            [
                'title'      => 'Name',
                'type'       => 'multiple',
                'required'   => '1',
                'sort_order' => '1',
                'price'      => 0,
                'price_type' => 'fixed',
                'values'     => [
                    [
                        'title'      => 'Red',
                        'price'      => 0,
                        'price_type' => 'fixed',
                        'sku'        => '',
                        'sort_order' => 1
                    ],
                    [
                        'title'      => 'Yellow',
                        'price'      => 0,
                        'price_type' => 'fixed',
                        'sku'        => '',
                        'sort_order' => 2
                    ],
                    [
                        'title'      => 'Green',
                        'price'      => 0,
                        'price_type' => 'fixed',
                        'sku'        => '',
                        'sort_order' => 3
                    ]
                ]
            ]
        ]
    ]
];
