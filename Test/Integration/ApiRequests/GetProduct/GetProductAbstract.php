<?php

namespace Dev\RestApi\Test\Integration\ApiRequests\GetProduct;

use Magento\Framework\Webapi\Rest\Request;
use Dev\RestApi\Test\Integration\ApiRequests\BaseRequestAbstract;

abstract class GetProductAbstract extends BaseRequestAbstract
{
    public function makeRequest($productId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/rest_dev/getProduct/{$productId}",
                'httpMethod'   => Request::HTTP_METHOD_POST,
            ]
        ];

        return $this->_webApiCall($serviceInfo);
    }

    public function sendRequestThatShouldError($productId, $postData, $expectedResponseCode)
    {
        try {
            $result = $this->makeRequest($productId, $postData);
            $code   = 200;
        } catch (\Exception $exception) {
            $result = json_decode($exception->getMessage());
            $code   = $exception->getCode();
        }
        $this->stripStackTrace($result);
        $this->assertEquals($expectedResponseCode, $code);

        return $result;
    }

    public function getStandardResponseJson()
    {
        /** @lang JSON */
        return <<<JSON
{
    "id": 1,
    "sku": "t_shirt_with_crochet_detail",
    "name": "T-shirt with crochet detail",
    "description": "Unicolour top with sheer crochet detail across the upper front. A subtle, feminine design. In a textured flame cotton mix. Straight shape. Wide round neckline. Short sleeves. Topstitched lower edge. Invest in a few colours of this charming top!"
}
JSON;
    }

    /**
     * @return string
     */
    private function getTestImage(): string
    {
        $testImagePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'magento_image.jpg';
        // @codingStandardsIgnoreLine
        return base64_encode(file_get_contents($testImagePath));
    }
}
