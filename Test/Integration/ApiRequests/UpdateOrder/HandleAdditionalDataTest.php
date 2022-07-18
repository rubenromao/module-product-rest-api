<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

use Magento\Framework\Webapi\Rest\Request;

class HandleAdditionalDataTest extends UpdateOrderAbstract
{
    const CREATED_AT_TIMESTAMP_UTC = '2018-03-23T18:29:00.232648Z';

    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/auto_return 1
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testAdditionalData()
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
        $info = $order->getPayment()->getAdditionalInformation();
        $this->assertArrayHasKey('info', $info);
        $this->assertArrayHasKey('data', $info['info']);
        $this->assertEquals([['label' => 'psp', 'value' => 'UpdatedGateway'], ['label' => 'transaction_id', 'value' => '123456789']], $info['info']['data']);
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

    public function getRequestJson($status)
    {
        return <<<JSON
{
    "payment": {
        "status": "$status",
        "data": [
          {
            "value": "UpdatedGateway",
            "label": "psp"
          },
          {
            "value": "123456789",
            "label": "transaction_id"
          }
        ]
    }
}
JSON;
    }

    private function getPlaceOrderJson($coreOrderId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_UTC;
        return
            <<<JSON
{
    "core_order_id": "$coreOrderId",
    "phone_number": "0123456789",
    "partner_id": 14,
    "timestamp": "$timestamp",
    "products": [
        {
            "origin": "string",
            "qty": 2,
            "id": 3,
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
    "payment_method": {
        "billing_address_details": {
            "street_1": "123 Billing Street",
            "street_2": "Billing Square",
            "city": "Westminster",
            "region": "London",
            "country": "United Kingdom",
            "post_code": "LS1 2AB"
        },
        "type": {
            "code": "union_pay",
            "label": "Union Pay"
        },
        "state": "paid",
        "data": [
          {
            "value": "TestGateway",
            "label": "psp"
          }
        ]
    },
    "delivery_method": {
        "shipping_address_details": {
            "street_1": "123 Shipping Street",
            "street_2": "",
            "city": "Leeds",
            "region": "West Yorkshire",
            "country": "United Kingdom",
            "post_code": "LS1 2AB"
        },
        "shipping_method": {
            "carrier_code": "flatrate",
            "method_code": "flatrate",
            "display_name": "Flat Rate Shipping",
            "extension_attributes": []
        }
    },
    "location": {
        "latitude": "4.56",
        "longitude": "-1.23"
    }
}
JSON;
    }

    private function makePlaceOrderRequest($appId, $merchantId, $postData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V4/api/mobile/{$appId}/{$merchantId}/order",
                'httpMethod'   => Request::HTTP_METHOD_POST,
                'token'        => 'test'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, $postData);

        return $this->normaliseTimeStamp($result);
    }
}
