<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch2;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class SingleActProductTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testGetBasketCall()
    {
        $this->markTestSkipped('Acts have been disabled at the moment, so skipping test');
        $testHelper   = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $customOption = $this->getProductCustomOptions(300, 'Name');
        $optionId     = $customOption->getOptionId();
        $requestJson  = $this->getRequestJson($orderId, $optionId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $order    = $this->getOrder();
        $allItems = $order->getAllItems();
        $this->assertEquals(1, count($allItems));
        foreach ($allItems as $item) {
            $this->assertEquals(300, $item->getProductId());
            $this->assertEquals(1, $item->getQtyOrdered());
            $options = $item->getProductOptions();
            $this->assertTrue(isset($options['options']));
            $this->assertNotEmpty($options['options']);
            foreach ($options['options'] as $option) {
                $this->assertEquals('full_name', $option['option_type']);
                $this->assertEquals($optionId, $option['option_id']);
                $this->assertEquals('Test', $option['value']);
            }
        }
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
                "value": ["Test"]
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
        "final_price": 0,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 0
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
            "custom_options": [
                {
                    "label": "Name",
                    "option_id": "58",
                    "value": "Test",
                    "value_ids": [
                        "Test"
                    ]
                }
            ],
            "description": "",
            "id": 300,
            "image_thumbs": [
                [
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/abasicimage_thumb_400x550.png",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/abasicimage_thumb_800x1100.png",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/abasicimage_thumb_1200x1651.png",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/abasicimage_thumb_1600x2201.png"
                ]
            ],
            "images": [
                "https://rce-dev-media.s3.amazonaws.com/chase/4/product/abasicimage.png"
            ],
            "merchant_id": 4,
            "price": 0,
            "is_virtual": true,
            "configurable_options": [

            ],
            "quantity": 1,
            "subtitle": "Act",
            "title": "A simple Act Product",
            "is_act": true,
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
}
