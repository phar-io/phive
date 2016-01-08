<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class RemoveCommandConfig {

    /**
     * @var CLI\Options
     */
    private $cliOptions;

    /**
     * @var Config
     */
    private $config;

    /**
     * InstallCommandConfig constructor.
     *
     * @param CLI\Options $options
     * @param Config      $config
     */
    public function __construct(CLI\Options $options, Config $config) {
        $this->cliOptions = $options;
        $this->config = $config;
    }

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return $this->config->getWorkingDirectory();
    }

    /**
     * @return string
     * @throws CLI\CommandOptionsException
     */
    public function getPharName() {
        return $this->cliOptions->getArgument(0);
    }

}
