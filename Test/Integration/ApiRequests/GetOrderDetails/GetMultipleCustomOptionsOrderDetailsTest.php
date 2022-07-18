<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\GetOrderDetails;

class GetMultipleCustomOptionsOrderDetailsTest extends GetOrderDetailsAbstract
{
    /**
     * @magentoApiDataFixture multipleCustomOptionsProductsFixtures
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $testHelper     = $this->getTestHelper();
        $pendingOrderId = '100000005';
        $result         = $this->makeRequest('chase', '4', $pendingOrderId);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getExpectedJson($pendingOrderId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getExpectedJson($orderId)
    {
        $customOption = $this->getProductCustomOptions(305, 'Name');
        $customOptionId = $customOption->getOptionId();
        $redOption = $this->getCustomOptionValueId('Red', $customOption);
        $yellowOption = $this->getCustomOptionValueId('Yellow', $customOption);
        $timeStamp = $this->getExpectedTimeStamp();
        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "complete",
    "timestamp": "$timeStamp",
    "is_virtual": false,
    "payment": {
        "final_price": 38,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 38
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
                    "option_id": "$customOptionId",
                    "value": "Red,Yellow",
                    "value_ids": [
                        "$redOption",
                        "$yellowOption"
                    ]
                }
            ],
            "description": "Product Has Multiple Custom Option Values",
            "id": 305,
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
            "price": 19,
            "is_virtual": false,
            "configurable_options": [

            ],
            "quantity": 2,
            "subtitle": "Act",
            "title": "Multiple Option Values",
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
        "street1": "96299 Darrion Junctions",
        "street2": "",
        "city": "North Schuylerhaven",
        "region": "Dr. Kylee Lubowitz",
        "country": "GD",
        "post_code": "40227",
        "first_name": "Talon",
        "last_name": "Berge",
        "telephone": "552.520.8991 x36829"
    },
    "shipping_address_details": {
        "street1": "96299 Darrion Junctions",
        "street2": "",
        "city": "North Schuylerhaven",
        "region": "Dr. Kylee Lubowitz",
        "country": "GD",
        "post_code": "40227",
        "location": null,
        "first_name": "Talon",
        "last_name": "Berge",
        "telephone": "552.520.8991 x36829"
    }
}
JSON;
    }
}
