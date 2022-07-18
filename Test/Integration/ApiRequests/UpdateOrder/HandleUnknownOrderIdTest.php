<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

class HandleUnknownOrderIdTest extends UpdateOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $testHelper     = $this->getTestHelper();
        $requestJson    = $this->getRequestJson('completed');
        $unknownOrderId = '123';
        $postData       = $testHelper->convertJsonToPostData($requestJson);
        $result         = $this->sendRequestThatShouldError('chase', '4', $unknownOrderId, $postData, 404);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getErrorJson($unknownOrderId);

        $this->assertEquals($expectedJson, $returnedJson);
    }

    private function getErrorJson($unknownOrderId)
    {
        return <<<JSON
{
    "message": "Could not find the requested entity",
    "success": false,
    "errors": {
        "description": "No such entity with %fieldName = %fieldValue",
        "details": {
            "fieldName": "core_order_id",
            "fieldValue": "$unknownOrderId"
        }
    }
}
JSON;
    }
}
