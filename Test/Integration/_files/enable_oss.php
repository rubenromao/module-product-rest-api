<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$ossConfigArray = [];
require __DIR__ . '/_fixtures/ossConfig.php';
$configLoader   = new ConfigurationLoader($ossConfigArray);
$configLoader->loadConfigurationForApi();
