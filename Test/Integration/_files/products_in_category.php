<?php
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Rezolve\APISalesV4\Test\FixtureLoaders\AfterLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\CategoryLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\CustomerLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\OrderLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\ProductAttributeLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\ProductLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\TaxLoader;
use Rezolve\APISalesV4\Test\FixtureLoaders\WebsiteLoader;

/*$rootCategories = [];
require __DIR__ . '/_fixtures/rootCategory.php';
$categoryLoader = new CategoryLoader($rootCategories);
$categoryLoader->createCategories();*/

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
if ($useMultipleCustomOptionsProducts) {
    $productsWithMultipleCustomOptions = [];
    require __DIR__ . '/_fixtures/multipleCustomOptions.php';
    $productDetails = array_merge($productDetails, $productsWithMultipleCustomOptions);
}
$productLoader = new ProductLoader($productDetails);
$productLoader->createProducts();

if ($useOutOfStockProducts) {
    $outProductDetails = [];
    require __DIR__ . '/_fixtures/outOfStockProducts.php';
    $outProductLoader = new ProductLoader($outProductDetails);
    $outProductLoader->createProducts();

    $objectManager = Bootstrap::getObjectManager();
    /** @var StockRegistryInterface $stock */
    $stock     = $objectManager->get(StockRegistryInterface::class);
    $stockItem = $stock->getStockItemBySku('tori_tank');
    $stockItem->setQty(0);
    $stock->updateStockItemBySku('tori_tank', $stockItem);
}

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
$categoryData = [];
require __DIR__ . '/_fixtures/categories.php';
if ($useVirtualProducts) {
    $categoryWithVirtualProductsData = [];
    require __DIR__ . '/_fixtures/categories_with_virtual_products.php';
    $categoryData = array_merge($categoryData, $categoryWithVirtualProductsData);
}
if ($useConfigurableProducts) {
    $categoryWithConfigurableProductsData = [];
    require __DIR__ . '/_fixtures/categories_with_configurable_products.php';
    $categoryData = array_merge($categoryData, $categoryWithConfigurableProductsData);
}

if ($enableIncludeCartButtonOnListing) {
    foreach ($categoryData as &$category) {
        if ($category['id'] == 70 || $category['id'] == 102) {
            $category['include_cart_button_on_listing'] = 1;
        }
        if ($category['id'] == 101) {
            $category['include_cart_button_on_listing'] = 0;
        }
    }
}

$categoryLoader = new CategoryLoader($categoryData);
$categoryLoader->createCategories();

$orderLoader = new OrderLoader([]);
$orderLoader->createOrders();

$orderLoader = new OrderLoader([]);
$orderLoader->createOrders();

/**
 * run whatever tasks are needed after loading the fixtures
 */
$afterLoader = new AfterLoader();
$afterLoader->afterLoad();
