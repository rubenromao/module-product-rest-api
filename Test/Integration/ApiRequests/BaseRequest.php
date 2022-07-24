<?php

namespace Dev\RestApi\Test\Integration\ApiRequests;

use Magento\TestFramework\TestCase\WebapiAbstract;

abstract class BaseRequest extends WebapiAbstract
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
}
