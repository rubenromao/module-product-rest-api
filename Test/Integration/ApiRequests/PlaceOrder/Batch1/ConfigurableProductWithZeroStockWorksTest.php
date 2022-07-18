<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch1;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class ConfigurableProductWithZeroStockWorksTest extends PlaceOrderAbstract
{

    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture outOfStockFixture
     */
    public function testOutOfStockWorks()
    {
        $testHelper   = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $order    = $this->getOrder();
        $allItems = $order->getAllItems();
        $this->assertEquals(2, count($allItems));

        foreach ($allItems as $item) {
            $product    = $item->getProduct();
            $stockItem  = $product->getExtensionAttributes()->getStockItem();
            $stockLevel = $stockItem->getQty();
            $sku = $product->getSku();
            switch ($sku) {
                case 'tori_tank':
                    $expectedStock = 0;
                    break;
                case 'tori_tank_indigo_l':
                    $expectedStock = 97;
                    break;
                default:
                    $this->fail("Unknown product in order with SKU of $sku");
                    /* Avoids an undefined variable notice below */
                    $expectedStock = "not-a-number";
            }

            $this->assertEquals($expectedStock, $stockLevel, "Expected $expectedStock, got $stockLevel for $sku");
        }
    }

    private function getRequestJson($orderId)
    {
        $testHelper = $this->getTestHelper();
        $timestamp = self::CREATED_AT_TIMESTAMP_UTC;
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'L');

        return <<<JSON
{
    "core_order_id": "$orderId",
    "phone_number": "0123456789",
    "partner_id": 12,
    "timestamp": "$timestamp",
    "products": [
        {
            "qty": 3,
            "id": "6",
            "configurable_options": [
                {
                    "code": "colour",
                    "value": "$colourId"
                },
                {
                    "code": "size",
                    "value": "$sizeId"
                }
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

    private function getExpectedJson($orderId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_RESPONSE;
        $testHelper = $this->getTestHelper();
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'L');
        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "processing",
    "timestamp": "$timestamp",
    "is_virtual": false,
    "payment": {
        "final_price": 231,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 180
            },
            {
                "type": "shipping",
                "amount": 15
            },
            {
                "type": "tax",
                "amount": 36
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
            "id": 6,
            "image_thumbs": [
                [
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003a_thumb_400x550.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003a_thumb_800x1100.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003a_thumb_1200x1651.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003a_thumb_1600x2201.jpg"
                ],
                [
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003b_thumb_400x550.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003b_thumb_800x1100.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003b_thumb_1200x1651.jpg",
                    "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003b_thumb_1600x2201.jpg"
                ]
            ],
            "images": [
                "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003a.jpg",
                "https://rce-dev-media.s3.amazonaws.com/chase/4/product/wbk003b.jpg"
            ],
            "merchant_id": 4,
            "price": 72,
            "is_virtual": false,
            "configurable_options": [
                {
                    "code": "colour",
                    "label": "Colour",
                    "value": "Indigo",
                    "value_id": $colourId
                },
                {
                    "code": "size",
                    "label": "Size",
                    "value": "L",
                    "value_id": $sizeId
                }
            ],
            "quantity": 3,
            "subtitle": "A simple ribbed cotton tank. Great for layering.",
            "title": "Tori Tank",
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
}
