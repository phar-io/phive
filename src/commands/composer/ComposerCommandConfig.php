<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class ComposerCommandConfig {

    /**
     * @var Cli\Options
     */
    private $cliOptions;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @param Cli\Options    $options
     * @param Config         $config
     * @param PhiveXmlConfig $phiveXmlConfig
     */
    public function __construct(Cli\Options $options, Config $config, PhiveXmlConfig $phiveXmlConfig) {
        $this->cliOptions = $options;
        $this->config = $config;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return $this->config->getWorkingDirectory();
    }

    /**
     * @return bool
     */
    public function installGlobally() {
        return $this->cliOptions->isSwitch('global');
    }

    /**
     * @return bool
     */
    public function makeCopy() {
        return $this->cliOptions->isSwitch('copy');
    }

    /**
     * @return bool
     */
    public function doNotAddToPhiveXml() {
        return $this->cliOptions->isSwitch('temporary');
    }

    /**
     * @return Filename
     */
    public function getComposerFilename() {
        return $this->getWorkingDirectory()->file('composer.json');
    }

}
