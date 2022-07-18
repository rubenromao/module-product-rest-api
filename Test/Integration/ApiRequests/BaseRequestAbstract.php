<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Rezolve\APISalesV4\Model\GCS\Config as GcsConfig;
use Rezolve\APISalesV4\Model\OSS\Config;
use Rezolve\APISalesV4\Test\FixtureLoaders\ConfigurationLoader;

abstract class BaseRequestAbstract extends WebapiAbstract
{
    const EXPECTED_TIMESTAMP = '2017-02-15T16:23:55+00:00';
    const EXPECTED_ORDER_ID = '000000001';

    private static $useVirtualProducts = false;
    private static $useConfigurableProducts  = false;
    private static $enableIncludeCartButtonOnListing  = false;
    private static $useMultipleCustomOptionsProducts = false;
    private static $useOutOfStockProducts = false;

    /** @var  TestHelper */
    private $testHelper;

    public static function apiFixtures()
    {
        $useVirtualProducts = self::$useVirtualProducts;
        $useConfigurableProducts = self::$useConfigurableProducts;
        $useMultipleCustomOptionsProducts = self::$useMultipleCustomOptionsProducts;
        $useOutOfStockProducts = self::$useOutOfStockProducts;
        $enableIncludeCartButtonOnListing = self::$enableIncludeCartButtonOnListing;
        require __DIR__ . '/../_files/products_in_category.php';
    }

    public static function apiFixturesRollback()
    {
        $useOutOfStockProducts = self::$useOutOfStockProducts;
        require __DIR__ . '/../_files/products_in_category_rollback.php';
    }

    public static function virtualProductsToCategoryFixtures()
    {
        self::$useVirtualProducts = true;
    }

    public static function virtualProductsToCategoryFixturesRollback()
    {
        self::$useVirtualProducts = false;
    }

    public static function configurableProductsToCategoryFixtures()
    {
        self::$useConfigurableProducts = true;
    }

    public static function configurableProductsToCategoryFixturesRollback()
    {
        self::$useConfigurableProducts = false;
    }

    public static function enableIncludeCartButtonOnListingFixtures()
    {
        self::$enableIncludeCartButtonOnListing = true;
    }

    public static function enableIncludeCartButtonOnListingFixturesRollback()
    {
        self::$enableIncludeCartButtonOnListing = false;
    }

    public static function multipleCustomOptionsProductsFixtures()
    {
        self::$useMultipleCustomOptionsProducts = true;
    }

    public static function multipleCustomOptionsProductsFixturesRollback()
    {
        self::$useMultipleCustomOptionsProducts = false;
    }

    public static function outOfStockFixture()
    {
        self::$useOutOfStockProducts = true;
    }

    public static function outOfStockFixtureRollback()
    {
        self::$useOutOfStockProducts = false;
    }

    public static function gcsFixtures()
    {
        require __DIR__ . '/../_files/enable_gcs.php';
    }

    public static function gcsFixturesRollback()
    {
        require __DIR__ . '/../_files/enable_gcs_rollback.php';
    }

    public static function ossFixtures()
    {
        require __DIR__ . '/../_files/enable_oss.php';
    }

    public static function ossFixturesRollback()
    {
        require __DIR__ . '/../_files/enable_oss_rollback.php';
    }

    public static function performanceFixtures()
    {
        require __DIR__ . '/../_files/performance.php';
    }

    public static function performanceFixturesRollback()
    {
        require __DIR__ . '/../_files/performance_rollback.php';
    }

    public static function loadStores()
    {
        require __DIR__ . '/../_files/pickup_in_store.php';
    }

    public static function loadStoresRollback()
    {
        require __DIR__ . '/../_files/pickup_in_store_rollback.php';
    }

    public static function enableIncludeCartButtonOnListingOnStoreConfiguration()
    {
        require __DIR__ . '/../_files/enable_include_cart_button_on_listing.php';
    }

    public static function enableIncludeCartButtonOnListingOnStoreConfigurationRollback()
    {
        require __DIR__ . '/../_files/enable_include_cart_button_on_listing_rollback.php';
    }

    public static function enableAlternateMaxQtyValues()
    {
        require __DIR__ . '/../_files/alternate_max_qty.php';
    }

    public static function enableAlternateMaxQtyValuesRollback()
    {
        require __DIR__ . '/../_files/alternate_max_qty_rollback.php';
    }

    public static function enableLargeMaxQtyValues()
    {
        require __DIR__ . '/../_files/large_max_qty.php';
    }

    public static function enableLargeMaxQtyValuesRollback()
    {
        require __DIR__ . '/../_files/large_max_qty_rollback.php';
    }

    public static function updateConfig($path, $value, $encrypt = false)
    {
        $configArray = [
            ['path' => $path, 'value' => $value, 'encrypt' => $encrypt]
        ];
        $configLoader = new ConfigurationLoader($configArray);
        $configLoader->loadConfigurationForApi();
    }

    public function setUp()
    {
        parent::setUp();
        $this->testHelper = new TestHelper();
    }

    public function getTestHelper()
    {
        return $this->testHelper;
    }

    public function stripStackTrace($result)
    {
        if (is_object($result) && property_exists($result, 'trace')) {
            unset($result->trace);
        }

        return $result;
    }

    public function getBaseUrl()
    {
        if (defined('TESTS_BASE_URL')) {
            return TESTS_BASE_URL;
        }

        throw new \Exception('The base URL has not been defined');
    }

    public function getProduct($productId)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Product $product */
        $product = $objectManager->create(Product::class);
        $product->load($productId);
        if ($product->getSku() === null) {
            throw new \Exception("Could not find a product with an ID of $productId");
        }

        return $product;
    }

    public function getProductCustomOptions($productId, $title = null)
    {
        $product = $this->getProduct($productId);

        $customOptions = $product->getOptions();

        if (empty($customOptions)) {
            throw new \Exception("Product with ID: $productId, does not have any custom foptions");
        }

        if ($title === null) {
            return $customOptions;
        }

        foreach ($customOptions as $option) {
            if ($option->getTitle() === $title) {
                return $option;
            }
        }

        throw new \Exception("Product with ID: $productId, does not have a custom option with a title of $title");
    }

    public function getCustomOptionValueId($title, ProductCustomOptionInterface $option)
    {
        $values = $option->getValues();
        if (!is_array($values)) {
            throw new \Exception('Option does not have any values');
        }
        foreach ($values as $value) {
            if ($value->getTitle() == $title) {
                return $value->getOptionTypeId();
            }
        }

        throw new \Exception("Could not find an option called $title");
    }

    public function getExpectedTimeStamp()
    {
        return self::EXPECTED_TIMESTAMP;
    }

    public function normaliseTimeStamp($results)
    {
        $testHelper = $this->getTestHelper();
        if (!isset($results['timestamp'])) {
            $testHelper->throwException("No timestamp returned");
        }
        $timeStamp = $results['timestamp'];
        date_default_timezone_set('UTC');

        $dateObject = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $timeStamp);
        if ($dateObject === false) {
            $testHelper->throwException("Time stamp is not in the correct format. Got $timeStamp");
        }

        $recastTimeStamp = date('Y-m-d\TH:i:sP', $dateObject->getTimestamp());
        if ($timeStamp !== $recastTimeStamp) {
            $message = "Time stamp did not recast to itself: started with $timeStamp, cast to $recastTimeStamp";
            $testHelper->throwException($message);
        }

        $results['timestamp'] = $this->getExpectedTimeStamp();

        return $results;
    }

    public function normaliseGcsUrls(array $result, array $toUpdate)
    {
        foreach ($toUpdate as $key) {
            if (!isset($result[$key])) {
                throw new \Exception("Could not find a param of $key in the result");
            }
            $result[$key] = $this->stripGcsQuery($result[$key]);
        }

        return $result;
    }

    public function stripGcsQuery($array)
    {
        if (!is_array($array)) {
            return preg_replace('#\?.*$#', '', $array);
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->stripGcsQuery($value);
            } else {
                $array[$key] = preg_replace('#\?.*$#', '', $value);
            }
        }

        return $array;
    }

    public function getCoreOrderId()
    {
        return time();
    }

    public function getOssConfig()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Product $product */
        $config = $objectManager->create(Config::class);
        return $config;
    }

    public function getGcsConfig()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Product $product */
        $config = $objectManager->create(GcsConfig::class);
        return $config;
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        self::assertJson($expectedJson, $message);
        self::assertJson($actualJson, $message);

        $expected = json_decode($expectedJson);
        $actual   = json_decode($actualJson);

        self::assertEquals($expected, $actual, $message);
    }

    /**
     * Fetch admin token
     *
     * @return String
     */
    public function getAdminToken()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/integration/admin/token",
                'httpMethod'   => Request::HTTP_METHOD_POST
            ]
        ];

        $postData = ["username" => TESTS_WEBSERVICE_USER, "password" => TESTS_WEBSERVICE_PASSWORD];
        $token = $this->_webApiCall($serviceInfo, $postData);
        return $token;
    }

    public function getOrderFromMagentoApi($orderId)
    {
        $adminToken = $this->getAdminToken();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/orders/" . $orderId,
                'httpMethod'   => Request::HTTP_METHOD_GET,
                'token'        => $adminToken
            ]
        ];
        $result = $this->_webApiCall($serviceInfo);
        return $result;
    }
}
