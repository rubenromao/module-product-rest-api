<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\CategoryLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\CustomerLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\OrderLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\ProductLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\TaxLoader;

$orderDetails = [];
$orderLoader = new OrderLoader($orderDetails);
$orderLoader->removeOrders();

/**
 * Remove the Products
 */
$productDetails = [];
require __DIR__ . '/_fixtures/products.php';
$productLoader = new ProductLoader($productDetails);
$productLoader->removeProducts();

$productsWithMultipleCustomOptions = [];
require __DIR__ . '/_fixtures/multipleCustomOptions.php';
$multipleCustomOptionsProductLoader = new ProductLoader($productsWithMultipleCustomOptions);
$multipleCustomOptionsProductLoader->removeProducts();

$outProductDetails = [];
require __DIR__ . '/_fixtures/outOfStockProducts.php';
$outProductLoader = new ProductLoader($outProductDetails);
$outProductLoader->removeProducts();

$acts = [];
require __DIR__ . '/_fixtures/acts.php';
$actsLoader = new ProductLoader($acts);
$actsLoader->removeProducts();

$productLoader->resetCatalogProductAutoIncrements();

$customerDetails = [];
require __DIR__ . '/_fixtures/customers.php';
$customerLoader = new CustomerLoader($customerDetails);
$customerLoader->removeCustomers();

/**
 * Remove the categories
 */
$categoryData = [];
require __DIR__ . '/_fixtures/categories.php';

$categoryWithVirtualProductsData = [];
require __DIR__ . '/_fixtures/categories_with_virtual_products.php';
$categoryData = array_merge($categoryData, $categoryWithVirtualProductsData);

$categoryLoader = new CategoryLoader($categoryData);
$categoryLoader->removeCategories();

/**
 * Remove the tax rules
 */
$taxDetails = [];
require __DIR__ . '/_fixtures/taxRates.php';
$taxLoader = new TaxLoader($taxDetails);
$taxLoader->removeTaxRules();
