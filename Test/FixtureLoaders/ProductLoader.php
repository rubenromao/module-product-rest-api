<?php

namespace Dev\RestApi\Test\FixtureLoaders;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Used to create product fixtures - this is a bit more complicated
 *
 * Class ProductLoader
 *
 * @package Dev\RestApi\Test\FixtureLoaders
 */
class ProductLoader extends AbstractLoader
{
    /**
     * @var  WriteInterface
     */
    private $mediaWriter;

    /**
     * @var array
     */
    private $productData;

    /**
     * @var array
     */
    private $configurableDetails = [];

    /**
     * @var array
     */
    private $attributeDetails = [];

    /**
     * ProductLoader constructor.
     *
     * @param array $productData
     */
    public function __construct(array $productData)
    {
        $this->productData = $productData;
    }

    /**
     * The media gallery needs to be in a certain form, and the image file has to exist in a temp directory. This
     * handles creating the array and storing a placeholder image for this
     *
     * @param array $imageData an array of file names i.e. /a/n/an_example_image.png
     *
     * @return array
     * @throws \Exception
     */
    public function buildMediaImagesArray(array $imageData)
    {
        $images   = [];
        $position = 1;
        foreach ($imageData as $image) {
            $images[] = [
                'file'       => $image,
                'position'   => $position,
                'label'      => 'Image Alt Text',
                'disabled'   => 0,
                'media_type' => 'image'
            ];
            $position++;
            $this->createImageDirectory($image, false);
            $this->createImageDirectory($image, true);
            $this->createTmpImage($image);
        }

        return $images;
    }

    /**
     * This is used to create the products from the data that is sent through
     */
    public function createProducts()
    {
        $this->setSecureArea();
        foreach ($this->productData as $product) {
            $image = $product['image'];
            $this->createMainImage($image);
            /** @var $productModel Product */
            $productModel = $this->createObject(Product::class);
            $productModel->setTypeId($product['type'])
                         ->setId($product['id'])
                         ->setAttributeSetId($product['attributeSetId'])
                         ->setName($product['name'])
                         ->setSku($product['sku'])
                         ->setVisibility($product['visibility'])
                         ->setStatus($product['status'])
                         ->setWebsiteIds($product['websiteIds'])
                         ->setDescription($product['description'])
                         ->setShortDescription($product['short_description'])
                         ->setStockData($product['stockData'])
                         ->setImage($product['image'])
                         ->setWeight(array_key_exists('weight', $product) ? $product['weight'] : null);

            if (isset($product['tax_class'])) {
                $productModel->setTaxClassId($this->getProductTaxClass($product['tax_class'])->getId());
            }
            switch ($product['type']) {
                case Type::TYPE_SIMPLE:
                    $productModel->setPrice($product['price']);
                    if (isset($product['special_price'])) {
                        $productModel->setSpecialPrice($product['special_price']);
                    }
                    $productModel->setPrice($product['price']);
                    $this->handleSimpleProduct($product, $productModel);
                    break;
                case Type::TYPE_VIRTUAL:
                    $productModel->setPrice($product['price']);
                    if (isset($product['special_price'])) {
                        $productModel->setSpecialPrice($product['special_price']);
                    }
                    $this->handleSimpleProduct($product, $productModel);
                    break;
                case Configurable::TYPE_CODE:
                    $this->handleConfigurableProduct($product, $productModel);
                    break;
            }
            if (isset($product['media_images']) && is_array($product['media_images'])) {
                $images = $this->buildMediaImagesArray($product['media_images']);
                $productModel->setData('media_gallery', ['images' => $images]);
            }
            $productModel->save();
            if (isset($product['custom_options']) && is_array($product['custom_options'])) {
                $this->createCustomOptions($product, $productModel);
            }
            if (isset($product['parent_sku'])) {
                $this->configurableDetails[$product['parent_sku']]['ids'][] = $productModel->getId();
            }
        }
    }

    /**
     * This is used to remove the products that are sent through
     */
    public function removeProducts()
    {
        $this->setSecureArea();
        foreach ($this->productData as $product) {
            $productId = $product['id'];
            $this->deleteObject(Product::class, $productId);
        }
    }

    public function resetCatalogProductAutoIncrements()
    {
        // Reset the auto increment IDs for some of the product tables which have been populated
        $resource   = $this->createObject(\Magento\Framework\App\ResourceConnection::class);
        /** @var \Magento\Framework\App\ResourceConnection $connection */
        $connection = $resource->getConnection();
        $tables = [
            'catalog_product_entity', 'catalog_product_entity_datetime', 'catalog_product_entity_decimal', 'catalog_product_entity_gallery',
            'catalog_product_entity_int', 'catalog_product_entity_media_gallery', 'catalog_product_entity_media_gallery_value',
            'catalog_product_entity_media_gallery_value_to_entity', 'catalog_product_entity_media_gallery_value_video',
            'catalog_product_entity_text', 'catalog_product_entity_tier_price', 'catalog_product_entity_varchar', 'catalog_product_link',
            'catalog_product_link_attribute', 'catalog_product_link_attribute_decimal', 'catalog_product_link_attribute_int',
            'catalog_product_link_attribute_varchar', 'catalog_product_link_type', 'catalog_product_option', 'catalog_product_option_price',
            'catalog_product_option_title', 'catalog_product_option_type_price', 'catalog_product_option_type_title', 'catalog_product_option_type_value',
            'catalog_product_relation', 'catalog_product_super_attribute', 'catalog_product_super_attribute_label', 'catalog_product_super_link',
            'catalog_product_website'
        ];
        foreach ($tables as $modelEntity) {
            $table = $connection->getTableName($modelEntity);
            $connection->query("ALTER TABLE `$table` AUTO_INCREMENT=1");
        }
    }

    private function handleConfigurableProduct($product, Product $productModel)
    {
        /** @var Factory $optionsFactory */
        $optionsFactory = $this->createObject(Factory::class);
        $attributeData  = [];
        $productSku     = $product['sku'];
        $position       = 0;
        foreach ($this->configurableDetails[$productSku]['types'] as $code => $values) {
            $attribute       = $this->getAttribute($code);
            $attributeData[] = [
                'attribute_id' => $attribute->getAttributeId(),
                'code'         => $attribute->getAttributeCode(),
                'label'        => $attribute->getStoreLabel(),
                'position'     => $position,
                'values'       => $values,
            ];
            $position++;
        }

        $configurableOptions = $optionsFactory->create($attributeData);

        $extensionAttributes = $productModel->getExtensionAttributes();
        $extensionAttributes->setConfigurableProductOptions($configurableOptions);
        $extensionAttributes->setConfigurableProductLinks($this->configurableDetails[$productSku]['ids']);
        $productModel->setExtensionAttributes($extensionAttributes);
    }

    private function handleSimpleProduct($product, Product $productModel)
    {

        if (isset($product['configurable_options'])) {
            foreach ($product['configurable_options'] as $code => $value) {
                $value = $this->getOptionValue($code, $value);
                $productModel->setData($code, $value);
                if (!isset($product['parent_sku'])) {
                    continue;
                }
                $parentSku = $product['parent_sku'];
                if (!isset($this->configurableDetails[$parentSku])) {
                    $this->configurableDetails[$parentSku] = ['types' => [], 'ids' => []];
                }
                if (!isset($this->configurableDetails[$parentSku]['types'][$code])) {
                    $this->configurableDetails[$parentSku]['types'][$code] = [];
                }
                $this->configurableDetails[$parentSku]['types'][$code][] = [
                    'label'        => 'test',
                    'attribute_id' => $this->getAttribute($code)->getId(),
                    'value_index'  => $value,
                ];
            }
        }
    }

    private function getOptionValue($code, $value)
    {
        if (!isset($this->attributeDetails[$code])) {
            $details   = [];
            $attribute = $this->getAttribute($code);
            $options   = $attribute->getOptions();
            foreach ($options as $option) {
                $optionLabel           = $option->getLabel();
                $optionValue           = $option->getValue();
                $details[$optionLabel] = $optionValue;
            }
            $this->attributeDetails[$code] = $details;
        }

        if (!isset($this->attributeDetails[$code][$value])) {
            throw new \Exception("Could not find an value of $value for option $code");
        }

        return $this->attributeDetails[$code][$value];
    }

    /**
     * Two different directories need to be created for the image. The main one where the image will be saved to, and a
     * tmp one where the image file has to be before the product is saved
     *
     * @param string $image - The path to the image
     * @param bool   $isTmp - whether to create the main or tmp directory
     */
    private function createImageDirectory($image, $isTmp = false)
    {
        $basePath = $this->getMediaPath($isTmp);
        $dirPath  = dirname($basePath . $image);
        $this->getMediaWriter()->create($dirPath);
    }

    /**
     * This is used to put the placeholder image into the tmp directory. If an image with the same name exists in the
     * final directory it will be remove to prevent Magento creating duplicates
     *
     * @param string $image - The path to the image that will be saved against the product
     *
     * @throws \Exception - If the placeholder image does not exist
     */
    private function createTmpImage($image)
    {
        $magentoImage = $this->getMediaPath(false, true) . $image;
        if (file_exists($magentoImage)) {
            unlink($magentoImage);
        }
        $tmpImageDirectory = $this->getMediaPath(true, true);
        $tmpImagePath      = $tmpImageDirectory . $image;
        $tmpImage          = __DIR__ . '/../Integration/_files/magento_image.jpg';
        if (!file_exists($tmpImage)) {
            throw new \Exception("Can not find the tmp image");
        }

        if (is_dir($tmpImageDirectory)) {
            $this->getMediaWriter()->create($tmpImageDirectory);
        }
        copy($tmpImage, $tmpImagePath);
    }

    private function createMainImage($image)
    {
        $magentoImageDirectory = $this->getMediaPath(false, true);
        $magentoImage          = $magentoImageDirectory . $image;
        if (file_exists($magentoImage)) {
            return;
        }
        $tmpImage = __DIR__ . '/../Integration/_files/magento_image.jpg';
        if (!file_exists($tmpImage)) {
            throw new \Exception("Can not find the tmp image");
        }

        $fullImageDir = dirname($magentoImage);

        if (!is_dir($fullImageDir)) {
            mkdir($fullImageDir, 0755, true);
        }

        copy($tmpImage, $magentoImage);
    }

    /**
     * We need to know the path to where the image will be saved.
     *
     * @param bool $isTmp      - Whether to get the main or tmp path
     * @param bool $isAbsolute - If the relative or absolute path is needed
     *
     * @return string
     */
    private function getMediaPath($isTmp = false, $isAbsolute = false)
    {
        $mediaConfig = $this->createObject(Config::class);
        if ($isTmp === true) {
            $path = $mediaConfig->getBaseTmpMediaPath();
        } else {
            $path = $mediaConfig->getBaseMediaPath();
        }

        if ($isAbsolute === true) {
            $path = $this->getMediaWriter()->getAbsolutePath($path);
        }

        return $path;
    }

    /**
     * Magento has a built in directory writer that is used in several places throughout the class. This is used to get
     * it
     *
     * @return WriteInterface
     */
    private function getMediaWriter()
    {
        if (null === $this->mediaWriter) {
            $this->mediaWriter = $this->createObject(\Magento\Framework\Filesystem::class)->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaWriter;
    }

    private function createCustomOptions($productData, Product $magentoProduct)
    {
        $customOptions = [];
        $customOptionsWithValues = [];
        $triggerSave = true;
        foreach ($productData['custom_options'] as $optionData) {
            if (isset($optionData['values'])) {
                $customOptionsWithValues[] = $optionData;
                continue;
            }
            $customOption = $this->createCustomOption($optionData);
            $customOption->setProductSku($magentoProduct->getSku());
            $customOptions[] = $customOption;
        }
        $magentoProduct->setHasOptions(1)->setCanSaveCustomOptions(true)->setOptions($customOptions);
        $magentoProduct->save();

        foreach ($customOptionsWithValues as $optionData) {
            $customOption = $this->createCustomOption($optionData);
            $customOption->setProductSku($magentoProduct->getSku());
            $customOption->setProductId($magentoProduct->getId());
            $customOption->save();
        }
    }

    private function createCustomOption($optionData)
    {
        /** @var ProductCustomOptionInterface $customOption */
        $customOption  = $this->createObject(ProductCustomOptionInterface::class);
        $title         = $optionData['title'];
        $type          = $optionData['type'];
        $required      = array_key_exists('required', $optionData) ? $optionData['required'] : null;
        $sortOrder     = $optionData['sort_order'];
        $price         = $optionData['price'] ?? 0;
        $priceType     = $optionData['price_type'] ?? 'fixed';
        $maxCharacters = $optionData['max_characters'] ?? null;
        $values        = $optionData['values'] ?? false;

        $customOption->setTitle($title)
                     ->setType($type)
                     ->setIsRequire($required)
                     ->setSortOrder($sortOrder)
                     ->setPrice($price)
                     ->setPriceType($priceType);
        if (is_array($values)) {
            $optionValues       = [];
            $optionValueFactory = $this->createObject(ProductCustomOptionValuesInterfaceFactory::class);
            foreach ($values as $value) {
                $optionValue    = $optionValueFactory->create(['data' => $value]);
                $optionValues[] = $optionValue;
            }
            $customOption->setValues($optionValues);
        }
        if ($maxCharacters !== null) {
            $customOption->setMaxCharacters($maxCharacters);
        }

        return $customOption;
    }
}
