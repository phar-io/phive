<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

class TargetDirectoryLocator {

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;
    /**
     * @var Options
     */
    private $cliOptions;

    /**
     * @param Config $config
     * @param PhiveXmlConfig $phiveXmlConfig
     * @param Options $cliOptions
     */
    public function __construct(Config $config, PhiveXmlConfig $phiveXmlConfig, Options $cliOptions) {
        $this->config = $config;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->cliOptions = $cliOptions;
    }

    /**
     * @return Directory
     * @throws Cli\CommandOptionsException
     * @throws ConfigException
     */
    public function getTargetDirectory() {
        if ($this->cliOptions->hasOption('target')) {
            return new Directory(rtrim($this->cliOptions->getOption('target'), '/'));
        }
        if (!$this->phiveXmlConfig->hasTargetDirectory()) {
            return $this->config->getToolsDirectory();
        }
        return $this->phiveXmlConfig->getTargetDirectory();        
    }
    
    
}