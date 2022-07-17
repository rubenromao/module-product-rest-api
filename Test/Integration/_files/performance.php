<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\AfterLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\CategoryLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\CustomerLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\OrderLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\ProductAttributeLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\ProductLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\TaxLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\TermsAndConditionsLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\WebsiteLoader;

$websiteDetails = [];
require __DIR__ . '/_fixtures/websites.php';
$websiteLoader = new WebsiteLoader($websiteDetails);
$websiteLoader->updateWebsiteCode('merchant_4', 'Test Merchant');

$customerDetails = [];
require __DIR__ . '/_fixtures/customers.php';
$customerLoader = new CustomerLoader($customerDetails);
$customerLoader->createCustomers();

/**
 * Create the product attributes
 */
$productAttributes = [];
require __DIR__ . '/_fixtures/productAttributes.php';
$attributeLoader = new ProductAttributeLoader($productAttributes);
$attributeLoader->createAttributes();

/**
 * Setup the tax rules
 */
$taxDetails = [];
require __DIR__ . '/_fixtures/taxRates.php';
$taxLoader = new TaxLoader($taxDetails);
$taxLoader->createTaxRules();

/**
 * Configure the site to use s3
 */
$s3ConfigArray = [];
require __DIR__ . '/_fixtures/s3Config.php';
$configLoader = new ConfigurationLoader($s3ConfigArray);
$configLoader->loadConfigurationForApi();

/**
 * Create the products
 */
$productDetails = [];
require __DIR__ . '/_fixtures/products.php';
$productLoader = new ProductLoader($productDetails);
$productLoader->createProducts();

/**
 * Create the acts
 */

$acts = [];
require __DIR__ . '/_fixtures/acts.php';
$actsLoader = new ProductLoader($acts);
$actsLoader->createProducts();

/**
 * Create the categories
 */
$largeCategory  = [];
require __DIR__ . '/_fixtures/large_category.php';
$categoryLoader = new CategoryLoader($largeCategory);
$categoryLoader->createCategories();

$orderLoader = new OrderLoader([]);
$orderLoader->createOrders();

/**
 * Create the terms and conditions
 */
$termsAndConditionsLoader = new TermsAndConditionsLoader();
$termsAndConditionsLoader->createTermsAndConditions();

$orderLoader = new OrderLoader([]);
$orderLoader->createOrders();

$enableProfiler = [
    ['path' => 'profiler/general/enable', 'value' => 1]
];
$configLoader = new ConfigurationLoader($enableProfiler);
$configLoader->loadConfigurationForApi();

/**
 * run whatever tasks are needed after loading the fixtures
 */
$afterLoader = new AfterLoader();
$afterLoader->afterLoad();
