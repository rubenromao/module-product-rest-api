<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch1;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class HandleFirstnameLastnameAndTelephoneInAddress extends PlaceOrderAbstract
{

    /**
     * @magentoApiDataFixture apiFixtures
     *
     * Test that when we pass through the first_name, last_name and telephone in the shipping address and the billing
     * address the values are stored in the database.
     */
    public function testFirstnameLastnameAndTelephoneInAddress()
    {
        $testHelper   = $this->getTestHelper();
        $orderId      = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId);

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

        $order = $this->getOrder();
        $shippingAddress = $order->getShippingAddress();
        $this->assertEquals("JohnShipping", $shippingAddress->getFirstname());
        $this->assertEquals("SmithShipping", $shippingAddress->getLastname());
        $this->assertEquals("07654213213", $shippingAddress->getTelephone());

        $billingAddress = $order->getBillingAddress();
        $this->assertEquals("JohnBilling", $billingAddress->getFirstname());
        $this->assertEquals("SmithBilling", $billingAddress->getLastname());
        $this->assertEquals("07654312312", $billingAddress->getTelephone());
    }

    private function getRequestJson($orderId)
    {
        $testHelper = $this->getTestHelper();
        $timestamp = self::CREATED_AT_TIMESTAMP_UTC;
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'XL');

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
            "origin": 5,
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
            "qty": 2,
            "id": "3",
            "configurable_options": [

            ],
            "custom_options": [

            ]
        },
        {
            "qty": 2,
            "id": "4",
            "origin": 6,
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
            "post_code": "LS1 2AB",
            "first_name": "JohnBilling",
            "last_name": "SmithBilling",
            "telephone": "07654312312"
        },
        "type": {
            "code": "union_pay",
            "label": "Union Pay"
        },
        "state": "paid",
        "data": []
    },
    "delivery_method": {
        "shipping_address_details": {
            "street_1": "123 Main Street",
            "street_2": "",
            "city": "Leeds",
            "region": "West Yorkshire",
            "country": "United Kingdom",
            "post_code": "LS1 2AB",
            "first_name": "JohnShipping",
            "last_name": "SmithShipping",
            "telephone": "07654213213"
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
        $timestamp  = self::CREATED_AT_TIMESTAMP_RESPONSE;
        $testHelper = $this->getTestHelper();
        $colourId   = $testHelper->getAttributeOptionId('colour', 'Indigo');
        $sizeId     = $testHelper->getAttributeOptionId('size', 'XL');

        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "processing",
    "timestamp": "$timestamp",
    "is_virtual": false,
    "payment": {
        "final_price": 163.90,
        "price_breakdown": [
            {
                "type": "unit",
                "amount": 138.9
            },
            {
                "type": "shipping",
                "amount": 25
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
        },
        {
            "custom_options": [

            ],
            "description": "",
            "id": 4,
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
            "price": 12.50,
            "is_virtual": false,
            "configurable_options": [

            ],
            "quantity": 2,
            "subtitle": "Ctr bk length approx 60cm. 100% polyester. Sleeves 100% polyester.",
            "title": "Silky Printed top",
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
        "first_name": "JohnBilling",
        "last_name": "SmithBilling",
        "telephone": "07654312312"
    },
	"shipping_address_details": {
		"location": null,
		"street1": "123 Main Street",
		"street2": "",
        "city": "Leeds",
        "region": "West Yorkshire",
		"country": "GB",
		"post_code": "LS1 2AB",
		"first_name": "JohnShipping",
		"last_name": "SmithShipping",
		"telephone": "07654213213"
	}
}
JSON;
    }
}