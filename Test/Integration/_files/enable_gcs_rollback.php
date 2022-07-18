<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$gcsConfigArray = [
    ['path' => 'rezolve/cdn/backend', 'value' => 'S3']
];
$configLoader   = new ConfigurationLoader($gcsConfigArray);
$configLoader->loadConfigurationForApi();
