<?php

namespace Dev\RestApi\Test\FixtureLoaders;

use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;

class AfterLoader extends AbstractLoader
{
    /**
     * @var array
     */
    private $sharedIndexesComplete = [];

    public function afterLoad()
    {
        $this->cleanCache();
        $this->reindexMagento();
    }

    private function reindexMagento()
    {
        foreach ($this->getIndexers() as $indexer) {
            $indexerConfig = $this->getConfig()->getIndexer($indexer->getId());
            $sharedIndex = $indexerConfig['shared_index'];

            // Skip indexers having shared index that was already complete
            if (!in_array($sharedIndex, $this->sharedIndexesComplete)) {
                $indexer->reindexAll();
                if ($sharedIndex) {
                    $this->validateSharedIndex($sharedIndex);
                }
            }
        }
    }

    private function validateSharedIndex($sharedIndex)
    {
        $indexerIds = $this->getIndexerIdsBySharedIndex($sharedIndex);
        if (empty($indexerIds)) {
            return $this;
        }
        foreach ($indexerIds as $indexerId) {
            $indexer = $this->getIndexerRegistry()->get($indexerId);
            /** @var \Magento\Indexer\Model\Indexer\State $state */
            $state = $indexer->getState();
            $state->setStatus(\Magento\Framework\Indexer\StateInterface::STATUS_VALID);
            $state->save();
        }
        $this->sharedIndexesComplete[] = $sharedIndex;
        return $this;
    }

    private function getIndexerRegistry()
    {
        return $this->getObject(\Magento\Framework\Indexer\IndexerRegistry::class);
    }

    private function getIndexerIdsBySharedIndex($sharedIndex)
    {
        $indexers = $this->getConfig()->getIndexers();
        $result = [];
        foreach ($indexers as $indexerConfig) {
            if ($indexerConfig['shared_index'] == $sharedIndex) {
                $result[] = $indexerConfig['indexer_id'];
            }
        }
        return $result;
    }

    protected function getIndexers()
    {
        $allIndexers = $this->getAllIndexers();
        return $allIndexers;
    }

    protected function getAllIndexers()
    {
        $indexers = $this->getCollectionFactory()->create()->getItems();
        return array_combine(
            array_map(
                function ($item) {
                    /** @var \Magento\Framework\Indexer\IndexerInterface $item */
                    return $item->getId();
                },
                $indexers
            ),
            $indexers
        );
    }

    private function getCollectionFactory()
    {
        return $this->getObject(\Magento\Indexer\Model\Indexer\CollectionFactory::class);
    }

    private function getConfig()
    {
        return $this->getObject(\Magento\Framework\Indexer\ConfigInterface::class);
    }

    private function cleanCache()
    {
        $_cacheTypeList = $this->createObject(TypeListInterface::class);
        $_cacheFrontendPool = $this->createObject(Pool::class);
        $types = [
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
