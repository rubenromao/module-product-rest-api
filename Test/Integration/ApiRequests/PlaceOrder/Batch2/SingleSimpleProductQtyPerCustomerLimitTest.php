<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch2;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class SingleSimpleProductQtyPerCustomerLimitTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testGetBasketCall()
    {
        $testHelper   = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $timestamp    =  date('Y-m-d\TH:i:s+00:00');
        $requestJson  = $this->getRequestJson($orderId, $timestamp);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId, $timestamp);
        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $orderId = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId, $timestamp);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->sendRequestThatShouldError('chase', '4', $postData, 400);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedErrorJson();
        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $order = $this->getOrder();
        $allItems = $order->getAllItems();
        $this->assertEquals(1, count($allItems));
    }

    private function getRequestJson($orderId, $timestamp)
    {
        return
            <<<JSON
{
    "core_order_id": "$orderId",
    "phone_number": "0123456789",
    "partner_id": 12,
    "timestamp": "$timestamp",
    "products": [
        {
            "qty": 2,
            "id": "4",
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

    private function getExpectedJson($orderId, $timestamp)
    {
        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "processing",
    "timestamp": "$timestamp",
    "is_virtual": false,
    "payment": {
        "final_price": 35,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 25
            },
            {
                "type": "shipping",
                "amount": 10
            },
            {
                "type": "tax",
                "amount": 0
            },
            {
                "type": "discount",
                "amount": 0
            }
        ]
    },
    "products": [
        {
            "custom_options": [

            ],
            "description": "",
            "id": 4,
            "image_thumbs": [
                [
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003t_thumb_400x550.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003t_thumb_800x1100.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003t_thumb_1200x1651.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003t_thumb_1600x2201.jpg"
                ]
            ],
            "images": [
                "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003t.jpg"
            ],
            "merchant_id": 4,
            "price": 12.5,
            "is_virtual": false,
            "configurable_options": [

            ],
            "quantity": 2,
            "subtitle": "Ctr bk length approx 60cm. 100% polyester. Sleeves 100% polyester.",
            "title": "Silky Printed top",
            "is_act": false,
            "assets": []
        }
    ],
	"shipping_method": {
		"carrier_code": "flatrate",
		"method_code": "flatrate",
		"display_name": "Flat Rate - Fixed",
		"extension_attributes": null,
		"is_default": true
	},
	"billing_address_details": {
        "street1": "123 Main Street",
        "street2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
        "country": "GB",
        "post_code": "LS1 2AB",
        "first_name": "John",
        "last_name": "Smith",
        "telephone": "0123456789"
    },
	"shipping_address_details": {
		"location": null,
		"street1": "123 Main Street",
		"street2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
		"country": "GB",
		"post_code": "LS1 2AB",
		"first_name": "John",
		"last_name": "Smith",
		"telephone": "0123456789"
	}
}
JSON;
    }

    private function getExpectedErrorJson()
    {
        return <<<JSON
{
    "message": "The most you may purchase is 3.",
    "success": false,
    "errors": ""
}
JSON;
    }
}
