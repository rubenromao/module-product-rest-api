<?php

use Dev\RestApi\Test\FixtureLoaders\AfterLoader;
use Dev\RestApi\Test\FixtureLoaders\ProductLoader;

/**
 * Create the products
 */
$productDetails = [];
require __DIR__ . '/_fixtures/products.php';
$productLoader = new ProductLoader($productDetails);
$productLoader->createProducts();

/**
 * run whatever tasks are needed after loading the fixtures
 */
$afterLoader = new AfterLoader();
$afterLoader->afterLoad();
