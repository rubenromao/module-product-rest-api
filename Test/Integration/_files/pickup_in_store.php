<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;
use Rezolve\Storepickup\Test\Rezolve\FixtureLoader\StoreLoader;

$storeDetails = [];
require __DIR__ . '/_fixtures/stores.php';
$stores = new StoreLoader($storeDetails);
$stores->createStores();

$shippingSettings = [
    ['path' => 'carriers/storepickup/active', 'value' => '1'],
    ['path' => 'carriers/storepickup/title', 'value' => 'Pickup In Store'],
    ['path' => 'carriers/storepickup/name', 'value' => 'Pickup In Store'],
    ['path' => 'carriers/storepickup/price', 'value' => '0']
];
$configLoader = new ConfigurationLoader($shippingSettings);
$configLoader->loadConfigurationForApi();
