<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

class HandleUnknownStatusTest extends UpdateOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testOrderUpdates()
    {
        $unknownStatus = 'not-real';
        $testHelper     = $this->getTestHelper();
        $requestJson    = $this->getRequestJson($unknownStatus);
        $pendingOrderID = $this->getPendingOrderID();
        $postData       = $testHelper->convertJsonToPostData($requestJson);
        $result         = $this->sendRequestThatShouldError('chase', '4', $pendingOrderID, $postData, 400);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getErrorJson($unknownStatus);

        $this->assertEquals($expectedJson, $returnedJson);
    }

    private function getErrorJson($status)
    {
        return <<<JSON
{
    "message": "There was an issue with the JSON that was sent through",
    "success": false,
    "errors": {
        "description": "Invalid value of \"%value\" provided for the %fieldName field.",
        "details": {
            "fieldName": "status",
            "value": "$status"
        }
    }
}
JSON;
    }
}
