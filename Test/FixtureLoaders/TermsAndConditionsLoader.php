<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * This is used to create and delete terms and conditions fixtures
 *
 * Class TermsAndConditionsLoader
 */
class TermsAndConditionsLoader extends AbstractLoader
{
    /**
     * Used to create the Terms And Conditions that were passed in to the class.
     *
     */
    public function createTermsAndConditions()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $agreement1 \Magento\CheckoutAgreements\Model\Agreement */
        $agreement1 = $objectManager->create('Magento\CheckoutAgreements\Model\Agreement');
        $agreement1->load(1, 'agreement_id');
        $agreement1->setId(1);
        $agreement1->setData([
                                 'name'           => 'Terms And Conditions 1 name',
                                 'content'        => 'Terms And Conditions content 1',
                                 'content_height' => '200px',
                                 'checkbox_text'  => 'Checkbox text 1',
                                 'is_active'      => true,
                                 'is_html'        => false,
                                 'stores'         => [0, 1],
                             ]);

        $agreement1->save();

        $agreement2 = $objectManager->create('Magento\CheckoutAgreements\Model\Agreement');
        $agreement2->load(2, 'agreement_id');
        $agreement2->setId(2);
        $agreement2->setData([
                                 'name'           => 'Terms And Conditions 2 name',
                                 'content'        => 'Terms And Conditions content 2',
                                 'content_height' => '200px',
                                 'checkbox_text'  => 'Checkbox text 2',
                                 'is_active'      => true,
                                 'is_html'        => false,
                                 'stores'         => [0, 1],
                             ]);
        $agreement2->save();

        $agreement3 = $objectManager->create('Magento\CheckoutAgreements\Model\Agreement');
        $agreement3->load(3, 'agreement_id');
        $agreement3->setId(3);
        $agreement3->setData([
                                 'name'           => 'Terms And Conditions 3 name',
                                 'content'        => 'Terms And Conditions content 3',
                                 'content_height' => '200px',
                                 'checkbox_text'  => 'Checkbox text 3',
                                 'is_active'      => false,
                                 'is_html'        => false,
                                 'stores'         => [0, 1],
                             ]);
        $agreement3->save();

        $this->cleanCache();
    }

    /**
     * Used to delete all created terms and conditions entries
     */
    public function removeTermsAndConditions()
    {
        /** @var Collection $collection */
        $collection = $this->getObject(Collection::class);
        foreach ($collection->getItems() as $item) {
            $item->delete();
        }

        $collection->getConnection()->query(sprintf('ALTER TABLE %s AUTO_INCREMENT=1', $collection->getMainTable()));
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
