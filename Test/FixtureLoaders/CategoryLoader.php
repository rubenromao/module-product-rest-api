<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * This is used to create and delete category fixtures
 *
 * Class CategoryLoader
 */
class CategoryLoader extends AbstractLoader
{
    /**
     * @var array
     */
    private $categoryData;

    private $mediaWriter;

    /**
     * CategoryLoader constructor.
     *
     * @param array $categoryData
     */
    public function __construct(array $categoryData)
    {
        $this->categoryData = $categoryData;
    }

    /**
     * Used to create the categories that were passed in to the class.
     *
     * Please be aware that I am doing no error checking on the data structure, so make sure it is right
     */
    public function createCategories()
    {
        foreach ($this->categoryData as $category) {
            /** @var Category $categoryModel */
            $categoryModel = $this->createObject(Category::class);
            $categoryModel->isObjectNew(true);
            $image = $category['image'];
            $this->createImage($image);
            $categoryModel
                ->setId($category['id'])
                ->setCreatedAt($category['created_at'])
                ->setName($category['name'])
                ->setParentId($category['parentId'])
                ->setPath($category['path'])
                ->setLevel($category['level'])
                ->setAvailableSortBy($category['availableSortBy'])
                ->setDefaultSortBy($category['defaultSortBy'])
                ->setIsActive($category['isActive'])
                ->setPosition($category['position'])
                ->setPostedProducts($category['postedProducts'])
                ->setImage($image);
            if (isset($category['include_cart_button_on_listing'])) {
                $categoryModel->setIncludeCartButtonOnListing($category['include_cart_button_on_listing']);
            }
            $categoryModel->save();
        }

        $this->cleanCache();
    }

    /**
     * Used to delete the categories that were passed into the class
     */
    public function removeCategories()
    {
        $this->setSecureArea();
        foreach ($this->categoryData as $category) {
            $categoryId = $category['id'];
            $this->deleteObject(Category::class, $categoryId);
        }
    }

    private function createImage($image)
    {
        $magentoImage = $this->getMediaPath() . $image;
        $this->createImageDirectory($image);
        if (file_exists($magentoImage)) {
            return;
        }
        $tmpImage = __DIR__ . '/../Integration/_files/magento_image.jpg';
        if (!file_exists($tmpImage)) {
            throw new \Exception("Can not find the tmp image");
        }
        copy($tmpImage, $magentoImage);
    }

    private function createImageDirectory($image)
    {
        $basePath = $this->getMediaPath(false);
        $dirPath  = dirname($basePath . $image);
        $this->getMediaWriter()->create($dirPath);
    }

    private function getMediaPath($absolute = true)
    {
        $path = 'catalog/category/';
        if ($absolute === true) {
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
            $this->mediaWriter = $this->createObject(Filesystem::class)->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaWriter;
    }

    private function cleanCache()
    {
        $_cacheTypeList     = $this->createObject(TypeListInterface::class);
        $_cacheFrontendPool = $this->createObject(Pool::class);
        $types              = [
            'config',
            'layout',
            'block_html',
            'collections',
            'reflection',
            'db_ddl',
            'eav',
            'config_integration',
            'config_integration_api',
            'full_page',
            'translate',
            'config_webservice'
        ];
        foreach ($types as $type) {
            $_cacheTypeList->cleanType($type);
        }
        foreach ($_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
