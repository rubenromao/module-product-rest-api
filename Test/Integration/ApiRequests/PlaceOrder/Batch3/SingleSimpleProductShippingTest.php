<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch3;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class SingleSimpleProductShippingTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture freeShipping
     * @magentoApiDataFixture loadStores
     */
    public function testGetBasketCall()
    {
        $testHelper   = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId, $this->getShippingJson('freeshipping'));
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedFreeShippingJson($orderId);
        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $orderId = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId, $this->getShippingJson('flatrate'));
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedFlatrateShippingJson($orderId);
        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $orderId = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId, $this->getShippingJson('storepickup'));
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedStorepickupShippingJson($orderId);
        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getRequestJson($orderId, $shippingMethod)
    {
        return
            <<<JSON
{
    "core_order_id": "$orderId",
    "phone_number": "0123456789",
    "partner_id": 12,
    "timestamp": "2018-03-23T18:29:00.232648Z",
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
        $shippingMethod
    },
    "location": {
        "latitude": "4.56",
        "longitude": "-1.23"
    }
}
JSON;
    }

    private function getShippingJson($shippingMethod)
    {
        if ($shippingMethod == 'storepickup') {
            return
                <<<JSON
    "shipping_method":{
        "carrier_code":"$shippingMethod",
        "method_code":"$shippingMethod",
        "display_name":"$shippingMethod",
        "extension_attributes":[{
            "code":"pickup_store",
            "value":"1"}
        ]
    }
JSON;
        } else {
            return
                <<<JSON
    "shipping_method":{
        "carrier_code":"$shippingMethod",
        "method_code":"$shippingMethod",
        "display_name":"$shippingMethod"
    }
JSON;
        }
    }

    private function getExpectedFlatrateShippingJson($orderId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_RESPONSE;
        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "processing",
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
            "custom_options": [

            ],
            "description": "",
            "id": 3,
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
            "price": 26.95,
            "is_virtual": false,
            "configurable_options": [

            ],
            "quantity": 2,
            "subtitle": "Length approx 70cm (shoulder to hem). 100% polyester. Lining 100% polyester.",
            "title": "Top with knotted shoulders",
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
        "street1": "123 Main Street",
        "street2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
        "country": "GB",
        "post_code": "LS1 2AB",
        "location": null,
        "first_name": "John",
        "last_name": "Smith",
        "telephone": "0123456789"
    }
}
JSON;
    }

    private function getExpectedFreeShippingJson($orderId)
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
            "custom_options": [

            ],
            "description": "",
            "id": 3,
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
            "price": 26.95,
            "is_virtual": false,
            "configurable_options": [

            ],
            "quantity": 2,
            "subtitle": "Length approx 70cm (shoulder to hem). 100% polyester. Lining 100% polyester.",
            "title": "Top with knotted shoulders",
            "is_act": false,
            "assets": []
        }
    ],
    "shipping_method": {
        "carrier_code": "freeshipping",
        "method_code": "freeshipping",
        "display_name": "Free Shipping - Free",
        "extension_attributes": null,
        "is_default": false
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
        "street1": "123 Main Street",
        "street2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
        "country": "GB",
        "post_code": "LS1 2AB",
        "location": null,
        "first_name": "John",
        "last_name": "Smith",
        "telephone": "0123456789"
    }
}
JSON;
    }

    private function getExpectedStorepickupShippingJson($orderId)
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
            "custom_options": [

            ],
            "description": "",
            "id": 3,
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
            "price": 26.95,
            "is_virtual": false,
            "configurable_options": [

            ],
            "quantity": 2,
            "subtitle": "Length approx 70cm (shoulder to hem). 100% polyester. Lining 100% polyester.",
            "title": "Top with knotted shoulders",
            "is_act": false,
            "assets": []
        }
    ],
    "shipping_method": {
        "carrier_code": "storepickup",
        "method_code": "storepickup",
        "display_name": "Pickup In Store - Pickup In Store",
        "extension_attributes": [
            {
                "code": "pickup_store",
                "value": "1"
            }
        ],
        "is_default": false
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
