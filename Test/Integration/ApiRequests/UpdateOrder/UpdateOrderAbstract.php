<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Rezolve\APISalesV4\Test\Integration\ApiRequests\BaseRequestAbstract;

abstract class UpdateOrderAbstract extends BaseRequestAbstract
{
    public static function orderFixtures()
    {
        require __DIR__ . '/../../_files/orders.php';
    }

    public static function orderFixturesRollback()
    {
        require __DIR__ . '/../../_files/orders_rollback.php';
    }

    public static function addNewDefaultClosedStatus()
    {
        require __DIR__ . '/../../_files/add_new_default_closed_status.php';
    }

    public static function addNewDefaultClosedStatusRollback()
    {
        require __DIR__ . '/../../_files/add_new_default_closed_status_rollback.php';
    }

    public function makeRequest($appId, $merchantId, $orderId, $postData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V4/api/mobile/{$appId}/{$merchantId}/order/$orderId",
                'httpMethod'   => Request::HTTP_METHOD_POST,
                'token'        => 'test'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, $postData);

        return $this->normaliseTimeStamp($result);
    }

    public function sendRequestThatShouldError($appId, $merchantId, $orderId, $postData, $expectedResponseCode)
    {
        try {
            $result = $this->makeRequest($appId, $merchantId, $orderId, $postData);
            $code   = 200;
        } catch (\Exception $exception) {
            $result = json_decode($exception->getMessage());
            $code   = $exception->getCode();
        }
        $this->stripStackTrace($result);
        $this->assertEquals($expectedResponseCode, $code);

        return $result;
    }

    public function getExpectedJson($status, $expectedIncrementId)
    {
        $expectedTimestamp   = self::EXPECTED_TIMESTAMP;

        return <<<JSON
{
    "order_id": "$expectedIncrementId",
    "order_status": "$status",
    "timestamp": "$expectedTimestamp"
}
JSON;
    }

    public function getRequestJson($status)
    {
        return <<<JSON
{
    "payment": {
        "status": "$status"
    }
}
JSON;
    }

    public function getPendingOrderID()
    {
        return '100000001';
    }

    public function getCompleteOrderId()
    {
        return '100000002';
    }

    public function getCancelledOrderId()
    {
        return '100000003';
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
