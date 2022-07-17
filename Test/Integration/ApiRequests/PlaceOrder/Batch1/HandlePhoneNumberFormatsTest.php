<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch1;

use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class HandlePhoneNumberFormatsTest extends PlaceOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testGetBasketCall()
    {
        $testHelper          = $this->getTestHelper();
        $expectedIncrementId = self::EXPECTED_ORDER_ID - 1;
        $phoneNumberFormats  = [
            /* Some common formats that I've seen in the past */
            '0123456789',
            '0789 123 456',
            '0456-789-123',
            '+00123456789',
            /* Taiwan Standards (https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers) */
            '(01) 2345 6789',
            '(0456) 789123',
            /* Mexican Standards (as above)*/
            '(01) 55 1234 5678',
            '(01-55)-1234-5678',
            '(55) 1234 5678',
            '6641234567',
            '664 123 4567',
            '664 123 45 67',
            '(044) 664 123 4567',
            '(044) 664 123 45 67',
        ];
        foreach ($phoneNumberFormats as $phoneNumberToUse) {
            $expectedIncrementId++;
            $orderId = $this->getCoreOrderId();
            $requestJson  = $this->getRequestJson($orderId, $phoneNumberToUse);
            $postData     = $testHelper->convertJsonToPostData($requestJson);
            $result       = $this->makeRequest('chase', '4', $postData);
            $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
            $expectedJson = $this->getExpectedResult($orderId, $phoneNumberToUse);

            $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);

            $order    = $this->getOrder($expectedIncrementId);
            $allItems = $order->getAllItems();
            $this->assertEquals(1, count($allItems));
            $billingAddress  = $order->getBillingAddress();
            $shippingAddress = $order->getShippingAddress();
            $this->assertEquals(
                $phoneNumberToUse,
                $billingAddress->getTelephone(),
                "Billing phone number is wrong for format $phoneNumberToUse"
            );
            $this->assertEquals(
                $phoneNumberToUse,
                $shippingAddress->getTelephone(),
                "Shipping phone number is wrong for format $phoneNumberToUse"
            );
            sleep(1);
        }
    }

    private function getRequestJson($orderId, $phoneNumber)
    {
        $timestamp = self::CREATED_AT_TIMESTAMP_UTC;
        return
            <<<JSON
{
    "core_order_id": "$orderId",
    "phone_number": "$phoneNumber",
    "partner_id": 10,
    "timestamp": "$timestamp",
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
        "shipping_address_details": {
            "street_1": "456 Different Road",
            "street_2": "",
            "city": "Oxford",
            "region": "Oxfordshire",
            "country": "United Kingdom",
            "post_code": "OX12 3CD"
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

    public function getExpectedResult($orderId, $phoneNumber)
    {
        $expectedTimestamp = self::CREATED_AT_TIMESTAMP_RESPONSE;

        return <<<JSON
{
    "order_id": "$orderId",
    "order_status": "processing",
    "timestamp": "$expectedTimestamp",
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
        "telephone": "$phoneNumber"
    },
	"shipping_address_details": {
		"location": null,
		"street1": "456 Different Road",
		"street2": "",
        "city": "Oxford",
        "region": "Oxfordshire",
		"country": "GB",
		"post_code": "OX12 3CD",
		"first_name": "John",
		"last_name": "Smith",
		"telephone": "$phoneNumber"
	}
}
JSON;
    }

    private function getExpectedJson($orderId)
    {
        return <<<JSON
{
     "order_id": "$orderId"
    }
JSON;
    }
}
