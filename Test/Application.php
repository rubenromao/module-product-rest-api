<?php

namespace Dev\RestApi\Test;

use Magento\Framework\Filesystem\Glob;

class Application extends \Magento\TestFramework\Application
{

    const PHP_NOXDEBUG_CMD='bash ' . __DIR__ . '/phpNoXdebug.bash ';

    /**
     * Copies configuration files from the main code base, so the installation could proceed in the tests directory
     *
     * @return void
     */
    private function copyAppConfigFiles()
    {
        $globalConfigFiles = Glob::glob(
            $this->_globalConfigDir . '/{di.xml,*/di.xml,vendor_path.php}',
            Glob::GLOB_BRACE
        );
        foreach ($globalConfigFiles as $file) {
            $targetFile = $this->_configDir . str_replace($this->_globalConfigDir, '', $file);
            $this->_ensureDirExists(dirname($targetFile));
            if ($file !== $targetFile) {
                copy($file, $targetFile);
            }
        }
    }

    /**
     * Encodes init params into a query string
     *
     * @return string
     */
    private function getInitParamsQuery()
    {
        return urldecode(http_build_query($this->_initParams));
    }

    /**
     * Overriding to use the phpNoXdebug
     *
     * Cleanup both the database and the file system
     *
     * @return void
     */
    public function cleanup()
    {
        $this->_ensureDirExists($this->installDir);
        $this->_ensureDirExists($this->_configDir);

        $this->copyAppConfigFiles();
        /**
         * @see \Magento\Setup\Mvc\Bootstrap\InitParamListener::BOOTSTRAP_PARAM
         */
        $this->_shell->execute(
            self::PHP_NOXDEBUG_CMD . ' -f %s setup:uninstall -vvv -n --magento-init-params=%s',
            [BP . '/bin/magento', $this->getInitParamsQuery()]
        );
    }

    /**
     * Overriding to use the phpNoXdebug
     *
     * {@inheritdoc}
     */
    public function install($cleanup = false)
    {
        $installOptions = $this->getInstallConfig();

        /* Install application */
        if ($installOptions) {
            $installCmd = self::PHP_NOXDEBUG_CMD . ' -f ' . BP . '/bin/magento setup:install -vvv';
            $installArgs = [];
            foreach ($installOptions as $optionName => $optionValue) {
                if (is_bool($optionValue)) {
                    if (true === $optionValue) {
                        $installCmd .= " --$optionName";
                    }
                    continue;
                }
                if (!empty($optionValue)) {
                    $installCmd .= " --$optionName=%s";
                    $installArgs[] = $optionValue;
                }
            }
            $this->_shell->execute($installCmd, $installArgs);
        }
    }
}
