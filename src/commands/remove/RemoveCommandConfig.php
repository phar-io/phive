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
     * InstallCommandConfig constructor.
     *
     * @param Cli\Options $options
     * @param Config      $config
     */
    public function __construct(Cli\Options $options, Config $config) {
        $this->cliOptions = $options;
        $this->config = $config;
    }

    /**
     * @return Directory
     */
    public function getTargetDirectory() {
        return $this->config->getWorkingDirectory()->child('tools');
    }

    /**
     * @return string
     * @throws Cli\CommandOptionsException
     */
    public function getPharName() {
        return $this->cliOptions->getArgument(0);
    }

}
