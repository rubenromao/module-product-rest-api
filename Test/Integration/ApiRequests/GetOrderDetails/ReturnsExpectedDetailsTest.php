<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\GetOrderDetails;

use Magento\Framework\Webapi\Rest\Request;

class ReturnsExpectedDetailsTest extends GetOrderDetailsAbstract
{
    /**
     * @magentoApiDataFixture multipleCustomOptionsProductsFixtures
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $testHelper   = $this->getTestHelper();
        $orderId      = $this->placeOrder();
        $result       = $this->makeRequest('chase', '4', $orderId);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function placeOrder()
    {
        $testHelper  = $this->getTestHelper();
        $requestJson = $this->getRequestJson();
        $postData    = $testHelper->convertJsonToPostData($requestJson);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/api/mobile/chase/4/order",
                'httpMethod'   => Request::HTTP_METHOD_POST,
                'token'        => 'test'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, $postData);

        return $result['order_id'];
    }

    private function getRequestJson()
    {
        $testHelper = $this->getTestHelper();
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'XL');

        return
            <<<JSON
{
    "core_order_id": "123",
    "phone_number": "0123456789",
    "partner_id": 12,
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
    "billing_address_details": {
        "street_1": "123 Main Street",
        "street_2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
        "country": "United Kingdom",
        "post_code": "LS1 2AB"
    },
    "shipping_address_details": {
        "street_1": "123 Main Street",
        "street_2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
        "country": "United Kingdom",
        "post_code": "LS1 2AB"
    }
}
JSON;
    }

    private function getExpectedJson($orderId)
    {
        $testHelper = $this->getTestHelper();
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'XL');
        $timeStamp  = $this->getExpectedTimeStamp();

        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "processing",
    "timestamp": "$timeStamp",
    "is_virtual": false,
    "payment": {
        "final_price": 65,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 60
            },
            {
                "type": "shipping",
                "amount": 5
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
}
