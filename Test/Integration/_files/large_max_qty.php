<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$maxQtyConfigArray = [];

$maxQtyConfigArray = [
    ['path' => 'cataloginventory/item_options/max_sale_qty', 'value' => 200]
];

$configLoader   = new ConfigurationLoader($maxQtyConfigArray);
$configLoader->loadConfigurationForApi();
