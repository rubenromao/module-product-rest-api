<?php

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;

$outProductDetails = [
    [
        'id'                => 401,
        'attributeSetId'    => 4,
        'name'              => 'Out Of Stock Product 401',
        'sku'               => 'out_of_stock_product_401',
        'price'             => 19.99,
        'visibility'        => Visibility::VISIBILITY_BOTH,
        'status'            => Status::STATUS_ENABLED,
        'type'              => Type::TYPE_SIMPLE,
        'websiteIds'        => [1],
        'stockData'         => [
                'qty'         => 0,
                'is_in_stock' => 0,
            ],
        'image'             => '/a/b/abasicimage.png',
        'description'       => 'A product that is out of stock',
        'weight'            => 1,
        'short_description' => 'Out Of Stock',
    ],
    [
        'id'                => 402,
        'attributeSetId'    => 4,
        'name'              => 'Out Of Stock Product 402',
        'sku'               => 'out_of_stock_product_402',
        'price'             => 19.99,
        'visibility'        => Visibility::VISIBILITY_BOTH,
        'status'            => Status::STATUS_ENABLED,
        'type'              => Type::TYPE_SIMPLE,
        'websiteIds'        => [1],
        'stockData'         => [
                'qty'         => 0,
                'is_in_stock' => 0,
            ],
        'image'             => '/a/b/abasicimage.png',
        'description'       => 'A product that is out of stock',
        'weight'            => 1,
        'short_description' => 'Out Of Stock',
    ]
];
