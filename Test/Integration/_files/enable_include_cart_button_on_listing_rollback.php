<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

$config = [['path' => 'app_templates/category/include_cart_button_on_listing', 'value' => 0]];
$configLoader   = new ConfigurationLoader($config);
$configLoader->loadConfigurationForApi();
