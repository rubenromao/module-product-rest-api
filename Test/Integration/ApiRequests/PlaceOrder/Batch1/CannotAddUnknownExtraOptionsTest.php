<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch1;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class CannotAddUnknownExtraOptionsTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testGetBasketCall()
    {
        $testHelper   = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $customOption = $this->getProductCustomOptions(300, 'Name');
        $optionId     = $customOption->getOptionId();
        $requestJson  = $this->getRequestJson($orderId, $optionId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->sendRequestThatShouldError('chase', '4', $postData, 400);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getErrorJson();

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getRequestJson($orderId, $optionId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_UTC;
        return
            <<<JSON
{
    "core_order_id": "$orderId",
    "phone_number": "0123456789",
    "partner_id": 12,
    "timestamp": "$timestamp",
    "products": [
        {
            "qty": 1,
            "id": "300",
            "configurable_options": [

            ],
            "custom_options": [
                {
                    "option_id": "$optionId",
                    "value": [
                        "Test"
                    ]
                },
                {
                    "option_id": "9999999999999",
                    "value": [
                        "Test"
                    ]
                }
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

    private function getErrorJson()
    {
        return <<<JSON
{
    "message": "There was an issue with the JSON that was sent through",
    "success": false,
    "errors": {
        "description": "Invalid value of \"%value\" provided for the %fieldName field.",
        "details": {
            "fieldName": "option_id",
            "value": 9999999999999
        }
    }
}
JSON;
    }
}
