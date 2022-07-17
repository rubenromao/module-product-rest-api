<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests\UpdateOrder;

class CanNotCompleteCancelledOrderTest extends UpdateOrderAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     * @magentoApiDataFixture orderFixtures
     */
    public function testCancelOrder()
    {
        $testHelper     = $this->getTestHelper();
        $requestJson    = $this->getRequestJson('completed');
        $pendingOrderId = $this->getCancelledOrderId();
        $postData       = $testHelper->convertJsonToPostData($requestJson);
        $result         = $this->sendRequestThatShouldError('chase', '4', $pendingOrderId, $postData, 400);
        $returnedJson   = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson   = $this->getErrorJson();

        $this->assertEquals($expectedJson, $returnedJson);
    }

    private function getErrorJson()
    {
        return <<<JSON
{
    "message": "We are not able to process this order at the moment",
    "success": false,
    "errors": ""
}
JSON;
    }
}
