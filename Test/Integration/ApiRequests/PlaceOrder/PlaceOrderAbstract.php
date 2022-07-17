<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Rezolve\APISalesV4\Test\Integration\ApiRequests\BaseRequestAbstract;

abstract class PlaceOrderAbstract extends BaseRequestAbstract
{
    const CREATED_AT_TIMESTAMP_UTC = '2018-03-23T18:29:00.232648Z';
    const CREATED_AT_TIMESTAMP_RESPONSE = '2018-03-23T18:29:00+00:00';
    const CREATED_AT_TIMESTAMP_EXPECTED_UTC = '2018-03-23 18:29:00';
    const CREATED_AT_TIMESTAMP_EXPECTED_PST = 'March 23, 2018 at 11:29:00 AM PDT';

    public static function forceCityNotRequired()
    {
        BaseRequestAbstract::updateConfig('rezolve_orders/address_fields/is_city_required', 'false');
    }

    public static function forceCityNotRequiredRollback()
    {
        return;
    }

    public static function tableRates()
    {
        require __DIR__ . '/../../_files/table_rates.php';
    }

    public static function tableRatesRollback()
    {
        require __DIR__ . '/../../_files/table_rates_rollback.php';
    }

    public static function freeShipping()
    {
        require __DIR__ . '/../../_files/free_shipping.php';
    }

    public static function freeShippingRollback()
    {
        require __DIR__ . '/../../_files/free_shipping_rollback.php';
    }

    public static function loadCountryDefaults()
    {
        require __DIR__ . '/../../_files/country_defaults.php';
    }

    public static function loadCountryDefaultsRollback()
    {
        require __DIR__ . '/../../_files/country_defaults_rollback.php';
    }

    public static function addNewDefaultNewStatus()
    {
        require __DIR__ . '/../../_files/add_new_default_new_status.php';
    }

    public static function addNewDefaultNewStatusRollback()
    {
        require __DIR__ . '/../../_files/add_new_default_new_status_rollback.php';
    }

    public function makeRequest($appId, $merchantId, $postData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V4/api/mobile/{$appId}/{$merchantId}/order",
                'httpMethod'   => Request::HTTP_METHOD_POST,
                'token'        => 'test'
            ]
        ];
        return $this->_webApiCall($serviceInfo, $postData);
    }

    public function sendRequestThatShouldError($appId, $merchantId, $postData, $expectedResponseCode)
    {
        try {
            $result = $this->makeRequest($appId, $merchantId, $postData);
            $code   = 200;
        } catch (\Exception $exception) {
            $result = json_decode($exception->getMessage());
            $code   = $exception->getCode();
        }
        $this->stripStackTrace($result);
        $this->assertEquals($expectedResponseCode, $code);

        return $result;
    }

    public function deadgetExpectedJson($status = 'pending_payment')
    {
        $expectedTimestamp   = self::EXPECTED_TIMESTAMP;
        $expectedIncrementId = self::EXPECTED_ORDER_ID;

        return <<<JSON
{
    "order_id": "$expectedIncrementId",
    "order_status": "$status",
    "timestamp": "$expectedTimestamp"
}
JSON;
    }

    /**
     * @param string $incrementNumber
     *
     * @return Order
     */
    public function getOrder($incrementNumber = self::EXPECTED_ORDER_ID)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Order $order */
        $order = $objectManager->create(Order::class);
        $order->loadByIncrementId($incrementNumber);

        return $order;
    }
}
