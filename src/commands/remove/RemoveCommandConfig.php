<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class RemoveCommandConfig {

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
     * @param Cli\Options $options
     * @param Config $config
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
    public function getTargetDirectory() {
        if (!$this->phiveXmlConfig->hasToolsDirectory()) {
            return $this->config->getToolsDirectory();
        }
        return $this->phiveXmlConfig->getToolsDirectory();
    }

    /**
     * @return string
     * @throws Cli\CommandOptionsException
     */
    public function getPharName() {
        return $this->cliOptions->getArgument(0);
    }

}
