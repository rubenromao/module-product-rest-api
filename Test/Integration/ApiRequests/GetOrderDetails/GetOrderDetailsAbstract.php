<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\GetOrderDetails;

use Magento\Framework\Webapi\Rest\Request;
use Rezolve\APISalesV4\Test\Integration\ApiRequests\BaseRequestAbstract;

abstract class GetOrderDetailsAbstract extends BaseRequestAbstract
{
    public static function orderFixtures()
    {
        require __DIR__ . '/../../_files/completed_orders.php';
    }

    public static function orderFixturesRollback()
    {
        require __DIR__ . '/../../_files/completed_orders_rollback.php';
    }

    public function makeRequest($appId, $merchantId, $orderId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V4/api/mobile/{$appId}/{$merchantId}/order/${orderId}",
                'httpMethod'   => Request::HTTP_METHOD_GET,
                'token'        => 'test'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo);

        return $this->normaliseTimeStamp($result);
    }

    public function sendRequestThatShouldError($appId, $merchantId, $orderId, $expectedResponseCode)
    {
        try {
            $result = $this->makeRequest($appId, $merchantId, $orderId);
            $code   = 200;
        } catch (\Exception $exception) {
            $result = json_decode($exception->getMessage());
            $code   = $exception->getCode();
        }
        $this->stripStackTrace($result);
        $this->assertEquals($expectedResponseCode, $code);

        return $result;
    }
}
