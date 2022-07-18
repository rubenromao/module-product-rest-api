<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch3;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class EnsurePaymentExtensionAttributesAreCorrect extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testExtensionAttributesAreCorrect()
    {
        $testHelper = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $requestJson = $this->getRequestJson($orderId);
        $postData = $testHelper->convertJsonToPostData($requestJson);
        $result = $this->makeRequest('test', '4', $postData);
        $order = $this->getOrderFromMagentoApi('1');
        $extensionAttributeJson = $testHelper->convertReturnedDataToJson($order['payment']['extension_attributes'], JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson();
        $this->assertJsonStringEqualsJsonString($expectedJson, $extensionAttributeJson);
    }

    private function getRequestJson($orderId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_UTC;
        return
            <<<JSON
{
    "core_order_id": "$orderId",
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
            {"label": "payment_type", "value": "Quickpass"},
            {"label": "psp", "value": "Union Pay"},
            {"label": "transaction_id", "value": "18181818181818181"}
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

    private function getExpectedJson()
    {
        return <<<JSON
{
    "rezolve_transaction_id": "18181818181818181",
    "rezolve_psp": "Union Pay",
    "rezolve_payment_type": "Quickpass"
}
JSON;
    }
}
