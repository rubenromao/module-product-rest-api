<?php

use Dev\RestApi\Test\FixtureLoaders\ProductLoader;

/**
 * Create the products
 */
$productDetails = [];
require __DIR__ . '/_fixtures/products.php';
$productLoader = new ProductLoader($productDetails);
$productLoader->createProducts();
