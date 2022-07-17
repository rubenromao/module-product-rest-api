<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\Config\Model\Config as ConfigWriter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\App\Config;

class ConfigurationLoader extends AbstractLoader
{
    private $configuration;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $_encryptor;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
        $this->_encryptor = $this->getObject('\Magento\Framework\Encryption\EncryptorInterface');
    }

    public function loadConfiguration()
    {
        /** @var Config $scopeConfig */
        $scopeConfig = $this->getObject(ScopeConfigInterface::class);
        foreach ($this->configuration as $config) {
            $path  = $config['path'];
            $value = $config['value'];
            $scopeConfig->setValue($path, $value);
        }
    }

    public function loadConfigurationForApi()
    {
        /**
         * This is a fairly horrific hack to get around a bug when the config gets saved in the wrong scope when single
         * store mode is enabled. This doesn't happen when we set it normally, only in the tests
         */
        $storeManager = $this->getObject(StoreManager::class);
        $storeManager->setIsSingleStoreModeAllowed(false);
        /** @var ConfigWriter $configWriter */
        $configWriter = $this->getObject(ConfigWriter::class);
        foreach ($this->configuration as $config) {
            $path  = $config['path'];
            $value = $config['value'];
            if (array_key_exists('encrypt', $config) && $config['encrypt'] === true) {
                $value = $this->_encryptor->encrypt($value);
            }
            $configWriter->setDataByPath($path, $value);
            $configWriter->save();
        }
    }

    /**
     * This method saves the configuration, in a manner closer to \Magento\Config\Controller\Adminhtml\System\Config\Save::execute.
     *
     * I had an issue with $this->loadConfigurationForApi() because it triggers a save of the config with each setting.
     * In my case, I had two settings where only one of them could be true at once. I added validation to enforce that,
     * however because $this->loadConfigurationForApi() saves them individually the validation was failing after the first save.
     * I noticed that the core Magento system configuration save did not suffer this issue, so the below method operates
     * in the same way.
     */
    public function loadConfigurationWithoutTriggeringValidationOnEachSave()
    {
        /** @var \Magento\Config\Model\Config\Factory $configFactory */
        $configFactory = $this->getObject('Magento\Config\Model\ConfigFactory');
        /** @var \Magento\Config\Model\Config $configModel  */
        $configModel = $configFactory->create(['data' => $this->configuration]);
        $configModel->save();
    }
}
