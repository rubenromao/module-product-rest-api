<?php

namespace Rezolve\APISalesV4\Test\Integration\ApiRequests;

use InvalidArgumentException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
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

    public function getAttributeOptionId($attributeCode, $optionName)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Attribute $attribute */
        $attribute = $objectManager->create(Attribute::class);
        $attribute->loadByCode('catalog_product', $attributeCode);

        $options  = $attribute->getOptions();
        $optionId = false;
        foreach ($options as $option) {
            if ($option->getData('label') == $optionName) {
                $optionId = $option->getData('value');
                break;
            }
        }
        if ($optionId === false) {
            $this->throwException("Could not find an options of $optionName for the $attributeCode attribute");
        }

        return $optionId;
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

    public function normaliseTheDates($newDate, $response)
    {
        $additionalOptions = $response['data']['additional_options'];
        $valuesToChange    = ['created_at', 'updated_at'];
        foreach ($additionalOptions as $key => $option) {
            if (in_array($option['code'], $valuesToChange)) {
                $response['data']['additional_options'][$key]['value']       = $newDate;
                $response['data']['additional_options'][$key]['value_label'] = $newDate;
            }
        }

        return $response;
    }
}
