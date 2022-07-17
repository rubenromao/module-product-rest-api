<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch3;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class PaidOrderWithStorePickupTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture loadCountryDefaults
     * @magentoApiDataFixture loadStores
     */
    public function testPaidOrderWithStorePickup()
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
            "code": "union_pay",
            "label": "Union Pay"
        },
        "state": "paid",
        "data": []
    },
    "delivery_method": {
        "shipping_address_details": {},
        "shipping_method": {
            "carrier_code":"storepickup",
            "method_code":"storepickup",
            "display_name":"storepickup",
            "extension_attributes":[{
                "code":"pickup_store",
                "value":"1"}
            ]
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
	"order_status": "processing",
	"timestamp": "$timestamp",
	"is_virtual": false,
	"payment": {
		"final_price": 53.9,
		"price_breakdown": [
			{
				"type": "unit",
				"amount": 53.9
			},
			{
				"type": "shipping",
				"amount": 0
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
		"carrier_code": "storepickup",
		"method_code": "storepickup",
		"display_name": "Pickup In Store - Pickup In Store",
		"extension_attributes": [{
                "code":"pickup_store",
                "value":"1"}
            ],
            "is_default": false
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
		"location": {
			"latitude": "5.56",
			"longitude": "-2.23"
		},
		"street1": "123 Main Street",
		"street2": "456 Second Street",
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
