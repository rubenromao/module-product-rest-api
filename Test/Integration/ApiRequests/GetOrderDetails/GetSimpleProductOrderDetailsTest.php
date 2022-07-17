<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\GetOrderDetails;

class GetSimpleProductOrderDetailsTest extends GetOrderDetailsAbstract
{
    /**
     * @magentoApiDataFixture multipleCustomOptionsProductsFixtures
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $testHelper     = $this->getTestHelper();
        $pendingOrderId = '100000002';
        $result         = $this->makeRequest('chase', '4', $pendingOrderId);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getExpectedJson($pendingOrderId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getExpectedJson($orderId)
    {
        $timeStamp = $this->getExpectedTimeStamp();
        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "complete",
    "timestamp": "$timeStamp",
    "is_virtual": false,
    "payment": {
        "final_price": 60,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 60
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
            "description": "Ribbed scoop neck Tank Indigo. 100% cotton.Machine wash.",
            "id": 100,
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
            "price": 60,
            "is_virtual": false,
            "configurable_options": [

            ],
            "quantity": 1,
            "subtitle": "A simple ribbed cotton Tank Indigo. Great for layering.",
            "title": "Tori Tank Indigo L",
            "custom_options": [
            
            ],
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
        "street1": "47568 Lavinia Road",
        "street2": "",
        "city": "Rosamondchester",
        "region": "Pat VonRueden",
        "country": "AW",
        "post_code": "31606",
        "first_name": "Duane",
        "last_name": "Halvorson",
        "telephone": "562-616-8762"
    },
    "shipping_address_details": {
        "street1": "47568 Lavinia Road",
        "street2": "",
        "city": "Rosamondchester",
        "region": "Pat VonRueden",
        "country": "AW",
        "post_code": "31606",
        "location": null,
        "first_name": "Duane",
        "last_name": "Halvorson",
        "telephone": "562-616-8762"
    }
}
JSON;
    }
}
