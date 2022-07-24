<?php

namespace Dev\RestApi\Test\Integration\ApiRequests;

use InvalidArgumentException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

class TestHelper
{
    public function convertJsonToPostData($jsonString)
    {
        $decodedString = json_decode($jsonString);
        if (empty($decodedString)) {
            throw new InvalidArgumentException('Could not decode the JSON');
        }

        return $decodedString;
    }

    public function convertReturnedDataToJson($returnedData, $jsonOptions = JSON_PRETTY_PRINT)
    {
        $encodedString = json_encode($returnedData, $jsonOptions);
        if (empty($encodedString)) {
            throw new InvalidArgumentException('Could not decode the JSON');
        }

        return $encodedString;
    }

    public function throwException($message)
    {
        throw new \Exception($message);
    }

    public function getProductId($sku, $refresh = false)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var ProductRepositoryInterface $repository */
        if ($refresh === false) {
            $repository = $objectManager->get(ProductRepositoryInterface::class);
        } else {
            $repository = $objectManager->create(ProductRepositoryInterface::class);
        }
        $product = $repository->get($sku);

        return $product->getId();
    }
}
