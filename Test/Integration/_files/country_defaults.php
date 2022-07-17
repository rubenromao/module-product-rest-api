<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$countrySettings = [
    ['path' => 'general/country/default', 'value' => 'CN'],
    ['path' => 'general/region/state_required', 'value' => 'AF']

];
$configLoader   = new ConfigurationLoader($countrySettings);
$configLoader->loadConfigurationForApi();
