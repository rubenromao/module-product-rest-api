<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$shippingConfig = [
    ['path' => 'carriers/flatrate/active', 'value' => '0'],
    ['path' => 'carriers/tablerate/active', 'value' => '1'],
    ['path' => 'carriers/tablerate/title', 'value' => 'Best Way'],
    ['path' => 'carriers/tablerate/name', 'value' => 'Table Rate'],
    ['path' => 'carriers/tablerate/condition_name', 'value' => 'package_weight'],
    ['path' => 'carriers/tablerate/include_virtual_price', 'value' => '1'],
    ['path' => 'carriers/tablerate/handling_type', 'value' => 'F'],
    ['path' => 'carriers/tablerate/handling_fee', 'value' => 'NULL'],
    ['path' => 'carriers/tablerate/specificerrmsg', 'value' => 'This shipping method is not available'],
    ['path' => 'carriers/tablerate/sallowspecific', 'value' => '0'],
    ['path' => 'carriers/tablerate/specificcountry', 'value' => 'NULL'],
    ['path' => 'carriers/tablerate/showmethod', 'value' => '0'],
    ['path' => 'carriers/tablerate/sort_order', 'value' => 'NULL'],
];
$configLoader   = new ConfigurationLoader($shippingConfig);
$configLoader->loadConfigurationForApi();

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Framework\App\ResourceConnection $connection */
$connection = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);

$columns = [
    'website_id',
    'dest_country_id',
    'dest_region_id',
    'dest_zip',
    'condition_name',
    'condition_value',
    'price',
    'cost'
];

$table = $connection->getTableName('shipping_tablerate');

$data = [
    ['1', 'TW', '0', '*', 'package_weight', '0.0000', '12.9900', '0.0000'],
    ['1', 'TW', '0', '*', 'package_weight', '1000.0000', '5.0000', '0.0000'],
    ['1', 'TW', '0', '*', 'package_weight', '2000.0000', '0.0000', '0.0000'],
    ['1', 'GB', '0', '*', 'package_weight', '0.0000', '8.9900', '0.0000'],
];

$connection->getConnection()->insertArray($table, $columns, $data);
