<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

class HandleMissingStatusTest extends UpdateOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $this->markTestSkipped('No longer need to handle a missing status, this now does nothing');

        $testHelper     = $this->getTestHelper();
        $requestJson    = $this->getBrokenRequestJson();
        $pendingOrderID = $this->getPendingOrderID();
        $postData       = $testHelper->convertJsonToPostData($requestJson);
        $result         = $this->sendRequestThatShouldError('chase', '4', $pendingOrderID, $postData, 400);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getErrorJson();

        $this->assertEquals($expectedJson, $returnedJson);
    }

    private function getBrokenRequestJson()
    {
        return <<<JSON
{
    "payment": {

    }
}
JSON;
    }

    private function getErrorJson()
    {
        return <<<JSON
{
    "message": "There was an issue with the JSON that was sent through",
    "success": false,
    "errors": {
        "description": "\"%fieldName\" is required. Enter and try again.",
        "details": {
            "fieldName": "status"
        }
    }
}
JSON;
    }
}
