<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch3;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class VirtualUnpaidOrderNoShippingAddressTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture loadCountryDefaults
     */
    public function testVirtualUnpaidOrderNoShippingAddress()
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
            "qty": 1,
            "id": 210,
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
        "billing_address_details": {},
        "type": {
            "code": "manual_payment",
            "label": "Manual Payment"
        },
        "state": "unpaid",
        "data": []
    },
    "delivery_method": {
        "shipping_address_details": {},
        "shipping_method": {}
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
	"is_virtual": true,
	"payment": {
		"final_price": 26.95,
		"price_breakdown": [
			{
				"type": "unit",
				"amount": 26.95
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
			"id": 210,
			"image_thumbs": [
				[
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/abasicimage_thumb_400x550.png",
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/abasicimage_thumb_800x1100.png",
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/abasicimage_thumb_1200x1651.png",
                    "https://rce-dev-media.s3.amazonaws.com/test/4/product/abasicimage_thumb_1600x2201.png"
				]
			],
			"images": [
			      "https://rce-dev-media.s3.amazonaws.com/test/4/product/abasicimage.png"
            ],
			"merchant_id": 4,
			"price": 26.95,
			"configurable_options": [],
			"quantity": 1,
			"subtitle": "Promo gift card",
			"title": "Gift Card",
			"is_virtual": true,
			"is_act": false,
			"assets": []
		}
    ],
	"shipping_method": {
		"carrier_code": "",
		"method_code": "",
		"display_name": "",
		"extension_attributes": null,
		"is_default": false
	},
	"billing_address_details": {
        "street1": "",
        "street2": null,
        "city": null,
        "region": null,
        "country": "CN",
        "post_code": null,
        "first_name": "John",
        "last_name": "Smith",
        "telephone": "0123456789"
    },
	"shipping_address_details": {
		"location": null,
		"street1": null,
		"street2": null,
		"city": null,
		"region": null,
		"country": null,
		"post_code": null,
		"first_name": null,
		"last_name": null,
		"telephone": null
	}
}
JSON;
    }
}
