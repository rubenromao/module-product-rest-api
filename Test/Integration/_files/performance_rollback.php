<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\CategoryLoader;

$largeCategory  = [];
require __DIR__ . '/_fixtures/large_category.php';
$categoryLoader = new CategoryLoader($largeCategory);
$categoryLoader->removeCategories();
