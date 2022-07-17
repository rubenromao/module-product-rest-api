<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$maxQtyConfigArray = [];

$maxQtyConfigArray = [
    ['path' => 'cataloginventory/item_options/max_sale_qty', 'value' => 20]
];

$configLoader   = new ConfigurationLoader($maxQtyConfigArray);
$configLoader->loadConfigurationForApi();
