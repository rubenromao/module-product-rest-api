<?php

namespace Dev\RestApi\Test\Integration\ApiRequests\GetProduct;

class GetProductTest extends GetProductAbstract
{
    /**
     * @magentoApiDataFixture apiFixtures
     */
    public function testGetSimpleProduct()
    {
        $testHelper   = $this->getTestHelper();
        $result       = $this->makeRequest(1);
        $returnedJson = $testHelper->convertReturnedDataToJson($result, JSON_PRETTY_PRINT);
        $expectedJson = $this->getExpectedJson();

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getExpectedJson()
    {
        return <<<JSON
{
    "id": 1,
    "sku": "t_shirt_with_crochet_detail",
    "name": "T-shirt with crochet detail",
    "description": "Unicolour top with sheer crochet detail across the upper front. A subtle, feminine design. In a textured flame cotton mix. Straight shape. Wide round neckline. Short sleeves. Topstitched lower edge. Invest in a few colours of this charming top!"
}
JSON;
    }
}
