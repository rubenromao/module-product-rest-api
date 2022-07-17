<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$gcsConfigArray = [];
require __DIR__ . '/_fixtures/gcsConfig.php';
$configLoader   = new ConfigurationLoader($gcsConfigArray);
$configLoader->loadConfigurationForApi();
