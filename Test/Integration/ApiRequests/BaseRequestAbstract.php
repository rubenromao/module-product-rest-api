<?php

namespace Dev\RestApi\Test\Integration\ApiRequests;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Webapi\Rest\Request;

abstract class BaseRequestAbstract extends WebapiAbstract
{
    /** @var  TestHelper */
    private $testHelper;

    public static function apiFixtures()
    {
        require __DIR__ . '/../_files/products_simple.php';
    }

    public static function apiFixturesRollback()
    {
        require __DIR__ . '/../_files/products_simple_rollback.php';
    }

    public function setUp(): void
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

    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = ''): void
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
