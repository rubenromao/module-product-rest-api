<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$ossConfigArray = [
    ['path' => 'rezolve/cdn/backend', 'value' => 'S3']
];
$configLoader   = new ConfigurationLoader($ossConfigArray);
$configLoader->loadConfigurationForApi();
