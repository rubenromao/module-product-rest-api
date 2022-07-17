<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch1;

use Magento\Sales\Model\Order\Item;
use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class AllProductTypesTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testGetBasketCall()
    {
        $testHelper   = $this->getTestHelper();
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'XL');
        $orderId = $this->getCoreOrderId();
        $customOption = $this->getProductCustomOptions(300, 'Name');
        $optionId     = $customOption->getOptionId();
        $requestJson  = $this->getRequestJson($orderId, $optionId, $colourId, $sizeId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId, $optionId, $colourId, $sizeId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $order            = $this->getOrder();
        $allItems         = $order->getAllItems();
        $expectedProducts = [
            'tori_tank'                  => [
                'type' => 'configurable',
                'qty'  => 1,
            ],
            'tori_tank_indigo_xl'        => [
                'type' => 'simple',
                'qty'  => 1
            ],
            'act_300'                    => [
                'qty'     => 1,
                'type'    => 'virtual',
                'options' => [
                    $optionId => [
                        'type'  => 'full_name',
                        'value' => 'Test'
                    ]
                ]
            ],
            'top_with_knotted_shoulders' => [
                'qty'  => 2,
                'type' => 'simple'
            ]
        ];
        $this->assertEquals(4, count($allItems));
        foreach ($allItems as $item) {
            $sku = $item->getProduct()->getSku();
            $this->assertTrue(isset($expectedProducts[$sku]));
            $productDetails = $expectedProducts[$sku];
            $this->assertEquals($productDetails['type'], $item->getProductType(), "Product with $sku has wrong type");
            $this->assertEquals($productDetails['qty'], $item->getQtyOrdered());
            if (isset($productDetails['options'])) {
                $this->checkCustomOptions($item, $productDetails['options']);
            }
        }
    }

    private function checkCustomOptions(Item $item, $expectedOptions)
    {
        $options = $item->getProductOptions();
        $this->assertTrue(isset($options['options']));
        $this->assertNotEmpty($options['options']);
        foreach ($options['options'] as $option) {
            $optionId = $option['option_id'];
            $this->assertTrue(isset($expectedOptions[$optionId]));
            $expectedOption = $expectedOptions[$optionId];
            $this->assertEquals($expectedOption['type'], $option['option_type']);
            $this->assertEquals($expectedOption['value'], $option['value']);
        }
    }

    private function getRequestJson($orderId, $optionId, $colourId, $sizeId)
    {
        $testHelper = $this->getTestHelper();
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
        },
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
        },
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

    private function getExpectedJson($orderId, $optionId, $colourId, $sizeId)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_RESPONSE;

        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "processing",
    "timestamp": "$timestamp",
    "is_virtual": false,
    "payment": {
        "final_price": 128.9,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 113.9
            },
            {
                "type": "shipping",
                "amount": 15
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
            "price": 60,
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
                    "value": "XL",
                    "value_id": $sizeId
                }
            ],
            "quantity": 1,
            "subtitle": "A simple ribbed cotton tank. Great for layering.",
            "title": "Tori Tank",
            "is_act": false,
            "assets": []
        },
        {
            "custom_options": [
                {
                    "label": "Name",
                    "option_id": "$optionId",
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
        },
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
    "shipping_address_details":{
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
