<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

class HandleInvalidOrderIdTest extends UpdateOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $testHelper     = $this->getTestHelper();
        $requestJson    = $this->getRequestJson('completed');
        $unknownOrderId = '100-abc';
        $postData       = $testHelper->convertJsonToPostData($requestJson);
        $result         = $this->sendRequestThatShouldError('chase', '4', $unknownOrderId, $postData, 400);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getErrorJson($unknownOrderId);

        $this->assertEquals($expectedJson, $returnedJson);
    }

    private function getErrorJson($unknownOrderId)
    {
        return <<<JSON
{
    "message": "There was an issue with the JSON that was sent through",
    "success": false,
    "errors": {
        "description": "Invalid value of \"%value\" provided for the %fieldName field.",
        "details": {
            "fieldName": "core_order_id",
            "value": "$unknownOrderId"
        }
    }
}
JSON;
    }
}
