<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$shippingConfig = [
    ['path' => 'carriers/flatrate/active', 'value' => '1'],
    ['path' => 'carriers/tablerate/active', 'value' => '0'],
];
$configLoader   = new ConfigurationLoader($shippingConfig);
$configLoader->loadConfigurationForApi();

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Framework\App\ResourceConnection $connection */
$connection = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
$table = $connection->getTableName('shipping_tablerate');
$connection->getConnection()->truncateTable($table);
