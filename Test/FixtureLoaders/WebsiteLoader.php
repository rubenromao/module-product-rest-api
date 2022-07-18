<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\Full;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;

class WebsiteLoader extends AbstractLoader
{
    /**
     * @var array
     */
    private $websiteData;

    private $mediaWriter;

    /**
     * WebsiteLoader constructor.
     *
     * @param array $websiteData
     */
    public function __construct(array $websiteData)
    {
        $this->websiteData = $websiteData;
    }

    public function createWebsites()
    {
        foreach ($this->websiteData as $data) {
            $name           = $data['name'];
            $code           = $data['code'];
            $defaultGroupId = $data['default_group_id'];
            $isDefault      = $data['is_default'];
            $rootCategory   = $data['root_category'];
            $website        = $this->createWebsite($name, $code, $defaultGroupId, $isDefault);
            $storeGroup     = $this->createStoreGroup($website, $name, $rootCategory, 1);
            foreach ($data['stores'] as $storeData) {
                $name      = $storeData['name'];
                $code      = $storeData['code'];
                $sortOrder = $storeData['sort_order'];
                $isActive  = $storeData['is_active'];
                $store     = $this->createStore($name, $code, $sortOrder, $isActive, $website, $storeGroup);
                if (isset($storeData['config'])) {
                    foreach ($storeData['config'] as $path => $value) {
                        $this->addConfig($path, $value, $store->getId(), false);
                    }
                }
            }
            if (isset($data['config'])) {
                foreach ($data['config'] as $path => $value) {
                    $this->addConfig($path, $value, $website->getId(), true);
                }
            }
        }

        $indexer = $this->createObject(Full::class);
        $indexer->reindexAll();
    }

    public function updateWebsiteCode($code, $name)
    {
        $website = $this->createObject(Website::class);
        $website->load('base');
        if ($website->getId() === null) {
            /* Already been updated */
            return;
        }

        $website->setCode($code)->setName($name)->save();

        $images = [
            'rezolve/merchants/logo'    => 'websites/1/logo.jpg',
            'rezolve/merchants/banner'  => 'websites/1/findstore.jpg',
            'rezolve/merchants/tagline' => 'Test Merchant Tagline',
        ];
        foreach ($images as $path => $value) {
            $this->addConfig($path, $value, $website->getId(), true);
        }

        $this->refreshStores();
    }

    private function addConfig($path, $value, $scopeId, $forWebsite = true)
    {
        switch ($path) {
            case 'rezolve/merchants/logo':
                $this->handleLogoUpload($value);
                break;
            case 'rezolve/merchants/banner':
                $this->handleBannerUpload($value);
                break;
        }
        /** @var Config $resource */
        $resource = $this->createObject(Config::class);
        $scope    = ($forWebsite === true) ? 'websites' : 'stores';
        $resource->saveConfig($path, $value, $scope, $scopeId);
    }

    private function createWebsite($name, $code, $defaultGroupId, $isDefault)
    {
        /** @var Website $website */
        $website = $this->createObject(Website::class);
        $website->load($code);
        if (null !== $website->getId()) {
            return $website;
        }

        $website->setCode($code)->setName($name)->setDefaultGroupId($defaultGroupId)->setIsDefault($isDefault)->save();
        $this->refreshStores();

        return $website;
    }

    private function createStore($name, $code, $sortOrder, $isActive, Website $website, Group $group)
    {
        /** @var Store $store */
        $store = $this->createObject(Store::class);
        $store->load($code);
        if (null !== $store->getId()) {
            return $store;
        }

        $store->setCode($code)
              ->setWebsiteId($website->getId())
              ->setGroupId($group->getId())
              ->setName($name)
              ->setSortOrder($sortOrder)
              ->setIsActive($isActive);

        $store->save();
        $eventManager = $this->getObject(ManagerInterface::class);
        $eventManager->dispatch('store_add', ['store' => $store]);
        $this->refreshStores();

        return $store;
    }

    private function createStoreGroup(Website $website, $name, $rootCategory, $storeId)
    {
        /** @var Group $storeGroup */
        $storeGroup = $this->createObject(GroupFactory::class)->create();
        $storeGroup->setWebsite($website);
        $storeGroup->setName($name);
        $storeGroup->setDefaultStoreId($storeId);
        $storeGroup->setRootCategoryId($rootCategory);
        $storeGroup->save();
        $this->refreshStores();

        return $storeGroup;
    }

    private function refreshStores()
    {
        $this->createObject(StoreManagerInterface::class)->reinitStores();
    }

    private function handleBannerUpload($value)
    {
        $path = 'banner/';
        $this->createImage($path, $value);
    }

    private function handleLogoUpload($value)
    {
        $path = 'logo/';
        $this->createImage($path, $value);
    }

    private function createImage($path, $image)
    {
        $magentoImage = $this->getMediaPath($path) . $image;
        $this->createImageDirectory($path, $image);
        if (file_exists($magentoImage)) {
            return;
        }
        $image    = basename($image);
        $tmpImage = __DIR__ . '/../Integration/_files/' . $image;
        if (!file_exists($tmpImage)) {
            throw new \Exception("Can not find the tmp image");
        }
        copy($tmpImage, $magentoImage);
    }

    private function createImageDirectory($path, $image)
    {
        $basePath = $this->getMediaPath($path, false);
        $dirPath  = dirname($basePath . $image);
        $this->getMediaWriter()->create($dirPath);
    }

    private function getMediaPath($path, $absolute = true)
    {
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
}
