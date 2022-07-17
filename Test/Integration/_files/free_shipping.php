<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$shippingConfig = [
    ['path' => 'carriers/flatrate/active', 'value' => '1'],
    ['path' => 'carriers/freeshipping/active', 'value' => '1']
];
$configLoader   = new ConfigurationLoader($shippingConfig);
$configLoader->loadConfigurationForApi();
