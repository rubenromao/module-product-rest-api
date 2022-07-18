<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\GetOrderDetails;

class GetOrderDetailsTest extends GetOrderDetailsAbstract
{
    /**
     * @magentoApiDataFixture multipleCustomOptionsProductsFixtures
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $testHelper     = $this->getTestHelper();
        $pendingOrderId = '100000001';
        $result         = $this->makeRequest('chase', '4', $pendingOrderId);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getExpectedJson($pendingOrderId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getExpectedJson($orderId)
    {
        $testHelper = $this->getTestHelper();
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'L');
        $timeStamp = $this->getExpectedTimeStamp();
        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "pending_payment",
    "timestamp": "$timeStamp",
    "is_virtual": false,
    "payment": {
        "final_price": 298,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 240
            },
            {
                "type": "shipping",
                "amount": 10
            },
            {
                "type": "tax",
                "amount": 48
            },
            {
                "type": "discount",
                "amount": 0
            }
        ]
    },
    "products": [
        {
            "description": "Ribbed scoop neck tank. 100% cotton.Machine wash.",
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
            "quantity": 4,
            "subtitle": "A simple ribbed cotton tank. Great for layering.",
            "title": "Tori Tank",
            "custom_options": [
            
            ],
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
        "street1": "10667 Noemie Estate ",
        "street2": "            A Second Street",
        "city": "Arvillamouth",
        "region": "Hipolito Towne",
        "country": "GB",
        "post_code": "49583",
        "first_name": "Grayce",
        "last_name": "Cummings",
        "telephone": "1-536-558-2753 x5698"
    },
    "shipping_address_details": {
        "street1": "10667 Noemie Estate ",
        "street2": "            A Second Street",
        "city": "Arvillamouth",
        "region": "Hipolito Towne",
        "country": "GB",
        "post_code": "49583",
        "location": null,
        "first_name": "Grayce",
        "last_name": "Cummings",
        "telephone": "1-536-558-2753 x5698"
    }
}
JSON;
    }
}
