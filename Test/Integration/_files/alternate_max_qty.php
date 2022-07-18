<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$maxQtyConfigArray = [];

require __DIR__ . '/_fixtures/maxQty.php';

$configLoader   = new ConfigurationLoader($maxQtyConfigArray);
$configLoader->loadConfigurationForApi();
