<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

use Magento\Framework\Webapi\Rest\Request;

class CancelOrderTest extends UpdateOrderAbstract
{
    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/auto_return 1
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testCancelOrder()
    {
        $testHelper     = $this->getTestHelper();
        $requestJson    = $this->getRequestJson('cancelled');
        $pendingOrderId = $this->createOrder();
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $pendingOrderId, $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson('closed', $pendingOrderId);

        $this->assertEquals($expectedJson, $returnedJson);

        $order = $this->getOrder();
        $allItems = $order->getAllItems();
        $this->assertEquals(1, count($allItems));
        foreach ($allItems as $item) {
            $product        = $item->getProduct();
            $stockItem      = $product->getExtensionAttributes()->getStockItem();
            $stockLevel     = $stockItem->getQty();
            $this->assertEquals(10000, $stockLevel);
        }
    }

    private function checkStockLevel($productId, $expectedStockLevel)
    {
        $product        = $this->getProduct($productId);
        $stockItem      = $product->getExtensionAttributes()->getStockItem();
        $stockLevel     = $stockItem->getQty();
        $this->assertEquals($expectedStockLevel, $stockLevel);
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
