<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

class NormalisedOrderStatusTest extends UpdateOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     * @magentoApiDataFixture addNewDefaultClosedStatus
     */
    public function testNormalisedOrderStatus()
    {
        $testHelper     = $this->getTestHelper();
        $requestJson    = $this->getRequestJson('cancelled');
        $pendingOrderId = $this->createOrder();
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $pendingOrderId, $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson('closed', $pendingOrderId);
        $this->assertEquals($expectedJson, $returnedJson);

        // Load the order and verify that it's status is the custom one.
        $objectManager = Bootstrap::getObjectManager();
        /** @var OrderRepositoryInterface $orderRepostiory */
        $searchCriteriaBuilder = $objectManager->get('Magento\Framework\Api\SearchCriteriaBuilder');
        $searchCriteria = $searchCriteriaBuilder->addFilter('rezolve_order_id', $pendingOrderId)->create();
        $orderRepostiory = $objectManager->create(OrderRepositoryInterface::class);
        /** @var OrderSearchResultInterface $orders */
        $orders = $orderRepostiory->getList($searchCriteria);
        // There should only be 1 order which matches.
        $this->assertEquals(1, $orders->getTotalCount());

        foreach ($orders as $order) {
            $this->assertEquals('custom_status_in_closed_state', $order->getStatus());
        }
    }

    private function createOrder()
    {
        $testHelper  = $this->getTestHelper();
        $requestJson = $this->getPlaceOrderJson($this->getCoreOrderId());
        $postData    = $testHelper->convertJsonToPostData($requestJson);
        $result      = $this->makePlaceOrderRequest('chase', '4', $postData);
        $this->assertTrue(isset($result['order_id']));

        return $result['order_id'];
    }

    private function getPlaceOrderJson($coreOrderId)
    {
        return
            <<<JSON
{
    "core_order_id": "$coreOrderId",
    "phone_number": "(012) 345-6789",
    "partner_id": 10,

    "products": [
        {
            "qty": 2,
            "id": "3",
            "configurable_options": [

            ],
            "custom_options": [

            ]
        }
    ],
    "customer_details": {
        "email_address": "test_order@example.com",
        "customer_identifier": "any_unique_random_string",
        "first_name": "John",
        "last_name": "Smith",
        "has_loyalty_card": 1,
        "loyalty_card_number": "123456789"
    },
    "billing_address_details": {
        "street_1": "123 Main Street",
        "street_2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
        "country": "United Kingdom",
        "post_code": "LS1 2AB"
    },
    "shipping_address_details": {
        "street_1": "123 Main Street",
        "street_2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
        "country": "United Kingdom",
        "post_code": "LS1 2AB"
    }
}
JSON;
    }

    private function makePlaceOrderRequest($appId, $merchantId, $postData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/api/mobile/{$appId}/{$merchantId}/order",
                'httpMethod'   => Request::HTTP_METHOD_POST,
                'token'        => 'test'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, $postData);

        return $this->normaliseTimeStamp($result);
    }
}
