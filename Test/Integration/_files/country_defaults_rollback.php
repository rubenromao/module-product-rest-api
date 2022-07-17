<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$countrySettings = [
    ['path' => 'general/country/default', 'value' => 'US'],
];
$configLoader   = new ConfigurationLoader($countrySettings);
$configLoader->loadConfigurationForApi();
