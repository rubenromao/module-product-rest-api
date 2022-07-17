<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch1;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class HandleOutOfStockProductsTest extends PlaceOrderAbstract
{

    /**
     * @magentoApiDataFixture outOfStockFixture
     * @magentoApiDataFixture apiFixtures
     */
    public function testOutOfStockWorks()
    {
        $testHelper   = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->sendRequestThatShouldError('chase', '4', $postData, 400);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson();

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getRequestJson($orderId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_UTC;
        return <<<JSON
{
    "core_order_id": "$orderId",
    "phone_number": "0123456789",
    "partner_id": 12,
    "timestamp": "$timestamp",
    "products": [
        {
            "qty": 7,
            "id": "401",
            "configurable_options": [

            ],
            "custom_options": [

            ]
        },
        {
            "qty": 5,
            "id": "402",
            "configurable_options": [

            ],
            "custom_options": [

            ]
        },
        {
            "qty": 3,
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
    "payment_method": {
        "billing_address_details": {
            "street_1": "123 Main Street",
            "street_2": "",
            "city": "Leeds",
            "region": "West Yorkshire",
            "country": "United Kingdom",
            "post_code": "LS1 2AB"
        },
        "type": {
            "code": "union_pay",
            "label": "Union Pay"
        },
        "state": "paid",
        "data": []
    },
    "delivery_method": {
        "shipping_address_details":{
            "street_1": "123 Main Street",
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

    private function getExpectedJson()
    {
        return <<<JSON
{
    "message": "We don't have as many \"Out Of Stock Product 401\" as you requested.",
     "success": false,
    "errors": "",
    "type": "OutOfStockException",
    "code": 400
}
JSON;
    }
}
