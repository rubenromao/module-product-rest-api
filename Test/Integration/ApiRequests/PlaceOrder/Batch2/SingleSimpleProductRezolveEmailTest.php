<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\Batch2;

use Magento\Framework\Webapi\Rest\Request;
use Rezolve\APISalesV4\Test\Integration\ApiRequests\PlaceOrder\PlaceOrderAbstract;

class SingleSimpleProductRezolveEmailTest extends PlaceOrderAbstract
{
    const REZOLVE_CUSTOMER_EMAIL = 'has_discount_rezolve@example.com';
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testGetBasketCall()
    {
        $testHelper   = $this->getTestHelper();
        $orderId = $this->getCoreOrderId();
        $requestJson  = $this->getRequestJson($orderId);
        $postData     = $testHelper->convertJsonToPostData($requestJson);
        $result       = $this->makeRequest('chase', '4', $postData);
        $magentoOrder = $this->getOrderFromMagentoApi('1');
        $magentoCustomer = $this->getMagentoCustomer($magentoOrder['customer_id']);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson($orderId);
        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
        $this->assertEquals(self::REZOLVE_CUSTOMER_EMAIL, $magentoOrder['customer_email']);
        $order = $this->getOrder();
        $allItems = $order->getAllItems();
        $this->assertEquals(1, count($allItems));
        // Get the value of the customer's rezolve_customer_email and confirm that it's been updated with the new value.
        foreach ($magentoCustomer['custom_attributes'] as $customAttribute) {
            if ($customAttribute['attribute_code'] == 'rezolve_customer_email') {
                $this->assertEquals(self::REZOLVE_CUSTOMER_EMAIL, $customAttribute['value']);
            }
        }
    }

    public function getMagentoCustomer($customerId)
    {
        $adminToken   = $this->getAdminToken();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customers/" . $customerId,
                'httpMethod'   => Request::HTTP_METHOD_GET,
                'token'        => $adminToken
            ]
        ];
        $result = $this->_webApiCall($serviceInfo);
        return $result;
    }

    private function getRequestJson($orderId)
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
            "qty": 2,
            "id": "3",
            "configurable_options": [

            ],
            "custom_options": [

            ]
        }
    ],
    "customer_details": {
        "email_address": "has_discount_rezolve@example.com",
        "customer_identifier": "123456789-A",
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
