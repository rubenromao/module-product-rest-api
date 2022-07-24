<?php

namespace Dev\RestApi\Test\FixtureLoaders;

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Eav\Model\Config;

/**
 * This is used to carry out common tasks that all of the loaders will need
 *
 * Class AbstractLoader
 *
 * @package Dev\RestApi\Test\FixtureLoaders
 */
abstract class AbstractLoader
{
    public $taxClasses;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /** @var  Config */
    private $eavConfig;

    /**
     * @return ObjectManagerInterface
     */
    public function getObjectManager()
    {
        if (null === $this->objectManager) {
            $this->objectManager = Bootstrap::getObjectManager();
        }

        return $this->objectManager;
    }

    /**
     * This is used to create a new instance of an object
     *
     * @param string $type - The type of object to create please use Object::class notation
     *
     * @return mixed - A new instance of the object
     */
    public function createObject($type)
    {
        $objectManager = $this->getObjectManager();

        return $objectManager->create($type);
    }

    /**
     * Used to get an existing instance of an object i.e. a singleton
     *
     * @param string $type - The type of object to get, please use Object::class notation
     *
     * @return mixed - The object
     */
    public function getObject($type)
    {
        $objectManager = $this->getObjectManager();

        return $objectManager->get($type);
    }

    /**
     * A simple wrapper to check if an object with an id exists, and if so delete it
     *
     * @param string $type     - The type of object to get, please use Object::class notation
     * @param string $objectId - The ID of the object
     */
    public function deleteObject($type, $objectId)
    {
        $object = $this->createObject($type);
        $object->load($objectId);
        if ($object->getId()) {
            $object->delete();
        }
    }

    /**
     * We are unable to delete certain objects unless Magento thinks it is in the admin/secure area. This sets that
     */
    public function setSecureArea()
    {
        $registry = $this->getObject(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);
    }

    public function getAttribute($attributeCode)
    {
        $attributeRepository = $this->getObject(Repository::class);

        return $attributeRepository->get($attributeCode);

        $eavConfig = $this->getEavConfig();
        $eavConfig->clear();
        $attribute = $eavConfig->getAttribute('catalog_product', $attributeCode);

        return $attribute;
    }

    /**
     * @return Config
     */
    public function getEavConfig()
    {
        if (null === $this->eavConfig) {
            $this->eavConfig = $this->getObject(Config::class);
        }

        return $this->eavConfig;
    }

    public function getProductTaxClass($name)
    {
        $type = ClassModel::TAX_CLASS_TYPE_PRODUCT;

        return $this->getTaxClass($type, $name);
    }

    public function getCustomerTaxClass($name)
    {
        $type = ClassModel::TAX_CLASS_TYPE_CUSTOMER;

        return $this->getTaxClass($type, $name);
    }

    public function getTaxClass($type, $name)
    {
        if (null === $this->taxClasses) {
            $classes = [];
            /** @var ClassModel $class */
            $class      = $this->createObject(ClassModel::class);
            $collection = $class->getCollection();
            foreach ($collection as $class) {
                $className = $class->getClassName();
                $classType = $class->getClassType();
                if (!isset($classes[$classType])) {
                    $classes[$classType] = [];
                }
                $classes[$classType][$className] = $class->getId();
            }

            $this->taxClasses = $classes;
        }

        if (!isset($this->taxClasses[$type]) || !isset($this->taxClasses[$type][$name])) {
            throw new \Exception("Could not find a tax class with name of $name and type of $type");
        }

        /** @var ClassModel $class */
        $class = $this->createObject(ClassModel::class);
        $class->load($this->taxClasses[$type][$name]);

        return $class;
    }
}
