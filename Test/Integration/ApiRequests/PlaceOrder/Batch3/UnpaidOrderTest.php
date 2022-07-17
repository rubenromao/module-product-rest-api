<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch3;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class UnpaidOrderTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture loadCountryDefaults
     */
    public function testUnpaidOrder()
    {
        $testHelper   = $this->getTestHelper();
        $orderId      = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('test', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId);
        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
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
            "code": "manual_payment",
            "label": "Manual Payment"
        },
        "state": "unpaid",
        "data": []
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

    private function getExpectedJson($orderId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_RESPONSE;
        return <<<JSON
{
	"order_id": "$orderId",	
	"order_status": "pending",
	"timestamp": "$timestamp",
	"is_virtual": false,
	"payment": {
		"final_price": 63.9,
		"price_breakdown": [
			{
				"type": "unit",
				"amount": 53.9
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
			"custom_options": [],
			"description": "",
			"id": 3,
			"image_thumbs": [
				[
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/wbk003t_thumb_400x550.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/wbk003t_thumb_800x1100.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/wbk003t_thumb_1200x1651.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/wbk003t_thumb_1600x2201.jpg"
				]
			],
			"images": [
			      "https://rce-dev-media.s3.amazonaws.com/test/4/product/wbk003t.jpg"
            ],
			"merchant_id": 4,
			"price": 26.95,
			"configurable_options": [],
			"quantity": 2,
			"subtitle": "Length approx 70cm (shoulder to hem). 100% polyester. Lining 100% polyester.",
			"title": "Top with knotted shoulders",
			"is_virtual": false,
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
        "street1": "123 Billing Street",
        "street2": "Billing Square",
        "city": "Westminster",
        "region": "London",
        "country": "GB",
        "post_code": "LS1 2AB",
        "first_name": "John",
        "last_name": "Smith",
        "telephone": "0123456789"
    },
	"shipping_address_details": {
		"location": null,
		"street1": "123 Shipping Street",
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
}
