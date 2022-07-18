<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductAttributeLoader extends AbstractLoader
{
    /**
     * @var array
     */
    private $attributeData;

    public function __construct(array $attributeData)
    {
        $this->attributeData = $attributeData;
    }

    public function createAttributes()
    {
        /** @var $installer CategorySetup */
        $installer = $this->createObject(CategorySetup::class);
        /** @var AttributeRepositoryInterface $attributeRepository */
        $attributeRepository = $this->createObject(AttributeRepositoryInterface::class);
        $entityTypeId        = $installer->getEntityTypeId('catalog_product');
        foreach ($this->attributeData as $attribute) {
            $this->hasExpectedAttributeValues($attribute);
            if ($this->doesAttributeExist($attribute['attribute_code']) === true) {
                continue;
            }
            $this->clearEavConfig();
            $attributeModel              = $this->createObject(Attribute::class);
            $attribute['entity_type_id'] = $entityTypeId;
            $attributeModel->setData($attribute);
            $attributeModel->save();
            $attributeRepository->save($attributeModel);

            /* Assign attribute to attribute set */
            $installer->addAttributeToGroup('catalog_product', 'Default', 'General', $attributeModel->getId());
        }

        $this->clearEavConfig();
    }

    public function removeAttributes()
    {
        $this->setSecureArea();
        foreach ($this->attributeData as $attribute) {
            $attributeCode = $attribute['attribute_code'];
            if ($this->doesAttributeExist($attributeCode) === true) {
                $this->getAttribute($attributeCode)->delete();
            }
        }

        $this->clearEavConfig();
    }

    private function doesAttributeExist($attributeCode)
    {
        try {
            $attribute = $this->getAttribute($attributeCode);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return (null !== $attribute->getId());
    }

    private function clearEavConfig()
    {
        $this->getEavConfig()->clear();
    }

    private function hasExpectedAttributeValues(array $attributeData)
    {
        $expectedValues = [
            'attribute_code',
            'backend_type',
            'frontend_input',
            'frontend_label',
            'is_comparable',
            'is_filterable',
            'is_filterable_in_search',
            'is_global',
            'is_html_allowed_on_front',
            'is_required',
            'is_searchable',
            'is_unique',
            'is_used_for_promo_rules',
            'is_user_defined',
            'is_visible_in_advanced_search',
            'is_visible_on_front',
            'used_for_sort_by',
            'used_in_product_listing',
        ];
        foreach ($expectedValues as $key) {
            if (!isset($attributeData[$key])) {
                throw new \Exception("Could not find a value for $key in the attribute data");
            }
        }

        return true;
    }
}
