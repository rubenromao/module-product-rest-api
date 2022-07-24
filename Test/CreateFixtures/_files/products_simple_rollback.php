<?php

use Dev\RestApi\Test\FixtureLoaders\ProductLoader;

/**
 * Remove the Products
 */
$productDetails = [];
require __DIR__ . '/_fixtures/products.php';
$productLoader = new ProductLoader($productDetails);
$productLoader->removeProducts();
