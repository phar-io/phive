<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class PurgeCommandConfig {

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

}
