<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;
use Rezolve\Storepickup\Test\Rezolve\FixtureLoader\StoreLoader;

$storeDetails = [];
require __DIR__ . '/_fixtures/stores.php';
$stores = new StoreLoader($storeDetails);
$stores->removeStores();

$shippingSettings = [
    ['path' => 'carriers/storepickup/active', 'value' => '0']
];
$configLoader = new ConfigurationLoader($shippingSettings);
$configLoader->loadConfigurationForApi();
